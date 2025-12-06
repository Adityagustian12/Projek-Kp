@echo off
REM Script untuk membuat file ZIP dari proyek Kos-Kosan
REM Exclude: node_modules, vendor, .git, storage/logs, dll

echo ========================================
echo Membuat ZIP Backup Proyek Kos-Kosan
echo ========================================
echo.

REM Set nama file ZIP dengan tanggal
for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /value') do set datetime=%%I
set datestamp=%datetime:~0,8%
set timestamp=%datetime:~8,6%
set zipname=kosku-v2-backup-%datestamp%-%timestamp%.zip

echo Nama file ZIP: %zipname%
echo.

REM Cek apakah 7-Zip tersedia
where 7z >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    echo Menggunakan 7-Zip...
    7z a -tzip "%zipname%" ^
        app\ ^
        artisan ^
        bootstrap\ ^
        composer.json ^
        composer.lock ^
        config\ ^
        database\ ^
        deploy.bat ^
        deploy.sh ^
        DEPLOYMENT_QUICK_START.md ^
        export_to_mysql.sql ^
        HOSTING_GUIDE.md ^
        package.json ^
        package-lock.json ^
        phpunit.xml ^
        Procfile ^
        public\ ^
        railway.json ^
        README.md ^
        resources\ ^
        routes\ ^
        storage\app\public\ ^
        storage\framework\ ^
        tests\ ^
        vercel.json ^
        vite.config.js ^
        -xr!node_modules ^
        -xr!vendor ^
        -xr!.git ^
        -xr!storage\logs\* ^
        -xr!storage\framework\cache\* ^
        -xr!storage\framework\sessions\* ^
        -xr!storage\framework\testing\* ^
        -xr!storage\framework\views\* ^
        -xr!.env ^
        -xr!.env.backup ^
        -xr!.phpunit.result.cache ^
        -xr!Homestead.json ^
        -xr!Homestead.yaml ^
        -xr!npm-debug.log ^
        -xr!yarn-error.log ^
        -xr!.fleet ^
        -xr!.idea ^
        -xr!.vscode ^
        -xr!database\database.sqlite
    
    if %ERRORLEVEL% EQU 0 (
        echo.
        echo ========================================
        echo ZIP berhasil dibuat: %zipname%
        echo ========================================
    ) else (
        echo.
        echo ERROR: Gagal membuat ZIP file!
    )
) else (
    REM Jika 7-Zip tidak tersedia, coba PowerShell
    echo 7-Zip tidak ditemukan, menggunakan PowerShell...
    powershell -NoProfile -ExecutionPolicy Bypass -Command "& {.\create-zip.ps1}"
)

pause

