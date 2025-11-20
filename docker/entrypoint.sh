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

# Install/Update Composer dependencies if needed
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
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

