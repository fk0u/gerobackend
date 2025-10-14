#!/bin/bash

# Session Payload Fix Script for Production
# This script fixes the "Data too long for column 'payload'" error

set -e  # Exit on error

echo "═══════════════════════════════════════════════════════════"
echo "   🔧 Session Payload Column Fix for Production"
echo "═══════════════════════════════════════════════════════════"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running in correct directory
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Error: artisan file not found!${NC}"
    echo "Please run this script from the Laravel backend directory."
    exit 1
fi

echo "Step 1: Checking database connection..."
if php artisan db:show > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Database connection OK${NC}"
else
    echo -e "${RED}❌ Cannot connect to database!${NC}"
    echo "Please check your .env database settings."
    exit 1
fi

echo ""
echo "Step 2: Checking current sessions table structure..."
CURRENT_TYPE=$(php artisan tinker --execute="
use Illuminate\Support\Facades\DB;
\$column = DB::select(\"SHOW COLUMNS FROM sessions LIKE 'payload'\")[0] ?? null;
echo \$column ? \$column->Type : 'unknown';
")

echo "Current payload column type: $CURRENT_TYPE"

if [[ "$CURRENT_TYPE" == *"longtext"* ]]; then
    echo -e "${GREEN}✅ Column is already LONGTEXT! No fix needed.${NC}"
    exit 0
fi

echo ""
echo "Step 3: Backing up sessions table..."
BACKUP_FILE="sessions_backup_$(date +%Y%m%d_%H%M%S).sql"
if php artisan tinker --execute="
use Illuminate\Support\Facades\DB;
DB::statement('CREATE TABLE sessions_backup_$(date +%Y%m%d_%H%M%S) LIKE sessions');
DB::statement('INSERT INTO sessions_backup_$(date +%Y%m%d_%H%M%S) SELECT * FROM sessions');
echo 'Backup created';
" > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Backup created${NC}"
else
    echo -e "${YELLOW}⚠️  Backup failed, continuing anyway...${NC}"
fi

echo ""
echo "Step 4: Running migration..."
if php artisan migrate --force --path=database/migrations/2025_01_14_000001_fix_sessions_payload_column.php; then
    echo -e "${GREEN}✅ Migration applied successfully!${NC}"
else
    echo -e "${RED}❌ Migration failed!${NC}"
    echo "Trying manual SQL fix..."
    
    # Try manual SQL as fallback
    if php artisan tinker --execute="
    use Illuminate\Support\Facades\DB;
    DB::statement('ALTER TABLE sessions MODIFY COLUMN payload LONGTEXT NOT NULL');
    echo 'Manual fix applied';
    "; then
        echo -e "${GREEN}✅ Manual SQL fix applied!${NC}"
        
        # Record migration manually
        php artisan tinker --execute="
        use Illuminate\Support\Facades\DB;
        \$batch = DB::table('migrations')->max('batch') + 1;
        DB::table('migrations')->insert([
            'migration' => '2025_01_14_000001_fix_sessions_payload_column',
            'batch' => \$batch
        ]);
        echo 'Migration recorded';
        "
    else
        echo -e "${RED}❌ Both migration and manual fix failed!${NC}"
        exit 1
    fi
fi

echo ""
echo "Step 5: Verifying fix..."
NEW_TYPE=$(php artisan tinker --execute="
use Illuminate\Support\Facades\DB;
\$column = DB::select(\"SHOW COLUMNS FROM sessions LIKE 'payload'\")[0] ?? null;
echo \$column ? \$column->Type : 'unknown';
")

echo "New payload column type: $NEW_TYPE"

if [[ "$NEW_TYPE" == *"longtext"* ]]; then
    echo -e "${GREEN}✅ Fix verified successfully!${NC}"
else
    echo -e "${RED}❌ Fix verification failed!${NC}"
    echo "Column type is still: $NEW_TYPE"
    exit 1
fi

echo ""
echo "Step 6: Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✅ Caches cleared${NC}"

echo ""
echo "Step 7: Flushing old sessions..."
php artisan session:flush
echo -e "${GREEN}✅ Old sessions cleared${NC}"

echo ""
echo "═══════════════════════════════════════════════════════════"
echo -e "${GREEN}   ✅ Session Payload Fix Applied Successfully!${NC}"
echo "═══════════════════════════════════════════════════════════"
echo ""
echo "Summary:"
echo "  - Column type changed: $CURRENT_TYPE → longtext"
echo "  - Old sessions cleared"
echo "  - Caches cleared"
echo "  - System ready for use"
echo ""
echo "Next steps:"
echo "  1. Test: curl -I https://gerobaks.dumeg.com/openapi.yaml"
echo "  2. Monitor: tail -f storage/logs/laravel.log"
echo "  3. Check: No more 'Data too long' errors"
echo ""
echo "Done! 🎉"
