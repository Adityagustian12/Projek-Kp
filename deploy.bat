@echo off
REM Script Deployment untuk Laravel Kos-Kosan H.Kastim (Windows)
REM Usage: deploy.bat

echo.
echo ========================================
echo   Deployment Script - Kos-Kosan H.Kastim
echo ========================================
echo.

REM Check if .env exists
if not exist .env (
    echo [ERROR] .env file not found!
    echo Please create .env file first
    pause
    exit /b 1
)

echo [OK] .env file found
echo.

REM Install/Update dependencies
echo [1/6] Installing dependencies...
call composer install --no-dev --optimize-autoloader
if errorlevel 1 (
    echo [ERROR] Composer install failed!
    pause
    exit /b 1
)
echo [OK] Dependencies installed
echo.

REM Generate app key if needed
findstr /C:"APP_KEY=base64:" .env >nul
if errorlevel 1 (
    echo [2/6] Generating application key...
    call php artisan key:generate --force
    echo [OK] Application key generated
) else (
    echo [2/6] Application key already exists
)
echo.

REM Clear caches
echo [3/6] Clearing caches...
call php artisan config:clear
call php artisan route:clear
call php artisan view:clear
call php artisan cache:clear
echo [OK] Caches cleared
echo.

REM Optimize
echo [4/6] Optimizing configuration...
call php artisan config:cache
call php artisan route:cache
call php artisan view:cache
echo [OK] Configuration optimized
echo.

REM Run migrations
echo [5/6] Running database migrations...
call php artisan migrate --force
if errorlevel 1 (
    echo [ERROR] Migration failed!
    pause
    exit /b 1
)
echo [OK] Migrations completed
echo.

REM Create storage link
echo [6/6] Creating storage link...
call php artisan storage:link
echo [OK] Storage link created
echo.

echo.
echo ========================================
echo   Deployment completed successfully!
echo ========================================
echo.
echo Next steps:
echo 1. Make sure your web server is configured
echo 2. Point document root to: %CD%\public
echo 3. Ensure PHP version ^>= 8.2
echo 4. Test your application
echo.
pause

