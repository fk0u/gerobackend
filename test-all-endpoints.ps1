# ================================================================
# COMPREHENSIVE API ENDPOINT TESTING
# ================================================================
# Tests ALL endpoints in Gerobaks API
# ================================================================

$baseUrl = "https://gerobaks.dumeg.com/api"
$testResults = @()
$passCount = 0
$failCount = 0

function Test-Endpoint {
    param(
        [string]$Method,
        [string]$Endpoint,
        [string]$Description,
        [hashtable]$Headers = @{},
        [object]$Body = $null,
        [int]$ExpectedStatus = 200
    )
    
    $url = "$baseUrl$Endpoint"
    
    Write-Host "`n----------------------------------------" -ForegroundColor Cyan
    Write-Host "Testing: $Description" -ForegroundColor Yellow
    Write-Host "Method: $Method | Endpoint: $Endpoint" -ForegroundColor Gray
    
    try {
        $params = @{
            Uri         = $url
            Method      = $Method
            Headers     = $Headers
            ErrorAction = 'Stop'
        }
        
        if ($Body) {
            $params.Body = ($Body | ConvertTo-Json -Depth 10)
            $params.ContentType = 'application/json'
        }
        
        $response = Invoke-RestMethod @params -StatusCodeVariable statusCode
        
        $success = $statusCode -eq $ExpectedStatus
        
        if ($success) {
            Write-Host "✅ PASS - Status: $statusCode" -ForegroundColor Green
            $script:passCount++
        }
        else {
            Write-Host "⚠️  WARN - Expected $ExpectedStatus, got $statusCode" -ForegroundColor Yellow
            $script:failCount++
        }
        
        # Show sample response
        if ($response) {
            Write-Host "Response:" -ForegroundColor Gray
            $response | ConvertTo-Json -Depth 2 -Compress | Write-Host -ForegroundColor DarkGray
        }
        
        return @{
            Success  = $success
            Status   = $statusCode
            Response = $response
        }
        
    }
    catch {
        Write-Host "❌ FAIL - Error: $($_.Exception.Message)" -ForegroundColor Red
        $script:failCount++
        
        return @{
            Success = $false
            Status  = 0
            Error   = $_.Exception.Message
        }
    }
}

Write-Host "================================================================" -ForegroundColor Magenta
Write-Host "  GEROBAKS API - COMPREHENSIVE ENDPOINT TEST" -ForegroundColor Magenta
Write-Host "  Base URL: $baseUrl" -ForegroundColor Magenta
Write-Host "================================================================" -ForegroundColor Magenta

# ================================================================
# 1. HEALTH & SYSTEM ENDPOINTS
# ================================================================
Write-Host "`n`n🏥 SECTION 1: HEALTH & SYSTEM ENDPOINTS" -ForegroundColor Magenta

Test-Endpoint -Method GET -Endpoint "/health" -Description "Health Check" -ExpectedStatus 200
Test-Endpoint -Method GET -Endpoint "/ping" -Description "Ping Check" -ExpectedStatus 200

# ================================================================
# 2. PUBLIC ENDPOINTS (No Auth)
# ================================================================
Write-Host "`n`n🌍 SECTION 2: PUBLIC ENDPOINTS (No Authentication)" -ForegroundColor Magenta

Test-Endpoint -Method GET -Endpoint "/settings" -Description "Get Public Settings" -ExpectedStatus 200
Test-Endpoint -Method GET -Endpoint "/settings/api-config" -Description "Get API Config" -ExpectedStatus 200
Test-Endpoint -Method GET -Endpoint "/schedules" -Description "List All Schedules (Public)" -ExpectedStatus 200
Test-Endpoint -Method GET -Endpoint "/schedules?limit=5" -Description "List Schedules with Limit" -ExpectedStatus 200
Test-Endpoint -Method GET -Endpoint "/tracking" -Description "List All Tracking (Public)" -ExpectedStatus 200
Test-Endpoint -Method GET -Endpoint "/tracking?limit=10" -Description "List Tracking with Limit" -ExpectedStatus 200
Test-Endpoint -Method GET -Endpoint "/services" -Description "List All Services (Public)" -ExpectedStatus 200
Test-Endpoint -Method GET -Endpoint "/ratings" -Description "List All Ratings (Public)" -ExpectedStatus 200

