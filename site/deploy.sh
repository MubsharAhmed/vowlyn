#!/usr/bin/env bash
#
# Atomic deployment for the Vowlyn Laravel application.
#
# First production run:
#   ./deploy.sh --bootstrap
#
# Later deployments:
#   ./deploy.sh
#
# Other operations:
#   ./deploy.sh --check       # local validation only; no server connection
#   ./deploy.sh --rollback    # switch to the previous healthy release
#
# SSH asks for the VPS password once when a key is not configured. The password
# is handled by OpenSSH and is never read, stored, or echoed by this script.

set -Eeuo pipefail

DEPLOY_HOST="${DEPLOY_HOST:-76.13.155.120}"
DEPLOY_USER="${DEPLOY_USER:-root}"
APP_LINK="${DEPLOY_APP_LINK:-/var/www/vowlyn}"
RELEASES_DIR="${DEPLOY_RELEASES_DIR:-/var/www/vowlyn-releases}"
SHARED_DIR="${DEPLOY_SHARED_DIR:-/var/www/vowlyn-shared}"
REMOTE_PHP="${DEPLOY_PHP:-php8.4}"
FPM_SERVICE="${DEPLOY_FPM_SERVICE:-php8.4-fpm}"
LOCAL_PHP="${LOCAL_PHP:-php}"
DOMAIN="${DEPLOY_DOMAIN:-vowlyn.com}"
KEEP_RELEASES="${DEPLOY_KEEP_RELEASES:-5}"

LOCAL_SRC="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/"
TARGET="${DEPLOY_USER}@${DEPLOY_HOST}"
HEALTH_PATHS=(
  "/"
  "/about"
  "/services"
  "/portfolio"
  "/content-creation"
  "/performance-marketing"
  "/why-us"
  "/admin/login"
  "/services/web-app-development"
  "/services/mobile-app-development"
  "/services/ai-development"
  "/services/saas-development"
  "/services/enterprise-security"
  "/services/cloud-devops"
  "/blog"
  "/blog/subscribe"
  "/blog/feed.xml"
  "/sitemap.xml"
)

MODE="deploy"
case "${1:-}" in
  "") ;;
  --bootstrap) MODE="bootstrap" ;;
  --rollback) MODE="rollback" ;;
  --check) MODE="check" ;;
  --help|-h)
    sed -n '2,17p' "$0"
    exit 0
    ;;
  *)
    echo "Unknown option: $1" >&2
    echo "Use --help for supported operations." >&2
    exit 2
    ;;
esac

say() { printf '\n\033[1;35m▶ %s\033[0m\n' "$1"; }
die() { printf '\nERROR: %s\n' "$1" >&2; exit 1; }

for configured_path in "$APP_LINK" "$RELEASES_DIR" "$SHARED_DIR"; do
  [[ "$configured_path" =~ ^/[A-Za-z0-9._-]+(/[A-Za-z0-9._-]+){2,}$ ]] || \
    die "Deployment paths must be absolute, safe paths at least three levels deep."
done
[[ "$APP_LINK" != "$RELEASES_DIR" && "$APP_LINK" != "$SHARED_DIR" && "$RELEASES_DIR" != "$SHARED_DIR" ]] || \
  die "Application, release, and shared paths must be different."
for parent_path in "$APP_LINK" "$RELEASES_DIR" "$SHARED_DIR"; do
  for child_path in "$APP_LINK" "$RELEASES_DIR" "$SHARED_DIR"; do
    [[ "$parent_path" == "$child_path" ]] && continue
    [[ "$child_path/" != "$parent_path/"* ]] || die "Deployment paths must not be nested inside one another."
  done
