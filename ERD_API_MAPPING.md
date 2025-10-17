# 🗄️ ERD ANALYSIS & API MAPPING - GEROBAKS

**Generated:** 15 Januari 2025  
**Purpose:** Memastikan API endpoints sesuai dengan ERD dan user flow

---

## 📊 DATABASE STRUCTURE (Dari ERD)

Berdasarkan ERD yang diberikan, berikut adalah struktur database lengkap:

### 🔵 CORE TABLES

#### 1. **users** (Central Entity)

**Relationships:** Hub utama untuk semua tabel

```
Columns:
- id (PK)
- name
- email (unique)
- password
- phone
- role (enum: end_user, mitra, admin)
- profile_image
- address
- latitude
- longitude
- is_active
- remember_token
- email_verified_at
- created_at
- updated_at

Relationships:
→ HAS MANY schedules (as user)
→ HAS MANY trackings (through schedules)
→ HAS MANY orders
→ HAS MANY payments
→ HAS MANY ratings
→ HAS MANY notifications
→ HAS MANY balance_ledger
→ HAS MANY chats (sender/receiver)
→ HAS MANY feedback
→ HAS MANY subscriptions
→ HAS MANY activities
```

#### 2. **schedules** (Jadwal Pengambilan)

```
Columns:
- id (PK)
- user_id (FK → users.id)
- pickup_location
- pickup_latitude (DECIMAL 10,7)
- pickup_longitude (DECIMAL 10,7)
- dropoff_location
- dropoff_latitude (DECIMAL 10,7)
- dropoff_longitude (DECIMAL 10,7)
- scheduled_at (datetime)
- completed_at (datetime, nullable)
- status (enum: pending, confirmed, in_progress, completed, cancelled)
- price (DECIMAL 10,2)
- notes (text, nullable)
- created_at
- updated_at

Relationships:
→ BELONGS TO user
→ HAS MANY trackings
→ HAS ONE order
```

#### 3. **trackings** (GPS Real-time)

```
Columns:
- id (PK)
- schedule_id (FK → schedules.id)
- latitude (DECIMAL 10,7)
- longitude (DECIMAL 10,7)
- speed (DECIMAL 8,2)
- heading (DECIMAL 5,2)
- recorded_at (datetime)
- created_at
- updated_at

Relationships:
→ BELONGS TO schedule
→ BELONGS TO user (through schedule)
```

#### 4. **services** (Jenis Layanan)

```
Columns:
- id (PK)
- name
- description
- base_price (DECIMAL 10,2)
- base_points (integer)
- is_active (boolean)
- created_at
- updated_at

Relationships:
→ HAS MANY orders
```

#### 5. **orders** (Pesanan)

```
Columns:
- id (PK)
- user_id (FK → users.id)
- service_id (FK → services.id)
- schedule_id (FK → schedules.id, nullable)
- mitra_id (FK → users.id, nullable)
- status (enum: pending, assigned, in_progress, completed, cancelled)
- total_price (DECIMAL 10,2)
- pickup_location
- dropoff_location
- notes (text, nullable)
- created_at
- updated_at

Relationships:
→ BELONGS TO user (customer)
→ BELONGS TO mitra (user with role=mitra)
→ BELONGS TO service
→ BELONGS TO schedule (optional)
→ HAS MANY payments
→ HAS ONE rating
```

#### 6. **payments** (Pembayaran)

```
Columns:
- id (PK)
- order_id (FK → orders.id)
- user_id (FK → users.id)
- amount (DECIMAL 10,2)
- method (enum: cash, transfer, ewallet, qris)
- status (enum: pending, paid, failed, refunded)
- payment_proof (string, nullable)
- paid_at (datetime, nullable)
- created_at
- updated_at

Relationships:
→ BELONGS TO order
→ BELONGS TO user
```

#### 7. **ratings** (Rating & Review)

```
Columns:
- id (PK)
- order_id (FK → orders.id)
- user_id (FK → users.id)
- mitra_id (FK → users.id)
- rating (integer 1-5)
- comment (text, nullable)
- created_at
- updated_at

Relationships:
→ BELONGS TO order
→ BELONGS TO user (reviewer)
→ BELONGS TO mitra
```

#### 8. **notifications** (Notifikasi)

