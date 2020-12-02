FROM php:7.2-fpm

RUN apt-get update

RUN apt-get install -y git wget unzip

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# install symfony binary
RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

RUN useradd --create-home --uid 1000 --shell /bin/bash web
RUN chown web. -R /var/www/html
USER web
WORKDIR /var/www/html
