# PowerShell Script untuk membuat file ZIP dari proyek Kos-Kosan
# Exclude: node_modules, vendor, .git, storage/logs, dll

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Membuat ZIP Backup Proyek Kos-Kosan" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Set nama file ZIP dengan tanggal
$dateStamp = Get-Date -Format "yyyyMMdd-HHmmss"
$zipName = "kosku-v2-backup-$dateStamp.zip"

Write-Host "Nama file ZIP: $zipName" -ForegroundColor Yellow
Write-Host ""

# Hapus file ZIP lama jika ada
if (Test-Path $zipName) {
    Remove-Item $zipName -Force
    Write-Host "File ZIP lama dihapus." -ForegroundColor Yellow
}

# Daftar folder/file yang akan di-include
$includeItems = @(
    "app",
    "artisan",
    "bootstrap",
    "composer.json",
    "composer.lock",
    "config",
    "database",
    "deploy.bat",
    "deploy.sh",
    "DEPLOYMENT_QUICK_START.md",
    "export_to_mysql.sql",
    "HOSTING_GUIDE.md",
    "package.json",
    "package-lock.json",
    "phpunit.xml",
    "Procfile",
    "public",
    "railway.json",
    "README.md",
    "resources",
    "routes",
    "storage\app\public",
    "storage\framework",
    "tests",
    "vercel.json",
    "vite.config.js"
)

# Daftar folder/file yang akan di-exclude
$excludePatterns = @(
    "node_modules",
    "vendor",
    ".git",
    "storage\logs",
    "storage\framework\cache",
    "storage\framework\sessions",
    "storage\framework\testing",
    "storage\framework\views",
    ".env",
    ".env.backup",
    ".phpunit.result.cache",
    "Homestead.json",
    "Homestead.yaml",
    "npm-debug.log",
    "yarn-error.log",
    ".fleet",
    ".idea",
    ".vscode",
    "database\database.sqlite"
)

Write-Host "Mengumpulkan file..." -ForegroundColor Yellow

# Kumpulkan semua file yang akan di-zip
$filesToZip = @()

foreach ($item in $includeItems) {
    if (Test-Path $item) {
        if ((Get-Item $item).PSIsContainer) {
            # Jika folder, ambil semua file di dalamnya
            $files = Get-ChildItem -Path $item -Recurse -File | Where-Object {
                $shouldExclude = $false
                foreach ($pattern in $excludePatterns) {
                    if ($_.FullName -like "*\$pattern\*" -or $_.FullName -like "*\$pattern") {
                        $shouldExclude = $true
                        break
                    }
                }
                return -not $shouldExclude
            }
            $filesToZip += $files
        } else {
            # Jika file
            $filesToZip += Get-Item $item
        }
    }
}

Write-Host "Menemukan $($filesToZip.Count) file untuk di-zip." -ForegroundColor Green
Write-Host "Membuat file ZIP..." -ForegroundColor Yellow

# Buat file ZIP
try {
    $zip = [System.IO.Compression.ZipFile]::Open($zipName, [System.IO.Compression.ZipArchiveMode]::Create)
    
    foreach ($file in $filesToZip) {
        $relativePath = $file.FullName.Substring((Get-Location).Path.Length + 1)
        $relativePath = $relativePath.Replace('\', '/')
        
        # Skip jika file ada di exclude patterns
        $shouldExclude = $false
        foreach ($pattern in $excludePatterns) {
            if ($relativePath -like "*$pattern*") {
                $shouldExclude = $true
                break
            }
        }
        
        if (-not $shouldExclude) {
            [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $file.FullName, $relativePath) | Out-Null
        }
    }
    
    $zip.Dispose()
    
    $zipSize = (Get-Item $zipName).Length / 1MB
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "ZIP berhasil dibuat: $zipName" -ForegroundColor Green
    Write-Host "Ukuran: $([math]::Round($zipSize, 2)) MB" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
} catch {
    Write-Host ""
    Write-Host "ERROR: Gagal membuat ZIP file!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
}

