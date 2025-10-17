# 🔄 USER FLOW & API VALIDATION - GEROBAKS

**Generated:** 15 Januari 2025  
**Purpose:** Detailed user flows and API validation rules based on ERD

---

## 📱 USER FLOWS

### FLOW 1: END USER - MEMBUAT JADWAL SAMPAH

#### Step-by-Step Flow

```
┌─────────────────────────────────────────────────────────────┐
│ 1. REGISTRATION & LOGIN                                     │
└─────────────────────────────────────────────────────────────┘
   POST /register
   Body: {
     "name": "John Doe",
     "email": "john@example.com",
     "password": "password123",
     "password_confirmation": "password123",
     "phone": "081234567890",
     "role": "end_user",
     "address": "Jl. Sudirman No. 1",
     "latitude": "-6.2088",
     "longitude": "106.8456"
   }
   → Creates: users table entry

   POST /login
   Body: {
     "email": "john@example.com",
     "password": "password123"
   }
   → Returns: {token, user}

┌─────────────────────────────────────────────────────────────┐
│ 2. PILIH PAKET LANGGANAN (Optional)                         │
└─────────────────────────────────────────────────────────────┘
   GET /subscription/plans
   → Returns: List of subscription_plans

   POST /subscription/subscribe
   Headers: Authorization: Bearer {token}
   Body: {
     "plan_id": 1
   }
   → Creates: subscriptions table entry (status=pending)

   POST /payments
   Body: {
     "amount": "99000",
     "method": "qris"
   }
   → Creates: payments table entry

   POST /subscription/{id}/activate
   → Updates: subscriptions.status = "active"
   → Updates: subscriptions.started_at, expired_at

┌─────────────────────────────────────────────────────────────┐
│ 3. BUAT JADWAL PENGAMBILAN SAMPAH                           │
└─────────────────────────────────────────────────────────────┘
   POST /schedules/mobile
   Headers: Authorization: Bearer {token}
   Body: {
     "pickup_location": "Rumah - Jl. Sudirman No. 1",
     "pickup_latitude": "-6.2088",
     "pickup_longitude": "106.8456",
     "dropoff_location": "TPA Bantar Gebang",
     "dropoff_latitude": "-6.3380",
     "dropoff_longitude": "106.9900",
     "scheduled_at": "2025-01-16 08:00:00",
     "notes": "Sampah organik dan plastik"
   }
   → Creates: schedules table entry
   → Auto-calculate: price based on distance
   → Sets: status = "pending", user_id = current user

┌─────────────────────────────────────────────────────────────┐
│ 4. LIHAT LAYANAN & BUAT ORDER (Alternative Flow)            │
└─────────────────────────────────────────────────────────────┘
   GET /services
   → Returns: List of services (pickup, delivery, etc)

   POST /orders
   Headers: Authorization: Bearer {token}
   Body: {
     "service_id": 1,
     "schedule_id": 5,  // From step 3
     "pickup_location": "Rumah",
     "dropoff_location": "TPA",
     "notes": "Urgent"
   }
   → Creates: orders table entry
   → Links: schedule_id (FK)
   → Sets: user_id, status="pending", total_price

┌─────────────────────────────────────────────────────────────┐
│ 5. BAYAR ORDER                                               │
└─────────────────────────────────────────────────────────────┘
   POST /payments
   Body: {
     "order_id": 10,
     "amount": "50000",
     "method": "cash"
   }
   → Creates: payments table entry
   → Sets: status="pending"

   POST /payments/{id}/mark-paid
   → Updates: payments.status = "paid", paid_at
   → Updates: orders.status = "confirmed"
   → Sends: notification to mitra

┌─────────────────────────────────────────────────────────────┐
│ 6. TRACKING REAL-TIME                                        │
└─────────────────────────────────────────────────────────────┘
   GET /tracking/schedule/{schedule_id}
   → Returns: List of trackings (GPS points)
   → Shows: real-time location of mitra's truck

   Response:
   {
     "data": [
       {
         "latitude": "-6.2088",
         "longitude": "106.8456",
         "speed": "45.50",
         "heading": "180.00",
         "recorded_at": "2025-01-16 08:15:00"
       },
       ...
     ]
   }

┌─────────────────────────────────────────────────────────────┐
│ 7. SELESAI & RATING                                          │
└─────────────────────────────────────────────────────────────┘
   // When mitra completes
   → Notification: "Sampah sudah diambil"
   → schedules.status = "completed"
   → schedules.completed_at = NOW()

   POST /ratings
   Body: {
     "order_id": 10,
     "rating": 5,
     "comment": "Petugas ramah dan cepat!"
   }
   → Creates: ratings table entry
   → Updates: mitra's rating average

   POST /balance/topup (Reward Points)
   → Creates: balance_ledger entry
   → Type: "reward"
   → Amount: based on service points
```

