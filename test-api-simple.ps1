# ================================================================
# COMPREHENSIVE API ENDPOINT TESTING - PowerShell 5.1 Compatible
# ================================================================

$baseUrl = "https://gerobaks.dumeg.com/api"
$passCount = 0
$failCount = 0

function Test-Endpoint {
    param(
        [string]$Method,
        [string]$Endpoint,
        [string]$Description
    )
    
    $url = "$baseUrl$Endpoint"
    
    Write-Host "`n----------------------------------------" -ForegroundColor Cyan
    Write-Host "Testing: $Description" -ForegroundColor Yellow
    Write-Host "$Method $Endpoint" -ForegroundColor Gray
    
    try {
        $response = Invoke-RestMethod -Uri $url -Method $Method -ErrorAction Stop
        Write-Host "[PASS] Response OK" -ForegroundColor Green
        $script:passCount++
        
        # Show data count if available
        if ($response.data) {
            $count = if ($response.data.Count) { $response.data.Count } else { 1 }
            Write-Host "Data: $count records" -ForegroundColor DarkGray
        }
        
        return $response
        
    }
    catch {
        $statusCode = $_.Exception.Response.StatusCode.value__
        if ($statusCode) {
            Write-Host "[INFO] Status: $statusCode" -ForegroundColor Yellow
        }
        else {
            Write-Host "[FAIL] $($_.Exception.Message)" -ForegroundColor Red
        }
        $script:failCount++
        return $null
    }
}

Write-Host "================================================================" -ForegroundColor Magenta
Write-Host "  GEROBAKS API - COMPREHENSIVE TEST" -ForegroundColor Magenta
Write-Host "  Base URL: $baseUrl" -ForegroundColor Magenta
Write-Host "================================================================" -ForegroundColor Magenta

# ================================================================
# 1. HEALTH & SYSTEM
# ================================================================
Write-Host "`n`n[1] HEALTH & SYSTEM" -ForegroundColor Magenta

Test-Endpoint -Method GET -Endpoint "/health" -Description "Health Check"
Test-Endpoint -Method GET -Endpoint "/ping" -Description "Ping"

# ================================================================
# 2. PUBLIC ENDPOINTS
# ================================================================
Write-Host "`n`n[2] PUBLIC ENDPOINTS" -ForegroundColor Magenta

Test-Endpoint -Method GET -Endpoint "/settings" -Description "Settings"
Test-Endpoint -Method GET -Endpoint "/settings/api-config" -Description "API Config"
Test-Endpoint -Method GET -Endpoint "/schedules" -Description "All Schedules"
Test-Endpoint -Method GET -Endpoint "/schedules?limit=5" -Description "Schedules (limit 5)"
Test-Endpoint -Method GET -Endpoint "/tracking" -Description "All Tracking"
Test-Endpoint -Method GET -Endpoint "/tracking?limit=10" -Description "Tracking (limit 10)"
Test-Endpoint -Method GET -Endpoint "/services" -Description "All Services"
Test-Endpoint -Method GET -Endpoint "/ratings" -Description "All Ratings"

# ================================================================
# 3. DATA VALIDATION
# ================================================================
Write-Host "`n`n[3] DATA STRUCTURE VALIDATION" -ForegroundColor Magenta

Write-Host "`nChecking Schedules..." -ForegroundColor Yellow
$schedules = Test-Endpoint -Method GET -Endpoint "/schedules?limit=1" -Description "Schedule Structure"
if ($schedules -and $schedules.data -and $schedules.data.Count -gt 0) {
    Write-Host "Fields:" -ForegroundColor Gray
    $schedules.data[0].PSObject.Properties.Name | ForEach-Object { Write-Host "  - $_" -ForegroundColor DarkGray }
}

Write-Host "`nChecking Tracking..." -ForegroundColor Yellow
$tracking = Test-Endpoint -Method GET -Endpoint "/tracking?limit=1" -Description "Tracking Structure"
if ($tracking -and $tracking.data -and $tracking.data.Count -gt 0) {
    Write-Host "Fields:" -ForegroundColor Gray
    $tracking.data[0].PSObject.Properties.Name | ForEach-Object { Write-Host "  - $_" -ForegroundColor DarkGray }
    
    Write-Host "`nDecimal Check:" -ForegroundColor Yellow
    Write-Host "  Latitude: $($tracking.data[0].latitude)" -ForegroundColor Gray
    Write-Host "  Longitude: $($tracking.data[0].longitude)" -ForegroundColor Gray
}

Write-Host "`nChecking Services..." -ForegroundColor Yellow
$services = Test-Endpoint -Method GET -Endpoint "/services" -Description "Services Structure"
if ($services -and $services.data -and $services.data.Count -gt 0) {
    Write-Host "Fields:" -ForegroundColor Gray
    $services.data[0].PSObject.Properties.Name | ForEach-Object { Write-Host "  - $_" -ForegroundColor DarkGray }
}

# ================================================================
# 4. QUERY PARAMETERS
# ================================================================
Write-Host "`n`n[4] QUERY PARAMETERS" -ForegroundColor Magenta

Test-Endpoint -Method GET -Endpoint "/schedules?status=pending" -Description "Filter by Status"
Test-Endpoint -Method GET -Endpoint "/tracking?schedule_id=1" -Description "Filter by Schedule ID"
Test-Endpoint -Method GET -Endpoint "/tracking/schedule/1" -Description "Tracking History"

# ================================================================
# SUMMARY
# ================================================================
Write-Host "`n`n================================================================" -ForegroundColor Magenta
Write-Host "  SUMMARY" -ForegroundColor Magenta
Write-Host "================================================================" -ForegroundColor Magenta
Write-Host "Total: $($passCount + $failCount)" -ForegroundColor White
Write-Host "Passed: $passCount" -ForegroundColor Green
Write-Host "Failed: $failCount" -ForegroundColor Red

if ($passCount -gt 0) {
    $rate = [math]::Round(($passCount / ($passCount + $failCount)) * 100, 1)
    Write-Host "`nSuccess Rate: $rate%" -ForegroundColor Green
}

Write-Host "`n[NEXT STEPS]" -ForegroundColor Cyan
Write-Host "1. Run insert-fake-tracking-data.sql in phpMyAdmin" -ForegroundColor Yellow
Write-Host "2. Test again to see real data responses" -ForegroundColor Yellow
Write-Host "3. Test authenticated endpoints with token" -ForegroundColor Yellow

Write-Host "`n================================================================`n" -ForegroundColor Magenta
