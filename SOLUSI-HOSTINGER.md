# Solusi Error "Framework tidak kompatibel" di Hostinger

## Masalah
Error: "Framework tidak kompatibel atau struktur project tidak valid"

## Solusi 1: Upload Manual via FTP/File Manager (RECOMMENDED)

Karena Hostinger auto-detection mungkin tidak bekerja, lebih baik upload manual:

### Langkah-langkah:

1. **Extract ZIP di komputer Anda**
   - Extract file `kosku-v2-hostinger-fixed-*.zip`
   - Pastikan semua file ter-extract

2. **Login ke Hostinger cPanel**
   - Buka File Manager
   - Masuk ke folder `public_html` atau folder domain Anda

3. **Upload semua file via FTP atau File Manager**
   - Upload semua file dan folder dari hasil extract
   - Pastikan struktur folder tetap sama:
     ```
     public_html/
     ├── app/
     ├── artisan
     ├── bootstrap/
     ├── composer.json
     ├── config/
     ├── database/
     ├── .htaccess
     ├── package.json
     ├── public/
     ├── resources/
     ├── routes/
     ├── storage/
     └── ...
     ```

4. **Install Dependencies via SSH/Terminal**
   ```bash
   cd public_html
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   ```

5. **Setup Environment**
   ```bash
   cp .env.example .env
   # Edit .env dengan database Hostinger
   php artisan key:generate
   ```

6. **Run Migrations**
   ```bash
   php artisan migrate --force
   php artisan storage:link
   ```

7. **Set Permissions**
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

8. **Point Document Root**
   - Di cPanel, pastikan document root mengarah ke `public_html/public`
   - Atau pastikan `.htaccess` di root sudah benar

## Solusi 2: Gunakan Fitur "Deploy Laravel" di Hostinger

Jika Hostinger punya fitur khusus untuk Laravel:

1. Buka cPanel Hostinger
2. Cari fitur "Laravel" atau "Framework Installer"
3. Install Laravel melalui fitur tersebut
4. Upload file project Anda ke folder yang sudah dibuat

## Solusi 3: Pastikan Struktur ZIP Benar

Jika tetap ingin upload via ZIP:

1. Pastikan file `composer.json` ada di root ZIP
2. Pastikan file `artisan` ada di root ZIP
3. Pastikan folder `public/` ada
4. Pastikan file `.htaccess` ada di root

## Catatan Penting

- **JANGAN** upload folder `vendor/` dan `node_modules/` (install via Composer/NPM)
- **JANGAN** upload file `.env` (buat manual di server)
- Pastikan PHP version >= 8.2
- Pastikan extension PHP yang diperlukan aktif

## Troubleshooting

Jika masih error setelah upload manual:

1. Cek error log di cPanel
2. Pastikan semua file ter-upload dengan benar
3. Pastikan permissions folder `storage` dan `bootstrap/cache` sudah 755
4. Pastikan `.htaccess` di root sudah benar
5. Pastikan document root mengarah ke folder `public`

## Kontak Support

Jika masih bermasalah, hubungi support Hostinger dengan informasi:
- Error message lengkap
- Struktur folder yang sudah di-upload
- PHP version yang digunakan

