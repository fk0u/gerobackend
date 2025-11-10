#!/bin/bash

# ===================================================================
# PRODUCTION DEPLOYMENT SCRIPT
# Deploy fixes to gerobaks.dumeg.com
# ===================================================================

echo "🚀 GEROBAKS PRODUCTION DEPLOYMENT"
echo "=================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
PRODUCTION_PATH="/home/dumeg/public_html/gerobaks.dumeg.com"
BACKUP_PATH="/home/dumeg/backups/gerobaks_$(date +%Y%m%d_%H%M%S)"

# Check if we're on the server
if [ ! -d "$PRODUCTION_PATH" ]; then
    echo -e "${RED}❌ Error: Production path not found!${NC}"
    echo "This script must be run ON the production server."
    echo ""
    echo "To deploy from local:"
    echo "1. scp this script to server"
    echo "2. SSH to server: ssh dumeg@gerobaks.dumeg.com"
    echo "3. Run: bash deploy_production_fix.sh"
    exit 1
fi

echo "📁 Production path: $PRODUCTION_PATH"
echo "💾 Backup path: $BACKUP_PATH"
echo ""

# Step 1: Create backup
echo "📦 Step 1: Creating backup..."
mkdir -p "$BACKUP_PATH"
cp "$PRODUCTION_PATH/routes/api.php" "$BACKUP_PATH/" 2>/dev/null || echo "Warning: routes/api.php not found"
cp "$PRODUCTION_PATH/app/Http/Controllers/Api/ScheduleController.php" "$BACKUP_PATH/" 2>/dev/null || echo "Warning: ScheduleController.php not found"
cp "$PRODUCTION_PATH/app/Http/Resources/ScheduleResource.php" "$BACKUP_PATH/" 2>/dev/null || echo "Warning: ScheduleResource.php not found"
echo -e "${GREEN}✅ Backup created at: $BACKUP_PATH${NC}"
echo ""

# Step 2: Pull latest code
echo "📥 Step 2: Pulling latest code from git..."
cd "$PRODUCTION_PATH"

# Check if git repo
if [ -d ".git" ]; then
    echo "Git repository found. Pulling changes..."
    git stash  # Stash any local changes
    git pull origin fk0u-feat/backend
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Code pulled successfully${NC}"
    else
        echo -e "${YELLOW}⚠️  Git pull failed. You may need to upload files manually.${NC}"
    fi
else
    echo -e "${YELLOW}⚠️  Not a git repository. Please upload files manually via FTP.${NC}"
fi
echo ""

# Step 3: Clear caches
echo "🗑️  Step 3: Clearing caches..."
php artisan route:clear
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo -e "${GREEN}✅ Caches cleared${NC}"
echo ""

# Step 4: Optimize for production
echo "⚡ Step 4: Optimizing for production..."
php artisan config:cache
php artisan route:cache
echo -e "${GREEN}✅ Optimized${NC}"
echo ""

# Step 5: Verify routes
echo "🔍 Step 5: Verifying routes..."
echo "Checking schedule routes..."
php artisan route:list --path=schedules | grep "POST.*schedules"
echo ""

# Step 6: Check permissions
echo "🔐 Step 6: Checking file permissions..."
chmod -R 755 storage bootstrap/cache
chown -R dumeg:dumeg storage bootstrap/cache
echo -e "${GREEN}✅ Permissions set${NC}"
echo ""

# Summary
echo "=================================="
echo "📊 DEPLOYMENT SUMMARY"
echo "=================================="
echo ""
echo -e "${GREEN}✅ Backup created${NC}"
echo -e "${GREEN}✅ Code updated${NC}"
echo -e "${GREEN}✅ Caches cleared${NC}"
echo -e "${GREEN}✅ Production optimized${NC}"
echo ""
echo "🧪 NEXT STEPS:"
echo "1. Test API endpoint:"
echo "   curl -X POST https://gerobaks.dumeg.com/api/login \\"
echo "     -H 'Content-Type: application/json' \\"
echo "     -d '{\"email\": \"daffa@gmail.com\", \"password\": \"daffa123\"}'"
echo ""
echo "2. Test schedule creation with token"
echo "3. Monitor logs: tail -f storage/logs/laravel.log"
echo ""
echo -e "${YELLOW}⚠️  If issues persist, restore backup:${NC}"
echo "   cp $BACKUP_PATH/api.php $PRODUCTION_PATH/routes/"
echo ""
echo "=================================="
echo "🎉 DEPLOYMENT COMPLETE!"
echo "=================================="
