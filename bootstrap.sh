#!/bin/bash

readonly DEV_PROJECT_NAME=flight_checkin

# Install LAMP stack.
debconf-set-selections <<< 'mysql-server mysql-server/root_password password password'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password password'
apt-get update
apt-get install -y apache2 mysql-server php5 php-pear php5-suhosin php5-mysql php5-mcrypt php5-json vim
a2enmod rewrite
echo "suhosin.executor.include.whitelist=phar" >> /etc/php5/cli/php.ini
rm -rf /var/www/
mkdir /var/www/
service apache2 restart

# Setup Laravel.
cd ~
apt-get install -y curl libcurl3 libcurl3-dev php5-curl
wget http://laravel.com/laravel.phar
chmod +x laravel.phar
mv laravel.phar /usr/local/bin/laravel
cd /var/www/
laravel new ${DEV_PROJECT_NAME}
chown -R vagrant:www-data ${DEV_PROJECT_NAME}/
chmod -R ug+w ${DEV_PROJECT_NAME}/app/storage/

# Configure Apache.
a2dissite default
echo "<VirtualHost *:80>
        ServerAdmin webmaster@localhost
        ServerName ${DEV_PROJECT_NAME}.dev
        DocumentRoot /var/www/${DEV_PROJECT_NAME}/public/
        ErrorLog ${APACHE_LOG_DIR}/${DEV_PROJECT_NAME}-error.log
        LogLevel warn
        CustomLog ${APACHE_LOG_DIR}/${DEV_PROJECT_NAME}-access.log combined
</VirtualHost>" > /etc/apache2/sites-available/${DEV_PROJECT_NAME}
a2ensite ${DEV_PROJECT_NAME}
service apache2 reload

# Install PECL HTTP.
sudo apt-get install -y build-essential libcurl3 php5-dev libcurl4-gnutls-dev libmagic-dev libpcre3-dev
sudo pecl install http://pecl.php.net/get/pecl_http-1.7.6.tgz
echo "extension=http.so" >> /etc/php5/cli/php.ini
echo "extension=http.so" >> /etc/php5/apache2/php.ini
service apache2 restart
