cd /var/www/vfuarrearsandtracker.impact-outsourcing.com
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
