# 🚛 Gerobaks API

<div align="center">

[![Laravel](https://img.shields.io/badge/Laravel-12.x-ff2d20.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777bb4.svg)](https://www.php.net/)
[![Database](https://img.shields.io/badge/Database-MySQL%20%7C%20SQLite-lightgrey.svg)](https://dev.mysql.com/)
[![Auth](https://img.shields.io/badge/Auth-Laravel%20Sanctum-0d9488.svg)](https://laravel.com/docs/sanctum)
[![Status](https://img.shields.io/badge/Status-Beta-green.svg)]()

**REST API yang melayani seluruh fitur aplikasi mobile Gerobaks.**

[📦 Production Deployment](#-production-deployment) | [🚀 Quick Start](START_HERE.md) | [📖 Full Docs](DEPLOYMENT.md)

</div>

---

## 🔴 LISENSI CLOSED SOURCE

> **⚠️ PENTING**: Kode di repository ini sepenuhnya dimiliki oleh **Gerobaks** dan **BUKAN open source**.
> Semua source code, dokumentasi, dan aset intellectual property berada di bawah perlindungan hukum Gerobaks.

---

## 🧭 Ikhtisar

Gerobaks API merupakan layanan backend yang menangani autentikasi, manajemen jadwal pengambilan sampah, pelacakan armada, sistem pembayaran, notifikasi pintar, hingga chatbot AI. API ini dioptimalkan untuk terhubung dengan aplikasi Flutter Gerobaks sekaligus siap diperluas ke kanal lain seperti dashboard internal. Riwayat rilis dan perubahan utama dapat diikuti melalui [`CHANGELOG.md`](CHANGELOG.md).

### Modul Inti

-   👥 **Manajemen Pengguna & Peran**: End-user, mitra (driver), dan admin.
-   🔐 **Autentikasi Token**: Laravel Sanctum untuk login mobile dan integrasi layanan lain.
-   📅 **Jadwal & Order**: CRUD jadwal, penugasan armada, status order real-time.
-   📍 **Pelacakan Armada**: Endpoint tracking lokasi dan estimasi kedatangan.
-   💰 **Saldo & Pembayaran**: Ringkasan saldo, ledger transaksi, integrasi QRIS/e-wallet (mock).
-   🔔 **Notifikasi**: Feed notifikasi, penandaan dibaca, pengaturan preferensi.
-   💬 **Percakapan**: Kanal chat antara pengguna dan petugas mitra.
-   ⭐ **Rating & Feedback**: Kuesioner penilaian layanan.

### Integrasi dengan Aplikasi Mobile

-   **Flutter SDK** memanggil API melalui `API_BASE_URL` yang disinkronkan dengan pilihan environment pada halaman dokumentasi internal.
-   **Error monitoring**: Event ID dari Sentry dicatat pada log backend sehingga tim mobile dapat menautkan laporan crash dengan insiden server.
-   **Payload terenkripsi**: Field sensitif (alamat, catatan pembayaran, pesan chat) sudah dienkripsi oleh backend sebelum dikirimkan ke mobile, kompatibel dengan deserializer di aplikasi.
-   **Realtime feedback loop**: Endpoint health check (`/api/health`) dan ping server pada dokumentasi membantu QA mobile mengecek kesiapan environment.

## 🛠️ Teknologi

| Layer          | Teknologi                                            |
| -------------- | ---------------------------------------------------- |
| Framework      | Laravel 12.x                                         |
| Bahasa         | PHP 8.3+, TypeScript (Vite assets opsional)          |
| Autentikasi    | Laravel Sanctum, hashed tokens                       |
| Database       | MySQL 8 / MariaDB 10.5 / SQLite (pengembangan cepat) |
| Queue & Events | Database queue, event broadcasting, job pipeline     |
| Storage        | Local disk (pengembangan), siap S3 kompatibel        |
| Observability  | Sentry Laravel SDK (error & performance telemetry)   |
| Testing        | PHPUnit, Pest (optional), Laravel test suite         |

## 📘 Dokumentasi API (Swagger/OpenAPI)

-   File spesifikasi utama tersedia di `docs/openapi.yaml`.
-   Seluruh `summary` dan `description` memiliki versi Bahasa Indonesia (**ID**) dan Inggris (**EN**).
-   Bukalah menggunakan tool seperti [Swagger UI](https://github.com/swagger-api/swagger-ui), [Stoplight Studio](https://stoplight.io/studio/), atau ekstensi VS Code "OpenAPI".

### Menjalankan Swagger UI Lokal

```bash
git clone https://github.com/swagger-api/swagger-ui.git
cd swagger-ui
npm install
npm start
```

Setelah server berjalan di `http://localhost:3200/`, masukkan URL file `docs/openapi.yaml` Anda (misal `http://127.0.0.1:8000/docs/openapi.yaml` jika disajikan via server statis).

### Tips Multilingual

-   Gunakan anotasi **EN** / **ID** pada dokumen sebagai panduan cepat untuk bahasa.
-   Tambahkan ekstensi khusus (mis. `x-i18n`) bila membutuhkan versi bahasa tambahan.

## ✅ Prasyarat

-   PHP 8.3 atau lebih baru
-   Composer 2.6+
-   MySQL/MariaDB (opsional SQLite untuk local)
-   Node.js 18+ (jika ingin build asset Vite)
-   Redis (opsional, dapat diganti database queue)

## 🚀 Setup Pengembangan Lokal

### 1. Clone & Inisialisasi

```bash
git clone https://github.com/aji-aali/gerobaks-api.git
cd gerobaks-api
composer install
```

### 2. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan variabel berikut:

| Variabel                      | Keterangan                                    |
| ----------------------------- | --------------------------------------------- |
| `APP_URL`                     | URL base API (contoh `http://127.0.0.1:8000`) |
| `DB_CONNECTION` & kawan-kawan | Kredensial database                           |
| `SANCTUM_STATEFUL_DOMAINS`    | Domain client jika perlu SPA                  |
| `QUEUE_CONNECTION`            | Default `database`                            |
| `GEMINI_API_KEY`              | Jika ingin melibatkan AI service tambahan     |
| `MIDTRANS_SERVER_KEY`         | Placeholder untuk integrasi pembayaran        |

### 3. Migrasi & Seeder

```bash
php artisan migrate --seed
```

Seeder akan membuat:

-   1 admin, 3 petugas, dan 5 end-user dengan kredensial demo
-   Layanan pickup, jadwal bulanan, order aktif & historis
-   Ledger saldo, riwayat pembayaran, notifikasi, dan chat dummy

### 4. Menjalankan Server

```bash
php artisan serve
```

Server akan berjalan pada `http://127.0.0.1:8000`. Sesuaikan `API_BASE_URL` pada aplikasi Flutter menjadi alamat ini atau gunakan `http://10.0.2.2:8000` untuk Android emulator.

### 5. Queue & Scheduler (Opsional)

-   Jalankan worker queue: `php artisan queue:work`
-   Jalankan schedule lokal: `php artisan schedule:work`
-   Pastikan cron di production memanggil `php artisan schedule:run` setiap menit

## 🔐 Alur Autentikasi

1. Pengguna login dengan email & password menggunakan `POST /api/login`.
2. API mengembalikan token Sanctum (`plain_text_token`).
3. Token disimpan di secure storage Flutter dan dikirim via header `Authorization: Bearer {token}` pada setiap request berikutnya.
4. `POST /api/logout` akan mencabut token aktif.

Role tersedia:

-   `end_user`
-   `mitra`
-   `admin`

Gunakan header `X-Role` jika diperlukan untuk endpoint tertentu (lihat middleware `EnsureRole`).

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
