#!/bin/bash

# LSEV Uninstaller Script for Ubuntu
# Removes: Project Files, Database, Nginx Config, SSL

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${RED}=========================================${NC}"
echo -e "${RED}      LSEV UNINSTALLER (DANGER)          ${NC}"
echo -e "${RED}=========================================${NC}"

# Check root
if [[ $EUID -ne 0 ]]; then
   echo -e "${RED}This script must be run as root.${NC}" 
   exit 1
fi

echo -e "${YELLOW}WARNING: This will permanently delete:${NC}"
echo -e "  - Folder: /var/www/lsev"
echo -e "  - Database: video_app_v2"
echo -e "  - Config: /etc/nginx/sites-available/lsev.conf"
echo -e ""
read -p "Are you sure you want to proceed? Type 'DELETE' to confirm: " CONFIRM

if [ "$CONFIRM" != "DELETE" ]; then
    echo -e "${GREEN}Uninstall cancelled.${NC}"
    exit 0
fi

# 1. Remove Project Files
target_dir="/var/www/lsev"
if [ -d "$target_dir" ]; then
    echo -e "${YELLOW}[+] Removing project directory...${NC}"
    rm -rf $target_dir
    echo -e "${GREEN}    Done.${NC}"
else
    echo -e "${YELLOW}[!] Project directory not found. Skipping.${NC}"
fi

# 2. Remove Database
DB_NAME="video_app_v2"
echo -e "${YELLOW}[+] Dropping database '$DB_NAME'...${NC}"
# Prompt for password if needed, or try without if lucky/configured
if mysql -u root -e "DROP DATABASE IF EXISTS $DB_NAME;" 2>/dev/null; then
    echo -e "${GREEN}    Database dropped.${NC}"
else
    # Try with password prompt if the above failed (e.g. access denied)
    echo -e "${YELLOW}    Authentication required for MariaDB.${NC}"
    mysql -u root -p -e "DROP DATABASE IF EXISTS $DB_NAME;"
fi

# 3. Remove Nginx Config
NGINX_CONF="/etc/nginx/sites-available/lsev.conf"
NGINX_LINK="/etc/nginx/sites-enabled/lsev.conf"

if [ -f "$NGINX_CONF" ] || [ -L "$NGINX_LINK" ]; then
    echo -e "${YELLOW}[+] Removing Nginx configuration...${NC}"
    rm -f $NGINX_CONF
    rm -f $NGINX_LINK
    systemctl reload nginx
    echo -e "${GREEN}    Nginx config removed and reloaded.${NC}"
else
    echo -e "${YELLOW}[!] Nginx config not found. Skipping.${NC}"
fi

# 4. Cleanup SSL (Optional - interactive)
echo -e ""
read -p "Do you want to delete SSL certificates for this domain? (y/n): " DEL_SSL
if [[ "$DEL_SSL" == "y" || "$DEL_SSL" == "Y" ]]; then
    read -p "Enter the domain name used (e.g. example.com): " DOMAIN_NAME
    if [ ! -z "$DOMAIN_NAME" ]; then
        certbot delete --cert-name $DOMAIN_NAME
    fi
fi

echo -e "${GREEN}=========================================${NC}"
echo -e "${GREEN}      UNINSTALLATION COMPLETED           ${NC}"
echo -e "${GREEN}=========================================${NC}"
