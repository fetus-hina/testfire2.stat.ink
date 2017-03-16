FROM centos:7
ARG GITHUB_TOKEN
ARG GIT_REVISION="Unknown"
ADD . /var/www/site/
RUN yum update -y && \
    yum install -y \
        epel-release \
        scl-utils \
        http://rpms.famillecollet.com/enterprise/remi-release-7.rpm \
      && \
    curl -sSL https://rpm.nodesource.com/setup_7.x | bash && \
    curl -sSL https://rpm.fetus.jp/jp3cki.repo -o /etc/yum.repos.d/jp3cki.repo && \
    yum-config-manager --enable jp3cki && \
    yum install -y \
        ImageMagick \
        brotli \
        gcc-c++ \
        gzip \
        jpegoptim \
        make \
        nodejs \
        patch \
        php71-php-cli \
        php71-php-fpm \
        php71-php-gd \
        php71-php-intl \
        php71-php-json \
        php71-php-mbstring \
        php71-php-mcrypt \
        php71-php-opcache \
        php71-php-pdo \
        php71-php-pecl-msgpack \
        php71-php-pecl-zip \
        php71-php-pgsql \
        php71-php-xml \
        php71-runtime \
        pngcrush \
        unzip \
      && \
    rm -rf /etc/yum.repos.d/jp3cki.repo && \
    mkdir -p /var/www/site && \
    scl enable php71 "curl -SL 'https://getcomposer.org/installer' | php -- --stable --install-dir=/var/www/site --filename=composer.phar" && \
    cd /var/www/site && \
    echo $GIT_REVISION > config/git-revision.txt && \
    scl enable php71 "./composer.phar config -g github-oauth.github.com $GITHUB_TOKEN" && \
    scl enable php71 "./composer.phar global require 'fxp/composer-asset-plugin:^1.2.0'" && \
    touch composer.lock && \
    scl enable php71 "make init" && \
    useradd  -d /var/www/site -M webapp && \
    chown -R webapp:webapp /var/www/site && \
    yum remove -y \
        brotli \
        epel-release \
        gcc \
        make \
        nodejs \
        nodesource-release \
        patch \
        remi-release \
        unzip \
      && \
    package-cleanup --quiet --leaves | xargs yum remove -y && \
    package-cleanup --quiet --leaves | xargs yum remove -y && \
    rm -rf ~/.composer && \
    yum clean all && \
    cp docker/php-fpm/fpm.conf /etc/opt/remi/php71/php-fpm.d/www.conf
CMD [ "/var/www/site/docker/entrypoint.sh" ]
EXPOSE 9000
