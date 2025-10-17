# 🎭 DATA PALSU UNTUK TESTING - COMPLETE GUIDE

## 🎯 Tujuan

Generate **realistic fake data** untuk testing API tanpa perlu data production real.

---

## 📦 Yang Tersedia

### 1. **QuickTrackingSeeder.php** ⚡

**Laravel Seeder** yang generate 225 tracking points

**Features:**

-   ✅ 3 routes berbeda (North, South, East Jakarta)
-   ✅ 225 GPS tracking points total
-   ✅ Realistic speeds (0-80 km/h)
-   ✅ Realistic headings (0-360°)
-   ✅ Time-series data (last 2 hours)
-   ✅ Jakarta coordinates (-6.2088, 106.8456)

**Requires:** SSH access

---

### 2. **insert-fake-tracking-data.sql** 🗄️

**SQL Script** untuk insert langsung via phpMyAdmin

**Features:**

-   ✅ 70 tracking points (3 routes)
-   ✅ Route 1: 30 points (North Jakarta)
-   ✅ Route 2: 20 points (South Jakarta)
-   ✅ Route 3: 20 points (East Jakarta)
-   ✅ No SSH needed (run in phpMyAdmin)
-   ✅ Easy to review before executing

**Recommended for Production!**

---

### 3. **QuickScheduleSeeder.php** 📅

**Laravel Seeder** untuk generate 10 sample schedules

**Features:**

-   ✅ 10 realistic schedules
-   ✅ Jakarta pickup/dropoff locations
-   ✅ Random prices (50k-200k)
-   ✅ Different statuses (pending, confirmed, etc.)
-   ✅ Realistic timestamps

---

## 🚀 Cara Pakai

### **Option 1: Via phpMyAdmin (RECOMMENDED!)** ⭐

#### Step 1: Copy SQL

```bash
# File: insert-fake-tracking-data.sql
# Already created in backend folder
```

#### Step 2: Execute in phpMyAdmin

1. Open phpMyAdmin
2. Select database: `gerobaksapp_db`
3. Click "SQL" tab
4. Paste content from `insert-fake-tracking-data.sql`
5. Click "Go"

#### Step 3: Verify

```sql
SELECT COUNT(*) FROM trackings;
-- Expected: 70 rows

SELECT schedule_id, COUNT(*) as points
FROM trackings
GROUP BY schedule_id;
-- Expected:
-- schedule_id | points
-- 1           | 30
-- 2           | 20
-- 3           | 20
```

#### Step 4: Test API

```bash
curl "https://gerobaks.dumeg.com/api/tracking?limit=10"
```

**Expected Result:**

```json
{
  "data": [
    {
      "id": 1,
      "schedule_id": 1,
      "latitude": -6.1698,
      "longitude": 106.8746,
      "speed": 37.90,
      "heading": 325.10,
      "recorded_at": "2025-01-14T08:31:00Z"
    },
    ...
  ]
}
```

---

### **Option 2: Via Laravel Seeder (SSH Required)**

#### Step 1: Run Seeder

```bash
cd backend
php artisan db:seed --class=QuickTrackingSeeder
```

**Output:**

```
🚀 Generating fake tracking data...
📍 Generating Route 1: North Jakarta (50 points)...
   ✅ Route 1: North Jakarta: 50 points inserted
📍 Generating Route 2: South Jakarta (75 points)...
   ✅ Route 2: South Jakarta: 75 points inserted
📍 Generating Route 3: East Jakarta (100 points)...
   ✅ Route 3: East Jakarta: 100 points inserted

🎉 DONE! Total: 225 tracking points created

📊 Statistics:
   Total Points: 225
   Schedules: 3
   Latitude Range: -6.2188 to -6.1988
   Longitude Range: 106.8356 to 106.8656
   Speed Range: 0.00 to 80.00 km/h
   Time Range: 2025-01-14 08:00:00 to 2025-01-14 10:00:00

✅ Test API: curl https://gerobaks.dumeg.com/api/tracking?limit=10
```

#### Step 2: Generate Schedules (Optional)

```bash
php artisan db:seed --class=QuickScheduleSeeder
```

---

## 🧪 Testing dengan Fake Data

### Test 1: Get All Tracking

```bash
curl "https://gerobaks.dumeg.com/api/tracking?limit=10"
```

### Test 2: Filter by Schedule

```bash
curl "https://gerobaks.dumeg.com/api/tracking?schedule_id=1&limit=20"
```

### Test 3: Get Tracking History

```bash
curl "https://gerobaks.dumeg.com/api/tracking/schedule/1"
```

### Test 4: PowerShell Version

```powershell
$response = Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/tracking?limit=10"
$response.data | Select-Object id, schedule_id, latitude, longitude, speed | Format-Table
```

### Test 5: Check Decimal Values

```powershell
$response = Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/tracking?limit=5"
$response.data | ForEach-Object {
    Write-Host "ID: $($_.id)"
    Write-Host "  Latitude:  $($_.latitude) (Type: $(($_.latitude).GetType().Name))"
    Write-Host "  Longitude: $($_.longitude) (Type: $(($_.longitude).GetType().Name))"
    Write-Host "  Speed:     $($_.speed) (Type: $(($_.speed).GetType().Name))"
    Write-Host ""
}
```

