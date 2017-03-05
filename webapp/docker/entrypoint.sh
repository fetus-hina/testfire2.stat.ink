#!/bin/sh

cd $(dirname $0)/..
chown -R webapp:webapp /var/www/site/runtime /var/www/site/web/assets
su -c 'scl enable php71 -- ./yii migrate/up --interactive=0' webapp
/opt/remi/php71/root/usr/sbin/php-fpm --nodaemonize
