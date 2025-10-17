# 🎯 CARA CEK API - COMPLETE GUIDE

## 📊 Current Status

**Tracking API:** ✅ Responds, ⚠️ Empty data  
**Schedules API:** ✅ Responds, ⚠️ Empty data

```
GET /api/tracking  → 200 OK, 0 records
GET /api/schedules → 200 OK, 0 records
```

---

## 🧪 3 Cara Mudah Test API

### **Method 1: Via Browser (Paling Gampang!)** 🌐

Langsung paste di browser:

```
https://gerobaks.dumeg.com/api/tracking
https://gerobaks.dumeg.com/api/schedules
```

**Hasil yang bagus:**

```json
{ "data": [] }
```

**Hasil yang error:**

```json
{ "message": "Server Error", "exception": "..." }
```

---

### **Method 2: Via PowerShell (Recommended!)** 💻

#### Quick Test:

```powershell
# Test Tracking API
Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/tracking?limit=5"

# Test Schedules API
Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/schedules?limit=5"
```

#### Comprehensive Test:

```powershell
# Run full test suite
cd backend
.\test-tracking-api.ps1
```

#### Check Specific Fields:

```powershell
$response = Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/tracking?limit=10"
$response.data | Select-Object id, latitude, longitude, speed | Format-Table
```

---

### **Method 3: Via cURL** 🔧

```bash
# Simple test
curl "https://gerobaks.dumeg.com/api/tracking?limit=5"

# Pretty JSON
curl -s "https://gerobaks.dumeg.com/api/tracking?limit=5" | jq '.'

# Check specific fields
curl -s "https://gerobaks.dumeg.com/api/tracking?limit=10" | jq '.data[] | {latitude, longitude}'
```

---

## 🚨 Saat Ini: Tables Kosong!

### Good News:

✅ API works (returns 200 OK)  
✅ No corrupt data to fix  
✅ Clean slate for testing

### Bad News:

⚠️ Database structure still WRONG (VARCHAR instead of DECIMAL)  
⚠️ Will cause errors when data comes in!

---

## 🔧 Yang Perlu Dilakukan SEKARANG

### Priority 1: Fix Database Structure (Even Though Empty!)

**Tracking Table:**

```sql
-- Current (WRONG):
latitude  → VARCHAR(32)  ❌
longitude → VARCHAR(32)  ❌

-- Fix to (CORRECT):
ALTER TABLE trackings MODIFY COLUMN latitude DECIMAL(10, 7) NULL;
ALTER TABLE trackings MODIFY COLUMN longitude DECIMAL(10, 7) NULL;
ALTER TABLE trackings MODIFY COLUMN speed DECIMAL(8, 2) NULL;
ALTER TABLE trackings MODIFY COLUMN heading DECIMAL(5, 2) NULL;
```

**Schedules Table:**

```sql
-- Fix decimal columns
-- Run: fix-schedules-ONE-LINER.sql
-- (Uses temp column approach)
```

---

## 📱 Kapan Data Akan Muncul?

### Tracking Data:

-   Saat **Mitra** mulai pickup/delivery
-   Flutter app kirim GPS coordinates via POST /api/tracking
-   Real-time tracking updates

### Schedules Data:

-   Saat **User** create jadwal penjemputan
-   Via Flutter app atau web admin
-   POST /api/schedules

---

## 🧪 Test Plan Lengkap

### Step 1: Check API Response (DONE! ✅)

```powershell
Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/tracking?limit=5"
```

**Result:** Empty array, no errors ✅

### Step 2: Fix Database Structure (TO DO)

```sql
-- For tracking (simple, table is empty):
ALTER TABLE trackings MODIFY COLUMN latitude DECIMAL(10, 7) NULL;
ALTER TABLE trackings MODIFY COLUMN longitude DECIMAL(10, 7) NULL;

-- For schedules (complex, might have data):
-- Use fix-schedules-ONE-LINER.sql
```

### Step 3: Verify Structure

```sql
SHOW COLUMNS FROM trackings WHERE Field IN ('latitude', 'longitude');
SHOW COLUMNS FROM schedules WHERE Field IN ('price', 'latitude', 'longitude');
```

### Step 4: Clear Laravel Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Step 5: Test Again

```powershell
Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/tracking"
Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/schedules"
```

### Step 6: Add Test Data (Optional)

```bash
php artisan db:seed --class=TrackingSeeder
php artisan db:seed --class=ScheduleSeeder
```

### Step 7: Test with Data

```powershell
$response = Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/tracking?limit=5"
$response.data | Select-Object id, latitude, longitude | Format-Table
```

---

## 🎯 Quick Commands untuk Cek API

