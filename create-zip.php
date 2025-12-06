<?php

/**
 * Script PHP untuk membuat file ZIP dari proyek Kos-Kosan
 * Exclude: node_modules, vendor, .git, storage/logs, dll
 */

echo "========================================\n";
echo "Membuat ZIP Backup Proyek Kos-Kosan\n";
echo "========================================\n\n";

// Set nama file ZIP dengan tanggal
$dateStamp = date('Ymd-His');
$zipName = "kosku-v2-backup-{$dateStamp}.zip";

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

// Daftar folder/file yang akan di-include
$includeItems = [
    'app',
    'artisan',
    'bootstrap',
    'composer.json',
    'composer.lock',
    'config',
    'database',
    'deploy.bat',
    'deploy.sh',
    'DEPLOYMENT_QUICK_START.md',
    'export_to_mysql.sql',
    'HOSTING_GUIDE.md',
    'package.json',
    'package-lock.json',
    'phpunit.xml',
    'Procfile',
    'public',
    'railway.json',
    'README.md',
    'resources',
    'routes',
    'storage/app/public',
    'storage/framework',
    'tests',
    'vercel.json',
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
        echo "  + {$zipPath}\n";
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

$zip->close();

$zipSize = filesize($zipName) / 1024 / 1024; // Convert to MB

echo "\n";
echo "========================================\n";
echo "ZIP berhasil dibuat: {$zipName}\n";
echo "Ukuran: " . round($zipSize, 2) . " MB\n";
echo "========================================\n";