```
Columns:
- id (PK)
- user_id (FK → users.id)
- title
- message
- type (enum: info, warning, success, order, payment, system)
- is_read (boolean)
- read_at (datetime, nullable)
- data (json, nullable)
- created_at
- updated_at

Relationships:
→ BELONGS TO user
```

#### 9. **balance_ledger** (Saldo & Transaksi)

```
Columns:
- id (PK)
- user_id (FK → users.id)
- type (enum: topup, withdraw, reward, payment, refund)
- amount (DECIMAL 10,2)
- balance_before (DECIMAL 10,2)
- balance_after (DECIMAL 10,2)
- description (text)
- reference_type (string, nullable) - morphable
- reference_id (bigint, nullable)
- created_at
- updated_at

Relationships:
→ BELONGS TO user
→ MORPH TO reference (order/payment/etc)
```

#### 10. **chats** (Pesan)

```
Columns:
- id (PK)
- sender_id (FK → users.id)
- receiver_id (FK → users.id)
- message (text)
- is_read (boolean)
- read_at (datetime, nullable)
- created_at
- updated_at

Relationships:
→ BELONGS TO sender (user)
→ BELONGS TO receiver (user)
```

#### 11. **feedback** (Feedback Sistem)

```
Columns:
- id (PK)
- user_id (FK → users.id)
- subject
- message (text)
- category (enum: bug, feature, complaint, suggestion)
- status (enum: pending, reviewed, resolved, closed)
- admin_response (text, nullable)
- created_at
- updated_at

Relationships:
→ BELONGS TO user
```

#### 12. **subscription_plans** (Paket Langganan)

```
Columns:
- id (PK)
- name
- description
- price (DECIMAL 10,2)
- duration_days (integer)
- features (json)
- is_active (boolean)
- created_at
- updated_at

Relationships:
→ HAS MANY subscriptions
```

#### 13. **subscriptions** (Langganan User)

```
Columns:
- id (PK)
- user_id (FK → users.id)
- plan_id (FK → subscription_plans.id)
- status (enum: pending, active, expired, cancelled)
- started_at (datetime, nullable)
- expired_at (datetime, nullable)
- auto_renew (boolean)
- created_at
- updated_at

Relationships:
→ BELONGS TO user
→ BELONGS TO plan
```

#### 14. **activities** (Log Aktivitas)

```
Columns:
- id (PK)
- user_id (FK → users.id)
- type (enum: login, order, payment, etc)
- description
- ip_address (nullable)
- user_agent (nullable)
- created_at
- updated_at

Relationships:
→ BELONGS TO user
→ HAS MANY activity_details
```

#### 15. **activity_details** (Detail Aktivitas)

```
Columns:
- id (PK)
- activity_id (FK → activities.id)
- key
- value
- created_at
- updated_at

Relationships:
→ BELONGS TO activity
```

---

## 🔗 RELATIONSHIP SUMMARY

```
users (1) ──────→ (*) schedules
users (1) ──────→ (*) orders
users (1) ──────→ (*) payments
users (1) ──────→ (*) ratings
users (1) ──────→ (*) notifications
users (1) ──────→ (*) balance_ledger
users (1) ──────→ (*) chats (sender)
users (1) ──────→ (*) chats (receiver)
users (1) ──────→ (*) feedback
users (1) ──────→ (*) subscriptions
users (1) ──────→ (*) activities

schedules (1) ───→ (*) trackings
schedules (1) ───→ (1) orders

services (1) ────→ (*) orders

orders (1) ──────→ (*) payments
orders (1) ──────→ (1) ratings

subscription_plans (1) → (*) subscriptions

activities (1) ──→ (*) activity_details
```

---

## 📍 API ENDPOINT MAPPING TO ERD

### ✅ USERS TABLE

| Endpoint                     | Method | Purpose          | ERD Match                       |
| ---------------------------- | ------ | ---------------- | ------------------------------- |
| `/auth/me`                   | GET    | Get current user | ✅ users.\*                     |
| `/user/update-profile`       | POST   | Update user data | ✅ users (name, phone, address) |
| `/user/change-password`      | POST   | Update password  | ✅ users.password               |
| `/user/upload-profile-image` | POST   | Update avatar    | ✅ users.profile_image          |