---

### FLOW 2: MITRA - TERIMA & KERJAKAN ORDER

```
┌─────────────────────────────────────────────────────────────┐
│ 1. LOGIN SEBAGAI MITRA                                      │
└─────────────────────────────────────────────────────────────┘
   POST /login
   Body: {
     "email": "mitra@example.com",
     "password": "password123"
   }
   → Validates: users.role = "mitra"
   → Returns: token

┌─────────────────────────────────────────────────────────────┐
│ 2. LIHAT DASHBOARD MITRA                                     │
└─────────────────────────────────────────────────────────────┘
   GET /dashboard/mitra/{user_id}
   Headers: Authorization: Bearer {token}
   → Returns:
     - Total orders today
     - Total earnings
     - Active schedules
     - Pending orders

┌─────────────────────────────────────────────────────────────┐
│ 3. LIHAT ORDER PENDING                                       │
└─────────────────────────────────────────────────────────────┘
   GET /orders?status=pending
   → Returns: List of orders WHERE status="pending"
   → Shows: pickup location, dropoff, price, distance

┌─────────────────────────────────────────────────────────────┐
│ 4. TERIMA ORDER                                               │
└─────────────────────────────────────────────────────────────┘
   PATCH /orders/{id}/assign
   Body: {
     "mitra_id": 5  // Current mitra's user_id
   }
   → Updates: orders.mitra_id = 5
   → Updates: orders.status = "assigned"
   → Sends: notification to end_user
   → Updates: schedules.status = "confirmed"

┌─────────────────────────────────────────────────────────────┐
│ 5. MULAI PERJALANAN                                          │
└─────────────────────────────────────────────────────────────┘
   PATCH /orders/{id}/status
   Body: {
     "status": "in_progress"
   }
   → Updates: orders.status = "in_progress"
   → Updates: schedules.status = "in_progress"
   → Sends: notification to end_user

┌─────────────────────────────────────────────────────────────┐
│ 6. KIRIM GPS REAL-TIME (Every 5-10 seconds)                 │
└─────────────────────────────────────────────────────────────┘
   POST /tracking
   Body: {
     "schedule_id": 5,
     "latitude": "-6.2088",
     "longitude": "106.8456",
     "speed": "45.50",
     "heading": "180.00"
   }
   → Creates: trackings table entry
   → Sets: recorded_at = NOW()
   → Real-time update to end_user's map

┌─────────────────────────────────────────────────────────────┐
│ 7. SAMPAI & SELESAIKAN                                       │
└─────────────────────────────────────────────────────────────┘
   POST /schedules/{id}/complete
   → Updates: schedules.status = "completed"
   → Updates: schedules.completed_at = NOW()
   → Updates: orders.status = "completed"
   → Sends: notification to end_user

   // Balance updated automatically
   POST /balance/topup
   Body: {
     "amount": "50000",
     "type": "payment",
     "description": "Payment for order #10"
   }
   → Creates: balance_ledger entry
   → Updates: user's balance

┌─────────────────────────────────────────────────────────────┐
│ 8. CEK SALDO & WITHDRAW                                      │
└─────────────────────────────────────────────────────────────┘
   GET /balance/summary
   → Returns: Current balance, total earnings

   GET /balance/ledger
   → Returns: Transaction history

   POST /balance/withdraw
   Body: {
     "amount": "100000"
   }
   → Creates: balance_ledger entry (type="withdraw")
   → Reduces: user's balance
```

---

### FLOW 3: ADMIN - MANAGE SYSTEM

