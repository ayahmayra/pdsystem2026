# 🔧 Maintenance Mode Feature - Implementation Guide

## 📋 Overview

Fitur **Maintenance Mode** memungkinkan administrator untuk menonaktifkan akses sistem kepada semua pengguna (kecuali superadmin) saat sistem sedang dalam pemeliharaan.

---

## ✅ Implementation Completed

### 1. **Database Migration** ✓
**File:** `database/migrations/2026_01_08_010417_add_maintenance_mode_to_org_settings_table.php`

Menambahkan 2 kolom baru ke tabel `org_settings`:
- `maintenance_mode` (boolean, default: false)
- `maintenance_message` (text, nullable)

```bash
php artisan migrate
```

---

### 2. **Model Update** ✓
**File:** `app/Models/OrgSettings.php`

- Menambahkan `maintenance_mode` dan `maintenance_message` ke `$fillable`
- Menambahkan cast `maintenance_mode` sebagai boolean

---

### 3. **Livewire Component** ✓
**File:** `app/Livewire/Settings/OrganizationSettings.php`

Properties ditambahkan:
```php
public $maintenance_mode = false;
public $maintenance_message = '';
```

Validasi rules:
```php
'maintenance_mode' => 'boolean',
'maintenance_message' => 'nullable|string|max:500',
```

---

### 4. **UI Form** ✓
**File:** `resources/views/livewire/settings/organization-settings.blade.php`

Section baru ditambahkan dengan:
- Toggle checkbox untuk mengaktifkan/menonaktifkan maintenance mode
- Textarea untuk pesan maintenance
- Warning notice untuk mengingatkan administrator

---

### 5. **Middleware** ✓
**File:** `app/Http/Middleware/CheckMaintenanceMode.php`

Logic:
1. Cek apakah `maintenance_mode` aktif
2. Jika aktif:
   - Allow **superadmin** untuk bypass
   - Block semua user lain dan tampilkan halaman maintenance
3. Jika tidak aktif, lanjutkan request normal

---

### 6. **Maintenance View** ✓
**File:** `resources/views/maintenance.blade.php`

Halaman maintenance dengan:
- ✨ Design modern menggunakan Tailwind CSS
- 🎨 Responsive layout
- 🔄 Auto-refresh setiap 5 menit
- 🏢 Menampilkan logo organisasi
- 💬 Pesan custom dari admin
- 🔘 Tombol "Coba Lagi"

---

### 7. **Middleware Registration** ✓
**File:** `bootstrap/app.php`

Middleware di-register dengan:
- Alias: `maintenance.check`
- Applied to: **web middleware group** (semua routes web)

```php
$middleware->alias([
    'maintenance.check' => \App\Http\Middleware\CheckMaintenanceMode::class,
]);

$middleware->web(append: [
    \App\Http\Middleware\CheckMaintenanceMode::class,
]);
```

---

## 🎯 How to Use

### **Metode 1: Via UI (Recommended for Normal Use)**

#### **Mengaktifkan Maintenance Mode:**

1. Login sebagai **superadmin**
2. Navigasi ke **Configuration** → **Organisasi**
3. Scroll ke section **"Mode Maintenance"**
4. ✅ Centang checkbox **"Aktifkan Mode Maintenance"**
5. Isi **"Pesan Maintenance"** (opsional)
6. Klik **"Simpan Konfigurasi"**

#### **Menonaktifkan Maintenance Mode:**

1. Login sebagai **superadmin**
2. Kembali ke **Configuration** → **Organisasi**
3. ❌ Uncheck checkbox **"Aktifkan Mode Maintenance"**
4. Klik **"Simpan Konfigurasi"**

### **Metode 2: Via Command Line (Emergency/Server Access)**

#### **Nonaktifkan (Emergency):**
```bash
php artisan maintenance:toggle off
```

#### **Aktifkan:**
```bash
php artisan maintenance:toggle on
```

#### **Cek Status:**
```bash
php artisan maintenance:toggle status
```

> 💡 **Gunakan command line jika terkunci atau tidak bisa akses UI**

---

## 🔐 Access Control

| User Role | Akses Saat Maintenance Mode |
|-----------|----------------------------|
| **Superadmin** | ✅ Full access (bypass) |
| **All other users** | ❌ Blocked (lihat halaman maintenance) |
| **Unauthenticated** | ❌ Blocked (lihat halaman maintenance) |

### 🔑 **Cara Admin Login Saat Maintenance Mode:**

1. **Halaman login tetap accessible** - Admin bisa mengakses `/login`
2. Di halaman maintenance, ada tombol **"Login Admin"** yang mengarah ke halaman login
3. Setelah login sebagai **superadmin** atau **super-admin**, akan otomatis bypass maintenance mode
4. Superadmin bisa langsung akses dashboard dan menonaktifkan maintenance mode

