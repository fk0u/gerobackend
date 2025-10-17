-- ================================================================
-- FIX TRACKINGS TABLE - phpMyAdmin Safe Version
-- ================================================================
-- Problem: latitude & longitude are VARCHAR(32) instead of DECIMAL
-- This causes casting errors and poor geo-query performance
-- ================================================================

-- STEP 0: Cleanup any leftover temp columns
ALTER TABLE trackings DROP COLUMN IF EXISTS latitude_temp;
ALTER TABLE trackings DROP COLUMN IF EXISTS longitude_temp;
ALTER TABLE trackings DROP COLUMN IF EXISTS speed_temp;
ALTER TABLE trackings DROP COLUMN IF EXISTS heading_temp;

-- STEP 1: Delete obviously invalid records first
DELETE FROM trackings WHERE latitude IS NULL OR latitude = '' OR longitude IS NULL OR longitude = '';
DELETE FROM trackings WHERE latitude = '0' AND longitude = '0';

-- STEP 2: Fix LATITUDE (VARCHAR → DECIMAL)
ALTER TABLE trackings ADD COLUMN latitude_temp DECIMAL(10, 7) NULL;

UPDATE trackings 
SET latitude_temp = CAST(latitude AS DECIMAL(10,7))
WHERE CONCAT('', latitude) REGEXP '^-?[0-9]+\\.?[0-9]+$' 
  AND CAST(latitude AS CHAR) NOT IN ('0', '0.0', '0.00');

ALTER TABLE trackings DROP COLUMN latitude;
ALTER TABLE trackings CHANGE COLUMN latitude_temp latitude DECIMAL(10, 7) NULL;

-- STEP 3: Fix LONGITUDE (VARCHAR → DECIMAL)
ALTER TABLE trackings ADD COLUMN longitude_temp DECIMAL(10, 7) NULL;

UPDATE trackings 
SET longitude_temp = CAST(longitude AS DECIMAL(10,7))
WHERE CONCAT('', longitude) REGEXP '^-?[0-9]+\\.?[0-9]+$' 
  AND CAST(longitude AS CHAR) NOT IN ('0', '0.0', '0.00');

ALTER TABLE trackings DROP COLUMN longitude;
ALTER TABLE trackings CHANGE COLUMN longitude_temp longitude DECIMAL(10, 7) NULL;

-- STEP 4: Fix SPEED (FLOAT → DECIMAL for consistency)
ALTER TABLE trackings ADD COLUMN speed_temp DECIMAL(8, 2) NULL;

UPDATE trackings 
SET speed_temp = CAST(speed AS DECIMAL(8,2))
WHERE CONCAT('', speed) REGEXP '^[0-9]+\\.?[0-9]*$';

ALTER TABLE trackings DROP COLUMN speed;
ALTER TABLE trackings CHANGE COLUMN speed_temp speed DECIMAL(8, 2) NULL;

-- STEP 5: Fix HEADING (FLOAT → DECIMAL for consistency)
ALTER TABLE trackings ADD COLUMN heading_temp DECIMAL(5, 2) NULL;

UPDATE trackings 
SET heading_temp = CAST(heading AS DECIMAL(5,2))
WHERE CONCAT('', heading) REGEXP '^[0-9]+\\.?[0-9]*$';

ALTER TABLE trackings DROP COLUMN heading;
ALTER TABLE trackings CHANGE COLUMN heading_temp heading DECIMAL(5, 2) NULL;

-- STEP 6: Verify the fix
SHOW COLUMNS FROM trackings WHERE Field IN ('latitude', 'longitude', 'speed', 'heading');

-- STEP 7: Check remaining data
SELECT COUNT(*) as total_records, 
       COUNT(latitude) as has_latitude, 
       COUNT(longitude) as has_longitude,
       COUNT(speed) as has_speed,
       COUNT(heading) as has_heading
FROM trackings;

-- ================================================================
-- Expected Results:
-- - latitude:  DECIMAL(10,7), Null = YES
-- - longitude: DECIMAL(10,7), Null = YES
-- - speed:     DECIMAL(8,2),  Null = YES
-- - heading:   DECIMAL(5,2),  Null = YES
-- ================================================================
