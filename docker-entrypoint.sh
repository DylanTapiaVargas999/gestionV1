#!/bin/sh
set -e

# If CA in env as base64, write it to file
if [ -n "$MYSQL_ATTR_SSL_CA_BASE64" ]; then
  echo "$MYSQL_ATTR_SSL_CA_BASE64" | base64 -d > /etc/ssl/certs/ca.pem
  export MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca.pem
fi

# If CA file already exists in repo, use it
if [ -f "/etc/ssl/certs/ca.pem" ]; then
  export MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca.pem
fi

# Ensure storage permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

# Migrations are intentionally not run automatically in this entrypoint.
# If you need to run migrations, do it manually with:
#   docker exec -it <container> php artisan migrate --force
# or run a one-off job in your hosting provider.

exec "$@"
