# Logo Upload Troubleshooting Guide

## Masalah: Logo aplikasi tidak bisa diupload atau terbaca di server production

### Langkah-langkah Diagnosa

#### 1. Jalankan Script Diagnosa
```bash
php diagnose_logo_upload.php
```

Script ini akan memeriksa:
- Permissions direktori storage
- Keberadaan symlink storage
- Konfigurasi PHP untuk file upload
- Status direktori logos
- File operations test

#### 2. Jalankan Script Perbaikan
```bash
php fix_logo_upload.php
```

Script ini akan:
- Membuat direktori storage yang diperlukan
- Memperbaiki permissions
- Membuat storage symlink
- Membersihkan cache Laravel
- Test file operations

### Masalah Umum dan Solusinya

#### 1. Storage Directory Permissions
**Masalah:** Web server tidak memiliki permission untuk menulis ke direktori storage

**Solusi:**
```bash
# Set permissions untuk storage directory
chmod -R 755 storage/
chown -R www-data:www-data storage/  # untuk Apache
# atau
chown -R nginx:nginx storage/        # untuk Nginx
```

#### 2. Storage Symlink Tidak Ada
**Masalah:** Symlink dari `public/storage` ke `storage/app/public` tidak ada

**Solusi:**
```bash
# Hapus direktori/symlink yang ada
rm -rf public/storage

# Buat symlink baru
php artisan storage:link
```

#### 3. PHP Upload Configuration
**Masalah:** PHP tidak dikonfigurasi untuk upload file yang cukup besar

**Solusi:** Edit `php.ini`
```ini
upload_max_filesize = 2M
post_max_size = 2M
max_execution_time = 300
memory_limit = 256M
file_uploads = On
```

#### 4. Web Server Configuration
**Masalah:** Web server tidak mengizinkan upload file

**Solusi Apache (.htaccess):**
```apache
php_value upload_max_filesize 2M
php_value post_max_size 2M
php_value max_execution_time 300
php_value memory_limit 256M
```

**Solusi Nginx:**
```nginx
client_max_body_size 2M;
```

#### 5. SELinux (CentOS/RHEL)
**Masalah:** SELinux memblokir file operations

**Solusi:**
```bash
# Set SELinux context untuk storage
setsebool -P httpd_can_network_connect 1
setsebool -P httpd_can_network_connect_db 1
chcon -R -t httpd_exec_t storage/
```

### Verifikasi Setelah Perbaikan

#### 1. Test Upload Manual
```bash
# Test upload file ke direktori logos
echo "test" > storage/app/public/logos/test.txt
ls -la storage/app/public/logos/

# Atau gunakan script test yang sudah disediakan
php test_logo_upload.php
```

#### 2. Test via Browser
- Akses `/settings/organization`
- Coba upload logo
- Periksa apakah file tersimpan di `storage/app/public/logos/`

#### 3. Test Display Logo
- Periksa apakah logo ditampilkan di sidebar
- Periksa apakah logo ditampilkan di welcome page
- Periksa URL logo: `/storage/logos/[filename]`

### Log Monitoring

#### 1. Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

#### 2. Web Server Logs
```bash
# Apache
tail -f /var/log/apache2/error.log

# Nginx
tail -f /var/log/nginx/error.log
```

#### 3. PHP Error Logs
```bash
tail -f /var/log/php_errors.log
```

### Konfigurasi Environment

Pastikan file `.env` memiliki konfigurasi yang benar:

```env
APP_ENV=production
APP_DEBUG=false
FILESYSTEM_DISK=local

# Untuk production, pastikan APP_URL benar
APP_URL=https://yourdomain.com
```

### Testing Commands

#### 1. Test Storage Configuration
```bash
php artisan tinker
>>> Storage::disk('public')->put('test.txt', 'test content');
>>> Storage::disk('public')->exists('test.txt');
>>> Storage::disk('public')->url('test.txt');
```

#### 2. Test Organization Settings
```bash
php artisan tinker
>>> $org = App\Models\OrgSettings::getInstance();
>>> $org->logo_path;
>>> Storage::disk('public')->exists($org->logo_path);
```

### Backup dan Recovery

#### 1. Backup Logo Files
```bash
# Backup semua logo yang ada
tar -czf logo_backup_$(date +%Y%m%d).tar.gz storage/app/public/logos/
```

#### 2. Restore Logo Files
```bash
# Restore dari backup
tar -xzf logo_backup_YYYYMMDD.tar.gz
```

### Kontak Support

Jika masalah masih berlanjut:
1. Jalankan script diagnosa dan kirim output-nya
2. Periksa log files untuk error messages
3. Verifikasi konfigurasi server dan PHP
4. Test dengan file upload yang lebih kecil

### Hasil Diagnosa yang Berhasil

Berdasarkan hasil diagnosa yang sudah berjalan:

```
✓ Storage directory exists dan writable
✓ Public storage symlink exists  
✓ Logos directory berhasil dibuat
✓ File operations test berhasil
✓ PHP configuration sudah cukup (2M upload, 8M post)
```

**Status:** Logo upload functionality sudah siap digunakan!

### Langkah Selanjutnya

1. **Perbaiki masalah permissions dan cache:**
   ```bash
   # Fix permissions
   bash fix_permissions.sh
   
   # Fix cache table issue
   php fix_cache_table.php
   
   # Fix production issues
   php fix_production_issues.php
   ```

2. **Test upload logo di aplikasi:**
   - Akses `/settings/organization`
   - Upload logo baru
   - Periksa apakah logo ditampilkan di sidebar dan welcome page

3. **Jika masih ada masalah, jalankan:**
   ```bash
   php fix_production_config.php
   php test_logo_upload.php
   ```

### Masalah yang Ditemukan dan Solusinya

#### 1. Permission Denied untuk Log File
**Error:** `Permission denied` untuk `/var/www/pdsystem.trust-idn.id/pdsystem/storage/logs/laravel.log`

**Solusi:**
```bash
# Fix log file permissions
chmod 644 storage/logs/laravel.log
chmod 755 storage/logs/
```

#### 2. Cache Table Tidak Ada
**Error:** `no such table: cache` di SQLite database

**Solusi:**
```bash
# Fix cache table
php fix_cache_table.php
```

### File yang Terlibat

- `app/Livewire/Settings/OrganizationSettings.php` - Logic upload logo
- `config/filesystems.php` - Konfigurasi storage
- `resources/views/livewire/settings/organization-settings.blade.php` - Form upload
- `resources/views/components/dynamic-app-logo.blade.php` - Display logo
- `resources/views/welcome.blade.php` - Welcome page logo
- `storage/app/public/logos/` - Direktori penyimpanan logo
