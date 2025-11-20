#!/bin/bash
# Script to check Flux Pro authentication setup

echo "=== Flux Pro Authentication Check ==="
echo ""

# Check if token is set
if [ -z "$FLUX_PRO_TOKEN" ] || [ "$FLUX_PRO_TOKEN" = "" ]; then
    echo "❌ FLUX_PRO_TOKEN is not set in environment"
    echo "   Please set it in .env file or docker-compose.yml"
    exit 1
else
    echo "✅ FLUX_PRO_TOKEN is set (length: ${#FLUX_PRO_TOKEN} chars)"
    echo "   Preview: ${FLUX_PRO_TOKEN:0:10}... (first 10 chars)"
fi

echo ""

# Check auth.json
if [ -f "/root/.composer/auth.json" ]; then
    echo "✅ Auth.json exists at /root/.composer/auth.json"
    echo "   Content:"
    cat /root/.composer/auth.json | sed 's/"password": "[^"]*"/"password": "***HIDDEN***"/'
else
    echo "❌ Auth.json not found at /root/.composer/auth.json"
    echo "   Creating it now..."
    mkdir -p /root/.composer
    cat > /root/.composer/auth.json <<EOF
{
    "http-basic": {
        "composer.fluxui.dev": {
            "username": "token",
            "password": "$FLUX_PRO_TOKEN"
        }
    }
}
EOF
    chmod 600 /root/.composer/auth.json
    echo "   ✅ Created auth.json"
fi

echo ""

# Check if flux-pro is installed
if composer show livewire/flux-pro 2>/dev/null; then
    echo "✅ Flux Pro is installed"
    composer show livewire/flux-pro
else
    echo "❌ Flux Pro is NOT installed"
    echo ""
    echo "Attempting to install..."
    composer require livewire/flux-pro:^2.2 --no-interaction --optimize-autoloader --no-dev
    if [ $? -eq 0 ]; then
        echo "✅ Flux Pro installed successfully!"
    else
        echo "❌ Failed to install Flux Pro"
        echo ""
        echo "Troubleshooting:"
        echo "1. Verify token is correct at https://flux.laravel.com"
        echo "2. Check if token is still valid (not expired)"
        echo "3. Ensure token has access to flux-pro package"
        echo "4. Try regenerating token"
        exit 1
    fi
fi

echo ""
echo "=== Check Complete ==="

