# 🚨 PRODUCTION FIX GUIDE - URGENT

## ❌ MASALAH DI PRODUCTION

### Error yang Terjadi:

1. **403 Forbidden** - "Insufficient permissions" pada `POST /api/schedules`
2. **500 Internal Server Error** - Error di middleware RoleAuthorization

### 🔍 Root Cause:

**Production server masih menggunakan routes LAMA** yang membutuhkan role `mitra` atau `admin` untuk create schedule, padahal seharusnya semua authenticated users bisa create schedule.

### 📝 Bukti dari Flutter Log:

```
POST https://gerobaks.dumeg.com/api/schedules
Response: 403 {"error":"Forbidden","message":"Insufficient permissions"}
Middleware: RoleAuthorization line 34
```

---

## ✅ SOLUSI - STEP BY STEP

### Step 1: Upload File yang Sudah Diperbaiki

Upload file-file berikut ke production server (`/home/dumeg/public_html/gerobaks.dumeg.com/`):

#### 📁 Files to Upload:

```
backend/routes/api.php                                     ← CRITICAL!
backend/app/Http/Controllers/Api/ScheduleController.php    ← CRITICAL!
backend/app/Http/Resources/ScheduleResource.php
backend/app/Models/Schedule.php
```

#### Via FTP/cPanel File Manager:

1. Login ke cPanel gerobaks.dumeg.com
2. Buka File Manager
3. Navigate ke: `/home/dumeg/public_html/gerobaks.dumeg.com/`
4. Upload file-file di atas sesuai struktur folder

#### Via SSH (Recommended):

```bash
# 1. Login ke server
ssh dumeg@gerobaks.dumeg.com

# 2. Navigate ke folder project
cd /home/dumeg/public_html/gerobaks.dumeg.com

# 3. Backup files lama
cp routes/api.php routes/api.php.backup
cp app/Http/Controllers/Api/ScheduleController.php app/Http/Controllers/Api/ScheduleController.php.backup

# 4. Upload file baru (gunakan git pull atau upload manual)
git pull origin fk0u-feat/backend

# Atau jika tidak menggunakan git, upload manual via FTP
```

---

### Step 2: Clear All Caches

Setelah upload file, **WAJIB** clear semua cache:

```bash
# Login SSH ke production
ssh dumeg@gerobaks.dumeg.com

# Navigate ke folder project
cd /home/dumeg/public_html/gerobaks.dumeg.com

# Clear semua cache
php artisan route:clear
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Optional: Optimize untuk production
php artisan config:cache
php artisan route:cache
```

---

### Step 3: Verify Routes

Cek apakah routes sudah benar:

```bash
# Cek route list
php artisan route:list --path=schedules

# Output yang BENAR:
# POST   api/schedules          auth:sanctum           ← No role middleware!
# POST   api/schedules/mobile   auth:sanctum           ← No role middleware!
# PATCH  api/schedules/{id}     auth:sanctum           ← No role middleware!

# Output yang SALAH (OLD):
# POST   api/schedules          auth:sanctum,role:mitra,admin  ← Ada role!
```

---

### Step 4: Test Production API

```bash
# Test dari terminal local
curl -X POST https://gerobaks.dumeg.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "daffa@gmail.com", "password": "daffa123"}'

# Simpan token yang didapat

# Test create schedule
curl -X POST https://gerobaks.dumeg.com/api/schedules \
  -H "Authorization: Bearer TOKEN_DISINI" \
  -H "Content-Type: application/json" \
  -d '{
    "service_type": "pickup_sampah_organik",
    "pickup_address": "Test Production",
    "pickup_latitude": -6.2088,
    "pickup_longitude": 106.8456,
    "scheduled_at": "2025-11-12 10:00:00",
    "payment_method": "cash",
    "frequency": "once"
  }'

# Expected: 201 Created ✅
# Wrong: 403 Forbidden ❌
```

---

## 🔧 QUICK FIX - ROUTE CHANGES NEEDED

Jika tidak bisa upload file, edit manual di production:

### File: `/home/dumeg/public_html/gerobaks.dumeg.com/routes/api.php`

**CARI** (sekitar line 65-75):

```php
Route::middleware(['auth:sanctum','role:mitra,admin'])->group(function () {
    Route::post('/schedules', [ScheduleController::class, 'store']);
    Route::put('/schedules/{id}', [ScheduleController::class, 'update']);
    Route::patch('/schedules/{id}', [ScheduleController::class, 'update']);
    Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy']);
    Route::post('/schedules/{id}/complete', [ScheduleController::class, 'complete']);
    Route::post('/schedules/{id}/cancel', [ScheduleController::class, 'cancel']);
});

Route::middleware(['auth:sanctum','role:end_user'])->group(function () {
    Route::post('/schedules/mobile', [ScheduleController::class, 'storeMobileFormat']);
});
```

