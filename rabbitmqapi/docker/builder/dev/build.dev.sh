#!/usr/bin/env bash

export SYMFONY_DEPRECATIONS_HELPER=weak

echo $DATABASE_HOST

composer install --no-interaction
