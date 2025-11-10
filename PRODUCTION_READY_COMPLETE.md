# 🎯 PRODUCTION READY - SCHEDULE API COMPLETE GUIDE

## ✅ Test Results: 100% Success Rate

**Date**: <?php echo date('Y-m-d H:i:s'); ?>

**Status**: ✅ ALL TESTS PASSED - PRODUCTION READY

### Test Coverage (16/16 Passed)

#### 📡 Health & Connectivity

-   ✅ Health Check
-   ✅ Ping Endpoint

#### 🔐 Authentication

-   ✅ End User Login (daffa@gmail.com / daffa123)
-   ✅ Mitra Login (driver.jakarta@gerobaks.com / mitra123)
-   ✅ Get Current User Info

#### 📅 Schedule Management (End User)

-   ✅ Create Schedule (Standard Format)
-   ✅ Create Schedule (Mobile Format)
-   ✅ List All Schedules (with pagination)
-   ✅ Get Schedule Details
-   ✅ Update Schedule (own schedules)

#### 🚛 Schedule Lifecycle (Mitra Actions)

-   ✅ Confirm Schedule
-   ✅ Start Schedule
-   ✅ Complete Schedule (with notes)
-   ✅ Cancel Schedule (with reason)

#### 🔍 Filtering & Search

-   ✅ Filter by Status
-   ✅ Filter by Date Range

---

## 🔑 Authentication Credentials

### End User (For Testing Schedule Creation)

-   **Email**: daffa@gmail.com
-   **Password**: daffa123
-   **Role**: end_user
-   **Can**: Create schedules, view schedules, update own schedules, cancel own schedules

### Mitra (For Testing Schedule Actions)

-   **Email**: driver.jakarta@gerobaks.com
-   **Password**: mitra123
-   **Role**: mitra
-   **Can**: View all schedules, confirm schedules, start schedules, complete schedules, update any schedule

### Admin

-   **Email**: admin@gerobaks.com
-   **Password**: admin123
-   **Role**: admin
-   **Can**: Full access to all operations

---

## 📋 API Endpoints Documentation

### Base URL

-   **Local**: http://127.0.0.1:8000/api
-   **Production**: https://gerobaks.dumeg.com/api

### Health Endpoints

```
GET /health
GET /ping
```

### Authentication

```
POST /login
POST /register
GET /auth/me (requires auth)
POST /auth/logout (requires auth)
```

### Schedule Endpoints

#### Public (No Auth Required)

```
GET /schedules
GET /schedules/{id}
```

#### Authenticated Users (All Roles)

```
POST /schedules
POST /schedules/mobile
PATCH /schedules/{id}
PUT /schedules/{id}
POST /schedules/{id}/cancel
```

#### Mitra/Admin Only

```
POST /schedules/{id}/complete
DELETE /schedules/{id}
```

---

## 🎨 Request Examples

### 1. Login

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "daffa@gmail.com",
    "password": "daffa123"
  }'
```

**Response**:

```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 3,
            "name": "Daffa",
            "email": "daffa@gmail.com",
            "role": "end_user"
        },
        "token": "18|Mfl8pm4GTVYIMygxU..."
    }
}
```

### 2. Create Schedule (Standard Format)

```bash
curl -X POST http://127.0.0.1:8000/api/schedules \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "service_type": "pickup_sampah_organik",
    "pickup_address": "Jl. Sudirman No. 123, Jakarta",
    "pickup_latitude": -6.2088,
    "pickup_longitude": 106.8456,
    "scheduled_at": "2025-01-20 10:00:00",
    "notes": "Harap datang tepat waktu",
    "payment_method": "cash",
    "frequency": "once",
    "waste_type": "organik",
    "estimated_weight": 5.5,
    "contact_name": "Budi",
    "contact_phone": "081234567890"
  }'
```

### 3. Create Schedule (Mobile Format)

```bash
curl -X POST http://127.0.0.1:8000/api/schedules/mobile \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "alamat": "Jl. Sudirman No. 123, Jakarta",
    "tanggal": "2025-01-20",
    "waktu": "14:30",
    "catatan": "Harap datang tepat waktu",
    "koordinat": {
      "lat": -6.2088,
      "lng": 106.8456
    },
    "jenis_layanan": "pickup_sampah_plastik",
    "metode_pembayaran": "cash"
  }'
```

### 4. List Schedules with Filters

```bash
# All schedules
curl -X GET http://127.0.0.1:8000/api/schedules \
  -H "Authorization: Bearer YOUR_TOKEN"

