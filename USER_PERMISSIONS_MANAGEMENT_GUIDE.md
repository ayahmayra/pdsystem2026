# Panduan Pengelolaan User Permissions Berdasarkan Akses Halaman

## Overview

Sistem ini memungkinkan **Super Admin** untuk mengelola permissions user secara granular berdasarkan akses halaman tertentu. Setiap user dapat memiliki permissions yang berbeda dari role default mereka.

## Fitur yang Diimplementasikan

### 1. **Manage Permissions Interface**
- ✅ Interface untuk mengelola permissions user secara individual
- ✅ Grouping permissions berdasarkan kategori (Master Data, Documents, dll)
- ✅ Toggle permissions per group atau individual
- ✅ Reset ke permissions dari role default
- ✅ Clear all permissions atau select all permissions

### 2. **Permission Categories**
- ✅ **Master Data**: View, Create, Edit, Delete master data
- ✅ **User Management**: View, Create, Edit, Delete users
- ✅ **Documents**: View, Create, Edit, Delete, Approve documents
- ✅ **Nota Dinas**: View, Create, Edit, Delete, Approve nota dinas
- ✅ **SPT**: View, Create, Edit, Delete, Approve SPT
- ✅ **SPPD**: View, Create, Edit, Delete, Approve SPPD
- ✅ **Receipts**: View, Create, Edit, Delete, Approve receipts
- ✅ **Trip Reports**: View, Create, Edit, Delete, Approve trip reports
- ✅ **Rekapitulasi**: View, Export rekapitulasi
- ✅ **Referensi Tarif**: View, Create, Edit, Delete reference rates
- ✅ **Lokasi & Rute**: View, Create, Edit, Delete locations
- ✅ **All Access**: Akses penuh ke semua fitur

### 3. **Access Control**
- ✅ Hanya Super Admin yang bisa mengelola permissions
- ✅ Admin dan role lainnya tidak bisa akses halaman manage permissions
- ✅ User dapat memiliki permissions individual di luar role mereka

## File yang Dibuat

### 1. **Livewire Component**

#### `app/Livewire/Users/ManagePermissions.php`
```php
<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Helpers\PermissionHelper;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ManagePermissions extends Component
{
    public User $user;
    public $selectedPermissions = [];
    public $availablePermissions = [];
    public $userPermissions = [];
    public $userRoles = [];

    public function mount(User $user)
    {
        // Check if user has permission to manage permissions (only super-admin)
        if (!PermissionHelper::hasRole('super-admin')) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
        
        $this->user = $user;
        $this->loadUserData();
    }

    // Methods untuk mengelola permissions
    public function savePermissions() { ... }
    public function resetToRolePermissions() { ... }
    public function clearAllPermissions() { ... }
    public function selectAllPermissions() { ... }
    public function toggleGroupPermissions($groupName) { ... }
    public function getPermissionGroups() { ... }
    public function getPermissionDisplayName($permissionName) { ... }
}
```

### 2. **View Template**

#### `resources/views/livewire/users/manage-permissions.blade.php`
- Interface untuk mengelola permissions
- Grouping permissions berdasarkan kategori
- Toggle buttons untuk memudahkan selection
- Summary permissions dari role vs permissions yang dipilih

### 3. **Route Configuration**

#### `routes/web.php`
```php
Route::get('users/{user}/permissions', \App\Livewire\Users\ManagePermissions::class)->name('users.permissions');
```

### 4. **Navigation Update**

#### `resources/views/livewire/users/index.blade.php`
- Tambahan tombol "Kelola Permissions" di dropdown actions
- Hanya visible untuk Super Admin

## Cara Menggunakan Sistem

### 1. **Akses Halaman Manage Permissions**

#### **Super Admin:**
1. Login sebagai Super Admin
2. Buka halaman "Data Pegawai" (`/users`)
3. Klik dropdown actions pada user yang ingin dikelola
4. Pilih "Kelola Permissions"
5. Halaman manage permissions akan terbuka

#### **Admin/Role Lainnya:**
- Tidak bisa mengakses halaman manage permissions
- Tombol "Kelola Permissions" tidak akan muncul

### 2. **Mengelola Permissions**

#### **Interface Features:**
- **Pilih Semua**: Select semua permissions
- **Reset ke Role**: Reset ke permissions dari role default user
- **Hapus Semua**: Clear semua permissions
- **Toggle All**: Toggle semua permissions dalam satu group

#### **Permission Groups:**
- Setiap group memiliki toggle untuk select/deselect semua permissions dalam group
- Counter menunjukkan berapa permissions yang dipilih dalam setiap group

#### **Saving Changes:**
1. Pilih/deselect permissions yang diinginkan
2. Klik "Simpan Permissions"
3. Permissions akan disinkronkan ke user
4. User akan memiliki permissions baru sesuai yang dipilih

### 3. **Permission Hierarchy**

#### **Default Behavior:**
- User mendapatkan permissions dari role mereka
- Super Admin dapat menambah/mengurangi permissions individual
- Permissions individual akan override permissions dari role

#### **Example:**
```
User dengan role "Admin" memiliki permissions:
- master-data.view
- master-data.create
- master-data.edit
- master-data.delete

Super Admin dapat menambah permissions:
- documents.view
- documents.create

User akan memiliki semua permissions tersebut.
```

## Permission Mapping ke Halaman

