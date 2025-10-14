#!/bin/bash

##############################################################################
# Gerobaks Backend - Server Deployment Script
# Run this on cPanel server after uploading files
##############################################################################

echo "🚀 Starting Server Deployment..."
echo "================================="
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# Check if artisan exists
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Error: Must be run from Laravel root directory${NC}"
    exit 1
fi

# Step 1: Install Composer dependencies
echo "📋 Step 1: Installing Composer dependencies..."
if command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader --no-interaction
    echo -e "${GREEN}✅ Dependencies installed${NC}"
else
    echo -e "${YELLOW}⚠️  Composer not found. Trying composer.phar...${NC}"
    if [ -f "composer.phar" ]; then
        php composer.phar install --no-dev --optimize-autoloader --no-interaction
        echo -e "${GREEN}✅ Dependencies installed${NC}"
    else
        echo -e "${RED}❌ Composer not available. Please install manually.${NC}"
        echo "   Download: curl -sS https://getcomposer.org/installer | php"
        exit 1
    fi
fi
echo ""

# Step 2: Check .env
echo "📋 Step 2: Checking .env configuration..."
if [ ! -f ".env" ]; then
    echo -e "${RED}❌ .env file not found!${NC}"
    echo "   Copying from .env.example..."
    cp .env.example .env
    echo -e "${YELLOW}⚠️  Please edit .env with production settings!${NC}"
    echo ""
    read -p "Press Enter after editing .env..."
else
    echo -e "${GREEN}✅ .env file exists${NC}"
fi
echo ""

# Step 3: Generate APP_KEY
echo "📋 Step 3: Checking APP_KEY..."
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=base64:$" .env; then
    echo "   Generating APP_KEY..."
    php artisan key:generate --force
    echo -e "${GREEN}✅ APP_KEY generated${NC}"
else
    echo -e "${GREEN}✅ APP_KEY already set${NC}"
fi
echo ""

# Step 4: Set permissions
echo "📋 Step 4: Setting file permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env
echo -e "${GREEN}✅ Permissions set${NC}"
echo ""

# Step 5: Database setup
echo "📋 Step 5: Database setup..."
echo "   Testing database connection..."

if php artisan migrate:status > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Database connection OK${NC}"
    
    read -p "   Run migrations? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        php artisan migrate --force
        echo -e "${GREEN}✅ Migrations completed${NC}"
        
        read -p "   Seed database with test data? (y/n) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            php artisan db:seed --force
            echo -e "${GREEN}✅ Database seeded${NC}"
        fi
    fi
else
    echo -e "${RED}❌ Database connection failed${NC}"
    echo "   Please check .env database credentials"
    echo ""
    exit 1
fi
echo ""

# Step 6: Clear and cache
echo "📋 Step 6: Optimizing for production..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

composer dump-autoload --optimize
echo -e "${GREEN}✅ Optimization complete${NC}"
echo ""

# Step 7: Test API
echo "📋 Step 7: Testing API endpoints..."
echo "   Testing health endpoint..."

HEALTH_RESPONSE=$(php artisan tinker --execute="echo file_get_contents('http://localhost/api/health');" 2>/dev/null)
if [[ $HEALTH_RESPONSE == *"ok"* ]]; then
    echo -e "${GREEN}✅ API health check passed${NC}"
else
    echo -e "${YELLOW}⚠️  Could not verify API health${NC}"
fi
echo ""

# Step 8: Summary
echo "================================="
echo "✅ Deployment Complete!"
echo "================================="
echo ""
echo "📝 Post-Deployment Checklist:"
echo "   [ ] Test: https://gerobaks.dumeg.com/api/health"
echo "   [ ] Test: Login API"
echo "   [ ] Test: CORS headers"
echo "   [ ] Test: Flutter app connection"
echo "   [ ] Setup cron job (if needed)"
echo "   [ ] Check error logs: storage/logs/laravel.log"
echo ""
echo "🔍 View logs: tail -f storage/logs/laravel.log"
echo ""
