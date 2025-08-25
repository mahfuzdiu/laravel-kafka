FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
        librdkafka-dev \
        libzip-dev \
        zip \
        unzip \
    && docker-php-ext-install zip \
    && pecl install rdkafka \
    && docker-php-ext-enable rdkafka \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql

# ✅ Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy files
COPY . .

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html
RUN chmod -R 644 /var/www/html/storage

EXPOSE 8000

#CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
