# Panduan Implementasi Roles dan Permissions

## Overview

Sistem ini menggunakan **Spatie Laravel Permission** untuk mengelola roles dan permissions dengan 5 level akses:

1. **Super Admin** - Akses penuh ke semua fitur dan data
2. **Admin** - Mengelola master data, user, referensi tarif
3. **Bendahara Pengeluaran** - Mengelola semua dokumen tanpa scope bidang
4. **Bendahara Pengeluaran Pembantu** - Mengelola dokumen dengan scope bidang
5. **Sekretariat** - Hanya akses rekapitulasi

## Instalasi dan Setup

### 1. Package Installation
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### 2. Model Configuration
Model `User` sudah dikonfigurasi dengan trait `HasRoles`:
```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    // ...
}
```

### 3. Seeding Roles dan Permissions
```bash
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=AssignDefaultRolesSeeder
```

## Roles dan Permissions

### Roles
- `super-admin` - Super Admin
- `admin` - Admin
- `bendahara-pengeluaran` - Bendahara Pengeluaran
- `bendahara-pengeluaran-pembantu` - Bendahara Pengeluaran Pembantu
- `sekretariat` - Sekretariat

### Permissions
#### Master Data
- `master-data.view` - Melihat master data
- `master-data.create` - Membuat master data
- `master-data.edit` - Mengedit master data
- `master-data.delete` - Menghapus master data

#### User Management
- `users.view` - Melihat user
- `users.create` - Membuat user
- `users.edit` - Mengedit user
- `users.delete` - Menghapus user

#### Document Management
- `documents.view` - Melihat dokumen
- `documents.create` - Membuat dokumen
- `documents.edit` - Mengedit dokumen
- `documents.delete` - Menghapus dokumen
- `documents.approve` - Menyetujui dokumen

#### Specific Documents
- `nota-dinas.*` - Nota Dinas permissions
- `spt.*` - SPT permissions
- `sppd.*` - SPPD permissions
- `receipts.*` - Receipts permissions
- `trip-reports.*` - Trip Reports permissions

#### Rekapitulasi
- `rekap.view` - Melihat rekapitulasi
- `rekap.export` - Mengekspor rekapitulasi

#### Reference Rates
- `reference-rates.*` - Reference rates permissions

#### Locations
- `locations.*` - Location permissions

## Middleware

### 1. CheckPermission Middleware
```php
// Usage in routes
Route::middleware(['auth', 'permission:nota-dinas.view'])->group(function () {
    Route::get('/nota-dinas', [NotaDinasController::class, 'index']);
});
```

### 2. CheckUnitScope Middleware
```php
// Usage in routes
Route::middleware(['auth', 'unit.scope'])->group(function () {
    Route::get('/documents', [DocumentController::class, 'index']);
});
```

## Helper Class

### PermissionHelper
Class helper untuk permission checks:

```php
use App\Helpers\PermissionHelper;

// Check single permission
if (PermissionHelper::can('nota-dinas.view')) {
    // User can view nota dinas
}

// Check multiple permissions (any)
if (PermissionHelper::canAny(['documents.create', 'documents.edit'])) {
    // User can create OR edit documents
}

// Check multiple permissions (all)
if (PermissionHelper::canAll(['rekap.view', 'rekap.export'])) {
    // User can view AND export rekap
}

// Check role
if (PermissionHelper::hasRole('admin')) {
    // User has admin role
}

// Check if user can access all data (no unit scope)
if (PermissionHelper::canAccessAllData()) {
    // Super admin, admin, or bendahara pengeluaran
}

// Get user's unit ID for scope filtering
$userUnitId = PermissionHelper::getUserUnitId();
```

## Controller Implementation

### Basic Permission Check
```php
public function index(Request $request)
{
    if (!PermissionHelper::can('nota-dinas.view')) {
        abort(403, 'Anda tidak memiliki izin untuk melihat nota dinas.');
    }

    $query = NotaDinas::with(['participants.user', 'requestingUnit']);

    // Apply unit scope filtering
    if (!PermissionHelper::canAccessAllData()) {
        $userUnitId = PermissionHelper::getUserUnitId();
        if ($userUnitId) {
            $query->whereHas('participants', function ($q) use ($userUnitId) {
                $q->where('unit_id', $userUnitId);
            });
        }
    }

    $notaDinas = $query->orderBy('created_at', 'desc')->paginate(15);
    return view('nota-dinas.index', compact('notaDinas'));
}
```

### Unit Scope Check
```php
public function edit(NotaDinas $notaDinas)
{
    if (!PermissionHelper::can('nota-dinas.edit')) {
        abort(403, 'Anda tidak memiliki izin untuk mengedit nota dinas.');
    }

    // Check unit scope for bendahara pengeluaran pembantu
    if (!PermissionHelper::canAccessAllData()) {
        $userUnitId = PermissionHelper::getUserUnitId();
        if ($userUnitId) {
            $hasAccess = $notaDinas->participants()
                ->where('unit_id', $userUnitId)
                ->exists();
            
            if (!$hasAccess) {
                abort(403, 'Anda hanya dapat mengedit nota dinas dari bidang Anda.');
            }
        }
    }

    return view('nota-dinas.edit', compact('notaDinas'));
}
```

## Route Implementation

