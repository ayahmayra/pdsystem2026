# Sistem Role-Based Access Control

## Overview

Sistem telah dikembalikan ke **role-based access control** dengan manajemen akses untuk setiap role, membuat sistem lebih simpel dan mudah dikelola.

## Perubahan Utama

### 1. **Role-Based vs Permission-Based**

#### **SEBELUM (Permission-Based):**
- User memiliki permissions langsung (direct permissions)
- Superadmin mengatur permissions individual untuk setiap user
- Lebih kompleks untuk dikelola

#### **SESUDAH (Role-Based):**
- User memiliki role yang menentukan permissions
- Superadmin mengatur permissions untuk setiap role
- Lebih simpel dan terstruktur

### 2. **Role Structure**

```php
// 5 Role Utama
'super-admin'                    // Akses penuh ke semua fitur
'admin'                         // Mengelola master data dan user
'bendahara-pengeluaran'         // Mengelola semua dokumen tanpa scope unit
'bendahara-pengeluaran-pembantu' // Mengelola dokumen dengan scope unit
'sekretariat'                   // Hanya akses fitur rekapitulasi
```

### 3. **PermissionHelper Methods**

```php
// Role-based checking
PermissionHelper::isSuperAdmin()           // Cek apakah super-admin
PermissionHelper::canManageUsers()         // Cek bisa mengelola user (admin atau super-admin)
PermissionHelper::canManagePermissions()   // Cek bisa mengelola permissions (hanya super-admin)
PermissionHelper::canAccessAllData()       // Cek bisa akses semua data

// Feature-based checking
PermissionHelper::canManageMasterData()    // Cek bisa mengelola master data
PermissionHelper::canManageDocuments()     // Cek bisa mengelola dokumen
PermissionHelper::canManageReferenceRates() // Cek bisa mengelola tarif referensi
PermissionHelper::canManageLocations()     // Cek bisa mengelola lokasi
```

## Cara Kerja Sistem Baru

### 1. **Role Management Interface**

#### **A. Daftar Role (`/roles`)**
- Menampilkan semua role dalam sistem
- Menampilkan jumlah user dan permissions per role
- Deskripsi role dan fungsinya
- Tombol "Kelola Permissions" untuk setiap role

#### **B. Kelola Permissions Role (`/roles/{role}/permissions`)**
- Interface untuk mengatur permissions per role
- Grouped permissions berdasarkan kategori
- Aksi cepat: Reset ke Default, Hapus Semua, Pilih Semua
- Toggle permissions per group

### 2. **Default Permissions per Role**

#### **A. Super Admin**
```php
[
    'master-data.view', 'master-data.create', 'master-data.edit', 'master-data.delete',
    'users.view', 'users.create', 'users.edit', 'users.delete',
    'documents.view', 'documents.create', 'documents.edit', 'documents.delete', 'documents.approve',
    'nota-dinas.view', 'nota-dinas.create', 'nota-dinas.edit', 'nota-dinas.delete', 'nota-dinas.approve',
    'spt.view', 'spt.create', 'spt.edit', 'spt.delete', 'spt.approve',
    'sppd.view', 'sppd.create', 'sppd.edit', 'sppd.delete', 'sppd.approve',
    'receipts.view', 'receipts.create', 'receipts.edit', 'receipts.delete', 'receipts.approve',
    'trip-reports.view', 'trip-reports.create', 'trip-reports.edit', 'trip-reports.delete', 'trip-reports.approve',
    'rekap.view', 'rekap.export',
    'reference-rates.view', 'reference-rates.create', 'reference-rates.edit', 'reference-rates.delete',
    'locations.view', 'locations.create', 'locations.edit', 'locations.delete',
]
```

