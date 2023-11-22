
ARG COMPOSER_VERSION=latest\
    PHP_VERSION=8.1 \
    NODE_VERSION=latest

# Uncomment line bellow for Nodejs support.
# FROM node:${NODE_VERSION} as node
FROM composer:${COMPOSER_VERSION} as composer
FROM php:${PHP_VERSION}-fpm

ENV PHP_CONF_DIR="$PHP_INI_DIR/conf.d" \
    PHP_SETTINGS="$PHP_INI_DIR/php.ini"\
    FPM_CONF_DIR="/usr/local/etc/php-fpm.d"\
    DOCKER_UID=1000 \
    DOCKER_GID=1000 \
    XDEBUG_HOST="" \
    XDEBUG_IDE_KEY="" \
    XDEBUG_PORT=9003 \
    XDEBUG_DBGP=FALSE \
    XDEBUG_ENABLE=FALSE \
    COMPOSER_MEMORY_LIMIT=-1

RUN usermod --shell /bin/bash www-data &&\
    curl -sSLf \
        -o /usr/local/bin/install-php-extensions \
        https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions &&\
    chmod +x /usr/local/bin/install-php-extensions

# INSTALL PHP MODULES HERE
RUN install-php-extensions\
     bcmath\
     ctype\
     curl\
     dom\ 
     fileinfo\ 
     filter\ 
     gd\ 
     hash\ 
     iconv\ 
     intl\ 
     json\ 
     libxml\ 
     openssl\ 
     mbstring\ 
     pcre\
     pdo pdo_mysql\
     simplexml\
     soap \
     sockets\
     sodium\
     spl\
     tokenizer\
     xmlwriter\
     xsl &&\
    apt-get autoremove && apt-get autoclean &&\
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* cache/* /var/lib/log/*


# Install composer & git
COPY --from=composer /usr/bin/composer /opt/composer
RUN install-php-extensions zip &&\
    chown root:root /opt/composer &&\
    echo "#!/usr/bin/env sh\nphp -dxdebug.mode=off /opt/composer" > /bin/composer &&\
    chmod +x /bin/composer &&\
    DEBIAN_FRONTEND=noninteractive apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y git &&\
    mkdir -p /var/www/.composer &&\
    chown www-data:www-data /var/www/.composer &&\
    chmod 666 /var/www/.composer &&\
    apt-get autoremove && apt-get autoclean &&\
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* cache/* /var/lib/log/*

VOLUME /var/www/.composer

# Install Xdebug
ENV XDEBUG_CONF_FILE=${PHP_CONF_DIR}/docker-php-ext-xdebug.ini

ARG XDEBUG_VERSION="latest"

COPY ./utils/test_xdebug.php /bin/test_xdebug
COPY ./utils/get_xdebug_ip.php /bin/get_xdebug_ip
COPY ./utils/setup_xdebug.php /tmp/setup_xdebug

RUN echo ${XDEBUG_VERSION} &&\
    php /tmp/setup_xdebug &&\
    rm -rf /tmp/setup_xdebug &&\
    rm -rf /tmp/xdebug &&\
    chown root:root /bin/test_xdebug &&\
    chmod +x /bin/test_xdebug &&\
    rm -rf /bin/setup_xdebug &&\
    apt-get autoremove && apt-get autoclean &&\
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* cache/* /var/lib/log/*

VOLUME /var/log/xdebug

# ***************************************************#
# Uncomment for nodejs (for example frontend builds) #
# ***************************************************#

# COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
# COPY --from=node /usr/local/bin/node /usr/local/bin/node

# RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm &&\
#     ln -s /usr/local/lib/node_modules/webpack-cli/bin/cli.js /usr/local/bin/webpack &&\
#     chown -R root:root /usr/local/lib/node_modules &&\
#     mkdir -p /var/www/.npm

#***********************************************#
# Do not edit bellow this line
#***********************************************#

COPY ./utils/entrypoint.sh  /usr/local/bin/entrypoint.sh

RUN chown root:root /usr/local/bin/entrypoint.sh &&\
    chmod +x /usr/local/bin/entrypoint.sh &&\
    usermod --shell /bin/bash www-data

# COPY ./ssl/ca/ca.crt /usr/local/share/ca-certificates/php-www-ca.crt
RUN cp /etc/ca-certificates.conf /etc/ca-certificates.conf.orig &&\
    mkdir -p /usr/local/share/cert_install

VOLUME /usr/local/share/cert_install

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]