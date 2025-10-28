#!/bin/bash

# ====================================
# 🧪 Schedule API Test Script
# ====================================
# Test dynamic schedule creation with additional wastes
#
# Usage:
#   bash test_schedule_api.sh
#
# Requirements:
#   - curl installed
#   - jq installed (for JSON formatting)
#   - Valid API token
# ====================================

# Configuration
API_BASE_URL="https://gerobaks.dumeg.com/api"
TOKEN="YOUR_AUTH_TOKEN_HERE"  # ⚠️ REPLACE WITH ACTUAL TOKEN

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Helper function to print colored output
print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

# ====================================
# Test 1: Create Schedule with Additional Wastes
# ====================================
test_create_schedule() {
    print_info "Test 1: Creating schedule with 2 additional wastes..."
    
    RESPONSE=$(curl -s -X POST "${API_BASE_URL}/schedules" \
        -H "Content-Type: application/json" \
        -H "Authorization: Bearer ${TOKEN}" \
        -H "Accept: application/json" \
        -d '{
            "service_type": "Pengambilan Sampah",
            "pickup_address": "Jl. Sudirman No. 123, Jakarta Pusat",
            "pickup_latitude": -6.208763,
            "pickup_longitude": 106.845599,
            "scheduled_at": "2025-10-25 10:00:00",
            "waste_type": "Organik",
            "estimated_weight": 5.5,
            "contact_name": "Budi Santoso",
            "contact_phone": "081234567890",
            "frequency": "weekly",
            "is_paid": false,
            "notes": "Sampah dari dapur restoran",
            "additional_wastes": [
                {
                    "waste_type": "Plastik",
                    "estimated_weight": 2.3,
                    "notes": "Botol plastik bekas minuman"
                },
                {
                    "waste_type": "Kertas",
                    "estimated_weight": 1.5,
                    "notes": "Kardus kemasan produk"
                }
            ]
        }')
    
    # Check if response contains success
    if echo "$RESPONSE" | grep -q '"success":true'; then
        print_success "Schedule created successfully!"
        
        # Extract schedule ID
        SCHEDULE_ID=$(echo "$RESPONSE" | grep -o '"id":[0-9]*' | head -1 | grep -o '[0-9]*')
        print_info "Schedule ID: ${SCHEDULE_ID}"
        
        # Pretty print response
        echo "$RESPONSE" | jq '.' 2>/dev/null || echo "$RESPONSE"
        
        return 0
    else
        print_error "Failed to create schedule"
        echo "$RESPONSE" | jq '.' 2>/dev/null || echo "$RESPONSE"
        return 1
    fi
}

# ====================================
# Test 2: Create Schedule WITHOUT Additional Wastes
# ====================================
test_create_simple_schedule() {
    print_info "Test 2: Creating simple schedule (no additional wastes)..."
    
    RESPONSE=$(curl -s -X POST "${API_BASE_URL}/schedules" \
        -H "Content-Type: application/json" \
        -H "Authorization: Bearer ${TOKEN}" \
        -H "Accept: application/json" \
        -d '{
            "service_type": "Pengambilan Sampah",
            "pickup_address": "Jl. Thamrin No. 45, Jakarta",
            "pickup_latitude": -6.195,
            "pickup_longitude": 106.822,
            "scheduled_at": "2025-10-26 14:00:00",
            "waste_type": "Plastik",
            "estimated_weight": 3.0,
            "frequency": "once",
            "notes": "Sampah kantor"
        }')
    
    if echo "$RESPONSE" | grep -q '"success":true'; then
        print_success "Simple schedule created!"
        echo "$RESPONSE" | jq '.' 2>/dev/null || echo "$RESPONSE"
        return 0
    else
        print_error "Failed to create simple schedule"
        echo "$RESPONSE" | jq '.' 2>/dev/null || echo "$RESPONSE"
        return 1
    fi
}

# ====================================
# Test 3: Get Schedule by ID
# ====================================
test_get_schedule() {
    local schedule_id=$1
    print_info "Test 3: Fetching schedule ID: ${schedule_id}..."
    
    RESPONSE=$(curl -s -X GET "${API_BASE_URL}/schedules/${schedule_id}" \
        -H "Authorization: Bearer ${TOKEN}" \
        -H "Accept: application/json")
    
    if echo "$RESPONSE" | grep -q '"id":'; then
        print_success "Schedule retrieved!"
        
        # Check if additional_wastes exists
        if echo "$RESPONSE" | grep -q '"additional_wastes":\['; then
            print_success "Additional wastes included in response"
        else
            print_warning "No additional_wastes in response (might be empty or not loaded)"
        fi
        
        echo "$RESPONSE" | jq '.' 2>/dev/null || echo "$RESPONSE"
        return 0
    else
        print_error "Failed to get schedule"
        echo "$RESPONSE" | jq '.' 2>/dev/null || echo "$RESPONSE"
        return 1
    fi
}

