#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
CONFIG_FILE="${DEPLOY_CONFIG:-$ROOT_DIR/deploy.local.json}"
COMMAND="${1:-deploy}"

if [[ ! -f "$CONFIG_FILE" ]]; then
  echo "Missing deploy config: $CONFIG_FILE" >&2
  echo "Copy deploy.local.json.example to deploy.local.json and fill in local values." >&2
  exit 1
fi

if ! command -v python3 >/dev/null 2>&1; then
  echo "python3 is required for scripts/deploy.sh" >&2
  exit 1
fi

eval "$(
  python3 - "$CONFIG_FILE" <<'PY'
import json
import shlex
import sys

path = sys.argv[1]
with open(path, 'r', encoding='utf-8') as f:
    cfg = json.load(f)

target_name = cfg.get('default_target', 'prod')
target = cfg['targets'][target_name]
sync = cfg.get('sync', {})
verify = cfg.get('verify', {})

values = {
    'TARGET_NAME': target_name,
    'TARGET_LABEL': target.get('name', target_name),
    'SSH_HOST': target['host'],
    'SSH_PORT': target.get('port', 22),
    'SSH_USER': target['user'],
    'SSH_KEY': target['identity_file'],
    'PROJECT_PATH': target['project_path'].rstrip('/'),
    'SITE_URL': target.get('url', ''),
    'SYNC_MODE': sync.get('mode', 'rsync'),
    'SYNC_STRATEGY': sync.get('strategy', 'full'),
    'SYNC_DELETE': '1' if sync.get('delete', False) else '0',
    'EXCLUDE_FILE': sync.get('exclude_file', '.deployignore'),
    'PHP_LINT_FILES': '\n'.join(verify.get('php_lint_files', [])),
    'CLEAR_COMMANDS': '\n'.join(verify.get('clear_commands', [])),
    'SMOKE_URLS': '\n'.join(verify.get('smoke_urls', [])),
}

for key, value in values.items():
    print(f"export {key}={shlex.quote(str(value))}")
PY
)"

SSH_BASE=(ssh -i "$SSH_KEY" -p "$SSH_PORT" -o StrictHostKeyChecking=accept-new "$SSH_USER@$SSH_HOST")
RSYNC_DELETE_ARGS=()
if [[ "$SYNC_DELETE" == "1" ]]; then
  RSYNC_DELETE_ARGS+=(--delete)
fi

timestamp() {
  date '+%F %T %Z'
}

log() {
  printf '[local %s] %s\n' "$(timestamp)" "$*"
}

timer_start() {
  date +%s
}

timer_finish() {
  local label="$1"
  local started_at="$2"
  local finished_at
  finished_at=$(date +%s)
  log "done: $label ($((finished_at-started_at))s)"
}

run_remote() {
  "${SSH_BASE[@]}" "cd '$PROJECT_PATH' && $1"
}

run_remote_batch() {
  local run_fix_runtime="${1:-0}"
  local run_verify_steps="${2:-0}"
  local remote_script
  local started_at
  started_at=$(timer_start)
  log "start: remote batch (fix_runtime=${run_fix_runtime}, verify=${run_verify_steps})"

  remote_script="$(mktemp)"
  cat >"$remote_script" <<EOF
set -euo pipefail

cd $(printf '%q' "$PROJECT_PATH")
SSH_USER=$(printf '%q' "$SSH_USER")
RUN_FIX_RUNTIME=$(printf '%q' "$run_fix_runtime")
RUN_VERIFY_STEPS=$(printf '%q' "$run_verify_steps")

run_step() {
  local label="\$1"
  shift
  local started_at
  local finished_at
  started_at=\$(date +%s)
  echo "[remote \$(date '+%F %T %Z')] run: \$label"
  "\$@" </dev/null
  finished_at=\$(date +%s)
  echo "[remote \$(date '+%F %T %Z')] done: \$label (\$((finished_at-started_at))s)"
}

normalize_command() {
  local command="\$1"
  case "\$command" in
    sudo\\ -n\\ *)
      printf '%s\n' "\$command"
      ;;
    sudo\\ *)
      printf 'sudo -n %s\n' "\${command#sudo }"
      ;;
    *)
      printf '%s\n' "\$command"
      ;;
  esac
}

mapfile -t PHP_LINT_FILES <<'PHP_LINT_EOF'
$PHP_LINT_FILES
PHP_LINT_EOF

mapfile -t CLEAR_COMMANDS <<'CLEAR_COMMANDS_EOF'
$CLEAR_COMMANDS
CLEAR_COMMANDS_EOF

