# 🗺️ Deployment Documentation Navigator

**Bingung mulai dari mana? Gunakan navigator ini!**

---

## 🎯 Pilih Berdasarkan Kebutuhan Anda

### 🚀 "Saya mau deploy cepat, langsung jalan!"

**→ Buka:** [QUICKSTART-CPANEL.md](./QUICKSTART-CPANEL.md)  
**Waktu:** ~20 menit  
**Kesulitan:** ⭐⭐ Intermediate

---

### 📚 "Saya butuh dokumentasi lengkap dan detail"

**→ Buka:** [DEPLOYMENT.md](./DEPLOYMENT.md)  
**Waktu:** Read as needed  
**Kesulitan:** ⭐⭐⭐ Advanced

---

### 🔑 "Saya lupa password/credentials test account"

**→ Buka:** [API_CREDENTIALS.md](./API_CREDENTIALS.md)  
**Info:** Username, password, dan API endpoints

---

### ✅ "Saya mau verify API sudah working atau belum"

**→ Buka:** [BACKEND_API_VERIFICATION.md](./BACKEND_API_VERIFICATION.md)  
**Info:** Test results dan verification steps

---

### 🧪 "Login masih error, gimana test?"

**→ Buka:** [LOGIN_FIX_TESTING_GUIDE.md](./LOGIN_FIX_TESTING_GUIDE.md)  
**Info:** Testing procedures dan troubleshooting

---

### 📦 "Saya mau tau semua file deployment apa aja"

**→ Buka:** [DEPLOYMENT_FILES_SUMMARY.md](./DEPLOYMENT_FILES_SUMMARY.md)  
**Info:** Complete list dengan penjelasan

---

### 🖥️ "Saya mau run script deployment"

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

**Server (via SSH):**

```bash
cd public_html/backend
chmod +x deploy-server.sh
./deploy-server.sh
```

---

## 🗂️ File Structure Overview

```
backend/
│
├── 📖 DOCUMENTATION (Baca ini)
│   ├── START_HERE.md ← YOU ARE HERE! 👈
│   ├── QUICKSTART-CPANEL.md ← Quick deployment guide
│   ├── DEPLOYMENT.md ← Full documentation
│   ├── DEPLOYMENT_FILES_SUMMARY.md ← File list
│   ├── API_CREDENTIALS.md ← Test accounts
│   ├── BACKEND_API_VERIFICATION.md ← Test results
│   └── LOGIN_FIX_TESTING_GUIDE.md ← Testing guide
│
├── 🤖 AUTOMATION SCRIPTS (Jalankan ini)
│   ├── deploy-prepare.sh ← Linux/Mac preparation
│   ├── deploy-prepare.bat ← Windows preparation
│   └── deploy-server.sh ← Server deployment (SSH)
│
├── 🧪 TEST SCRIPTS (Test dengan ini)
│   ├── test_api_comprehensive.php ← Test all endpoints
│   ├── test_cors.php ← Test CORS headers
│   └── test_login.php ← Quick login test
│
├── ⚙️ CONFIG FILES (Copy & edit ini)
│   ├── .env.example ← Environment template
│   ├── .env.production ← Production template
│   ├── .htaccess ← Apache security
│   └── deployment-checklist.template ← Checklist template
│
└── 📝 APPLICATION CODE
    ├── app/
    ├── config/
    ├── database/
    ├── routes/
    └── ...
```

---

## 🎓 Learning Path by Experience Level

### Level 1: Pemula (First-time cPanel deployment)

1. ✅ Baca [QUICKSTART-CPANEL.md](./QUICKSTART-CPANEL.md)
2. ✅ Run `deploy-prepare.bat` (Windows) atau `deploy-prepare.sh` (Linux)
3. ✅ Follow generated `deployment-checklist-*.txt`
4. ✅ Test dengan [BACKEND_API_VERIFICATION.md](./BACKEND_API_VERIFICATION.md)

**Estimated Time:** 30-45 minutes

---

### Level 2: Intermediate (Sudah pernah deploy, butuh reference)

1. ✅ Quick reference: [DEPLOYMENT_FILES_SUMMARY.md](./DEPLOYMENT_FILES_SUMMARY.md)
2. ✅ Troubleshooting: [DEPLOYMENT.md](./DEPLOYMENT.md) → "Troubleshooting"
3. ✅ Verify CORS: Run `test_cors.php`
4. ✅ Check logs: `tail -f storage/logs/laravel.log`

**Estimated Time:** 10-20 minutes

---

### Level 3: Advanced (Setup automation/CI-CD)

1. ✅ Read full [DEPLOYMENT.md](./DEPLOYMENT.md)
2. ✅ Customize `deploy-*.sh` scripts
3. ✅ Setup Git webhooks
4. ✅ Implement monitoring

**Estimated Time:** 1-2 hours

---

## 🔥 Common Scenarios (Quick Jump)

### Scenario 1: Fresh Deployment (Pertama Kali)

**Steps:**

1. [QUICKSTART-CPANEL.md](./QUICKSTART-CPANEL.md) → Read full guide
2. Run `deploy-prepare` script
3. Upload ZIP to cPanel
4. Run `deploy-server.sh` on server
5. Test with [BACKEND_API_VERIFICATION.md](./BACKEND_API_VERIFICATION.md)

---

### Scenario 2: Re-deployment (Update Code)

**Quick Steps:**

```bash
# Via SSH
git pull origin fk0u/staging
composer install --no-dev
php artisan migrate --force
php artisan config:cache
```