> 💡 **Note:** Middleware mendukung kedua format role name: `superadmin` dan `super-admin`

### 📋 **Routes yang Dikecualikan dari Maintenance Check:**
- `/login` - Halaman login
- `/logout` - Logout
- `/register` - Register (jika digunakan)
- `/password/*` - Password reset routes
- `/forgot-password` - Forgot password
- `/reset-password` - Reset password
- `/verify-email` - Email verification
- `/confirm-password` - Confirm password

Semua routes authentication di atas **tetap accessible** saat maintenance mode aktif.

---

## 🎨 Maintenance Page Features

### Visual Elements:
- 🏢 Logo organisasi (jika ada)
- ⚙️ Icon maintenance yang menarik
- 📝 Pesan custom dari admin
- 🔄 Auto-refresh setiap 5 menit
- 📱 Responsive design (mobile-friendly)
- 🌙 Dark mode support

### User Experience:
- Clear messaging
- Tombol "Coba Lagi" untuk refresh manual
- Informasi kapan sistem akan kembali online
- Footer dengan contact info

---

## 📂 Files Modified/Created

### Created:
1. `database/migrations/2026_01_08_010417_add_maintenance_mode_to_org_settings_table.php`
2. `app/Http/Middleware/CheckMaintenanceMode.php`
3. `app/Console/Commands/MaintenanceModeToggle.php` ⭐ **Emergency Command**
4. `resources/views/maintenance.blade.php`
5. `MAINTENANCE_MODE_GUIDE.md` (this file)
6. `MAINTENANCE_MODE_EMERGENCY_RECOVERY.md` 🚨 **Emergency Guide**

### Modified:
1. `app/Models/OrgSettings.php`
2. `app/Livewire/Settings/OrganizationSettings.php`
3. `resources/views/livewire/settings/organization-settings.blade.php`
4. `bootstrap/app.php`

---

## 🧪 Testing Checklist

- [ ] Aktifkan maintenance mode sebagai superadmin
- [ ] Verify superadmin masih bisa akses sistem
- [ ] Logout dan verify user biasa tidak bisa akses
- [ ] Verify pesan maintenance muncul dengan benar
- [ ] Verify auto-refresh bekerja (tunggu 5 menit)
- [ ] Verify tombol "Coba Lagi" bekerja
- [ ] Verify responsive design di mobile
- [ ] Nonaktifkan maintenance mode
- [ ] Verify semua user bisa akses kembali

---

## 💡 Technical Notes

### Why Apply to Web Middleware Group?
- Middleware ditambahkan ke **web middleware group** agar check maintenance dilakukan pada **semua routes web**
- Ini memastikan tidak ada route yang terlewat
- Superadmin bypass logic memastikan admin tetap bisa mengelola sistem

### Auto-Refresh Logic
```javascript
setTimeout(function(){
    location.reload();
}, 300000); // 5 minutes
```
- Page akan auto-refresh setiap 5 menit
- Ini memastikan user tidak perlu manual refresh untuk cek apakah sistem sudah kembali online

### HTTP Status Code
- Halaman maintenance dikembalikan dengan **503 Service Unavailable**
- Ini adalah status code standard untuk maintenance mode

---

## 🔍 Troubleshooting

### Masalah 1: Terkunci - Tidak Bisa Login/Akses
**Solusi:** 
```bash
# Emergency disable
php artisan maintenance:toggle off
php artisan optimize:clear
```
📖 **Lihat:** [MAINTENANCE_MODE_EMERGENCY_RECOVERY.md](MAINTENANCE_MODE_EMERGENCY_RECOVERY.md)

### Masalah 2: Superadmin juga terblokir setelah login
**Solusi:** 
- Pastikan superadmin memiliki role `superadmin` (exact match)
- Check dengan: `php artisan tinker` → `User::find(1)->hasRole('superadmin')`

### Masalah 3: Perubahan tidak berlaku
**Solusi:** Clear cache
```bash
php artisan optimize:clear
```

### Masalah 4: Logo tidak muncul di halaman maintenance
**Solusi:**
- Pastikan storage link sudah dibuat: `php artisan storage:link`
- Pastikan logo sudah diupload di Organization Settings

### Masalah 5: Halaman login tidak muncul
**Solusi:**
- Middleware sudah dikonfigurasi untuk allow `/login`
- Clear cache: `php artisan optimize:clear`
- Check error di `storage/logs/laravel.log`

---

## 🎉 Feature Complete!

Fitur maintenance mode sekarang sudah lengkap dan siap digunakan. Administrator dapat dengan mudah mengaktifkan/menonaktifkan mode maintenance dari halaman Organization Settings.

**Last Updated:** January 8, 2026

