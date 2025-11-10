# 📝 GitHub Changelog Integration - Complete Guide

## 📋 Overview

Dokumentasi API Swagger sudah terintegrasi dengan GitHub untuk menampilkan changelog otomatis dari commit history repository [fk0u/gerobackend](https://github.com/fk0u/gerobackend).

**Features:**

-   ✅ Menampilkan 20 commit terbaru secara otomatis
-   ✅ Cache 1 jam untuk menghindari GitHub API rate limit
-   ✅ Support public & private repository
-   ✅ Tampilan commit dengan author, date, dan link ke GitHub
-   ✅ RESTful API endpoint untuk akses changelog
-   ✅ Manual cache refresh untuk update instant

---

## 🌐 Akses Changelog

### 1. Via Swagger UI

**URL:** [https://gerobaks.dumeg.com/api/documentation](https://gerobaks.dumeg.com/api/documentation)

Di halaman Swagger UI, akan ada **tab "Changelog"** yang menampilkan:

-   📅 Tanggal commit
-   🔗 SHA commit (7 karakter)
-   ✍️ Commit message
-   👤 Author name & avatar
-   📊 Stats (additions/deletions)
-   🔗 Link langsung ke GitHub commit

### 2. Via API Endpoint

**GET /api/changelog**

```bash
curl https://gerobaks.dumeg.com/api/changelog
```

**Response:**

```json
{
    "success": true,
    "data": {
        "repository": "fk0u/gerobackend",
        "branch": "main",
        "commits": [
            {
                "sha": "a1b2c3d",
                "full_sha": "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0",
                "message": "feat: add schedule lifecycle endpoints",
                "author": "John Doe",
                "email": "john@example.com",
                "date": "2024-01-15T10:30:00Z",
                "url": "https://github.com/fk0u/gerobackend/commit/a1b2c3d",
                "avatar_url": "https://avatars.githubusercontent.com/u/12345?v=4",
                "stats": {
                    "additions": 150,
                    "deletions": 30,
                    "total": 180
                }
            }
        ],
        "total": 20,
        "cached": true,
        "cache_expires_at": "2024-01-15T11:30:00Z"
    },
    "meta": {
        "github_url": "https://github.com/fk0u/gerobackend",
        "commits_url": "https://github.com/fk0u/gerobackend/commits/main"
    }
}
```

### 3. Query Parameters

**Limit commits:**

```bash
curl https://gerobaks.dumeg.com/api/changelog?limit=10
```

**Specific branch:**

```bash
curl https://gerobaks.dumeg.com/api/changelog?branch=develop
```

**Kombinasi:**

```bash
curl "https://gerobaks.dumeg.com/api/changelog?limit=50&branch=main"
```

---

## ⚙️ Configuration

### Environment Variables

File: `.env`

```env
# Enable/disable changelog feature
SWAGGER_CHANGELOG_ENABLED=true

# GitHub repository (format: owner/repo)
GITHUB_REPO=fk0u/gerobackend

# GitHub Personal Access Token (optional untuk public repo)
GITHUB_TOKEN=ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

# Cache duration (detik) - default 1 jam
CHANGELOG_CACHE_TTL=3600

# Jumlah commit default
CHANGELOG_LIMIT=20

# Default branch
CHANGELOG_DEFAULT_BRANCH=main
```

### Laravel Config

File: `config/l5-swagger.php`

```php
'changelog' => [
    'enabled' => env('SWAGGER_CHANGELOG_ENABLED', true),
    'github_repo' => env('GITHUB_REPO', 'fk0u/gerobackend'),
    'github_token' => env('GITHUB_TOKEN', null),
    'cache_ttl' => env('CHANGELOG_CACHE_TTL', 3600),
    'limit' => env('CHANGELOG_LIMIT', 20),
    'default_branch' => env('CHANGELOG_DEFAULT_BRANCH', 'main'),
],
```

---

## 🔑 GitHub Token Setup

### Kapan Butuh Token?

| Scenario                          | Butuh Token? | Alasan                         |
| --------------------------------- | ------------ | ------------------------------ |
| Public repository, traffic rendah | ❌ Tidak     | Rate limit 60/hour cukup       |
| Public repository, traffic tinggi | ✅ Ya        | Butuh rate limit 5000/hour     |
| Private repository                | ✅ Ya        | Wajib untuk akses private repo |

### Cara Generate Token

1. **Login ke GitHub**

    - Buka [https://github.com/settings/tokens](https://github.com/settings/tokens)

2. **Generate New Token (Classic)**

    - Click "Generate new token" → "Generate new token (classic)"

3. **Set Token Details**

    - **Note:** `Gerobaks Backend Changelog`
    - **Expiration:** 90 days atau No expiration
    - **Scopes:**
        - ✅ `repo` (untuk private repository)
        - ✅ `public_repo` (untuk public repository)

4. **Generate & Copy Token**

    - Click "Generate token"
    - **COPY TOKEN SEGERA** (hanya muncul sekali!)
    - Format: `ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`

5. **Add to .env**

    ```env
    GITHUB_TOKEN=ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    ```

6. **Clear Cache**
    ```bash
    php artisan config:clear
    ```

### Rate Limits

| Type              | Rate Limit     | Per  |
| ----------------- | -------------- | ---- |
| **Without Token** | 60 requests    | hour |
| **With Token**    | 5,000 requests | hour |

**Dengan cache TTL 1 jam:** 1 request/jam → ampuh untuk 60 users/jam tanpa token!

---

## 🧪 Testing

### Test 1: Check Configuration

```bash
php artisan tinker
```

```php
config('l5-swagger.changelog.github_repo');
// Output: "fk0u/gerobackend"

config('l5-swagger.changelog.enabled');
// Output: true
```

### Test 2: Manual API Call

```bash
curl https://gerobaks.dumeg.com/api/changelog | jq
```

**Expected response:**

-   ✅ `success: true`
-   ✅ `commits` array dengan 20 item
-   ✅ Setiap commit punya `sha`, `message`, `author`, `url`

### Test 3: Test dengan Postman

1. Import collection: `Gerobaks_API.postman_collection.json`
2. Request: `GET {{baseUrl}}/api/changelog`
3. Check response:
    - Status: `200 OK`
    - Body: JSON dengan commits array

### Test 4: Test Cache

```bash
# Call pertama (hit GitHub API)
time curl https://gerobaks.dumeg.com/api/changelog

# Call kedua (dari cache, harusnya lebih cepat)
time curl https://gerobaks.dumeg.com/api/changelog
```

**Expected:**

-   Call 1: ~500ms (hit GitHub API)
-   Call 2: ~50ms (dari cache)

### Test 5: Clear Cache

```bash
# Login dulu untuk dapat token
curl -X POST https://gerobaks.dumeg.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@gerobaks.com","password":"admin123"}'

# Copy token dari response
export TOKEN="your_token_here"

# Clear cache
curl -X POST https://gerobaks.dumeg.com/api/changelog/clear-cache \
  -H "Authorization: Bearer $TOKEN"
```

**Expected response:**

```json
{
    "success": true,
    "message": "Changelog cache cleared successfully",
    "timestamp": "2024-01-15T12:00:00+00:00"
}
```

---

## 🎨 Frontend Integration (Swagger UI)

### Custom Template

File: `resources/views/vendor/l5-swagger/index.blade.php`

```html
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>
            {{ config('l5-swagger.documentations.default.api.title') }}
        </title>
        <link
            rel="stylesheet"
            href="https://unpkg.com/swagger-ui-dist@latest/swagger-ui.css"
        />
        <style>
            .changelog-tab {
                padding: 20px;
            }
            .commit-item {
                border-left: 3px solid #4caf50;
                padding: 15px;
                margin-bottom: 15px;
                background: #f9f9f9;
            }
            .commit-header {
                display: flex;
                justify-content: space-between;
                margin-bottom: 8px;
            }
            .commit-sha {
                font-family: monospace;
                background: #e0e0e0;
                padding: 2px 6px;
                border-radius: 3px;
            }
            .commit-message {
                font-weight: bold;
                margin-bottom: 8px;
            }
            .commit-meta {
                display: flex;
                justify-content: space-between;
                font-size: 0.9em;
                color: #666;
            }
        </style>
    </head>
    <body>
        <div id="swagger-ui"></div>

        <!-- Changelog Tab -->
        <div id="changelog-section" class="changelog-tab">
            <h2>📝 Latest Changes</h2>
            <div id="changelog-container">
                <p>Loading changelog from GitHub...</p>
            </div>
        </div>

        <script src="https://unpkg.com/swagger-ui-dist@latest/swagger-ui-bundle.js"></script>
        <script>
            // Initialize Swagger UI
            window.ui = SwaggerUIBundle({
                url: "{{ route('l5-swagger.default.api') }}",
                dom_id: "#swagger-ui",
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIBundle.SwaggerUIStandalonePreset,
                ],
            });

            // Fetch and render changelog
            fetch("/api/changelog")
                .then((res) => res.json())
                .then((data) => {
                    const container = document.getElementById(
                        "changelog-container"
                    );

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
                        <div class="commit-message">${escapeHtml(
                            commit.message
                        )}</div>
                        <div class="commit-meta">
                            <span class="commit-author">👤 ${escapeHtml(
                                commit.author
                            )}</span>
                            <a href="${
                                commit.url
                            }" target="_blank" rel="noopener">View on GitHub →</a>
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
                        '<p style="color: red;">Failed to load changelog. Check console for details.</p>';
                });

            function escapeHtml(text) {
                const div = document.createElement("div");
                div.textContent = text;
                return div.innerHTML;
            }
        </script>
    </body>
</html>
```

### Publish Swagger Views (Optional)

Jika ingin customize lebih lanjut:

```bash
php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider" --tag="views"
```

Edit file di: `resources/views/vendor/l5-swagger/index.blade.php`

---

## 🛠️ API Endpoints Reference

### 1. Get Changelog

**Endpoint:** `GET /api/changelog`

**Parameters:**
| Param | Type | Default | Description |
|-------|------|---------|-------------|
| `limit` | integer | 20 | Jumlah commit (max: 100) |
| `branch` | string | main | Branch name |

**Response:** JSON dengan array commits

### 2. Get Repository Stats

**Endpoint:** `GET /api/changelog/stats`

**Response:**

```json
{
    "success": true,
    "data": {
        "name": "gerobackend",
        "full_name": "fk0u/gerobackend",
        "description": "Gerobaks Backend API",
        "stars": 5,
        "watchers": 2,
        "forks": 1,
        "open_issues": 3,
        "language": "PHP",
        "default_branch": "main",
        "created_at": "2024-01-01T00:00:00Z",
        "updated_at": "2024-01-15T12:00:00Z",
        "homepage": "https://gerobaks.dumeg.com"
    }
}
```

### 3. Clear Cache (Authenticated)

**Endpoint:** `POST /api/changelog/clear-cache`

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```json
{
    "success": true,
    "message": "Changelog cache cleared successfully",
    "timestamp": "2024-01-15T12:00:00+00:00"
}
```

---

## 🚨 Troubleshooting

### Issue 1: Changelog tidak muncul

**Check:**

```bash
# Test API endpoint
curl https://gerobaks.dumeg.com/api/changelog

# Check logs
tail -f storage/logs/laravel.log | grep -i changelog
```

**Common causes:**

-   ❌ `SWAGGER_CHANGELOG_ENABLED=false`
-   ❌ `GITHUB_REPO` salah format
-   ❌ Cache corrupted
-   ❌ GitHub API rate limit exceeded

**Fix:**

```bash
php artisan config:clear
php artisan cache:clear
```

### Issue 2: GitHub API Rate Limit

**Error message:**

```json
{
    "success": false,
    "error": "GitHub API returned status 403"
}
```

**Solution:**

1. Generate GitHub token (langkah di atas)
2. Add to `.env`: `GITHUB_TOKEN=ghp_xxx`
3. Clear cache: `php artisan config:clear`

### Issue 3: Cache tidak ter-update

**Manual clear cache:**

```bash
# Via artisan
php artisan cache:clear

# Via API (butuh auth token)
curl -X POST https://gerobaks.dumeg.com/api/changelog/clear-cache \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Issue 4: Private repository error

**Error:** `404 Not Found`

**Cause:** Token tidak punya akses ke private repo

**Fix:**

1. Check token scope includes `repo`
2. Pastikan token dari user yang punya akses ke repo
3. Re-generate token dengan scope `repo`

### Issue 5: Commit message terpotong

**Solution:** Fetch full commit message

Update `ChangelogController`:

```php
return [
    'sha' => substr($commit['sha'], 0, 7),
    'message' => $commit['commit']['message'], // Full message
    // ...
];
```

---

## 📊 Monitoring & Analytics

### GitHub API Usage

Check rate limit status:

```bash
curl -H "Authorization: token YOUR_GITHUB_TOKEN" \
  https://api.github.com/rate_limit
```

**Response:**

```json
{
    "resources": {
        "core": {
            "limit": 5000,
            "remaining": 4999,
            "reset": 1705329600
        }
    }
}
```

### Cache Hit Rate

Monitor Laravel logs:

```bash
tail -f storage/logs/laravel.log | grep 'changelog'
```

### Performance Metrics

Di Sentry, track transaction:

-   `api.changelog.index` - Response time
-   `github.api.fetch` - External API call time

---

## ✅ Best Practices

### ✅ DO

-   ✅ Use GitHub token untuk production
-   ✅ Set cache TTL minimal 1 jam (3600 detik)
-   ✅ Monitor GitHub API rate limit
-   ✅ Implement error handling di frontend
-   ✅ Show loading state saat fetch changelog
-   ✅ Add link ke full commit di GitHub

### ❌ DON'T

-   ❌ Jangan set cache TTL < 300 detik (5 menit)
-   ❌ Jangan expose GitHub token di frontend
-   ❌ Jangan fetch changelog di setiap page load
-   ❌ Jangan ignore rate limit errors
-   ❌ Jangan hardcode repository name di code

---

## 📞 Support & Resources

-   📖 **GitHub API Docs:** https://docs.github.com/en/rest/commits/commits
-   🔑 **Token Management:** https://github.com/settings/tokens
-   📧 **Team Contact:** dev@gerobaks.com
-   🐛 **Report Issues:** https://github.com/fk0u/gerobackend/issues

---

## ✅ Deployment Checklist

Before deployment:

-   [ ] `SWAGGER_CHANGELOG_ENABLED=true` di `.env`
-   [ ] `GITHUB_REPO=fk0u/gerobackend` sudah benar
-   [ ] GitHub token generated (jika perlu)
-   [ ] `CHANGELOG_CACHE_TTL` sudah di-set (min 3600)
-   [ ] Test endpoint `/api/changelog` berjalan
-   [ ] Swagger UI menampilkan tab changelog
-   [ ] Cache clear endpoint terproteksi auth
-   [ ] Error handling sudah terimplementasi

After deployment:

-   [ ] Verify changelog muncul di Swagger UI
-   [ ] Test manual cache clear
-   [ ] Monitor GitHub API rate limit
-   [ ] Check Laravel logs untuk errors
-   [ ] Validate commit data accuracy

---

<div align="center">

**📝 GitHub Changelog Integration Complete! 📝**

Changelog otomatis terupdate dari repository 🚀

</div>
