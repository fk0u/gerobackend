# ================================================================

# QUICK TRACKING API TESTS - Copy & Paste Commands

# ================================================================

## 1. GET ALL TRACKING (Limit 5)

## --------------------------------

curl "https://gerobaks.dumeg.com/api/tracking?limit=5"

## 2. GET ALL TRACKING (Pretty JSON)

## --------------------------------

curl -s "https://gerobaks.dumeg.com/api/tracking?limit=5" | jq '.'

## 3. GET TRACKING BY SCHEDULE ID

## --------------------------------

curl "https://gerobaks.dumeg.com/api/tracking?schedule_id=1&limit=10"

## 4. GET TRACKING WITH DATE RANGE

## --------------------------------

curl "https://gerobaks.dumeg.com/api/tracking?since=2025-01-01T00:00:00Z&limit=20"

## 5. GET TRACKING HISTORY FOR SPECIFIC SCHEDULE

## ------------------------------------------------

curl "https://gerobaks.dumeg.com/api/tracking/schedule/1"

## 6. CHECK FIRST TRACKING POINT STRUCTURE

## -----------------------------------------

curl -s "https://gerobaks.dumeg.com/api/tracking?limit=1" | jq '.data[0]'

## 7. VERIFY DECIMAL FIELDS (Should NOT be null/empty)

## -----------------------------------------------------

curl -s "https://gerobaks.dumeg.com/api/tracking?limit=10" | jq '.data[] | {id, latitude, longitude, speed, heading}'

## 8. COUNT TOTAL RECORDS

## ------------------------

curl -s "https://gerobaks.dumeg.com/api/tracking?limit=1000" | jq '.data | length'

## 9. CHECK SPECIFIC FIELDS ONLY

## -------------------------------

curl -s "https://gerobaks.dumeg.com/api/tracking?limit=5" | jq '.data[] | {id, schedule_id, lat: .latitude, lng: .longitude, time: .recorded_at}'

## 10. GET LATEST 3 TRACKING POINTS

## ----------------------------------

curl -s "https://gerobaks.dumeg.com/api/tracking?limit=3" | jq '.data[] | {id, latitude, longitude, recorded_at}'

# ================================================================

# EXPECTED RESPONSE FORMAT

# ================================================================

# {

# "data": [

# {

# "id": 1,

# "schedule_id": "abc123",

# "latitude": -6.2088, // ✅ Should be decimal number

# "longitude": 106.8456, // ✅ Should be decimal number

# "speed": 45.5, // ✅ Should be decimal number or null

# "heading": 180.0, // ✅ Should be decimal number or null

# "recorded_at": "2025-01-14T10:30:00Z",

# "created_at": "2025-01-14T10:30:00Z",

# "updated_at": "2025-01-14T10:30:00Z",

# "schedule": {

# "id": "abc123",

# "assigned_user_id": 1,

# ...

# }

# }

# ]

# }

# ================================================================

# ERROR INDICATORS (BEFORE FIX)

# ================================================================

# ❌ "latitude": "" // Empty string = BAD

# ❌ "longitude": "0" // String zero = BAD

# ❌ "latitude": null // Null might be OK (no GPS signal)

# ❌ 500 Internal Server Error // Casting error!

# ================================================================

# SUCCESS INDICATORS (AFTER FIX)

# ================================================================

# ✅ "latitude": -6.2088 // Clean decimal

# ✅ "longitude": 106.8456 // Clean decimal

# ✅ "latitude": null // OK if no GPS

# ✅ 200 OK status // No errors!
