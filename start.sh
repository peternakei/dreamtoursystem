#!/usr/bin/env bash
set -euo pipefail
project_dir="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
for dependency in node setsid; do
  if ! command -v "$dependency" >/dev/null 2>&1; then
    printf 'Required command not found: %s\n' "$dependency" >&2
    exit 1
  fi
done
if [[ ! -f "$project_dir/FRONTEND/node_modules/vite/bin/vite.js" ]]; then
  echo 'Frontend dependencies are missing. Run npm install in FRONTEND.' >&2
  exit 1
fi
# Fail before spawning either server; never kill an unrelated port owner.
node "$project_dir/scripts/check-dev-ports.mjs"
php_runtime=""
if [[ -n "${PHP_BIN:-}" ]]; then
  candidates=("$PHP_BIN")
else
  candidates=(php php8.4 php8.3 php8.2 "$HOME/.config/herd-lite/bin/php")
fi
for candidate in "${candidates[@]}"; do
  if ! command -v "$candidate" >/dev/null 2>&1; then continue; fi
  if runtime_result=$("$candidate" "$project_dir/scripts/check-php-runtime.php" 2>&1); then
    php_runtime=$(command -v "$candidate")
    echo "Backend runtime: $runtime_result"
    break
  fi
  printf 'Skipping %s: %s\n' "$candidate" "$runtime_result" >&2
done
if [[ -z "$php_runtime" ]]; then
  echo "No compatible PHP runtime found. Enable the configured database's PDO extension, or set PHP_BIN to a compatible PHP executable." >&2
  exit 1
fi
backend_pid=""
frontend_pid=""
cleanup() {
  # Each server owns a separate process group, including PHP's child server.
  trap '' INT TERM
  for server_pid in "$frontend_pid" "$backend_pid"; do
    if [[ -n "$server_pid" ]]; then kill -TERM -- "-$server_pid" 2>/dev/null || true; fi
  done
  for server_pid in "$frontend_pid" "$backend_pid"; do
    if [[ -n "$server_pid" ]]; then wait "$server_pid" 2>/dev/null || true; fi
  done
}
trap cleanup EXIT
trap 'exit 130' INT
trap 'exit 143' TERM
cd "$project_dir/BACKEND"
setsid "$php_runtime" artisan serve --host=127.0.0.1 --port=8001 --tries=1 &
backend_pid=$!
cd "$project_dir/FRONTEND"
setsid node node_modules/vite/bin/vite.js --host=127.0.0.1 --port=5174 --strictPort &
frontend_pid=$!
node "$project_dir/scripts/check-dev-ports.mjs" --wait
kill -0 "$backend_pid" "$frontend_pid"
echo "Dream Travel and Tours is ready: http://127.0.0.1:5174"
echo "Keep this terminal open. Press Ctrl+C to stop both servers."
wait -n "$backend_pid" "$frontend_pid"
