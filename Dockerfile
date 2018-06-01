FROM php:7.1-cli

COPY ./php.ini /usr/local/etc/php

WORKDIR /project

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

RUN apt-get update && apt-get install -y git