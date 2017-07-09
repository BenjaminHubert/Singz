#!/bin/bash


composer install --no-scripts \
     && rm -rf var/cache/prod/ var/cache/dev/ \
     && bin/console doctrine:schema:update --dump-sql \
     && bin/console doctrine:schema:update --force \
     && bin/console asset:install \
     && bin/console singz:setting:create \
     && rm -rf var/cache/prod/ var/cache/dev/ 
     
bin/console gos:websocket:server