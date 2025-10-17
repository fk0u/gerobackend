# ================================================================

# DAFTAR LENGKAP SEMUA API ENDPOINT GEROBAKS

# ================================================================

# Dokumentasi lengkap untuk testing dan development

# Base URL: https://gerobaks.dumeg.com/api

# ================================================================

## 📋 DAFTAR ISI

-   [Health & System](#health--system)
-   [Authentication](#authentication)
-   [User Management](#user-management)
-   [Schedules](#schedules)
-   [Tracking](#tracking)
-   [Services](#services)
-   [Orders](#orders)
-   [Payments](#payments)
-   [Ratings](#ratings)
-   [Notifications](#notifications)
-   [Balance](#balance)
-   [Chat](#chat)
-   [Feedback](#feedback)
-   [Subscription](#subscription)
-   [Dashboard](#dashboard)
-   [Reports](#reports)
-   [Admin](#admin)
-   [Settings](#settings)

---

## 🏥 HEALTH & SYSTEM

### 1. Health Check

```bash
GET /api/health
# No auth required
# Response: {"status": "ok"}
```

### 2. Ping Check

```bash
GET /api/ping
# No auth required
# Response: {"status": "ok", "message": "Gerobaks API is running", ...}
```

**PowerShell Test:**

```powershell
Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/health"
Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/ping"
```

---

## 🔐 AUTHENTICATION

### 3. Login

```bash
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}

# Response: {"token": "...", "user": {...}}
```

### 4. Register

```bash
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "081234567890",
  "role": "end_user"
}
```

### 5. Get Current User

```bash
GET /api/auth/me
Authorization: Bearer {token}
```

### 6. Logout

```bash
POST /api/auth/logout
Authorization: Bearer {token}
```

**PowerShell Test:**

```powershell
# Login
$loginData = @{
    email = "test@example.com"
    password = "password123"
} | ConvertTo-Json

$response = Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/login" `
    -Method POST -Body $loginData -ContentType "application/json"

$token = $response.token

# Get current user
$headers = @{Authorization = "Bearer $token"}
Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/auth/me" -Headers $headers
```

---

## 👤 USER MANAGEMENT

### 7. Update Profile

```bash
POST /api/user/update-profile
Authorization: Bearer {token}

{
  "name": "New Name",
  "phone": "081234567890"
}
```

### 8. Change Password

```bash
POST /api/user/change-password
Authorization: Bearer {token}

{
  "current_password": "oldpass",
  "new_password": "newpass",
  "new_password_confirmation": "newpass"
}
```

### 9. Upload Profile Image

```bash
POST /api/user/upload-profile-image
Authorization: Bearer {token}
Content-Type: multipart/form-data

profile_image: <file>
```

---

## 📅 SCHEDULES

### 10. List All Schedules (Public)

```bash
GET /api/schedules
GET /api/schedules?limit=10&offset=0
GET /api/schedules?status=pending
GET /api/schedules?user_id=1
```

### 11. Get Schedule by ID

```bash
GET /api/schedules/{id}
```

### 12. Create Schedule (Mitra/Admin)

```bash
POST /api/schedules
Authorization: Bearer {token}
Role: mitra, admin

{
  "user_id": 1,
  "pickup_location": "Jakarta",
  "pickup_latitude": "-6.2088",
  "pickup_longitude": "106.8456",
  "dropoff_location": "Bogor",
  "dropoff_latitude": "-6.5950",
  "dropoff_longitude": "106.8166",
  "scheduled_at": "2025-01-15 10:00:00",
  "price": "150000"
}
```

### 13. Update Schedule (Mitra/Admin)

```bash
PATCH /api/schedules/{id}
Authorization: Bearer {token}
Role: mitra, admin

{
  "status": "confirmed",
  "price": "175000"
}
```

### 14. Complete Schedule

```bash
POST /api/schedules/{id}/complete
Authorization: Bearer {token}
Role: mitra, admin
```

### 15. Cancel Schedule

```bash
POST /api/schedules/{id}/cancel
Authorization: Bearer {token}
Role: mitra, admin

{
  "reason": "Customer request"
}
```

### 16. Create Schedule (Mobile - End User)

```bash
POST /api/schedules/mobile
Authorization: Bearer {token}
Role: end_user

{
  "pickup_location": "Jakarta Pusat",
  "dropoff_location": "Tangerang",
  "scheduled_at": "2025-01-15 14:00:00"
}
```

**PowerShell Test:**

```powershell
# Public list
Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/schedules?limit=5"

# Filter by status
Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/schedules?status=pending"

# Get by ID
Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/schedules/1"
```

---

## 📍 TRACKING

### 17. List All Tracking (Public)

```bash
GET /api/tracking
GET /api/tracking?limit=10
GET /api/tracking?schedule_id=1
```

### 18. Get Tracking History by Schedule

```bash
GET /api/tracking/schedule/{scheduleId}
```

### 19. Store Tracking (Mitra only)

```bash
POST /api/tracking
Authorization: Bearer {token}
Role: mitra

{
  "schedule_id": 1,
  "latitude": "-6.2088",
  "longitude": "106.8456",
  "speed": "45.50",
  "heading": "180.00"
}
```

**PowerShell Test:**

```powershell
# List all
Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/tracking?limit=10"

# By schedule
Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/tracking/schedule/1"

# With filter
Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/tracking?schedule_id=1&limit=50"
```

---

## 🛠️ SERVICES

### 20. List Services (Public)

```bash
GET /api/services
```

### 21. Create Service (Admin)

```bash
POST /api/services
Authorization: Bearer {token}
Role: admin

{
  "name": "Premium Transport",
  "description": "VIP service",
  "price": "500000"
}
```

### 22. Update Service (Admin)

```bash
PATCH /api/services/{id}
Authorization: Bearer {token}
Role: admin
```

---

## 🛒 ORDERS

### 23. List Orders

```bash
GET /api/orders
Authorization: Bearer {token}
```

### 24. Get Order by ID

```bash
GET /api/orders/{id}
Authorization: Bearer {token}
```

### 25. Create Order (End User)

```bash
POST /api/orders
Authorization: Bearer {token}
Role: end_user

{
  "service_id": 1,
  "pickup_location": "Jakarta",
  "dropoff_location": "Bekasi"
}
```

### 26. Cancel Order (End User)

```bash
POST /api/orders/{id}/cancel
Authorization: Bearer {token}
Role: end_user
```

### 27. Assign Order (Mitra)

```bash
PATCH /api/orders/{id}/assign
Authorization: Bearer {token}
Role: mitra

{
  "mitra_id": 5
}
```

### 28. Update Order Status (Mitra/Admin)

```bash
PATCH /api/orders/{id}/status
Authorization: Bearer {token}
Role: mitra, admin

{
  "status": "in_progress"
}
```

---

## 💰 PAYMENTS

### 29. List Payments

```bash
GET /api/payments
Authorization: Bearer {token}
```

### 30. Create Payment

```bash
POST /api/payments
Authorization: Bearer {token}

{
  "order_id": 1,
  "amount": "150000",
  "method": "cash"
}
```

### 31. Update Payment

```bash
PATCH /api/payments/{id}
Authorization: Bearer {token}
```

### 32. Mark as Paid

```bash
POST /api/payments/{id}/mark-paid
Authorization: Bearer {token}
```

---

## ⭐ RATINGS

### 33. List Ratings (Public)

```bash
GET /api/ratings
```

### 34. Create Rating (End User)

```bash
POST /api/ratings
Authorization: Bearer {token}
Role: end_user

{
  "order_id": 1,
  "rating": 5,
  "comment": "Excellent service!"
}
```

---

## 🔔 NOTIFICATIONS

### 35. List Notifications

```bash
GET /api/notifications
Authorization: Bearer {token}
```

### 36. Create Notification (Admin)

```bash
POST /api/notifications
Authorization: Bearer {token}
Role: admin

{
  "user_id": 1,
  "title": "New Order",
  "message": "You have a new order",
  "type": "order"
}
```

### 37. Mark as Read

```bash
POST /api/notifications/mark-read
Authorization: Bearer {token}

{
  "notification_ids": [1, 2, 3]
}
```

---

## 💵 BALANCE

### 38. Get Balance Ledger

```bash
GET /api/balance/ledger
Authorization: Bearer {token}
```

### 39. Get Balance Summary

```bash
GET /api/balance/summary
Authorization: Bearer {token}
```

### 40. Top Up

```bash
POST /api/balance/topup
Authorization: Bearer {token}

{
  "amount": "100000",
  "method": "bank_transfer"
}
```

### 41. Withdraw

```bash
POST /api/balance/withdraw
Authorization: Bearer {token}

{
  "amount": "50000"
}
```

---

## 💬 CHAT

### 42. List Chats

```bash
GET /api/chats
Authorization: Bearer {token}
```

### 43. Send Message

```bash
POST /api/chats
Authorization: Bearer {token}

{
  "receiver_id": 5,
  "message": "Hello!"
}
```

---

## 📝 FEEDBACK

### 44. List Feedback

```bash
GET /api/feedback
Authorization: Bearer {token}
```

### 45. Submit Feedback

```bash
POST /api/feedback
Authorization: Bearer {token}

{
  "subject": "App Issue",
  "message": "The map is not loading",
  "category": "bug"
}
```

---

## 📦 SUBSCRIPTION

### 46. List Subscription Plans

```bash
GET /api/subscription/plans
Authorization: Bearer {token}
```

### 47. Get Plan Details

```bash
GET /api/subscription/plans/{plan}
Authorization: Bearer {token}
```

### 48. Get Current Subscription

```bash
GET /api/subscription/current
Authorization: Bearer {token}
```

### 49. Subscribe

```bash
POST /api/subscription/subscribe
Authorization: Bearer {token}

{
  "plan_id": 1
}
```

### 50. Activate Subscription

```bash
POST /api/subscription/{subscription}/activate
Authorization: Bearer {token}
```

### 51. Cancel Subscription

```bash
POST /api/subscription/{subscription}/cancel
Authorization: Bearer {token}
```

### 52. Subscription History

```bash
GET /api/subscription/history
Authorization: Bearer {token}
```

---

## 📊 DASHBOARD

### 53. Mitra Dashboard

```bash
GET /api/dashboard/mitra/{id}
Authorization: Bearer {token}
Role: mitra, admin
```

### 54. User Dashboard

```bash
GET /api/dashboard/user/{id}
Authorization: Bearer {token}
Role: end_user, admin
```

---

## 📄 REPORTS

### 55. List Reports

```bash
GET /api/reports
Authorization: Bearer {token}
```

### 56. Create Report

```bash
POST /api/reports
Authorization: Bearer {token}

{
  "title": "Monthly Revenue",
  "type": "revenue",
  "period": "2025-01"
}
```

### 57. Get Report Details

```bash
GET /api/reports/{id}
Authorization: Bearer {token}
```

### 58. Update Report (Admin)

```bash
PATCH /api/reports/{id}
Authorization: Bearer {token}
Role: admin
```

---

## 🔧 ADMIN

### 59. Get Statistics

```bash
GET /api/admin/stats
Authorization: Bearer {token}
Role: admin
```

### 60. List Users

```bash
GET /api/admin/users
Authorization: Bearer {token}
Role: admin
```

### 61. Create User

```bash
POST /api/admin/users
Authorization: Bearer {token}
Role: admin
```

### 62. Update User

```bash
PATCH /api/admin/users/{id}
Authorization: Bearer {token}
Role: admin
```

### 63. Delete User

```bash
DELETE /api/admin/users/{id}
Authorization: Bearer {token}
Role: admin
```

### 64. Get Logs

```bash
GET /api/admin/logs
Authorization: Bearer {token}
Role: admin
```

### 65. Export Data

```bash
GET /api/admin/export
Authorization: Bearer {token}
Role: admin
```

### 66. Send Notification

```bash
POST /api/admin/notifications
Authorization: Bearer {token}
Role: admin
```

### 67. System Health

```bash
GET /api/admin/health
Authorization: Bearer {token}
Role: admin
```

---

## ⚙️ SETTINGS

### 68. Get Settings (Public)

```bash
GET /api/settings
```

### 69. Get API Config (Public)

```bash
GET /api/settings/api-config
```

### 70. Update Settings (Admin)

```bash
PATCH /api/settings
Authorization: Bearer {token}
Role: admin

{
  "app_name": "Gerobaks",
  "maintenance_mode": false
}
```

---

## 🧪 TESTING GUIDE

### Run Comprehensive Test

```powershell
# Test all endpoints
.\test-all-endpoints.ps1

# Test specific endpoint
Invoke-RestMethod -Uri "https://gerobaks.dumeg.com/api/schedules"
```

### Insert Fake Data

```sql
-- Run in phpMyAdmin
source insert-fake-tracking-data.sql
```

### Check Logs

```bash
# On server
tail -f storage/logs/laravel.log
```

---

## 📌 SUMMARY

**Total Endpoints:** 70+

**Public (No Auth):** 10

-   Health, Ping, Settings, Schedules (read), Tracking (read), Services (read), Ratings (read)

**Authenticated:** 60+

-   User management, Orders, Payments, Notifications, Balance, Chat, Feedback, Subscriptions, Dashboard, Reports, Admin

**Role-Based:**

-   **End User:** Orders, Ratings, Mobile Schedules, Subscriptions
-   **Mitra:** Tracking (write), Order assignment, Schedule management
-   **Admin:** All operations, User management, System settings

---

## 🚀 QUICK START

1. **Test Health:**

    ```powershell
    Invoke-RestMethod https://gerobaks.dumeg.com/api/health
    ```

2. **Run Full Test:**

    ```powershell
    .\test-all-endpoints.ps1
    ```

3. **Insert Fake Data:**

    - Open phpMyAdmin
    - Run `insert-fake-tracking-data.sql`

4. **Test with Data:**
    ```powershell
    Invoke-RestMethod https://gerobaks.dumeg.com/api/tracking?limit=10
    ```

---

**Last Updated:** January 14, 2025
**API Version:** 1.0
**Base URL:** https://gerobaks.dumeg.com/api