### ✅ SCHEDULES TABLE

| Endpoint                   | Method | Purpose           | ERD Match                         |
| -------------------------- | ------ | ----------------- | --------------------------------- |
| `/schedules`               | GET    | List schedules    | ✅ schedules.\*                   |
| `/schedules/{id}`          | GET    | Show schedule     | ✅ schedules.\*                   |
| `/schedules`               | POST   | Create schedule   | ✅ INSERT schedules               |
| `/schedules/{id}`          | PATCH  | Update schedule   | ✅ UPDATE schedules               |
| `/schedules/{id}/complete` | POST   | Complete schedule | ✅ schedules.status, completed_at |
| `/schedules/{id}/cancel`   | POST   | Cancel schedule   | ✅ schedules.status               |
| `/schedules/mobile`        | POST   | Create (mobile)   | ✅ INSERT schedules               |

**Required Fields (from ERD):**

-   ✅ user_id (FK)
-   ✅ pickup_location, pickup_latitude, pickup_longitude
-   ✅ dropoff_location, dropoff_latitude, dropoff_longitude
-   ✅ scheduled_at
-   ✅ price (DECIMAL 10,2)
-   ✅ status (enum)

### ✅ TRACKINGS TABLE

| Endpoint                  | Method | Purpose              | ERD Match                      |
| ------------------------- | ------ | -------------------- | ------------------------------ |
| `/tracking`               | GET    | List all tracking    | ✅ trackings.\*                |
| `/tracking/schedule/{id}` | GET    | Tracking by schedule | ✅ trackings WHERE schedule_id |
| `/tracking`               | POST   | Store GPS point      | ✅ INSERT trackings            |

**Required Fields (from ERD):**

-   ✅ schedule_id (FK)
-   ✅ latitude (DECIMAL 10,7)
-   ✅ longitude (DECIMAL 10,7)
-   ✅ speed (DECIMAL 8,2)
-   ✅ heading (DECIMAL 5,2)
-   ✅ recorded_at

### ✅ SERVICES TABLE

| Endpoint         | Method | Purpose        | ERD Match          |
| ---------------- | ------ | -------------- | ------------------ |
| `/services`      | GET    | List services  | ✅ services.\*     |
| `/services`      | POST   | Create service | ✅ INSERT services |
| `/services/{id}` | PATCH  | Update service | ✅ UPDATE services |

**Required Fields (from ERD):**

-   ✅ name
-   ✅ description
-   ✅ base_price (DECIMAL 10,2)
-   ✅ base_points (integer)
-   ✅ is_active (boolean)

### ✅ ORDERS TABLE

| Endpoint              | Method | Purpose       | ERD Match          |
| --------------------- | ------ | ------------- | ------------------ |
| `/orders`             | GET    | List orders   | ✅ orders.\*       |
| `/orders/{id}`        | GET    | Show order    | ✅ orders.\*       |
| `/orders`             | POST   | Create order  | ✅ INSERT orders   |
| `/orders/{id}/cancel` | POST   | Cancel order  | ✅ orders.status   |
| `/orders/{id}/assign` | PATCH  | Assign mitra  | ✅ orders.mitra_id |
| `/orders/{id}/status` | PATCH  | Update status | ✅ orders.status   |

**Required Fields (from ERD):**

-   ✅ user_id (FK)
-   ✅ service_id (FK)
-   ❓ schedule_id (FK, nullable) - **MISSING in current API?**
-   ❓ mitra_id (FK, nullable)
-   ✅ total_price (DECIMAL 10,2)
-   ✅ status (enum)

### ✅ PAYMENTS TABLE

| Endpoint                   | Method | Purpose        | ERD Match                   |
| -------------------------- | ------ | -------------- | --------------------------- |
| `/payments`                | GET    | List payments  | ✅ payments.\*              |
| `/payments`                | POST   | Create payment | ✅ INSERT payments          |
| `/payments/{id}`           | PATCH  | Update payment | ✅ UPDATE payments          |
| `/payments/{id}/mark-paid` | POST   | Mark as paid   | ✅ payments.status, paid_at |

**Required Fields (from ERD):**