done
[[ "$KEEP_RELEASES" =~ ^[1-9][0-9]*$ ]] || die "DEPLOY_KEEP_RELEASES must be a positive integer."
[[ "$DOMAIN" =~ ^[A-Za-z0-9.-]+$ ]] || die "DEPLOY_DOMAIN contains invalid characters."
[[ "$DEPLOY_HOST" =~ ^[A-Za-z0-9.-]+$ ]] || die "DEPLOY_HOST contains invalid characters."
[[ "$DEPLOY_USER" =~ ^[A-Za-z_][A-Za-z0-9_-]*$ ]] || die "DEPLOY_USER contains invalid characters."

run_local_release_gates() {
  say "Running local release gates"

  for command_name in composer npm rsync ssh ffmpeg ffprobe "$LOCAL_PHP"; do
    command -v "$command_name" >/dev/null || die "Required local command is missing: $command_name"
  done
  command -v magick >/dev/null || command -v convert >/dev/null || \
    die "Required local command is missing: ImageMagick (magick or convert)"

  (
    cd "$LOCAL_SRC"
    composer validate --no-check-publish --strict
    composer audit --locked --no-dev
    composer install --prefer-dist --no-interaction --no-progress
    npm ci
    npm audit --audit-level=high
    "$LOCAL_PHP" artisan test
    npm run build
  )
}

if [[ "$MODE" != "rollback" ]]; then
  run_local_release_gates
fi

if [[ "$MODE" == "check" ]]; then
  printf '\n\033[1;32m✓ Local deployment checks passed. No server connection was made.\033[0m\n'
  exit 0
fi

SSH_CONTROL_DIR="$(mktemp -d -t vowlyn-deploy-ssh.XXXXXX)"
SSH_CONTROL_SOCKET="$SSH_CONTROL_DIR/control"
DEPLOY_LOCK_DIR="/var/lock/vowlyn-deploy.lock.d"
DEPLOY_LOCK_TOKEN="$(date -u +%Y%m%dT%H%M%SZ)-$$-${RANDOM}"
DEPLOY_LOCK_ACQUIRED=0
SSH_OPTIONS=(
  -o StrictHostKeyChecking=accept-new
  -o ConnectTimeout=20
  -o ServerAliveInterval=30
  -o ServerAliveCountMax=4
)

close_ssh_master() {
  ssh -S "$SSH_CONTROL_SOCKET" -O exit "$TARGET" >/dev/null 2>&1 || true
  rmdir "$SSH_CONTROL_DIR" >/dev/null 2>&1 || true
}
release_deploy_lock() {
  if [[ "$DEPLOY_LOCK_ACQUIRED" -eq 1 ]]; then
    ssh -S "$SSH_CONTROL_SOCKET" "${SSH_OPTIONS[@]}" "$TARGET" bash -s -- \
      "$DEPLOY_LOCK_DIR" "$DEPLOY_LOCK_TOKEN" <<'REMOTE_UNLOCK' >/dev/null 2>&1 || true
set -euo pipefail
lock_dir="$1"
lock_token="$2"
owner="$(sudo cat "$lock_dir/owner" 2>/dev/null || true)"
if [[ "$owner" == "$lock_token" ]]; then
  sudo rm -rf -- "$lock_dir"
fi
REMOTE_UNLOCK
    DEPLOY_LOCK_ACQUIRED=0
  fi
}
cleanup_transport() {
  release_deploy_lock
  close_ssh_master
}
trap cleanup_transport EXIT

say "Opening one secure SSH session to $TARGET"
echo "If SSH key authentication is not configured, enter the VPS password at the OpenSSH prompt."
ssh -M -S "$SSH_CONTROL_SOCKET" -o ControlPersist=600 "${SSH_OPTIONS[@]}" -fnNT "$TARGET"

SSH=(ssh -S "$SSH_CONTROL_SOCKET" "${SSH_OPTIONS[@]}")
RSH="ssh -S $SSH_CONTROL_SOCKET -o StrictHostKeyChecking=accept-new -o ConnectTimeout=20"