#### **B. Admin**
```php
[
    'master-data.view', 'master-data.create', 'master-data.edit', 'master-data.delete',
    'users.view', 'users.create', 'users.edit', 'users.delete',
    'rekap.view', 'rekap.export',
    'reference-rates.view', 'reference-rates.create', 'reference-rates.edit', 'reference-rates.delete',
    'locations.view', 'locations.create', 'locations.edit', 'locations.delete',
]
```

#### **C. Bendahara Pengeluaran**
```php
[
    'documents.view', 'documents.create', 'documents.edit', 'documents.delete', 'documents.approve',
    'nota-dinas.view', 'nota-dinas.create', 'nota-dinas.edit', 'nota-dinas.delete', 'nota-dinas.approve',
    'spt.view', 'spt.create', 'spt.edit', 'spt.delete', 'spt.approve',
    'sppd.view', 'sppd.create', 'sppd.edit', 'sppd.delete', 'sppd.approve',
    'receipts.view', 'receipts.create', 'receipts.edit', 'receipts.delete', 'receipts.approve',
    'trip-reports.view', 'trip-reports.create', 'trip-reports.edit', 'trip-reports.delete', 'trip-reports.approve',
    'rekap.view', 'rekap.export',
]
```

#### **D. Bendahara Pengeluaran Pembantu**
```php
[
    'documents.view', 'documents.create', 'documents.edit', 'documents.delete', 'documents.approve',
    'nota-dinas.view', 'nota-dinas.create', 'nota-dinas.edit', 'nota-dinas.delete', 'nota-dinas.approve',
    'spt.view', 'spt.create', 'spt.edit', 'spt.delete', 'spt.approve',
    'sppd.view', 'sppd.create', 'sppd.edit', 'sppd.delete', 'sppd.approve',
    'receipts.view', 'receipts.create', 'receipts.edit', 'receipts.delete', 'receipts.approve',
    'trip-reports.view', 'trip-reports.create', 'trip-reports.edit', 'trip-reports.delete', 'trip-reports.approve',
    'rekap.view', 'rekap.export',
]
```

#### **E. Sekretariat**
```php
[
    'rekap.view', 'rekap.export',
]
```

## Interface Features

### 1. **Role Index Page**

#### **Features:**
- Daftar semua role dengan informasi lengkap
- Search functionality untuk mencari role
- Jumlah user dan permissions per role
- Deskripsi role dan fungsinya
- Tombol "Kelola Permissions" untuk setiap role

#### **Access Control:**
- Hanya super-admin yang bisa akses
- Link muncul di Master Data jika user bisa manage permissions

### 2. **Manage Role Permissions Page**

#### **Features:**
- Interface untuk mengatur permissions per role
- Grouped permissions berdasarkan kategori (master-data, users, documents, dll)
- Toggle permissions per group
- Aksi cepat:
  - Reset ke Default
  - Hapus Semua Permissions
  - Pilih Semua Permissions
- Ringkasan permissions yang dipilih

#### **Permission Groups:**
- **Master Data**: master-data.view, master-data.create, master-data.edit, master-data.delete
- **Users**: users.view, users.create, users.edit, users.delete
- **Documents**: documents.view, documents.create, documents.edit, documents.delete, documents.approve
- **Nota Dinas**: nota-dinas.view, nota-dinas.create, nota-dinas.edit, nota-dinas.delete, nota-dinas.approve
- **SPT**: spt.view, spt.create, spt.edit, spt.delete, spt.approve
- **SPPD**: sppd.view, sppd.create, sppd.edit, sppd.delete, sppd.approve
- **Receipts**: receipts.view, receipts.create, receipts.edit, receipts.delete, receipts.approve
- **Trip Reports**: trip-reports.view, trip-reports.create, trip-reports.edit, trip-reports.delete, trip-reports.approve
- **Rekap**: rekap.view, rekap.export
- **Reference Rates**: reference-rates.view, reference-rates.create, reference-rates.edit, reference-rates.delete
- **Locations**: locations.view, locations.create, locations.edit, locations.delete

## Testing Results