### Permission-based Routes
```php
// Single permission
Route::middleware(['auth', 'permission:nota-dinas.view'])->group(function () {
    Route::get('/nota-dinas', [NotaDinasController::class, 'index']);
});

// Multiple permissions (any)
Route::middleware(['auth'])->group(function () {
    Route::get('/documents/manage', function () {
        if (!auth()->user()->canAny(['documents.create', 'documents.edit'])) {
            abort(403, 'Anda tidak memiliki izin untuk mengelola dokumen.');
        }
        return view('documents.manage');
    });
});

// Role-based access
Route::middleware(['auth'])->group(function () {
    Route::get('/admin-panel', function () {
        if (!auth()->user()->hasAnyRole(['super-admin', 'admin'])) {
            abort(403, 'Hanya admin yang dapat mengakses panel ini.');
        }
        return view('admin.panel');
    });
});
```

## View Implementation

### Conditional Access in Blade
```blade
{{-- Check permission --}}
@if(\App\Helpers\PermissionHelper::can('nota-dinas.create'))
    <a href="{{ route('nota-dinas.create') }}" class="btn btn-primary">
        Buat Nota Dinas
    </a>
@endif

{{-- Check role --}}
@if(\App\Helpers\PermissionHelper::hasRole('admin'))
    <div class="admin-panel">
        <!-- Admin only content -->
    </div>
@endif

{{-- Check multiple permissions --}}
@if(\App\Helpers\PermissionHelper::canAny(['documents.create', 'documents.edit']))
    <div class="document-actions">
        <!-- Document management actions -->
    </div>
@endif

{{-- User role information --}}
<div class="user-info">
    <p>Role: {{ \App\Helpers\PermissionHelper::getUserRoleDisplayName() }}</p>
    <p>Unit: {{ auth()->user()->unit->name ?? 'Tidak ada unit' }}</p>
</div>
```

## Unit Scope Filtering

### For Bendahara Pengeluaran Pembantu and Sekretariat
```php
// In controller
$query = Model::query();

if (!PermissionHelper::canAccessAllData()) {
    $userUnitId = PermissionHelper::getUserUnitId();
    if ($userUnitId) {
        $query->whereHas('participants', function ($q) use ($userUnitId) {
            $q->where('unit_id', $userUnitId);
        });
    }
}
```

## User Management

### Assign Role to User
```php
$user = User::find(1);
$user->assignRole('admin');
```

### Remove Role from User
```php
$user->removeRole('admin');
```

### Check User Role
```php
if ($user->hasRole('admin')) {
    // User has admin role
}

if ($user->hasAnyRole(['admin', 'super-admin'])) {
    // User has admin or super-admin role
}
```

### Check User Permission
```php
if ($user->can('nota-dinas.view')) {
    // User can view nota dinas
}

if ($user->canAny(['documents.create', 'documents.edit'])) {
    // User can create or edit documents
}
```

## Best Practices

### 1. Always Check Permissions
- Gunakan permission checks di controller methods
- Gunakan middleware untuk route protection
- Gunakan helper class untuk consistency

### 2. Unit Scope Filtering
- Selalu terapkan unit scope filtering untuk bendahara pengeluaran pembantu
- Gunakan `PermissionHelper::canAccessAllData()` untuk check
- Gunakan `PermissionHelper::getUserUnitId()` untuk filtering

### 3. Error Handling
- Gunakan `abort(403, 'message')` untuk permission denied
- Berikan pesan error yang jelas dan informatif
- Log permission violations untuk security monitoring

### 4. Performance
- Cache permissions jika diperlukan
- Gunakan eager loading untuk relationships
- Optimize queries dengan proper indexing

## Security Considerations

### 1. Permission Validation
- Selalu validasi permissions di server-side
- Jangan hanya mengandalkan frontend validation
- Gunakan middleware untuk route protection

### 2. Data Access Control
- Implementasikan unit scope filtering dengan benar
- Validasi ownership sebelum edit/delete operations
- Gunakan proper authorization policies jika diperlukan

### 3. Role Management
- Hanya super admin yang dapat mengelola roles
- Implementasikan audit trail untuk role changes
- Regular review dan cleanup unused permissions

## Troubleshooting

### Common Issues

1. **Permission not working**
   - Check if user has the role
   - Check if role has the permission
   - Clear permission cache: `php artisan permission:cache-reset`

2. **Unit scope not working**
   - Check if user has unit_id
   - Verify middleware is applied
   - Check PermissionHelper::getUserUnitId() return value

3. **Role assignment not working**
   - Check if role exists
   - Verify user model has HasRoles trait
   - Check database relationships

### Debug Commands
```bash
# Clear permission cache
php artisan permission:cache-reset

# Show user roles and permissions
php artisan tinker
>>> $user = User::find(1);
>>> $user->getRoleNames();
>>> $user->getAllPermissions()->pluck('name');
```

## Migration dari Sistem Lama

Jika ada sistem roles/permissions lama:

1. Backup data user yang ada
2. Run migration untuk Spatie tables
3. Run seeder untuk roles dan permissions
4. Assign default roles ke user yang ada
5. Update controllers dan views
6. Test semua functionality

## Monitoring dan Maintenance

### Regular Tasks
- Review user roles dan permissions
- Cleanup unused permissions
- Monitor permission violations
- Update roles sesuai kebutuhan bisnis

### Logging
- Log permission violations
- Track role changes
- Monitor data access patterns
- Security audit trails
