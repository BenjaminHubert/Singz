#!/bin/bash


composer install --no-scripts \
     && bin/console cache:clear \
     && bin/console doctrine:schema:update --dump-sql \
     && bin/console doctrine:schema:update --force \
     && bin/console asset:install \
     && bin/console singz:setting:create\
     && bin/console cache:clear \