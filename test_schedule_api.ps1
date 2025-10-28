# ====================================
# 🧪 Schedule API Test Script (PowerShell)
# ====================================
# Test dynamic schedule creation with additional wastes
#
# Usage:
#   .\test_schedule_api.ps1
#
# Requirements:
#   - PowerShell 5.1 or higher
#   - Valid API token
# ====================================

# Configuration
$ApiBaseUrl = "https://gerobaks.dumeg.com/api"
$Token = "YOUR_AUTH_TOKEN_HERE"  # ⚠️ REPLACE WITH ACTUAL TOKEN

# Helper functions for colored output
function Write-Success {
    param([string]$Message)
    Write-Host "✅ $Message" -ForegroundColor Green
}

function Write-Failure {
    param([string]$Message)
    Write-Host "❌ $Message" -ForegroundColor Red
}

function Write-Info {
    param([string]$Message)
    Write-Host "ℹ️  $Message" -ForegroundColor Cyan
}

function Write-Warning {
    param([string]$Message)
    Write-Host "⚠️  $Message" -ForegroundColor Yellow
}

# ====================================
# Test 1: Create Schedule with Additional Wastes
# ====================================
function Test-CreateSchedule {
    Write-Info "Test 1: Creating schedule with 2 additional wastes..."
    
    $body = @{
        service_type      = "Pengambilan Sampah"
        pickup_address    = "Jl. Sudirman No. 123, Jakarta Pusat"
        pickup_latitude   = -6.208763
        pickup_longitude  = 106.845599
        scheduled_at      = "2025-10-25 10:00:00"
        waste_type        = "Organik"
        estimated_weight  = 5.5
        contact_name      = "Budi Santoso"
        contact_phone     = "081234567890"
        frequency         = "weekly"
        is_paid           = $false
        notes             = "Sampah dari dapur restoran"
        additional_wastes = @(
            @{
                waste_type       = "Plastik"
                estimated_weight = 2.3
                notes            = "Botol plastik bekas minuman"
            },
            @{
                waste_type       = "Kertas"
                estimated_weight = 1.5
                notes            = "Kardus kemasan produk"
            }
        )
    } | ConvertTo-Json -Depth 10
    
    try {
        $response = Invoke-RestMethod -Uri "$ApiBaseUrl/schedules" `
            -Method Post `
            -Headers @{
            "Authorization" = "Bearer $Token"
            "Content-Type"  = "application/json"
            "Accept"        = "application/json"
        } `
            -Body $body
        
        if ($response.success) {
            Write-Success "Schedule created successfully!"
            Write-Info "Schedule ID: $($response.data.id)"
            $response | ConvertTo-Json -Depth 10
            return @{ Success = $true; ScheduleId = $response.data.id }
        }
        else {
            Write-Failure "Failed to create schedule"
            $response | ConvertTo-Json -Depth 10
            return @{ Success = $false }
        }
    }
    catch {
        Write-Failure "Error: $_"
        Write-Host $_.Exception.Response.StatusCode
        return @{ Success = $false }
    }
}

# ====================================
# Test 2: Create Simple Schedule (No Additional Wastes)
# ====================================
function Test-CreateSimpleSchedule {
    Write-Info "Test 2: Creating simple schedule (no additional wastes)..."
    
    $body = @{
        service_type     = "Pengambilan Sampah"
        pickup_address   = "Jl. Thamrin No. 45, Jakarta"
        pickup_latitude  = -6.195
        pickup_longitude = 106.822
        scheduled_at     = "2025-10-26 14:00:00"
        waste_type       = "Plastik"
        estimated_weight = 3.0
        frequency        = "once"
        notes            = "Sampah kantor"
    } | ConvertTo-Json
    
    try {
        $response = Invoke-RestMethod -Uri "$ApiBaseUrl/schedules" `
            -Method Post `
            -Headers @{
            "Authorization" = "Bearer $Token"
            "Content-Type"  = "application/json"
            "Accept"        = "application/json"
        } `
            -Body $body
        
        if ($response.success) {
            Write-Success "Simple schedule created!"
            $response | ConvertTo-Json -Depth 10
            return @{ Success = $true }
        }
        else {
            Write-Failure "Failed to create simple schedule"
            $response | ConvertTo-Json -Depth 10
            return @{ Success = $false }
        }
    }
    catch {
        Write-Failure "Error: $_"
        return @{ Success = $false }
    }
}

