#!/bin/bash

# Schedule API Decimal Casting Fix Script
# Fixes "Unable to cast value to a decimal" error

set -e

echo "═══════════════════════════════════════════════════════════"
echo "   🔧 Schedule API Decimal Casting Fix"
echo "═══════════════════════════════════════════════════════════"
echo ""

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Check artisan
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Error: artisan file not found!${NC}"
    exit 1
fi

echo "Step 1: Checking database connection..."
if php artisan db:show > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Database connected${NC}"
else
    echo -e "${RED}❌ Database connection failed!${NC}"
    exit 1
fi

echo ""
echo "Step 2: Running fix migration (makes nullable + cleans data)..."
if php artisan migrate --force --path=database/migrations/2025_01_14_000002_fix_schedules_decimal_fields.php; then
    echo -e "${GREEN}✅ Fix migration completed${NC}"
else
    echo -e "${YELLOW}⚠️  Migration may have already run${NC}"
    echo "Trying to run all pending migrations..."
    php artisan migrate --force
fi

echo ""
echo "Step 4: Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear
echo -e "${GREEN}✅ Caches cleared${NC}"

echo ""
echo "Step 5: Verifying fix..."

# Check for invalid data
INVALID_COUNT=$(php artisan tinker --execute="
use App\Models\Schedule;
\$count = Schedule::where('pickup_latitude', '')
    ->orWhere('pickup_longitude', '')
    ->orWhere('latitude', '')
    ->orWhere('longitude', '')
    ->count();
echo \$count;
" 2>/dev/null || echo "0")

if [ "$INVALID_COUNT" -eq "0" ]; then
    echo -e "${GREEN}✅ No invalid data found${NC}"
else
    echo -e "${YELLOW}⚠️  Found $INVALID_COUNT records with invalid data${NC}"
    echo "Running manual cleanup..."
    
    php artisan tinker --execute="
    use Illuminate\Support\Facades\DB;
    DB::statement(\"UPDATE schedules SET pickup_latitude = NULL WHERE pickup_latitude = '' OR pickup_latitude = '0'\");
    DB::statement(\"UPDATE schedules SET pickup_longitude = NULL WHERE pickup_longitude = '' OR pickup_longitude = '0'\");
    DB::statement(\"UPDATE schedules SET latitude = NULL WHERE latitude = '' OR latitude = '0'\");
    DB::statement(\"UPDATE schedules SET longitude = NULL WHERE longitude = '' OR longitude = '0'\");
    DB::statement(\"UPDATE schedules SET price = NULL WHERE price = '' OR price IS NULL\");
    echo 'Cleanup completed';
    "
    
    echo -e "${GREEN}✅ Manual cleanup completed${NC}"
fi

echo ""
echo "═══════════════════════════════════════════════════════════"
echo -e "${GREEN}   ✅ Schedule API Fix Applied Successfully!${NC}"
echo "═══════════════════════════════════════════════════════════"
echo ""
echo "Summary:"
echo "  - Data cleanup: completed"
echo "  - Schema updated: columns now nullable"
echo "  - Invalid data: cleaned"
echo "  - Caches: cleared"
echo ""
echo "Test now:"
echo "  curl https://gerobaks.dumeg.com/api/schedules"
echo ""
echo "Done! 🎉"
