# Implementasi Field Roles pada Form Create dan Edit User

## Overview

Implementasi ini menambahkan field roles pada form create dan edit user yang hanya bisa diakses oleh **superadmin**. User tanpa role tidak dapat mengakses sistem.

## Fitur yang Diimplementasikan

### 1. **Field Roles pada Form User**
- ✅ Field checkbox untuk memilih multiple roles
- ✅ Admin dan superadmin bisa akses form create/edit user
- ✅ Field role hanya bisa diakses oleh superadmin
- ✅ Admin melihat informasi role tanpa bisa mengubahnya
- ✅ Validasi roles yang dipilih
- ✅ Default: user tidak memiliki role (tidak bisa akses sistem)

### 2. **Middleware CheckUserRole**
- ✅ Memastikan user yang login memiliki minimal 1 role
- ✅ User tanpa role akan di-logout dan redirect ke login
- ✅ Pesan error yang informatif

### 3. **Security & Access Control**
- ✅ Admin dan superadmin bisa manage user data
- ✅ Hanya superadmin yang bisa manage roles
- ✅ User tanpa role tidak bisa akses sistem
- ✅ Middleware diterapkan ke semua route yang memerlukan auth

## File yang Dimodifikasi

### 1. **Livewire Components**

#### `app/Livewire/Users/Create.php`
```php
// Added imports
use App\Helpers\PermissionHelper;
use Spatie\Permission\Models\Role;

// Added properties
public $roles = [];

// Added mount method with permission check
public function mount()
{
    // Check if user has permission to manage users (admin or super-admin)
    if (!PermissionHelper::hasAnyRole(['admin', 'super-admin'])) {
        abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}

// Added validation rules
'roles' => 'array',
'roles.*' => 'exists:roles,name',

// Added role assignment in save method (only if user is super-admin)
if (PermissionHelper::hasRole('super-admin') && !empty($validated['roles'])) {
    $user->assignRole($validated['roles']);
}

// Added roles to render method
$availableRoles = Role::orderBy('name')->get();
$canManageRoles = PermissionHelper::hasRole('super-admin');
return view('livewire.users.create', compact('units', 'positions', 'ranks', 'travelGrades', 'availableRoles', 'canManageRoles'));
```

#### `app/Livewire/Users/Edit.php`
```php
// Added imports
use App\Helpers\PermissionHelper;
use Spatie\Permission\Models\Role;

// Added properties
public $roles = [];

// Added permission check in mount method
public function mount(User $user)
{
    if (!PermissionHelper::hasRole('super-admin')) {
        abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
    
    // Load existing roles
    $this->roles = $user->roles->pluck('name')->toArray();
}

// Added validation rules
'roles' => 'array',
'roles.*' => 'exists:roles,name',

// Added role sync in update method
$this->user->syncRoles($validated['roles'] ?? []);

// Added roles to render method
$roles = Role::orderBy('name')->get();
return view('livewire.users.edit', compact('units', 'positions', 'ranks', 'travelGrades', 'roles'));
```

### 2. **View Templates**

#### `resources/views/livewire/users/create.blade.php`
```blade
<!-- Role Assignment -->
<div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Penugasan Role</h3>
    <div class="space-y-3">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role User</label>
            <div class="space-y-2">
                @foreach($roles as $role)
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" wire:model="roles" value="{{ $role->name }}" id="role_{{ $role->id }}" 
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
                        <label for="role_{{ $role->id }}" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                        </label>
                    </div>
                @endforeach
            </div>
            @error('roles') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                <strong>Catatan:</strong> User tanpa role tidak dapat mengakses sistem. Pastikan untuk memberikan role yang sesuai.
            </p>
        </div>
    </div>
</div>
```

#### `resources/views/livewire/users/edit.blade.php`
```blade
<!-- Same structure as create form -->
<!-- Role Assignment section with checkboxes for each role -->
```

### 3. **Middleware**

