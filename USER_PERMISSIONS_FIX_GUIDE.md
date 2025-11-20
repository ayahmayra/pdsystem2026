# Panduan Perbaikan User Permissions

## Overview

Masalah telah diperbaiki dimana user dengan role admin (ID 54) masih bisa melakukan operasi hapus dan edit user meskipun sudah diatur hanya bisa membuat dan melihat user. Perbaikan ini memastikan bahwa permission checks menggunakan granular permissions daripada role-based checks.

## Masalah yang Ditemukan

### 1. **UI Permission Checks**
- File `resources/views/livewire/users/index.blade.php` menggunakan `PermissionHelper::canManageUsers()` untuk semua operasi
- Method `canManageUsers()` mengembalikan `true` untuk role admin, padahal seharusnya menggunakan permission checks yang spesifik

### 2. **Livewire Component Permission Checks**
- `app/Livewire/Users/Edit.php` menggunakan `PermissionHelper::canManageUsers()` di method `mount()`
- `app/Livewire/Users/Create.php` menggunakan `PermissionHelper::canManageUsers()` di method `mount()`
- `app/Livewire/Users/Index.php` tidak memiliki permission check di method `delete()`

## Perbaikan yang Dilakukan

### 1. **UI Permission Checks (Fixed)**

#### **Before:**
```blade
@if(\App\Helpers\PermissionHelper::canManageUsers())
<a href="{{ route('users.create') }}">Tambah Pegawai</a>
@endif

@if(\App\Helpers\PermissionHelper::canManageUsers())
<a href="{{ route('users.edit', $user) }}">Edit</a>
@endif

@if(\App\Helpers\PermissionHelper::canManageUsers())
<button wire:click="delete({{ $user->id }})">Hapus</button>
@endif
```

#### **After:**
```blade
@if(\App\Helpers\PermissionHelper::can('users.create'))
<a href="{{ route('users.create') }}">Tambah Pegawai</a>
@endif

@if(\App\Helpers\PermissionHelper::can('users.edit'))
<a href="{{ route('users.edit', $user) }}">Edit</a>
@endif

@if(\App\Helpers\PermissionHelper::can('users.delete'))
<button wire:click="delete({{ $user->id }})">Hapus</button>
@endif
```

### 2. **Livewire Component Permission Checks (Fixed)**

#### **A. Users/Edit.php**
```php
// Before
public function mount(User $user)
{
    if (!PermissionHelper::canManageUsers()) {
        abort(403, 'Anda tidak memiliki izin untuk mengelola user.');
    }
    // ...
}

// After
public function mount(User $user)
{
    if (!PermissionHelper::can('users.edit')) {
        abort(403, 'Anda tidak memiliki izin untuk mengedit user.');
    }
    // ...
}
```

#### **B. Users/Create.php**
```php
// Before
public function mount()
{
    if (!PermissionHelper::canManageUsers()) {
        abort(403, 'Anda tidak memiliki izin untuk mengelola user.');
    }
}

// After
public function mount()
{
    if (!PermissionHelper::can('users.create')) {
        abort(403, 'Anda tidak memiliki izin untuk membuat user.');
    }
}
```

#### **C. Users/Index.php**
```php
// Before
public function delete(User $user)
{
    try {
        $user->delete();
        session()->flash('message', 'Data pegawai berhasil dihapus.');
    } catch (\Exception $e) {
        session()->flash('error', 'Gagal menghapus data pegawai. ' . $e->getMessage());
    }
}

// After
public function delete(User $user)
{
    if (!PermissionHelper::can('users.delete')) {
        session()->flash('error', 'Anda tidak memiliki izin untuk menghapus user.');
        return;
    }
    
    try {
        $user->delete();
        session()->flash('message', 'Data pegawai berhasil dihapus.');
    } catch (\Exception $e) {
        session()->flash('error', 'Gagal menghapus data pegawai. ' . $e->getMessage());
    }
}
```

## Testing Results

