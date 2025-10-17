# 🛠️ FIX TRACKING API - DECIMAL CASTING ERROR

## 📋 Problem Summary

**Table:** `trackings`  
**Issue:** Column type mismatch causing potential casting errors

### Current State (WRONG):

```sql
latitude  → VARCHAR(32)  ❌
longitude → VARCHAR(32)  ❌
speed     → FLOAT        ⚠️
heading   → FLOAT        ⚠️
```

### Expected State (CORRECT):

```sql
latitude  → DECIMAL(10, 7) NULL  ✅
longitude → DECIMAL(10, 7) NULL  ✅
speed     → DECIMAL(8, 2) NULL   ✅
heading   → DECIMAL(5, 2) NULL   ✅
```

---

## 🔥 Why This is Critical

### 1. **Type Safety**

-   VARCHAR can store any string: `''`, `'abc'`, `'N/A'`
-   DECIMAL enforces numeric values only
-   Prevents data corruption

### 2. **Performance**

-   Geo queries (distance calculation) need DECIMAL/FLOAT
-   VARCHAR requires casting on every query (slow!)
-   DECIMAL has native geo-spatial index support

### 3. **Laravel Casting**

```php
// Model casts to 'decimal:7'
'latitude' => 'decimal:7',

// If database has empty string '' → MathException!
// Same error as schedules table
```

---

## 🎯 Root Cause

**Migration defines DECIMAL:**

```php
$table->decimal('latitude', 10, 7);
```

**But database shows VARCHAR:**

-   Migration might not have run
-   Or someone altered table manually
-   Or old migration with wrong type

---

## ✅ Solution Options

### Option 1: Via phpMyAdmin (Recommended for Production)

1. Open phpMyAdmin
2. Select database `gerobaksapp_db`
3. Go to SQL tab
4. Copy & paste from: `fix-trackings-phpmyadmin.sql`
5. Execute

**What it does:**

-   ✅ Cleanup invalid data first
-   ✅ Use temporary columns (no truncation error)
-   ✅ CONCAT() trick to avoid auto-casting
-   ✅ REGEXP validation for valid numbers
-   ✅ Safe for production (no downtime)

### Option 2: Via Laravel Migration (Recommended for Development)

```bash
php artisan migrate --path=database/migrations/2025_01_14_000003_fix_trackings_decimal_fields.php
```

**Requires SSH access**

### Option 3: Via Terminal (Fastest)

```bash
cd backend
php artisan migrate --force
```

---

## 📝 Files Changed

### 1. Migration: `2025_01_14_000003_fix_trackings_decimal_fields.php`

```php
// Cleanup invalid data
DB::statement("DELETE FROM trackings WHERE latitude IS NULL OR latitude = ''");

// Change type VARCHAR → DECIMAL
Schema::table('trackings', function (Blueprint $table) {
    $table->decimal('latitude', 10, 7)->nullable()->change();
    $table->decimal('longitude', 10, 7)->nullable()->change();
    $table->decimal('speed', 8, 2)->nullable()->change();
    $table->decimal('heading', 5, 2)->nullable()->change();
});
```

### 2. Model: `app/Models/Tracking.php`

```php
protected $casts = [
    'latitude' => 'decimal:7',   // Changed from 'float'
    'longitude' => 'decimal:7',  // Changed from 'float'
    'speed' => 'decimal:2',      // Changed from 'float'
    'heading' => 'decimal:2',    // Changed from 'float'
    'recorded_at' => 'datetime',
];
```

### 3. Resource: `app/Http/Resources/TrackingResource.php`

```php
private function safeDecimal($value, int $precision = 7): ?float
{
    if ($value === null || $value === '' || $value === '0' || $value === 0) {
        return null;
    }

    return is_numeric($value) ? round((float) $value, $precision) : null;
}

public function toArray(Request $request): array
{
    return [
        'latitude' => $this->safeDecimal($this->latitude, 7),
        'longitude' => $this->safeDecimal($this->longitude, 7),
        'speed' => $this->safeDecimal($this->speed, 2),
        'heading' => $this->safeDecimal($this->heading, 2),
        // ...
    ];
}
```

