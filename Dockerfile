FROM php:8.3-fpm-alpine

RUN apk update; \
    apk upgrade;

# RUN apk add --no-cache $PHPIZE_DEPS
RUN apk add --no-cache \
                $PHPIZE_DEPS \
		bash \
		git \
		unzip \
		nginx \
		supervisor

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY docker/fpm/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/nginx-vserver.conf /etc/nginx/conf.d/default.conf

# Set build arguments
ARG RUN_USER="webapp"
ARG RUN_GROUP="${RUN_USER}"
# UID is missed as it will be set by docker-compose
ARG RUN_UID=""
ARG RUN_GID="${RUN_UID}"
ARG USER_HOME="/${RUN_USER}"
ARG APPLICATION_ROOT="${USER_HOME}/app"

# Set the application root folder
WORKDIR ${APPLICATION_ROOT}


RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

RUN mkdir -p /etc/sudoers.d/

# Create an unprivileged user
RUN set -x && addgroup -g ${RUN_GID} ${RUN_GROUP} \
    && adduser -S -h ${USER_HOME} \
       -u ${RUN_UID} -G ${RUN_GROUP} -D ${RUN_USER} \
    && echo "${RUN_USER} ALL=(ALL) NOPASSWD: ALL" > /etc/sudoers.d/${RUN_USER} \
    && chmod 0440 /etc/sudoers.d/${RUN_USER} \
    && chown -R ${RUN_USER}:${RUN_GROUP} ${USER_HOME}

RUN mkdir -p /var/log/nginx
RUN chown ${RUN_USER} /var/log/nginx
RUN mkdir -p /var/lib/nginx
RUN chown ${RUN_USER} /var/lib/nginx

# Run the following under the created user
USER ${RUN_USER}

COPY docker/start.sh /start.sh

ENTRYPOINT [ "" ]
CMD ["sh", "/start.sh"]
