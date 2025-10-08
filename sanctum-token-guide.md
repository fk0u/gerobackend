# Script untuk Generate Sanctum Token

## Via API Login (Rekomendasi)

```bash
# PowerShell
$response = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/login" -Method POST -ContentType "application/json" -Body '{"email":"daffa@gmail.com","password":"password123"}'
Write-Host "Token: $($response.data.token)"
```

## Via Laravel Tinker

```bash
php artisan tinker

# Di dalam tinker:
$user = App\Models\User::where('email', 'daffa@gmail.com')->first();
$token = $user->createToken('api-token')->plainTextToken;
echo "Token: " . $token;
exit
```

## Via Artisan Command (Custom)

Buat command custom untuk generate token dengan mudah:

```bash
php artisan make:command GenerateToken
```

## Current Valid Token untuk daffa@gmail.com:

```
15|fcBLIKOQeLSuhu4Jy5i6DNOmhAd86g85avwdtv58ed8e0c4f
```

## Cara Menggunakan Token:

```bash
# Contoh request dengan token
curl -H "Authorization: Bearer 15|fcBLIKOQeLSuhu4Jy5i6DNOmhAd86g85avwdtv58ed8e0c4f" \
     -H "Accept: application/json" \
     http://127.0.0.1:8000/api/auth/me
```

## Test Token di Swagger UI:

1. Buka http://127.0.0.1:8000/api/documentation
2. Klik tombol "Authorize"
3. Masukkan: `Bearer 15|fcBLIKOQeLSuhu4Jy5i6DNOmhAd86g85avwdtv58ed8e0c4f`
4. Klik "Authorize"
5. Sekarang bisa test endpoint yang memerlukan authentication
