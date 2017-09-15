FROM php:7-fpm

RUN \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"

RUN \
    apt-get update && \
    apt-get install -y git mysql-client && \
    rm -r /var/lib/apt/lists/*

RUN \
    mkdir /root/.ssh && \
    echo $SSH_KEY > /root/.ssh/id_rsa && \
    echo "Host stash.wimionline.local\n\tStrictHostKeyChecking no\n" >> /root/.ssh/config && \
    echo "Host stash.wimionline.local:7999\n\tStrictHostKeyChecking no\n" >> /root/.ssh/config

RUN apt-get update && apt-get install -y git libpng12-dev
RUN docker-php-ext-install zip && docker-php-ext-enable zip

RUN docker-php-ext-install pdo_mysql

#RUN apt-get install php-mysql

ADD . /var/www

WORKDIR /var/www

RUN chmod +x /var/www/bin/console

RUN chmod +x -R /var/www/docker/builder

RUN composer install

#RUN php bin/console doctrine:database:drop --force

#RUN php bin/console doctrine:database:create --if-not-exists

#RUN php bin/console cache:clear

#RUN php bin/console doctrine:schema:update --force

ENTRYPOINT ["/var/www/docker/builder/startup.sh"] 
#&& /bin/bash

CMD ["/var/www/bin/console", "server:run", "0.0.0.0:8000"]