# ================================================================
# 3. AUTH ENDPOINTS (Registration & Login)
# ================================================================
Write-Host "`n`n🔐 SECTION 3: AUTHENTICATION ENDPOINTS" -ForegroundColor Magenta

# Test invalid login (should fail)
Test-Endpoint -Method POST -Endpoint "/login" `
    -Description "Login - Invalid Credentials (Expected to fail)" `
    -Body @{
    email    = "nonexistent@test.com"
    password = "wrongpassword"
} `
    -ExpectedStatus 401

# Test registration with missing fields (should fail)
Test-Endpoint -Method POST -Endpoint "/register" `
    -Description "Register - Missing Fields (Expected to fail)" `
    -Body @{
    email = "test@test.com"
} `
    -ExpectedStatus 422

# ================================================================
# 4. PROTECTED ENDPOINTS (Requires Auth - Expected to fail without token)
# ================================================================
Write-Host "`n`n🔒 SECTION 4: PROTECTED ENDPOINTS (Should fail without auth)" -ForegroundColor Magenta

Test-Endpoint -Method GET -Endpoint "/auth/me" `
    -Description "Get Current User (No Token)" `
    -ExpectedStatus 401

Test-Endpoint -Method POST -Endpoint "/auth/logout" `
    -Description "Logout (No Token)" `
    -ExpectedStatus 401

Test-Endpoint -Method POST -Endpoint "/user/update-profile" `
    -Description "Update Profile (No Token)" `
    -ExpectedStatus 401

Test-Endpoint -Method POST -Endpoint "/orders" `
    -Description "Create Order (No Token)" `
    -ExpectedStatus 401

Test-Endpoint -Method GET -Endpoint "/notifications" `
    -Description "Get Notifications (No Token)" `
    -ExpectedStatus 401

Test-Endpoint -Method GET -Endpoint "/balance/summary" `
    -Description "Get Balance Summary (No Token)" `
    -ExpectedStatus 401

Test-Endpoint -Method GET -Endpoint "/chats" `
    -Description "Get Chats (No Token)" `
    -ExpectedStatus 401

Test-Endpoint -Method GET -Endpoint "/subscription/current" `
    -Description "Get Current Subscription (No Token)" `
    -ExpectedStatus 401

Test-Endpoint -Method GET -Endpoint "/admin/stats" `
    -Description "Admin Stats (No Token)" `
    -ExpectedStatus 401

# ================================================================
# 5. DATA STRUCTURE VALIDATION
# ================================================================
Write-Host "`n`n📊 SECTION 5: DATA STRUCTURE VALIDATION" -ForegroundColor Magenta

Write-Host "`nTesting Schedules Response Structure..." -ForegroundColor Yellow
$schedulesResponse = Test-Endpoint -Method GET -Endpoint "/schedules?limit=1" -Description "Get Schedule Structure"
if ($schedulesResponse.Response.data) {
    Write-Host "✅ Schedules has 'data' array" -ForegroundColor Green
    if ($schedulesResponse.Response.data.Count -gt 0) {
        $schedule = $schedulesResponse.Response.data[0]
        Write-Host "Sample Schedule Fields:" -ForegroundColor Gray
        $schedule.PSObject.Properties.Name | ForEach-Object { Write-Host "  - $_" -ForegroundColor DarkGray }
    }
}
else {
    Write-Host "ℹ️  No schedules data (table might be empty)" -ForegroundColor Cyan
}

