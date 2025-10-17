# 🔥 QUICK FIX - Schedule API Error (phpMyAdmin)

## Problem

```
Error: Unable to cast value to a decimal
Endpoint: GET /api/schedules
```

## Solution (3 Steps via phpMyAdmin)

### ⚠️ IMPORTANT: Follow order exactly!

---

## STEP 1: Fix PRICE Column (Most Important)

**Use temporary column to avoid truncation:**

```sql
ALTER TABLE schedules ADD COLUMN price_temp DECIMAL(10, 2) NULL;
UPDATE schedules SET price_temp = CASE WHEN price IS NOT NULL AND price != 0 THEN price ELSE NULL END;
ALTER TABLE schedules DROP COLUMN price;
ALTER TABLE schedules CHANGE COLUMN price_temp price DECIMAL(10, 2) NULL;
```

**✅ Expected:** Query OK, columns updated

---

## STEP 2: Fix LATITUDE Column

```sql
ALTER TABLE schedules ADD COLUMN latitude_temp DECIMAL(10, 7) NULL;
UPDATE schedules SET latitude_temp = CASE WHEN latitude IS NOT NULL AND latitude != 0 THEN latitude ELSE NULL END;
ALTER TABLE schedules DROP COLUMN latitude;
ALTER TABLE schedules CHANGE COLUMN latitude_temp latitude DECIMAL(10, 7) NULL;
```

**✅ Expected:** Query OK, columns updated

---

## STEP 3: Fix LONGITUDE Column

```sql
ALTER TABLE schedules ADD COLUMN longitude_temp DECIMAL(10, 7) NULL;
UPDATE schedules SET longitude_temp = CASE WHEN longitude IS NOT NULL AND longitude != 0 THEN longitude ELSE NULL END;
ALTER TABLE schedules DROP COLUMN longitude;
ALTER TABLE schedules CHANGE COLUMN longitude_temp longitude DECIMAL(10, 7) NULL;
```

**✅ Expected:** Query OK, columns updated

---

## STEP 4: Fix PICKUP Columns (If They Exist)

**Skip if these columns are already nullable!**

```sql
-- pickup_latitude
ALTER TABLE schedules ADD COLUMN pickup_latitude_temp DECIMAL(10, 8) NULL;
UPDATE schedules SET pickup_latitude_temp = CASE WHEN pickup_latitude IS NOT NULL AND pickup_latitude != 0 THEN pickup_latitude ELSE NULL END;
ALTER TABLE schedules DROP COLUMN pickup_latitude;
ALTER TABLE schedules CHANGE COLUMN pickup_latitude_temp pickup_latitude DECIMAL(10, 8) NULL;

-- pickup_longitude
ALTER TABLE schedules ADD COLUMN pickup_longitude_temp DECIMAL(11, 8) NULL;
UPDATE schedules SET pickup_longitude_temp = CASE WHEN pickup_longitude IS NOT NULL AND pickup_longitude != 0 THEN pickup_longitude ELSE NULL END;
ALTER TABLE schedules DROP COLUMN pickup_longitude;
ALTER TABLE schedules CHANGE COLUMN pickup_longitude_temp pickup_longitude DECIMAL(11, 8) NULL;
```

**✅ Expected:** Query OK for each column

---

## STEP 5: Verify Fix

**Run this query:**

```sql
SHOW COLUMNS FROM schedules WHERE Field IN ('latitude', 'longitude', 'price', 'pickup_latitude', 'pickup_longitude');
```

**✅ Expected Result:**

| Field            | Type          | Null    |
| ---------------- | ------------- | ------- |
| latitude         | decimal(10,7) | **YES** |
| longitude        | decimal(10,7) | **YES** |
| price            | decimal(10,2) | **YES** |
| pickup_latitude  | decimal(10,8) | **YES** |
| pickup_longitude | decimal(11,8) | **YES** |

**All should show Null = YES**

**Check data:**

```sql
SELECT COUNT(*) as total,
       COUNT(price) as has_price,
       COUNT(latitude) as has_lat,
       COUNT(longitude) as has_lng
FROM schedules;
```

---

## STEP 6: Clear Laravel Cache

**Via SSH or Terminal:**

```bash
cd public_html/gerobaks.dumeg.com
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

**Or via cPanel Terminal (if SSH not available)**

---

## STEP 7: Test API

```bash
curl https://gerobaks.dumeg.com/api/schedules
```

**✅ Expected:** JSON response with schedules (no 500 error)

---

## Record Migration (Optional)

If you want to mark this as "migrated" in Laravel:

```sql
-- Get current batch number
SELECT MAX(batch) as current_batch FROM migrations;

-- Insert migration record (replace 2 with MAX_BATCH + 1)
INSERT INTO migrations (migration, batch)
VALUES ('2025_01_14_000002_fix_schedules_decimal_fields', 2);
```

---

## Troubleshooting

### Error: "Truncated incorrect DECIMAL value"

**Cause:** Trying to clean data before making columns nullable

**Fix:** Make sure you run STEP 1 completely before STEP 2!

### Error: "Column already modified"

**Cause:** You already ran STEP 1

**Fix:** Skip STEP 1, go directly to STEP 2

### Still getting API error?

**Fix:** Clear Laravel caches (STEP 4)

---

## Success Checklist

-   [ ] All 5 ALTER TABLE queries successful
-   [ ] All 5 UPDATE queries successful
-   [ ] SHOW COLUMNS shows NULL = YES
-   [ ] Laravel caches cleared
-   [ ] API returns 200 OK
-   [ ] No errors in logs

---

**Total Time:** 3-5 minutes  
**Risk:** Low (safe operations)  
**Rollback:** Not needed (safe changes)

**Done? Test the API! 🚀**
