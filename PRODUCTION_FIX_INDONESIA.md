# 🚨 ANALISIS PRODUCTION ERROR - INDONESIA

## ❌ MASALAH UTAMA

Production API **BROKEN** karena route configuration berbeda dengan local!

### Error yang Terjadi:

1. **403 Forbidden** - "Insufficient permissions"
2. **500 Internal Server Error** - Error di middleware

### Penyebab Utama:

**Production masih pakai routes LAMA** yang mengharuskan user punya role `mitra` atau `admin` untuk create schedule. Jadi **end_user tidak bisa create schedule**!

---

## 🔍 BUKTI DARI FLUTTER

### Request dari Flutter App:

```
POST https://gerobaks.dumeg.com/api/schedules
Authorization: Bearer 45|lmsqSPyhaSxzmetDnhs3VvTB7qG8N1GcVCN36YlPb62da686
Body: {
  "title": "Lokasi belum diisi",
  "latitude": 37.4219983,
  "longitude": -122.084,
  ...
}
```

### Response dari Production:

```
Status: 403 Forbidden ❌
Body: {
  "error": "Forbidden",
  "message": "Insufficient permissions"
}
```

### Yang Seharusnya:

```
Status: 201 Created ✅
Body: {
  "success": true,
  "message": "Schedule created successfully",
  "data": { ... }
}
```

---

## 🆚 PERBEDAAN LOCAL VS PRODUCTION

### ✅ LOCAL (BENAR):

```php
// File: routes/api.php
Route::middleware(['auth:sanctum'])->group(function () {
    // SEMUA user yang login bisa create schedule
    Route::post('/schedules', [ScheduleController::class, 'store']);
});
```

**Hasil**: End user ✅ bisa create schedule

---

### ❌ PRODUCTION (SALAH):

```php
// File: routes/api.php
Route::middleware(['auth:sanctum','role:mitra,admin'])->group(function () {
    // HANYA mitra & admin yang bisa create schedule
    Route::post('/schedules', [ScheduleController::class, 'store']);
});
```

**Hasil**: End user ❌ tidak bisa create schedule (403 Forbidden)

---

## 📊 ENDPOINT YANG BERMASALAH

| Endpoint                        | End User (Local) | End User (Production) |
| ------------------------------- | ---------------- | --------------------- |
| POST /api/schedules             | ✅ BISA          | ❌ TIDAK BISA         |
| POST /api/schedules/mobile      | ✅ BISA          | ✅ BISA               |
| PATCH /api/schedules/{id}       | ✅ BISA          | ❌ TIDAK BISA         |
| POST /api/schedules/{id}/cancel | ✅ BISA          | ❌ TIDAK BISA         |

**Kesimpulan**: 3 dari 4 endpoint utama BROKEN di production! 🔴

---

## ✅ SOLUSI LENGKAP

### Cara 1: Upload File Baru (RECOMMENDED)

1. **Upload file ini ke production** via FTP/cPanel:

    ```
    backend/routes/api.php
    backend/app/Http/Controllers/Api/ScheduleController.php
    backend/app/Http/Resources/ScheduleResource.php
    ```

2. **SSH ke server** dan jalankan:

    ```bash
    cd /home/dumeg/public_html/gerobaks.dumeg.com
    php artisan route:clear
    php artisan cache:clear
    php artisan config:clear
    php artisan route:cache
    ```

3. **Test** dengan curl:

    ```bash
    curl -X POST https://gerobaks.dumeg.com/api/schedules \
      -H "Authorization: Bearer TOKEN" \
      -H "Content-Type: application/json" \
      -d '{"service_type":"pickup_sampah_organik",...}'

    # Harusnya dapat: 201 Created ✅
    # Bukan: 403 Forbidden ❌
    ```

---

### Cara 2: Edit Manual (QUICK FIX)

1. **Login cPanel** gerobaks.dumeg.com

2. **Buka File Manager**, navigate ke:

    ```
    /home/dumeg/public_html/gerobaks.dumeg.com/routes/api.php
    ```

3. **Cari baris ini** (sekitar line 65-75):

    ```php
    Route::middleware(['auth:sanctum','role:mitra,admin'])->group(function () {
        Route::post('/schedules', [ScheduleController::class, 'store']);
        Route::put('/schedules/{id}', [ScheduleController::class, 'update']);
        Route::patch('/schedules/{id}', [ScheduleController::class, 'update']);
        Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy']);
        Route::post('/schedules/{id}/complete', [ScheduleController::class, 'complete']);
        Route::post('/schedules/{id}/cancel', [ScheduleController::class, 'cancel']);
    });
    ```

4. **GANTI dengan ini**:

    ```php
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/schedules', [ScheduleController::class, 'store']);
        Route::post('/schedules/mobile', [ScheduleController::class, 'storeMobileFormat']);
        Route::put('/schedules/{id}', [ScheduleController::class, 'update']);
        Route::patch('/schedules/{id}', [ScheduleController::class, 'update']);
        Route::post('/schedules/{id}/cancel', [ScheduleController::class, 'cancel']);

        Route::middleware(['role:mitra,admin'])->group(function () {
            Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy']);
            Route::post('/schedules/{id}/complete', [ScheduleController::class, 'complete']);
        });
    });
    ```

5. **Hapus baris ini** (jika ada):

    ```php
    Route::middleware(['auth:sanctum','role:end_user'])->group(function () {
        Route::post('/schedules/mobile', [ScheduleController::class, 'storeMobileFormat']);
    });
    ```

6. **Save file**

7. **Clear cache** via SSH atau cPanel Terminal:
    ```bash
    php artisan route:clear
    php artisan cache:clear
    ```

---

## 🧪 CARA TEST SETELAH FIX

### Test 1: Login

