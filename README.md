# README #

This is Laravel 5.4 project. Include network/redis/database connection modules.

##### Build Setup

``` bash
# Updating dependencies
composer install

# Rename App
php artisan app:name [your_project_name]

# Generate new App key
php artisan key:generate

# Cache/optimize
php artisan config:cache
php artisan route:cache
php artisan optimize --force
```