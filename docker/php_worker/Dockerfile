FROM php:7.2

MAINTAINER ryanover

RUN mkdir /home/ryanover/ && \
    useradd ryanover && \
    usermod -s /bin/bash ryanover && \
    usermod -d /home/ryanover/ ryanover && \
    chown -R ryanover:ryanover /home/ryanover && \
    apt-get update && apt-get -y install sudo && \
    adduser ryanover sudo && apt-get -y install wget supervisor librabbitmq-dev

RUN mkdir -p /var/log/consumer && \
    chown -R ryanover:ryanover /var/log/consumer && \
    chmod -R a+w /var/log/consumer

RUN docker-php-ext-install opcache && \
    pecl install amqp && docker-php-ext-enable amqp && \
    docker-php-ext-install bcmath

COPY ./docker/php_worker/php.ini /usr/local/etc/php/php.ini
COPY ./docker/php_worker/supervisord.conf /etc/supervisord.conf
COPY . /home/ryanover/source/

RUN rm -rf /home/ryanover/source/docker && \
    rm -rf /home/ryanover/source/.env && \
    rm -rf /home/ryanover/source/.git

RUN chown -R ryanover:ryanover /home/ryanover/

WORKDIR /etc/supervisor/conf.d/

CMD ["/usr/bin/supervisord"]