---

## ⚡ Quick Fix Steps (Production)

### Step 1: Execute SQL Fix

```bash
# Copy fix-trackings-phpmyadmin.sql
# Paste to phpMyAdmin SQL tab
# Execute
```

### Step 2: Update Code

```bash
# Pull latest code
git pull origin fk0u/staging

# Or manually update:
# - app/Models/Tracking.php
# - app/Http/Resources/TrackingResource.php
```

### Step 3: Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Step 4: Test API

```bash
curl https://gerobaks.dumeg.com/api/tracking
```

**Expected:** No more casting errors, clean decimal values

---

## 🔍 Verification

### Check Database Structure:

```sql
SHOW COLUMNS FROM trackings WHERE Field IN ('latitude', 'longitude', 'speed', 'heading');
```

**Expected Output:**

```
+------------+---------------+------+-----+---------+-------+
| Field      | Type          | Null | Key | Default | Extra |
+------------+---------------+------+-----+---------+-------+
| latitude   | decimal(10,7) | YES  |     | NULL    |       |
| longitude  | decimal(10,7) | YES  |     | NULL    |       |
| speed      | decimal(8,2)  | YES  |     | NULL    |       |
| heading    | decimal(5,2)  | YES  |     | NULL    |       |
+------------+---------------+------+-----+---------+-------+
```

### Check Data Quality:

```sql
SELECT
    COUNT(*) as total_records,
    COUNT(latitude) as has_latitude,
    COUNT(longitude) as has_longitude,
    MIN(latitude) as min_lat,
    MAX(latitude) as max_lat,
    MIN(longitude) as min_lng,
    MAX(longitude) as max_lng
FROM trackings;
```

### Test API Response:

```bash
curl https://gerobaks.dumeg.com/api/tracking?limit=5 | jq
```

**Expected:**

```json
{
    "data": [
        {
            "id": 1,
            "schedule_id": "abc123",
            "latitude": -6.2088,
            "longitude": 106.8456,
            "speed": 45.5,
            "heading": 180.0,
            "recorded_at": "2025-01-14T10:30:00Z"
        }
    ]
}
```

---

## 🚨 Common Issues

### Issue 1: "Truncated incorrect DECIMAL value"

**Solution:** Use the phpMyAdmin SQL fix (already handles this with CONCAT trick)

### Issue 2: "Column not found: latitude"

**Solution:** Migration might not have run. Check migration table:

```sql
SELECT * FROM migrations WHERE migration LIKE '%trackings%';
```

### Issue 3: "Unable to cast value to decimal"

**Solution:**

1. Run SQL fix first (cleans invalid data)
2. Update Model casts
3. Update Resource with safeDecimal()

---

## 📊 Impact Analysis

### Before Fix:

-   ❌ Potential casting errors with empty strings
-   ❌ Poor geo-query performance (VARCHAR)
-   ❌ No data validation at database level
-   ❌ Risk of corrupt data (`'abc'`, `'N/A'`)

### After Fix:

-   ✅ Type-safe DECIMAL columns
-   ✅ Fast geo-queries with proper indexing
-   ✅ Database-level validation
-   ✅ Clean, consistent data
-   ✅ No casting errors

---

## 🎯 Related Issues

This fix is related to:

-   **schedules table** decimal casting error (same root cause)
-   **coordinates validation** in TrackingController
-   **geo-queries** for distance calculation
-   **GPS accuracy** requirements

---

## 📚 References

-   Laravel Decimal Casting: https://laravel.com/docs/eloquent-mutators#custom-casts
-   MySQL DECIMAL Type: https://dev.mysql.com/doc/refman/8.0/en/fixed-point-types.html
-   GPS Coordinate Precision: https://en.wikipedia.org/wiki/Decimal_degrees

---

**Status:** ✅ Ready to deploy  
**Priority:** HIGH (prevents API errors)  
**Tested:** ✅ Migration tested locally  
**Production Safe:** ✅ Uses temp columns, no downtime
