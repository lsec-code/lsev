@echo off
title AUTO BOOSTER - VISUAL MODE (JANGAN DITUTUP)
color 0A
cd /d c:\laragon\www\v2_laravel
set PHP_BIN=c:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe

:loop
cls
echo ========================================================
echo   AUTO BOOSTER RUNNING... (Tekan CTRL+C untuk stop)
echo   Mode: Visual Log (Detail User dan Views Tampil Disini)
echo ========================================================
echo.

REM Jalankan Booster (Output Tampil disini)
"%PHP_BIN%" artisan boost:process

REM Jalankan Task Lain (Cleanup dll - Output Hidden agar rapi)
"%PHP_BIN%" artisan schedule:run >NUL 2>&1

echo.
echo   [WAIT] Menunggu 60 detik untuk proses berikutnya...
timeout /t 60 >NUL
goto loop
