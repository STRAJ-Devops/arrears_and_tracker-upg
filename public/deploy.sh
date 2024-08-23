#!/bin/bash

cd /var/www/vfuarrearsandtracker.impact-outsourcing.com || exit
git pull origin master >> /var/www/vfuarrearsandtracker.impact-outsourcing.com/storage/laravel.log 2>&1
composer install --optimize-autoloader --no-dev >> /var/www/vfuarrearsandtracker.impact-outsourcing.com/storage/laravel.log 2>&1
php artisan migrate --force >> /var/www/vfuarrearsandtracker.impact-outsourcing.com/storage/logs/laravel.log 2>&1
php artisan config:cache >> /var/www/vfuarrearsandtracker.impact-outsourcing.com/storage/logs/laravel.log 2>&1
# compile assets with npm
npm install >> /var/www/vfuarrearsandtracker.impact-outsourcing.com/storage/logs/laravel.log 2>&1
# build assets
npm run build >> /var/www/vfuarrearsandtracker.impact-outsourcing.com/storage/logs/laravel.log 2>&1
# log a  deployment success message
echo "Deployment successful at $(date)" >> /var/www/vfuarrearsandtracker.impact-outsourcing.com/storage/logs/laravel.log
