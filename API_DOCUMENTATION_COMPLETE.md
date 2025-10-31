# 📚 Gerobaks API - Complete Documentation

![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

**Backend API for Gerobaks Waste Management System**

🔗 **Repository**: [https://github.com/fk0u/gerobackend](https://github.com/fk0u/gerobackend)

---

## 📋 Table of Contents

-   [Overview](#-overview)
-   [Base URL](#-base-url)
-   [Authentication](#-authentication)
-   [Response Format](#-response-format)
-   [Error Handling](#-error-handling)
-   [Rate Limiting](#-rate-limiting)
-   [API Endpoints](#-api-endpoints)
    -   [Authentication](#1-authentication)
    -   [User Management](#2-user-management)
    -   [Schedules](#3-schedules)
    -   [Orders](#4-orders)
    -   [Tracking](#5-tracking)
    -   [Payments](#6-payments)
    -   [Balance](#7-balance)
    -   [Ratings](#8-ratings)
    -   [Notifications](#9-notifications)
    -   [Chat](#10-chat)
    -   [Feedback](#11-feedback)
    -   [Services](#12-services)
    -   [Subscriptions](#13-subscriptions)
    -   [Dashboard](#14-dashboard)
    -   [Reports](#15-reports)
    -   [Admin](#16-admin)
    -   [Settings](#17-settings)
-   [Roles & Permissions](#-roles--permissions)
-   [Data Models](#-data-models)
-   [Testing](#-testing)
-   [Deployment](#-deployment)

---

## 🎯 Overview

Gerobaks API adalah RESTful API untuk sistem manajemen pengumpulan sampah yang menghubungkan pengguna dengan mitra pengumpul sampah. API ini dibangun menggunakan Laravel 10 dan menyediakan fitur lengkap untuk:

-   ✅ Manajemen jadwal pengambilan sampah
-   ✅ Tracking real-time lokasi mitra
-   ✅ Sistem pembayaran dan balance
-   ✅ Rating dan feedback
-   ✅ Notifikasi dan chat
-   ✅ Subscription plans
-   ✅ Dashboard analytics
-   ✅ Admin management

---

## 🌐 Base URL

### Production

```
https://gerobaks.dumeg.com/api
```

### Local Development

```
http://127.0.0.1:8000/api
```

---

## 🔐 Authentication

API menggunakan **Laravel Sanctum** untuk autentikasi berbasis token.

### Login Flow

1. **Login** untuk mendapatkan token
2. **Include token** di header setiap request
3. **Logout** untuk revoke token

### Header Format

```http
Authorization: Bearer {your-token-here}
Content-Type: application/json
Accept: application/json
```

### Example

```bash
curl -X POST https://gerobaks.dumeg.com/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123"
  }'
```

---

## 📨 Response Format

Semua response API mengikuti format standar:

### Success Response

```json
{
    "success": true,
    "message": "Operation successful",
    "data": {
        // response data
    }
}
```

### Paginated Response

```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 100,
    "from": 1,
    "to": 20
  }
}
```

### Error Response

```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        "field": ["Validation error message"]
    }
}
```

---

## ❌ Error Handling

### HTTP Status Codes

| Code | Description                              |
| ---- | ---------------------------------------- |
| 200  | OK - Request berhasil                    |
| 201  | Created - Resource berhasil dibuat       |
| 400  | Bad Request - Request tidak valid        |
| 401  | Unauthorized - Token tidak valid/expired |
| 403  | Forbidden - Tidak memiliki permission    |
| 404  | Not Found - Resource tidak ditemukan     |
| 422  | Unprocessable Entity - Validation error  |
| 429  | Too Many Requests - Rate limit exceeded  |
| 500  | Internal Server Error - Server error     |

### Common Errors

```json
// Unauthorized
{
  "message": "Unauthenticated."
}

// Forbidden
{
  "error": "Forbidden",
  "message": "Insufficient permissions"
}

// Validation Error
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

---

## ⏱️ Rate Limiting

-   **Default**: 60 requests per minute
-   **Authenticated**: 120 requests per minute

Headers dalam response:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
```

---

## 📡 API Endpoints

### 1. Authentication

#### 1.1 Register

Mendaftarkan user baru.

**Endpoint**: `POST /register`

**Access**: Public

**Request Body**:

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "08123456789",
    "role": "end_user"
}
```

**Response** (201):

```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "08123456789",
            "role": "end_user",
            "created_at": "2025-10-31T10:00:00.000000Z"
        },
        "token": "33|vPTYQr0DF4ESykfUGg1aly2PKc50273Ex6HH0UC50e894e5d"
    }
}
```

**Validation Rules**:

-   `name`: required, string, max:255
-   `email`: required, email, unique
-   `password`: required, min:8, confirmed
-   `phone`: nullable, string
-   `role`: required, in:end_user,mitra,admin

---

#### 1.2 Login

Login dan mendapatkan token.

**Endpoint**: `POST /login`

**Access**: Public

**Request Body**:

```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response** (200):

```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "end_user",
            "phone": "08123456789",
            "profile_image": null,
            "balance": 0
        },
        "token": "33|vPTYQr0DF4ESykfUGg1aly2PKc50273Ex6HH0UC50e894e5d"
    }
}
```

---

#### 1.3 Get Current User

Mendapatkan informasi user yang sedang login.

**Endpoint**: `GET /auth/me`

**Access**: Authenticated

**Headers**:

```
Authorization: Bearer {token}
```

**Response** (200):

```json
{
    "success": true,
    "message": "User data retrieved",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "end_user",
        "phone": "08123456789",
        "profile_image": null,
        "balance": 50000,
        "created_at": "2025-10-31T10:00:00.000000Z"
    }
}
```

---

#### 1.4 Logout

Logout dan revoke token.

**Endpoint**: `POST /auth/logout`

**Access**: Authenticated

**Response** (200):

```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

---

### 2. User Management

#### 2.1 Update Profile

Update informasi profil user.

**Endpoint**: `POST /user/update-profile`

**Access**: Authenticated

**Request Body**:

```json
{
    "name": "John Doe Updated",
    "phone": "08123456789",
    "address": "Jl. Merdeka No. 123"
}
```

**Response** (200):

```json
{
    "success": true,
    "message": "Profile updated successfully",
    "data": {
        "id": 1,
        "name": "John Doe Updated",
        "email": "john@example.com",
        "phone": "08123456789",
        "address": "Jl. Merdeka No. 123"
    }
}
```

---

#### 2.2 Change Password

Mengubah password user.

**Endpoint**: `POST /user/change-password`

**Access**: Authenticated

**Request Body**:

```json
{
    "current_password": "oldpassword123",
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
}
```

**Response** (200):

```json
{
    "success": true,
    "message": "Password changed successfully"
}
```

---

#### 2.3 Upload Profile Image

Upload foto profil user.

**Endpoint**: `POST /user/upload-profile-image`

**Access**: Authenticated

**Request**: Multipart Form Data

```
image: [file] (jpg, jpeg, png, max: 2MB)
```

**Response** (200):

```json
{
    "success": true,
    "message": "Profile image uploaded successfully",
    "data": {
        "profile_image_url": "https://gerobaks.dumeg.com/storage/profiles/abc123.jpg"
    }
}
```

---

### 3. Schedules

#### 3.1 Get All Schedules

Mendapatkan daftar jadwal dengan filter dan pagination.

**Endpoint**: `GET /schedules`

**Access**: Public (with optional filters for authenticated users)

**Query Parameters**:

-   `status`: Filter by status (pending, confirmed, in_progress, completed, cancelled)
-   `mitra_id`: Filter by mitra ID
-   `user_id`: Filter by user ID
-   `date_from`: Filter dari tanggal (YYYY-MM-DD)
-   `date_to`: Filter sampai tanggal (YYYY-MM-DD)
-   `service_type`: Filter by service type
-   `per_page`: Items per page (default: 20)
-   `page`: Page number (default: 1)

**Example Request**:

```bash
GET /schedules?status=pending&per_page=10&page=1
```

**Response** (200):

```json
{
    "success": true,
    "message": "Schedules retrieved successfully",
    "data": [
        {
            "id": 1,
            "user": {
                "id": 5,
                "name": "John Doe",
                "email": "john@example.com"
            },
            "mitra": null,
            "service_type": "pickup_sampah_organik",
            "pickup_address": "Jl. Merdeka No. 123",
            "pickup_latitude": -6.2,
            "pickup_longitude": 106.816667,
            "scheduled_at": "2025-11-01T08:00:00.000000Z",
            "status": "pending",
            "payment_method": "cash",
            "price": 15000,
            "notes": "Tolong ambil di depan rumah",
            "created_at": "2025-10-31T10:00:00.000000Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 10,
        "total": 50
    }
}
```

---

#### 3.2 Get Schedule by ID

Mendapatkan detail jadwal spesifik.

**Endpoint**: `GET /schedules/{id}`

**Access**: Public

**Response** (200):

```json
{
    "success": true,
    "message": "Schedule retrieved successfully",
    "data": {
        "id": 1,
        "user": {
            "id": 5,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "08123456789"
        },
        "mitra": {
            "id": 10,
            "name": "Driver Ahmad",
            "email": "ahmad@gerobaks.com",
            "phone": "08987654321"
        },
        "service_type": "pickup_sampah_organik",
        "pickup_address": "Jl. Merdeka No. 123, Jakarta Pusat",
        "pickup_latitude": -6.2,
        "pickup_longitude": 106.816667,
        "scheduled_at": "2025-11-01T08:00:00.000000Z",
        "estimated_duration": 30,
        "status": "in_progress",
        "payment_method": "cash",
        "price": 15000,
        "notes": "Tolong ambil di depan rumah",
        "waste_type": "Organik",
        "estimated_weight": 5.5,
        "contact_name": "John Doe",
        "contact_phone": "08123456789",
        "trackings_count": 5,
        "trackings": [
            {
                "id": 1,
                "latitude": -6.2001,
                "longitude": 106.8167,
                "recorded_at": "2025-11-01T08:05:00.000000Z"
            }
        ],
        "additional_wastes": [],
        "created_at": "2025-10-31T10:00:00.000000Z",
        "updated_at": "2025-11-01T08:00:00.000000Z"
    }
}
```

---

#### 3.3 Create Schedule (Standard Format)

Membuat jadwal baru (untuk mitra/admin).

**Endpoint**: `POST /schedules`

**Access**: Authenticated (mitra, admin)

**Request Body**:

```json
{
    "service_type": "pickup_sampah_organik",
    "pickup_address": "Jl. Merdeka No. 123, Jakarta Pusat",
    "pickup_latitude": -6.2,
    "pickup_longitude": 106.816667,
    "scheduled_at": "2025-11-01T08:00:00",
    "estimated_duration": 30,
    "notes": "Tolong ambil di depan rumah",
    "payment_method": "cash",
    "price": 15000,
    "frequency": "once",
    "waste_type": "Organik",
    "estimated_weight": 5.5,
    "contact_name": "John Doe",
    "contact_phone": "08123456789",
    "additional_wastes": [
        {
            "waste_type": "Plastik",
            "estimated_weight": 2.0,
            "notes": "Botol plastik"
        }
    ]
}
```

**Response** (201):

```json
{
    "success": true,
    "message": "Schedule created successfully",
    "data": {
        "id": 1,
        "user_id": 5,
        "service_type": "pickup_sampah_organik",
        "pickup_address": "Jl. Merdeka No. 123, Jakarta Pusat",
        "scheduled_at": "2025-11-01T08:00:00.000000Z",
        "status": "pending",
        "created_at": "2025-10-31T10:00:00.000000Z"
    }
}
```

---

#### 3.4 Create Schedule (Mobile Format)

Membuat jadwal dengan format mobile app (untuk end_user).

**Endpoint**: `POST /schedules/mobile`

**Access**: Authenticated (end_user)

**Request Body**:

```json
{
    "alamat": "Jl. Merdeka No. 123, Jakarta Pusat",
    "tanggal": "2025-11-01",
    "waktu": "08:00",
    "catatan": "Tolong ambil di depan rumah",
    "koordinat": {
        "lat": -6.2,
        "lng": 106.816667
    },
    "jenis_layanan": "pickup_sampah_organik",
    "metode_pembayaran": "cash"
}
```

**Validation Rules**:

-   `alamat`: required, string, max:500
-   `tanggal`: required, date, after:now
-   `waktu`: required, string (format HH:mm)
-   `catatan`: nullable, string, max:1000
-   `koordinat.lat`: required, numeric, between:-90,90
-   `koordinat.lng`: required, numeric, between:-180,180
-   `jenis_layanan`: required, string, max:100
-   `metode_pembayaran`: nullable, in:cash,transfer,wallet

**Response** (201):

```json
{
    "success": true,
    "message": "Jadwal berhasil dibuat",
    "data": {
        "id": 1,
        "user": {
            "id": 5,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "service_type": "pickup_sampah_organik",
        "pickup_address": "Jl. Merdeka No. 123, Jakarta Pusat",
        "pickup_latitude": -6.2,
        "pickup_longitude": 106.816667,
        "scheduled_at": "2025-11-01T08:00:00.000000Z",
        "status": "pending",
        "payment_method": "cash",
        "created_at": "2025-10-31T10:00:00.000000Z"
    }
}
```

---

#### 3.5 Update Schedule

Update jadwal yang ada.

**Endpoint**: `PATCH /schedules/{id}`

**Access**: Authenticated (mitra, admin)

**Request Body** (semua field optional):

```json
{
    "service_type": "pickup_sampah_anorganik",
    "pickup_address": "Updated address",
    "scheduled_at": "2025-11-02T09:00:00",
    "status": "confirmed",
    "mitra_id": 10,
    "price": 20000
}
```

**Response** (200):

```json
{
    "success": true,
    "message": "Schedule updated successfully",
    "data": {
        "id": 1,
        "status": "confirmed",
        "mitra_id": 10,
        "updated_at": "2025-10-31T11:00:00.000000Z"
    }
}
```

---

#### 3.6 Complete Schedule

Menandai jadwal sebagai selesai.

**Endpoint**: `POST /schedules/{id}/complete`

**Access**: Authenticated (mitra, admin)

**Request Body**:

```json
{
    "completion_notes": "Sampah berhasil diambil",
    "actual_duration": 25
}
```

**Response** (200):

```json
{
    "success": true,
    "message": "Schedule completed successfully",
    "data": {
        "id": 1,
        "status": "completed",
        "completed_at": "2025-11-01T08:30:00.000000Z"
    }
}
```

---

#### 3.7 Cancel Schedule

Membatalkan jadwal.

**Endpoint**: `POST /schedules/{id}/cancel`

**Access**: Authenticated (mitra, admin)

**Request Body**:

```json
{
    "cancellation_reason": "Customer request"
}
```

**Response** (200):

```json
{
    "success": true,
    "message": "Schedule cancelled successfully",
    "data": {
        "id": 1,
        "status": "cancelled",
        "cancelled_at": "2025-10-31T12:00:00.000000Z"
    }
}
```

---

### 4. Orders

#### 4.1 Get All Orders

**Endpoint**: `GET /orders`

**Access**: Authenticated

**Query Parameters**:

-   `status`: Filter by status
-   `user_id`: Filter by user
-   `mitra_id`: Filter by mitra
-   `per_page`: Items per page (default: 20)

**Response** (200):

```json
{
    "success": true,
    "message": "Orders retrieved successfully",
    "data": [
        {
            "id": 1,
            "user_id": 5,
            "service_type": "pickup_sampah_organik",
            "status": "pending",
            "total_amount": 25000,
            "created_at": "2025-10-31T10:00:00.000000Z"
        }
    ]
}
```

---

#### 4.2 Create Order

**Endpoint**: `POST /orders`

**Access**: Authenticated (end_user)

**Request Body**:

```json
{
    "service_id": 1,
    "pickup_address": "Jl. Merdeka No. 123",
    "pickup_latitude": -6.2,
    "pickup_longitude": 106.816667,
    "scheduled_date": "2025-11-01",
    "notes": "Urgent pickup"
}
```

**Response** (201):

```json
{
    "success": true,
    "message": "Order created successfully",
    "data": {
        "id": 1,
        "order_number": "ORD-20251031-001",
        "status": "pending",
        "total_amount": 25000
    }
}
```

---

#### 4.3 Cancel Order

**Endpoint**: `POST /orders/{id}/cancel`

**Access**: Authenticated (end_user)

**Request Body**:

```json
{
    "reason": "Change of plans"
}
```

**Response** (200):

```json
{
    "success": true,
    "message": "Order cancelled successfully"
}
```

---

### 5. Tracking

#### 5.1 Get Tracking Data

**Endpoint**: `GET /tracking`

**Access**: Public

**Query Parameters**:

-   `schedule_id`: Filter by schedule
-   `mitra_id`: Filter by mitra
-   `from`: Date from (YYYY-MM-DD)
-   `to`: Date to (YYYY-MM-DD)

**Response** (200):

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "schedule_id": 1,
            "mitra_id": 10,
            "latitude": -6.2001,
            "longitude": 106.8167,
            "speed": 25.5,
            "bearing": 180,
            "recorded_at": "2025-11-01T08:05:00.000000Z"
        }
    ]
}
```

---

#### 5.2 Create Tracking Point

**Endpoint**: `POST /tracking`

**Access**: Authenticated (mitra)

**Request Body**:

```json
{
    "schedule_id": 1,
    "latitude": -6.2001,
    "longitude": 106.8167,
    "speed": 25.5,
    "bearing": 180,
    "accuracy": 5.0
}
```

**Response** (201):

```json
{
    "success": true,
    "message": "Tracking point recorded",
    "data": {
        "id": 1,
        "recorded_at": "2025-11-01T08:05:00.000000Z"
    }
}
```

---

#### 5.3 Get Tracking by Schedule

**Endpoint**: `GET /tracking/schedule/{scheduleId}`

**Access**: Public

**Response** (200):

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "latitude": -6.2001,
            "longitude": 106.8167,
            "recorded_at": "2025-11-01T08:05:00.000000Z"
        }
    ]
}
```

---

### 6. Payments

#### 6.1 Get Payment History

**Endpoint**: `GET /payments`

**Access**: Authenticated

**Response** (200):

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "amount": 25000,
            "method": "cash",
            "status": "paid",
            "created_at": "2025-10-31T10:00:00.000000Z"
        }
    ]
}
```

---

#### 6.2 Create Payment

**Endpoint**: `POST /payments`

**Access**: Authenticated

**Request Body**:

```json
{
    "order_id": 1,
    "amount": 25000,
    "method": "transfer",
    "proof_image": "base64_or_url"
}
```

**Response** (201):

```json
{
    "success": true,
    "message": "Payment created successfully",
    "data": {
        "id": 1,
        "payment_number": "PAY-20251031-001",
        "status": "pending"
    }
}
```

---

### 7. Balance

#### 7.1 Get Balance Summary

**Endpoint**: `GET /balance/summary`

**Access**: Authenticated

**Response** (200):

```json
{
    "success": true,
    "data": {
        "current_balance": 50000,
        "total_topup": 100000,
        "total_spent": 50000,
        "pending_transactions": 0
    }
}
```

---

#### 7.2 Top Up Balance

**Endpoint**: `POST /balance/topup`

**Access**: Authenticated

**Request Body**:

```json
{
    "amount": 50000,
    "method": "transfer",
    "reference": "TRF123456"
}
```

**Response** (201):

```json
{
    "success": true,
    "message": "Top-up successful",
    "data": {
        "new_balance": 100000,
        "transaction_id": "TXN-20251031-001"
    }
}
```

---

#### 7.3 Withdraw Balance

**Endpoint**: `POST /balance/withdraw`

**Access**: Authenticated

**Request Body**:

```json
{
    "amount": 25000,
    "bank_account": "1234567890",
    "bank_name": "BCA"
}
```

**Response** (201):

```json
{
    "success": true,
    "message": "Withdrawal request submitted",
    "data": {
        "new_balance": 25000,
        "transaction_id": "WD-20251031-001"
    }
}
```

---

### 8. Ratings

#### 8.1 Get Ratings

**Endpoint**: `GET /ratings`

**Access**: Public

**Query Parameters**:

-   `mitra_id`: Filter by mitra
-   `per_page`: Items per page

**Response** (200):

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "user": {
                "name": "John Doe"
            },
            "mitra": {
                "name": "Driver Ahmad"
            },
            "rating": 5,
            "comment": "Excellent service!",
            "created_at": "2025-10-31T10:00:00.000000Z"
        }
    ]
}
```

---

#### 8.2 Create Rating

**Endpoint**: `POST /ratings`

**Access**: Authenticated (end_user)

**Request Body**:

```json
{
    "schedule_id": 1,
    "mitra_id": 10,
    "rating": 5,
    "comment": "Excellent service, very professional!"
}
```

**Validation Rules**:

-   `schedule_id`: required, exists
-   `mitra_id`: required, exists
-   `rating`: required, integer, between:1,5
-   `comment`: nullable, string, max:500

**Response** (201):

```json
{
    "success": true,
    "message": "Rating submitted successfully",
    "data": {
        "id": 1,
        "rating": 5,
        "created_at": "2025-10-31T10:00:00.000000Z"
    }
}
```

---

### 9. Notifications

#### 9.1 Get Notifications

**Endpoint**: `GET /notifications`

**Access**: Authenticated

**Query Parameters**:

-   `unread`: Filter unread only (true/false)

**Response** (200):

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Schedule Confirmed",
            "message": "Your schedule has been confirmed",
            "type": "schedule",
            "read_at": null,
            "created_at": "2025-10-31T10:00:00.000000Z"
        }
    ]
}
```

---

#### 9.2 Mark as Read

**Endpoint**: `POST /notifications/mark-read`

**Access**: Authenticated

**Request Body**:

```json
{
    "notification_ids": [1, 2, 3]
}
```

**Response** (200):

```json
{
    "success": true,
    "message": "Notifications marked as read"
}
```

---

### 10. Chat

#### 10.1 Get Chat Messages

**Endpoint**: `GET /chats`

**Access**: Authenticated

**Query Parameters**:

-   `conversation_id`: Filter by conversation
-   `with_user_id`: Filter by user

**Response** (200):

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "sender": {
                "id": 5,
                "name": "John Doe"
            },
            "message": "Hello, where are you?",
            "created_at": "2025-10-31T10:00:00.000000Z"
        }
    ]
}
```

---

#### 10.2 Send Message

**Endpoint**: `POST /chats`

**Access**: Authenticated

**Request Body**:

```json
{
    "receiver_id": 10,
    "message": "I'm on the way",
    "schedule_id": 1
}
```

**Response** (201):

```json
{
    "success": true,
    "message": "Message sent",
    "data": {
        "id": 1,
        "created_at": "2025-10-31T10:05:00.000000Z"
    }
}
```

---

### 11. Feedback

#### 11.1 Get Feedback

**Endpoint**: `GET /feedback`

**Access**: Authenticated

**Response** (200):

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "category": "service",
            "subject": "Great app!",
            "message": "Love the features",
            "status": "pending",
            "created_at": "2025-10-31T10:00:00.000000Z"
        }
    ]
}
```

---

#### 11.2 Submit Feedback

**Endpoint**: `POST /feedback`

**Access**: Authenticated

**Request Body**:

```json
{
    "category": "service",
    "subject": "Improvement suggestion",
    "message": "Please add dark mode feature",
    "rating": 4
}
```

**Response** (201):

```json
{
    "success": true,
    "message": "Feedback submitted successfully",
    "data": {
        "id": 1,
        "status": "pending"
    }
}
```

---

### 12. Services

#### 12.1 Get Services

**Endpoint**: `GET /services`

**Access**: Public

**Response** (200):

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Pickup Sampah Organik",
            "code": "pickup_sampah_organik",
            "description": "Layanan pengambilan sampah organik",
            "base_price": 15000,
            "unit": "kg",
            "is_active": true
        }
    ]
}
```

---

### 13. Subscriptions

#### 13.1 Get Subscription Plans

**Endpoint**: `GET /subscription/plans`

**Access**: Authenticated

**Response** (200):

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Basic Plan",
            "price": 50000,
            "duration_days": 30,
            "features": ["Feature 1", "Feature 2"]
        }
    ]
}
```

---

#### 13.2 Subscribe

**Endpoint**: `POST /subscription/subscribe`

**Access**: Authenticated

**Request Body**:

```json
{
    "plan_id": 1,
    "payment_method": "transfer"
}
```

**Response** (201):

```json
{
    "success": true,
    "message": "Subscription activated",
    "data": {
        "id": 1,
        "expires_at": "2025-11-30T10:00:00.000000Z"
    }
}
```

---

### 14. Dashboard

#### 14.1 Mitra Dashboard

**Endpoint**: `GET /dashboard/mitra/{id}`

**Access**: Authenticated (mitra, admin)

**Response** (200):

```json
{
    "success": true,
    "data": {
        "total_schedules": 150,
        "completed_schedules": 120,
        "pending_schedules": 30,
        "total_earnings": 5000000,
        "average_rating": 4.8,
        "recent_schedules": []
    }
}
```

---

#### 14.2 User Dashboard

**Endpoint**: `GET /dashboard/user/{id}`

**Access**: Authenticated (end_user, admin)

**Response** (200):

```json
{
    "success": true,
    "data": {
        "total_schedules": 25,
        "active_schedules": 3,
        "completed_schedules": 22,
        "total_spent": 500000,
        "balance": 50000
    }
}
```

---

### 15. Reports

#### 15.1 Get Reports

**Endpoint**: `GET /reports`

**Access**: Authenticated

**Response** (200):

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Monthly Report",
            "type": "monthly",
            "period": "2025-10",
            "created_at": "2025-10-31T10:00:00.000000Z"
        }
    ]
}
```

---

### 16. Admin

#### 16.1 Get Statistics

**Endpoint**: `GET /admin/stats`

**Access**: Authenticated (admin)

**Response** (200):

```json
{
    "success": true,
    "data": {
        "total_users": 1000,
        "total_mitra": 50,
        "total_schedules": 5000,
        "total_revenue": 50000000,
        "active_schedules": 150,
        "pending_payments": 25
    }
}
```

---

#### 16.2 Get All Users

**Endpoint**: `GET /admin/users`

**Access**: Authenticated (admin)

**Query Parameters**:

-   `role`: Filter by role
-   `search`: Search by name/email
-   `per_page`: Items per page

**Response** (200):

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "end_user",
            "created_at": "2025-10-31T10:00:00.000000Z"
        }
    ]
}
```

---

#### 16.3 Create User

**Endpoint**: `POST /admin/users`

**Access**: Authenticated (admin)

**Request Body**:

```json
{
    "name": "New User",
    "email": "newuser@example.com",
    "password": "password123",
    "role": "end_user",
    "phone": "08123456789"
}
```

---

### 17. Settings

#### 17.1 Get Settings

**Endpoint**: `GET /settings`

**Access**: Public

**Response** (200):

```json
{
    "success": true,
    "data": {
        "app_name": "Gerobaks",
        "version": "1.0.0",
        "maintenance_mode": false
    }
}
```

---

## 👥 Roles & Permissions

### Role Types

| Role       | Description        | Key Permissions                                              |
| ---------- | ------------------ | ------------------------------------------------------------ |
| `end_user` | Pelanggan/End User | Create schedules (mobile), view own data, rate services      |
| `mitra`    | Driver/Mitra       | View assigned schedules, update tracking, complete schedules |
| `admin`    | Administrator      | Full access, manage users, view analytics                    |

### Permission Matrix

| Endpoint                      | end_user | mitra | admin |
| ----------------------------- | -------- | ----- | ----- |
| POST /schedules/mobile        | ✅       | ❌    | ❌    |
| POST /schedules               | ❌       | ✅    | ✅    |
| POST /tracking                | ❌       | ✅    | ✅    |
| POST /schedules/{id}/complete | ❌       | ✅    | ✅    |
| POST /ratings                 | ✅       | ❌    | ✅    |
| GET /admin/\*                 | ❌       | ❌    | ✅    |

---

## 📊 Data Models

### Schedule Model

```json
{
    "id": "integer",
    "user_id": "integer",
    "mitra_id": "integer|null",
    "service_type": "string",
    "pickup_address": "string",
    "pickup_latitude": "decimal(10,7)",
    "pickup_longitude": "decimal(10,7)",
    "scheduled_at": "datetime",
    "estimated_duration": "integer|null",
    "status": "enum: pending|confirmed|in_progress|completed|cancelled",
    "payment_method": "enum: cash|transfer|wallet",
    "price": "decimal(10,2)|null",
    "frequency": "enum: once|daily|weekly|biweekly|monthly",
    "notes": "text|null",
    "waste_type": "string|null",
    "estimated_weight": "decimal(8,2)|null",
    "completed_at": "datetime|null",
    "cancelled_at": "datetime|null",
    "created_at": "datetime",
    "updated_at": "datetime"
}
```

### User Model

```json
{
    "id": "integer",
    "name": "string",
    "email": "string",
    "role": "enum: end_user|mitra|admin",
    "phone": "string|null",
    "address": "text|null",
    "profile_image": "string|null",
    "balance": "decimal(10,2)",
    "is_active": "boolean",
    "created_at": "datetime",
    "updated_at": "datetime"
}
```

---

## 🧪 Testing

### Health Check

```bash
curl https://gerobaks.dumeg.com/api/health
```

### Test Login

```bash
curl -X POST https://gerobaks.dumeg.com/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "daffa@gmail.com",
    "password": "password123"
  }'
```

### Test Authenticated Request

```bash
curl https://gerobaks.dumeg.com/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Postman Collection

Import collection dari: `backend/tests/postman/Gerobaks_API.postman_collection.json`

---

## 🚀 Deployment

### Production URL

```
https://gerobaks.dumeg.com
```

### Environment Setup

1. Clone repository

```bash
git clone https://github.com/fk0u/gerobackend.git
cd gerobackend
```

2. Install dependencies

```bash
composer install
```

3. Setup environment

```bash
cp .env.example .env
php artisan key:generate
```

4. Run migrations

```bash
php artisan migrate --seed
```

5. Start server

```bash
php artisan serve
```

---

## 📝 Changelog

### Version 1.0.0 (2025-10-31)

✅ **Initial Release**

-   Complete authentication system
-   Schedule management with mobile format support
-   Real-time tracking
-   Payment & balance system
-   Rating & feedback
-   Admin panel
-   Subscription management

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License.

---

## 👨‍💻 Developer

**fk0u**

-   GitHub: [@fk0u](https://github.com/fk0u)
-   Repository: [gerobackend](https://github.com/fk0u/gerobackend)

---

## 📞 Support

For issues and questions:

-   Create an issue on [GitHub](https://github.com/fk0u/gerobackend/issues)
-   Email: support@gerobaks.com

---

## 🙏 Acknowledgments

-   Laravel Framework
-   Laravel Sanctum
-   MySQL
-   All contributors

---

**Made with ❤️ by fk0u**

**Repository**: [https://github.com/fk0u/gerobackend](https://github.com/fk0u/gerobackend)