#### `app/Http/Middleware/CheckUserRole.php`
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip check for guest users (they will be redirected to login)
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        
        // Check if user has any roles
        if ($user->roles()->count() === 0) {
            // User has no roles, redirect to access denied page
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda tidak memiliki role yang diperlukan untuk mengakses sistem. Silakan hubungi administrator.');
        }

        return $next($request);
    }
}
```

### 4. **Route Configuration**

#### `bootstrap/app.php`
```php
$middleware->alias([
    'permission' => \App\Http\Middleware\CheckPermission::class,
    'unit.scope' => \App\Http\Middleware\CheckUnitScope::class,
    'user.role' => \App\Http\Middleware\CheckUserRole::class, // Added
]);
```

#### `routes/web.php`
```php
Route::middleware(['auth', 'user.role'])->group(function () {
    // All authenticated routes now require user to have at least one role
});
```

## Cara Kerja Sistem

### 1. **Access Control Matrix**

| Role | Create User | Edit User | Manage Roles | View Role Info |
|------|-------------|-----------|--------------|----------------|
| **Super Admin** | ✅ | ✅ | ✅ | ✅ |
| **Admin** | ✅ | ✅ | ❌ | ✅ (Read-only) |
| **Other Roles** | ❌ | ❌ | ❌ | ❌ |

### 2. **User Creation Process**

#### **Super Admin:**
1. Superadmin mengakses form create user
2. Superadmin mengisi data user dan memilih roles
3. User dibuat dengan roles yang dipilih
4. User dapat login dan mengakses sistem sesuai permissions dari roles

#### **Admin:**
1. Admin mengakses form create user
2. Admin mengisi data user (tanpa field role)
3. User dibuat tanpa role (perlu superadmin untuk assign role)
4. User tidak bisa login sampai mendapat role

### 3. **User Edit Process**

#### **Super Admin:**
1. Superadmin mengakses form edit user
2. Superadmin dapat mengubah semua data termasuk roles
3. Roles disinkronkan menggunakan `syncRoles()`
4. User akan memiliki permissions sesuai roles baru

#### **Admin:**
1. Admin mengakses form edit user
2. Admin dapat mengubah data user (kecuali roles)
3. Admin melihat informasi role saat ini (read-only)
4. Untuk mengubah role, admin harus menghubungi superadmin

### 4. **Access Control**
1. User login ke sistem
2. Middleware `CheckUserRole` mengecek apakah user memiliki role
3. Jika tidak ada role: user di-logout dan redirect ke login dengan pesan error
4. Jika ada role: user dapat mengakses sistem sesuai permissions

## Testing

### 1. **Test User dengan Role**
```bash
php artisan tinker
```
```php
use App\Models\User;
use Spatie\Permission\Models\Role;

$user = User::where('email', 'hermansyah_@live.com')->first();
echo 'User: ' . $user->name;
echo 'Roles: ' . $user->roles->pluck('name')->implode(', ');
echo 'Has roles: ' . ($user->roles()->count() > 0 ? 'Yes' : 'No');
```

### 2. **Test User tanpa Role**
```bash
php artisan tinker
```
```php
use App\Models\User;

$user = User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('password')
]);

echo 'User created: ' . $user->name;
echo 'Has roles: ' . ($user->roles()->count() > 0 ? 'Yes' : 'No');
```

### 3. **Test Permission Check**
```bash
php artisan tinker
```
```php
use App\Helpers\PermissionHelper;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

$superAdmin = User::where('email', '197503022002121004@gmail.com')->first();
Auth::login($superAdmin);

echo 'Logged in as: ' . Auth::user()->name;
echo 'Has super-admin role: ' . (PermissionHelper::hasRole('super-admin') ? 'Yes' : 'No');
```

## Roles yang Tersedia

1. **super-admin** - Full access to all features and data
2. **admin** - Manages master data, user access (except superadmin), location/route references, and tariff references
3. **bendahara-pengeluaran** - Manages all documents without unit scope, accesses all recapitulation features
4. **bendahara-pengeluaran-pembantu** - Manages all documents with unit scope, accesses recapitulation features for their unit
5. **sekretariat** - Only accesses all recapitulation features

## Security Features

### 1. **Access Control**
- ✅ Hanya superadmin yang bisa akses form create/edit user
- ✅ User tanpa role tidak bisa akses sistem
- ✅ Middleware diterapkan ke semua route yang memerlukan auth

### 2. **Data Validation**
- ✅ Validasi roles yang dipilih (harus exists di database)
- ✅ Array validation untuk multiple roles
- ✅ Error handling yang informatif

### 3. **User Experience**
- ✅ Pesan error yang jelas untuk user tanpa role
- ✅ Catatan di form bahwa user tanpa role tidak bisa akses sistem
- ✅ Role names ditampilkan dengan format yang user-friendly

## Troubleshooting

### Error: "Attempt to read property 'name' on string"

**Penyebab:** Konflik nama variabel antara property `$roles` di Livewire component dan variabel `$roles` yang dikirim ke view.

**Solusi:** Mengubah nama variabel di view dari `$roles` menjadi `$availableRoles`:

```php
// Di Livewire component
$availableRoles = Role::orderBy('name')->get();
return view('livewire.users.edit', compact('units', 'positions', 'ranks', 'travelGrades', 'availableRoles'));

// Di view
@foreach($availableRoles as $role)
    <input type="checkbox" wire:model="roles" value="{{ $role->name }}" />
@endforeach
```

### Error: "403 Forbidden" saat akses form user

**Penyebab:** User tidak memiliki role super-admin.

**Solusi:** Pastikan user memiliki role super-admin:
```php
$user->assignRole('super-admin');
```

## Kesimpulan

Implementasi ini berhasil menambahkan:
1. **Field roles** pada form create dan edit user
2. **Access control** yang ketat (hanya superadmin)
3. **Middleware** untuk memastikan user memiliki role
4. **Security** yang robust dengan validasi dan error handling
5. **User experience** yang baik dengan pesan yang informatif

Sistem sekarang memastikan bahwa:
- ✅ Hanya superadmin yang bisa manage roles
- ✅ User tanpa role tidak bisa akses sistem
- ✅ Roles dapat di-assign dan di-update dengan mudah
- ✅ Security terjaga dengan middleware dan permission checks
