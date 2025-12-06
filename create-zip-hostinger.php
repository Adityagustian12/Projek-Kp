<?php

/**
 * Script untuk membuat file ZIP khusus Hostinger cPanel
 * Struktur Laravel yang benar untuk Hostinger
 */

echo "========================================\n";
echo "Membuat ZIP Backup untuk Hostinger\n";
echo "========================================\n\n";

// Set nama file ZIP dengan tanggal
$dateStamp = date('Ymd-His');
$zipName = "kosku-v2-hostinger-{$dateStamp}.zip";

echo "Nama file ZIP: {$zipName}\n\n";

// Hapus file ZIP lama jika ada
if (file_exists($zipName)) {
    unlink($zipName);
    echo "File ZIP lama dihapus.\n";
}

// Cek apakah extension ZipArchive tersedia
if (!extension_loaded('zip')) {
    die("ERROR: Extension ZIP tidak tersedia. Install php-zip terlebih dahulu.\n");
}

$zip = new ZipArchive();
if ($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die("ERROR: Tidak dapat membuat file ZIP.\n");
}

// Daftar folder/file yang akan di-include (struktur Laravel lengkap)
$includeItems = [
    'app',
    'artisan',
    'bootstrap',
    'composer.json',
    'composer.lock',
    'config',
    'database',
    'package.json',
    'package-lock.json',
    'phpunit.xml',
    'public',
    'resources',
    'routes',
    'storage/app/public',
    'storage/framework',
    'tests',
    'vite.config.js',
];

// Daftar folder/file yang akan di-exclude
$excludePatterns = [
    'node_modules',
    'vendor',
    '.git',
    'storage/logs',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/testing',
    'storage/framework/views',
    '.env',
    '.env.backup',
    '.phpunit.result.cache',
    'Homestead.json',
    'Homestead.yaml',
    'npm-debug.log',
    'yarn-error.log',
    '.fleet',
    '.idea',
    '.vscode',
    'database/database.sqlite',
];

/**
 * Fungsi untuk mengecek apakah path harus di-exclude
 */
function shouldExclude($path, $excludePatterns) {
    foreach ($excludePatterns as $pattern) {
        if (strpos($path, $pattern) !== false) {
            return true;
        }
    }
    return false;
}

/**
 * Fungsi untuk menambahkan file/folder ke ZIP
 */
function addToZip($zip, $path, $basePath = '', $excludePatterns = []) {
    if (shouldExclude($path, $excludePatterns)) {
        return;
    }
    
    $fullPath = $basePath ? $basePath . '/' . $path : $path;
    
    if (is_file($fullPath)) {
        $zipPath = str_replace('\\', '/', $path);
        $zip->addFile($fullPath, $zipPath);
    } elseif (is_dir($fullPath)) {
        $files = scandir($fullPath);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filePath = $path . '/' . $file;
                addToZip($zip, $filePath, $basePath, $excludePatterns);
            }
        }
    }
}

echo "Mengumpulkan file...\n";

$fileCount = 0;
foreach ($includeItems as $item) {
    if (file_exists($item)) {
        addToZip($zip, $item, '', $excludePatterns);
        $fileCount++;
    }
}

// Tambahkan file .htaccess di root (untuk redirect ke public)
echo "Menambahkan .htaccess di root...\n";
$htaccessContent = <<<'HTACCESS'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
HTACCESS;
$zip->addFromString('.htaccess', $htaccessContent);

// Tambahkan file .env.example jika ada
if (file_exists('.env.example')) {
    echo "Menambahkan .env.example...\n";
    addToZip($zip, '.env.example', '', $excludePatterns);
} else {
    echo "PERINGATAN: File .env.example tidak ditemukan!\n";
}

// Tambahkan README untuk Hostinger
$readmeContent = <<<'README'
# Instruksi Deploy ke Hostinger

## Langkah-langkah:

1. **Upload file ZIP ini ke Hostinger cPanel**
   - Gunakan fitur "Upload Backup" di Hostinger
   - Atau extract di local, lalu upload via FTP/File Manager

2. **Extract file ZIP di server**
   - Extract di folder `public_html` atau folder yang ditentukan

3. **Install Dependencies**
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   ```

4. **Setup Environment**
   - Copy `.env.example` ke `.env`
   - Edit `.env` dengan konfigurasi database Hostinger:
     ```
     DB_CONNECTION=mysql
     DB_HOST=localhost
     DB_PORT=3306
     DB_DATABASE=nama_database_dari_cpanel
     DB_USERNAME=username_database_dari_cpanel
     DB_PASSWORD=password_database_dari_cpanel
     ```

5. **Generate App Key**
   ```bash
   php artisan key:generate
   ```

6. **Run Migrations**
   ```bash
   php artisan migrate --force
   ```

7. **Create Storage Link**
   ```bash
   php artisan storage:link
   ```

8. **Set Permissions**
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

9. **Optimize**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

10. **Point Document Root**
    - Di cPanel, pastikan document root mengarah ke folder `public`
    - Atau buat `.htaccess` di root yang redirect ke `public`

## Catatan:
- Pastikan PHP version >= 8.2
- Pastikan extension PHP yang diperlukan sudah aktif
- File `.env` tidak termasuk dalam ZIP untuk keamanan
README;

$zip->addFromString('HOSTINGER-DEPLOY-README.txt', $readmeContent);

$zip->close();

$zipSize = filesize($zipName) / 1024 / 1024; // Convert to MB

echo "\n";
echo "========================================\n";
echo "ZIP untuk Hostinger berhasil dibuat!\n";
echo "File: {$zipName}\n";
echo "Ukuran: " . round($zipSize, 2) . " MB\n";
echo "========================================\n";
echo "\n";
echo "CATATAN PENTING:\n";
echo "1. File ini sudah disiapkan khusus untuk Hostinger\n";
echo "2. Pastikan struktur folder Laravel lengkap\n";
echo "3. Upload file ZIP ini ke Hostinger cPanel\n";
echo "4. Ikuti instruksi di HOSTINGER-DEPLOY-README.txt\n";
echo "\n";

