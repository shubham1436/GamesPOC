#!/usr/bin/env bash

databases=(players-mysql)

for i in ${databases[@]}; do
    while true; do
      mysqladmin ping --host=${i} --silent

      if [ $? -eq 0 ]; then
        break
      fi

      echo "Could not (yet) connect to the '${i}' mysql database"

      sleep 1
    done
done

php bin/console doctrine:database:create --env=dev --if-not-exists -n
php bin/console doctrine:database:create --env=test --if-not-exists -n
php bin/console doctrine:migrations:migrate --env=dev -n
php bin/console doctrine:migrations:migrate --env=test -n
php bin/console hautelook:fixtures:load -n --env=dev
php bin/console cache:warmup --env=dev
