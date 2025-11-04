# Single-stage image for Render.com
FROM php:8.4-cli

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    APP_ENV=production \
    PORT=10000 \
    PATH=/root/.composer/vendor/bin:$PATH

WORKDIR /var/www

# System deps + PHP extensions required for Laravel + Postgres/Redis
RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl unzip libpq-dev libzip-dev libicu-dev libxml2-dev libonig-dev libcurl4-openssl-dev zlib1g-dev \
    && docker-php-ext-install -j"$(nproc)" pdo pdo_pgsql pdo_mysql zip intl bcmath xml pcntl \
    && pecl install redis || true \
    && docker-php-ext-enable redis || true \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Use Composer official image binary (faster and keeps single layer small)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy app code
COPY . /var/www

# Install PHP deps
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Ensure writable dirs
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache || true

# Add entrypoint
COPY docker/render/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 10000

CMD ["/usr/local/bin/entrypoint.sh"]