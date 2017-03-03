#!/bin/sh

cd $(dirname $0)/..
su -c './yii migrate/up --nointeractive' webapp
chown webapp:webapp /var/www/site/runtime/logs /var/www/site/web/assets
php-fpm7.1 --nodaemonize
