# 🚀 Gerobaks API - Quick Start Guide

Panduan cepat untuk setup dan testing Gerobaks Backend API.

**Repository**: [https://github.com/fk0u/gerobackend](https://github.com/fk0u/gerobackend)

---

## 📦 Prerequisites

Pastikan Anda sudah menginstall:

-   ✅ PHP >= 8.1
-   ✅ Composer >= 2.0
-   ✅ MySQL >= 8.0 (atau MariaDB >= 10.5)
-   ✅ Git
-   ✅ Text Editor (VS Code recommended)

---

## ⚡ 5-Minute Setup

### 1. Clone Repository

```bash
git clone https://github.com/fk0u/gerobackend.git
cd gerobackend
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Setup Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Database

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gerobaks
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Create Database

```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE gerobaks CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 6. Run Migrations & Seeders

```bash
php artisan migrate --seed
```

### 7. Start Server

```bash
php artisan serve
```

✅ **API Ready!** Akses di `http://127.0.0.1:8000/api`

---

## 🧪 Quick Test

### Test 1: Health Check

```bash
curl http://127.0.0.1:8000/api/health
```

**Expected Response:**

```json
{
    "status": "ok"
}
```

### Test 2: Login

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "daffa@gmail.com",
    "password": "password123"
  }'
```

**Expected Response:**

```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 5,
            "name": "User Daffa",
            "email": "daffa@gmail.com",
            "role": "end_user"
        },
        "token": "33|vPTYQr0DF4ESykfUGg1aly2PKc50273Ex6HH0UC50e894e5d"
    }
}
```

### Test 3: Get User Info

```bash
# Replace YOUR_TOKEN with token from login response
curl http://127.0.0.1:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

---

## 👥 Demo Accounts

Seeder otomatis membuat akun-akun berikut:

### End Users (Pelanggan)

| Name         | Email            | Password    | Role     |
| ------------ | ---------------- | ----------- | -------- |
| User Daffa   | daffa@gmail.com  | password123 | end_user |
| Jane San     | sansan@gmail.com | password456 | end_user |
| Lionel Wahyu | wahyuh@gmail.com | password789 | end_user |

### Mitra (Drivers)

| Name            | Email                            | Password | Role  |
| --------------- | -------------------------------- | -------- | ----- |
| Ahmad Kurniawan | driver.jakarta@gerobaks.com      | mitra123 | mitra |
| Budi Santoso    | driver.bandung@gerobaks.com      | mitra123 | mitra |
| Siti Nurhaliza  | supervisor.surabaya@gerobaks.com | mitra123 | mitra |

### Admin

| Name          | Email              | Password | Role  |
| ------------- | ------------------ | -------- | ----- |
| Administrator | admin@gerobaks.com | admin123 | admin |

---

## 📱 Testing with Postman

### Import Collection

1. Open Postman
2. Click **Import**
3. Select file: `Gerobaks_API.postman_collection.json`
4. Import environment:
    - `Gerobaks_Local.postman_environment.json` (for local)
    - `Gerobaks_Production.postman_environment.json` (for production)

### Select Environment

1. Click environment dropdown (top-right)
2. Select "Gerobaks Local" or "Gerobaks Production"

### Run Requests

1. **Authentication** → **Login End User**
    - Token akan otomatis tersimpan di environment variable
2. Jalankan request lainnya
    - Token otomatis digunakan di header

---

## 🔧 Common Issues & Solutions

### Issue 1: Database Connection Error

**Error:**

```
SQLSTATE[HY000] [2002] Connection refused
```

**Solution:**

1. Pastikan MySQL service running:

    ```bash
    # Windows
    net start mysql

    # Linux/Mac
    sudo systemctl start mysql
    ```

2. Check kredensial di `.env`
3. Pastikan database sudah dibuat

---

### Issue 2: Key Not Generated

**Error:**

```
No application encryption key has been specified
```

**Solution:**

```bash
php artisan key:generate
```

---

### Issue 3: Migration Failed

**Error:**

```
SQLSTATE[42S01]: Base table or view already exists
```

**Solution:**

```bash
# Reset database
php artisan migrate:fresh --seed
```

---

### Issue 4: Permission Denied (Storage)

**Error:**

```
file_put_contents(): failed to open stream: Permission denied
```

**Solution:**

```bash
# Linux/Mac
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache

# Windows (run as Administrator)
icacls storage /grant Users:F /T
icacls bootstrap\cache /grant Users:F /T
```

---

## 📚 Next Steps

1. **Read Full Documentation**

    - [API_DOCUMENTATION_COMPLETE.md](./API_DOCUMENTATION_COMPLETE.md)

2. **Explore Endpoints**

    - Use Postman collection untuk testing semua endpoint
    - Check API documentation untuk detail request/response

3. **Integrate with Mobile App**

    - Update `API_BASE_URL` di Flutter app
    - Test login flow
    - Test create schedule

4. **Deploy to Production**
    - Follow [DEPLOYMENT.md](./DEPLOYMENT.md)
    - Configure production environment
    - Setup SSL certificate

---

## 🎯 Key Endpoints

### Authentication

-   `POST /api/login` - Login
-   `POST /api/register` - Register
-   `GET /api/auth/me` - Get current user
-   `POST /api/auth/logout` - Logout

### Schedules

-   `GET /api/schedules` - Get all schedules
-   `POST /api/schedules/mobile` - Create schedule (end_user)
-   `POST /api/schedules` - Create schedule (mitra/admin)

### Tracking

-   `GET /api/tracking` - Get tracking data
-   `POST /api/tracking` - Create tracking point

### Balance

-   `GET /api/balance/summary` - Get balance
-   `POST /api/balance/topup` - Top up balance

---

## 📞 Need Help?

-   📚 **Documentation**: [API_DOCUMENTATION_COMPLETE.md](./API_DOCUMENTATION_COMPLETE.md)
-   🐛 **Issues**: [GitHub Issues](https://github.com/fk0u/gerobackend/issues)
-   💬 **Discussions**: [GitHub Discussions](https://github.com/fk0u/gerobackend/discussions)

---

## ✅ Checklist

-   [ ] PHP 8.1+ installed
-   [ ] Composer installed
-   [ ] MySQL running
-   [ ] Repository cloned
-   [ ] Dependencies installed
-   [ ] .env configured
-   [ ] Database created
-   [ ] Migrations run
-   [ ] Seeders run
-   [ ] Server started
-   [ ] Health check passed
-   [ ] Login test passed
-   [ ] Postman collection imported

---

**Made with ❤️ by [@fk0u](https://github.com/fk0u)**

**Repository**: [https://github.com/fk0u/gerobackend](https://github.com/fk0u/gerobackend)
