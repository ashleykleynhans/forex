#!/bin/bash

# Installation
PHALCON="v2.0.9"

echo "Provisioning Development Environment"
apt-get -y update > /dev/null

echo "Installing PHP packages and dependencies"
apt-get -y install php5 php5-mcrypt php5-fpm php5-mysql php5-json php5-dev php5-curl php5-common php5-cli > /dev/null

echo "Installing nginx webserver"
apt-get -y install nginx-extras > /dev/null

# Password is intentionally insecure for development purposes, this would never happen in production :)
echo "Installing MySQL"
sudo debconf-set-selections <<< 'mysql-server-5.6 mysql-server/root_password password rootpass'
sudo debconf-set-selections <<< 'mysql-server-5.6 mysql-server/root_password_again password rootpass'
apt-get -y install mysql-server-5.6 > /dev/null

echo "Installing git"
apt-get -y install git > /dev/null

echo "Installing Phalcon dependencies"
apt-get -y install build-essential libpcre3-dev > /dev/null

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

# Configuration
echo "Configuring Nginx"
cp /vagrant/vagrant/config/nginx_vhost /etc/nginx/sites-available/nginx_vhost > /dev/null
ln -s /etc/nginx/sites-available/nginx_vhost /etc/nginx/sites-enabled/
rm -rf /etc/nginx/sites-available/default
service nginx restart > /dev/null
service php5-fpm restart > /dev/null

echo "Setting up MySQL database"
mysqladmin -u root -prootpass create forex
echo "GRANT UPDATE,DELETE,SELECT,INSERT ON forex.* TO forex@'localhost' IDENTIFIED BY 'f0r3x'" > mysql

echo "Installing Composer Dependencies"
cd /vagrant
composer update

echo "Updating hosts file"
echo "127.0.0.1  api.forex" >> /etc/hosts
echo "127.0.0.1  web.forex" >> /etc/hosts