### Check if API is alive:

```powershell
Invoke-WebRequest -Uri "https://gerobaks.dumeg.com/api/tracking" -Method Head
```

### Get response with headers:

```powershell
$response = Invoke-WebRequest -Uri "https://gerobaks.dumeg.com/api/tracking?limit=5"
Write-Host "Status: $($response.StatusCode)"
Write-Host "Content-Type: $($response.Headers['Content-Type'])"
$response.Content | ConvertFrom-Json
```

### Check data count:

```powershell
$data = (Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/tracking?limit=1000").data
Write-Host "Total records: $($data.Count)"
```

### Monitor real-time (keep checking):

```powershell
while ($true) {
    $count = (Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/tracking").data.Count
    Write-Host "$(Get-Date -Format 'HH:mm:ss') - Tracking records: $count"
    Start-Sleep -Seconds 5
}
```

---

## 📊 Expected API Response Structure

### Tracking API:

```json
{
  "data": [
    {
      "id": 1,
      "schedule_id": "abc123",
      "latitude": -6.2088,        // ✅ DECIMAL number
      "longitude": 106.8456,       // ✅ DECIMAL number
      "speed": 45.5,               // ✅ DECIMAL or null
      "heading": 180.0,            // ✅ DECIMAL or null
      "recorded_at": "2025-01-14T10:30:00Z",
      "schedule": {
        "id": "abc123",
        "user_name": "John Doe",
        ...
      }
    }
  ]
}
```

### Schedules API:

```json
{
    "data": [
        {
            "id": "abc123",
            "user_id": 1,
            "user_name": "John Doe",
            "mitra_id": 2,
            "mitra_name": "Mitra ABC",
            "price": 50000.0, // ✅ DECIMAL number
            "latitude": -6.2088, // ✅ DECIMAL number
            "longitude": 106.8456, // ✅ DECIMAL number
            "pickup_latitude": -6.2, // ✅ DECIMAL number
            "pickup_longitude": 106.83, // ✅ DECIMAL number
            "status": "pending",
            "scheduled_time": "2025-01-14T14:00:00Z",
            "trackings_count": 0
        }
    ]
}
```

---

## ⚠️ Error Signs to Look For

### Before Fix:

```json
{
    "latitude": "", // ❌ Empty string
    "longitude": "0", // ❌ String zero
    "price": null // ⚠️ Might be OK or error
}
```

### After Fix:

```json
{
    "latitude": -6.2088, // ✅ Clean decimal
    "longitude": 106.8456, // ✅ Clean decimal
    "price": 50000.0 // ✅ Clean decimal
}
```

### Server Error:

```json
{
    "message": "Server Error",
    "exception": "MathException",
    "message": "Unable to cast value to a decimal"
}
```

---

## 🔗 Test Scripts Available

1. **test-tracking-api.ps1** - PowerShell comprehensive test
2. **test-tracking-api.sh** - Bash comprehensive test
3. **test-tracking-curl.md** - cURL commands list
4. **TRACKING_API_TESTING.md** - Full testing guide

---

## 📝 Checklist untuk Production

-   [ ] Fix `trackings` table structure (ALTER to DECIMAL)
-   [ ] Fix `schedules` table structure (run SQL script)
-   [ ] Clear Laravel cache
-   [ ] Test API endpoints (GET /tracking, GET /schedules)
-   [ ] Monitor logs when data comes in
-   [ ] Verify decimal values in responses
-   [ ] Check mobile app sends correct format

---

## 🚀 Recommended Next Steps

### SEKARANG (High Priority):

1. ✅ Fix `trackings` structure: Simple ALTER (table kosong)
2. ✅ Fix `schedules` structure: Run SQL script (might have data)
3. ✅ Clear cache
4. ✅ Test APIs

### NANTI (When Data Comes):

1. Monitor logs: `tail -f storage/logs/laravel.log`
2. Check responses: `curl https://gerobaks.dumeg.com/api/tracking`
3. Verify decimal format in JSON responses

### OPTIONAL (For Testing):

1. Run seeders to populate test data
2. Use Postman for POST requests
3. Monitor real-time with PowerShell loop

---

**TL;DR:**

-   ✅ APIs work (200 OK)
-   ⚠️ Tables empty (no data yet)
-   ❌ Structure wrong (VARCHAR not DECIMAL)
-   🎯 **FIX STRUCTURE NOW!** Before data comes in

**Cara cek:** Browser, PowerShell, atau cURL  
**Yang perlu difix:** Database structure (DECIMAL columns)  
**Kapan fix:** SEKARANG! Sebelum ada data masuk
