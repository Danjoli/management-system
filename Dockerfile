FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock* ./
RUN composer install --no-interaction --prefer-dist --no-scripts

FROM php:8.4-cli-alpine
RUN docker-php-ext-install pdo_mysql
WORKDIR /app
COPY --from=vendor /app/vendor ./vendor
COPY . .
EXPOSE 8080
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]