if [[ "\$RUN_FIX_RUNTIME" == "1" ]]; then
  run_step "sudo -n chown -R www:www runtime" sudo -n chown -R www:www runtime
  run_step "sudo -n find runtime -type d -exec chmod 775 {} +" sudo -n find runtime -type d -exec chmod 775 {} +
  run_step "sudo -n find runtime -type f -exec chmod 664 {} +" sudo -n find runtime -type f -exec chmod 664 {} +
fi

if [[ "\$RUN_VERIFY_STEPS" == "1" ]]; then
  run_step "php -v" php -v

  if command -v composer >/dev/null 2>&1; then
    run_step "composer --version" composer --version
    run_step "composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader" composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
  else
    echo "composer is required on remote host for migration verify" >&2
    exit 1
  fi

  run_step "php think --version" php think --version
  run_step "php think" php think
  run_step "php think worker --help" php think worker --help
  if php think list 2>/dev/null | grep -q "worker:server"; then
    run_step "php think worker:server --help" php think worker:server --help
  else
    echo "[remote \$(date '+%F %T %Z')] skip: php think worker:server --help (not registered)"
  fi
  if php think list 2>/dev/null | grep -q "worker:gateway"; then
    run_step "php think worker:gateway --help" php think worker:gateway --help
  else
    echo "[remote \$(date '+%F %T %Z')] skip: php think worker:gateway --help (not registered)"
  fi
  run_step "php gateway-worker class check" php -r "require 'vendor/autoload.php'; exit((class_exists('GatewayWorker\\\\Gateway') && class_exists('GatewayWorker\\\\Register') && class_exists('GatewayWorker\\\\BusinessWorker')) ? 0 : 1);"

  lint_failed=0
  for file in "\${PHP_LINT_FILES[@]}"; do
    [[ -z "\$file" ]] && continue
    echo "php -l \$file"
    if ! php -l "\$file"; then
      lint_failed=1
    fi
  done

  for cmd in "\${CLEAR_COMMANDS[@]}"; do
    [[ -z "\$cmd" ]] && continue
    normalized_cmd=\$(normalize_command "\$cmd")
    run_step "\$normalized_cmd" bash -lc "\$normalized_cmd"
  done

  run_step "sudo -n php think api:doc --output docs/api-v1.md" sudo -n php think api:doc --output docs/api-v1.md
  run_step "sudo -n php think api:doc --format=openapi --output public/docs/api-v1.openapi.json" sudo -n php think api:doc --format=openapi --output public/docs/api-v1.openapi.json
  run_step "sudo -n chown \$SSH_USER:\$SSH_USER docs/api-v1.md public/docs/api-v1.openapi.json" sudo -n chown "\$SSH_USER:\$SSH_USER" docs/api-v1.md public/docs/api-v1.openapi.json

  if [[ "\$lint_failed" == "1" ]]; then
    exit 1
  fi
fi
EOF

  "${SSH_BASE[@]}" 'bash -s' <"$remote_script"
  rm -f "$remote_script"
  timer_finish "remote batch" "$started_at"
}

show_config() {
  cat <<EOF
target: $TARGET_NAME ($TARGET_LABEL)
host: $SSH_USER@$SSH_HOST:$SSH_PORT
identity_file: $SSH_KEY
project_path: $PROJECT_PATH
site_url: $SITE_URL
sync_mode: $SYNC_MODE
sync_strategy: $SYNC_STRATEGY
exclude_file: $EXCLUDE_FILE
EOF
}

ensure_sync_prerequisites() {
  if [[ "$SYNC_MODE" != "rsync" ]]; then
    echo "Unsupported sync mode: $SYNC_MODE" >&2
    exit 1
  fi

  if ! command -v rsync >/dev/null 2>&1; then
    echo "rsync is required for sync" >&2
    exit 1
  fi

  local exclude_path="$ROOT_DIR/$EXCLUDE_FILE"
  if [[ ! -f "$exclude_path" ]]; then
    echo "Missing exclude file: $exclude_path" >&2
    exit 1
  fi

  mkdir -p "$ROOT_DIR/runtime"
}

sync_full() {
  local started_at
  local exclude_path="$ROOT_DIR/$EXCLUDE_FILE"
  started_at=$(timer_start)
  log "start: rsync sync"
  rsync -az "${RSYNC_DELETE_ARGS[@]}" \
    --inplace \
    --no-perms \
    --no-owner \
    --no-group \
    --omit-dir-times \
    --exclude-from="$exclude_path" \
    -e "ssh -i '$SSH_KEY' -p '$SSH_PORT' -o StrictHostKeyChecking=accept-new" \
    "$ROOT_DIR/" "$SSH_USER@$SSH_HOST:$PROJECT_PATH/"
  timer_finish "rsync sync" "$started_at"
}

