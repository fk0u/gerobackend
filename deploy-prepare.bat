@echo off
REM ############################################################################
REM Gerobaks Backend - Windows Deployment Preparation
REM ############################################################################

echo.
echo ========================================
echo    Gerobaks Backend Deployment Prep
echo ========================================
echo.

REM Check if we're in backend directory
if not exist "artisan" (
    echo [ERROR] Must be run from backend directory
    pause
    exit /b 1
)

REM Step 1: Check PHP
echo [Step 1/8] Checking PHP version...
php -v
if errorlevel 1 (
    echo [ERROR] PHP not found in PATH
    pause
    exit /b 1
)
echo [OK] PHP found
echo.

REM Step 2: Check Composer
echo [Step 2/8] Checking Composer...
composer --version
if errorlevel 1 (
    echo [ERROR] Composer not found in PATH
    pause
    exit /b 1
)
echo [OK] Composer found
echo.

REM Step 3: Install Dependencies
echo [Step 3/8] Installing production dependencies...
call composer install --no-dev --optimize-autoloader --no-interaction
if errorlevel 1 (
    echo [ERROR] Failed to install dependencies
    pause
    exit /b 1
)
echo [OK] Dependencies installed
echo.

REM Step 4: Check .env
echo [Step 4/8] Checking .env file...
if not exist ".env" (
    echo [WARNING] .env not found, copying from .env.example
    copy .env.example .env
    echo [WARNING] Please edit .env with production values!
    pause
) else (
    echo [OK] .env file exists
)
echo.

REM Step 5: Generate APP_KEY if needed
echo [Step 5/8] Checking APP_KEY...
findstr /C:"APP_KEY=base64:" .env >nul
if errorlevel 1 (
    echo Generating APP_KEY...
    php artisan key:generate --force
    echo [OK] APP_KEY generated
) else (
    echo [OK] APP_KEY already set
)
echo.

REM Step 6: Clear caches
echo [Step 6/8] Clearing caches...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo [OK] Caches cleared
echo.

REM Step 7: Run tests
echo [Step 7/8] Running API tests...
php test_api_comprehensive.php >nul 2>&1
if errorlevel 1 (
    echo [WARNING] Some tests failed
) else (
    echo [OK] Tests passed
)
echo.

REM Step 8: Create deployment package
echo [Step 8/8] Creating deployment package...

REM Get timestamp for filename
for /f "tokens=2-4 delims=/ " %%a in ('date /t') do (set mydate=%%c%%a%%b)
for /f "tokens=1-2 delims=/:" %%a in ('time /t') do (set mytime=%%a%%b)
set TIMESTAMP=%mydate%_%mytime%
set ZIP_FILE=gerobaks-backend-%TIMESTAMP%.zip

echo Creating ZIP file: %ZIP_FILE%

REM Create zip using PowerShell (Windows 10+)
powershell -Command "Compress-Archive -Path .\* -DestinationPath ..\%ZIP_FILE% -Force -CompressionLevel Optimal"

if exist "..\%ZIP_FILE%" (
    echo [OK] Package created: %ZIP_FILE%
    echo.
    echo ========================================
    echo    Preparation Complete!
    echo ========================================
    echo.
    echo Package Location: ..\%ZIP_FILE%
    echo.
    echo Next Steps:
    echo 1. Upload %ZIP_FILE% to cPanel File Manager
    echo 2. Extract in public_html/
    echo 3. SSH to server and run deploy-server.sh
    echo.
    echo For detailed instructions, see DEPLOYMENT.md
    echo.
) else (
    echo [ERROR] Failed to create package
    pause
    exit /b 1
)

REM Create checklist
echo DEPLOYMENT CHECKLIST > ..\deployment-checklist-%TIMESTAMP%.txt
echo ==================== >> ..\deployment-checklist-%TIMESTAMP%.txt
echo. >> ..\deployment-checklist-%TIMESTAMP%.txt
echo BEFORE UPLOADING: >> ..\deployment-checklist-%TIMESTAMP%.txt
echo [ ] Review .env file >> ..\deployment-checklist-%TIMESTAMP%.txt
echo [ ] Verify database credentials >> ..\deployment-checklist-%TIMESTAMP%.txt
echo [ ] Check APP_URL is production URL >> ..\deployment-checklist-%TIMESTAMP%.txt
echo [ ] Ensure APP_DEBUG=false >> ..\deployment-checklist-%TIMESTAMP%.txt
echo. >> ..\deployment-checklist-%TIMESTAMP%.txt
echo Package: %ZIP_FILE% >> ..\deployment-checklist-%TIMESTAMP%.txt
echo Created: %date% %time% >> ..\deployment-checklist-%TIMESTAMP%.txt

echo Checklist created: ..\deployment-checklist-%TIMESTAMP%.txt
echo.

pause
