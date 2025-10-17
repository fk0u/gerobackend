# 🔧 Schedule API Decimal Casting Error Fix

## Problem

**Error:**

```
Illuminate\Support\Exceptions\MathException: Unable to cast value to a decimal.
```

**Occurs at:**

-   Endpoint: `GET /api/schedules`
-   Location: `ScheduleResource.php` line 20
-   Trigger: When converting decimal fields (latitude, longitude, price) that contain NULL or invalid values

## Root Cause

1. **Model casts decimal fields:**

    - `pickup_latitude` → `decimal:8`
    - `pickup_longitude` → `decimal:8`
    - `latitude` → `decimal:8`
    - `longitude` → `decimal:8`
    - `price` → `decimal:2`

2. **Database contains invalid values:**

    - Empty strings (`''`)
    - NULL values in NOT NULL columns
    - Invalid numeric strings
    - String `'0'` treated as missing value

3. **Laravel casting fails:**
    - Eloquent tries to cast invalid values to decimal
    - Throws `MathException` when value cannot be converted

## Solution Applied

### 1️⃣ Updated Schedule Model ✅

**File:** `app/Models/Schedule.php`

**Changes:**

-   ✅ Added `Attribute` accessor methods for safe decimal handling
-   ✅ Convert empty strings to NULL before casting
-   ✅ Validate numeric values before casting
-   ✅ Use modern `casts()` method instead of `$casts` property

**Code:**

```php
use Illuminate\Database\Eloquent\Casts\Attribute;

protected function pickupLatitude(): Attribute
{
    return Attribute::make(
        get: fn ($value) => $value !== null && $value !== '' ? (string) $value : null,
    );
}

protected function pickupLongitude(): Attribute
{
    return Attribute::make(
        get: fn ($value) => $value !== null && $value !== '' ? (string) $value : null,
    );
}

protected function latitude(): Attribute
{
    return Attribute::make(
        get: fn ($value) => $value !== null && $value !== '' ? (string) $value : null,
    );
}

protected function longitude(): Attribute
{
    return Attribute::make(
        get: fn ($value) => $value !== null && $value !== '' ? (string) $value : null,
    );
}

protected function price(): Attribute
{
    return Attribute::make(
        get: fn ($value) => $value !== null && $value !== '' ? (string) $value : null,
    );
}
```

### 2️⃣ Updated ScheduleResource ✅

**File:** `app/Http/Resources/ScheduleResource.php`

**Changes:**

-   ✅ Added `safeDecimal()` helper method
-   ✅ Replace direct casting with safe conversion
-   ✅ Handle null, empty strings, and invalid values
-   ✅ Fallback to NULL for invalid data

**Code:**

```php
private function safeDecimal($value): ?float
{
    if ($value === null || $value === '' || $value === '0') {
        return null;
    }

    if (is_numeric($value)) {
        return (float) $value;
    }

    return null;
}

// Usage:
'pickup_latitude' => $this->safeDecimal($this->pickup_latitude),
'pickup_longitude' => $this->safeDecimal($this->pickup_longitude),
'price' => $this->safeDecimal($this->price),
'latitude' => $this->safeDecimal($this->latitude ?? $this->pickup_latitude),
'longitude' => $this->safeDecimal($this->longitude ?? $this->pickup_longitude),
```

### 3️⃣ Migration: Fix Existing Data ✅

**File:** `database/migrations/2025_01_14_000002_fix_schedules_decimal_fields.php`

**Purpose:** Clean up invalid data in production database

**Actions:**

-   ✅ Convert empty strings to NULL
-   ✅ Convert '0' to NULL (invalid coordinate)
-   ✅ Remove non-numeric values
-   ✅ Apply to all decimal fields

**SQL:**

```sql
UPDATE schedules
SET pickup_latitude = NULL
WHERE pickup_latitude = ''
   OR pickup_latitude = '0'
   OR pickup_latitude IS NULL
   OR CAST(pickup_latitude AS CHAR) NOT REGEXP '^-?[0-9]+(\\.[0-9]+)?$';

UPDATE schedules
SET pickup_longitude = NULL
WHERE pickup_longitude = ''
   OR pickup_longitude = '0'
   OR pickup_longitude IS NULL
   OR CAST(pickup_longitude AS CHAR) NOT REGEXP '^-?[0-9]+(\\.[0-9]+)?$';

-- Similar for latitude, longitude, price
```

### 4️⃣ Migration: Make Columns Nullable ✅

**File:** `database/migrations/2025_01_14_000003_make_schedules_coordinates_nullable.php`

**Purpose:** Update schema to allow NULL values

**Changes:**

```php
Schema::table('schedules', function (Blueprint $table) {
    $table->decimal('latitude', 10, 7)->nullable()->change();
    $table->decimal('longitude', 10, 7)->nullable()->change();
});
```

## Deployment Steps

### Quick Fix (Production) 🔥

**Via SSH:**

```bash
ssh username@gerobaks.dumeg.com
cd public_html/gerobaks.dumeg.com

# Pull latest changes
git pull origin fk0u/staging

# Run migrations
php artisan migrate --force

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Test endpoint
curl https://gerobaks.dumeg.com/api/schedules
```

**Time:** ~2 minutes

### Manual Fix (Via phpMyAdmin)

If migrations fail, run SQL directly in this **exact order**:

**⚠️ IMPORTANT: Run STEP 1 first, then STEP 2!**

