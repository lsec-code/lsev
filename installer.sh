#!/bin/bash

# LSEV Installer Script for Ubuntu
# Repository: https://github.com/lsec-code/lsev.git
# Author: LinuxSec / Antigravity

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}=========================================${NC}"
echo -e "${GREEN}   LSEV AUTOMATED INSTALLER (UBUNTU)     ${NC}"
echo -e "${GREEN}=========================================${NC}"

# Check logic for root
if [[ $EUID -ne 0 ]]; then
   echo -e "${RED}This script must be run as root.${NC}" 
   exit 1
fi

# 1. Configuration & Inputs
echo -e "${YELLOW}[?] Configuration Setup:${NC}"

# Domain/Ip Selection
read -p "Enter Domain or IP (default: localhost): " APP_URL
APP_URL=${APP_URL:-localhost}

# DB Password Selection
read -p "Enter Desired MariaDB Root Password (press Enter for empty/no password): " DB_ROOT_PASS

# SSL Selection (Only if not localhost)
USE_SSL="n"
if [ "$APP_URL" != "localhost" ] && [ "$APP_URL" != "127.0.0.1" ]; then
    read -p "Do you want to install Let's Encrypt SSL? (y/n): " USE_SSL
fi

echo -e "${GREEN}[+] Configuration set! Starting Installation...${NC}"
sleep 2

# 2. System Update & Dependencies
echo -e "${YELLOW}[+] Updating System & Installing Dependencies...${NC}"
apt update -y
apt install -y software-properties-common git unzip curl zip
add-apt-repository -y ppa:ondrej/php
apt update -y

# Install PHP 8.2 (Compatible with most Laravel v10/v11 apps) and Extensions
# Adjust version if needed (user didn't specify, assuming 8.2 or 8.3)
PHP_VER="8.2"
apt install -y nginx mariadb-server php${PHP_VER} php${PHP_VER}-fpm php${PHP_VER}-mysql \
php${PHP_VER}-mbstring php${PHP_VER}-xml php${PHP_VER}-bcmath php${PHP_VER}-curl \
php${PHP_VER}-gd php${PHP_VER}-zip php${PHP_VER}-intl php${PHP_VER}-tokenizer \
phpmyadmin

# 3. Database Setup
echo -e "${YELLOW}[+] Configuring MariaDB...${NC}"

# Secure installation (simplified)
if [ -z "$DB_ROOT_PASS" ]; then
    # No password configuration
    mysql -e "UPDATE mysql.global_priv SET priv=json_set(priv, '$.plugin', 'mysql_native_password', '$.authentication_string', PASSWORD('')) WHERE User='root'; FLUSH PRIVILEGES;"
else
    # Set password
    mysql -e "UPDATE mysql.global_priv SET priv=json_set(priv, '$.plugin', 'mysql_native_password', '$.authentication_string', PASSWORD('$DB_ROOT_PASS')) WHERE User='root'; FLUSH PRIVILEGES;"
fi

# Create Database
DB_NAME="video_app_v2"
echo -e "${YELLOW}[+] Creating Database '$DB_NAME'...${NC}"
mysql -u root ${DB_ROOT_PASS:+-p$DB_ROOT_PASS} -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"

# 4. Clone Project
target_dir="/var/www/lsev"
echo -e "${YELLOW}[+] Cloning Project to $target_dir...${NC}"

if [ -d "$target_dir" ]; then
    echo -e "${RED}Directory $target_dir already exists. Backing up...${NC}"
    mv $target_dir "${target_dir}_backup_$(date +%s)"
fi

git clone https://github.com/lsec-code/lsev.git $target_dir

# 5. Database Restore
echo -e "${YELLOW}[+] Checking for SQL Dump...${NC}"
if [ -f "$target_dir/video_app_v2.sql" ]; then
    echo -e "${GREEN}[+] Restoring Database from file...${NC}"
    mysql -u root ${DB_ROOT_PASS:+-p$DB_ROOT_PASS} $DB_NAME < "$target_dir/video_app_v2.sql"
else
    echo -e "${RED}[!] 'video_app_v2.sql' not found in project root. Skipping Restore.${NC}"
fi