-   ✅ order_id (FK)
-   ✅ user_id (FK)
-   ✅ amount (DECIMAL 10,2)
-   ✅ method (enum)
-   ✅ status (enum)

### ✅ RATINGS TABLE

| Endpoint   | Method | Purpose       | ERD Match         |
| ---------- | ------ | ------------- | ----------------- |
| `/ratings` | GET    | List ratings  | ✅ ratings.\*     |
| `/ratings` | POST   | Create rating | ✅ INSERT ratings |

**Required Fields (from ERD):**

-   ✅ order_id (FK)
-   ✅ user_id (FK)
-   ❓ mitra_id (FK) - **MISSING in current API?**
-   ✅ rating (integer 1-5)
-   ✅ comment (text)

### ✅ NOTIFICATIONS TABLE

| Endpoint                   | Method | Purpose             | ERD Match                         |
| -------------------------- | ------ | ------------------- | --------------------------------- |
| `/notifications`           | GET    | List notifications  | ✅ notifications.\*               |
| `/notifications`           | POST   | Create notification | ✅ INSERT notifications           |
| `/notifications/mark-read` | POST   | Mark as read        | ✅ notifications.is_read, read_at |

**Required Fields (from ERD):**

-   ✅ user_id (FK)
-   ✅ title
-   ✅ message
-   ✅ type (enum)

### ✅ BALANCE_LEDGER TABLE

| Endpoint            | Method | Purpose             | ERD Match                |
| ------------------- | ------ | ------------------- | ------------------------ |
| `/balance/ledger`   | GET    | Transaction history | ✅ balance_ledger.\*     |
| `/balance/summary`  | GET    | Balance summary     | ✅ SUM(balance_ledger)   |
| `/balance/topup`    | POST   | Top up balance      | ✅ INSERT balance_ledger |
| `/balance/withdraw` | POST   | Withdraw balance    | ✅ INSERT balance_ledger |

**Required Fields (from ERD):**

-   ✅ user_id (FK)
-   ✅ type (enum)
-   ✅ amount (DECIMAL 10,2)
-   ✅ balance_before (DECIMAL 10,2)
-   ✅ balance_after (DECIMAL 10,2)

### ✅ CHATS TABLE

| Endpoint | Method | Purpose       | ERD Match       |
| -------- | ------ | ------------- | --------------- |
| `/chats` | GET    | List messages | ✅ chats.\*     |
| `/chats` | POST   | Send message  | ✅ INSERT chats |

**Required Fields (from ERD):**

-   ✅ sender_id (FK)
-   ✅ receiver_id (FK)
-   ✅ message (text)

### ✅ FEEDBACK TABLE

| Endpoint    | Method | Purpose         | ERD Match          |
| ----------- | ------ | --------------- | ------------------ |
| `/feedback` | GET    | List feedback   | ✅ feedback.\*     |
| `/feedback` | POST   | Submit feedback | ✅ INSERT feedback |

**Required Fields (from ERD):**

-   ✅ user_id (FK)
-   ✅ subject
-   ✅ message (text)
-   ✅ category (enum)

### ✅ SUBSCRIPTIONS & PLANS

| Endpoint                      | Method | Purpose              | ERD Match                           |
| ----------------------------- | ------ | -------------------- | ----------------------------------- |
| `/subscription/plans`         | GET    | List plans           | ✅ subscription_plans.\*            |
| `/subscription/current`       | GET    | Current subscription | ✅ subscriptions WHERE user_id      |
| `/subscription/subscribe`     | POST   | Create subscription  | ✅ INSERT subscriptions             |
| `/subscription/{id}/activate` | POST   | Activate             | ✅ subscriptions.status, started_at |
| `/subscription/{id}/cancel`   | POST   | Cancel               | ✅ subscriptions.status             |

---

## ⚠️ MISSING/INCOMPLETE MAPPINGS

### 1. **Reports** Table

❌ **NOT in ERD** but exists in API

-   Endpoint: `/reports/*`
-   **Action Required:** Add reports table to ERD or remove from API

### 2. **Admin Stats**

❓ **Aggregation endpoint** - no specific table

-   Endpoint: `/admin/stats`
-   **Source:** Aggregates from multiple tables (users, orders, payments)

### 3. **Settings**

❓ **Configuration table missing** from ERD