---

## 🔍 Verify Data Quality

### Check in Database:

```sql
-- Total points
SELECT COUNT(*) as total FROM trackings;

-- By schedule
SELECT
    schedule_id,
    COUNT(*) as points,
    AVG(speed) as avg_speed,
    MIN(recorded_at) as start_time,
    MAX(recorded_at) as end_time
FROM trackings
GROUP BY schedule_id;

-- Coordinate ranges
SELECT
    MIN(latitude) as min_lat,
    MAX(latitude) as max_lat,
    MIN(longitude) as min_lng,
    MAX(longitude) as max_lng
FROM trackings;

-- Check for invalid values
SELECT COUNT(*) FROM trackings WHERE latitude IS NULL OR longitude IS NULL;
-- Expected: 0

SELECT COUNT(*) FROM trackings WHERE latitude = 0 AND longitude = 0;
-- Expected: 0
```

### Check via API:

```bash
# Count records
curl -s "https://gerobaks.dumeg.com/api/tracking?limit=1000" | jq '.data | length'

# Check data structure
curl -s "https://gerobaks.dumeg.com/api/tracking?limit=1" | jq '.data[0]'

# Verify decimal fields
curl -s "https://gerobaks.dumeg.com/api/tracking?limit=5" | jq '.data[] | {id, lat: .latitude, lng: .longitude, speed}'
```

---

## 🧹 Clean Up

### Delete Fake Data:

```sql
-- Delete specific routes
DELETE FROM trackings WHERE schedule_id IN (1, 2, 3);

-- Or truncate entire table
TRUNCATE TABLE trackings;

-- Verify
SELECT COUNT(*) FROM trackings;
-- Expected: 0
```

---

## 📊 Data Details

### Route 1 - North Jakarta

```
Schedule ID: 1
Points: 30 (SQL) / 50 (Seeder)
Start: -6.1988, 106.8456
End: -6.1698, 106.8746
Direction: Northeast
Duration: ~30 minutes
Avg Speed: ~42 km/h
```

### Route 2 - South Jakarta

```
Schedule ID: 2
Points: 20 (SQL) / 75 (Seeder)
Start: -6.2288, 106.8356
End: -6.2098, 106.8546
Direction: Northeast
Duration: ~38 minutes
Avg Speed: ~45 km/h
```

### Route 3 - East Jakarta

```
Schedule ID: 3
Points: 20 (SQL) / 100 (Seeder)
Start: -6.2088, 106.8856
End: -6.1898, 106.8666
Direction: West
Duration: ~38 minutes
Avg Speed: ~44 km/h
```

---

## 🎯 Use Cases

### For API Testing:

✅ Test GET /tracking endpoint  
✅ Test filtering by schedule_id  
✅ Test date range filtering  
✅ Test pagination (limit parameter)  
✅ Verify decimal casting works  
✅ Check response time with data

### For Frontend Development:

✅ Test map rendering with GPS points  
✅ Test route visualization  
✅ Test real-time tracking simulation  
✅ Test speed/heading indicators  
✅ Test timeline/history views

### For Performance Testing:

✅ Test API with 70-225 records  
✅ Test database queries  
✅ Test response times  
✅ Test data serialization

---

## 🚨 Important Notes

### DO NOT use in Production with Real Users!

-   ❌ Coordinates are fake (Jakarta area)
-   ❌ Schedule IDs might not exist
-   ❌ Timestamps are generated (not real tracking)

### Safe for Development/Staging:

-   ✅ Test API structure
-   ✅ Test decimal fields
-   ✅ Test filtering/pagination
-   ✅ Test frontend integration
-   ✅ Delete anytime (no impact)

---

## ✅ Recommended Workflow

1. **Fix database structure first:**

    ```sql
    -- Run: fix-trackings-phpmyadmin.sql
    -- Changes VARCHAR to DECIMAL
    ```

2. **Insert fake data:**

    ```sql
    -- Run: insert-fake-tracking-data.sql
    -- Inserts 70 tracking points
    ```

3. **Test API:**

    ```bash
    curl "https://gerobaks.dumeg.com/api/tracking?limit=10"
    ```

4. **Verify decimal values:**

    ```bash
    curl -s "https://gerobaks.dumeg.com/api/tracking?limit=5" | jq '.data[] | {latitude, longitude}'
    ```

5. **Test frontend:**

    - Open Flutter app
    - View tracking map
    - Verify routes display correctly

6. **Clean up when done:**
    ```sql
    DELETE FROM trackings WHERE schedule_id IN (1, 2, 3);
    ```

---

## 📂 Files Summary

```
backend/
├── database/seeders/
│   ├── QuickTrackingSeeder.php      ✅ 225 points (SSH required)
│   └── QuickScheduleSeeder.php      ✅ 10 schedules (SSH required)
├── insert-fake-tracking-data.sql    ✅ 70 points (phpMyAdmin)
├── GENERATE_FAKE_DATA.md            📚 This guide
└── CARA_CEK_API.md                  📚 API testing guide
```

---

**Quick Start:** Copy `insert-fake-tracking-data.sql` → Paste in phpMyAdmin → Execute → Test API! 🚀