# 6. Configure Application
echo -e "${YELLOW}[+] Setting up Laravel...${NC}"
cd $target_dir
cp .env.example .env

# Update .env
# 1. Change Connection to mysql
sed -i "s/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/g" .env

# 2. Uncomment and set Database Config
# Handle commented out defaults in .env.example
sed -i "s/# DB_HOST=127.0.0.1/DB_HOST=127.0.0.1/g" .env
sed -i "s/# DB_PORT=3306/DB_PORT=3306/g" .env
sed -i "s/# DB_DATABASE=laravel/DB_DATABASE=$DB_NAME/g" .env
sed -i "s/# DB_USERNAME=root/DB_USERNAME=root/g" .env
sed -i "s/# DB_PASSWORD=/DB_PASSWORD=$DB_ROOT_PASS/g" .env

# Handle if already uncommented (standard replace)
sed -i "s/APP_URL=http:\/\/localhost/APP_URL=http:\/\/$APP_URL:8000/g" .env
sed -i "s/DB_DATABASE=laravel/DB_DATABASE=$DB_NAME/g" .env
sed -i "s/DB_PASSWORD=/DB_PASSWORD=$DB_ROOT_PASS/g" .env
# Ensure DB_USERNAME is root
sed -i "s/DB_USERNAME=root/DB_USERNAME=root/g" .env

# Permissions
chown -R www-data:www-data $target_dir
chmod -R 775 $target_dir/storage $target_dir/bootstrap/cache

# Install Composer Dependencies
# Install composer globally if not exists
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
fi

# Run Composer (as user usually, but root is fine for setup if permissions fixed after)
export COMPOSER_ALLOW_SUPERUSER=1
composer install --no-interaction --optimize-autoloader

# Key Generate & Migrate (Partial migrate if restore happened?)
php artisan key:generate
# Check if tables exist before migrating to avoid errors if restore worked
# But user wants restore. Setup finished.

# 7. Nginx Setup
echo -e "${YELLOW}[+] Configuring Nginx (Port 8000)...${NC}"

cat > /etc/nginx/sites-available/lsev.conf <<EOF
server {
    listen 8000;
    server_name $APP_URL;
    root $target_dir/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php${PHP_VER}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # phpMyAdmin Integration
    location /phpmyadmin {
        root /usr/share/;
        index index.php index.html index.htm;
        location ~ ^/phpmyadmin/(.+\.php)$ {
            try_files \$uri =404;
            root /usr/share/;
            fastcgi_pass unix:/var/run/php/php${PHP_VER}-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
            include fastcgi_params;
        }
        location ~* ^/phpmyadmin/(.+\.(jpg|jpeg|gif|css|png|js|ico|html|xml|txt))$ {
            root /usr/share/;
        }
    }
}
EOF

# Enable Site
ln -sf /etc/nginx/sites-available/lsev.conf /etc/nginx/sites-enabled/
# Remove default if exists (optional but cleaner on port 80, but we use 8000 so irrelevant)
# rm /etc/nginx/sites-enabled/default

# Test & Reload
nginx -t && systemctl reload nginx

# 8. SSL Setup
if [[ "$USE_SSL" == "y" || "$USE_SSL" == "Y" ]]; then
    echo -e "${YELLOW}[+] Setting up Let's Encrypt SSL...${NC}"
    apt install -y certbot python3-certbot-nginx
    # Note: Certbot might struggle with port 8000 if not standard. 
    # Usually certbot expects port 80/443.
    # We will try simpler webroot mode or nginx plugin if port 80 is open to this server
    certbot --nginx -d $APP_URL --non-interactive --agree-tos -m admin@$APP_URL --redirect
fi

echo -e "${GREEN}=========================================${NC}"
echo -e "${GREEN}      INSTALLATION COMPLETED!            ${NC}"
echo -e "${GREEN}=========================================${NC}"
echo -e "Application URL : http://$APP_URL:8000"
echo -e "phpMyAdmin      : http://$APP_URL:8000/phpmyadmin"
echo -e "Database        : $DB_NAME"
echo -e "DB User         : root"
echo -e "DB Password     : (as configured)"
echo -e ""
echo -e "${YELLOW}NOTE: Make sure Port 8000 is open in your firewall (UFW/AWS Security Group)!${NC}"
