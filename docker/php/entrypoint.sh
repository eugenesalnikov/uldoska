#!/bin/sh
set -e

if [ ! -f /var/www/html/storage/app/.gitignore ]; then
  cp -a /var/www/storage-init/. /var/www/html/storage/
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

exec "$@"
