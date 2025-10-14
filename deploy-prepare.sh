#!/bin/bash

##############################################################################
# Gerobaks Backend - Local Preparation Script
# Prepares backend for deployment to cPanel
##############################################################################

echo "🚀 Starting Deployment Preparation..."
echo "========================================"
echo ""

# Check if we're in backend directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Must be run from backend directory"
    exit 1
fi

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Step 1: Check PHP Version
echo "📋 Step 1: Checking PHP version..."
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "   PHP Version: $PHP_VERSION"

if php -r "exit(version_compare(PHP_VERSION, '8.1.0', '<') ? 1 : 0);"; then
    echo -e "${RED}❌ PHP 8.1+ is required${NC}"
    exit 1
fi
echo -e "${GREEN}✅ PHP version OK${NC}"
echo ""

# Step 2: Check Composer
echo "📋 Step 2: Checking Composer..."
if ! command -v composer &> /dev/null; then
    echo -e "${RED}❌ Composer not found. Please install Composer first.${NC}"
    exit 1
fi
COMPOSER_VERSION=$(composer --version)
echo "   $COMPOSER_VERSION"
echo -e "${GREEN}✅ Composer OK${NC}"
echo ""

# Step 3: Install Dependencies
echo "📋 Step 3: Installing production dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
echo -e "${GREEN}✅ Dependencies installed${NC}"
echo ""

# Step 4: Check .env file
echo "📋 Step 4: Checking environment file..."
if [ ! -f ".env" ]; then
    echo -e "${YELLOW}⚠️  .env file not found, copying from .env.example${NC}"
    cp .env.example .env
    echo -e "${YELLOW}⚠️  Please edit .env with production values before deploying!${NC}"
else
    echo -e "${GREEN}✅ .env file exists${NC}"
fi
echo ""

# Step 5: Generate APP_KEY if not set
echo "📋 Step 5: Checking APP_KEY..."
if grep -q "APP_KEY=$" .env; then
    echo "   Generating APP_KEY..."
    php artisan key:generate --force
    echo -e "${GREEN}✅ APP_KEY generated${NC}"
else
    echo -e "${GREEN}✅ APP_KEY already set${NC}"
fi
echo ""

# Step 6: Clear all caches
echo "📋 Step 6: Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✅ Caches cleared${NC}"
echo ""

# Step 7: Run tests
echo "📋 Step 7: Running tests..."
echo "   Testing API endpoints..."
if php test_api_comprehensive.php > /dev/null 2>&1; then
    echo -e "${GREEN}✅ API tests passed${NC}"
else
    echo -e "${YELLOW}⚠️  Some tests failed, check manually${NC}"
fi
echo ""

# Step 8: Create deployment package
echo "📋 Step 8: Creating deployment package..."

TIMESTAMP=$(date +%Y%m%d_%H%M%S)
ZIP_FILE="gerobaks-backend-${TIMESTAMP}.zip"

echo "   Packaging files..."

# Create zip excluding unnecessary files
zip -r "../$ZIP_FILE" . \
    -x "*.git*" \
    -x "node_modules/*" \
    -x "vendor/*" \
    -x "storage/logs/*" \
    -x "storage/framework/cache/*" \
    -x "storage/framework/sessions/*" \
    -x "storage/framework/views/*" \
    -x "database/database.sqlite" \
    -x "*.zip" \
    -x "tests/*" \
    -x ".env.example" \
    > /dev/null 2>&1

if [ -f "../$ZIP_FILE" ]; then
    FILE_SIZE=$(du -h "../$ZIP_FILE" | cut -f1)
    echo -e "${GREEN}✅ Package created: $ZIP_FILE ($FILE_SIZE)${NC}"
    echo ""
    echo "📦 Deployment package ready!"
    echo "   Location: ../$ZIP_FILE"
else
    echo -e "${RED}❌ Failed to create package${NC}"
    exit 1
fi

echo ""
echo "========================================"
echo "✅ Preparation Complete!"
echo "========================================"
echo ""
echo "📝 Next Steps:"
echo "   1. Upload ../$ZIP_FILE to cPanel File Manager"
echo "   2. Extract in public_html/"
echo "   3. SSH to server and run: ./deploy-server.sh"
echo ""
echo "📖 For detailed instructions, see DEPLOYMENT.md"
echo ""

# Create deployment checklist
cat > "../deployment-checklist-${TIMESTAMP}.txt" << EOF
DEPLOYMENT CHECKLIST - $(date)
================================

BEFORE UPLOADING:
[ ] Review .env file for production settings
[ ] Verify database credentials
[ ] Check APP_URL is set to production URL
[ ] Ensure APP_DEBUG=false
[ ] Backup existing production database (if any)

UPLOAD TO cPANEL:
[ ] Login to cPanel (https://gerobaks.dumeg.com:2083)
[ ] Navigate to File Manager → public_html
[ ] Upload $ZIP_FILE
[ ] Extract the zip file
[ ] Rename/move folder to desired location

SERVER CONFIGURATION:
[ ] Create MySQL database in cPanel
[ ] Create database user with all privileges
[ ] Update .env with database credentials
[ ] Run: composer install --no-dev --optimize-autoloader
[ ] Run: php artisan key:generate (if needed)
[ ] Run: php artisan migrate --force
[ ] Run: php artisan db:seed --force (optional)
[ ] Set permissions: chmod -R 755 storage bootstrap/cache
[ ] Run: php artisan config:cache
[ ] Run: php artisan route:cache
[ ] Run: php artisan view:cache

TESTING:
[ ] Test: curl https://gerobaks.dumeg.com/api/health
[ ] Test: Login API endpoint
[ ] Test: CORS headers
[ ] Test: Flutter app connection

FINAL CHECKS:
[ ] SSL certificate active (HTTPS)
[ ] Error logs empty
[ ] API documentation accessible
[ ] Cron jobs configured (if needed)
[ ] Backup created

Package: $ZIP_FILE
Created: $(date)
EOF

echo "📋 Deployment checklist created: ../deployment-checklist-${TIMESTAMP}.txt"
echo ""
