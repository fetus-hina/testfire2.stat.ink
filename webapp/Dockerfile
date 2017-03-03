FROM alpine:edge
ARG GITHUB_TOKEN
ADD . /var/www/site/
RUN echo 'http://dl-cdn.alpinelinux.org/alpine/edge/testing' >> /etc/apk/repositories && \
    apk update && \
    apk upgrade && \
    apk add \
        php7.1 \
        php7.1-ctype \
        php7.1-curl \
        php7.1-dom  \
        php7.1-fpm \
        php7.1-gd \
        php7.1-iconv \
        php7.1-intl \
        php7.1-json \
        php7.1-mbstring \
        php7.1-mcrypt \
        php7.1-opcache \
        php7.1-openssl \
        php7.1-pdo \
        php7.1-pdo_pgsql \
        php7.1-pgsql \
        php7.1-phar \
        php7.1-session \
        php7.1-xml \
        php7.1-xmlwriter \
        php7.1-zlib \
      && \
    apk add --virtual .deps \
        curl \
        gcc \
        git \
        gzip \
        imagemagick \
        make \
        musl-dev \
        nodejs-current \
        pngcrush \
        unzip \
      && \
    git clone --depth=1 -b v0.5.2 https://github.com/google/brotli.git /root/brotli && \
    cd /root/brotli && \
    make bro && \
    cp bin/bro /usr/local/bin/ && \
    cd /root && \
    rm -rf /root/brotli && \
    mkdir -p /var/www/site && \
    curl -SL 'https://getcomposer.org/installer' | php7.1 -- --stable --install-dir=/var/www/site --filename=composer.phar && \
    cd /var/www/site && \
    ./composer.phar config -g github-oauth.github.com $GITHUB_TOKEN && \
    ./composer.phar global require "fxp/composer-asset-plugin:^1.2.0" && \
    touch composer.lock && \
    make init && \
    adduser -h /var/www/site -D -H webapp && \
    chown -R webapp:webapp /var/www/site && \
    rm -rf ~/.composer /usr/local/bin/bro && \
    apk del --purge .deps && \
    rm -rf /var/cache/apk/* && \
    cp docker/php-fpm/fpm.conf /etc/php7.1/php-fpm.d/www.conf
CMD [ "/var/www/site/docker/entrypoint.sh" ]
