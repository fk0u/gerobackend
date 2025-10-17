# ================================================================

# GENERATE FAKE DATA - Quick Commands

# ================================================================

## Method 1: Via Laravel Seeder (SSH Required)

## ------------------------------------------------

# Generate TRACKING data (225 points across 3 routes)

php artisan db:seed --class=QuickTrackingSeeder

# Generate SCHEDULE data (10 schedules)

php artisan db:seed --class=QuickScheduleSeeder

# Generate BOTH

php artisan db:seed --class=QuickTrackingSeeder
php artisan db:seed --class=QuickScheduleSeeder

## Method 2: Via phpMyAdmin SQL (No SSH Needed!)

## ------------------------------------------------

# 1. Open phpMyAdmin

# 2. Select database: gerobaksapp_db

# 3. Go to SQL tab

# 4. Paste content from: insert-fake-tracking-data.sql

# 5. Execute

# This will insert:

# - 70 tracking points (3 routes in Jakarta)

# - Route 1: 30 points (North Jakarta)

# - Route 2: 20 points (South Jakarta)

# - Route 3: 20 points (East Jakarta)

## Method 3: Via PowerShell (Remote SQL)

## ------------------------------------------------

# If you have MySQL client installed:

mysql -h your-host -u your-user -p gerobaksapp_db < insert-fake-tracking-data.sql

## ================================================================

## After Inserting Data, TEST:

## ================================================================

# Test Tracking API

curl "https://gerobaks.dumeg.com/api/tracking?limit=10"

# Test with schedule filter

curl "https://gerobaks.dumeg.com/api/tracking?schedule_id=1&limit=20"

# Get tracking history

curl "https://gerobaks.dumeg.com/api/tracking/schedule/1"

# PowerShell version

Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/tracking?limit=10"

## ================================================================

## Verify Data in Database

## ================================================================

# Check count

SELECT COUNT(\*) FROM trackings;

# Check by schedule

SELECT schedule_id, COUNT(\*) as points FROM trackings GROUP BY schedule_id;

# Check latest points

SELECT \* FROM trackings ORDER BY recorded_at DESC LIMIT 10;

# Check data quality

SELECT
COUNT(\*) as total,
COUNT(latitude) as has_lat,
COUNT(longitude) as has_lng,
MIN(latitude) as min_lat,
MAX(latitude) as max_lat,
MIN(longitude) as min_lng,
MAX(longitude) as max_lng
FROM trackings;

## ================================================================

## Clean Up (Delete All Fake Data)

## ================================================================

# Delete all tracking data

DELETE FROM trackings WHERE schedule_id IN (1, 2, 3);

# Or truncate (reset auto-increment)

TRUNCATE TABLE trackings;

## ================================================================

## Data Summary

## ================================================================

QuickTrackingSeeder generates:

-   Route 1 (schedule_id=1): 50 points, North Jakarta
-   Route 2 (schedule_id=2): 75 points, South Jakarta
-   Route 3 (schedule_id=3): 100 points, East Jakarta
-   Total: 225 realistic GPS tracking points
-   Time span: Last 2 hours
-   Speed: 0-80 km/h (realistic vehicle speeds)
-   Heading: 0-360° (compass direction)
-   Coordinates: Jakarta area (-6.2088, 106.8456)

SQL insert (phpMyAdmin) generates:

-   Route 1: 30 points
-   Route 2: 20 points
-   Route 3: 20 points
-   Total: 70 points
-   Easier to run (no SSH needed)

## ================================================================

## Recommended: Use SQL Insert for Production

## ================================================================

Safer because:

-   ✅ No SSH access needed
-   ✅ Can review SQL before executing
-   ✅ Smaller dataset (70 vs 225 points)
-   ✅ Can run incrementally
-   ✅ Easy to rollback (just DELETE)

File: insert-fake-tracking-data.sql
