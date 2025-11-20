#!/bin/bash
set -e

echo "Starting entrypoint script..."

# Wait for database to be ready
echo "Waiting for database..."
DB_HOST=${DB_HOST:-mariadb}
DB_PORT=${DB_PORT:-3306}
DB_USERNAME=${DB_USERNAME:-perjadin_user}
DB_PASSWORD=${DB_PASSWORD:-perjadin_pass}
DB_DATABASE=${DB_DATABASE:-perjadin_db}

until php -r "
try {
    \$pdo = new PDO('mysql:host=${DB_HOST};port=${DB_PORT}', '${DB_USERNAME}', '${DB_PASSWORD}');
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo 'Database connection established\n';
    exit(0);
} catch (Exception \$e) {
    exit(1);
}" 2>/dev/null; do
  echo "Database is unavailable - sleeping..."
  sleep 2
done

echo "Database is ready!"

# Set permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Setup Composer auth for Flux Pro if token is provided
if [ -n "$FLUX_PRO_TOKEN" ] && [ "$FLUX_PRO_TOKEN" != "" ]; then
    echo "Setting up Flux Pro authentication..."
    mkdir -p /root/.composer
    echo "{\"http-basic\": {\"composer.fluxui.dev\": {\"username\": \"token\", \"password\": \"$FLUX_PRO_TOKEN\"}}}" > /root/.composer/auth.json
    chmod 600 /root/.composer/auth.json
fi

# Install/Update Composer dependencies if needed
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Install Flux Pro if token is available and package is not installed
FLUX_PRO_INSTALLED=false
if [ -n "$FLUX_PRO_TOKEN" ] && [ "$FLUX_PRO_TOKEN" != "" ]; then
    if ! composer show livewire/flux-pro 2>/dev/null; then
        echo "Installing Flux Pro..."
        composer require livewire/flux-pro:^2.2 --no-interaction --optimize-autoloader && FLUX_PRO_INSTALLED=true || echo "Failed to install Flux Pro, continuing..."
    else
        echo "Flux Pro is already installed."
        FLUX_PRO_INSTALLED=true
    fi
fi

# Build assets if flux-pro is installed or if public/build doesn't exist
if [ "$FLUX_PRO_INSTALLED" = true ]; then
    echo "Building assets (Flux Pro is installed)..."
    npm run build || echo "Warning: Asset build failed, continuing..."
elif [ ! -d "public/build" ]; then
    echo "Warning: Flux Pro not installed and assets not built. Build may fail."
    echo "Please set FLUX_PRO_TOKEN in environment and restart container."
    # Try to build anyway (may fail if flux.css is missing)
    npm run build || echo "Asset build failed - Flux Pro required for build"
fi

# Generate application key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "Generating application key..."
    php artisan key:generate --ansi
fi

# Cache configuration
echo "Caching configuration..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Run migrations
echo "Running migrations..."
php artisan migrate --force || echo "Migrations failed or already run"

# Create storage link if not exists
if [ ! -L "public/storage" ]; then
    echo "Creating storage link..."
    php artisan storage:link || true
fi

echo "Entrypoint script completed successfully!"

# Execute the main command
exec "$@"