say "Acquiring the production deployment lock"
DEPLOY_LOCK_ACQUIRED=1
"${SSH[@]}" "$TARGET" bash -s -- "$DEPLOY_LOCK_DIR" "$DEPLOY_LOCK_TOKEN" <<'REMOTE_LOCK'
set -Eeuo pipefail
lock_dir="$1"
lock_token="$2"
if ! sudo mkdir "$lock_dir" 2>/dev/null; then
  echo "Another deployment lock exists at $lock_dir." >&2
  owner="$(sudo cat "$lock_dir/owner" 2>/dev/null || true)"
  if [[ -n "$owner" ]]; then
    echo "Lock owner: $owner" >&2
  fi
  exit 1
fi
if ! printf '%s\n' "$lock_token" | sudo tee "$lock_dir/owner" >/dev/null; then
  sudo rmdir "$lock_dir" >/dev/null 2>&1 || true
  exit 1
fi
sudo chmod 700 "$lock_dir"
sudo chmod 600 "$lock_dir/owner"
REMOTE_LOCK

say "Checking production prerequisites"
"${SSH[@]}" "$TARGET" bash -s -- \
  "$MODE" "$APP_LINK" "$RELEASES_DIR" "$SHARED_DIR" "$REMOTE_PHP" "$FPM_SERVICE" <<'REMOTE_PREFLIGHT'
set -Eeuo pipefail

mode="$1"
app_link="$2"
releases_dir="$3"
shared_dir="$4"
php_bin="$5"
fpm_service="$6"

for command_name in "$php_bin" composer curl rsync sudo systemctl python3 mv readlink ffmpeg ffprobe; do
  command -v "$command_name" >/dev/null || {
    echo "Missing required server command: $command_name" >&2
    exit 1
  }
done
if ! command -v magick >/dev/null && ! command -v convert >/dev/null; then
  echo "Missing required server command: ImageMagick (magick or convert)" >&2
  exit 1
fi

sudo -n true >/dev/null 2>&1 || {
  echo "The deploy user requires passwordless sudo. Use root or configure a restricted sudo rule." >&2
  exit 1
}
sudo systemctl is-active --quiet "$fpm_service" || {
  echo "PHP-FPM service is not active: $fpm_service" >&2
  exit 1
}
case "$mode" in
  bootstrap)
    [[ -d "$app_link" && ! -L "$app_link" && -f "$app_link/artisan" ]] || {
      echo "Bootstrap expects the current Laravel app to be a real directory at $app_link." >&2
      exit 1
    }
    [[ -f "$app_link/.env" ]] || { echo "Missing production .env at $app_link/.env" >&2; exit 1; }
    [[ -d "$app_link/storage" ]] || { echo "Missing production storage directory." >&2; exit 1; }
    if [[ -L "$app_link/storage" ]]; then
      [[ "$(readlink -f "$app_link/storage")" == "$(readlink -m "$shared_dir/storage")" ]] || {
        echo "Existing storage symlink does not target $shared_dir/storage." >&2
        exit 1
      }
    fi

    # Prove that this filesystem supports the atomic directory/symlink exchange
    # required for the one-time conversion before touching the live path.
    atomic_test="$(sudo mktemp -d "$(dirname "$app_link")/.vowlyn-atomic-test.XXXXXX")"
    trap 'sudo rm -rf -- "$atomic_test"' EXIT
    sudo mkdir "$atomic_test/directory"
    sudo ln -s "$atomic_test/directory" "$atomic_test/link"
    sudo python3 - "$atomic_test/directory" "$atomic_test/link" <<'PY'
import ctypes
import os
import sys

AT_FDCWD = -100
RENAME_EXCHANGE = 2
libc = ctypes.CDLL(None, use_errno=True)
result = libc.renameat2(AT_FDCWD, os.fsencode(sys.argv[1]), AT_FDCWD, os.fsencode(sys.argv[2]), RENAME_EXCHANGE)
if result != 0:
    errno = ctypes.get_errno()
    raise OSError(errno, os.strerror(errno))
