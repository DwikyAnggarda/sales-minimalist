#!/bin/sh
set -e

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Caching config, routes, and views..."
php artisan optimize

echo "==> Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
