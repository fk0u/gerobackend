# 🚀 Quick Deployment Script for cPanel

This script helps you prepare the backend for deployment to cPanel.

## Prerequisites

-   PHP 8.1+
-   Composer installed
-   Git repository synced

## Usage

### 1. Run Preparation Script

```bash
cd backend
./deploy-prepare.sh
```

### 2. Upload to cPanel

-   Upload `gerobaks-backend-production.zip` to cPanel File Manager
-   Extract in `public_html/`

### 3. Run Post-Deployment Script via SSH

```bash
ssh username@gerobaks.dumeg.com
cd public_html/Gerobaks/backend
./deploy-server.sh
```

## Manual Steps

If scripts don't work, follow DEPLOYMENT.md for manual steps.

## Files

-   `deploy-prepare.sh` - Local preparation script
-   `deploy-server.sh` - Server-side deployment script
-   `DEPLOYMENT.md` - Full deployment documentation
