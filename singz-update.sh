#!/bin/bash


composer install --no-scripts \
     && rm -rf var/cache/prod/ var/cache/dev/ \
     && bin/console doctrine:schema:update --dump-sql \
     && bin/console doctrine:schema:update --force \
     && bin/console asset:install \
     && bin/console singz:setting:create \
     && rm -rf var/cache/prod/ var/cache/dev/  

rm nohup.out > /dev/null 2>&1
nohup bin/console gos:websocket:server &
sleep 2
cat nohup.out