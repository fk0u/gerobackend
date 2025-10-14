# 🚀 Gerobaks Backend - Deployment Guide

## 📋 Daftar Isi

1. [Persiapan Local](#persiapan-local)
2. [Deployment ke cPanel](#deployment-ke-cpanel)
3. [Konfigurasi cPanel](#konfigurasi-cpanel)
4. [Testing Production](#testing-production)
5. [Troubleshooting](#troubleshooting)

---

## 🔧 Persiapan Local

### 1. Install Dependencies

```bash
cd backend
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### 2. Generate Production Key (jika belum ada)

```bash
php artisan key:generate
```

### 3. Optimize untuk Production

```bash
# Clear semua cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Generate cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Test Local

```bash
# Jalankan test
php artisan test

# Test API endpoints
php test_api_comprehensive.php
php test_cors.php
```

---

## 📤 Deployment ke cPanel

### Metode 1: Upload via File Manager (Recommended untuk pertama kali)

#### Step 1: Prepare Files

1. **Compress backend folder** (exclude folder yang tidak perlu):

    ```bash
    # Di root project
    zip -r gerobaks-backend.zip backend \
      -x "backend/node_modules/*" \
      -x "backend/vendor/*" \
      -x "backend/storage/logs/*" \
      -x "backend/.git/*" \
      -x "backend/database/database.sqlite"
    ```

2. **File yang HARUS di-exclude saat upload:**
    - `node_modules/` (akan di-install ulang di server)
    - `vendor/` (akan di-install ulang di server)
    - `storage/logs/*` (file log lama)
    - `.git/` (tidak diperlukan di production)
    - `database/database.sqlite` (database local)

#### Step 2: Upload ke cPanel

1. Login ke cPanel (`https://gerobaks.dumeg.com:2083`)
2. Buka **File Manager**
3. Navigate ke `public_html/`
4. Upload `gerobaks-backend.zip`
5. Extract file zip
6. Rename folder `backend` menjadi `api` (opsional, untuk URL lebih bersih)

#### Step 3: Set Permissions

```bash
# Via Terminal cPanel atau File Manager
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env
```

### Metode 2: Upload via Git (Recommended untuk update)

#### Step 1: Setup Git di cPanel

```bash
# Login SSH ke cPanel
ssh username@gerobaks.dumeg.com

# Navigate ke directory
cd public_html

# Clone repository
git clone https://github.com/aji-aali/Gerobaks.git
cd Gerobaks/backend
```

#### Step 2: Checkout Branch

```bash
git checkout fk0u/staging
git pull origin fk0u/staging
```

---

## ⚙️ Konfigurasi cPanel

### 1. Setup Database MySQL

#### Via cPanel MySQL Databases:

1. Login cPanel → **MySQL Databases**
2. **Create New Database:**
    - Database Name: `gerobaks_db`
    - Create Database
3. **Create Database User:**
    - Username: `gerobaks_user`
    - Password: `[STRONG_PASSWORD]` (simpan untuk .env)
    - Create User
4. **Add User to Database:**
    - User: `gerobaks_user`
    - Database: `gerobaks_db`
    - Privileges: **ALL PRIVILEGES**

#### Catat informasi ini:

```
DB_HOST: localhost (biasanya)
DB_PORT: 3306
DB_DATABASE: gerobaks_db
DB_USERNAME: gerobaks_user
DB_PASSWORD: [password yang dibuat]
```

### 2. Setup Environment File

#### Copy .env.example ke .env

```bash
cp .env.example .env
```

#### Edit .env untuk Production:

```env
APP_NAME=Gerobaks
APP_ENV=production
APP_KEY=base64:XXXXX  # Akan di-generate
APP_DEBUG=false
APP_URL=https://gerobaks.dumeg.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=gerobaks_db
DB_USERNAME=gerobaks_user
DB_PASSWORD=[YOUR_DB_PASSWORD]

# Cache & Session (gunakan database untuk shared hosting)
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Mail Configuration (opsional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@gerobaks.com
MAIL_FROM_NAME="${APP_NAME}"

# Production URLs
GEROBAKS_PRODUCTION_URL=https://gerobaks.dumeg.com
GEROBAKS_STAGING_URL=https://staging-gerobaks.dumeg.com
```

### 3. Install Composer Dependencies (via SSH Terminal)

```bash
# Login SSH
ssh username@gerobaks.dumeg.com

# Navigate ke folder backend
cd public_html/Gerobaks/backend

# Install Composer (jika belum ada)
curl -sS https://getcomposer.org/installer | php
php composer.phar install --no-dev --optimize-autoloader

# Atau jika composer sudah global
composer install --no-dev --optimize-autoloader
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Run Database Migrations

```bash
# Migrate database
php artisan migrate --force

# Seed database dengan data awal
php artisan db:seed --force
```

### 6. Setup Public Directory

#### Opsi A: Symbolic Link (jika supported)

```bash
# Di public_html/
ln -s Gerobaks/backend/public api
```

#### Opsi B: .htaccess Redirect

Buat file `.htaccess` di `public_html/`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^api/(.*)$ Gerobaks/backend/public/$1 [L]
</IfModule>
```

#### Opsi C: Copy Public Folder (not recommended)

```bash
cp -r backend/public/* public_html/api/
```

### 7. Setup File Permissions

```bash
# Set ownership (adjust user:group sesuai server)
chown -R username:username storage bootstrap/cache

# Set permissions
find storage -type d -exec chmod 755 {} \;
find storage -type f -exec chmod 644 {} \;
find bootstrap/cache -type d -exec chmod 755 {} \;
find bootstrap/cache -type f -exec chmod 644 {} \;
```

### 8. Optimize untuk Production

```bash
# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache config untuk performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### 9. Setup Cron Job (untuk Laravel Queue/Scheduler)

Via cPanel → **Cron Jobs**:

```
* * * * * cd /home/username/public_html/Gerobaks/backend && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔐 Security Checklist

### File .htaccess di Root Laravel

Buat/edit `backend/.htaccess`:

```apache
# Prevent directory browsing
Options -Indexes

# Deny access to sensitive files
<FilesMatch "^\.env">
    Order allow,deny
    Deny from all
</FilesMatch>

<FilesMatch "^composer\.(json|lock)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# PHP Settings
php_flag display_errors Off
php_value max_execution_time 300
php_value upload_max_filesize 20M
php_value post_max_size 20M
```

### File .htaccess di Public

Edit `backend/public/.htaccess` (sudah ada, pastikan ada):

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## 🧪 Testing Production

### 1. Test API Health

```bash
curl https://gerobaks.dumeg.com/api/health
```

Expected:

```json
{ "status": "ok" }
```

### 2. Test Login

```bash
curl -X POST https://gerobaks.dumeg.com/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"daffa@gmail.com","password":"password123"}'
```

Expected:

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {...},
    "token": "..."
  }
}
```

### 3. Test CORS

```bash
curl -I -X OPTIONS https://gerobaks.dumeg.com/api/login \
  -H "Origin: https://example.com" \
  -H "Access-Control-Request-Method: POST"
```

Check for CORS headers in response.

### 4. Test dari Flutter App

Update Flutter `lib/utils/app_config.dart`:

```dart
static const String apiBaseUrl = 'https://gerobaks.dumeg.com';
```

---

## 🐛 Troubleshooting

### Error: 500 Internal Server Error

**Kemungkinan penyebab:**

1. File permissions salah
2. .env tidak ada atau invalid
3. APP_KEY tidak di-set

**Solusi:**

```bash
# Set permissions
chmod -R 755 storage bootstrap/cache

# Regenerate key
php artisan key:generate

# Clear cache
php artisan config:clear
php artisan cache:clear
```

### Error: Database Connection Failed

**Kemungkinan penyebab:**

1. Kredensial DB salah di .env
2. Database belum dibuat
3. User tidak punya akses

**Solusi:**

1. Verify di cPanel → MySQL Databases
2. Test koneksi:
    ```bash
    php artisan tinker
    # Run: DB::connection()->getPdo();
    ```

### Error: 404 Not Found untuk /api/\*

**Kemungkinan penyebab:**

1. Public directory tidak di-set dengan benar
2. .htaccess tidak bekerja
3. mod_rewrite tidak aktif

**Solusi:**

1. Check .htaccess di public/
2. Contact hosting support untuk enable mod_rewrite

### Error: CORS Issues

**Solusi:**

```bash
# Pastikan middleware CORS sudah terdaftar
php artisan route:list

# Check file app/Http/Middleware/Cors.php
# Sudah di-fix di commit terakhir
```

### Error: Storage Permission Denied

**Solusi:**

```bash
# Set proper permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Atau jika perlu
chmod -R 777 storage
chmod -R 777 bootstrap/cache
```

### Error: Composer Not Found

**Solusi - Install Composer di cPanel:**

```bash
cd ~
curl -sS https://getcomposer.org/installer | php
mkdir bin
mv composer.phar bin/composer
chmod +x bin/composer

# Tambahkan ke PATH
echo 'export PATH="$HOME/bin:$PATH"' >> ~/.bashrc
source ~/.bashrc
```

---

## 🔄 Update/Deploy Ulang

### Via Git (Recommended)

```bash
# SSH ke server
ssh username@gerobaks.dumeg.com

cd public_html/Gerobaks/backend

# Pull latest changes
git pull origin fk0u/staging

# Update dependencies
composer install --no-dev --optimize-autoloader

# Run migrations (jika ada)
php artisan migrate --force

# Clear & cache
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Via File Upload

1. Upload file yang berubah via File Manager
2. Jalankan optimisasi via SSH/Terminal

---

## 📊 Monitoring & Logs

### View Logs

```bash
# Via SSH
tail -f storage/logs/laravel.log

# Atau via cPanel File Manager
# Navigate ke: storage/logs/laravel.log
```

### Clear Logs

```bash
# Truncate log file
echo "" > storage/logs/laravel.log

# Atau via artisan
php artisan log:clear  # jika ada command ini
```

---

## � Common Production Issues & Fixes

### Issue 1: "Data too long for column 'payload'" Error

**Error:**

```
SQLSTATE[22001]: String data, right truncated: 1406
Data too long for column 'payload' at row 1
```

**Cause:** Sessions table `payload` column (TEXT type, ~64KB) is too small for large session data.

**Quick Fix (Recommended - Via SSH):**

```bash
cd public_html/backend
chmod +x fix-session-payload.sh
./fix-session-payload.sh
```

**Manual Fix (Via phpMyAdmin):**

```sql
-- Run this SQL in phpMyAdmin
ALTER TABLE sessions MODIFY COLUMN payload LONGTEXT NOT NULL;

-- Then record migration
INSERT INTO migrations (migration, batch)
VALUES ('2025_01_14_000001_fix_sessions_payload_column',
        (SELECT MAX(batch) + 1 FROM (SELECT batch FROM migrations) AS temp));
```

**Or Use Migration:**

```bash
php artisan migrate --force --path=database/migrations/2025_01_14_000001_fix_sessions_payload_column.php
```

**Verify Fix:**

```sql
SHOW COLUMNS FROM sessions LIKE 'payload';
-- Type should be: longtext
```

**Related Files:**

-   `SESSION_PAYLOAD_FIX.md` - Full documentation
-   `fix-session-payload.sh` - Auto-fix script
-   `fix-session-payload.sql` - Manual SQL script
-   `database/migrations/2025_01_14_000001_fix_sessions_payload_column.php` - Migration

### Issue 2: 500 Internal Server Error

**Quick Checks:**

```bash
# Check .env exists
ls -la .env

# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Check logs
tail -f storage/logs/laravel.log

# Verify permissions
chmod -R 755 storage bootstrap/cache
```

### Issue 3: Database Connection Error

**Check .env:**

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

**Test connection:**

```bash
php artisan db:show
```

### Issue 4: CORS Error from Flutter App

**Already fixed in middleware, just clear cache:**

```bash
php artisan config:clear
php artisan route:clear
```

**Verify CORS middleware:**

```bash
php test_cors.php
```

### Issue 5: 404 on /api/\* Routes

**Check .htaccess:**

```bash
# Ensure .htaccess exists in public/
cat public/.htaccess
```

**Regenerate .htaccess:**

```bash
php artisan route:clear
```

**Check mod_rewrite enabled:**

-   In cPanel → Software → Select PHP Version → Check "rewrite" module

---

## �📞 Support

**Documentation:** `/docs/openapi.yaml`  
**API Docs:** `https://gerobaks.dumeg.com/docs`  
**Health Check:** `https://gerobaks.dumeg.com/api/health`

**Test Scripts di Backend:**

-   `test_api_comprehensive.php` - Test semua endpoint
-   `test_cors.php` - Test CORS configuration
-   `test_login.php` - Quick login test

---

## ✅ Deployment Checklist

Sebelum go-live, pastikan:

-   [ ] Database MySQL dibuat di cPanel
-   [ ] User database dibuat dengan privileges
-   [ ] File .env dikonfigurasi dengan benar
-   [ ] APP_KEY sudah di-generate
-   [ ] Composer dependencies ter-install
-   [ ] Database migrations dijalankan
-   [ ] Database seeder dijalankan (opsional)
-   [ ] File permissions di-set (755/644)
-   [ ] .htaccess configured
-   [ ] Cache di-clear dan di-regenerate
-   [ ] HTTPS/SSL certificate aktif
-   [ ] Test login API berhasil
-   [ ] Test CORS berhasil
-   [ ] Flutter app bisa connect
-   [ ] Cron job di-setup (jika perlu)
-   [ ] Backup database (untuk safety)

---

**Last Updated:** October 13, 2025  
**Laravel Version:** 11.x  
**PHP Version:** 8.1+  
**Recommended Hosting:** cPanel with SSH access