PY
    ;;
  deploy)
    [[ -L "$app_link" && -f "$app_link/artisan" ]] || {
      echo "Atomic layout is not initialized. Run ./deploy.sh --bootstrap once." >&2
      exit 1
    }
    [[ -f "$shared_dir/.env" && -d "$shared_dir/storage" ]] || {
      echo "Shared production state is incomplete at $shared_dir." >&2
      exit 1
    }
    ;;
  rollback)
    [[ -L "$app_link" && -f "$app_link/artisan" ]] || {
      echo "Rollback requires an initialized atomic deployment layout." >&2
      exit 1
    }
    [[ -d "$releases_dir" ]] || { echo "Release directory not found." >&2; exit 1; }
    ;;
esac

runtime_info=$(cd "$app_link" && sudo -u www-data "$php_bin" artisan about --only=environment --no-ansi)
grep -Eq 'Environment[ .]+production' <<<"$runtime_info" || {
  echo "Deployment refused: APP_ENV is not production." >&2
  exit 1
}
grep -Eq 'Debug Mode[ .]+(DISABLED|OFF)' <<<"$runtime_info" || {
  echo "Deployment refused: APP_DEBUG must be false." >&2
  exit 1
}
REMOTE_PREFLIGHT

if [[ "$MODE" == "rollback" ]]; then
  say "Switching to the previous release"
  "${SSH[@]}" "$TARGET" bash -s -- \
    "$APP_LINK" "$RELEASES_DIR" "$REMOTE_PHP" "$FPM_SERVICE" "$DOMAIN" "${HEALTH_PATHS[@]}" <<'REMOTE_ROLLBACK'
set -Eeuo pipefail

app_link="$1"
releases_dir="$2"
php_bin="$3"
fpm_service="$4"
domain="$5"
shift 5
health_paths=("$@")

current="$(readlink -f "$app_link")"
legacy_pattern="$(dirname "$app_link")/$(basename "$app_link")-legacy-*"
mapfile -t candidates < <(
  find "$releases_dir" -mindepth 1 -maxdepth 1 -type d -printf '%T@ %p\n'
  compgen -G "$legacy_pattern" | while IFS= read -r path; do
    [[ -d "$path" ]] && printf '%s %s\n' "$(stat -c %Y "$path")" "$path"
  done
)
IFS=$'\n' candidates=($(printf '%s\n' "${candidates[@]}" | sort -nr | cut -d' ' -f2-))
unset IFS

previous=""
for candidate in "${candidates[@]}"; do
  if [[ "$(readlink -f "$candidate")" != "$current" && -f "$candidate/artisan" ]]; then
    previous="$candidate"
    break
  fi
done
[[ -n "$previous" ]] || { echo "No previous Laravel release is available." >&2; exit 1; }

next_link="${app_link}.next"
[[ ! -e "$next_link" && ! -L "$next_link" ]] || { echo "$next_link already exists." >&2; exit 1; }
sudo ln -s "$previous" "$next_link"

switched=0
verified=0
recover() {
  if [[ "$switched" -eq 1 && "$verified" -eq 0 ]]; then
    echo "Rollback health check failed; restoring $current" >&2
    sudo ln -s "$current" "$next_link"
    sudo mv -Tf "$next_link" "$app_link"
    sudo systemctl reload "$fpm_service" || true
    (cd "$app_link" && sudo -u www-data "$php_bin" artisan queue:restart) || true
  else
    sudo rm -f -- "$next_link"
  fi
}
trap recover EXIT

sudo mv -Tf "$next_link" "$app_link"
switched=1
sudo systemctl reload "$fpm_service"
(cd "$app_link" && sudo -u www-data "$php_bin" artisan queue:restart)

for path in "${health_paths[@]}"; do
  code=$(curl --silent --show-error --output /dev/null --write-out '%{http_code}' \
    --connect-timeout 5 --max-time 20 --resolve "${domain}:443:127.0.0.1" "https://${domain}${path}" || true)
  printf '  %s  %s\n' "${code:-curl-error}" "$path"
  [[ "$code" == "200" ]] || exit 1
