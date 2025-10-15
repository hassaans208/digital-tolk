#!/bin/bash

# Exit on any error
set -e

# Function to wait for database
wait_for_db() {
    echo "Waiting for database connection..."
    while ! php artisan migrate:status > /dev/null 2>&1; do
        echo "Database not ready, waiting..."
        sleep 2
    done
    echo "Database is ready!"
}

# Function to run migrations
run_migrations() {
    echo "Running database migrations..."
    php artisan migrate --force
}

# Function to clear and cache config
optimize_app() {
    echo "Optimizing application..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
}

# Function to create storage link
create_storage_link() {
    if [ ! -L "/var/www/public/storage" ]; then
        echo "Creating storage link..."
        php artisan storage:link
    fi
}

# Main execution
echo "Starting Laravel application setup..."

# Wait for database to be ready
wait_for_db

# Run migrations
run_migrations

# Create storage link
create_storage_link

# Optimize application for production
if [ "$APP_ENV" = "production" ]; then
    optimize_app
fi

echo "Laravel application setup completed!"

# Execute the main command
exec "$@"
