#!/bin/sh
set -e

# Railway (e outros PaaS) injetam PORT; fallback para desenvolvimento local.
export PORT="${PORT:-8080}"

mkdir -p /etc/nginx/http.d

envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/http.d/default.conf

exec /usr/bin/supervisord -c /etc/supervisord.conf