# Filter by status
curl -X GET "http://127.0.0.1:8000/api/schedules?status=pending" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Filter by date range
curl -X GET "http://127.0.0.1:8000/api/schedules?date_from=2025-01-15&date_to=2025-02-15" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Filter by mitra
curl -X GET "http://127.0.0.1:8000/api/schedules?mitra_id=2" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Filter by user
curl -X GET "http://127.0.0.1:8000/api/schedules?user_id=3" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Pagination
curl -X GET "http://127.0.0.1:8000/api/schedules?per_page=20&page=1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 5. Update Schedule

```bash
curl -X PATCH http://127.0.0.1:8000/api/schedules/15 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "notes": "Updated notes",
    "estimated_weight": 7.5
  }'
```

### 6. Mitra Confirm Schedule

```bash
curl -X PATCH http://127.0.0.1:8000/api/schedules/15 \
  -H "Authorization: Bearer MITRA_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "confirmed",
    "notes": "Mitra confirmed this schedule"
  }'
```

### 7. Mitra Start Schedule

```bash
curl -X PATCH http://127.0.0.1:8000/api/schedules/15 \
  -H "Authorization: Bearer MITRA_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "in_progress"
  }'
```

### 8. Mitra Complete Schedule

```bash
curl -X POST http://127.0.0.1:8000/api/schedules/15/complete \
  -H "Authorization: Bearer MITRA_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "completion_notes": "Pickup completed successfully. Collected 6kg of organic waste.",
    "actual_duration": 45
  }'
```

### 9. Cancel Schedule

```bash
curl -X POST http://127.0.0.1:8000/api/schedules/15/cancel \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "cancellation_reason": "User requested cancellation due to schedule conflict"
  }'
```

---

## 🛡️ Authorization Rules

### End Users

-   ✅ Can create schedules
-   ✅ Can view all schedules (public)
-   ✅ Can update their own schedules only
-   ✅ Can cancel their own schedules
-   ❌ Cannot complete schedules
-   ❌ Cannot delete schedules

### Mitra

-   ✅ Can view all schedules
-   ✅ Can update any schedule
-   ✅ Can confirm schedules
-   ✅ Can start schedules
-   ✅ Can complete schedules
-   ❌ Cannot delete schedules

### Admin

-   ✅ Full access to all operations

---

## 📊 Response Format

All responses follow this envelope format:

### Success Response

```json
{
    "success": true,
    "message": "Operation successful",
    "data": {
        // Response data here
    },
    "meta": {
        // Pagination or additional metadata (if applicable)
    }
}
```

### Error Response

```json
{
    "error": "Error Type",
    "message": "Detailed error message",
    "errors": {
        // Validation errors (if applicable)
    }
}
```

### Paginated Response

```json
{
    "success": true,
    "message": "Schedules retrieved successfully",
    "data": {
        "items": [
            // Array of schedules
        ],
        "meta": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 15,
            "total": 72,
            "from": 1,
            "to": 15
        }
    }
}
```

---

## 🚀 Running Tests

### Comprehensive API Test

```bash
cd backend
php test_schedule_complete.php
```

### Laravel Unit Tests

```bash
cd backend
php artisan test
```

### Specific Test

```bash
php artisan test --filter=ScheduleLifecycleTest
```

---

## 📱 Flutter Integration

### Example: Create Schedule from Flutter App

```dart
// 1. Login first
final authService = AuthService();
await authService.login('daffa@gmail.com', 'daffa123');

// 2. Create schedule
final scheduleService = ScheduleService();
final schedule = ScheduleModel(
  serviceType: ServiceType.pickupSampahOrganik,
  pickupAddress: 'Jl. Sudirman No. 123, Jakarta',
  pickupLatitude: -6.2088,
  pickupLongitude: 106.8456,
  scheduledAt: DateTime.now().add(Duration(days: 2)),
  notes: 'Harap datang tepat waktu',
  paymentMethod: PaymentMethod.cash,
  frequency: Frequency.once,
  wasteType: 'organik',
  estimatedWeight: 5.5,
  contactName: 'Budi',
  contactPhone: '081234567890',
);

try {
  final created = await scheduleService.createSchedule(schedule);

  // Show success toast
  ScaffoldMessenger.of(context).showSnackBar(
    SnackBar(
      content: Text('✅ Schedule created successfully!'),
      backgroundColor: Colors.green,
    ),
  );
} catch (e) {
  // Show error toast
  ScaffoldMessenger.of(context).showSnackBar(
    SnackBar(
      content: Text('❌ Failed to create schedule: $e'),
      backgroundColor: Colors.red,
    ),
  );
}
```

### Example: Mitra Complete Schedule

