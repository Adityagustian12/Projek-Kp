<?php

/**
 * Script untuk mengecek struktur ZIP file
 */

$zipFile = 'kosku-v2-hostinger-20251206-171258.zip';

if (!file_exists($zipFile)) {
    die("File ZIP tidak ditemukan: {$zipFile}\n");
}

$zip = new ZipArchive();
if ($zip->open($zipFile) !== TRUE) {
    die("Tidak dapat membuka file ZIP.\n");
}

echo "========================================\n";
echo "Struktur File ZIP untuk Hostinger\n";
echo "========================================\n\n";

// File penting yang harus ada
$requiredFiles = [
    'composer.json',
    'artisan',
    'package.json',
    '.htaccess',
    'app/',
    'config/',
    'database/',
    'public/',
    'resources/',
    'routes/',
    'storage/',
];

echo "File/Folder yang Diperlukan:\n";
echo "----------------------------\n";

$allFiles = [];
for ($i = 0; $i < $zip->numFiles; $i++) {
    $filename = $zip->getNameIndex($i);
    $allFiles[] = $filename;
}

$found = [];
$missing = [];

foreach ($requiredFiles as $required) {
    $foundFile = false;
    foreach ($allFiles as $file) {
        if (strpos($file, $required) === 0 || $file === $required) {
            $foundFile = true;
            break;
        }
    }
    
    if ($foundFile) {
        $found[] = $required;
        echo "✓ {$required}\n";
    } else {
        $missing[] = $required;
        echo "✗ {$required} - TIDAK DITEMUKAN!\n";
    }
}

echo "\n";
echo "Total file dalam ZIP: " . $zip->numFiles . "\n";
echo "\n";

if (count($missing) > 0) {
    echo "PERINGATAN: Beberapa file penting tidak ditemukan!\n";
} else {
    echo "✓ Semua file penting ditemukan!\n";
}

// Tampilkan beberapa file di root
echo "\n";
echo "File di Root Level:\n";
echo "-------------------\n";
$rootFiles = array_filter($allFiles, function($file) {
    return strpos($file, '/') === false || substr_count($file, '/') === 0;
});
foreach (array_slice($rootFiles, 0, 20) as $file) {
    echo "  - {$file}\n";
}

$zip->close();

echo "\n";
echo "========================================\n";

