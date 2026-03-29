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

run_remote() {
  "${SSH_BASE[@]}" "cd '$PROJECT_PATH' && $1"
}

generate_remote_api_docs() {
  echo "run: sudo php think api:doc --output docs/api-v1.md"
  run_remote "sudo php think api:doc --output docs/api-v1.md"
  echo "run: sudo php think api:doc --format=openapi --output public/docs/api-v1.openapi.json"
  run_remote "sudo php think api:doc --format=openapi --output public/docs/api-v1.openapi.json"
  run_remote "sudo chown '$SSH_USER:$SSH_USER' docs/api-v1.md public/docs/api-v1.openapi.json"
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
  rsync -az "${RSYNC_DELETE_ARGS[@]}" \
    --inplace \
    --no-perms \
    --no-owner \
    --no-group \
    --omit-dir-times \
    --exclude-from="$exclude_path" \
    -e "ssh -i '$SSH_KEY' -p '$SSH_PORT' -o StrictHostKeyChecking=accept-new" \
    "$ROOT_DIR/" "$SSH_USER@$SSH_HOST:$PROJECT_PATH/"

  fix_runtime_permissions
}

fix_runtime_permissions() {
  run_remote "sudo chown -R www:www runtime && sudo find runtime -type d -exec chmod 775 {} + && sudo find runtime -type f -exec chmod 664 {} +"
}

verify_remote() {
  local lint_failed=0
  while IFS= read -r file; do
    [[ -z "$file" ]] && continue
    echo "php -l $file"
    if ! run_remote "php -l '$file'"; then
      lint_failed=1
    fi
  done <<< "$PHP_LINT_FILES"

  while IFS= read -r cmd; do
    [[ -z "$cmd" ]] && continue
    echo "run: $cmd"
    run_remote "$cmd"
  done <<< "$CLEAR_COMMANDS"

  generate_remote_api_docs

  if command -v curl >/dev/null 2>&1 && [[ -n "$SITE_URL" ]]; then
    while IFS= read -r path; do
      [[ -z "$path" ]] && continue
      echo "smoke: $SITE_URL$path"
      curl -I -L --max-time 20 "$SITE_URL$path" >/dev/null
    done <<< "$SMOKE_URLS"
  else
    echo "skip smoke test: curl or site_url unavailable"
  fi

  if [[ "$lint_failed" == "1" ]]; then
    exit 1
  fi
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
    ;;
  verify)
    verify_remote
    ;;
  deploy)
    sync_code
    verify_remote
    ;;
  shell)
    open_shell
    ;;
  *)
    echo "Usage: bash scripts/deploy.sh [show-config|sync|verify|deploy|shell]" >&2
    exit 1
    ;;
esac
