# 🚨 Maintenance Mode - Emergency Recovery Guide

## ⚠️ JIKA TERKUNCI (Tidak Bisa Login/Akses)

Jika Anda mengaktifkan maintenance mode dan **tidak bisa login atau akses sistem**, ikuti langkah berikut:

---

## 🔧 **Metode 1: Menggunakan Artisan Command (RECOMMENDED)**

### **Nonaktifkan Maintenance Mode:**

```bash
php artisan maintenance:toggle off
```

### **Cek Status:**

```bash
php artisan maintenance:toggle status
```

### **Aktifkan Kembali (Jika Diperlukan):**

```bash
php artisan maintenance:toggle on
```

---

## 🔧 **Metode 2: Menggunakan Tinker**

Jika command tidak tersedia, gunakan tinker:

```bash
php artisan tinker
```

Kemudian jalankan di tinker console:

```php
DB::table('org_settings')->update(['maintenance_mode' => false]);
exit
```

---

## 🔧 **Metode 3: Edit Database Langsung**

### **Melalui MySQL Client:**

```bash
mysql -u pdsystemuser -p pdsystemdb
```

Kemudian jalankan query:

```sql
UPDATE org_settings SET maintenance_mode = 0;
EXIT;
```

### **Melalui phpMyAdmin:**

1. Buka phpMyAdmin
2. Pilih database `pdsystemdb`
3. Buka tabel `org_settings`
4. Edit row dan set `maintenance_mode` = `0`
5. Save

---

## 🔧 **Metode 4: Hapus Cache**

Setelah disable maintenance mode, selalu clear cache:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

Atau semua sekaligus:

```bash
php artisan optimize:clear
```

---

## 📋 **Command Reference**

### **Toggle Maintenance Mode:**

| Command | Deskripsi |
|---------|-----------|
| `php artisan maintenance:toggle` | Interactive mode (pilih action) |
| `php artisan maintenance:toggle on` | Aktifkan maintenance mode |
| `php artisan maintenance:toggle off` | Nonaktifkan maintenance mode |
| `php artisan maintenance:toggle status` | Cek status saat ini |

---

## 🔍 **Troubleshooting**

### **Problem 1: Halaman Login Tidak Muncul**

**Solusi:**
1. Clear cache: `php artisan optimize:clear`
2. Pastikan maintenance mode OFF: `php artisan maintenance:toggle status`
3. Restart web server jika perlu

### **Problem 2: Admin Sudah Login Tapi Tetap Kena Block**

**Solusi:**
1. Test dengan command:
   ```bash
   php artisan maintenance:test [USER_ID]
   ```
   
2. Pastikan user memiliki role `superadmin` atau `super-admin`:
   ```bash
   php artisan tinker
   User::find(29)->hasRole('super-admin'); // Should return true
   ```
   
3. Jika false, assign role:
   ```bash
   User::find(29)->assignRole('super-admin');
   ```

> 💡 **Note:** Sistem mendukung kedua format: `superadmin` dan `super-admin`

### **Problem 3: Maintenance Mode Tidak Bisa Dinonaktifkan dari UI**

**Solusi:**
- Gunakan command line: `php artisan maintenance:toggle off`
- Atau edit database langsung (Metode 3)

---

## 🛡️ **Best Practices**

### **SEBELUM Aktifkan Maintenance Mode:**

1. ✅ Pastikan Anda sudah login sebagai **superadmin**
2. ✅ Pastikan role superadmin sudah benar
3. ✅ Test login di tab/browser lain untuk memastikan
4. ✅ Catat command emergency: `php artisan maintenance:toggle off`
5. ✅ Pastikan Anda punya akses SSH/terminal ke server

### **SAAT Maintenance Mode Aktif:**

1. ✅ Superadmin tetap bisa akses `/login`
2. ✅ Superadmin bisa akses semua halaman setelah login
3. ✅ User biasa akan lihat halaman maintenance
4. ✅ Klik "Login Admin" di halaman maintenance untuk login

### **SETELAH Selesai Maintenance:**

1. ✅ Nonaktifkan maintenance mode dari UI atau command
2. ✅ Test dengan akun user biasa
3. ✅ Clear cache: `php artisan optimize:clear`

---

## 🔐 **Security Notes**

1. **Hanya superadmin** yang bisa bypass maintenance mode
2. Halaman `/login` **SELALU accessible** (tidak diblokir)
3. Semua authentication routes tetap accessible
4. Command `maintenance:toggle` bisa dijalankan dari server manapun yang punya akses ke code

---

## 📞 **Emergency Contact**

Jika semua metode gagal:

1. Check log: `storage/logs/laravel.log`
2. Restart web server: `sudo systemctl restart nginx` atau `sudo systemctl restart apache2`
3. Restart PHP-FPM: `sudo systemctl restart php8.2-fpm`

---

## 🎯 **Quick Recovery Checklist**

```bash
# 1. Nonaktifkan maintenance mode
php artisan maintenance:toggle off

# 2. Clear semua cache
php artisan optimize:clear

# 3. Restart queue workers (jika ada)
php artisan queue:restart

# 4. Cek status
php artisan maintenance:toggle status
```

---

## ✅ **Verifikasi Recovery**

Setelah recovery, test:

1. ✅ Buka `/login` - Harus bisa akses
2. ✅ Login dengan superadmin - Harus berhasil
3. ✅ Login dengan user biasa - Harus berhasil
4. ✅ Akses dashboard - Harus bisa
5. ✅ Cek Organization Settings - Maintenance mode harus OFF

---

**Last Updated:** January 8, 2026  
**Version:** 1.0

