#!/usr/bin/env bash

echo "Installing dependencies"
composer install --no-dev --prefer-dist --working-dir=/var/www/html

echo "Optimizing laravel app"
php artisan optimize

echo "Running pending migrations"
php artisan migrate --force
