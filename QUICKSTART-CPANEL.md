# 🚀 Quick Start - Deploy ke cPanel

Panduan singkat deployment backend Laravel ke cPanel.

## 📋 Prerequisites Checklist

-   [ ] Akses cPanel (https://gerobaks.dumeg.com:2083)
-   [ ] Akses SSH (opsional, tapi sangat direkomendasikan)
-   [ ] Database MySQL credentials siap
-   [ ] Domain/subdomain sudah pointing ke server
-   [ ] SSL certificate aktif (HTTPS)

## ⚡ Quick Deploy (3 Langkah Utama)

### 1️⃣ Persiapan Local (5 menit)

**Windows:**

```batch
cd backend
deploy-prepare.bat
```

**Linux/Mac:**

```bash
cd backend
chmod +x deploy-prepare.sh
./deploy-prepare.sh
```

**Output:**

-   File: `gerobaks-backend-YYYYMMDD_HHMMSS.zip`
-   Checklist: `deployment-checklist-YYYYMMDD_HHMMSS.txt`

### 2️⃣ Upload ke cPanel (5 menit)

1. **Login cPanel**

    - URL: `https://gerobaks.dumeg.com:2083`
    - Masukkan username & password

2. **Upload File**

    - Buka **File Manager**
    - Navigate ke `public_html/`
    - Klik **Upload**
    - Pilih file `gerobaks-backend-*.zip`
    - Tunggu upload selesai

3. **Extract File**
    - Klik kanan pada file zip
    - Pilih **Extract**
    - Extract ke `public_html/`
    - Rename folder `backend` → `api` (opsional)

### 3️⃣ Konfigurasi Server (10 menit)

#### A. Setup Database (via cPanel)

1. **MySQL Databases**

    ```
    Create Database:
    - Name: gerobaks_db

    Create User:
    - Username: gerobaks_user
    - Password: [STRONG_PASSWORD]

    Add User to Database:
    - User: gerobaks_user
    - Database: gerobaks_db
    - Privileges: ALL
    ```

2. **Catat credentials:**
    ```
    DB_HOST=localhost
    DB_DATABASE=gerobaks_db
    DB_USERNAME=gerobaks_user
    DB_PASSWORD=your_password
    ```

#### B. Setup Environment (via SSH atau File Manager)

**Via SSH (Recommended):**

```bash
ssh username@gerobaks.dumeg.com
cd public_html/backend
```

**Edit .env:**

```bash
nano .env
# Atau via File Manager → Edit
```

**Update values:**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://gerobaks.dumeg.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=gerobaks_db
DB_USERNAME=gerobaks_user
DB_PASSWORD=your_password_here
```

#### C. Run Server Script (via SSH)

```bash
chmod +x deploy-server.sh
./deploy-server.sh
```

Script akan:

-   ✅ Install Composer dependencies
-   ✅ Generate APP_KEY
-   ✅ Set file permissions
-   ✅ Run migrations
-   ✅ Seed database (optional)
-   ✅ Optimize untuk production

#### D. Manual Steps (jika SSH tidak tersedia)

**Via cPanel Terminal atau File Manager:**

1. **Install Dependencies:**

    ```bash
    cd public_html/backend
    php composer.phar install --no-dev --optimize-autoloader
    # Atau: composer install --no-dev --optimize-autoloader
    ```

2. **Generate Key:**

    ```bash
    php artisan key:generate --force
    ```

3. **Set Permissions:**

    ```bash
    chmod -R 755 storage bootstrap/cache
    ```

4. **Run Migrations:**

    ```bash
    php artisan migrate --force
    php artisan db:seed --force
    ```

5. **Optimize:**
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

## ✅ Verification (2 menit)

### Test 1: Health Check

```bash
curl https://gerobaks.dumeg.com/api/health
```

**Expected:** `{"status":"ok"}`

### Test 2: API Login

```bash
curl -X POST https://gerobaks.dumeg.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"daffa@gmail.com","password":"password123"}'
```

**Expected:** JSON response dengan token

### Test 3: CORS Headers

```bash
curl -I https://gerobaks.dumeg.com/api/login \
  -H "Origin: https://example.com"
```

**Expected:** Headers `Access-Control-Allow-Origin` ada

### Test 4: Documentation

Buka browser: `https://gerobaks.dumeg.com/docs`

## 🎯 Common Issues & Quick Fix

| Issue            | Quick Fix                                           |
| ---------------- | --------------------------------------------------- |
| 500 Error        | Check `.env` exists, run `php artisan config:clear` |
| Database Error   | Verify credentials in `.env`, check user privileges |
| Permission Error | Run `chmod -R 755 storage bootstrap/cache`          |
| 404 on /api/\*   | Check `.htaccess` in public/, enable mod_rewrite    |
| CORS Error       | Middleware already fixed, clear cache               |
| **Session Payload Error** | **Run `./fix-session-payload.sh` or see below** |

### 🔥 Session Payload Error (Production)

If you see: `SQLSTATE[22001]: Data too long for column 'payload'`

**Quick Fix:**
```bash
# Via SSH
cd public_html/backend
chmod +x fix-session-payload.sh
./fix-session-payload.sh
```

**Or via phpMyAdmin:**
```sql
ALTER TABLE sessions MODIFY COLUMN payload LONGTEXT NOT NULL;
```

**Read full docs:** [SESSION_PAYLOAD_FIX.md](./SESSION_PAYLOAD_FIX.md)

## 📱 Update Flutter App

Edit `lib/utils/app_config.dart`:

```dart
static const String apiBaseUrl = 'https://gerobaks.dumeg.com';
```

Rebuild dan test:

```bash
flutter clean
flutter pub get
flutter run
```

## 🔄 Deploy Updates (Re-deployment)

### Via Git (Recommended)

```bash
ssh username@gerobaks.dumeg.com
cd public_html/backend
git pull origin fk0u/staging
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
```

### Via File Upload

1. Upload only changed files
2. Re-run optimization:
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

## 🆘 Need Help?

1. **Check logs:**

    ```bash
    tail -f storage/logs/laravel.log
    ```

2. **Read full documentation:**

    - [DEPLOYMENT.md](./DEPLOYMENT.md) - Complete guide
    - [README.md](./README.md) - Project overview

3. **Test scripts:**
    ```bash
    php test_api_comprehensive.php
    php test_cors.php
    ```

## 📞 Support Contacts

-   **Documentation:** `/docs/` in this repo
-   **API Docs:** `https://gerobaks.dumeg.com/docs`
-   **Repository:** https://github.com/aji-aali/Gerobaks

---

**Total Time:** ~20 menit  
**Difficulty:** ⭐⭐ Intermediate  
**Success Rate:** 95%+ (if following steps)

**Next:** After deployment, test with Flutter app! 🚀