### 1. **Super Admin Test**
```bash
User: H. AREADY (Super Admin)
Is Super Admin: Yes ✅
Can manage users: Yes ✅
Can manage permissions: Yes ✅
Can access all data: Yes ✅
```

### 2. **User 29 Test**
```bash
User: HERMANSYAH (Super Admin)
Is Super Admin: Yes ✅
Can manage users: Yes ✅
Can manage permissions: Yes ✅
Can access all data: Yes ✅
```

## Keuntungan Sistem Baru

### 1. **Simplicity**
- Lebih mudah dikelola dengan role-based approach
- Permissions dikelompokkan per role
- Interface yang user-friendly untuk mengatur permissions

### 2. **Scalability**
- Mudah menambah role baru
- Default permissions yang sudah terdefinisi
- Consistent permission structure

### 3. **Maintainability**
- Centralized permission management
- Clear role hierarchy
- Easy to audit and review

### 4. **User Experience**
- Intuitive interface untuk role management
- Grouped permissions untuk kemudahan navigasi
- Quick actions untuk common tasks

## Files yang Dibuat/Dimodifikasi

### 1. **New Files**
- `app/Livewire/Roles/ManageRolePermissions.php` - Component untuk manage role permissions
- `app/Livewire/Roles/Index.php` - Component untuk daftar role
- `resources/views/livewire/roles/manage-role-permissions.blade.php` - View untuk manage permissions
- `resources/views/livewire/roles/index.blade.php` - View untuk daftar role

### 2. **Modified Files**
- `app/Helpers/PermissionHelper.php` - Updated untuk role-based checking
- `app/Livewire/Users/Create.php` - Updated access control
- `app/Livewire/Users/Edit.php` - Updated access control
- `resources/views/livewire/master-data/index.blade.php` - Added role management link
- `routes/web.php` - Added role management routes

## Cara Menggunakan

### 1. **Akses Role Management**
1. Login sebagai super-admin
2. Buka Master Data
3. Klik "Manajemen Role"

### 2. **Kelola Permissions Role**
1. Pilih role yang akan dikelola
2. Klik "Kelola Permissions"
3. Centang/hilangkan permissions yang diinginkan
4. Gunakan "Toggle All" untuk group permissions
5. Klik "Simpan Permissions"

### 3. **Aksi Cepat**
- **Reset ke Default**: Mengembalikan permissions ke default role
- **Hapus Semua**: Menghapus semua permissions dari role
- **Pilih Semua**: Memilih semua permissions untuk role

## Best Practices

### 1. **Role Management**
- Gunakan default permissions sebagai starting point
- Review permissions secara berkala
- Test permissions setelah perubahan

### 2. **Permission Assignment**
- Berikan permissions minimal yang diperlukan
- Gunakan grouped permissions untuk kemudahan
- Dokumentasikan perubahan permissions

### 3. **User Assignment**
- Assign role yang sesuai dengan job function
- Review user roles secara berkala
- Monitor role usage dan effectiveness

## Troubleshooting

### 1. **Role Tidak Muncul**
- Cek apakah role sudah ada di database
- Jalankan seeder untuk membuat role default
- Cek permission untuk akses role management

### 2. **Permissions Tidak Tersimpan**
- Cek apakah user memiliki permission untuk manage permissions
- Cek database untuk memastikan permissions tersimpan
- Test dengan user yang memiliki role yang benar

### 3. **Access Denied**
- Cek apakah user memiliki role yang sesuai
- Cek permissions yang dimiliki role
- Test dengan super-admin untuk memastikan fitur berfungsi

## Kesimpulan

Sistem role-based access control memberikan kemudahan dalam mengelola permissions dengan interface yang user-friendly. Superadmin bisa dengan mudah mengatur permissions untuk setiap role, dan sistem menjadi lebih simpel dan terstruktur.

**Sistem ini lebih mudah dikelola, scalable, dan user-friendly!** ✅
