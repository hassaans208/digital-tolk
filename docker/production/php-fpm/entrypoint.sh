#!/bin/bash

# Don't exit on error - let the container continue running
# set -e

# Function to wait for database
wait_for_db() {
    echo "Waiting for database connection..."
    local max_attempts=30
    local attempt=1
    
    while [ $attempt -le $max_attempts ]; do
        if php artisan migrate:status > /dev/null 2>&1; then
            echo "Database is ready!"
            return 0
        fi
        
        echo "Database not ready, waiting... (attempt $attempt/$max_attempts)"
        sleep 2
        attempt=$((attempt + 1))
    done
    
    echo "Database connection failed after $max_attempts attempts"
    return 1
}

# Function to run migrations
run_migrations() {
    echo "Running database migrations..."
    if php artisan migrate:fresh --seed; then
        echo "Migrations completed successfully"
    else
        echo "Warning: Migrations failed, but continuing..."
    fi
}

# Function to clear and cache config
optimize_app() {
    echo "Optimizing application..."
    if php artisan config:cache && php artisan route:cache && php artisan view:cache; then
        echo "Application optimization completed"
    else
        echo "Warning: Application optimization failed, but continuing..."
    fi
}

# Function to create storage link and fix permissions
create_storage_link() {
    echo "Setting up storage directories..."
    
    mkdir -p /var/www/storage/app/public
    mkdir -p /var/www/storage/framework/cache
    mkdir -p /var/www/storage/framework/sessions
    mkdir -p /var/www/storage/framework/views
    mkdir -p /var/www/storage/logs
    
    chown -R www:www /var/www/storage
    chmod -R 777 /var/www/storage
    chmod -R 777 /var/www/storage/logs/laravel.log
    chmod -R 777 /var/www/bootstrap/cache
    
    chmod -R 777 /var/www/storage/framework/views
    chmod -R 777 /var/www/storage/framework/cache
    chmod -R 777 /var/www/storage/framework/sessions
    
    if [ ! -L "/var/www/public/storage" ] && [ -f "/var/www/artisan" ]; then
        echo "Creating storage link..."
        if php artisan storage:link; then
            echo "Storage link created successfully"
        else
            echo "Warning: Storage link creation failed, but continuing..."
        fi
    fi
}

echo "Starting Laravel application setup..."

create_storage_link

if wait_for_db; then
    # Run migrations
    run_migrations
    
    # Optimize application for production
    if [ "$APP_ENV" = "production" ]; then
        optimize_app
    fi
    
    echo "Laravel application setup completed!"
else
    echo "Warning: Database not ready, skipping migrations. Container will continue running."
fi

exec "$@"