-   Endpoint: `/settings`, `/settings/api-config`
-   **Recommendation:** Add `settings` or `configurations` table to ERD

### 4. **Activities** Table

✅ **Exists in ERD** but NO API endpoints

-   **Missing Endpoints:**
    -   `GET /activities` - List user activities
    -   `GET /activities/{id}` - Show activity details
-   **Recommendation:** Add activity logging endpoints

---

## 🔄 USER FLOW MAPPING

### Flow 1: End User - Order Creation

```
1. User Login → users (authenticate)
2. Browse Services → services (list)
3. Create Schedule → schedules (INSERT)
4. Create Order → orders (INSERT with schedule_id)
5. Make Payment → payments (INSERT)
6. Track Delivery → trackings (real-time)
7. Rate Service → ratings (INSERT)
```

**API Sequence:**

```
POST /login
GET /services
POST /schedules/mobile
POST /orders (body: {service_id, schedule_id})
POST /payments
GET /tracking/schedule/{schedule_id}
POST /ratings
```

### Flow 2: Mitra - Accept & Complete Order

```
1. Mitra Login → users (role=mitra)
2. View Orders → orders (WHERE status=pending)
3. Accept Order → orders (UPDATE mitra_id, status=assigned)
4. Start Journey → orders (status=in_progress)
5. Send GPS → trackings (INSERT continuously)
6. Complete → schedules (UPDATE completed_at)
7. Receive Payment → balance_ledger (INSERT)
```

**API Sequence:**

```
POST /login
GET /orders?status=pending
PATCH /orders/{id}/assign (body: {mitra_id})
PATCH /orders/{id}/status (body: {status: "in_progress"})
POST /tracking (body: {schedule_id, lat, lng, speed, heading})
POST /schedules/{id}/complete
GET /balance/summary
```

### Flow 3: Subscription Purchase

```
1. View Plans → subscription_plans
2. Choose Plan → subscription_plans/{id}
3. Create Subscription → subscriptions (INSERT)
4. Make Payment → payments (INSERT)
5. Activate → subscriptions (UPDATE status=active)
```

**API Sequence:**

```
GET /subscription/plans
GET /subscription/plans/{id}
POST /subscription/subscribe (body: {plan_id})
POST /payments (body: {amount, method})
POST /subscription/{id}/activate
```

---

## ✅ RECOMMENDATIONS

### 1. Database Consistency

-   ✅ All tables match ERD structure
-   ⚠️ Add `reports` table to ERD if needed
-   ⚠️ Add `settings` table to ERD
-   ⚠️ Add activity logging endpoints

### 2. API Completeness

-   ✅ All major CRUD operations covered
-   ❓ Add `mitra_id` to ratings POST endpoint
-   ❓ Expose `schedule_id` in orders creation
-   ❓ Add activity logging endpoints

### 3. Foreign Key Validation

Ensure API validates:

-   ✅ `user_id` exists in users
-   ✅ `schedule_id` exists in schedules
-   ✅ `service_id` exists in services
-   ✅ `order_id` exists in orders
-   ❓ `mitra_id` is user with role='mitra'

### 4. Enum Validation

Ensure API validates enum values:

-   ✅ users.role → `end_user|mitra|admin`
-   ✅ schedules.status → `pending|confirmed|in_progress|completed|cancelled`
-   ✅ orders.status → `pending|assigned|in_progress|completed|cancelled`
-   ✅ payments.method → `cash|transfer|ewallet|qris`
-   ✅ payments.status → `pending|paid|failed|refunded`
-   ✅ notifications.type → `info|warning|success|order|payment|system`
-   ✅ balance_ledger.type → `topup|withdraw|reward|payment|refund`
-   ✅ feedback.category → `bug|feature|complaint|suggestion`

---

## 📋 NEXT STEPS

1. ✅ Verify all decimal fields (schedules, trackings, payments)
2. ⏳ Add missing `mitra_id` to ratings API
3. ⏳ Add activity logging endpoints
4. ⏳ Create ERD update for reports/settings tables
5. ⏳ Document user flows in detail
6. ⏳ Add API validation rules based on ERD constraints

---

**Generated by:** GitHub Copilot  
**Date:** 15 Januari 2025  
**Status:** Ready for Review