collect_changed_files() {
  local path
  (
    cd "$ROOT_DIR"
    while IFS= read -r path; do
      [[ -z "$path" ]] && continue
      if [[ -e "$path" || -L "$path" ]]; then
        printf '%s\n' "$path"
      fi
    done < <(
      {
        git diff --name-only --relative
        git diff --name-only --relative --cached
        git ls-files --others --exclude-standard
      } | awk 'NF' | sort -u
    )
  )
}

collect_deleted_files() {
  (
    cd "$ROOT_DIR"
    {
      git diff --name-only --relative --diff-filter=D
      git diff --name-only --relative --cached --diff-filter=D
    } | awk 'NF' | sort -u
  )
}

sync_incremental() {
  local started_at changed_file deleted_file changed_count deleted_count remote_script
  started_at=$(timer_start)
  changed_file="$(mktemp)"
  deleted_file="$(mktemp)"

  collect_changed_files >"$changed_file"
  collect_deleted_files >"$deleted_file"

  changed_count=$(wc -l <"$changed_file" | tr -d ' ')
  deleted_count=$(wc -l <"$deleted_file" | tr -d ' ')
  log "start: incremental sync (changed=${changed_count}, deleted=${deleted_count})"

  if [[ "$changed_count" -gt 0 ]]; then
    rsync -az \
      --inplace \
      --no-perms \
      --no-owner \
      --no-group \
      --omit-dir-times \
      --exclude-from="$ROOT_DIR/$EXCLUDE_FILE" \
      --files-from="$changed_file" \
      -e "ssh -i '$SSH_KEY' -p '$SSH_PORT' -o StrictHostKeyChecking=accept-new" \
      "$ROOT_DIR/" "$SSH_USER@$SSH_HOST:$PROJECT_PATH/"
  fi

  if [[ "$SYNC_DELETE" == "1" && "$deleted_count" -gt 0 ]]; then
    remote_script="$(mktemp)"
    {
      echo "set -euo pipefail"
      printf "cd %q\n" "$PROJECT_PATH"
      while IFS= read -r path; do
        [[ -z "$path" ]] && continue
        printf "rm -rf -- %q\n" "$path"
      done <"$deleted_file"
    } >"$remote_script"
    "${SSH_BASE[@]}" 'bash -s' <"$remote_script"
    rm -f "$remote_script"
  fi

  rm -f "$changed_file" "$deleted_file"
  timer_finish "incremental sync" "$started_at"
}

sync_code() {
  ensure_sync_prerequisites

  if [[ "$SYNC_STRATEGY" == "changed" ]]; then
    if command -v git >/dev/null 2>&1 && git -C "$ROOT_DIR" rev-parse --is-inside-work-tree >/dev/null 2>&1; then
      sync_incremental
      return
    fi
    log "fallback to full sync: git repository not available"
  fi

  sync_full
}

verify_remote() {
  local started_at
  started_at=$(timer_start)
  run_remote_batch 0 1

  if command -v curl >/dev/null 2>&1 && [[ -n "$SITE_URL" ]]; then
    while IFS= read -r path; do
      [[ -z "$path" ]] && continue
      log "smoke: $SITE_URL$path"
      curl -fsSIL --max-time 20 "$SITE_URL$path" >/dev/null
    done <<< "$SMOKE_URLS"
  else
    echo "skip smoke test: curl or site_url unavailable"
  fi

  timer_finish "verify remote" "$started_at"
}

open_shell() {
  "${SSH_BASE[@]}"
}

case "$COMMAND" in
  show-config)
    show_config
    ;;
  sync)
    sync_code
    run_remote_batch 1 0
    ;;
  verify)
    verify_remote
    ;;
  deploy)
    log "start: deploy"
    sync_code
    run_remote_batch 1 1
    if command -v curl >/dev/null 2>&1 && [[ -n "$SITE_URL" ]]; then
      deploy_started_at=$(timer_start)
      while IFS= read -r path; do
        [[ -z "$path" ]] && continue
        log "smoke: $SITE_URL$path"
        curl -fsSIL --max-time 20 "$SITE_URL$path" >/dev/null
      done <<< "$SMOKE_URLS"
      timer_finish "smoke checks" "$deploy_started_at"
    else
      echo "skip smoke test: curl or site_url unavailable"
    fi
    log "done: deploy"
    ;;
  shell)
    open_shell
    ;;
  *)
    echo "Usage: bash scripts/deploy.sh [show-config|sync|verify|deploy|shell]" >&2
    exit 1
    ;;
esac
