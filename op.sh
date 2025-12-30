#!/bin/bash

# LSEV SERVER OPTIMIZER V6 (PERMISSION FIXER)
# Fixes "Upload Gagal" at 99% due to Permission/Ownership issues
# Fixes Nginx Body Size Limit

if [[ $EUID -ne 0 ]]; then
   echo -e "\033[0;31mThis script must be run as root.\033[0m" 
   exit 1
fi

WEB_ROOT="/var/www/lsev"

echo "=============================================="
echo " FIXING PERMISSIONS & NGINX LIMITS"
echo "=============================================="

# 1. FIX FILE PERMISSIONS (CRITICAL FOR UPLOAD MERGE)
if [ -d "$WEB_ROOT" ]; then
    echo "[+] Fixing Ownership (chown www-data)..."
    chown -R www-data:www-data $WEB_ROOT
    
    echo "[+] Fixing Permissions (chmod)..."
    chmod -R 755 $WEB_ROOT
    chmod -R 775 $WEB_ROOT/storage
    chmod -R 777 $WEB_ROOT/public # Allow public uploads
    chmod -R 777 $WEB_ROOT/storage/logs
    
    echo "    -> Permissions Fixed (www-data)"
else
    echo "[-] Web root $WEB_ROOT not found!"
fi

# 2. FIX PHP-FPM (SAFE MODE 60)
VERSIONS=("8.1" "8.2" "8.3")
for PHP_VER in "${VERSIONS[@]}"; do
    FPM_CONF="/etc/php/$PHP_VER/fpm/pool.d/www.conf"
    if [ -f "$FPM_CONF" ]; then
        echo "[+] Tuning PHP $PHP_VER (Safe Mode 60)..."
        # Clean
        sed -i '/^pm =/d' $FPM_CONF
        sed -i '/^pm.max_children =/d' $FPM_CONF
        sed -i '/^pm.max_requests =/d' $FPM_CONF
        sed -i '/^php_admin_value\[memory_limit\]/d' $FPM_CONF
        
        # Write
        echo "" >> $FPM_CONF
        echo "pm = ondemand" >> $FPM_CONF
        echo "pm.max_children = 60" >> $FPM_CONF
        echo "pm.process_idle_timeout = 10s" >> $FPM_CONF
        echo "pm.max_requests = 1000" >> $FPM_CONF
        echo "php_admin_value[memory_limit] = 512M" >> $FPM_CONF
        echo "php_admin_value[upload_max_filesize] = 512M" >> $FPM_CONF
        echo "php_admin_value[post_max_size] = 512M" >> $FPM_CONF
        echo "php_admin_value[max_execution_time] = 600" >> $FPM_CONF
        
        systemctl restart php$PHP_VER-fpm
    fi
done

# 3. FIX NGINX CONFIG (Add client_max_body_size)
NGINX_CONF="/etc/nginx/sites-available/lsev"
if [ ! -f "$NGINX_CONF" ]; then
    NGINX_CONF="/etc/nginx/sites-available/default"
fi

if [ -f "$NGINX_CONF" ]; then
    echo "[+] Checking Nginx Config ($NGINX_CONF)..."
    # Check if directive exists
    if grep -q "client_max_body_size" "$NGINX_CONF"; then
        # Update existing
        sed -i 's/client_max_body_size.*/client_max_body_size 512M;/g' $NGINX_CONF
    else
        # Insert into server block
        sed -i '/server_name/a \    client_max_body_size 512M;' $NGINX_CONF
    fi
    echo "    -> Set client_max_body_size 512M"
fi

echo "[+] Restarting Nginx..."
systemctl restart nginx

echo "=============================================="
echo " ALL FIXED! UPLOAD SHOULD WORK NOW."
echo "=============================================="
