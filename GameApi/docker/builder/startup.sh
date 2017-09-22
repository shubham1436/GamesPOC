#!/usr/bin/env bash

chmod +x -R /var/www/docker/builder

php bin/console doctrine:database:create --if-not-exists

php bin/console cache:clear

php bin/console doctrine:schema:update --force

set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php "$@"
fi

exec "$@"

#php-fpm
