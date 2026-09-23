# Production deployment

`deploy.sh` provides repeatable, atomic Laravel deployments. It builds and tests
locally, prepares a complete release away from the live document root, switches
the live path atomically, and verifies the application over production HTTPS.
Laravel maintenance mode is not used.

## Server layout

The Nginx document root remains `/var/www/vowlyn/public`.

- `/var/www/vowlyn` — symlink to the active release
- `/var/www/vowlyn-releases/<release-id>` — immutable application releases
- `/var/www/vowlyn-shared/.env` — production environment file
- `/var/www/vowlyn-shared/storage` — persistent uploads and Laravel runtime data

The first deployment converts the current `/var/www/vowlyn` directory to this
layout. The conversion uses an atomic Linux filesystem exchange and retains the
original installation as `/var/www/vowlyn-legacy-<release-id>`.

## Requirements

Local machine:

- Bash, SSH, rsync, PHP, Composer, Node.js, and npm
- Production code checked out in this repository

Production VPS:

- Linux filesystem supporting `renameat2(RENAME_EXCHANGE)`
- PHP 8.4 CLI and an active `php8.4-fpm` service
- Composer, curl, rsync, Python 3, ImageMagick, FFmpeg/FFprobe, and systemd
- Root SSH access, or a deployment user with non-interactive restricted sudo
- Existing Laravel application at `/var/www/vowlyn` for the first bootstrap
- `APP_ENV=production` and `APP_DEBUG=false`

The script validates these requirements before changing the live application.

Video uploads also require Vowlyn-scoped PHP and Nginx upload limits described in
[CONTENT-VIDEOS.md](CONTENT-VIDEOS.md). Local SQLite databases and environment
files are excluded from release uploads; production data remains on the VPS.

Leads are answered by email from the admin panel, so the production `.env` must
carry real `MAIL_*` values (see [MAIL-REPLIES.md](MAIL-REPLIES.md)). Until it
does, the panel warns and records replies as logged rather than delivered — no
code change or extra package is required to switch it on.

## Usage

From the local `site` directory, run this once for the first atomic deployment:

```bash
./deploy.sh --bootstrap
```

For every later update, run:

```bash
./deploy.sh
```

OpenSSH prompts once for the VPS password when no SSH key is configured. The
script does not accept a password variable and never stores or prints the
password. SSH keys are recommended for routine deployments but are optional.

Run all local release checks without connecting to production:

```bash
./deploy.sh --check
```

Switch to the most recent previous release and verify it:

```bash
./deploy.sh --rollback
```

## What a deployment does

1. Validates and audits Composer and npm dependencies.
2. Installs local dependencies, runs the complete Laravel test suite, and builds Vite assets.
3. Opens one multiplexed SSH session, producing at most one native password prompt.
4. Acquires an exclusive production lock so two deployments cannot overlap.
5. Validates the production environment, PHP-FPM, ImageMagick, and deployment layout.
6. Uploads code to a new timestamped release directory.
7. Installs production Composer dependencies and warms Laravel/Filament caches off-line.
8. Runs forward-compatible database migrations.
9. Atomically switches `/var/www/vowlyn` to the new release and gracefully reloads PHP-FPM.
10. Checks all important public, admin, blog, RSS, and sitemap URLs over HTTPS.
11. Automatically restores the previous release if activation or health checks fail.

The latest five releases are retained by default, including the active release.
Override this with `DEPLOY_KEEP_RELEASES`.

## Configuration

Defaults match the current Vowlyn server. Override values only when the server
configuration has intentionally changed:

```bash
DEPLOY_HOST=76.13.155.120 \
DEPLOY_USER=root \
DEPLOY_DOMAIN=vowlyn.com \
DEPLOY_KEEP_RELEASES=5 \
./deploy.sh
```

Supported variables:

- `DEPLOY_HOST`
- `DEPLOY_USER`
- `DEPLOY_DOMAIN`
- `DEPLOY_APP_LINK`
- `DEPLOY_RELEASES_DIR`
- `DEPLOY_SHARED_DIR`
- `DEPLOY_PHP`
- `DEPLOY_FPM_SERVICE`
- `DEPLOY_KEEP_RELEASES`
- `LOCAL_PHP`

## Scheduled tasks

The scheduler must be running on the server, or cold outreach never sends and a
paused campaign looks like a broken one. One cron entry is enough:

```cron
* * * * * cd /var/www/vowlyn && php8.4 artisan schedule:run >> /dev/null 2>&1
```

That runs `outreach:send` every fifteen minutes, which sends only what the
sending window and the daily limit allow. Verify it with:

```bash
php artisan schedule:list          # what is scheduled
php artisan outreach:send          # run it once, by hand
```

See `OUTREACH.md` for the limits and `MAIL-REPLIES.md` for the mail credentials.
Both features send with the same mailer, so if one is configured, both are.

## Zero-downtime rules

Atomic code switching cannot make a destructive database migration safe. Every
production migration must use the expand/contract approach:

1. Add nullable columns, new tables, or compatible indexes first.
2. Deploy code that supports both old and new schemas.
3. Backfill data separately when necessary.
4. Remove old columns or tables only in a later release after old code is no longer used.

The deploy script intentionally never runs `migrate:rollback`, because reversing
a migration during an application rollback can destroy production data.

Maintain an infrastructure-level database backup policy and verify restore
procedures independently. Database credentials and backup storage are deliberately
not guessed or embedded in this repository.

## Recovery notes

If a new release fails its HTTPS checks, the script restores the prior code and
reloads PHP-FPM automatically. Review the production log with:

```bash
ssh root@76.13.155.120 'tail -100 /var/www/vowlyn/storage/logs/laravel.log'
```

Do not remove the one-time `vowlyn-legacy-*` directory until the first atomic
deployment and a later rollback drill have both been verified.
