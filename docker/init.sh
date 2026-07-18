#!/bin/sh
# First-run WordPress install + theme/plugin activation.
set -e

WP_URL="${WP_URL:-http://localhost}"
ADMIN_USER="${ADMIN_USER:-admin}"
ADMIN_PASS="${ADMIN_PASS:-Admin@12345!}"
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@example.com}"

for i in $(seq 1 40); do
    if wp --allow-root core is-installed >/dev/null 2>&1; then
        break
    fi
    wp --allow-root core install \
        --url="$WP_URL" --title="Stilla" \
        --admin_user="$ADMIN_USER" --admin_password="$ADMIN_PASS" \
        --admin_email="$ADMIN_EMAIL" --skip-email >/dev/null 2>&1 || true
    sleep 3
done

wp --allow-root theme activate stilla >/dev/null 2>&1 || true
wp --allow-root plugin activate spectra ultimate-addons-for-gutenberg >/dev/null 2>&1 || true
wp --allow-root rewrite flush --hard >/dev/null 2>&1 || true

echo "Stilla init complete"
