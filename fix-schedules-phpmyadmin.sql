-- ================================================================
-- Schedule Decimal Fields Fix for phpMyAdmin
-- ================================================================
-- Purpose: Fix "Unable to cast value to decimal" error
-- Issue: Decimal fields contain NULL/0 values causing casting errors
-- ================================================================

-- STEP 1: Make columns nullable first (IMPORTANT!)
-- This must be done BEFORE cleaning data to avoid truncation errors

ALTER TABLE schedules 
MODIFY COLUMN pickup_latitude DECIMAL(10, 8) NULL;

ALTER TABLE schedules 
MODIFY COLUMN pickup_longitude DECIMAL(11, 8) NULL;

ALTER TABLE schedules 
MODIFY COLUMN latitude DECIMAL(10, 7) NULL;

ALTER TABLE schedules 
MODIFY COLUMN longitude DECIMAL(10, 7) NULL;

ALTER TABLE schedules 
MODIFY COLUMN price DECIMAL(10, 2) NULL;

-- ================================================================
-- STEP 2: Use SAFE ALTER with temporary columns
-- ================================================================
-- This approach avoids truncation errors by:
-- 1. Create new nullable column
-- 2. Copy valid data
-- 3. Drop old column
-- 4. Rename new column

-- For PRICE column
ALTER TABLE schedules ADD COLUMN price_temp DECIMAL(10, 2) NULL;
UPDATE schedules SET price_temp = CASE 
    WHEN price IS NOT NULL AND price != 0 THEN price 
    ELSE NULL 
END;
ALTER TABLE schedules DROP COLUMN price;
ALTER TABLE schedules CHANGE COLUMN price_temp price DECIMAL(10, 2) NULL;

-- For LATITUDE column
ALTER TABLE schedules ADD COLUMN latitude_temp DECIMAL(10, 7) NULL;
UPDATE schedules SET latitude_temp = CASE 
    WHEN latitude IS NOT NULL AND latitude != 0 THEN latitude 
    ELSE NULL 
END;
ALTER TABLE schedules DROP COLUMN latitude;
ALTER TABLE schedules CHANGE COLUMN latitude_temp latitude DECIMAL(10, 7) NULL;

-- For LONGITUDE column
ALTER TABLE schedules ADD COLUMN longitude_temp DECIMAL(10, 7) NULL;
UPDATE schedules SET longitude_temp = CASE 
    WHEN longitude IS NOT NULL AND longitude != 0 THEN longitude 
    ELSE NULL 
END;
ALTER TABLE schedules DROP COLUMN longitude;
ALTER TABLE schedules CHANGE COLUMN longitude_temp longitude DECIMAL(10, 7) NULL;

-- For PICKUP_LATITUDE column (if exists and not nullable)
ALTER TABLE schedules ADD COLUMN pickup_latitude_temp DECIMAL(10, 8) NULL;
UPDATE schedules SET pickup_latitude_temp = CASE 
    WHEN pickup_latitude IS NOT NULL AND pickup_latitude != 0 THEN pickup_latitude 
    ELSE NULL 
END;
ALTER TABLE schedules DROP COLUMN pickup_latitude;
ALTER TABLE schedules CHANGE COLUMN pickup_latitude_temp pickup_latitude DECIMAL(10, 8) NULL;

-- For PICKUP_LONGITUDE column (if exists and not nullable)
ALTER TABLE schedules ADD COLUMN pickup_longitude_temp DECIMAL(11, 8) NULL;
UPDATE schedules SET pickup_longitude_temp = CASE 
    WHEN pickup_longitude IS NOT NULL AND pickup_longitude != 0 THEN pickup_longitude 
    ELSE NULL 
END;
ALTER TABLE schedules DROP COLUMN pickup_longitude;
ALTER TABLE schedules CHANGE COLUMN pickup_longitude_temp pickup_longitude DECIMAL(11, 8) NULL;

-- ================================================================
-- STEP 3: Verify the changes
-- ================================================================

-- Check column definitions
SHOW COLUMNS FROM schedules 
WHERE Field IN ('pickup_latitude', 'pickup_longitude', 'latitude', 'longitude', 'price');

-- Expected: All should show NULL = YES

-- Check for remaining invalid data
SELECT 
    COUNT(*) as total_records,
    COUNT(pickup_latitude) as has_pickup_lat,
    COUNT(pickup_longitude) as has_pickup_lng,
    COUNT(latitude) as has_legacy_lat,
    COUNT(longitude) as has_legacy_lng,
    COUNT(price) as has_price
FROM schedules;

-- Check specific records with NULL values
SELECT id, pickup_latitude, pickup_longitude, latitude, longitude, price 
FROM schedules 
WHERE pickup_latitude IS NULL 
   OR pickup_longitude IS NULL 
   OR latitude IS NULL 
   OR longitude IS NULL
LIMIT 10;

-- ================================================================
-- STEP 4: Record migration (if not using Laravel migrate command)
-- ================================================================

-- Get current max batch
SELECT MAX(batch) as current_batch FROM migrations;

-- Insert migration record (replace <MAX_BATCH + 1> with actual number from above + 1)
INSERT INTO migrations (migration, batch) 
VALUES ('2025_01_14_000002_fix_schedules_decimal_fields', <MAX_BATCH + 1>);

-- ================================================================
-- Expected Results:
-- ================================================================
-- After running these queries:
-- 1. All decimal columns should be nullable
-- 2. All 0 values converted to NULL
-- 3. No "Unable to cast value to decimal" errors
-- 4. API endpoint /api/schedules works without errors
-- ================================================================

-- Test the API after running:
-- curl https://gerobaks.dumeg.com/api/schedules
-- Expected: JSON response with schedules (no 500 error)
