# Gerobaks API Endpoint Testing Script
# Run this with: .\test_endpoints.ps1

$apiUrl = "http://127.0.0.1:8000/api"
$passed = 0
$failed = 0

function Test-Endpoint {
    param(
        [string]$Method,
        [string]$Endpoint,
        [object]$Body = $null,
        [string]$Token = $null,
        [int]$ExpectedStatus = 200,
        [string]$Description
    )
    
    try {
        $headers = @{
            "Accept"       = "application/json"
            "Content-Type" = "application/json"
        }
        
        if ($Token) {
            $headers["Authorization"] = "Bearer $Token"
        }
        
        $params = @{
            Uri         = "$apiUrl$Endpoint"
            Method      = $Method
            Headers     = $headers
            ErrorAction = "Stop"
        }
        
        if ($Body) {
            $params["Body"] = ($Body | ConvertTo-Json)
        }
        
        $response = Invoke-WebRequest @params
        
        if ($response.StatusCode -eq $ExpectedStatus) {
            Write-Host "✓ $Description - Status: $($response.StatusCode)" -ForegroundColor Green
            $script:passed++
            return $response.Content | ConvertFrom-Json
        }
        else {
            Write-Host "✗ $Description - Expected: $ExpectedStatus, Got: $($response.StatusCode)" -ForegroundColor Red
            $script:failed++
            return $null
        }
    }
    catch {
        Write-Host "✗ $Description - Error: $($_.Exception.Message)" -ForegroundColor Red
        $script:failed++
        return $null
    }
}

Write-Host "`n╔═══════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║       GEROBAKS API ENDPOINT VERIFICATION TEST            ║" -ForegroundColor Cyan
Write-Host "╚═══════════════════════════════════════════════════════════╝`n" -ForegroundColor Cyan

# Test credentials
$email = "daffa@gmail.com"
$password = "daffa123"

Write-Host "`n═══ Public Endpoints ═══`n" -ForegroundColor Yellow

Test-Endpoint -Method "GET" -Endpoint "/health" -Description "GET /health"
Test-Endpoint -Method "GET" -Endpoint "/ping" -Description "GET /ping"
Test-Endpoint -Method "GET" -Endpoint "/settings" -Description "GET /settings"
Test-Endpoint -Method "GET" -Endpoint "/changelog?page=1&per_page=5" -Description "GET /changelog"
Test-Endpoint -Method "GET" -Endpoint "/changelog/stats" -Description "GET /changelog/stats"

Write-Host "`n═══ Authentication ═══`n" -ForegroundColor Yellow

$loginBody = @{
    email    = $email
    password = $password
}

Write-Host "ℹ Logging in as: $email" -ForegroundColor Cyan
$loginResponse = Test-Endpoint -Method "POST" -Endpoint "/login" -Body $loginBody -Description "POST /login"

