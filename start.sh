#!/usr/bin/env bash
set -euo pipefail
project_dir="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
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
  if [[ -n "$frontend_pid" ]]; then kill "$frontend_pid" 2>/dev/null || true; fi
  if [[ -n "$backend_pid" ]]; then kill "$backend_pid" 2>/dev/null || true; fi
}
trap cleanup EXIT INT TERM
cd "$project_dir/BACKEND"
"$php_runtime" artisan serve --host=127.0.0.1 --port=8001 --tries=1 &
backend_pid=$!
cd "$project_dir/FRONTEND"
node node_modules/vite/bin/vite.js --host=127.0.0.1 --port=5174 &
frontend_pid=$!
echo "Dream Travel and Tours: http://127.0.0.1:5174"
wait -n "$backend_pid" "$frontend_pid"
