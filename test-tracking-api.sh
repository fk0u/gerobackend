#!/bin/bash
# ================================================================
# TRACKING API TEST SCRIPT
# ================================================================

BASE_URL="https://gerobaks.dumeg.com/api"
echo "🔍 Testing Tracking API..."
echo "================================"

# Test 1: Get all tracking (default limit 200)
echo -e "\n📍 TEST 1: Get All Tracking"
curl -s "${BASE_URL}/tracking?limit=5" | jq '.'

# Test 2: Get tracking by schedule_id
echo -e "\n📍 TEST 2: Get Tracking by Schedule ID"
curl -s "${BASE_URL}/tracking?schedule_id=1&limit=5" | jq '.'

# Test 3: Get tracking with date range
echo -e "\n📍 TEST 3: Get Tracking Since Yesterday"
YESTERDAY=$(date -u -d "yesterday" +"%Y-%m-%dT%H:%M:%SZ" 2>/dev/null || date -u -v-1d +"%Y-%m-%dT%H:%M:%SZ")
curl -s "${BASE_URL}/tracking?since=${YESTERDAY}&limit=10" | jq '.'

# Test 4: Get tracking history by schedule
echo -e "\n📍 TEST 4: Get Tracking History for Schedule ID 1"
curl -s "${BASE_URL}/tracking/schedule/1" | jq '.'

# Test 5: Check structure of first tracking point
echo -e "\n📍 TEST 5: Check First Tracking Point Structure"
curl -s "${BASE_URL}/tracking?limit=1" | jq '.data[0]'

# Test 6: Verify decimal values (should not be null or empty)
echo -e "\n📍 TEST 6: Verify Decimal Values"
curl -s "${BASE_URL}/tracking?limit=10" | jq '.data[] | {id, latitude, longitude, speed, heading}'

# Test 7: Count records
echo -e "\n📍 TEST 7: Count Total Records"
curl -s "${BASE_URL}/tracking?limit=1000" | jq '.data | length'

echo -e "\n================================"
echo "✅ Testing Complete!"
