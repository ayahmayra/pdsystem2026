#!/bin/bash
# Don't exit on error immediately - we'll handle errors explicitly
set +e

echo "Starting entrypoint script..."

# Fix git ownership warning
git config --global --add safe.directory /var/www/html 2>/dev/null || true

# Wait for database to be ready
echo "Waiting for database..."
DB_HOST=${DB_HOST:-mariadb}
DB_PORT=${DB_PORT:-3306}
DB_USERNAME=${DB_USERNAME:-perjadin_user}
DB_PASSWORD=${DB_PASSWORD:-perjadin_pass}
DB_DATABASE=${DB_DATABASE:-perjadin_db}

# Wait for database with timeout
DB_WAIT_COUNT=0
DB_MAX_WAIT=30
until php -r "
try {
    \$pdo = new PDO('mysql:host=${DB_HOST};port=${DB_PORT}', '${DB_USERNAME}', '${DB_PASSWORD}');
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo 'Database connection established\n';
    exit(0);
} catch (Exception \$e) {
    exit(1);
}" 2>/dev/null; do
  DB_WAIT_COUNT=$((DB_WAIT_COUNT + 1))
  if [ $DB_WAIT_COUNT -ge $DB_MAX_WAIT ]; then
    echo "ERROR: Database not available after $DB_MAX_WAIT attempts. Exiting."
    exit 1
  fi
  echo "Database is unavailable - sleeping... (attempt $DB_WAIT_COUNT/$DB_MAX_WAIT)"
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
    
    # Also set in project directory (Composer checks both locations)
    mkdir -p /var/www/html
    PROJECT_AUTH="/var/www/html/auth.json"
    
    # Create auth.json in both locations
    AUTH_JSON="{
    \"http-basic\": {
        \"composer.fluxui.dev\": {
            \"username\": \"token\",
            \"password\": \"$FLUX_PRO_TOKEN\"
        }
    }
}"
    
    echo "$AUTH_JSON" > /root/.composer/auth.json
    echo "$AUTH_JSON" > "$PROJECT_AUTH"
    
    chmod 600 /root/.composer/auth.json
    chmod 600 "$PROJECT_AUTH"
    
    echo "Auth.json created in /root/.composer/ and project root. Token length: ${#FLUX_PRO_TOKEN}"
    
    # Verify auth.json format
    if command -v php >/dev/null 2>&1; then
        php -r "json_decode(file_get_contents('/root/.composer/auth.json')); echo json_last_error() === JSON_ERROR_NONE ? 'Auth.json is valid JSON' : 'Auth.json is INVALID JSON';" 2>/dev/null || echo "Could not validate auth.json"
    fi
else
    echo "Warning: FLUX_PRO_TOKEN not set. Flux Pro will not be installed."
fi

# Install/Update Composer dependencies if needed
# Use set +e to allow partial failures
set +e
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
    COMPOSER_EXIT=$?
    if [ $COMPOSER_EXIT -ne 0 ]; then
        echo "Warning: Composer install had errors. Some packages may be missing."
    fi
fi
set -e

# Install Flux Pro if token is available and package is not installed
FLUX_PRO_INSTALLED=false
if [ -n "$FLUX_PRO_TOKEN" ] && [ "$FLUX_PRO_TOKEN" != "" ]; then
    set +e
    if ! composer show livewire/flux-pro 2>/dev/null; then
        echo "Installing Flux Pro..."
        # composer require doesn't support --no-dev, use --update-no-dev instead
        composer require livewire/flux-pro:^2.2 --no-interaction --optimize-autoloader --update-no-dev
        if [ $? -eq 0 ]; then
            echo "Flux Pro package installed successfully."
            
            # Activate Flux Pro (required after installation)
            echo "Activating Flux Pro..."
            php artisan flux:activate --no-interaction
            if [ $? -eq 0 ]; then
                FLUX_PRO_INSTALLED=true
                echo "Flux Pro activated successfully."
            else
                echo "Warning: Flux Pro installed but activation failed. Continuing..."
                FLUX_PRO_INSTALLED=true  # Still mark as installed, activation might not be critical
            fi
        else
            echo "ERROR: Failed to install Flux Pro. Please check your token."
            echo "Token preview: ${FLUX_PRO_TOKEN:0:10}... (first 10 chars)"
            echo "Debug: Checking auth.json..."
            if [ -f "/root/.composer/auth.json" ]; then
                echo "Auth.json exists. Content (password hidden):"
                cat /root/.composer/auth.json | sed 's/"password": "[^"]*"/"password": "***HIDDEN***"/'
            else
                echo "Auth.json NOT found!"
            fi
        fi
    else
        echo "Flux Pro is already installed."
        
        # Check if Flux Pro is activated
        if php artisan flux:status 2>/dev/null | grep -q "activated\|active"; then
            echo "Flux Pro is already activated."
        else
            echo "Activating Flux Pro..."
            php artisan flux:activate --no-interaction || echo "Warning: Activation failed, continuing..."
        fi
        
        FLUX_PRO_INSTALLED=true
    fi
    set -e
fi

# Install npm dependencies if needed
if [ ! -d "node_modules" ] || [ ! -f "node_modules/.bin/vite" ]; then
    echo "Installing npm dependencies..."
    set +e
    npm install
    if [ $? -ne 0 ]; then
        echo "Warning: npm install failed, continuing..."
    else
        echo "npm dependencies installed successfully."
    fi
    set -e
fi

# Build assets if flux-pro is installed or if public/build doesn't exist
set +e
if [ "$FLUX_PRO_INSTALLED" = true ]; then
    echo "Building assets (Flux Pro is installed)..."
    if [ -f "node_modules/.bin/vite" ] || command -v vite >/dev/null 2>&1; then
        npm run build
        if [ $? -ne 0 ]; then
            echo "Warning: Asset build failed, continuing..."
        else
            echo "Assets built successfully."
        fi
    else
        echo "Warning: vite not found. Skipping asset build."
    fi
elif [ ! -d "public/build" ]; then
    echo "Warning: Flux Pro not installed and assets not built."
    echo "Please set FLUX_PRO_TOKEN in environment and restart container."
    # Try to build anyway (may fail if flux.css is missing)
    if [ -f "node_modules/.bin/vite" ] || command -v vite >/dev/null 2>&1; then
        npm run build 2>&1 | head -20
        if [ $? -ne 0 ]; then
            echo "Asset build failed - Flux Pro required for build. Continuing anyway..."
        fi
    else
        echo "Warning: vite not found. Cannot build assets."
    fi
fi
set -e

# Generate application key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "Generating application key..."
    php artisan key:generate --ansi
fi

# Cache configuration
echo "Caching configuration..."
set +e
php artisan config:cache || echo "Config cache failed, continuing..."
php artisan route:cache || echo "Route cache failed, continuing..."
php artisan view:cache || echo "View cache failed, continuing..."
set -e

# Run migrations
echo "Running migrations..."
set +e
php artisan migrate --force
if [ $? -ne 0 ]; then
    echo "Migrations failed or already run, continuing..."
fi
set -e

# Create storage link if not exists
if [ ! -L "public/storage" ]; then
    echo "Creating storage link..."
    set +e
    php artisan storage:link || echo "Storage link failed, continuing..."
    set -e
fi

echo "Entrypoint script completed successfully!"

# Execute the main command (php-fpm)
# This should never fail, but if it does, container will restart
exec "$@"