```bash
curl -X POST https://gerobaks.dumeg.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"daffa@gmail.com","password":"daffa123"}'
```

**Expected**: Dapat token ✅

---

### Test 2: Create Schedule

```bash
curl -X POST https://gerobaks.dumeg.com/api/schedules \
  -H "Authorization: Bearer TOKEN_DARI_LOGIN" \
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
```

**Before Fix**: 403 Forbidden ❌  
**After Fix**: 201 Created ✅

---

### Test 3: Dari Flutter App

1. **Logout** dan **login** lagi di Flutter app
2. **Buat schedule baru**
3. **Harusnya sukses** dengan toast hijau ✅

---

## 📋 CHECKLIST DEPLOYMENT

-   [ ] Backup file lama (routes/api.php)
-   [ ] Upload file baru ATAU edit manual
-   [ ] Clear route cache
-   [ ] Clear application cache
-   [ ] Test login via curl
-   [ ] Test create schedule via curl (harusnya 201, bukan 403)
-   [ ] Test dari Flutter app
-   [ ] Monitor Laravel logs
-   [ ] Cek database ada schedule baru

---

## ⚠️ MASALAH TAMBAHAN YANG DITEMUKAN

### 1. GPS Coordinates Salah

Flutter app kirim koordinat:

```
latitude: 37.4219983
longitude: -122.084
```

Ini adalah **Google HQ di California, USA**! Bukan Indonesia! 🌍

Seharusnya koordinat Indonesia:

-   Jakarta: `-6.2088, 106.8456`
-   Bandung: `-6.9175, 107.6191`
-   Surabaya: `-7.2575, 112.7521`

**Action**: Fix GPS di Flutter app! ⚠️

---

### 2. Field Names Lama

Flutter masih pakai field lama:

```json
{
    "title": "...",
    "latitude": 37.4219983,
    "longitude": -122.084,
    "description": "..."
}
```

Tapi backend sudah support field baru juga:

```json
{
    "service_type": "pickup_sampah_organik",
    "pickup_address": "...",
    "pickup_latitude": -6.2088,
    "pickup_longitude": 106.8456,
    "notes": "..."
}
```

**Kabar Baik**: Backend support BOTH formats! Jadi tidak urgent, tapi lebih baik update Flutter ke format baru. ✅

---

## 📊 DAMPAK

### Sebelum Fix:

-   🔴 **End users**: TIDAK BISA create schedule
-   🟡 **End users**: Hanya bisa via mobile endpoint
-   🟢 **Mitra**: Semua fitur jalan
-   🟢 **Admin**: Semua fitur jalan

### Setelah Fix:

-   🟢 **End users**: BISA create schedule (semua endpoint)
-   🟢 **End users**: BISA update schedule sendiri
-   🟢 **End users**: BISA cancel schedule
-   🟢 **Mitra**: Semua fitur jalan
-   🟢 **Admin**: Semua fitur jalan

**User Terpengaruh**: SEMUA end users  
**Tingkat Keparahan**: TINGGI (fitur utama broken)  
**Risiko Fix**: RENDAH (cuma update routes, no database change)  
**Waktu Fix**: 5-10 menit

---

## 🚀 SETELAH DEPLOY

### 1. Monitoring

```bash
# SSH ke production
ssh dumeg@gerobaks.dumeg.com

# Monitor logs real-time
tail -f /home/dumeg/public_html/gerobaks.dumeg.com/storage/logs/laravel.log
```

### 2. Verify Routes

```bash
cd /home/dumeg/public_html/gerobaks.dumeg.com
php artisan route:list --path=schedules
```

Output yang BENAR:

```
POST   api/schedules          auth:sanctum
POST   api/schedules/mobile   auth:sanctum
PATCH  api/schedules/{id}     auth:sanctum
```

Output yang SALAH:

```
POST   api/schedules          auth:sanctum,role:mitra,admin  ← Ada role!
```

---

## 📝 RINGKASAN

**Masalah**: Production butuh role mitra/admin untuk create schedule  
**Dampak**: End user dapat 403 Forbidden  
**Penyebab**: File routes/api.php di production belum update  
**Solusi**: Upload routes/api.php yang baru atau edit manual  
**Risiko**: RENDAH (cuma config, no DB change)  
**Waktu**: 5-10 menit  
**Test**: Create schedule harus 201 Created, bukan 403 Forbidden

---

## 🎯 ACTION ITEMS

### URGENT (Sekarang):

1. ✅ Upload routes/api.php ke production
2. ✅ Clear cache
3. ✅ Test create schedule

### IMPORTANT (Segera):

4. ⚠️ Fix GPS coordinates di Flutter (pakai koordinat Indonesia!)
5. ⚠️ Update Flutter field names (opsional tapi recommended)

### MONITORING (Ongoing):

6. 📊 Monitor Laravel logs
7. 📊 Check Sentry errors
8. 📊 Track database growth

---

**Dibuat**: <?php echo date('Y-m-d H:i:s'); ?>  
**Priority**: 🔥🔥🔥 CRITICAL  
**Status**: READY TO FIX  
**ETA**: 5-10 menit

---

## 📞 BUTUH BANTUAN?

Jika masih error setelah fix:

1. **Cek Laravel logs**:

    ```bash
    tail -50 /home/dumeg/public_html/gerobaks.dumeg.com/storage/logs/laravel.log
    ```

2. **Enable debug mode** (TEMPORARY):

    ```
    Edit .env:
    APP_DEBUG=true

    Test API, lihat error detail

    JANGAN LUPA set kembali:
    APP_DEBUG=false
    ```

3. **Share error logs** untuk analisis lebih lanjut

---

🎉 **SEMUA DOKUMENTASI LENGKAP! SIAP DEPLOY!**
