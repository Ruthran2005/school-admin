#!/usr/bin/env bash
# School ERP - Admin app (standalone). Needs PHP 8.2+ and Composer.   Usage: bash setup.sh
set -e
ROOT="$(cd "$(dirname "$0")" && pwd)"
DIR="$ROOT/laravel-admin"
PORT=8000

setenv() { sed -i.bak "/^#* *$1=/d" .env && rm -f .env.bak && echo "$1=$2" >> .env; }

[ -d "$DIR" ] || composer create-project laravel/laravel "$DIR" --no-interaction
for d in app database resources routes; do cp -R "$ROOT/$d" "$DIR/"; done
cd "$DIR"

setenv APP_NAME "\"School ERP Admin\""
setenv APP_URL "http://127.0.0.1:$PORT"
setenv SESSION_DRIVER file
setenv SESSION_COOKIE school_admin_session
setenv CACHE_STORE file
setenv QUEUE_CONNECTION sync
# Own database by default (SQLite file inside this project).
# To share a database with other School ERP apps, run like:
#   DB_CONNECTION=mysql DB_DATABASE=school_erp DB_USERNAME=root DB_PASSWORD= bash setup.sh
if [ -n "$DB_CONNECTION" ]; then
  setenv DB_CONNECTION "$DB_CONNECTION"
  setenv DB_HOST "${DB_HOST:-127.0.0.1}"; setenv DB_PORT "${DB_PORT:-3306}"
  setenv DB_DATABASE "${DB_DATABASE:-school_erp}"; setenv DB_USERNAME "${DB_USERNAME:-root}"; setenv DB_PASSWORD "${DB_PASSWORD:-}"
fi
php artisan key:generate --force
php artisan migrate --seed --force

echo
echo "Done. Start:  cd laravel-admin && php artisan serve --port=$PORT"
echo "Demo login: admin@school.test / password"
