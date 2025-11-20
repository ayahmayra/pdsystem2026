# Sistem Permission-Based Access Control

## Overview

Sistem telah diubah dari role-based menjadi **permission-based access control**, dimana superadmin mengatur hak akses setiap user secara spesifik tanpa harus mengatur role tertentu.

## Perubahan Utama

### 1. **Permission-Based vs Role-Based**

#### **SEBELUM (Role-Based):**
- User memiliki role (admin, super-admin, dll)
- Role menentukan permissions yang bisa diakses
- Sulit untuk memberikan permission spesifik ke user tertentu

#### **SESUDAH (Permission-Based):**
- User memiliki permissions langsung (direct permissions)
- Superadmin bisa memberikan permission spesifik ke user manapun
- Lebih fleksibel dan granular

### 2. **System Permissions Baru**

```php
// System Management Permissions
'system.manage-users'        // Mengelola user (create, edit, delete)
'system.manage-permissions'  // Mengelola permissions user
'system.access-all-data'     // Akses semua data tanpa scope unit
```

### 3. **PermissionHelper Methods Baru**

```php
// System Management
PermissionHelper::isSuperAdmin()           // Cek apakah superadmin
PermissionHelper::canManageUsers()         // Cek bisa mengelola user
PermissionHelper::canManagePermissions()   // Cek bisa mengelola permissions
PermissionHelper::canAccessAllData()       // Cek bisa akses semua data

// Feature Management
PermissionHelper::canManageMasterData()    // Cek bisa mengelola master data
PermissionHelper::canManageDocuments()     // Cek bisa mengelola dokumen
PermissionHelper::canManageReferenceRates() // Cek bisa mengelola tarif referensi
PermissionHelper::canManageLocations()     // Cek bisa mengelola lokasi

// Permission Summary
PermissionHelper::getUserPermissionSummary() // Ringkasan permissions user
```

## Cara Kerja Sistem Baru

### 1. **Superadmin Setup**

Superadmin memiliki permissions:
- `system.manage-users` - Bisa mengelola semua user
- `system.manage-permissions` - Bisa mengatur permissions user
- `system.access-all-data` - Bisa akses semua data

### 2. **User Management**

#### **A. Create User**
```php
// Hanya user dengan system.manage-users yang bisa create user
if (!PermissionHelper::canManageUsers()) {
    abort(403, 'Anda tidak memiliki izin untuk mengelola user.');
}
```

#### **B. Edit User**
```php
// Hanya user dengan system.manage-users yang bisa edit user
if (!PermissionHelper::canManageUsers()) {
    abort(403, 'Anda tidak memiliki izin untuk mengelola user.');
}
```

#### **C. Manage Permissions**
```php
// Hanya user dengan system.manage-permissions yang bisa manage permissions
if (!PermissionHelper::canManagePermissions()) {
    abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
}
```

### 3. **UI Access Control**

#### **A. Tombol Tambah User**
```blade
@if(\App\Helpers\PermissionHelper::canManageUsers())
<a href="{{ route('users.create') }}">Tambah Pegawai</a>
@endif
```

#### **B. Tombol Edit User**
```blade
@if(\App\Helpers\PermissionHelper::canManageUsers())
<a href="{{ route('users.edit', $user) }}">Edit</a>
@endif
```

#### **C. Tombol Kelola Permissions**
```blade
@if(\App\Helpers\PermissionHelper::canManagePermissions())
<a href="{{ route('users.permissions', $user) }}">Kelola Permissions</a>
@endif
```

## Contoh Penggunaan

### 1. **Setup Superadmin**

```php
// Berikan system permissions ke superadmin
$superAdmin = User::where('email', 'superadmin@example.com')->first();
$superAdmin->givePermissionTo([
    'system.manage-users',
    'system.manage-permissions', 
    'system.access-all-data'
]);
```

### 2. **Setup User Biasa**

```php
// Berikan permissions spesifik ke user
$user = User::find(54);
$user->givePermissionTo([
    'users.view',           // Bisa melihat user
    'users.create',         // Bisa membuat user
    'master-data.view',     // Bisa melihat master data
    'master-data.edit',     // Bisa edit master data
    'rekap.view',           // Bisa melihat rekap
    'rekap.export'          // Bisa export rekap
]);
```

### 3. **Check Permissions**

```php
// Cek apakah user bisa mengelola user
if (PermissionHelper::canManageUsers()) {
    // User bisa mengelola user
}

// Cek apakah user bisa mengelola permissions
if (PermissionHelper::canManagePermissions()) {
    // User bisa mengelola permissions
}

// Cek permission spesifik
if (PermissionHelper::can('users.edit')) {
    // User bisa edit user
}
```

## Permission Groups

