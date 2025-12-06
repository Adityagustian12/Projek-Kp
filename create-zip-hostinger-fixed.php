<?php

/**
 * Script untuk membuat file ZIP khusus Hostinger dengan struktur yang benar
 * Memastikan semua file penting ada di root untuk deteksi framework
 */

echo "========================================\n";
echo "Membuat ZIP Backup untuk Hostinger (Fixed)\n";
echo "========================================\n\n";

// Set nama file ZIP dengan tanggal
$dateStamp = date('Ymd-His');
$zipName = "kosku-v2-hostinger-fixed-{$dateStamp}.zip";

echo "Nama file ZIP: {$zipName}\n\n";

// Hapus file ZIP lama jika ada
if (file_exists($zipName)) {
    unlink($zipName);
}

if (!extension_loaded('zip')) {
    die("ERROR: Extension ZIP tidak tersedia.\n");
}

$zip = new ZipArchive();
if ($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die("ERROR: Tidak dapat membuat file ZIP.\n");
}

// File penting yang HARUS ada di root untuk deteksi Laravel
$criticalFiles = [
    'composer.json',
    'composer.lock',
    'artisan',
    'package.json',
    'package-lock.json',
    'phpunit.xml',
    'vite.config.js',
];

echo "Menambahkan file penting di root...\n";
foreach ($criticalFiles as $file) {
    if (file_exists($file)) {
        $zip->addFile($file, $file);
        echo "  ✓ {$file}\n";
    } else {
        echo "  ✗ {$file} - TIDAK DITEMUKAN!\n";
    }
}

// Tambahkan .htaccess di root (PENTING untuk Hostinger)
echo "\nMenambahkan .htaccess di root...\n";
$htaccessContent = <<<'HTACCESS'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
HTACCESS;
$zip->addFromString('.htaccess', $htaccessContent);
echo "  ✓ .htaccess\n";

// Tambahkan folder penting
$folders = [
    'app',
    'bootstrap',
    'config',
    'database',
    'public',
    'resources',
    'routes',
    'storage/app/public',
    'storage/framework',
    'tests',
];

echo "\nMenambahkan folder penting...\n";
foreach ($folders as $folder) {
    if (is_dir($folder)) {
        addFolderToZip($zip, $folder, $folder);
        echo "  ✓ {$folder}/\n";
    } else {
        echo "  ✗ {$folder}/ - TIDAK DITEMUKAN!\n";
    }
}

// Exclude patterns
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
 * Tambahkan folder ke ZIP dengan exclude patterns
 */
function addFolderToZip($zip, $folder, $zipPath, $excludePatterns = []) {
    global $excludePatterns;
    
    if (shouldExclude($folder, $excludePatterns)) {
        return;
    }
    
    $files = scandir($folder);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        
        $filePath = $folder . '/' . $file;
        $zipFilePath = $zipPath . '/' . $file;
        
        if (shouldExclude($filePath, $excludePatterns)) {
            continue;
        }
        
        if (is_dir($filePath)) {
            addFolderToZip($zip, $filePath, $zipFilePath, $excludePatterns);
        } else {
            $zip->addFile($filePath, $zipFilePath);
        }
    }
}

function shouldExclude($path, $excludePatterns) {
    foreach ($excludePatterns as $pattern) {
        if (strpos($path, $pattern) !== false) {
            return true;
        }
    }
    return false;
}

// Pastikan file composer.json valid untuk Laravel
echo "\nMemverifikasi composer.json...\n";
$composerJson = json_decode(file_get_contents('composer.json'), true);
if (isset($composerJson['name']) && strpos($composerJson['name'], 'laravel') !== false) {
    echo "  ✓ composer.json valid untuk Laravel\n";
} else {
    echo "  ⚠ composer.json mungkin tidak terdeteksi sebagai Laravel\n";
}

// Tambahkan file README
echo "\nMenambahkan file README...\n";
$readmeContent = <<<'README'
# Instruksi Deploy ke Hostinger

## File ZIP ini sudah disiapkan khusus untuk Hostinger

### Struktur yang sudah disertakan:
- ✓ composer.json (untuk deteksi Laravel)
- ✓ artisan (Laravel CLI)
- ✓ .htaccess (redirect ke public)
- ✓ Folder app/, config/, database/, public/, resources/, routes/, storage/
- ✓ Semua file penting Laravel

### Langkah-langkah setelah upload:

1. Extract file ZIP di public_html
2. Install dependencies:
   composer install --no-dev --optimize-autoloader
   npm install && npm run build

3. Buat file .env:
   cp .env.example .env
   (edit dengan database Hostinger)

4. Generate key:
   php artisan key:generate

5. Run migrations:
   php artisan migrate --force

6. Create storage link:
   php artisan storage:link

7. Set permissions:
   chmod -R 755 storage bootstrap/cache

8. Optimize:
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
README;

$zip->addFromString('DEPLOY-README.txt', $readmeContent);
echo "  ✓ DEPLOY-README.txt\n";

$zip->close();

$zipSize = filesize($zipName) / 1024 / 1024;

echo "\n";
echo "========================================\n";
echo "ZIP untuk Hostinger berhasil dibuat!\n";
echo "File: {$zipName}\n";
echo "Ukuran: " . round($zipSize, 2) . " MB\n";
echo "========================================\n";
echo "\n";
echo "CATATAN:\n";
echo "- File ini sudah disiapkan dengan struktur yang benar\n";
echo "- Pastikan upload file ZIP ini ke Hostinger\n";
echo "- Jika masih error, coba extract manual dan upload via FTP\n";
echo "\n";

