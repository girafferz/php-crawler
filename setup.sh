
sudo mkdir /var/www/html/
sudo chmod 0777 /var/www/html/
mkdir ./fetch_test/
cd ./fetch_test/
curl -sS https://getcomposer.org/installer | php
php composer.phar require fabpot/goutte:~2.0
cd ../
