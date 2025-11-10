# 🚛 Gerobaks Backend API

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/license-Proprietary-red?style=flat-square)
![Build](https://img.shields.io/badge/build-passing-brightgreen?style=flat-square)
![Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen?style=flat-square)
[![wakatime](https://wakatime.com/badge/user/cc62a71b-688a-408a-96de-c02f19b880ec/project/f39bfe89-e9bd-4aec-93e6-b6511d267bac.svg)](https://wakatime.com/badge/user/cc62a71b-688a-408a-96de-c02f19b880ec/project/f39bfe89-e9bd-4aec-93e6-b6511d267bac)

RESTful API Backend untuk sistem manajemen pengumpulan sampah Gerobaks - Menghubungkan pengguna dengan mitra pengumpul sampah secara efisien.

## 🔗 Quick Links

| Resource                | URL                                                                                          |
| ----------------------- | -------------------------------------------------------------------------------------------- |
| 🌐 **Production API**   | [https://gerobaks.dumeg.com](https://gerobaks.dumeg.com)                                     |
| 📖 **Swagger API Docs** | [https://gerobaks.dumeg.com/api/documentation](https://gerobaks.dumeg.com/api/documentation) |
| 📊 **Sentry Dashboard** | [https://sentry.io/gerobaks](https://sentry.io/organizations/gerobaks/projects/)             |
| 📝 **Changelog**        | [GitHub Commits](https://github.com/fk0u/gerobackend/commits)                                |
| 🐛 **Issue Tracker**    | [GitHub Issues](https://github.com/fk0u/gerobackend/issues)                                  |
| 👨‍💻 **Developer**        | [@fk0u](https://github.com/fk0u)                                                             |
| 🔗 **Repository**       | [https://github.com/fk0u/gerobackend](https://github.com/fk0u/gerobackend)                   |

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

## � Production Monitoring dengan Sentry

### Setup Sentry

Sentry sudah terintegrasi untuk monitoring error di production secara real-time.

#### 1. **Install Sentry SDK** (Sudah terinstall)

```bash
composer require sentry/sentry-laravel
php artisan sentry:publish --dsn
```

#### 2. **Konfigurasi Environment Variables**

Tambahkan ke `.env` production:

```env
# Sentry Configuration
SENTRY_LARAVEL_DSN=https://[your-key]@[region].ingest.sentry.io/[project-id]
SENTRY_TRACES_SAMPLE_RATE=1.0
SENTRY_ENVIRONMENT=production
SENTRY_RELEASE=gerobaks-backend@1.0.0
```

**Cara mendapatkan DSN:**

1. Login ke [sentry.io](https://sentry.io)
2. Create project "gerobaks-backend" (Laravel PHP)
3. Copy DSN dari Settings → Client Keys (DSN)
4. Paste ke `.env` production

#### 3. **Konfigurasi Lanjutan** (`config/sentry.php`)

File konfigurasi sudah include:

```php
<?php

return [
    'dsn' => env('SENTRY_LARAVEL_DSN'),

    'environment' => env('SENTRY_ENVIRONMENT', env('APP_ENV', 'production')),

    'release' => env('SENTRY_RELEASE'),

    'sample_rate' => (float) env('SENTRY_TRACES_SAMPLE_RATE', 1.0),

    'traces_sample_rate' => (float) env('SENTRY_TRACES_SAMPLE_RATE', 1.0),

    'send_default_pii' => false, // Jangan kirim data personal

    'breadcrumbs' => [
        'logs' => true,
        'sql_queries' => true,
        'sql_bindings' => true,
        'queue_info' => true,
        'command_info' => true,
    ],

    'integrations' => [
        \Sentry\Laravel\Integration::class,
    ],

    'before_send' => function (\Sentry\Event $event): ?\Sentry\Event {
        // Filter error yang tidak perlu dilaporkan
        if (str_contains($event->getMessage() ?? '', 'TokenMismatchException')) {
            return null;
        }
        return $event;
    },
];
```

#### 4. **Testing Sentry Integration**

Test apakah Sentry sudah terkoneksi:

```bash
php artisan sentry:test
```

Atau test manual dengan melempar exception:

```php
Route::get('/sentry-test', function () {
    throw new Exception('Sentry test exception dari production!');
});
```

Akses `https://gerobaks.dumeg.com/sentry-test` → error akan muncul di Sentry dashboard.

#### 5. **Monitoring di Sentry Dashboard**

**Dashboard URL:** [https://sentry.io/organizations/gerobaks/projects/](https://sentry.io/organizations/gerobaks/projects/)

**Fitur yang bisa dipantau:**

-   🐛 **Issues** - Error grouping dengan stacktrace lengkap
-   📈 **Performance** - API response time & slow queries
-   🔍 **Releases** - Track error per deployment
-   👤 **User Context** - Error yang dialami user tertentu (tanpa PII)
-   📊 **Alerts** - Email/Slack notification untuk error critical

**Alert Setup:**

1. Buka project di Sentry
2. Settings → Alerts → Create Alert Rule
3. Contoh rule:
    - **Name:** Critical API Errors
    - **Conditions:** When error count > 10 in 5 minutes
    - **Actions:** Send email to dev@gerobaks.com

#### 6. **Custom Error Tracking**

Track custom events di code:

```php
use Sentry\Laravel\Facades\Sentry;

// Capture exception dengan context
try {
    $schedule = Schedule::create($data);
} catch (\Exception $e) {
    Sentry::captureException($e, [
        'tags' => ['component' => 'schedule-create'],
        'extra' => [
            'user_id' => auth()->id(),
            'mitra_id' => $data['mitra_id'] ?? null,
        ],
    ]);
    throw $e;
}

// Capture custom message
Sentry::captureMessage('Schedule bulk import failed', [
    'level' => 'warning',
    'extra' => ['count' => $failedCount],
]);
```

#### 7. **Performance Monitoring**

Track slow API endpoints:

```php
// config/sentry.php
'traces_sample_rate' => 0.2, // Sample 20% requests untuk performance
```

Sentry akan otomatis track:

-   Database query performance
-   HTTP request duration
-   External API calls
-   Job queue processing time

---

## 📝 Changelog Integration dengan GitHub

### Swagger UI dengan Changelog Otomatis

API Documentation sudah terintegrasi dengan GitHub untuk menampilkan changelog terbaru.

#### 1. **Akses Changelog di Swagger UI**

Buka [https://gerobaks.dumeg.com/api/documentation](https://gerobaks.dumeg.com/api/documentation)

**Tab Changelog** menampilkan:

-   📅 Commit terbaru dari repository
-   👨‍💻 Author & timestamp
-   📝 Commit message
-   🔗 Link ke GitHub commit detail

#### 2. **Konfigurasi GitHub API**

File konfigurasi di `config/l5-swagger.php`:

```php
'changelog' => [
    'enabled' => env('SWAGGER_CHANGELOG_ENABLED', true),
    'github_repo' => env('GITHUB_REPO', 'fk0u/gerobackend'),
    'github_token' => env('GITHUB_TOKEN', null), // Optional untuk public repo
    'cache_ttl' => env('CHANGELOG_CACHE_TTL', 3600), // Cache 1 jam
    'limit' => env('CHANGELOG_LIMIT', 20), // Tampilkan 20 commit terakhir
],
```

**Environment Variables** (`.env`):

```env
# GitHub Changelog Configuration
SWAGGER_CHANGELOG_ENABLED=true
GITHUB_REPO=fk0u/gerobackend
GITHUB_TOKEN= # Opsional, untuk private repo atau rate limit lebih tinggi
CHANGELOG_CACHE_TTL=3600
CHANGELOG_LIMIT=20
```

#### 3. **GitHub Token Setup** (Opsional)

Untuk private repository atau rate limit lebih tinggi:

1. Buka [GitHub Settings → Developer Settings → Personal Access Tokens](https://github.com/settings/tokens)
2. Generate new token (classic)
3. Permissions: `repo` (untuk private) atau `public_repo` (untuk public)
4. Copy token dan paste ke `.env`:

```env
GITHUB_TOKEN=ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

#### 4. **Custom Changelog Controller**

File `app/Http/Controllers/Api/ChangelogController.php` handle fetching commits:

```php
<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ChangelogController extends Controller
{
    public function index(Request $request)
    {
        $repo = config('l5-swagger.changelog.github_repo');
        $token = config('l5-swagger.changelog.github_token');
        $limit = $request->get('limit', config('l5-swagger.changelog.limit', 20));

        $cacheKey = "github_changelog_{$repo}_{$limit}";
        $cacheTtl = config('l5-swagger.changelog.cache_ttl', 3600);

        $commits = Cache::remember($cacheKey, $cacheTtl, function () use ($repo, $token, $limit) {
            $url = "https://api.github.com/repos/{$repo}/commits?per_page={$limit}";

            $response = Http::withHeaders([
                'Accept' => 'application/vnd.github.v3+json',
                'Authorization' => $token ? "token {$token}" : null,
            ])->get($url);

            if (!$response->successful()) {
                return [];
            }

            return collect($response->json())->map(function ($commit) {
                return [
                    'sha' => substr($commit['sha'], 0, 7),
                    'message' => $commit['commit']['message'],
                    'author' => $commit['commit']['author']['name'],
                    'email' => $commit['commit']['author']['email'],
                    'date' => $commit['commit']['author']['date'],
                    'url' => $commit['html_url'],
                ];
            });
        });

        return response()->json([
            'success' => true,
            'data' => [
                'repository' => $repo,
                'commits' => $commits,
                'total' => $commits->count(),
            ],
        ]);
    }

    public function clearCache()
    {
        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'Changelog cache cleared successfully',
        ]);
    }
}
```

**Route** (`routes/api.php`):

```php
Route::get('/changelog', [ChangelogController::class, 'index']);
Route::post('/changelog/clear-cache', [ChangelogController::class, 'clearCache'])->middleware('auth:sanctum');
```

#### 5. **Swagger UI Custom Template**

File `resources/views/vendor/l5-swagger/index.blade.php` sudah include tab Changelog:

```html
<!-- Changelog Tab -->
<div id="changelog-tab" class="tab-pane">
    <h3>📝 Latest Changes</h3>
    <div id="changelog-container">
        <p>Loading changelog from GitHub...</p>
    </div>
</div>

<script>
    // Fetch changelog dari API
    fetch("/api/changelog")
        .then((res) => res.json())
        .then((data) => {
            const container = document.getElementById("changelog-container");

            if (!data.success || !data.data.commits.length) {
                container.innerHTML = "<p>No commits found.</p>";
                return;
            }

            const commitsHtml = data.data.commits
                .map(
                    (commit) => `
            <div class="commit-item">
                <div class="commit-header">
                    <span class="commit-sha">${commit.sha}</span>
                    <span class="commit-date">${new Date(
                        commit.date
                    ).toLocaleDateString()}</span>
                </div>
                <div class="commit-message">${commit.message}</div>
                <div class="commit-meta">
                    <span class="commit-author">👤 ${commit.author}</span>
                    <a href="${commit.url}" target="_blank">View on GitHub →</a>
                </div>
            </div>
        `
                )
                .join("");

            container.innerHTML = commitsHtml;
        })
        .catch((err) => {
            console.error("Failed to load changelog:", err);
            document.getElementById("changelog-container").innerHTML =
                '<p class="error">Failed to load changelog. Check console for details.</p>';
        });
</script>
```

#### 6. **Manual Clear Cache**

Jika changelog tidak update:

```bash
# Via artisan
php artisan cache:clear

# Via API (dengan auth)
curl -X POST https://gerobaks.dumeg.com/api/changelog/clear-cache \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### 7. **Rate Limiting**

GitHub API rate limit:

-   **Without token:** 60 requests/hour
-   **With token:** 5000 requests/hour

Cache TTL default 1 jam sudah cukup untuk menghindari rate limit.

---

## 🚀 Deployment Guide

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
