@echo off
REM === Konfigurasi Database ===
set DB_NAME=siakad
set DB_USER=root
set DB_PASS=
set SQL_FILE=database.sql

REM === Buat Database Kalau Belum Ada ===
echo Membuat database %DB_NAME% jika belum ada...
mysql -u %DB_USER% -p%DB_PASS% -e "CREATE DATABASE IF NOT EXISTS %DB_NAME% CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

REM === Import File SQL ===
echo Mengimport %SQL_FILE% ke database %DB_NAME%...
mysql -u %DB_USER% -p%DB_PASS% %DB_NAME% < %SQL_FILE%

echo ================================
echo Database %DB_NAME% sudah siap!
echo ================================
pause