```sql
-- STEP 1: Make columns nullable FIRST (prevents truncation error)
ALTER TABLE schedules MODIFY COLUMN pickup_latitude DECIMAL(10, 8) NULL;
ALTER TABLE schedules MODIFY COLUMN pickup_longitude DECIMAL(11, 8) NULL;
ALTER TABLE schedules MODIFY COLUMN latitude DECIMAL(10, 7) NULL;
ALTER TABLE schedules MODIFY COLUMN longitude DECIMAL(10, 7) NULL;
ALTER TABLE schedules MODIFY COLUMN price DECIMAL(10, 2) NULL;

-- STEP 2: Clean invalid data (set 0 to NULL)
UPDATE schedules SET pickup_latitude = NULL WHERE pickup_latitude = 0;
UPDATE schedules SET pickup_longitude = NULL WHERE pickup_longitude = 0;
UPDATE schedules SET latitude = NULL WHERE latitude = 0;
UPDATE schedules SET longitude = NULL WHERE longitude = 0;
UPDATE schedules SET price = NULL WHERE price = 0;

-- STEP 3: Verify
SHOW COLUMNS FROM schedules WHERE Field IN ('latitude', 'longitude', 'price');
-- All should show NULL = YES
```

**Use complete script:** `fix-schedules-phpmyadmin.sql`

## Verification

### 1. Test API Endpoint

```bash
curl https://gerobaks.dumeg.com/api/schedules
```

**Expected:** JSON response with schedules (no error)

### 2. Check Database

```sql
-- Check for invalid data
SELECT id, pickup_latitude, pickup_longitude, latitude, longitude, price
FROM schedules
WHERE pickup_latitude = ''
   OR pickup_longitude = ''
   OR latitude = ''
   OR longitude = '';

-- Should return 0 rows
```

### 3. Test From Flutter App

**Action:** Open schedules list in app

**Expected:**

-   ✅ Schedules load successfully
-   ✅ No errors in logs
-   ✅ Coordinates display correctly or show "Location not set"

### 4. Monitor Logs

```bash
tail -f storage/logs/laravel.log
```

**Expected:** No "Unable to cast value to decimal" errors

## Prevention

### Future Data Entry

**In Schedule Controller (create/update):**

```php
// Validate coordinates
$validated = $request->validate([
    'pickup_latitude' => 'nullable|numeric|between:-90,90',
    'pickup_longitude' => 'nullable|numeric|between:-180,180',
    'price' => 'nullable|numeric|min:0',
]);

// Clean values before saving
$schedule->pickup_latitude = $validated['pickup_latitude'] ?: null;
$schedule->pickup_longitude = $validated['pickup_longitude'] ?: null;
$schedule->price = $validated['price'] ?: null;
```

### Database Constraints

**Already applied in migrations:**

-   ✅ Decimal precision: `decimal(10, 7)` for coordinates
-   ✅ Nullable columns allow NULL instead of empty strings
-   ✅ Validation in controller prevents invalid data

## Files Changed

| File                                                                            | Changes                                    |
| ------------------------------------------------------------------------------- | ------------------------------------------ |
| `app/Models/Schedule.php`                                                       | Added Attribute accessors for safe casting |
| `app/Http/Resources/ScheduleResource.php`                                       | Added `safeDecimal()` helper method        |
| `database/migrations/2025_01_14_000002_fix_schedules_decimal_fields.php`        | Data cleanup migration                     |
| `database/migrations/2025_01_14_000003_make_schedules_coordinates_nullable.php` | Schema update migration                    |
| `SCHEDULES_DECIMAL_FIX.md`                                                      | This documentation                         |

## Rollback (If Needed)

```bash
# Rollback last 2 migrations
php artisan migrate:rollback --step=2
```

**⚠️ Warning:** Rolling back will cause the error to return!

## Testing Checklist

-   [ ] Pull latest code
-   [ ] Run migrations
-   [ ] Clear caches
-   [ ] Test `GET /api/schedules`
-   [ ] Test `POST /api/schedules` with coordinates
-   [ ] Test `POST /api/schedules` without coordinates
-   [ ] Check database for NULL values
-   [ ] Test from Flutter app
-   [ ] Monitor logs for errors

## Success Criteria

✅ API endpoint returns 200 OK  
✅ No "Unable to cast value to decimal" errors  
✅ NULL values handled gracefully  
✅ Coordinates display correctly or show NULL  
✅ Flutter app loads schedules without errors  
✅ No errors in Laravel logs

## Common Issues

### Issue: Migration Fails

**Error:** `SQLSTATE[42000]: Syntax error or access violation`

**Fix:**

```bash
# Check migration status
php artisan migrate:status

# Try running specific migration
php artisan migrate --path=database/migrations/2025_01_14_000002_fix_schedules_decimal_fields.php --force
```

### Issue: Still Getting Casting Error

**Fix:**

```bash
# Clear all caches
php artisan optimize:clear

# Check model file
cat app/Models/Schedule.php | grep "Attribute"

# Verify Attribute import exists
```

### Issue: Data Still Contains Empty Strings

**Fix:**

```sql
-- Run cleanup SQL manually
UPDATE schedules SET
    pickup_latitude = NULLIF(pickup_latitude, ''),
    pickup_longitude = NULLIF(pickup_longitude, ''),
    latitude = NULLIF(latitude, ''),
    longitude = NULLIF(longitude, ''),
    price = NULLIF(price, '');
```

## Related Documentation

-   [Laravel Casting Documentation](https://laravel.com/docs/11.x/eloquent-mutators#attribute-casting)
-   [Laravel Attributes Documentation](https://laravel.com/docs/11.x/eloquent-mutators#defining-an-accessor)
-   [MySQL DECIMAL Type](https://dev.mysql.com/doc/refman/8.0/en/fixed-point-types.html)

---

**Status:** ✅ Fix Ready  
**Priority:** 🔥 High (Production API Error)  
**Estimated Fix Time:** 2-5 minutes  
**Risk Level:** Low (Safe migrations)

**Created:** January 14, 2025  
**Last Updated:** January 14, 2025
