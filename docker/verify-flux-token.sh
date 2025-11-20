#!/bin/bash
# Script to verify Flux Pro token

TOKEN="${1:-$FLUX_PRO_TOKEN}"

if [ -z "$TOKEN" ]; then
    echo "Usage: $0 <token>"
    echo "Or set FLUX_PRO_TOKEN environment variable"
    exit 1
fi

echo "Verifying Flux Pro token..."
echo "Token preview: ${TOKEN:0:10}... (length: ${#TOKEN})"
echo ""

# Test token with curl
echo "Testing token authentication..."
RESPONSE=$(curl -s -w "\n%{http_code}" -u "token:$TOKEN" "https://composer.fluxui.dev/packages.json" 2>&1)
HTTP_CODE=$(echo "$RESPONSE" | tail -1)
BODY=$(echo "$RESPONSE" | sed '$d')

echo "HTTP Status Code: $HTTP_CODE"

if [ "$HTTP_CODE" = "200" ]; then
    echo "✅ Token is VALID!"
    echo "Response preview:"
    echo "$BODY" | head -5
    exit 0
elif [ "$HTTP_CODE" = "401" ]; then
    echo "❌ Token is INVALID or EXPIRED"
    echo ""
    echo "Possible reasons:"
    echo "1. Token is incorrect"
    echo "2. Token has expired"
    echo "3. Token doesn't have access to flux-pro package"
    echo ""
    echo "Please:"
    echo "1. Check token at https://flux.laravel.com"
    echo "2. Generate a new token if needed"
    echo "3. Ensure token has access to flux-pro"
    exit 1
else
    echo "⚠️  Unexpected response: $HTTP_CODE"
    echo "Response: $BODY"
    exit 1
fi