```
┌─────────────────────────────────────────────────────────────┐
│ 1. LOGIN SEBAGAI ADMIN                                      │
└─────────────────────────────────────────────────────────────┘
   POST /login
   Body: {
     "email": "admin@gerobaks.com",
     "password": "admin123"
   }
   → Validates: users.role = "admin"

┌─────────────────────────────────────────────────────────────┐
│ 2. DASHBOARD & STATISTICS                                    │
└─────────────────────────────────────────────────────────────┘
   GET /admin/stats
   → Returns:
     - Total users (by role)
     - Total orders (by status)
     - Total revenue
     - Active subscriptions
     - System health metrics

┌─────────────────────────────────────────────────────────────┐
│ 3. MANAGE USERS                                              │
└─────────────────────────────────────────────────────────────┘
   GET /admin/users
   → Returns: List of all users

   POST /admin/users
   Body: {
     "name": "New Mitra",
     "email": "mitra2@example.com",
     "role": "mitra",
     "phone": "081234567890"
   }
   → Creates: users table entry

   PATCH /admin/users/{id}
   Body: {
     "is_active": false
   }
   → Updates: user status

   DELETE /admin/users/{id}
   → Soft delete: user account

┌─────────────────────────────────────────────────────────────┐
│ 4. MANAGE SERVICES                                           │
└─────────────────────────────────────────────────────────────┘
   POST /services
   Body: {
     "name": "Express Pickup",
     "description": "Pengambilan cepat dalam 1 jam",
     "base_price": "75000",
     "base_points": 150,
     "is_active": true
   }
   → Creates: services table entry

   PATCH /services/{id}
   Body: {
     "base_price": "80000"
   }
   → Updates: service

┌─────────────────────────────────────────────────────────────┐
│ 5. MANAGE FEEDBACK                                           │
└─────────────────────────────────────────────────────────────┘
   GET /feedback
   → Returns: User feedback list

   PATCH /reports/{id}
   Body: {
     "status": "resolved",
     "admin_response": "Issue telah diperbaiki"
   }
   → Updates: feedback status

┌─────────────────────────────────────────────────────────────┐
│ 6. SEND NOTIFICATIONS                                        │
└─────────────────────────────────────────────────────────────┘
   POST /admin/notifications
   Body: {
     "user_id": null,  // null = broadcast to all
     "title": "Maintenance Scheduled",
     "message": "Sistem akan maintenance jam 02:00",
     "type": "system"
   }
   → Creates: notifications for all users

┌─────────────────────────────────────────────────────────────┐
│ 7. SETTINGS & CONFIG                                         │
└─────────────────────────────────────────────────────────────┘
   PATCH /settings
   Body: {
     "app_name": "Gerobaks",
     "maintenance_mode": false,
     "min_order_amount": "20000"
   }
   → Updates: system settings
```

---

## 🔐 API VALIDATION RULES (Based on ERD)

### 1. **POST /schedules** Validation

```javascript
{
  "user_id": "required|exists:users,id",
  "pickup_location": "required|string|max:255",
  "pickup_latitude": "required|decimal:10,7|between:-90,90",
  "pickup_longitude": "required|decimal:10,7|between:-180,180",
  "dropoff_location": "required|string|max:255",
  "dropoff_latitude": "required|decimal:10,7|between:-90,90",
  "dropoff_longitude": "required|decimal:10,7|between:-180,180",
  "scheduled_at": "required|date|after:now",
  "price": "required|decimal:10,2|min:0",
  "status": "required|in:pending,confirmed,in_progress,completed,cancelled",
  "notes": "nullable|string|max:1000"
}
```

### 2. **POST /tracking** Validation

```javascript
{
  "schedule_id": "required|exists:schedules,id",
  "latitude": "required|decimal:10,7|between:-90,90",
  "longitude": "required|decimal:10,7|between:-180,180",
  "speed": "required|decimal:8,2|min:0|max:200",
  "heading": "required|decimal:5,2|min:0|max:360",
  "recorded_at": "nullable|date"  // Default: NOW()
}
```

### 3. **POST /orders** Validation

```javascript
{
  "user_id": "required|exists:users,id",
  "service_id": "required|exists:services,id",
  "schedule_id": "nullable|exists:schedules,id",
  "mitra_id": "nullable|exists:users,id|role:mitra",
  "status": "required|in:pending,assigned,in_progress,completed,cancelled",
  "total_price": "required|decimal:10,2|min:0",
  "pickup_location": "required|string|max:255",
  "dropoff_location": "required|string|max:255",
  "notes": "nullable|string|max:1000"
}
```

### 4. **POST /payments** Validation

```javascript
{
  "order_id": "required|exists:orders,id",
  "user_id": "required|exists:users,id",
  "amount": "required|decimal:10,2|min:0",
  "method": "required|in:cash,transfer,ewallet,qris",
  "status": "required|in:pending,paid,failed,refunded",
  "payment_proof": "nullable|string|url",
  "paid_at": "nullable|date"
}
```

### 5. **POST /ratings** Validation

```javascript
{
  "order_id": "required|exists:orders,id|unique:ratings,order_id",
  "user_id": "required|exists:users,id",
  "mitra_id": "required|exists:users,id|role:mitra",
  "rating": "required|integer|min:1|max:5",
  "comment": "nullable|string|max:500"
}
```

