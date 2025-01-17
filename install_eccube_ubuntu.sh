#!/bin/bash

set -e

# Cập nhật hệ thống
echo "Cập nhật hệ thống..."
sudo apt update && sudo apt upgrade -y

# Cài đặt các gói cơ bản
echo "Cài đặt các gói cơ bản..."
sudo apt install -y software-properties-common curl unzip git

# Thêm kho lưu trữ PHP
echo "Thêm kho lưu trữ PHP..."
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update

# Cài đặt Apache
echo "Cài đặt Apache..."
sudo apt install -y apache2
sudo systemctl enable apache2
sudo systemctl start apache2

# Cài đặt PHP 8.1 và các extension cần thiết
echo "Cài đặt PHP 8.1 và các extension..."
sudo apt install -y php8.1 php8.1-cli php8.1-common php8.1-mbstring php8.1-xml php8.1-curl php8.1-pgsql php8.1-intl php8.1-zip php8.1-bcmath libapache2-mod-php8.1

# Cài đặt PostgreSQL
echo "Cài đặt PostgreSQL..."
sudo apt install -y postgresql postgresql-contrib
sudo systemctl enable postgresql
sudo systemctl start postgresql

# Tạo cơ sở dữ liệu và người dùng PostgreSQL
echo "Cấu hình PostgreSQL..."
sudo -u postgres psql <<EOF
CREATE DATABASE eccubedb;
CREATE USER eccube_usr WITH PASSWORD 'eccube_pwd';
GRANT ALL PRIVILEGES ON DATABASE eccubedb TO eccube_usr;
EOF

# Cài đặt Composer
echo "Cài đặt Composer..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Cài đặt Mailcatcher
echo "Cài đặt Mailcatcher..."
sudo apt install -y ruby-dev build-essential
sudo gem install mailcatcher
sudo tee /etc/systemd/system/mailcatcher.service > /dev/null <<EOF
[Unit]
Description=Mailcatcher Service
After=network.target

[Service]
ExecStart=/usr/local/bin/mailcatcher --ip=0.0.0.0
Restart=always
User=$USER

[Install]
WantedBy=multi-user.target
EOF

sudo systemctl enable mailcatcher
sudo systemctl start mailcatcher

# Clone mã nguồn EC-CUBE từ GitHub
echo "Clone mã nguồn EC-CUBE từ github..."
cd /var/www/html
sudo rm -rf eccube
sudo git clone --branch develop https://github.com/viethc/ec-cube-4.2.3.git eccube
cd eccube

# Cấp quyền cho thư mục EC-CUBE
sudo chmod -R 777 html var

# Cấu hình tệp .env
cp .env.install .env
echo "Cấu hình .env ..."
sudo tee .env > /dev/null <<EOF
APP_ENV=dev
APP_DEBUG=1
DATABASE_URL=pgsql://eccube_usr:eccube_pwd@127.0.0.1:5432/eccubedb
DATABASE_SERVER_VERSION=16
DATABASE_CHARSET='utf8'
MAILER_DSN=smtp://127.0.0.1:1025
ECCUBE_LOCALE="en"
EOF

# Cấp quyền cho Apache
echo "Cấp quyền cho Apache..."
sudo chown -R www-data:www-data /var/www/html/eccube
sudo a2enmod rewrite
sudo systemctl restart apache2

# Tạo VirtualHost cho EC-CUBE
echo "Tạo VirtualHost cho EC-CUBE..."
sudo tee /etc/apache2/sites-available/eccube.conf > /dev/null <<EOF
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/eccube/html

    <Directory /var/www/html/eccube/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/eccube_error.log
    CustomLog \${APACHE_LOG_DIR}/eccube_access.log combined
</VirtualHost>
EOF

sudo a2ensite eccube
sudo systemctl reload apache2

# Install
php bin/console eccube:install --env=dev -n

# Hoàn tất
echo "Hoàn tất cài đặt EC-CUBE! Truy cập http://localhost để bắt đầu."