### 1. **Master Data Permissions**
- `master-data.view` → Akses halaman master data
- `master-data.create` → Tombol "Tambah" di master data
- `master-data.edit` → Tombol "Edit" di master data
- `master-data.delete` → Tombol "Hapus" di master data

### 2. **User Management Permissions**
- `users.view` → Akses halaman data pegawai
- `users.create` → Tombol "Tambah Pegawai"
- `users.edit` → Tombol "Edit" di data pegawai
- `users.delete` → Tombol "Hapus" di data pegawai

### 3. **Document Permissions**
- `documents.view` → Akses halaman dokumen
- `documents.create` → Tombol "Buat Dokumen"
- `documents.edit` → Tombol "Edit" di dokumen
- `documents.delete` → Tombol "Hapus" di dokumen
- `documents.approve` → Tombol "Approve" di dokumen

### 4. **Specific Document Types**
- `nota-dinas.*` → Permissions untuk Nota Dinas
- `spt.*` → Permissions untuk SPT
- `sppd.*` → Permissions untuk SPPD
- `receipts.*` → Permissions untuk Kwitansi
- `trip-reports.*` → Permissions untuk Laporan Perjalanan

### 5. **Rekapitulasi Permissions**
- `rekap.view` → Akses halaman rekapitulasi
- `rekap.export` → Tombol "Export" di rekapitulasi

### 6. **Reference Permissions**
- `reference-rates.*` → Permissions untuk referensi tarif
- `locations.*` → Permissions untuk lokasi & rute

### 7. **All Access Permission**
- `all-access` → Akses penuh ke semua fitur sistem

## Implementation di Controllers/Views

### 1. **Controller Level**
```php
// Di controller
public function index()
{
    if (!PermissionHelper::can('documents.view')) {
        abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
    
    // Controller logic
}
```

### 2. **View Level**
```blade
{{-- Di view --}}
@if(PermissionHelper::can('documents.create'))
    <a href="{{ route('documents.create') }}" class="btn btn-primary">
        Buat Dokumen
    </a>
@endif

@if(PermissionHelper::can('documents.edit'))
    <a href="{{ route('documents.edit', $document) }}" class="btn btn-secondary">
        Edit
    </a>
@endif
```

### 3. **Middleware Level**
```php
// Di routes
Route::middleware(['auth', 'permission:documents.view'])->group(function () {
    Route::get('documents', [DocumentController::class, 'index']);
});

Route::middleware(['auth', 'permission:documents.create'])->group(function () {
    Route::get('documents/create', [DocumentController::class, 'create']);
});
```

## Testing

### 1. **Test Super Admin Access**
```bash
php artisan tinker
```
```php
use App\Helpers\PermissionHelper;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

$superAdmin = User::where('email', '197503022002121004@gmail.com')->first();
Auth::login($superAdmin);

echo 'Can manage permissions: ' . (PermissionHelper::hasRole('super-admin') ? 'Yes' : 'No');
```

### 2. **Test User Permissions**
```php
$user = User::find(1);
echo 'User permissions: ' . $user->getAllPermissions()->pluck('name')->implode(', ');
echo 'Can view documents: ' . ($user->can('documents.view') ? 'Yes' : 'No');
```

### 3. **Test Permission Assignment**
```php
$user = User::find(1);
$user->givePermissionTo('documents.view');
$user->givePermissionTo('documents.create');

echo 'New permissions: ' . $user->getAllPermissions()->pluck('name')->implode(', ');
```

## Best Practices

### 1. **Permission Naming Convention**
- Format: `{module}.{action}`
- Examples: `documents.view`, `users.create`, `rekap.export`

### 2. **Role vs Individual Permissions**
- **Roles**: Untuk permissions yang umum (admin, user, dll)
- **Individual Permissions**: Untuk permissions khusus per user

### 3. **Permission Checking**
- Selalu check permissions di controller dan view
- Gunakan `PermissionHelper::can()` untuk consistency
- Implementasikan middleware untuk route protection

### 4. **User Experience**
- Berikan feedback yang jelas jika user tidak memiliki permission
- Sembunyikan UI elements yang tidak bisa diakses
- Gunakan tooltips untuk menjelaskan permission requirements

## Troubleshooting

### 1. **Permission tidak berfungsi**
- Check apakah permission sudah di-assign ke user
- Check apakah permission sudah di-assign ke role user
- Check apakah permission name sudah benar

### 2. **User tidak bisa akses halaman**
- Check middleware permission
- Check controller permission check
- Check apakah user memiliki role yang diperlukan

### 3. **UI elements tidak muncul**
- Check view permission checks
- Check apakah permission name sudah benar
- Check apakah user sudah login

## Kesimpulan

Sistem pengelolaan permissions ini memberikan fleksibilitas penuh untuk Super Admin dalam mengatur akses user ke berbagai fitur sistem. Dengan interface yang user-friendly dan permission mapping yang jelas, sistem ini memungkinkan pengelolaan akses yang granular dan mudah dipahami.

**Key Benefits:**
- ✅ **Granular Control**: Permissions per halaman/fitur
- ✅ **User-Friendly Interface**: Easy to use permission management
- ✅ **Flexible**: Individual permissions override role permissions
- ✅ **Secure**: Only Super Admin can manage permissions
- ✅ **Scalable**: Easy to add new permissions and features
