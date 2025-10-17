-- ================================================================
-- SUPER QUICK FIX - Copy All & Paste to phpMyAdmin
-- ================================================================
-- This fixes "Truncated incorrect DECIMAL value" error
-- by using temporary columns approach
-- ================================================================

-- STEP 0: Cleanup any leftover temp columns from previous attempts
ALTER TABLE schedules DROP COLUMN IF EXISTS price_temp;
ALTER TABLE schedules DROP COLUMN IF EXISTS latitude_temp;
ALTER TABLE schedules DROP COLUMN IF EXISTS longitude_temp;
ALTER TABLE schedules DROP COLUMN IF EXISTS pickup_latitude_temp;
ALTER TABLE schedules DROP COLUMN IF EXISTS pickup_longitude_temp;

-- Fix PRICE (most important)
ALTER TABLE schedules ADD COLUMN price_temp DECIMAL(10, 2) NULL;

-- Only copy valid numeric values
UPDATE schedules 
SET price_temp = CAST(price AS DECIMAL(10,2))
WHERE CONCAT('', price) REGEXP '^[0-9]+\\.?[0-9]+$' AND CAST(price AS CHAR) != '0';

ALTER TABLE schedules DROP COLUMN price;
ALTER TABLE schedules CHANGE COLUMN price_temp price DECIMAL(10, 2) NULL;

-- Fix LATITUDE
ALTER TABLE schedules ADD COLUMN latitude_temp DECIMAL(10, 7) NULL;

-- Only copy valid numeric values (allows negative for coordinates)
UPDATE schedules 
SET latitude_temp = CAST(latitude AS DECIMAL(10,7))
WHERE CONCAT('', latitude) REGEXP '^-?[0-9]+\\.?[0-9]+$' AND CAST(latitude AS CHAR) NOT IN ('0', '0.0', '0.00');

ALTER TABLE schedules DROP COLUMN latitude;
ALTER TABLE schedules CHANGE COLUMN latitude_temp latitude DECIMAL(10, 7) NULL;

-- Fix LONGITUDE
ALTER TABLE schedules ADD COLUMN longitude_temp DECIMAL(10, 7) NULL;

-- Only copy valid numeric values (allows negative for coordinates)
UPDATE schedules 
SET longitude_temp = CAST(longitude AS DECIMAL(10,7))
WHERE CONCAT('', longitude) REGEXP '^-?[0-9]+\\.?[0-9]+$' AND CAST(longitude AS CHAR) NOT IN ('0', '0.0', '0.00');

ALTER TABLE schedules DROP COLUMN longitude;
ALTER TABLE schedules CHANGE COLUMN longitude_temp longitude DECIMAL(10, 7) NULL;

-- Fix PICKUP_LATITUDE (if column exists and not nullable)
ALTER TABLE schedules ADD COLUMN pickup_latitude_temp DECIMAL(10, 8) NULL;

-- Only copy valid numeric values (allows negative for coordinates)
UPDATE schedules 
SET pickup_latitude_temp = CAST(pickup_latitude AS DECIMAL(10,8))
WHERE CONCAT('', pickup_latitude) REGEXP '^-?[0-9]+\\.?[0-9]+$' AND CAST(pickup_latitude AS CHAR) NOT IN ('0', '0.0', '0.00');

ALTER TABLE schedules DROP COLUMN pickup_latitude;
ALTER TABLE schedules CHANGE COLUMN pickup_latitude_temp pickup_latitude DECIMAL(10, 8) NULL;

-- Fix PICKUP_LONGITUDE (if column exists and not nullable)
ALTER TABLE schedules ADD COLUMN pickup_longitude_temp DECIMAL(11, 8) NULL;

-- Only copy valid numeric values (allows negative for coordinates)
UPDATE schedules 
SET pickup_longitude_temp = CAST(pickup_longitude AS DECIMAL(11,8))
WHERE CONCAT('', pickup_longitude) REGEXP '^-?[0-9]+\\.?[0-9]+$' AND CAST(pickup_longitude AS CHAR) NOT IN ('0', '0.0', '0.00');

ALTER TABLE schedules DROP COLUMN pickup_longitude;
ALTER TABLE schedules CHANGE COLUMN pickup_longitude_temp pickup_longitude DECIMAL(11, 8) NULL;

-- Verify the fix
SHOW COLUMNS FROM schedules WHERE Field IN ('price', 'latitude', 'longitude', 'pickup_latitude', 'pickup_longitude');

-- Check data
SELECT COUNT(*) as total_records, COUNT(price) as has_price, COUNT(latitude) as has_latitude, COUNT(longitude) as has_longitude FROM schedules;

-- ================================================================
-- Expected: All columns should show Null = YES
-- ================================================================
-- After this, clear Laravel cache and test API!
-- ================================================================