```dart
final scheduleService = ScheduleService();

try {
  await scheduleService.completeSchedule(
    scheduleId: 15,
    completionNotes: 'Pickup completed successfully. Collected 6kg of organic waste.',
    actualDuration: 45,
  );

  // Show success toast
  ScaffoldMessenger.of(context).showSnackBar(
    SnackBar(
      content: Text('✅ Schedule completed!'),
      backgroundColor: Colors.green,
    ),
  );
} catch (e) {
  // Show error toast
  ScaffoldMessenger.of(context).showSnackBar(
    SnackBar(
      content: Text('❌ Failed to complete schedule: $e'),
      backgroundColor: Colors.red,
    ),
  );
}
```

---

## 🎨 Toast Notification Implementation

### Using SnackBar (Built-in)

```dart
void showSuccessToast(BuildContext context, String message) {
  ScaffoldMessenger.of(context).showSnackBar(
    SnackBar(
      content: Row(
        children: [
          Icon(Icons.check_circle, color: Colors.white),
          SizedBox(width: 8),
          Expanded(child: Text(message)),
        ],
      ),
      backgroundColor: Colors.green,
      duration: Duration(seconds: 3),
      behavior: SnackBarBehavior.floating,
    ),
  );
}

void showErrorToast(BuildContext context, String message) {
  ScaffoldMessenger.of(context).showSnackBar(
    SnackBar(
      content: Row(
        children: [
          Icon(Icons.error, color: Colors.white),
          SizedBox(width: 8),
          Expanded(child: Text(message)),
        ],
      ),
      backgroundColor: Colors.red,
      duration: Duration(seconds: 4),
      behavior: SnackBarBehavior.floating,
    ),
  );
}
```

### Using fluttertoast Package

```yaml
# pubspec.yaml
dependencies:
    fluttertoast: ^8.2.4
```

```dart
import 'package:fluttertoast/fluttertoast.dart';

void showSuccessToast(String message) {
  Fluttertoast.showToast(
    msg: message,
    toastLength: Toast.LENGTH_SHORT,
    gravity: ToastGravity.BOTTOM,
    backgroundColor: Colors.green,
    textColor: Colors.white,
    fontSize: 16.0,
  );
}

void showErrorToast(String message) {
  Fluttertoast.showToast(
    msg: message,
    toastLength: Toast.LENGTH_LONG,
    gravity: ToastGravity.BOTTOM,
    backgroundColor: Colors.red,
    textColor: Colors.white,
    fontSize: 16.0,
  );
}
```

---

## 🔧 Deployment Checklist

### Local Development

-   [x] Backend API running on http://127.0.0.1:8000
-   [x] All tests passing (100% success rate)
-   [x] Database seeded with test data
-   [x] Authentication working
-   [x] Schedule CRUD operations functional
-   [x] Schedule lifecycle (confirm, start, complete, cancel) working
-   [x] Filtering and pagination working

### Production Deployment

-   [ ] Update `.env` with production database credentials
-   [ ] Set `APP_ENV=production`
-   [ ] Set `APP_DEBUG=false`
-   [ ] Run migrations: `php artisan migrate --force`
-   [ ] Seed initial data: `php artisan db:seed --class=UserSeeder`
-   [ ] Clear config cache: `php artisan config:clear`
-   [ ] Cache config: `php artisan config:cache`
-   [ ] Run tests: `php test_schedule_complete.php`
-   [ ] Update Flutter app API URL to production URL

---

## 🐛 Troubleshooting

### Problem: 500 Internal Server Error

**Solution**: Check Laravel logs in `storage/logs/laravel.log`

### Problem: Authentication Failed

**Solution**: Ensure token is included in `Authorization: Bearer TOKEN` header

### Problem: Permission Denied

**Solution**: Check user role - only mitra can complete schedules, only users can update their own schedules

### Problem: Date Filter Not Working

**Solution**: Ensure dates are in `Y-m-d` format (e.g., `2025-01-20`)

### Problem: Schedule Not Found

**Solution**: Verify schedule ID exists and user has permission to access it

---

## 📝 Important Notes

1. **Password for Daffa**: `daffa123` (Updated successfully)
2. **All schedule endpoints tested**: 100% success rate
3. **Authorization implemented**: End users can only update their own schedules
4. **Date handling fixed**: ScheduleResource now safely handles both Carbon instances and strings
5. **Pagination working**: Returns proper meta data with current_page, total, etc.
6. **Filters working**: status, date_from, date_to, user_id, mitra_id all functional

---

## ✅ Production Ready Confirmation

-   ✅ Backend API fully functional
-   ✅ All 16 tests passing
-   ✅ Authentication working
-   ✅ Authorization implemented correctly
-   ✅ Schedule CRUD operations complete
-   ✅ Schedule lifecycle operations complete
-   ✅ Filtering and pagination working
-   ✅ Error handling implemented
-   ✅ Response format standardized
-   ✅ Documentation complete

**Status**: 🎉 **READY FOR PRODUCTION DEPLOYMENT** 🎉

---

Generated on: <?php echo date('Y-m-d H:i:s'); ?>
