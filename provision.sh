#!/usr/bin/env bash

export DEBIAN_FRONTEND=noninteractive

# Variables
APPENV=dev

echo -e "\n--- Updating packages list ---\n"
apt-get -qq update

echo -e "\n--- Install base packages ---\n"
apt-get -y install vim curl build-essential python-software-properties git mysql-client zsh php apache2 libapache2-mod-php php-mysql php-curl php-dom > /dev/null 2>&1

echo -e "\n--- Installing Zend Server"
wget http://downloads.zend.com/zendserver/9.0.1/ZendServer-9.0.1-RepositoryInstaller-linux.tar.gz -q -O - | tar -xzf - -C /tmp && /tmp/ZendServer-RepositoryInstaller-linux/install_zs.sh 7.0 --automatic

echo -e "\n--- Allowing Apache override to all ---\n"
sed -i "s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf

echo -e "\n--- Setting document root to public directory ---\n"
rm -rf /var/wwwa2ps
ln -fs /vagrant /var/www

echo -e "\n--- We definitly need to see the PHP errors, turning them on ---\n"
sed -i "s/error_reporting=.*/error_reporting=E_ALL \& ~E_NOTICE \& ~E_DEPRECATED/" /etc/php/7.0/apache2/php.ini
sed -i "s/display_errors=.*/display_errors=On/" /etc/php/7.0/apache2/php.ini

echo -e "\n--- Custom PHP ---\n"
sed -i "31iAddType application/x-httpd-php .inc" /etc/apache2/mods-available/mime.conf

echo -e "\n--- Turn off disabled pcntl functions ---\n"
sed -i "s/disable_functions = .*//" /etc/php/7.0/apache2/php.ini

echo -e "\n--- Add environment variables to Apache ---\n"
cat > /etc/apache2/sites-enabled/000-default.conf <<EOF
SetEnv APP_ENV dev

<VirtualHost *:80>
    ServerName  objective.dev
    DocumentRoot /var/www/public
    ErrorLog \${APACHE_LOG_DIR}/dev-error.log
    CustomLog \${APACHE_LOG_DIR}/dev-access.log combined
    SetEnv REL_PATH "/vagrant/"
    SetEnv APP_ENV developpement
    Options -Indexes
    <Directory "/vagrant/public">
        AllowOverride All
    </Directory>
</VirtualHost>
EOF

echo -e "\n--- Adding custom hosts ---\n"
cat >> /etc/hosts <<EOF
151.80.111.53 mymaster
149.202.68.113 myslave
EOF


echo -e "\n--- Restarting apache2 ---\n"
service apache2 restart > /dev/null 2>&1

echo -e "\n--- Installing Composer for PHP package management ---\n"
curl --silent https://getcomposer.org/installer | php > /dev/null 2>&1
mv composer.phar /usr/local/bin/composer

echo -e "\n--- Creating a symlink for future phpunit use ---\n"
ln -fs /vagrant/vendor/bin/phpunit /usr/local/bin/phpunit

echo -e "\n--- Do a composer install ---\n"
cd /vagrant && php composer.phar install

ln -s /usr/bin/php /usr/local/bin/php

echo -e "\n--- Create /NASTEMPO  \n"
mkdir /NASTEMPO
chmod 777 /NASTEMPO