Write-Host "`nTesting Tracking Response Structure..." -ForegroundColor Yellow
$trackingResponse = Test-Endpoint -Method GET -Endpoint "/tracking?limit=1" -Description "Get Tracking Structure"
if ($trackingResponse.Response.data) {
    Write-Host "✅ Tracking has 'data' array" -ForegroundColor Green
    if ($trackingResponse.Response.data.Count -gt 0) {
        $tracking = $trackingResponse.Response.data[0]
        Write-Host "Sample Tracking Fields:" -ForegroundColor Gray
        $tracking.PSObject.Properties.Name | ForEach-Object { Write-Host "  - $_" -ForegroundColor DarkGray }
        
        # Check decimal precision
        if ($tracking.latitude) {
            Write-Host "`nDecimal Precision Check:" -ForegroundColor Yellow
            Write-Host "  Latitude: $($tracking.latitude) (type: $($tracking.latitude.GetType().Name))" -ForegroundColor Gray
            Write-Host "  Longitude: $($tracking.longitude) (type: $($tracking.longitude.GetType().Name))" -ForegroundColor Gray
        }
    }
}
else {
    Write-Host "ℹ️  No tracking data (table might be empty)" -ForegroundColor Cyan
}

Write-Host "`nTesting Services Response Structure..." -ForegroundColor Yellow
$servicesResponse = Test-Endpoint -Method GET -Endpoint "/services" -Description "Get Services Structure"
if ($servicesResponse.Response.data) {
    Write-Host "✅ Services has 'data' array" -ForegroundColor Green
    if ($servicesResponse.Response.data.Count -gt 0) {
        $service = $servicesResponse.Response.data[0]
        Write-Host "Sample Service Fields:" -ForegroundColor Gray
        $service.PSObject.Properties.Name | ForEach-Object { Write-Host "  - $_" -ForegroundColor DarkGray }
    }
}
else {
    Write-Host "ℹ️  No services data" -ForegroundColor Cyan
}

# ================================================================
# 6. QUERY PARAMETER TESTING
# ================================================================
Write-Host "`n`n🔍 SECTION 6: QUERY PARAMETER VALIDATION" -ForegroundColor Magenta

Test-Endpoint -Method GET -Endpoint "/schedules?status=pending" -Description "Filter Schedules by Status"
Test-Endpoint -Method GET -Endpoint "/schedules?limit=5" -Description "Pagination - Limit Only"
Test-Endpoint -Method GET -Endpoint "/tracking?schedule_id=1" -Description "Filter Tracking by Schedule ID"
Test-Endpoint -Method GET -Endpoint "/tracking/schedule/1" -Description "Get Tracking History by Schedule"

# ================================================================
# SUMMARY
# ================================================================
Write-Host "`n`n================================================================" -ForegroundColor Magenta
Write-Host "  TEST SUMMARY" -ForegroundColor Magenta
Write-Host "================================================================" -ForegroundColor Magenta
Write-Host "Total Tests: $($passCount + $failCount)" -ForegroundColor White
Write-Host "✅ Passed: $passCount" -ForegroundColor Green
Write-Host "❌ Failed: $failCount" -ForegroundColor Red

$successRate = [math]::Round(($passCount / ($passCount + $failCount)) * 100, 2)
Write-Host "`nSuccess Rate: $successRate%" -ForegroundColor $(if ($successRate -ge 80) { "Green" } elseif ($successRate -ge 50) { "Yellow" } else { "Red" })

Write-Host "`n📋 RECOMMENDATIONS:" -ForegroundColor Cyan
if ($failCount -gt 0) {
    Write-Host "  - Review failed endpoints above" -ForegroundColor Yellow
    Write-Host "  - Check Laravel logs: storage/logs/laravel.log" -ForegroundColor Yellow
}
Write-Host "  - Insert fake data to test with real responses" -ForegroundColor Cyan
Write-Host "  - Test authenticated endpoints with valid token" -ForegroundColor Cyan
Write-Host "  - Run: insert-fake-tracking-data.sql in phpMyAdmin" -ForegroundColor Cyan

Write-Host "`n================================================================`n" -ForegroundColor Magenta
