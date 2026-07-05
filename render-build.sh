#!/usr/bin/env bash
# Exit immediately if a command exits with a non-zero status
set -o errexit

echo "🚀 Compiling composer production modules..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

echo "📦 Optimizing system performance layouts..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🗄️ Processing database migrations dynamically..."
# The force flag triggers migrations automatically in non-interactive production modes
php artisan migrate --force