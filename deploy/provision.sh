#!/usr/bin/env bash
set -euo pipefail

# ============================================================
# Vowlyn — Server Provisioning Script (Ubuntu 22.04 / 24.04)
# Run once on a fresh VPS as root or with sudo.
# Usage: bash deploy/provision.sh <your-domain>
#   e.g.  bash deploy/provision.sh vowlyn.com
# ============================================================

DOMAIN="${1:-}"
if [ -z "$DOMAIN" ]; then
    echo "Usage: bash deploy/provision.sh <your-domain>"
    echo "  e.g.  bash deploy/provision.sh vowlyn.com"
    exit 1
fi

PHP_VERSION="8.3"

echo ">>> Updating system packages..."
apt update && apt upgrade -y

echo ">>> Installing Nginx, PHP $PHP_VERSION, and extensions..."
apt install -y nginx \
    php$PHP_VERSION-fpm \
    php$PHP_VERSION-cli \
    php$PHP_VERSION-mbstring \
    php$PHP_VERSION-xml \
    php$PHP_VERSION-curl \
    php$PHP_VERSION-zip \
    php$PHP_VERSION-bcmath \
    php$PHP_VERSION-gd \
    php$PHP_VERSION-sqlite3 \
    php$PHP_VERSION-intl \
    unzip curl git supervisor

echo ">>> Installing Composer..."
if ! command -v composer &>/dev/null; then
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    php -r "unlink('composer-setup.php');"
fi

echo ">>> Installing Node.js 22 LTS..."
if ! command -v node &>/dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
    apt install -y nodejs
fi

echo ">>> Creating deploy user..."
id -u deploy &>/dev/null || useradd -m -s /bin/bash deploy
usermod -aG www-data deploy
mkdir -p /home/deploy/.ssh
touch /home/deploy/.ssh/authorized_keys
chown -R deploy:deploy /home/deploy/.ssh
chmod 700 /home/deploy/.ssh
chmod 600 /home/deploy/.ssh/authorized_keys

echo ">>> Creating application directory..."
mkdir -p /var/www/vowlyn/site
chown -R deploy:www-data /var/www/vowlyn

echo ">>> Configuring Nginx..."
cat > /etc/nginx/sites-available/vowlyn <<NGINX
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN;
    root /var/www/vowlyn/site/public;

    index index.php index.html;
    charset utf-8;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    gzip on;
    gzip_vary on;
    gzip_types text/plain text/css text/xml application/json application/javascript image/svg+xml;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~* \.(?:css|js|gif|ico|jpg|jpeg|png|webp|avif|svg|woff2?|eot|ttf|otf)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    location ~ /\.(?!well-known) { deny all; }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php$PHP_VERSION-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    access_log /var/log/nginx/vowlyn-access.log;
    error_log  /var/log/nginx/vowlyn-error.log error;
}
NGINX

ln -sf /etc/nginx/sites-available/vowlyn /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx

echo ">>> Setting up Supervisor for queue worker..."
cat > /etc/supervisor/conf.d/vowlyn-queue.conf <<SUPER
[program:vowlyn-queue]
process_name=%(program_name)s_%(process_num)02d
command=php$PHP_VERSION /var/www/vowlyn/site/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=deploy
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/vowlyn/site/storage/logs/queue-worker.log
stopwaitsecs=3600
SUPER

supervisorctl reread && supervisorctl update || true

echo ">>> Setting up SSL with Certbot..."
apt install -y certbot python3-certbot-nginx
certbot --nginx -d "$DOMAIN" --non-interactive --agree-tos --email "admin@$DOMAIN" || {
    echo "NOTE: SSL setup skipped. Run later: certbot --nginx -d $DOMAIN"
}

echo ">>> Ensuring services are running..."
systemctl enable nginx php$PHP_VERSION-fpm
systemctl start nginx php$PHP_VERSION-fpm

echo ""
echo "=============================================="
echo "  PROVISIONING COMPLETE"
echo "=============================================="
echo ""
echo "  Domain  : https://$DOMAIN"
echo "  App dir : /var/www/vowlyn"
echo "  User    : deploy"
echo ""
echo "  NEXT: Add your SSH public key to the deploy user:"
echo "    cat ~/.ssh/id_ed25519.pub | ssh root@<vps-ip> \"tee -a /home/deploy/.ssh/authorized_keys\""
echo ""
echo "  Then set up GitHub Secrets for auto-deploy:"
echo "    VPS_HOST     = your VPS IP or domain"
echo "    VPS_USER     = deploy"
echo "    VPS_SSH_KEY  = your private SSH key content"
echo ""
echo "  Run the initial deploy manually:"
echo "    rsync -avz --delete site/ deploy@<vps-ip>:/var/www/vowlyn/site"
echo "    ssh deploy@<vps-ip> 'cd /var/www/vowlyn/site && php artisan key:generate && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache'"
echo ""
