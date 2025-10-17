# 🧪 TRACKING API TESTING GUIDE

## 📍 API Endpoint

**Base URL:** `https://gerobaks.dumeg.com/api`

**Endpoints:**

-   `GET /tracking` - List all tracking points
-   `GET /tracking/schedule/{id}` - Get tracking history for specific schedule
-   `POST /tracking` - Create new tracking point (requires auth)

---

## 🔍 Method 1: Browser (Easiest)

Just paste this in your browser:

```
https://gerobaks.dumeg.com/api/tracking?limit=5
```

**Expected:** JSON response with tracking data  
**If Error:** Check Laravel logs or browser console

---

## 💻 Method 2: PowerShell (Recommended for Windows)

### Quick Test:

```powershell
Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/tracking?limit=5" | ConvertTo-Json -Depth 10
```

### Full Test Suite:

```powershell
# Run comprehensive test
.\test-tracking-api.ps1
```

**What it tests:**

-   ✅ API connectivity
-   ✅ Data structure
-   ✅ Decimal field validation
-   ✅ NULL handling
-   ✅ Date filtering
-   ✅ Schedule filtering
-   ✅ Record count

---

## 🔧 Method 3: cURL (Cross-platform)

### Basic Test:

```bash
curl "https://gerobaks.dumeg.com/api/tracking?limit=5"
```

### With Pretty JSON:

```bash
curl -s "https://gerobaks.dumeg.com/api/tracking?limit=5" | jq '.'
```

### All Test Commands:

See `test-tracking-curl.md` for complete list

---

## 📊 Method 4: Postman

### Setup:

1. Create new request
2. Method: **GET**
3. URL: `https://gerobaks.dumeg.com/api/tracking`
4. Add Query Params:
    - `limit`: `10`
    - `schedule_id`: `1` (optional)
    - `since`: `2025-01-01T00:00:00Z` (optional)

### Expected Response:

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
      "recorded_at": "2025-01-14T10:30:00Z",
      "schedule": {...}
    }
  ]
}
```

---

## ✅ Validation Checklist

After running SQL fix, verify these:

### 1. Response Status

```bash
curl -I "https://gerobaks.dumeg.com/api/tracking?limit=1"
```

**Expected:** `HTTP/1.1 200 OK`

### 2. Data Types

```bash
curl -s "https://gerobaks.dumeg.com/api/tracking?limit=1" | jq '.data[0] | {latitude, longitude, speed, heading}'
```

**Expected:**

```json
{
    "latitude": -6.2088, // ✅ Number, not string
    "longitude": 106.8456, // ✅ Number, not string
    "speed": 45.5, // ✅ Number or null
    "heading": 180.0 // ✅ Number or null
}
```

### 3. No Empty Strings

```bash
curl -s "https://gerobaks.dumeg.com/api/tracking?limit=50" | jq '.data[] | select(.latitude == "" or .longitude == "")'
```

**Expected:** No output (no empty strings)

### 4. Count Records

```bash
curl -s "https://gerobaks.dumeg.com/api/tracking?limit=1000" | jq '.data | length'
```

**Expected:** Number of tracking points

### 5. Test Filtering

```bash
curl -s "https://gerobaks.dumeg.com/api/tracking?schedule_id=1&limit=10" | jq '.data | length'
```

**Expected:** Tracking points for schedule 1

---

## 🚨 Common Issues & Solutions

### Issue 1: 500 Internal Server Error

**Symptom:**

```json
{
    "message": "Server Error"
}
```

**Cause:** Database has VARCHAR with empty strings  
**Solution:** Run `fix-trackings-phpmyadmin.sql`

### Issue 2: Empty/NULL Coordinates

**Symptom:**

```json
{
    "latitude": null,
    "longitude": null
}
```

**Possible Causes:**

-   No GPS signal when recorded ✅ (OK)
-   Empty strings in database ❌ (needs fix)

**Check Database:**

```sql
SELECT COUNT(*) FROM trackings WHERE latitude = '' OR longitude = '';
```

**If count > 0:** Run SQL fix

### Issue 3: String Instead of Number

**Symptom:**

```json
{
    "latitude": "0", // ❌ String!
    "longitude": "106.8456" // ❌ String!
}
```

**Cause:** Database column is VARCHAR  
**Solution:** Run SQL fix + update Model casts

### Issue 4: Decimal Casting Error

**Symptom:** Laravel log shows:

```
MathException: Unable to cast value to a decimal
```

**Solution:**

1. Run SQL fix
2. Update Model: `'latitude' => 'decimal:7'`
3. Update Resource with `safeDecimal()`
4. Clear cache

---

## 📈 Performance Testing

### Test Response Time:

```bash
curl -w "\nTime: %{time_total}s\n" -s "https://gerobaks.dumeg.com/api/tracking?limit=100" -o /dev/null
```

**Expected:** < 1 second for 100 records

### Test with Large Dataset:

```bash
curl -w "\nTime: %{time_total}s\n" -s "https://gerobaks.dumeg.com/api/tracking?limit=1000" -o /dev/null
```

**Expected:** < 3 seconds for 1000 records

---

## 🎯 Complete Test Workflow

### Before SQL Fix:

```bash
# 1. Test current state
curl "https://gerobaks.dumeg.com/api/tracking?limit=5"

# 2. Check for errors in logs
tail -f storage/logs/laravel.log

# 3. Verify database structure
# In phpMyAdmin:
SHOW COLUMNS FROM trackings WHERE Field IN ('latitude', 'longitude');
```

### After SQL Fix:

```bash
# 1. Run SQL fix
# Execute fix-trackings-phpmyadmin.sql in phpMyAdmin

# 2. Clear cache
php artisan cache:clear
php artisan config:clear

# 3. Test API again
curl "https://gerobaks.dumeg.com/api/tracking?limit=5"

# 4. Run full test suite
.\test-tracking-api.ps1

# 5. Verify decimal fields
curl -s "https://gerobaks.dumeg.com/api/tracking?limit=10" | jq '.data[] | {latitude, longitude}'
```

---

## 📝 Test Results Template

```markdown
## Tracking API Test Results

**Date:** 2025-01-14  
**Environment:** Production  
**Tester:** [Your Name]

### Before Fix:

-   [ ] API Status: ❌ 500 Error
-   [ ] Data Type: ❌ VARCHAR
-   [ ] Empty Strings: ❌ Found X records
-   [ ] Casting Errors: ❌ Yes

### After Fix:

-   [ ] API Status: ✅ 200 OK
-   [ ] Data Type: ✅ DECIMAL
-   [ ] Empty Strings: ✅ 0 records
-   [ ] Casting Errors: ✅ None
-   [ ] Response Time: ✅ < 1s
-   [ ] Data Integrity: ✅ Valid coordinates

### Issues Found:

1. [Issue description]
2. [Issue description]

### Notes:

-   [Any additional notes]
```

---

## 🔗 Related Files

-   `fix-trackings-phpmyadmin.sql` - SQL fix script
-   `test-tracking-api.ps1` - PowerShell test script
-   `test-tracking-api.sh` - Bash test script
-   `test-tracking-curl.md` - cURL commands
-   `TRACKING_DECIMAL_FIX.md` - Complete documentation

---

**Ready to test?** Start with the browser method, then use PowerShell for comprehensive testing! 🚀
