FROM php:7.2-fpm

RUN apt-get update && apt-get install -y apt-transport-https gnupg \
    && curl -sL https://deb.nodesource.com/setup_6.x | bash - \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
    && echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list \
    && apt-get update && apt-get install -y libmcrypt-dev libpng-dev libfreetype6-dev libjpeg62-turbo-dev \
        libxml2-dev libxslt-dev zlib1g-dev libicu-dev libexif-dev g++ git nodejs yarn \
    && apt-get update && apt-get install -y supervisor \
    && apt-get update && apt-get install cron -y \
    && pecl install mcrypt-1.0.1 \
    && pecl install xdebug-2.6.0alpha1 \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd mbstring pcntl pdo_mysql soap intl xsl zip exif opcache \
    && docker-php-ext-enable opcache xdebug \
    && docker-php-ext-install bcmath \
    && rm -rf /var/lib/apt/lists/*

ADD ./crontab /etc/cron.d/cron_test
RUN chmod 0644 /etc/cron.d/cron_test
RUN crontab /etc/cron.d/cron_test
RUN touch /var/log/cron.log

RUN curl -s https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

COPY config.ini /usr/local/etc/php/conf.d/
COPY xdebug.ini /usr/local/etc/php/conf.d/

COPY ./supervisord/supervisord.conf /etc/supervisor
COPY ./supervisord/supervisor/* /etc/supervisor/conf.d

RUN mkdir /home/www-data \
    && chown www-data:www-data /home/www-data \
    && usermod -d /home/www-data www-data \
    && usermod -s /bin/bash www-data

# admin tools ================================
RUN apt-get update && apt-get install -y \
    net-tools \
    telnet \
    nmap \
    vim \
    nano \
    mc
# ============================================

USER root
WORKDIR /var/www
VOLUME /var/www


