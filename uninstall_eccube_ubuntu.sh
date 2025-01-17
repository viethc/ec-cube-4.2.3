#!/bin/bash

set -e

echo "Bắt đầu quá trình gỡ bỏ cài đặt EC-CUBE và các thành phần liên quan..."

# Gỡ bỏ Apache
echo "Dừng và gỡ bỏ Apache..."
sudo systemctl stop apache2
sudo systemctl disable apache2
sudo apt purge -y apache2 apache2-utils apache2-bin apache2.2-common
sudo apt autoremove -y
sudo rm -rf /etc/apache2 /var/www/html/eccube /var/log/apache2

# Gỡ bỏ PHP và các extension
echo "Gỡ bỏ PHP và các extension..."
sudo apt purge -y php8.1 php8.1-cli php8.1-common php8.1-mbstring php8.1-xml php8.1-curl php8.1-pgsql php8.1-intl php8.1-zip php8.1-bcmath libapache2-mod-php8.1
sudo apt autoremove -y
sudo rm -rf /etc/php /var/log/php

# Gỡ bỏ PostgreSQL
echo "Dừng và gỡ bỏ PostgreSQL..."
sudo systemctl stop postgresql
sudo systemctl disable postgresql
sudo apt purge -y postgresql postgresql-contrib
sudo apt autoremove -y
sudo rm -rf /etc/postgresql /var/lib/postgresql /var/log/postgresql /var/log/postgresql

# Xóa cơ sở dữ liệu EC-CUBE
echo "Xóa cơ sở dữ liệu EC-CUBE..."
sudo -u postgres psql <<EOF
DROP DATABASE IF EXISTS eccube;
DROP USER IF EXISTS eccube_user;
EOF

# Gỡ bỏ Mailcatcher
echo "Gỡ bỏ Mailcatcher..."
sudo systemctl stop mailcatcher || true
sudo systemctl disable mailcatcher || true
sudo rm -f /etc/systemd/system/mailcatcher.service
sudo gem uninstall mailcatcher -a -x || true

# Gỡ bỏ Composer
echo "Gỡ bỏ Composer..."
sudo rm -f /usr/local/bin/composer

# Gỡ bỏ các gói cơ bản
echo "Gỡ bỏ các gói cơ bản..."
sudo apt purge -y ruby-dev build-essential software-properties-common curl unzip git
sudo apt autoremove -y

# Làm sạch hệ thống
echo "Dọn dẹp hệ thống..."
sudo apt autoremove -y
sudo apt autoclean -y
sudo rm -rf /var/cache/* /tmp/*

# Thông báo hoàn tất
echo "Đã hoàn tất gỡ bỏ cài đặt EC-CUBE và các thành phần liên quan!"
