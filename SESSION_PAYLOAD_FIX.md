# 🔧 Session Payload Error Fix

## Problem

**Error:**

```
SQLSTATE[22001]: String data, right truncated: 1406 Data too long for column 'payload' at row 1
```

**Terjadi saat:**

-   User mengakses halaman dokumentasi `/openapi.yaml`
-   Laravel mencoba menyimpan session data
-   Payload session melebihi kapasitas kolom database

## Root Cause

Tabel `sessions` di production database menggunakan kolom `payload` dengan tipe `TEXT`:

-   **TEXT:** Maksimal ~65,535 bytes (~64KB)
-   **Session data:** Bisa melebihi 64KB ketika menyimpan banyak data

## Solution

### 1️⃣ Apply Migration (RECOMMENDED)

**Via SSH:**

```bash
ssh username@gerobaks.dumeg.com
cd public_html/backend
php artisan migrate --force
```

Migration akan:

-   ✅ Mengubah kolom `payload` dari `TEXT` → `LONGTEXT`
-   ✅ LONGTEXT support hingga ~4GB data
-   ✅ Tidak ada data yang hilang (safe operation)

### 2️⃣ Manual Fix (jika migration gagal)

**Via cPanel → phpMyAdmin:**

```sql
-- Alter sessions table
ALTER TABLE `sessions`
MODIFY COLUMN `payload` LONGTEXT NOT NULL;
```

**Atau via MySQL command line:**

```bash
mysql -u gerobaks_user -p gerobaks_db
```

```sql
USE gerobaks_db;
ALTER TABLE sessions MODIFY COLUMN payload LONGTEXT NOT NULL;
EXIT;
```

## Verification

**Check kolom type:**

```sql
DESCRIBE sessions;
```

**Expected result:**

```
Field         | Type      | Null | Key | Default | Extra
--------------+-----------+------+-----+---------+-------
id            | varchar   | NO   | PRI | NULL    |
user_id       | bigint    | YES  | MUL | NULL    |
ip_address    | varchar   | YES  |     | NULL    |
user_agent    | text      | YES  |     | NULL    |
payload       | longtext  | NO   |     | NULL    |  ← Should be LONGTEXT
last_activity | int       | NO   | MUL | NULL    |
```

**Test fix:**

```bash
# Clear existing sessions
php artisan session:flush

# Test access
curl -I https://gerobaks.dumeg.com/openapi.yaml
# Expected: 200 OK (no 500 error)
```

## Why This Happened

Laravel's default session migration uses `TEXT` type, which is fine for small sessions but fails when:

-   Session stores large data (like OpenAPI spec in memory)
-   Multiple middleware add data to session
-   Session contains encrypted/serialized complex objects

## Prevention

✅ **Done:** Migration created to fix production
✅ **Future:** Schema already updated for new deployments
✅ **Monitoring:** Error will not occur after applying fix

## Migration Details

**File:** `database/migrations/2025_01_14_000001_fix_sessions_payload_column.php`

**What it does:**

```php
Schema::table('sessions', function (Blueprint $table) {
    $table->longText('payload')->change();
});
```

**Safe to run:**

-   ✅ Non-destructive (data preserved)
-   ✅ Quick operation (~1 second)
-   ✅ No downtime required

## Quick Deploy Steps

### Option A: Via SSH (Recommended)

```bash
# 1. SSH to server
ssh username@gerobaks.dumeg.com

# 2. Navigate to backend
cd public_html/backend

# 3. Pull latest code (includes migration)
git pull origin fk0u/staging

# 4. Run migration
php artisan migrate --force

# 5. Verify
php artisan migrate:status

# 6. Clear cache
php artisan config:clear
php artisan route:clear

# 7. Test
curl https://gerobaks.dumeg.com/openapi.yaml
```

### Option B: Via cPanel File Manager + phpMyAdmin

```
1. Upload migration file:
   - Go to File Manager
   - Navigate to: public_html/backend/database/migrations/
   - Upload: 2025_01_14_000001_fix_sessions_payload_column.php

2. Apply via phpMyAdmin:
   - Go to phpMyAdmin
   - Select database: gerobaks_db
   - Run SQL:
     ALTER TABLE sessions MODIFY COLUMN payload LONGTEXT NOT NULL;

3. Record migration:
   - In phpMyAdmin, select 'migrations' table
   - Insert new row:
     - migration: 2025_01_14_000001_fix_sessions_payload_column
     - batch: (highest batch number + 1)
```

## Alternative Solutions (Not Recommended)

### Use Cache Driver Instead of Database

```env
# In .env
SESSION_DRIVER=file  # or redis if available
```

**Pros:** Avoids database size issues
**Cons:** File-based sessions harder to share across servers

### Increase Session Lifetime (Reduce Data)

```env
# In .env
SESSION_LIFETIME=120  # minutes (default)
```

**Pros:** Sessions expire faster
**Cons:** Doesn't solve the root cause

## Post-Fix Verification

✅ **Check migration status:**

```bash
php artisan migrate:status
```

✅ **Check database schema:**

```sql
SHOW COLUMNS FROM sessions LIKE 'payload';
```

✅ **Test affected endpoint:**

```bash
curl -I https://gerobaks.dumeg.com/openapi.yaml
curl -I https://gerobaks.dumeg.com/docs
```

✅ **Monitor logs:**

```bash
tail -f storage/logs/laravel.log
```

## Rollback (If Needed)

```bash
# Rollback last migration
php artisan migrate:rollback --step=1

# Or manually via SQL
ALTER TABLE sessions MODIFY COLUMN payload TEXT NOT NULL;
```

**⚠️ Warning:** Rolling back will cause the error to return!

## Summary

| Item             | Before               | After                |
| ---------------- | -------------------- | -------------------- |
| Column Type      | TEXT (~64KB)         | LONGTEXT (~4GB)      |
| Max Session Size | ~65,535 bytes        | ~4,294,967,295 bytes |
| Error Status     | ❌ Occurs frequently | ✅ Fixed             |
| Performance      | Normal               | Normal (no impact)   |

## Related Files

-   **Migration:** `database/migrations/2025_01_14_000001_fix_sessions_payload_column.php`
-   **Config:** `config/session.php`
-   **Environment:** `.env` → `SESSION_DRIVER=database`

## Status

-   [x] Migration created
-   [ ] Migration applied to production
-   [ ] Verified working
-   [ ] Documented

**Next Action:** Apply migration to production server!

---

**Last Updated:** January 14, 2025  
**Issue:** SQLSTATE[22001] Session payload truncated  
**Status:** ✅ Fix ready, pending deployment