### 1. **System Permissions**
- `system.manage-users` - Mengelola user
- `system.manage-permissions` - Mengelola permissions
- `system.access-all-data` - Akses semua data

### 2. **User Permissions**
- `users.view` - Melihat user
- `users.create` - Membuat user
- `users.edit` - Mengedit user
- `users.delete` - Menghapus user

### 3. **Master Data Permissions**
- `master-data.view` - Melihat master data
- `master-data.create` - Membuat master data
- `master-data.edit` - Mengedit master data
- `master-data.delete` - Menghapus master data

### 4. **Document Permissions**
- `documents.view` - Melihat dokumen
- `documents.create` - Membuat dokumen
- `documents.edit` - Mengedit dokumen
- `documents.delete` - Menghapus dokumen
- `documents.approve` - Menyetujui dokumen

### 5. **Rekap Permissions**
- `rekap.view` - Melihat rekap
- `rekap.export` - Export rekap

### 6. **Reference Rates Permissions**
- `reference-rates.view` - Melihat tarif referensi
- `reference-rates.create` - Membuat tarif referensi
- `reference-rates.edit` - Mengedit tarif referensi
- `reference-rates.delete` - Menghapus tarif referensi

### 7. **Location Permissions**
- `locations.view` - Melihat lokasi
- `locations.create` - Membuat lokasi
- `locations.edit` - Mengedit lokasi
- `locations.delete` - Menghapus lokasi

## Testing Results

### 1. **Superadmin Test**
```bash
User: H. AREADY (Superadmin)
Is Super Admin: Yes ✅
Can manage users: Yes ✅
Can manage permissions: Yes ✅
Can access all data: Yes ✅
```

### 2. **Regular User Test**
```bash
User: admin (User 54)
Can manage users: No ✅
Can manage permissions: No ✅
Can access all data: No ✅
Can view users: Yes ✅
Can create users: Yes ✅
```

## Keuntungan Sistem Baru

### 1. **Fleksibilitas**
- Superadmin bisa memberikan permission spesifik ke user manapun
- Tidak terbatas pada role yang sudah ditentukan
- Mudah untuk menyesuaikan dengan kebutuhan organisasi

### 2. **Granular Control**
- Permission yang sangat spesifik (view, create, edit, delete)
- Bisa memberikan akses parsial ke fitur tertentu
- Control yang lebih tepat sasaran

### 3. **Scalability**
- Mudah menambah permission baru
- Tidak perlu mengubah role structure
- Sistem yang lebih maintainable

### 4. **Security**
- Access control yang lebih ketat
- User hanya bisa akses fitur yang diizinkan
- Audit trail yang lebih jelas

## Migration Guide

### 1. **Untuk Superadmin**
- Jalankan `SystemPermissionsSeeder` untuk menambah system permissions
- Berikan system permissions ke superadmin
- Test akses ke semua fitur

### 2. **Untuk User Existing**
- Review permissions yang dimiliki user
- Sesuaikan dengan kebutuhan baru
- Test akses ke fitur yang diizinkan

### 3. **Untuk Development**
- Gunakan `PermissionHelper::can()` untuk permission checking
- Hindari menggunakan `hasRole()` kecuali untuk backward compatibility
- Test semua permission checks

## Best Practices

### 1. **Permission Naming**
- Gunakan format: `module.action` (e.g., `users.create`)
- Konsisten dengan naming convention
- Descriptive dan mudah dipahami

### 2. **Permission Checking**
- Selalu gunakan `PermissionHelper::can()` di controller
- Gunakan `@if(\App\Helpers\PermissionHelper::can())` di view
- Test permission checks secara menyeluruh

### 3. **User Management**
- Berikan permissions minimal yang diperlukan
- Review permissions secara berkala
- Dokumentasikan permission assignments

## Troubleshooting

### 1. **User Tidak Bisa Akses Fitur**
- Cek apakah user memiliki permission yang diperlukan
- Cek apakah permission checking sudah benar
- Test dengan superadmin untuk memastikan fitur berfungsi

### 2. **Permission Tidak Tersimpan**
- Cek apakah permission sudah ada di database
- Cek apakah user model sudah di-refresh
- Test dengan `getAllPermissions()` untuk memastikan

### 3. **UI Element Tidak Muncul**
- Cek apakah permission checking sudah benar di view
- Cek apakah user memiliki permission yang diperlukan
- Test dengan user yang memiliki permission

## Kesimpulan

Sistem permission-based access control memberikan fleksibilitas dan kontrol yang lebih baik untuk mengelola hak akses user. Superadmin bisa memberikan permission spesifik ke user manapun tanpa terbatas pada role yang sudah ditentukan.

**Sistem ini lebih aman, fleksibel, dan mudah dikelola!** ✅