**GANTI DENGAN**:

```php
// Authenticated schedule operations
Route::middleware(['auth:sanctum'])->group(function () {
    // All authenticated users can create, update (own), and cancel schedules
    Route::post('/schedules', [ScheduleController::class, 'store']);
    Route::post('/schedules/mobile', [ScheduleController::class, 'storeMobileFormat']);
    Route::put('/schedules/{id}', [ScheduleController::class, 'update']);
    Route::patch('/schedules/{id}', [ScheduleController::class, 'update']);
    Route::post('/schedules/{id}/cancel', [ScheduleController::class, 'cancel']);

    // Mitra/Admin only operations
    Route::middleware(['role:mitra,admin'])->group(function () {
        Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy']);
        Route::post('/schedules/{id}/complete', [ScheduleController::class, 'complete']);
    });
});
```

**KEMUDIAN** jalankan:

```bash
php artisan route:clear
php artisan route:cache
```

---

## 📋 CHECKLIST DEPLOYMENT

-   [ ] **Backup production files** (routes/api.php, ScheduleController.php)
-   [ ] **Upload file baru** ke production
-   [ ] **Clear route cache**: `php artisan route:clear`
-   [ ] **Clear config cache**: `php artisan config:clear`
-   [ ] **Clear application cache**: `php artisan cache:clear`
-   [ ] **Verify routes**: `php artisan route:list --path=schedules`
-   [ ] **Test login**: POST /api/login
-   [ ] **Test create schedule**: POST /api/schedules (should return 201, not 403)
-   [ ] **Test mobile endpoint**: POST /api/schedules/mobile
-   [ ] **Test from Flutter app**
-   [ ] **Monitor Laravel logs**: `tail -f storage/logs/laravel.log`

---

## 🚨 JIKA MASIH ERROR 500

Jika setelah fix routes masih ada 500 error, check:

### 1. Check Laravel Logs

```bash
# Di production server
tail -50 storage/logs/laravel.log
```

### 2. Common Causes:

-   **Database connection** - Check `.env` DB credentials
-   **Missing migrations** - Run `php artisan migrate`
-   **Permission issues** - Check `storage/` and `bootstrap/cache/` permissions
-   **Composer packages** - Run `composer install --no-dev`

### 3. Enable Debug Mode (TEMPORARY)

```bash
# Edit .env
APP_DEBUG=true
APP_ENV=local

# Test API, lihat error detail
# JANGAN LUPA set kembali:
APP_DEBUG=false
APP_ENV=production
```

---

## 📞 VERIFICATION

Setelah deploy, test dengan Flutter app:

### Expected Result:

```
POST https://gerobaks.dumeg.com/api/schedules
Status: 201 Created ✅
Response: {
  "success": true,
  "message": "Schedule created successfully",
  "data": { ... }
}
```

### Current Result (BEFORE FIX):

```
POST https://gerobaks.dumeg.com/api/schedules
Status: 403 Forbidden ❌
Response: {
  "error": "Forbidden",
  "message": "Insufficient permissions"
}
```

---

## 🎯 FILES COMPARISON

### ✅ CORRECT (Local - Working):

```php
// routes/api.php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/schedules', [ScheduleController::class, 'store']);
```

### ❌ WRONG (Production - Broken):

```php
// routes/api.php
Route::middleware(['auth:sanctum','role:mitra,admin'])->group(function () {
    Route::post('/schedules', [ScheduleController::class, 'store']);
```

Perbedaan: Production punya **`'role:mitra,admin'`** yang membuat end_user tidak bisa create schedule!

---

## 🚀 SETELAH FIX

1. **Clear cache di Flutter app** juga
2. **Re-login** di Flutter app untuk get fresh token
3. **Test create schedule** dari Flutter app
4. **Monitor** Laravel logs untuk error lain

---

## 📝 SUMMARY

**Problem**: Production routes require `role:mitra,admin` for schedule creation  
**Solution**: Update routes to allow all `auth:sanctum` users  
**Critical Files**: `routes/api.php`  
**Must Do**: Clear route cache after update  
**Test**: Create schedule should return 201, not 403

---

**PRIORITY**: 🔥🔥🔥 URGENT - Production is broken!  
**ETA**: 5-10 minutes if you have SSH/FTP access  
**Risk**: LOW - We're just updating routes, no database changes

---

Generated: <?php echo date('Y-m-d H:i:s'); ?>
