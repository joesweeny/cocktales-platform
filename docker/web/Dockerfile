FROM php:7.1-fpm

RUN apt-get update
RUN apt-get install -y \
        supervisor \
        nginx \
        libcurl4-gnutls-dev \
        libsqlite3-0 \
        curl

RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo \
    curl \
    json

RUN apt-get update && apt-get install -y libmagickwand-6.q16-dev --no-install-recommends \
    && ln -s /usr/lib/x86_64-linux-gnu/ImageMagick-6.8.9/bin-Q16/MagickWand-config /usr/bin \
    && pecl install imagick \
    && echo "extension=imagick.so" > /usr/local/etc/php/conf.d/ext-imagick.ini

ADD ./docker/web/php.ini /usr/local/etc/php/php.ini
ADD ./docker/web/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
ADD ./docker/web/nginx.conf /etc/nginx/nginx.conf
ADD ./src /opt/src/
ADD ./bin /opt/bin/

EXPOSE 80
WORKDIR /opt
CMD ["/usr/bin/supervisord"]