done

verified=1
trap - EXIT
echo "Rollback complete: $previous"
REMOTE_ROLLBACK

  printf '\n\033[1;32m✓ Previous release restored and verified.\033[0m\n'
  exit 0
fi

GIT_REF="nogit"
if command -v git >/dev/null && git -C "$LOCAL_SRC" rev-parse --is-inside-work-tree >/dev/null 2>&1; then
  GIT_REF="$(git -C "$LOCAL_SRC" rev-parse --short=12 HEAD)"
  if [[ -n "$(git -C "$LOCAL_SRC" status --porcelain --untracked-files=normal -- .)" ]]; then
    GIT_REF="${GIT_REF}-dirty"
  fi
fi
RELEASE_ID="$(date -u +%Y%m%d%H%M%S)-${GIT_REF}"
RELEASE_DIR="${RELEASES_DIR}/${RELEASE_ID}"

release_ready=0
remove_incomplete_release() {
  if [[ "$release_ready" -eq 0 ]]; then
    "${SSH[@]}" "$TARGET" bash -s -- "$RELEASE_DIR" "$RELEASES_DIR" "$APP_LINK" <<'REMOTE_REMOVE' >/dev/null 2>&1 || true
set -euo pipefail
release_dir="$1"
releases_dir="$2"
app_link="$3"
if [[ -L "$app_link" && "$(readlink -f "$app_link")" == "$(readlink -f "$release_dir")" ]]; then
  exit 0
fi
case "$release_dir" in
  "$releases_dir"/*) sudo rm -rf -- "$release_dir" ;;
esac
REMOTE_REMOVE
  fi
  cleanup_transport
}
trap remove_incomplete_release EXIT

say "Preparing isolated release $RELEASE_ID"
"${SSH[@]}" "$TARGET" bash -s -- \
  "$MODE" "$APP_LINK" "$RELEASES_DIR" "$SHARED_DIR" "$RELEASE_DIR" "$DEPLOY_USER" <<'REMOTE_PREPARE'
set -Eeuo pipefail

mode="$1"
app_link="$2"
releases_dir="$3"
shared_dir="$4"
release_dir="$5"
deploy_owner="$6"
stage_suffix="$(basename "$release_dir")"
env_stage="$shared_dir/.env.${stage_suffix}.tmp"
storage_stage="$shared_dir/.storage.${stage_suffix}.tmp"

clean_stages() {
  sudo rm -f -- "$env_stage"
  sudo rm -rf -- "$storage_stage"
}
trap clean_stages EXIT

[[ ! -e "$release_dir" ]] || { echo "Release already exists: $release_dir" >&2; exit 1; }
sudo mkdir -p "$releases_dir" "$shared_dir" "$release_dir"
sudo chown "$deploy_owner":www-data "$releases_dir" "$release_dir"

if [[ "$mode" == "bootstrap" ]]; then
  if [[ ! -f "$shared_dir/.env" ]]; then
    sudo install -o root -g www-data -m 640 "$app_link/.env" "$env_stage"
    sudo mv "$env_stage" "$shared_dir/.env"
  fi
  if [[ ! -d "$shared_dir/storage" ]]; then
    sudo mkdir -p "$storage_stage"
    sudo rsync -a "$app_link/storage/" "$storage_stage/"
    sudo mv "$storage_stage" "$shared_dir/storage"
  fi
fi

[[ -f "$shared_dir/.env" && -d "$shared_dir/storage" ]] || {
  echo "Shared .env or storage is missing." >&2
  exit 1
}
sudo chown -R www-data:www-data "$shared_dir/storage"
sudo find "$shared_dir/storage" -type d -exec chmod 775 {} +
sudo find "$shared_dir/storage" -type f -exec chmod 664 {} +
trap - EXIT
REMOTE_PREPARE

say "Uploading application code"
rsync -az --delete-delay --info=stats1 -e "$RSH" \
  --exclude '.git' \
  --exclude '.env' \
  --exclude '.env.*' \
  --exclude 'database/*.sqlite*' \
  --exclude '.phpunit.result.cache' \
  --exclude 'bootstrap/cache/*' \
  --exclude 'deploy.sh' \
  --exclude 'node_modules' \
  --exclude 'public/hot' \
  --exclude 'public/storage' \
  --exclude 'storage' \
  --exclude 'tests' \
  --exclude 'vendor' \
  "$LOCAL_SRC" "${TARGET}:${RELEASE_DIR}/"

say "Installing and warming the inactive release"
"${SSH[@]}" "$TARGET" bash -s -- \
  "$RELEASE_DIR" "$SHARED_DIR" "$REMOTE_PHP" "$DEPLOY_USER" <<'REMOTE_BUILD'
set -Eeuo pipefail

release_dir="$1"
shared_dir="$2"
php_bin="$3"
deploy_owner="$4"

cd "$release_dir"
ln -s "$shared_dir/.env" .env
ln -s "$shared_dir/storage" storage
mkdir -p bootstrap/cache

composer install \
  --no-dev \
  --no-scripts \
  --prefer-dist \
  --no-interaction \
  --no-progress \
  --classmap-authoritative

rm -f public/hot public/storage
ln -s ../storage/app/public public/storage

sudo chown -R "$deploy_owner":www-data "$release_dir"
sudo chmod -R u=rwX,g=rX,o=rX "$release_dir"
sudo chown -R www-data:www-data bootstrap/cache
sudo find bootstrap/cache -type d -exec chmod 775 {} +
sudo find bootstrap/cache -type f -exec chmod 664 {} +

sudo -u www-data "$php_bin" artisan package:discover --ansi

# Migrations run before cutover. Production migrations must follow the
# expand/contract pattern so the currently live release remains compatible.
sudo -u www-data "$php_bin" artisan migrate --force
sudo -u www-data "$php_bin" artisan filament:cache-components
sudo -u www-data "$php_bin" artisan optimize
sudo -u www-data "$php_bin" artisan route:list --no-ansi >/dev/null

runtime_info=$(sudo -u www-data "$php_bin" artisan about --only=environment --no-ansi)
grep -Eq 'Environment[ .]+production' <<<"$runtime_info"
grep -Eq 'Debug Mode[ .]+(DISABLED|OFF)' <<<"$runtime_info"
REMOTE_BUILD

say "Atomically activating and health-checking the release"
"${SSH[@]}" "$TARGET" bash -s -- "$DEPLOY_LOCK_DIR" "$DEPLOY_LOCK_TOKEN" <<'REMOTE_VERIFY_LOCK'
set -euo pipefail
lock_dir="$1"
lock_token="$2"
owner="$(sudo cat "$lock_dir/owner" 2>/dev/null || true)"
[[ "$owner" == "$lock_token" ]] || {
  echo "The production deployment lock was lost before cutover." >&2
  exit 1
}
REMOTE_VERIFY_LOCK
"${SSH[@]}" "$TARGET" bash -s -- \
  "$MODE" "$APP_LINK" "$RELEASES_DIR" "$SHARED_DIR" "$RELEASE_DIR" "$REMOTE_PHP" \
  "$FPM_SERVICE" "$DOMAIN" "$KEEP_RELEASES" "${HEALTH_PATHS[@]}" <<'REMOTE_CUTOVER'
set -Eeuo pipefail

mode="$1"
app_link="$2"
releases_dir="$3"
shared_dir="$4"
release_dir="$5"
php_bin="$6"
fpm_service="$7"
domain="$8"
keep_releases="$9"
shift 9
health_paths=("$@")

next_link="${app_link}.next"
[[ ! -e "$next_link" && ! -L "$next_link" ]] || { echo "$next_link already exists." >&2; exit 1; }

atomic_exchange() {
  sudo python3 - "$1" "$2" <<'PY'
import ctypes
import os
import sys

AT_FDCWD = -100
RENAME_EXCHANGE = 2
libc = ctypes.CDLL(None, use_errno=True)
result = libc.renameat2(AT_FDCWD, os.fsencode(sys.argv[1]), AT_FDCWD, os.fsencode(sys.argv[2]), RENAME_EXCHANGE)
if result != 0:
    errno = ctypes.get_errno()
    raise OSError(errno, os.strerror(errno))
PY
}

previous=""
switched=0
verified=0
recover() {
  if [[ "$switched" -eq 1 && "$verified" -eq 0 ]]; then
    echo "Health check failed; restoring the previous release." >&2
    if [[ "$mode" == "bootstrap" ]]; then
      atomic_exchange "$app_link" "$next_link" || true
    else
      sudo ln -s "$previous" "$next_link"
      sudo mv -Tf "$next_link" "$app_link" || true
    fi
    sudo systemctl reload "$fpm_service" || true
    (cd "$app_link" && sudo -u www-data "$php_bin" artisan queue:restart) || true
  fi
  sudo rm -f -- "$next_link"
}
trap recover EXIT

sudo ln -s "$release_dir" "$next_link"

if [[ "$mode" == "bootstrap" ]]; then
  # Convert the live app to shared storage atomically. This occurs immediately
  # before the app cutover, and the old storage directory is retained.
  if [[ ! -L "$app_link/storage" ]]; then
    storage_next="$app_link/.storage-next"
    [[ ! -e "$storage_next" && ! -L "$storage_next" ]] || {
      echo "Unexpected bootstrap path already exists: $storage_next" >&2
      exit 1
    }
    sudo ln -s "$shared_dir/storage" "$storage_next"
    atomic_exchange "$app_link/storage" "$storage_next"
    sudo rsync -a --ignore-existing "$storage_next/" "$shared_dir/storage/"
  fi

  atomic_exchange "$app_link" "$next_link"
else
  previous="$(readlink -f "$app_link")"
  sudo mv -Tf "$next_link" "$app_link"
fi
switched=1

sudo systemctl reload "$fpm_service"
(cd "$app_link" && sudo -u www-data "$php_bin" artisan queue:restart)

for path in "${health_paths[@]}"; do
  code=$(curl --silent --show-error --output /dev/null --write-out '%{http_code}' \
    --connect-timeout 5 --max-time 20 --resolve "${domain}:443:127.0.0.1" "https://${domain}${path}" || true)
  printf '  %s  %s\n' "${code:-curl-error}" "$path"
  [[ "$code" == "200" ]] || exit 1
done

verified=1
trap - EXIT

if [[ "$mode" == "bootstrap" ]]; then
  legacy_dir="$(dirname "$app_link")/$(basename "$app_link")-legacy-$(basename "$release_dir")"
  sudo mv "$next_link" "$legacy_dir"
  echo "Original installation retained at $legacy_dir"
fi

# Keep the active release and enough previous releases to reach the configured total.
current="$(readlink -f "$app_link")"
other_kept=0
while IFS= read -r candidate; do
  [[ -n "$candidate" ]] || continue
  [[ "$(readlink -f "$candidate")" == "$current" ]] && continue
  if (( other_kept < keep_releases - 1 )); then
    other_kept=$((other_kept + 1))
    continue
  fi
  case "$candidate" in
    "$releases_dir"/*) sudo rm -rf -- "$candidate" ;;
  esac
done < <(find "$releases_dir" -mindepth 1 -maxdepth 1 -type d -printf '%T@ %p\n' | sort -nr | cut -d' ' -f2-)
REMOTE_CUTOVER

release_ready=1
trap cleanup_transport EXIT

printf '\n\033[1;32m✓ Release %s is live and all HTTPS checks passed.\033[0m\n' "$RELEASE_ID"
printf 'Rollback command: ./deploy.sh --rollback\n'
