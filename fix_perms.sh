#!/bin/bash
# FIX LOCKED FILES SCRIPT
# Run this to unlock all files in your uploads folder

if [[ $EUID -ne 0 ]]; then
   echo "Run as root!" 
   exit 1
fi

echo "=================================="
echo " UNLOCKING ALL FILES..."
echo "=================================="

# 1. Force Ownership to Web User (www-data)
chown -R www-data:www-data /var/www/lsev/public/uploads
chown -R www-data:www-data /var/www/lsev/storage

# 2. Force Permissions (Read/Write for Everyone temporarily to be safe)
chmod -R 777 /var/www/lsev/public/uploads
chmod -R 777 /var/www/lsev/storage

echo "=================================="
echo " DONE. FILES UNLOCKED."
echo " Try uploading now."
echo "=================================="
