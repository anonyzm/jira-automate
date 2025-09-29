# Используем официальный образ PHP 8.4 с FPM
FROM php:8.4-fpm

# Устанавливаем системные зависимости
RUN apt-get update && apt-get install -y \
    bc \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libxml2-utils \
    zip \
    unzip \
    nginx \
    supervisor \
    autoconf \
    build-essential \
    librdkafka-dev \
    liblz4-dev \
    libzstd-dev \
    libssl-dev \
    pkg-config \
    && docker-php-ext-install mbstring exif pcntl bcmath gd

# Устанавливаем PHP расширения для Kafka
RUN pecl channel-update pecl.php.net \
    && pecl install rdkafka \
    && docker-php-ext-enable rdkafka

# Устанавливаем остальные расширения
RUN docker-php-ext-install sockets \
    && pecl install xdebug pcov \
    && docker-php-ext-enable xdebug pcov sockets

# Настраиваем Xdebug для покрытия кода
RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.discover_client_host=0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Настройки PHP для подавления предупреждений
RUN echo "error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT" >> /usr/local/etc/php/conf.d/docker-php-custom.ini

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Устанавливаем рабочую директорию
WORKDIR /opt/app

# Копируем конфигурацию Nginx
COPY docker/app/nginx-main.conf /etc/nginx/nginx.conf
COPY docker/app/nginx.conf /etc/nginx/sites-available/default

# Копируем конфигурацию Supervisor
COPY docker/app/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Копируем конфигурацию PHP-FPM
COPY docker/app/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Создаем пользователя для приложения
RUN groupadd --gid 1001 application 
RUN useradd --uid 1001 --gid application --shell /bin/bash --create-home application
RUN usermod -aG www-data application
RUN usermod -aG application www-data

# Создаем необходимые директории для supervisor
RUN mkdir -p /var/log/supervisor

# Создаем необходимые директории для nginx и php-fpm и настраиваем права
RUN mkdir -p /var/run/nginx && chown -R application:application /var/run/nginx && chmod 755 /var/run/nginx
RUN mkdir -p /var/run/php-fpm && chown -R application:application /var/run/php-fpm && chmod 755 /var/run/php-fpm
RUN mkdir -p /var/log/php-fpm && chown -R application:application /var/log/php-fpm && chmod 755 /var/log/php-fpm
RUN chown -R application:application /var/log/nginx \
    && chown -R application:application /var/lib/nginx

# Копируем composer.json и устанавливаем зависимости
COPY app/composer.json ./
RUN composer install --no-dev --optimize-autoloader

# Копируем исходный код приложения
COPY app/ ./

# Устанавливаем права на папки
RUN chown -R application:application /opt/app \
    && chmod -R 755 /opt/app \
   # && chmod -R 775 /opt/app/cache \
   # && chmod -R 775 /opt/app/logs \
    && chown -R application:application /var/log/supervisor \
    && chown -R application:application /var/run

# Открываем порт 80
EXPOSE 80

# Запускаем Supervisor для управления процессами
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]