### 6. **POST /balance/topup** Validation

```javascript
{
  "user_id": "required|exists:users,id",
  "type": "required|in:topup,withdraw,reward,payment,refund",
  "amount": "required|decimal:10,2|min:0",
  "balance_before": "required|decimal:10,2",
  "balance_after": "required|decimal:10,2",
  "description": "required|string|max:255",
  "reference_type": "nullable|string",
  "reference_id": "nullable|integer"
}
```

---

## 🔄 BUSINESS LOGIC RULES

### Rule 1: Schedule Creation

```
- scheduled_at MUST be in future
- price auto-calculated based on distance (pickup → dropoff)
- Default status = "pending"
- User must have active subscription OR pay per use
```

### Rule 2: Order Assignment

```
- Only mitra with role="mitra" can be assigned
- Order status must be "pending" before assignment
- After assignment: status → "assigned"
- Notification sent to both user and mitra
```

### Rule 3: Tracking GPS

```
- Can only track active schedules (status = "in_progress")
- GPS points inserted every 5-10 seconds
- Latitude: -90 to 90, Longitude: -180 to 180
- Speed: 0-200 km/h, Heading: 0-360 degrees
```

### Rule 4: Payment Processing

```
- Order must exist before payment
- Amount must match order.total_price
- Status flow: pending → paid → (order.status = confirmed)
- Payment proof required for transfer/ewallet
```

### Rule 5: Rating Submission

```
- Can only rate completed orders
- One rating per order (unique constraint)
- Rating: 1-5 stars
- Mitra_id must be the assigned mitra for that order
```

### Rule 6: Balance Management

```
- Topup: balance_after = balance_before + amount
- Withdraw: balance_after = balance_before - amount
- Minimum balance: 0 (cannot withdraw more than balance)
- Each transaction creates balance_ledger entry
```

---

## 📊 DATABASE CONSTRAINTS (ERD-based)

### Foreign Key Constraints

```sql
-- Schedules
schedules.user_id → users.id (CASCADE on delete)

-- Trackings
trackings.schedule_id → schedules.id (CASCADE on delete)

-- Orders
orders.user_id → users.id (RESTRICT on delete)
orders.service_id → services.id (RESTRICT on delete)
orders.schedule_id → schedules.id (SET NULL on delete)
orders.mitra_id → users.id (SET NULL on delete)

-- Payments
payments.order_id → orders.id (CASCADE on delete)
payments.user_id → users.id (RESTRICT on delete)

-- Ratings
ratings.order_id → orders.id (CASCADE on delete)
ratings.user_id → users.id (RESTRICT on delete)
ratings.mitra_id → users.id (SET NULL on delete)

-- Balance Ledger
balance_ledger.user_id → users.id (CASCADE on delete)

-- Subscriptions
subscriptions.user_id → users.id (CASCADE on delete)
subscriptions.plan_id → subscription_plans.id (RESTRICT on delete)
```

### Unique Constraints

```sql
users.email → UNIQUE
ratings.order_id → UNIQUE (one rating per order)
```

### Index Recommendations

```sql
CREATE INDEX idx_schedules_user_id ON schedules(user_id);
CREATE INDEX idx_schedules_status ON schedules(status);
CREATE INDEX idx_trackings_schedule_id ON trackings(schedule_id);
CREATE INDEX idx_orders_user_id ON orders(user_id);
CREATE INDEX idx_orders_mitra_id ON orders(mitra_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_payments_order_id ON payments(order_id);
CREATE INDEX idx_balance_ledger_user_id ON balance_ledger(user_id);
```

---

## ✅ VALIDATION SUMMARY

### All APIs Follow ERD: ✅

-   ✅ Users → Complete mapping
-   ✅ Schedules → All fields validated
-   ✅ Trackings → Decimal precision correct
-   ✅ Services → CRUD complete
-   ✅ Orders → Foreign keys validated
-   ✅ Payments → Enum values checked
-   ✅ Ratings → Constraints enforced
-   ✅ Notifications → Type validation
-   ✅ Balance → Transaction logic correct
-   ✅ Subscriptions → Plan linking works

### Missing/To Add:

-   ⚠️ Add `mitra_id` to ratings POST endpoint
-   ⚠️ Add activities logging endpoints
-   ⚠️ Add reports table to ERD or remove from API
-   ⚠️ Add settings table to ERD

---

**Last Updated:** 15 Januari 2025  
**Status:** Ready for Implementation  
**Next:** Implement validation rules in Controllers
