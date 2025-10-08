# Register API - Testing Guide

## ✅ MASALAH TERATASI

### Problem Sebelumnya:

```json
{
    "message": "The password field confirmation does not match.",
    "errors": {
        "password": ["The password field confirmation does not match."]
    }
}
```

### Root Cause:

-   AuthController menggunakan validation rule `confirmed` yang memerlukan `password_confirmation`
-   OpenAPI spec hanya mendefinisikan `password` tanpa `password_confirmation`

### Solusi Applied:

1. **Removed `confirmed` validation** dari password field
2. **Updated OpenAPI annotation** sesuai dengan spec di openapi.yaml
3. **Simplified request body** tanpa password_confirmation

## ✅ REGISTER API SEKARANG BERFUNGSI

### Request Format (PowerShell):

```powershell
$response = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/register" -Method POST -ContentType "application/json" -Body '{"name":"Siti Rahma","email":"siti@example.com","password":"rahasia123","role":"end_user"}'
```

### Request Format (cURL):

```bash
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Siti Rahma",
    "email": "siti@example.com",
    "password": "rahasia123",
    "role": "end_user"
  }'
```

### Required Fields:

-   ✅ `name` (string, max 255 characters)
-   ✅ `email` (string, email format, unique)
-   ✅ `password` (string, min 6 characters)

### Optional Fields:

-   `role` (enum: end_user, mitra, admin - default: end_user)
-   `phone` (string, max 20 characters)
-   `address` (string, max 500 characters)
-   `vehicle_type` (string, max 100 characters - untuk mitra)
-   `vehicle_plate` (string, max 20 characters - untuk mitra)
-   `work_area` (string, max 255 characters - untuk mitra)

## ✅ SUCCESSFUL RESPONSE

### Example Response:

```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 9,
            "name": "Test User 2",
            "email": "test2@example.com",
            "role": "end_user",
            "status": "active",
            "points": 0,
            "total_collections": 0
        },
        "token": "13|St93CFPocYPYp9MGGAx0cmpyCmG3U3ezPXKMIpeY50041bab"
    }
}
```

## 🧪 TESTING DI SWAGGER UI

1. **Buka Swagger UI:** http://127.0.0.1:8000/api/documentation
2. **Klik endpoint:** `POST /api/register`
3. **Click "Try it out"**
4. **Input data:**
    ```json
    {
        "name": "Siti Rahma",
        "email": "siti@example.com",
        "password": "rahasia123",
        "role": "end_user"
    }
    ```
5. **Click "Execute"**

## 🔧 PERUBAHAN YANG DIBUAT

### File: `app/Http/Controllers/Api/AuthController.php`

#### Before:

```php
'password' => 'required|string|min:6|confirmed',
```

#### After:

```php
'password' => 'required|string|min:6',
```

#### OpenAPI Annotation Updated:

-   ❌ Removed `password_confirmation` from examples
-   ✅ Updated sesuai dengan openapi.yaml spec
-   ✅ Added proper response schema with user and token

### Swagger Documentation:

-   ✅ Register endpoint sekarang match dengan openapi.yaml
-   ✅ Request/Response examples yang akurat
-   ✅ Proper field descriptions dan validations

## 🎯 DEMO READY

Register API sekarang:

-   ✅ **Validation error resolved**
-   ✅ **Sesuai dengan openapi.yaml spec**
-   ✅ **Returns user + token**
-   ✅ **Ready untuk demo di Swagger UI**
-   ✅ **Supports role-based registration**

### Test Accounts Created:

1. **siti@example.com** (User ID: 8)
2. **test2@example.com** (User ID: 9)

### Available Tokens:

-   **daffa@gmail.com:** `10|sLxW3ZpzbXHh4CEvPyZp8czZwkPO4qoooFXa0JNr15f28020`
-   **test2@example.com:** `13|St93CFPocYPYp9MGGAx0cmpyCmG3U3ezPXKMIpeY50041bab`
