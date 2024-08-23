#!/bin/bash

cd /var/www/vfuarrearsandtracker.impact-outsourcing.com || exit
git pull origin main >> /var/www/vfuarrearsandtracker.impact-outsourcing.com/storage 2>&1
composer install --optimize-autoloader --no-dev >> /var/www/vfuarrearsandtracker.impact-outsourcing.com/storage/laravel.log 2>&1
php artisan migrate --force >> /var/www/vfuarrearsandtracker.impact-outsourcing.com/storage/logs/laravel.log 2>&1
php artisan config:cache >> /var/www/vfuarrearsandtracker.impact-outsourcing.com/storage/laravel.log 2>&1
php artisan route:cache >> /var/www/vfuarrearsandtracker.impact-outsourcing.com/storage/laravel.log 2>&1
php artisan view:cache >> /var/www/vfuarrearsandtracker.impact-outsourcing.com/storage/laravel.log 2>&1