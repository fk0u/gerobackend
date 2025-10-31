# 🚛 Gerobaks Backend API

![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)

RESTful API Backend untuk sistem manajemen pengumpulan sampah Gerobaks - Menghubungkan pengguna dengan mitra pengumpul sampah secara efisien.

🌐 **Production**: [https://gerobaks.dumeg.com](https://gerobaks.dumeg.com)  
� **Full API Documentation**: [API_DOCUMENTATION_COMPLETE.md](./API_DOCUMENTATION_COMPLETE.md)  
👨‍💻 **Developer**: [@fk0u](https://github.com/fk0u)  
🔗 **Repository**: [https://github.com/fk0u/gerobackend](https://github.com/fk0u/gerobackend)

---

## ✨ Features

-   🔐 **Authentication & Authorization** - Laravel Sanctum with role-based access control
-   � **Schedule Management** - Complete CRUD with mobile app format support
-   📍 **Real-time Tracking** - GPS tracking for mitra location
-   💰 **Payment & Balance System** - Top-up, withdrawal, and transaction history
-   ⭐ **Rating & Feedback** - User rating and feedback system
-   💬 **Chat System** - Real-time messaging between users and mitra
-   🔔 **Notifications** - Push notifications for schedule updates
-   📊 **Dashboard Analytics** - Comprehensive statistics for users and mitra
-   🎫 **Subscription Management** - Multiple subscription plans
-   👨‍💼 **Admin Panel** - Complete admin management features

---

## 🚀 Quick Start

### Prerequisites

-   PHP >= 8.1
-   Composer
-   MySQL >= 8.0
-   Laravel 10.x

### Installation

1. **Clone the repository**

```bash
git clone https://github.com/fk0u/gerobackend.git
cd gerobackend
```

2. **Install dependencies**

```bash
composer install
```

3. **Environment setup**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database** (edit `.env`)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gerobaks
DB_USERNAME=root
DB_PASSWORD=
```

5. **Run migrations and seeders**

```bash
php artisan migrate --seed
```

6. **Start development server**

```bash
php artisan serve
```

API akan berjalan di `http://127.0.0.1:8000`

---

## 📡 API Endpoints Overview

### Authentication

-   `POST /api/register` - Register new user
-   `POST /api/login` - Login and get token
-   `GET /api/auth/me` - Get current user
-   `POST /api/auth/logout` - Logout

### Schedules

-   `GET /api/schedules` - Get all schedules
-   `POST /api/schedules` - Create schedule (mitra/admin)
-   `POST /api/schedules/mobile` - Create schedule (end_user)
-   `PATCH /api/schedules/{id}` - Update schedule
-   `POST /api/schedules/{id}/complete` - Complete schedule
-   `POST /api/schedules/{id}/cancel` - Cancel schedule

### Tracking

-   `GET /api/tracking` - Get tracking data
-   `POST /api/tracking` - Create tracking point
-   `GET /api/tracking/schedule/{id}` - Get tracking by schedule

### Orders, Payments, Balance, Ratings, and more...

📚 **See full documentation**: [API_DOCUMENTATION_COMPLETE.md](./API_DOCUMENTATION_COMPLETE.md)

---

## � Demo Credentials

### End User (Pelanggan)

```
Email: daffa@gmail.com
Password: password123
Role: end_user
```

### Mitra (Driver)

```
Email: driver.jakarta@gerobaks.com
Password: mitra123
Role: mitra
```

### Admin

```
Email: admin@gerobaks.com
Password: admin123
Role: admin
```

---

## 📖 Documentation

-   **Complete API Documentation**: [API_DOCUMENTATION_COMPLETE.md](./API_DOCUMENTATION_COMPLETE.md)
-   **Deployment Guide**: [DEPLOYMENT.md](./DEPLOYMENT.md)
-   **Database ERD**: [ERD_COMPLIANCE_SUMMARY.md](./ERD_COMPLIANCE_SUMMARY.md)
-   **API Changelog**: [CHANGELOG.md](./CHANGELOG.md)

---

## 🧪 Testing

### Health Check

```bash
curl http://127.0.0.1:8000/api/health
```

### Test Login

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "daffa@gmail.com",
    "password": "password123"
  }'
```

### Run Tests

```bash
php artisan test
```

---

## 📡 Ringkasan Endpoint

| Method | Endpoint                        | Deskripsi                       | Role            |
| ------ | ------------------------------- | ------------------------------- | --------------- |
| POST   | `/api/login`                    | Login dan mendapat token        | Semua           |
| POST   | `/api/logout`                   | Cabut token aktif               | Authenticated   |
| GET    | `/api/profile`                  | Profil user yang login          | Semua           |
| GET    | `/api/schedules`                | Daftar jadwal pengambilan       | end_user, mitra |
| GET    | `/api/orders`                   | Riwayat order dengan pagination | end_user        |
| POST   | `/api/orders/{order}/cancel`    | Batalkan order                  | end_user        |
| GET    | `/api/tracking/{order}`         | Posisi armada real-time         | end_user        |
| GET    | `/api/balance/summary`          | Ringkasan saldo & poin          | end_user        |
| GET    | `/api/balance/ledger`           | Riwayat transaksi saldo         | end_user        |
| GET    | `/api/notifications`            | Feed notifikasi                 | Semua           |
| POST   | `/api/notifications/{id}/read`  | Tandai notifikasi dibaca        | Semua           |
| GET    | `/api/chats`                    | Daftar percakapan               | end_user, mitra |
| POST   | `/api/chats`                    | Kirim pesan baru                | end_user, mitra |
| POST   | `/api/payments/{order}/confirm` | Konfirmasi pembayaran           | admin           |
| POST   | `/api/ratings`                  | Kirim rating layanan            | end_user        |

> Detail lengkap lihat file route `routes/api.php` dan masing-masing controller di `app/Http/Controllers/Api/`.

## 📦 Seeder Pengguna Demo

| Role     | Email                  | Password   |
| -------- | ---------------------- | ---------- |
| Admin    | `admin@gerobaks.test`  | `password` |
| Mitra    | `mitra1@gerobaks.test` | `password` |
| End User | `user1@gerobaks.test`  | `password` |

> Ubah kredensial sebelum produksi. Seeder dibuat untuk pengujian lokal.

## 🔧 Skrip Artisan Berguna

-   `php artisan optimize`: Membersihkan cache konfigurasi & route
-   `php artisan storage:link`: Membuat symlink storage (bila butuh upload)
-   `php artisan make:module ...`: Gunakan blueprint internal (lihat `README-dev.md` bila ada)
-   `php artisan tinker`: Eksperimen cepat dengan data

## 📈 Observability & Incident Response

-   **Sentry Laravel SDK** aktif pada channel log `stack` (`single,sentry`). Pastikan `SENTRY_LARAVEL_DSN` terisi pada `.env` produksi.
-   Variabel `SENTRY_TRACES_SAMPLE_RATE` default `1.0` untuk staging sehingga request dari aplikasi mobile tercatat lengkap. Turunkan nilainya (`0.2`–`0.3`) bila beban produksi meningkat.
-   Ganti `SENTRY_SEND_DEFAULT_PII` ke `false` jika deployment tertentu tidak boleh mengirim data PII (email/IP) ke Sentry.
-   Gunakan tombol "Ping" dan "Use Server" di halaman dokumentasi internal (Blade `docs/index.blade.php`) untuk menguji health check API sebelum rilis mobile.

## 🧪 Testing

```bash
php artisan test           # Menjalankan seluruh test suite
php artisan test --filter=OrderTest
```

Tambahkan test baru di folder `tests/Feature` untuk flow API dan `tests/Unit` untuk logika helper.

## 📦 Production Deployment

### 🚀 Quick Start (cPanel)

**Deployment ke cPanel bisa selesai dalam ~20 menit!**

#### Step 1: Persiapan Local

```bash
# Windows
cd backend
deploy-prepare.bat

# Linux/Mac
chmod +x deploy-prepare.sh
./deploy-prepare.sh
```

#### Step 2: Upload ke cPanel

1. Login ke cPanel: https://gerobaks.dumeg.com:2083
2. File Manager → Upload ZIP yang di-generate
3. Extract di `public_html/`

#### Step 3: Konfigurasi Server

```bash
# Via SSH (Recommended)
ssh username@gerobaks.dumeg.com
cd public_html/backend
chmod +x deploy-server.sh
./deploy-server.sh
```

#### Step 4: Verify

```bash
curl https://gerobaks.dumeg.com/api/health
# Expected: {"status":"ok"}
```

### 📚 Complete Documentation

Kami menyediakan dokumentasi deployment yang lengkap dan terstruktur:

| File                                                           | Description                         | When to Use               |
| -------------------------------------------------------------- | ----------------------------------- | ------------------------- |
| **[START_HERE.md](START_HERE.md)**                             | 🗺️ Navigator & decision tree        | Bingung mulai dari mana   |
| **[QUICKSTART-CPANEL.md](QUICKSTART-CPANEL.md)**               | ⚡ Quick deployment guide (~20 min) | First-time deployment     |
| **[DEPLOYMENT.md](DEPLOYMENT.md)**                             | 📖 Complete reference (400+ lines)  | Troubleshooting & details |
| **[DEPLOYMENT_FILES_SUMMARY.md](DEPLOYMENT_FILES_SUMMARY.md)** | 📦 File list & explanation          | Overview semua tools      |
| **[API_CREDENTIALS.md](API_CREDENTIALS.md)**                   | 🔑 Test accounts & credentials      | Butuh login info          |
| **[BACKEND_API_VERIFICATION.md](BACKEND_API_VERIFICATION.md)** | ✅ Test results & procedures        | Verify API working        |

### 🤖 Automation Scripts

| Script               | Platform     | Purpose                       |
| -------------------- | ------------ | ----------------------------- |
| `deploy-prepare.sh`  | Linux/Mac    | Local preparation & packaging |
| `deploy-prepare.bat` | Windows      | Local preparation & packaging |
| `deploy-server.sh`   | Server (SSH) | Server-side deployment        |

### 🧪 Test Scripts

Verify API functionality before production:

```bash
php test_api_comprehensive.php  # Test all endpoints
php test_cors.php                # Verify CORS headers
php test_login.php              # Quick login test
```

### ✅ Production Checklist

-   [ ] Set `APP_ENV=production` dan `APP_DEBUG=false`
-   [ ] Database MySQL configured (tidak pakai SQLite)
-   [ ] SSL certificate active (HTTPS)
-   [ ] Run migrations: `php artisan migrate --force`
-   [ ] Run seeders: `php artisan db:seed --force`
-   [ ] Optimize caches: `php artisan config:cache && route:cache`
-   [ ] Set permissions: `chmod -R 755 storage bootstrap/cache`
-   [ ] Test endpoints: All return JSON dengan CORS headers
-   [ ] Update Flutter app: Change `apiBaseUrl` to production URL
-   [ ] Monitor logs: `tail -f storage/logs/laravel.log`

### 🔧 Production URLs

-   **API Base:** https://gerobaks.dumeg.com
-   **API Docs:** https://gerobaks.dumeg.com/docs
-   **Health Check:** https://gerobaks.dumeg.com/api/health
-   **cPanel:** https://gerobaks.dumeg.com:2083

### 🆘 Common Issues

| Issue            | Quick Fix                                |
| ---------------- | ---------------------------------------- |
| 500 Error        | `php artisan config:clear`               |
| Database Error   | Check `.env` credentials                 |
| Permission Error | `chmod -R 755 storage bootstrap/cache`   |
| CORS Error       | Already fixed in middleware, clear cache |
| 404 on /api/\*   | Check `.htaccess`, enable mod_rewrite    |

**Need detailed help?** → Read [DEPLOYMENT.md](DEPLOYMENT.md) Troubleshooting section

## 🤝 Kontribusi Internal

-   Branch utama: `main`
-   Ikuti konvensi commit conventional (`feat:`, `fix:`)
-   PR harus menyertakan test bila menyentuh business logic
-   Jalankan `php artisan test` sebelum push

## 📞 Kontak

-   Engineering Lead: dev@gerobaks.com
-   Product: product@gerobaks.com
-   Support Teknis: support@gerobaks.com

## 📄 Lisensi

**🔴 CLOSED SOURCE LICENSE**

Seluruh kode sumber, aset, dan dokumentasi adalah milik eksklusif Gerobaks.

Dilarang untuk:

-   Mendistribusikan atau mempublikasikan ulang kode
-   Melakukan reverse engineering
-   Membuat karya turunan tanpa izin
-   Menggunakan untuk kepentingan komersial tanpa persetujuan tertulis

Permintaan kerjasama dan perizinan: **legal@gerobaks.com**

---

<div align="center">

**🌱 Gerobaks API — Mendukung ekosistem pengelolaan sampah yang bersih dan berkelanjutan. 🌱**

</div>