**Reference:** [DEPLOYMENT.md](./DEPLOYMENT.md) → "Updating Existing Deployment"

---

### Scenario 3: Login Error 422

**Troubleshoot:**

1. Check [API_CREDENTIALS.md](./API_CREDENTIALS.md) untuk correct credentials
2. Run `test_login.php`
3. Read [LOGIN_FIX_TESTING_GUIDE.md](./LOGIN_FIX_TESTING_GUIDE.md)
4. Check `.env` DB credentials

---

### Scenario 4: CORS Error

**Fix:**

1. Run `test_cors.php`
2. Check `app/Http/Middleware/Cors.php`
3. Clear cache: `php artisan config:clear`
4. Re-test from Flutter app

---

### Scenario 5: 500 Internal Server Error

**Troubleshoot:**

```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear caches
php artisan optimize:clear

# Verify .env exists
ls -la .env

# Check permissions
chmod -R 755 storage bootstrap/cache
```

**Reference:** [DEPLOYMENT.md](./DEPLOYMENT.md) → "Troubleshooting"

---

## 🛠️ Quick Commands Reference

### Deployment Preparation

```bash
# Windows
deploy-prepare.bat

# Linux/Mac
chmod +x deploy-prepare.sh && ./deploy-prepare.sh
```

### Server Setup (SSH)

```bash
chmod +x deploy-server.sh && ./deploy-server.sh
```

### Testing

```bash
php test_api_comprehensive.php
php test_cors.php
php test_login.php
```

### Maintenance

```bash
# Clear all caches
php artisan optimize:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check logs
tail -f storage/logs/laravel.log
```

---

## 📞 Quick Reference

### Important URLs

-   **Production API:** https://gerobaks.dumeg.com
-   **API Documentation:** https://gerobaks.dumeg.com/docs
-   **cPanel:** https://gerobaks.dumeg.com:2083

### Test Accounts

-   **Admin:** `daffa@gmail.com` / `password123`
-   **Driver Jakarta:** `driver.jakarta@gerobaks.com` / `mitra123`
-   **Driver Bandung:** `driver.bandung@gerobaks.com` / `mitra123`

_Full list:_ [API_CREDENTIALS.md](./API_CREDENTIALS.md)

---

## 🆘 Still Confused?

### Decision Tree:

**Question 1: Apakah ini first-time deployment?**

-   ✅ Yes → Go to [QUICKSTART-CPANEL.md](./QUICKSTART-CPANEL.md)
-   ❌ No → Question 2

**Question 2: Apakah ada specific issue/error?**

-   ✅ Yes (Login error) → Go to [LOGIN_FIX_TESTING_GUIDE.md](./LOGIN_FIX_TESTING_GUIDE.md)
-   ✅ Yes (CORS error) → Run `test_cors.php`
-   ✅ Yes (500 error) → Check [DEPLOYMENT.md](./DEPLOYMENT.md) Troubleshooting
-   ❌ No → Question 3

**Question 3: Apa yang ingin dilakukan?**

-   📚 Baca dokumentasi lengkap → [DEPLOYMENT.md](./DEPLOYMENT.md)
-   📦 Lihat list semua file → [DEPLOYMENT_FILES_SUMMARY.md](./DEPLOYMENT_FILES_SUMMARY.md)
-   🔑 Cari credentials → [API_CREDENTIALS.md](./API_CREDENTIALS.md)
-   ✅ Verify API → [BACKEND_API_VERIFICATION.md](./BACKEND_API_VERIFICATION.md)

---

## 📊 Documentation Stats

| Document                    | Pages | Words | Read Time |
| --------------------------- | ----- | ----- | --------- |
| START_HERE.md               | 1     | ~800  | 3 min     |
| QUICKSTART-CPANEL.md        | 3     | ~2000 | 8 min     |
| DEPLOYMENT.md               | 15    | ~8000 | 30 min    |
| DEPLOYMENT_FILES_SUMMARY.md | 5     | ~2500 | 10 min    |
| API_CREDENTIALS.md          | 1     | ~500  | 2 min     |

**Total Documentation:** ~25 pages, ~14,000 words

---

## ✅ Deployment Success Checklist

Quick check sebelum declare "deployment complete":

-   [ ] API endpoint `/api/health` returns `{"status":"ok"}`
-   [ ] Login endpoint returns token (not 422)
-   [ ] CORS headers present in OPTIONS request
-   [ ] Flutter app bisa connect ke production API
-   [ ] No 500 errors in `storage/logs/laravel.log`
-   [ ] Database migrations ran successfully
-   [ ] File permissions correct (755 for storage/)
-   [ ] `.env` configured with production values
-   [ ] SSL certificate active (HTTPS working)

**All checked?** 🎉 Deployment successful!

---

## 🚀 Next Steps After Deployment

1. **Monitor logs regularly**

    ```bash
    tail -f storage/logs/laravel.log
    ```

2. **Setup automated backups**

    - Database: cPanel MySQL backup
    - Files: Git repository + cPanel backup

3. **Test from Flutter app**

    - Edit `lib/utils/app_config.dart`
    - Set `apiBaseUrl = 'https://gerobaks.dumeg.com'`
    - Run `flutter clean && flutter run`

4. **Share with team**
    - Send [QUICKSTART-CPANEL.md](./QUICKSTART-CPANEL.md)
    - Share [API_CREDENTIALS.md](./API_CREDENTIALS.md) (securely!)

---

**Last Updated:** 2025-01-XX  
**Status:** ✅ Ready for Production  
**Questions?** Read the docs or check troubleshooting sections!

---

**Happy Deploying! 🚀**