if ($loginResponse -and $loginResponse.data.token) {
    $token = $loginResponse.data.token
    Write-Host "✓ Token obtained: $($token.Substring(0, 20))..." -ForegroundColor Green
    
    Write-Host "`n═══ Authenticated Endpoints ═══`n" -ForegroundColor Yellow
    
    Test-Endpoint -Method "GET" -Endpoint "/auth/me" -Token $token -Description "GET /auth/me"
    
    Write-Host "`n═══ User Profile ═══`n" -ForegroundColor Yellow
    
    $profileBody = @{
        name  = "Daffa Updated"
        phone = "081234567890"
    }
    Test-Endpoint -Method "POST" -Endpoint "/user/update-profile" -Body $profileBody -Token $token -Description "POST /user/update-profile"
    
    Write-Host "`n═══ Schedules ═══`n" -ForegroundColor Yellow
    
    Test-Endpoint -Method "GET" -Endpoint "/schedules" -Description "GET /schedules (public)"
    
    $scheduleBody = @{
        waste_type       = "organic"
        pickup_date      = (Get-Date).AddDays(1).ToString("yyyy-MM-dd")
        pickup_time      = "10:00:00"
        pickup_address   = "Jl. Test No. 123"
        pickup_latitude  = -6.2088
        pickup_longitude = 106.8456
        estimated_weight = 5.5
        notes            = "Test schedule"
    }
    
    $scheduleResponse = Test-Endpoint -Method "POST" -Endpoint "/schedules" -Body $scheduleBody -Token $token -ExpectedStatus 201 -Description "POST /schedules"
    
    if ($scheduleResponse -and $scheduleResponse.data.id) {
        $scheduleId = $scheduleResponse.data.id
        Write-Host "ℹ Created schedule ID: $scheduleId" -ForegroundColor Cyan
        
        Test-Endpoint -Method "GET" -Endpoint "/schedules/$scheduleId" -Description "GET /schedules/{id}"
        
        $updateBody = @{
            notes            = "Updated notes"
            estimated_weight = 6.0
        }
        Test-Endpoint -Method "PUT" -Endpoint "/schedules/$scheduleId" -Body $updateBody -Token $token -Description "PUT /schedules/{id}"
        
        Test-Endpoint -Method "POST" -Endpoint "/schedules/$scheduleId/cancel" -Token $token -Description "POST /schedules/{id}/cancel"
    }
    
    Write-Host "`n═══ Services ═══`n" -ForegroundColor Yellow
    
    $servicesResponse = Test-Endpoint -Method "GET" -Endpoint "/services" -Description "GET /services"
    
    if ($servicesResponse -and $servicesResponse.data -and $servicesResponse.data.Count -gt 0) {
        $serviceId = $servicesResponse.data[0].id
        Test-Endpoint -Method "GET" -Endpoint "/services/$serviceId" -Description "GET /services/{id}"
    }
    
    Write-Host "`n═══ Orders ═══`n" -ForegroundColor Yellow
    
    Test-Endpoint -Method "GET" -Endpoint "/orders" -Token $token -Description "GET /orders"
    
    Write-Host "`n═══ Tracking ═══`n" -ForegroundColor Yellow
    
    Test-Endpoint -Method "GET" -Endpoint "/tracking" -Description "GET /tracking"
    
    Write-Host "`n═══ Ratings ═══`n" -ForegroundColor Yellow
    
    Test-Endpoint -Method "GET" -Endpoint "/ratings" -Description "GET /ratings"
    
    Write-Host "`n═══ Notifications ═══`n" -ForegroundColor Yellow
    
    Test-Endpoint -Method "GET" -Endpoint "/notifications" -Token $token -Description "GET /notifications"
    
    Write-Host "`n═══ Balance ═══`n" -ForegroundColor Yellow
    
    Test-Endpoint -Method "GET" -Endpoint "/balance" -Token $token -Description "GET /balance"
    
    Write-Host "`n═══ Dashboard ═══`n" -ForegroundColor Yellow
    
    Test-Endpoint -Method "GET" -Endpoint "/dashboard" -Token $token -Description "GET /dashboard"
    
    Write-Host "`n═══ Subscription ═══`n" -ForegroundColor Yellow
    
    Test-Endpoint -Method "GET" -Endpoint "/subscription-plans" -Description "GET /subscription-plans"
    Test-Endpoint -Method "GET" -Endpoint "/subscription" -Token $token -Description "GET /subscription"
    
    Write-Host "`n═══ Feedback ═══`n" -ForegroundColor Yellow
    
    Test-Endpoint -Method "GET" -Endpoint "/feedbacks" -Token $token -Description "GET /feedbacks"
    
    $feedbackBody = @{
        subject  = "Test Feedback"
        message  = "This is a test feedback message"
        category = "suggestion"
    }
    Test-Endpoint -Method "POST" -Endpoint "/feedbacks" -Body $feedbackBody -Token $token -ExpectedStatus 201 -Description "POST /feedbacks"
    
    Write-Host "`n═══ Logout ═══`n" -ForegroundColor Yellow
    
    Test-Endpoint -Method "POST" -Endpoint "/auth/logout" -Token $token -Description "POST /auth/logout"
    
}
else {
    Write-Host "✗ Failed to obtain authentication token. Skipping authenticated tests." -ForegroundColor Red
}

# Summary
Write-Host "`n╔═══════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║                    TEST SUMMARY                           ║" -ForegroundColor Cyan
Write-Host "╚═══════════════════════════════════════════════════════════╝`n" -ForegroundColor Cyan

Write-Host "✓ Passed: $passed" -ForegroundColor Green
if ($failed -gt 0) {
    Write-Host "✗ Failed: $failed" -ForegroundColor Red
}
else {
    Write-Host "✓ Failed: 0" -ForegroundColor Green
}

$total = $passed + $failed
if ($total -gt 0) {
    $percentage = [math]::Round(($passed / $total) * 100, 2)
    if ($percentage -ge 80) {
        Write-Host "`n✓ Success Rate: $percentage% ($passed/$total)" -ForegroundColor Green
    }
    else {
        Write-Host "`n✗ Success Rate: $percentage% ($passed/$total)" -ForegroundColor Red
    }
}

if ($failed -eq 0) {
    Write-Host "`n🎉 ALL TESTS PASSED! 🎉`n" -ForegroundColor Green
    exit 0
}
else {
    Write-Host "`n⚠️  SOME TESTS FAILED ⚠️`n" -ForegroundColor Yellow
    exit 1
}
