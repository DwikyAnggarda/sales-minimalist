#!/bin/sh
set -e

echo "==> Production Mode: Laravel 13"

# 1. Bersihkan semua cache lama agar environment variable baru terbaca
php artisan optimize:clear

# 2. Migrasi database & Seed (Flag --force wajib di production)
# Pastikan DatabaseSeeder kamu menggunakan updateOrCreate agar tidak duplikat saat redeploy
php artisan migrate --force

# 3. Laravel 13 Single Command Optimization
# Ini akan meng-cache config, routes, dan views secara sekaligus
php artisan optimize

echo "==> Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