# ====================================
# Test 3: Get Schedule by ID
# ====================================
function Test-GetSchedule {
    param([int]$ScheduleId)
    
    Write-Info "Test 3: Fetching schedule ID: $ScheduleId..."
    
    try {
        $response = Invoke-RestMethod -Uri "$ApiBaseUrl/schedules/$ScheduleId" `
            -Method Get `
            -Headers @{
            "Authorization" = "Bearer $Token"
            "Accept"        = "application/json"
        }
        
        Write-Success "Schedule retrieved!"
        
        # Check if additional_wastes exists
        if ($response.data.additional_wastes) {
            Write-Success "Additional wastes included in response"
            Write-Info "Additional wastes count: $($response.data.additional_wastes.Count)"
        }
        else {
            Write-Warning "No additional_wastes in response"
        }
        
        $response | ConvertTo-Json -Depth 10
        return @{ Success = $true }
    }
    catch {
        Write-Failure "Error: $_"
        return @{ Success = $false }
    }
}

# ====================================
# Test 4: Validation Error Test
# ====================================
function Test-ValidationError {
    Write-Info "Test 4: Testing validation (missing required fields)..."
    
    $body = @{
        service_type = "Pengambilan Sampah"
    } | ConvertTo-Json
    
    try {
        $response = Invoke-RestMethod -Uri "$ApiBaseUrl/schedules" `
            -Method Post `
            -Headers @{
            "Authorization" = "Bearer $Token"
            "Content-Type"  = "application/json"
            "Accept"        = "application/json"
        } `
            -Body $body `
            -ErrorAction Stop
        
        Write-Warning "Expected validation error, but request succeeded"
        return @{ Success = $false }
    }
    catch {
        if ($_.Exception.Response.StatusCode -eq 422) {
            Write-Success "Validation error handled correctly! (422 Unprocessable Entity)"
            return @{ Success = $true }
        }
        else {
            Write-Failure "Unexpected error: $_"
            return @{ Success = $false }
        }
    }
}

# ====================================
# Test 5: List User Schedules
# ====================================
function Test-ListSchedules {
    param([int]$UserId = 1)
    
    Write-Info "Test 5: Listing schedules for user ID: $UserId..."
    
    try {
        $response = Invoke-RestMethod -Uri "$ApiBaseUrl/schedules?user_id=$UserId" `
            -Method Get `
            -Headers @{
            "Authorization" = "Bearer $Token"
            "Accept"        = "application/json"
        }
        
        Write-Success "Schedules list retrieved!"
        Write-Info "Found $($response.data.Count) schedules"
        
        $response | ConvertTo-Json -Depth 10
        return @{ Success = $true }
    }
    catch {
        Write-Failure "Error: $_"
        return @{ Success = $false }
    }
}

# ====================================
# Main Execution
# ====================================
function Main {
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Magenta
    Write-Host "   🧪 Schedule API Test Suite" -ForegroundColor Magenta
    Write-Host "========================================" -ForegroundColor Magenta
    Write-Host ""
    
    # Check if token is set
    if ($Token -eq "YOUR_AUTH_TOKEN_HERE") {
        Write-Failure "Please set your API token in the script!"
        Write-Info "Edit `$Token variable at the top of this file"
        exit 1
    }
    
    Write-Host ""
    
    # Run tests
    $passed = 0
    $failed = 0
    
    # Test 1
    $result1 = Test-CreateSchedule
    if ($result1.Success) { $passed++ } else { $failed++ }
    Write-Host ""
    
    # Test 2
    $result2 = Test-CreateSimpleSchedule
    if ($result2.Success) { $passed++ } else { $failed++ }
    Write-Host ""
    
    # Test 3 (if we have ScheduleId from Test 1)
    if ($result1.ScheduleId) {
        $result3 = Test-GetSchedule -ScheduleId $result1.ScheduleId
        if ($result3.Success) { $passed++ } else { $failed++ }
        Write-Host ""
    }
    
    # Test 4
    $result4 = Test-ValidationError
    if ($result4.Success) { $passed++ } else { $failed++ }
    Write-Host ""
    
    # Test 5
    $result5 = Test-ListSchedules -UserId 1
    if ($result5.Success) { $passed++ } else { $failed++ }
    Write-Host ""
    
    # Summary
    Write-Host "========================================" -ForegroundColor Magenta
    Write-Host "   📊 Test Summary" -ForegroundColor Magenta
    Write-Host "========================================" -ForegroundColor Magenta
    Write-Success "Passed: $passed"
    Write-Failure "Failed: $failed"
    Write-Host ""
    
    if ($failed -eq 0) {
        Write-Success "All tests passed! 🎉"
        exit 0
    }
    else {
        Write-Failure "Some tests failed. Check output above."
        exit 1
    }
}

# Run main function
Main
