# Stilla furniture storefront — Railway-ready image.
# Based on the official WordPress PHP-FPM image; bakes the custom theme + mu-plugin
# into the image, runs nginx + php-fpm via supervisord. DB comes from Railway's MySQL
# plugin (env: WORDPRESS_DB_HOST/USER/PASSWORD/NAME).

FROM wordpress:php8.3-fpm-alpine

RUN apk add --no-cache nginx supervisor bash gettext && \
    mkdir -p /run/nginx /var/log/supervisor

# Bake theme + mu-plugin into the image (no bind-mounts on Railway).
COPY themes/stilla /var/www/html/wp-content/themes/stilla
COPY wp-content/mu-plugins /var/www/html/wp-content/mu-plugins
RUN chown -R www-data:www-data \
        /var/www/html/wp-content/themes/stilla \
        /var/www/html/wp-content/mu-plugins

# nginx + supervisord + first-run WP install
COPY nginx/railway.conf /etc/nginx/http.d/default.conf.template
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/init.sh /usr/local/bin/init.sh
RUN chmod +x /usr/local/bin/init.sh

# wp-cli for first-run install/activate
RUN curl -sO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x wp-cli.phar && mv wp-cli.phar /usr/local/bin/wp

EXPOSE 80

# 1) Run the official WordPress entrypoint: it downloads core into /var/www/html,
#    waits for the DB, and generates wp-config.php from the WORDPRESS_DB_* env vars.
# 2) Then our init.sh installs WP + activates the theme, and supervisord launches
#    php-fpm + nginx.
CMD ["/bin/sh", "-c", "PORT=${PORT:-80}; sed \"s/__PORT__/$PORT/\" /etc/nginx/http.d/default.conf.template > /etc/nginx/http.d/default.conf && /usr/local/bin/docker-entrypoint.sh apache2ctl & /usr/local/bin/init.sh && exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf"]
