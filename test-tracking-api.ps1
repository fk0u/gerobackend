# ================================================================
# TRACKING API TEST SCRIPT - PowerShell Version
# ================================================================

$BaseUrl = "https://gerobaks.dumeg.com/api"
Write-Host "🔍 Testing Tracking API..." -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan

# Test 1: Get all tracking (limit 5 for quick test)
Write-Host "`n📍 TEST 1: Get All Tracking (limit 5)" -ForegroundColor Yellow
$response1 = Invoke-RestMethod -Uri "$BaseUrl/tracking?limit=5" -Method Get
$response1 | ConvertTo-Json -Depth 10

# Test 2: Get tracking by schedule_id
Write-Host "`n📍 TEST 2: Get Tracking by Schedule ID" -ForegroundColor Yellow
try {
    $response2 = Invoke-RestMethod -Uri "$BaseUrl/tracking?schedule_id=1&limit=5" -Method Get
    $response2 | ConvertTo-Json -Depth 10
}
catch {
    Write-Host "⚠️  Schedule ID 1 might not exist" -ForegroundColor Red
}

# Test 3: Get tracking with date filter
Write-Host "`n📍 TEST 3: Get Recent Tracking (last 24 hours)" -ForegroundColor Yellow
$yesterday = (Get-Date).AddDays(-1).ToString("yyyy-MM-ddTHH:mm:ssZ")
$response3 = Invoke-RestMethod -Uri "$BaseUrl/tracking?since=$yesterday&limit=10" -Method Get
$response3 | ConvertTo-Json -Depth 10

# Test 4: Check first tracking point structure
Write-Host "`n📍 TEST 4: Check Tracking Data Structure" -ForegroundColor Yellow
$response4 = Invoke-RestMethod -Uri "$BaseUrl/tracking?limit=1" -Method Get
if ($response4.data.Count -gt 0) {
    Write-Host "`n✅ Sample Tracking Point:" -ForegroundColor Green
    $response4.data[0] | Format-List
    
    # Verify decimal fields
    Write-Host "`n🔍 Checking Decimal Fields:" -ForegroundColor Cyan
    $point = $response4.data[0]
    Write-Host "  Latitude:  $($point.latitude) ($(($point.latitude).GetType().Name))" -ForegroundColor White
    Write-Host "  Longitude: $($point.longitude) ($(($point.longitude).GetType().Name))" -ForegroundColor White
    Write-Host "  Speed:     $($point.speed) ($(($point.speed).GetType().Name))" -ForegroundColor White
    Write-Host "  Heading:   $($point.heading) ($(($point.heading).GetType().Name))" -ForegroundColor White
}
else {
    Write-Host "⚠️  No tracking data found!" -ForegroundColor Red
}

# Test 5: Count total records
Write-Host "`n📍 TEST 5: Count Total Records" -ForegroundColor Yellow
$response5 = Invoke-RestMethod -Uri "$BaseUrl/tracking?limit=1000" -Method Get
$totalCount = $response5.data.Count
Write-Host "Total tracking points: $totalCount" -ForegroundColor Green

# Test 6: Check for errors (empty strings, nulls)
Write-Host "`n📍 TEST 6: Validate Data Quality" -ForegroundColor Yellow
$response6 = Invoke-RestMethod -Uri "$BaseUrl/tracking?limit=50" -Method Get
$invalidPoints = 0
foreach ($point in $response6.data) {
    if ($null -eq $point.latitude -or $null -eq $point.longitude) {
        $invalidPoints++
    }
}
if ($invalidPoints -gt 0) {
    Write-Host "⚠️  Found $invalidPoints points with NULL coordinates!" -ForegroundColor Red
}
else {
    Write-Host "✅ All coordinates are valid!" -ForegroundColor Green
}

# Test 7: Test specific schedule tracking history
Write-Host "`n📍 TEST 7: Get Tracking History for Schedule" -ForegroundColor Yellow
try {
    $response7 = Invoke-RestMethod -Uri "$BaseUrl/tracking/schedule/1" -Method Get
    Write-Host "Schedule 1 tracking history: $($response7.data.Count) points" -ForegroundColor Green
}
catch {
    Write-Host "⚠️  Could not fetch schedule tracking history" -ForegroundColor Red
}

Write-Host "`n================================" -ForegroundColor Cyan
Write-Host "✅ Testing Complete!" -ForegroundColor Green
Write-Host "`nℹ️  If you see errors, run the SQL fix first:" -ForegroundColor Yellow
Write-Host "   fix-trackings-phpmyadmin.sql" -ForegroundColor White
