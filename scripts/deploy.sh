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
exclude_file: $EXCLUDE_FILE
EOF
}

sync_code() {
  local started_at
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

  started_at=$(timer_start)
  log "start: rsync sync"
  mkdir -p "$ROOT_DIR/runtime"
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
