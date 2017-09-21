#!/usr/bin/env bash

cat /var/www/builder/docker-env.dev.yml | grep "=" | sed 's/ //g' | cut -c 2- | sed 's/^/export /' > /environment_vars
source /environment_vars

if [ $BUILD == "true" ]; then
    /builder/${ENVIRONMENT}/build.${ENVIRONMENT}.sh
fi

if [ $FIXTURES == "true" ]; then
    /builder/${ENVIRONMENT}/fixtures.${ENVIRONMENT}.sh
fi
