#!/bin/bash

# Installation
PHALCON="v2.0.9"

echo "Provisioning Development Environment"
apt-get -y update > /dev/null 2>&1

echo "Installing PHP packages and dependencies"
apt-get -y install php5 php5-mcrypt php5-fpm php5-mysql php5-json php5-dev php5-curl php5-common php5-cli > /dev/null 2>&1

echo "Installing nginx webserver"
apt-get -y install nginx-extras > /dev/null 2>&1

# Password is intentionally insecure for development purposes, this would never happen in production :)
echo "Installing MySQL"
debconf-set-selections <<< 'mysql-server-5.6 mysql-server/root_password password rootpass'
debconf-set-selections <<< 'mysql-server-5.6 mysql-server/root_password_again password rootpass'
apt-get -y install mysql-server-5.6 > /dev/null 2>&1

echo "Installing git"
apt-get -y install git > /dev/null 2>&1

echo "Installing Phalcon dependencies"
apt-get -y install build-essential libpcre3-dev > /dev/null 2>&1

echo "Installing Phalcon"
echo "Cloning Phalcon Repository"
cd /usr/local/src
git clone https://github.com/phalcon/cphalcon.git > /dev/null 2>&1
cd cphalcon
echo "Checking out tag $PHALCON"
git checkout phalcon-$PHALCON > /dev/null 2>&1
echo "Building and installing Phalcon"
cd build
./install > /dev/null 2>&1
echo "extension=phalcon.so" > /etc/php5/mods-available/phalcon.ini
ln -s /etc/php5/mods-available/phalcon.ini /etc/php5/cli/conf.d/20-phalcon.ini
ln -s /etc/php5/mods-available/phalcon.ini /etc/php5/fpm/conf.d/20-phalcon.ini

echo "Installing Composer"
curl -sS https://getcomposer.org/installer | php > /dev/null 2>&1
mv composer.phar /usr/local/bin/composer

# Configuration
echo "Configuring Nginx"
rm -rf /etc/nginx/sites-available/default
rm -rf /etc/nginx/sites-enabled/default
cp /vagrant/provision/config/nginx/service /etc/nginx/sites-available/service > /dev/null 2>&1
cp /vagrant/provision/config/nginx/app /etc/nginx/sites-available/app > /dev/null 2>&1
ln -s /etc/nginx/sites-available/service /etc/nginx/sites-enabled/
ln -s /etc/nginx/sites-available/app /etc/nginx/sites-enabled/
service nginx restart > /dev/null
service php5-fpm restart > /dev/null

echo "Setting up MySQL database"
mysqladmin -u root -prootpass create forex > /dev/null 2>&1
echo "GRANT UPDATE,DELETE,SELECT,INSERT ON forex.* TO forex@'localhost' IDENTIFIED BY 'f0r3x'" | mysql -u root -prootpass > /dev/null 2>&1
mysql -u root -prootpass < /vagrant/sql/forex.sql > /dev/null 2>&1

echo "Installing Composer Dependencies"
cd /vagrant
composer update > /dev/null 2>&1

echo "Updating hosts file"
echo "127.0.0.1  api.forex" >> /etc/hosts
echo "127.0.0.1  app.forex" >> /etc/hosts
