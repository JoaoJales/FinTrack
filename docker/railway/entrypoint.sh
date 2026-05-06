#!/bin/sh
set -e

# Railway (e outros PaaS) injetam PORT; fallback para desenvolvimento local.
export PORT="${PORT:-8080}"

mkdir -p /etc/nginx/http.d

envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/http.d/default.conf

php artisan migrate --force

if [ "$RUN_SEED" = "true" ]; then
  php artisan db:seed --force
  php artisan db:seed --class=TestDataSeeder --force
fi

exec /usr/bin/supervisord -c /etc/supervisord.conf
