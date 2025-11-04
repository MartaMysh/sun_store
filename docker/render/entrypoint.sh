#!/usr/bin/env sh
set -e

# Wait for DB optionally (simple loop, adjust timeout as needed)
if [ -n "${DB_HOST:-}" ]; then
  echo "Waiting for database ${DB_HOST}:${DB_PORT:-5432}..."
  COUNT=0
  while ! (php -r "exit((int) !(@fsockopen('${DB_HOST}', ${DB_PORT:-5432})));"); do
    COUNT=$((COUNT+1))
    if [ "$COUNT" -gt 30 ]; then
      echo "Database did not become available in time" >&2
      break
    fi
    sleep 1
  done
fi

# Ensure .env exists
if [ ! -f .env ]; then
  if [ -f .env.example ]; then
    cp .env.example .env
  fi
fi

# Generate app key if missing
php artisan key:generate --force || true

# Run migrations (optional — remove if you prefer manual migrate/deploy hook)
php artisan migrate --force || true

# Clear & cache config/routes if desired
php artisan config:clear || true
php artisan route:clear || true
php artisan config:cache || true

# Start the built-in server on the port Render provides
exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"