### 1. **User ID 54 (Admin Role) - After Fix**
```bash
User: admin (User 54)
Can view users: Yes ✅
Can create users: Yes ✅
Can edit users: No ❌
Can delete users: No ❌
```

### 2. **Super Admin - After Fix**
```bash
User: H. AREADY (Super Admin)
Can view users: Yes ✅
Can create users: Yes ✅
Can edit users: Yes ✅
Can delete users: Yes ✅
```

## Files yang Dimodifikasi

### 1. **UI Files**
- `resources/views/livewire/users/index.blade.php`
  - Changed `PermissionHelper::canManageUsers()` to specific permission checks
  - `users.create` for "Tambah Pegawai" button
  - `users.edit` for "Edit" link
  - `users.delete` for "Hapus" button

### 2. **Livewire Component Files**
- `app/Livewire/Users/Edit.php`
  - Changed `mount()` method to use `PermissionHelper::can('users.edit')`
- `app/Livewire/Users/Create.php`
  - Changed `mount()` method to use `PermissionHelper::can('users.create')`
- `app/Livewire/Users/Index.php`
  - Added `PermissionHelper` import
  - Added permission check in `delete()` method using `PermissionHelper::can('users.delete')`

## Permission Matrix

### **Super Admin**
- ✅ `users.view` - Can view users
- ✅ `users.create` - Can create users
- ✅ `users.edit` - Can edit users
- ✅ `users.delete` - Can delete users

### **Admin (User ID 54)**
- ✅ `users.view` - Can view users
- ✅ `users.create` - Can create users
- ❌ `users.edit` - Cannot edit users
- ❌ `users.delete` - Cannot delete users

### **Other Roles**
- ❌ No user management permissions (unless explicitly assigned)

## Keuntungan Perbaikan

### 1. **Granular Control**
- Kontrol akses yang sangat spesifik untuk setiap operasi user
- User hanya bisa melakukan operasi yang diizinkan
- Interface yang lebih aman dan konsisten

### 2. **Security Enhancement**
- Mencegah akses tidak sah ke operasi user tertentu
- Permission checks di level UI dan controller
- Consistent permission enforcement

### 3. **User Experience**
- Interface yang lebih jelas tentang operasi yang tersedia
- Error messages yang informatif
- Consistent behavior across the application

### 4. **Maintainability**
- Permission logic yang lebih mudah dipahami
- Consistent use of permission checks
- Easier to debug permission issues

## Best Practices

### 1. **Permission Check Strategy**
- Always use specific permission checks (`PermissionHelper::can('permission.name')`)
- Avoid using role-based checks (`PermissionHelper::canManageUsers()`) for granular operations
- Check permissions at both UI and controller levels

### 2. **Error Handling**
- Provide clear error messages for permission violations
- Use appropriate HTTP status codes (403 for forbidden)
- Log permission violations for security monitoring

### 3. **Testing**
- Test permission checks with different user roles
- Verify UI elements are hidden/shown correctly
- Test controller-level permission enforcement

## Troubleshooting

### 1. **User Still Can Perform Unauthorized Operations**
- Check if permission is correctly assigned to user's role
- Verify UI permission checks are using correct permission names
- Check if controller-level permission checks are implemented

### 2. **UI Elements Not Showing/Hiding Correctly**
- Verify permission names in UI checks match database permissions
- Check if user has the required permissions
- Test with different user roles

### 3. **Permission Check Errors**
- Verify `PermissionHelper::can()` method is working correctly
- Check if user is properly authenticated
- Verify permission names are correct

## Kesimpulan

Perbaikan ini memastikan bahwa sistem user management menggunakan permission checks yang granular dan konsisten. User dengan role admin sekarang hanya bisa melakukan operasi yang diizinkan (view dan create), sementara operasi edit dan delete diblokir sesuai dengan permission yang diberikan.

**Sistem sekarang lebih aman dan konsisten dalam mengelola akses user operations!** ✅
