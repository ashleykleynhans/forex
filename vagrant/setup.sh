#!/bin/bash

PHALCON="v2.0.9"

echo "Provisioning Development Environment for the Mukuru Assessment"

echo "Installing Phalcon"
echo "Cloning Phalcon Repository"
cd /usr/local/src
git clone https://github.com/phalcon/cphalcon.git
cd cphalcon
echo "Checking out tag $PHALCON"
git checkout phalcon-$PHALCON
echo "Building and installing Phalcon"
cd build
./install
echo "extension=phalcon.so" > /etc/php5/mods-available/phalcon.ini
ln -s /etc/php5/mods-available/phalcon.ini /etc/php5/cli/conf.d/20-phalcon.ini
ln -s /etc/php5/mods-available/phalcon.ini /etc/php5/fpm/conf.d/20-phalcon.ini

echo "Installing Composer"
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

echo "Installing Codeception"
wget -O /usr/local/bin/codecept http://codeception.com/codecept.phar
chmod a+rx /usr/local/bin/codecept

echo "Configuring Nginx"
cp /vagrant/vagrant/config/nginx_vhost /etc/nginx/sites-available/nginx_vhost > /dev/null
ln -s /etc/nginx/sites-available/nginx_vhost /etc/nginx/sites-enabled/
rm -rf /etc/nginx/sites-available/default
service nginx restart > /dev/null
service php5-fpm restart > /dev/null

# TODO: Insert Database configuration here

echo "Installing Composer Dependencies"
cd /vagrant
composer update

echo "Updating hosts file"
echo "127.0.0.1  api.forex" >> /etc/hosts
echo "127.0.0.1  web.forex" >> /etc/hosts
