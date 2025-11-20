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
    
    # Method 1: Use composer config (recommended)
    echo "Configuring Composer authentication using composer config..."
    composer config --global http-basic.composer.fluxui.dev token "$FLUX_PRO_TOKEN"
    
    # Method 2: Also create auth.json file (backup method)
    AUTH_JSON="{
    \"http-basic\": {
        \"composer.fluxui.dev\": {
            \"username\": \"token\",
            \"password\": \"$FLUX_PRO_TOKEN\"
        }
    }
}"
    
    echo "$AUTH_JSON" > /root/.composer/auth.json
    chmod 600 /root/.composer/auth.json
    
    # Also set in project directory
    mkdir -p /var/www/html
    echo "$AUTH_JSON" > /var/www/html/auth.json
    chmod 600 /var/www/html/auth.json
    
    echo "Auth configured via composer config and auth.json files. Token length: ${#FLUX_PRO_TOKEN}"
    
    # Verify authentication works
    echo "Verifying authentication..."
    set +e
    TEST_AUTH=$(composer show -a livewire/flux-pro 2>&1 | head -5)
    if echo "$TEST_AUTH" | grep -q "HTTP 401\|authentication\|401"; then
        echo "⚠️  WARNING: Authentication test failed. Token may be invalid."
        echo "Test output: $TEST_AUTH"
    else
        echo "✅ Authentication verified successfully."
    fi
    set -e
else
    echo "Warning: FLUX_PRO_TOKEN not set. Flux Pro will not be installed."
fi

# Install/Update Composer dependencies if needed
# Use set +e to allow partial failures
set +e
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "Installing Composer dependencies..."
    
    # Try to install without flux-pro first (if it fails due to auth)
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev 2>&1 | tee /tmp/composer-install.log
    
    COMPOSER_EXIT=$?
    if [ $COMPOSER_EXIT -ne 0 ]; then
        if grep -q "flux-pro\|fluxui.dev" /tmp/composer-install.log; then
            echo "Warning: Composer install failed due to flux-pro authentication."
            echo "Will try to install flux-pro separately after auth is configured."
        else
            echo "Warning: Composer install had errors. Some packages may be missing."
        fi
    fi
fi
set -e

# Install and activate Flux Pro using the correct method
FLUX_PRO_INSTALLED=false
if [ -n "$FLUX_PRO_TOKEN" ] && [ "$FLUX_PRO_TOKEN" != "" ]; then
    set +e
    
    # Step 1: Install livewire/flux (free package, no auth needed)
    if ! composer show livewire/flux 2>/dev/null; then
        echo "Installing livewire/flux..."
        composer require livewire/flux:^2.2 --no-interaction --optimize-autoloader --update-no-dev
        if [ $? -ne 0 ]; then
            echo "Warning: Failed to install livewire/flux, continuing..."
        fi
    else
        echo "livewire/flux is already installed."
    fi
    
    # Step 2: Activate Flux Pro using flux:activate command
    # This command will use the token interactively or from environment
    echo "Activating Flux Pro..."
    
    # Try to activate with token via stdin (non-interactive)
    echo "$FLUX_PRO_TOKEN" | php artisan flux:activate 2>&1
    
    # Check if activation was successful
    if php artisan flux:status 2>/dev/null | grep -q "activated\|active\|pro"; then
        FLUX_PRO_INSTALLED=true
        echo "✅ Flux Pro activated successfully."
    else
        # Try alternative: set token as environment variable and activate
        echo "Trying alternative activation method..."
        FLUX_TOKEN="$FLUX_PRO_TOKEN" php artisan flux:activate <<EOF
$FLUX_PRO_TOKEN
EOF
        
        if php artisan flux:status 2>/dev/null | grep -q "activated\|active\|pro"; then
            FLUX_PRO_INSTALLED=true
            echo "✅ Flux Pro activated successfully (alternative method)."
        else
            echo "⚠️  Warning: Flux Pro activation may have failed."
            echo "Token preview: ${FLUX_PRO_TOKEN:0:10}... (first 10 chars)"
            echo "You may need to activate manually: php artisan flux:activate"
        fi
    fi
    
    set -e
else
    # Install livewire/flux even without token (free version)
    set +e
    if ! composer show livewire/flux 2>/dev/null; then
        echo "Installing livewire/flux (free version, no token needed)..."
        composer require livewire/flux:^2.2 --no-interaction --optimize-autoloader --update-no-dev || echo "Warning: Failed to install livewire/flux"
    fi
    set -e
    
    echo "Warning: FLUX_PRO_TOKEN not set. Flux Pro will not be activated."
    echo "Install livewire/flux (free) only. Set FLUX_PRO_TOKEN to activate Pro features."
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