# ====================================
# Test 4: Validation Error Test
# ====================================
test_validation_error() {
    print_info "Test 4: Testing validation (missing required fields)..."
    
    RESPONSE=$(curl -s -X POST "${API_BASE_URL}/schedules" \
        -H "Content-Type: application/json" \
        -H "Authorization: Bearer ${TOKEN}" \
        -H "Accept: application/json" \
        -d '{
            "service_type": "Pengambilan Sampah"
        }')
    
    if echo "$RESPONSE" | grep -q '"errors":\|"message":'; then
        print_success "Validation error handled correctly!"
        echo "$RESPONSE" | jq '.' 2>/dev/null || echo "$RESPONSE"
        return 0
    else
        print_warning "Unexpected response for validation test"
        echo "$RESPONSE" | jq '.' 2>/dev/null || echo "$RESPONSE"
        return 1
    fi
}

# ====================================
# Test 5: List User Schedules
# ====================================
test_list_schedules() {
    local user_id=${1:-1}
    print_info "Test 5: Listing schedules for user ID: ${user_id}..."
    
    RESPONSE=$(curl -s -X GET "${API_BASE_URL}/schedules?user_id=${user_id}" \
        -H "Authorization: Bearer ${TOKEN}" \
        -H "Accept: application/json")
    
    if echo "$RESPONSE" | grep -q '"data":\['; then
        print_success "Schedules list retrieved!"
        
        # Count schedules
        COUNT=$(echo "$RESPONSE" | grep -o '"id":' | wc -l)
        print_info "Found ${COUNT} schedules"
        
        echo "$RESPONSE" | jq '.' 2>/dev/null || echo "$RESPONSE"
        return 0
    else
        print_error "Failed to list schedules"
        echo "$RESPONSE" | jq '.' 2>/dev/null || echo "$RESPONSE"
        return 1
    fi
}

# ====================================
# Main Execution
# ====================================
main() {
    echo ""
    echo "========================================"
    echo "   🧪 Schedule API Test Suite"
    echo "========================================"
    echo ""
    
    # Check if token is set
    if [ "$TOKEN" == "YOUR_AUTH_TOKEN_HERE" ]; then
        print_error "Please set your API token in the script!"
        print_info "Edit TOKEN variable at the top of this file"
        exit 1
    fi
    
    # Check if curl is installed
    if ! command -v curl &> /dev/null; then
        print_error "curl is not installed!"
        exit 1
    fi
    
    # Check if jq is installed (optional)
    if ! command -v jq &> /dev/null; then
        print_warning "jq is not installed (JSON output will not be formatted)"
        print_info "Install with: apt-get install jq (Linux) or brew install jq (Mac)"
    fi
    
    echo ""
    
    # Run tests
    PASSED=0
    FAILED=0
    
    # Test 1
    if test_create_schedule; then
        ((PASSED++))
    else
        ((FAILED++))
    fi
    echo ""
    
    # Test 2
    if test_create_simple_schedule; then
        ((PASSED++))
    else
        ((FAILED++))
    fi
    echo ""
    
    # Test 3 (if we have SCHEDULE_ID from Test 1)
    if [ ! -z "$SCHEDULE_ID" ]; then
        if test_get_schedule "$SCHEDULE_ID"; then
            ((PASSED++))
        else
            ((FAILED++))
        fi
        echo ""
    fi
    
    # Test 4
    if test_validation_error; then
        ((PASSED++))
    else
        ((FAILED++))
    fi
    echo ""
    
    # Test 5
    if test_list_schedules 1; then
        ((PASSED++))
    else
        ((FAILED++))
    fi
    echo ""
    
    # Summary
    echo "========================================"
    echo "   📊 Test Summary"
    echo "========================================"
    print_success "Passed: ${PASSED}"
    print_error "Failed: ${FAILED}"
    echo ""
    
    if [ $FAILED -eq 0 ]; then
        print_success "All tests passed! 🎉"
        exit 0
    else
        print_error "Some tests failed. Check output above."
        exit 1
    fi
}

# Run main function
main "$@"
