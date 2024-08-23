#!/bin/bash

cd /var/www/vfuarrearsandtracker.impact-outsourcing.com || exit
git pull origin main >> /var/www/logs/deploy.log 2>&1
composer install --optimize-autoloader --no-dev >> /var/www/storage/deploy.log 2>&1
php artisan migrate --force >> /var/www/logs/deploy.log 2>&1
php artisan config:cache >> /var/www/logs/deploy.log 2>&1
php artisan route:cache >> /var/www/logs/deploy.log 2>&1
php artisan view:cache >> /var/www/logs/deploy.log 2>&1
