#!/bin/bash

# AUTO BOOSTER - UBUNTU VERSION (MATCHING WINDOWS LOGIC)

# Configuration
TARGET_DIR="/var/www/lsev"

# Check if directory exists
if [ ! -d "$TARGET_DIR" ]; then
    echo "Error: Directory $TARGET_DIR not found."
    exit 1
fi

while true; do
    clear
    # Color Green
    echo -e "\033[0;32m"
    echo "========================================================"
    echo " AUTO BOOSTER RUNNING... (Tekan CTRL+C untuk stop)"
    echo " Mode: Visual Log (Detail User dan Views Tampil Disini)"
    echo "========================================================"
    echo -e "\033[0m" # Reset Color
    echo ""

    # Jalankan Booster (Output Tampil disini)
    php "$TARGET_DIR/artisan" boost:process

    # Jalankan Task Lain (Cleanup dll - Output Hidden agar rapi)
    php "$TARGET_DIR/artisan" schedule:run > /dev/null 2>&1

    echo ""
    echo "  [WAIT] Menunggu 60 detik untuk proses berikutnya..."
    
    # Wait 60 seconds (timeout /t 60)
    sleep 60
done
