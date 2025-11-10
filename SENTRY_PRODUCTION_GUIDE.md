# 🚨 Panduan Lengkap Sentry Production Monitoring

## 📋 Daftar Isi

1. [Setup Awal Sentry](#setup-awal-sentry)
2. [Konfigurasi Environment](#konfigurasi-environment)
3. [Testing Integration](#testing-integration)
4. [Dashboard & Alert](#dashboard--alert)
5. [Custom Error Tracking](#custom-error-tracking)
6. [Performance Monitoring](#performance-monitoring)
7. [Troubleshooting](#troubleshooting)

---

## 🎯 Setup Awal Sentry

### Step 1: Buat Account & Project di Sentry

1. **Sign up ke Sentry.io**

    - Kunjungi [https://sentry.io/signup/](https://sentry.io/signup/)
    - Bisa pakai GitHub/Google account atau email

2. **Create Organization**

    - Organization Name: `Gerobaks` atau nama perusahaan
    - Click "Create Organization"

3. **Create Project**

    - Platform: **PHP → Laravel**
    - Project Name: `gerobaks-backend`
    - Alert Settings: Default (bisa diubah nanti)
    - Click "Create Project"

4. **Copy DSN**
    - Setelah project dibuat, akan muncul DSN
    - Format: `https://[key]@[region].ingest.sentry.io/[project-id]`
    - **SIMPAN DSN INI** untuk langkah berikutnya

### Step 2: Install Sentry SDK (Sudah Terinstall)

Jika belum terinstall, jalankan:

```bash
composer require sentry/sentry-laravel

# Publish config file
php artisan sentry:publish --dsn
```

SDK sudah otomatis terintegrasi dengan Laravel exception handler.

---

## ⚙️ Konfigurasi Environment

### Production Environment (.env)

Edit file `.env` di production server:

```env
# Sentry Configuration
SENTRY_LARAVEL_DSN=https://YOUR_KEY@YOUR_REGION.ingest.sentry.io/PROJECT_ID
SENTRY_ENVIRONMENT=production
SENTRY_RELEASE=gerobaks-backend@1.0.0
SENTRY_SEND_DEFAULT_PII=false

# Sample Rate (1.0 = 100% error tracking)
SENTRY_SAMPLE_RATE=1.0

# Performance Monitoring (0.25 = 25% requests)
SENTRY_TRACES_SAMPLE_RATE=0.25

# Breadcrumbs (debugging context)
SENTRY_BREADCRUMBS_SQL_QUERIES_ENABLED=true
SENTRY_BREADCRUMBS_SQL_BINDINGS_ENABLED=false
SENTRY_BREADCRUMBS_LOGS_ENABLED=true
SENTRY_BREADCRUMBS_CACHE_ENABLED=true
SENTRY_BREADCRUMBS_QUEUE_INFO_ENABLED=true
SENTRY_BREADCRUMBS_COMMAND_JOBS_ENABLED=true
SENTRY_BREADCRUMBS_HTTP_CLIENT_REQUESTS_ENABLED=true
```

### Penjelasan Environment Variables

| Variable                    | Value                    | Penjelasan                                             |
| --------------------------- | ------------------------ | ------------------------------------------------------ |
| `SENTRY_LARAVEL_DSN`        | `https://...`            | **WAJIB** - DSN dari project Sentry                    |
| `SENTRY_ENVIRONMENT`        | `production`             | Environment name (production/staging/local)            |
| `SENTRY_RELEASE`            | `gerobaks-backend@1.0.0` | Versi release untuk tracking                           |
| `SENTRY_SEND_DEFAULT_PII`   | `false`                  | Jangan kirim data personal (email, IP, dsb)            |
| `SENTRY_SAMPLE_RATE`        | `1.0`                    | 100% error ditrack. Bisa dikurangi jika traffic tinggi |
| `SENTRY_TRACES_SAMPLE_RATE` | `0.25`                   | 25% request untuk performance monitoring               |

### Logging Configuration (config/logging.php)

Sudah dikonfigurasi di `LOG_STACK=single,sentry`:

```php
'stack' => [
    'driver' => 'stack',
    'channels' => ['single', 'sentry'],
    'ignore_exceptions' => false,
],

'sentry' => [
    'driver' => 'sentry',
    'level' => env('LOG_LEVEL', 'error'), // Only log errors and above
],
```

---

## 🧪 Testing Integration

### Test 1: Artisan Command

```bash
php artisan sentry:test
```

**Output yang diharapkan:**

```
[Sentry] DSN correctly configured.
[Sentry] Generating test event
[Sentry] Sending test event
[Sentry] Event sent: https://sentry.io/organizations/gerobaks/issues/?project=PROJECT_ID
```

### Test 2: Manual Exception Route

Tambahkan route test di `routes/web.php`:

```php
Route::get('/sentry-test', function () {
    throw new \Exception('🧪 Sentry production test dari Gerobaks Backend!');
});
```

Akses: `https://gerobaks.dumeg.com/sentry-test`

### Test 3: API Error Test

```bash
curl -X POST https://gerobaks.dumeg.com/api/schedules \
  -H "Content-Type: application/json" \
  -d '{"invalid": "data"}'
```

Error validation akan muncul di Sentry dengan context lengkap.

### Test 4: Check di Sentry Dashboard

1. Login ke [https://sentry.io](https://sentry.io)
2. Pilih organization → project `gerobaks-backend`
3. Menu **Issues** → Harus ada test exception tadi
4. Click issue untuk lihat detail:
    - Stacktrace lengkap
    - Request headers
    - User context (tanpa PII)
    - Breadcrumbs (SQL queries, logs)

---

## 📊 Dashboard & Alert

### Sentry Dashboard Features

**URL:** https://sentry.io/organizations/YOUR_ORG/projects/

#### 1. **Issues Tab**

-   Error grouping otomatis
-   Filter by status: unresolved, resolved, ignored
-   Search: `is:unresolved environment:production`
-   Sort by: frequency, last seen, users affected

#### 2. **Performance Tab**

-   API endpoint response time
-   Slow database queries
-   Transaction breakdown
-   Web vitals (jika ada frontend)

#### 3. **Releases Tab**

-   Error tracking per versi release
-   Regression detection (error baru di release tertentu)
-   Deploy history

#### 4. **Alerts Tab**

-   Email/Slack notification
-   Custom alert rules

### Setup Email Alerts

1. **Settings → Alerts → Create Alert Rule**

    **Example Alert: Critical API Errors**

    ```
    Trigger: When error count is more than 10 in 5 minutes
    Environment: production
    Actions:
      - Send email to dev@gerobaks.com
      - Send Slack message to #backend-alerts
    ```

2. **Example Alert: Database Performance**

    ```
    Trigger: When p95 database query time is above 500ms
    Environment: production
    Actions:
      - Send email to dba@gerobaks.com
    ```

3. **Example Alert: New Issue**
    ```
    Trigger: When a new issue is created
    Environment: production
    Level: error or fatal
    Actions:
      - Send email immediately
    ```

### Slack Integration (Optional)

1. Go to **Settings → Integrations → Slack**
2. Click "Add Workspace"
3. Authorize Sentry to access Slack
4. Choose channel: `#backend-alerts`
5. Configure alert routing

---

## 🎯 Custom Error Tracking

### 1. Capture Exception dengan Context

```php
use Sentry\Laravel\Facades\Sentry;

try {
    $schedule = Schedule::create($data);
} catch (\Exception $e) {
    Sentry::captureException($e, [
        'tags' => [
            'component' => 'schedule-create',
            'severity' => 'high',
        ],
        'extra' => [
            'user_id' => auth()->id(),
            'mitra_id' => $data['mitra_id'] ?? null,
            'request_data' => $data,
        ],
        'user' => [
            'id' => auth()->id(),
            'role' => auth()->user()->role ?? 'guest',
        ],
    ]);

    throw $e; // Re-throw untuk response ke client
}
```

### 2. Capture Custom Message (Non-Exception)

```php
Sentry::captureMessage('Schedule bulk import completed with errors', [
    'level' => 'warning',
    'extra' => [
        'total_schedules' => 100,
        'successful' => 95,
        'failed' => 5,
        'failed_ids' => [12, 34, 56, 78, 90],
    ],
]);
```

### 3. Add Breadcrumbs untuk Debugging

```php
Sentry::addBreadcrumb(new \Sentry\Breadcrumb(
    \Sentry\Breadcrumb::LEVEL_INFO,
    \Sentry\Breadcrumb::TYPE_USER,
    'schedule',
    'User requested schedule completion',
    [
        'schedule_id' => $scheduleId,
        'status_before' => $schedule->status,
        'status_after' => 'completed',
    ]
));
```

### 4. Set User Context (Tanpa PII)

```php
Sentry::configureScope(function (\Sentry\State\Scope $scope) {
    $scope->setUser([
        'id' => auth()->id(),
        'role' => auth()->user()->role,
        // JANGAN include email, phone, nama lengkap (karena PII)
    ]);
});
```

### 5. Set Transaction Name untuk Performance

```php
use function Sentry\startTransaction;

$transaction = startTransaction('schedule.complete');

try {
    // Business logic
    $schedule->complete($data);
} finally {
    $transaction->finish();
}
```

---

## 🚀 Performance Monitoring

### Enable Performance Tracking

Sudah enabled di `.env`:

```env
SENTRY_TRACES_SAMPLE_RATE=0.25  # Sample 25% requests
```

### Automatic Performance Tracking

Sentry otomatis track:

-   ✅ HTTP request duration
-   ✅ Database queries (SQL)
-   ✅ External HTTP calls (Guzzle, HTTP Client)
-   ✅ Queue jobs
-   ✅ Cache operations
-   ✅ View rendering

### Custom Performance Spans

```php
use function Sentry\startSpan;

$transaction = startTransaction('api.schedule.create');

// Span untuk validasi
$validationSpan = startSpan('validation');
$validator = Validator::make($request->all(), $rules);
$validationSpan->finish();

// Span untuk database operation
$dbSpan = startSpan('database.insert');
$schedule = Schedule::create($data);
$dbSpan->finish();

// Span untuk external API call
$notificationSpan = startSpan('notification.send');
Http::post('https://fcm.googleapis.com/fcm/send', $payload);
$notificationSpan->finish();

$transaction->finish();
```

### Performance Dashboard

Check di: **Sentry → Performance → Transactions**

Lihat:

-   P50, P75, P95, P99 response time
-   Slowest endpoints
-   Database query performance
-   N+1 query detection

---

## 🔍 Advanced Features

### 1. Release Tracking

Set release di deployment:

```bash
# Option 1: Static version
export SENTRY_RELEASE=gerobaks-backend@1.0.0

# Option 2: Dynamic git commit
export SENTRY_RELEASE="gerobaks-backend@$(git rev-parse --short HEAD)"

# Deploy
php artisan config:cache
```

### 2. Source Maps (untuk stacktrace)

Upload source code untuk better stacktrace:

```bash
# Install Sentry CLI
curl -sL https://sentry.io/get-cli/ | bash

# Login
sentry-cli login

# Upload source maps
sentry-cli releases files gerobaks-backend@1.0.0 upload-sourcemaps ./app
```

### 3. Environment-Specific Configuration

```php
// config/sentry.php
'before_send' => function (\Sentry\Event $event): ?\Sentry\Event {
    // Jangan kirim error TokenMismatchException
    if (str_contains($event->getMessage() ?? '', 'TokenMismatchException')) {
        return null;
    }

    // Jangan kirim error 404
    if ($event->getLevel() === 'info') {
        return null;
    }

    // Filter environment local
    if (app()->environment('local')) {
        return null;
    }

    return $event;
},
```

### 4. Custom Tags untuk Filtering

```php
Sentry::configureScope(function (\Sentry\State\Scope $scope) {
    $scope->setTags([
        'server' => gethostname(),
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
    ]);
});
```

---

## 🛠️ Troubleshooting

### Issue 1: Error tidak muncul di Sentry

**Check:**

1. ✅ DSN sudah benar di `.env`?
2. ✅ `APP_ENV=production` (bukan local)?
3. ✅ `APP_DEBUG=false`?
4. ✅ Cache sudah di-clear? `php artisan config:clear`
5. ✅ Exception di-throw atau di-suppress?

**Test:**

```bash
php artisan sentry:test
```

### Issue 2: Rate Limit Exceeded

**Cause:** Terlalu banyak error di-track

**Solution:**

```env
# Kurangi sample rate
SENTRY_SAMPLE_RATE=0.5  # Track 50% errors
SENTRY_TRACES_SAMPLE_RATE=0.1  # Track 10% performance
```

### Issue 3: Performance overhead

**Cause:** Traces sample rate terlalu tinggi

**Solution:**

```env
# Production: 10-25%
SENTRY_TRACES_SAMPLE_RATE=0.1

# Disable SQL bindings
SENTRY_BREADCRUMBS_SQL_BINDINGS_ENABLED=false
```

### Issue 4: Error flood dari bot/crawler

**Solution: Ignore exceptions**

```php
// config/sentry.php
'ignore_exceptions' => [
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
    Illuminate\Session\TokenMismatchException::class,
],
```

### Issue 5: Tidak ada User Context

**Check:** PII setting

```env
# Set true untuk development, false untuk production
SENTRY_SEND_DEFAULT_PII=false
```

Kalau butuh user context tanpa PII:

```php
Sentry::configureScope(function ($scope) {
    $scope->setUser(['id' => auth()->id()]);
});
```

---

## 📈 Best Practices

### ✅ DO

-   ✅ Set `SENTRY_ENVIRONMENT` berbeda per environment
-   ✅ Use release tracking untuk monitor deploy
-   ✅ Configure alerts untuk critical errors
-   ✅ Review Sentry dashboard setiap hari
-   ✅ Mark resolved issues as "Resolved in Next Release"
-   ✅ Use breadcrumbs untuk debugging context
-   ✅ Set reasonable sample rates (jangan 100% di production)

### ❌ DON'T

-   ❌ Jangan kirim PII (email, phone, password, token)
-   ❌ Jangan set `SENTRY_SEND_DEFAULT_PII=true` di production
-   ❌ Jangan ignore semua exceptions
-   ❌ Jangan set `SENTRY_TRACES_SAMPLE_RATE=1.0` di production
-   ❌ Jangan lupa update release version di setiap deploy
-   ❌ Jangan log sensitive data di breadcrumbs

---

## 📞 Support & Resources

-   📖 **Sentry Docs:** https://docs.sentry.io/platforms/php/guides/laravel/
-   💬 **Sentry Discord:** https://discord.gg/sentry
-   🐛 **Report Issues:** https://github.com/getsentry/sentry-laravel/issues
-   📧 **Team Contact:** dev@gerobaks.com

---

## ✅ Deployment Checklist

Sebelum production:

-   [ ] DSN sudah di-set di `.env` production
-   [ ] `SENTRY_ENVIRONMENT=production`
-   [ ] `SENTRY_RELEASE` sudah di-set
-   [ ] `SENTRY_SEND_DEFAULT_PII=false`
-   [ ] Sample rates sudah reasonable (10-25%)
-   [ ] Test `php artisan sentry:test` berhasil
-   [ ] Alert rules sudah dikonfigurasi
-   [ ] Team sudah bisa akses Sentry dashboard
-   [ ] Integration Slack/email sudah di-setup
-   [ ] Error filtering rules sudah di-setup

Setelah production deploy:

-   [ ] Monitor Sentry dashboard 24 jam pertama
-   [ ] Check alert notification jalan
-   [ ] Validate performance metrics masuk akal
-   [ ] Review error patterns
-   [ ] Adjust sample rates kalau perlu

---

<div align="center">

**🚨 Production Monitoring Ready! 🚨**

Track, debug, and fix errors faster dengan Sentry 🎯

</div>
