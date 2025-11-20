# Struktur Tabel Roles dan Permissions

## Overview

Sistem roles dan permissions menggunakan **Spatie Laravel Permission** yang terdiri dari 5 tabel utama:

## 1. Tabel `roles`
**Fungsi**: Menyimpan data roles yang tersedia dalam sistem

**Struktur**:
- `id` (bigint, primary key)
- `name` (varchar, unique) - Nama role (contoh: 'super-admin', 'admin')
- `guard_name` (varchar) - Guard yang digunakan (default: 'web')
- `created_at` (timestamp)
- `updated_at` (timestamp)

**Data yang ada**:
```
ID: 1 | Name: super-admin | Guard: web
ID: 2 | Name: admin | Guard: web
ID: 3 | Name: bendahara-pengeluaran | Guard: web
ID: 4 | Name: bendahara-pengeluaran-pembantu | Guard: web
ID: 5 | Name: sekretariat | Guard: web
```

## 2. Tabel `permissions`
**Fungsi**: Menyimpan data permissions yang tersedia dalam sistem

**Struktur**:
- `id` (bigint, primary key)
- `name` (varchar, unique) - Nama permission (contoh: 'nota-dinas.view')
- `guard_name` (varchar) - Guard yang digunakan (default: 'web')
- `created_at` (timestamp)
- `updated_at` (timestamp)

**Data yang ada**:
```
ID: 1 | Name: master-data.view | Guard: web
ID: 2 | Name: master-data.create | Guard: web
ID: 3 | Name: master-data.edit | Guard: web
ID: 4 | Name: master-data.delete | Guard: web
ID: 5 | Name: users.view | Guard: web
... (dan seterusnya)
```

## 3. Tabel `model_has_roles` ⭐
**Fungsi**: **TABEL UTAMA** yang menentukan role seorang user

**Struktur**:
- `role_id` (bigint, foreign key ke tabel `roles`)
- `model_type` (varchar) - Tipe model (default: 'App\\Models\\User')
- `model_id` (bigint) - ID user dari tabel `users`

**Data yang ada**:
```
User: H. AREADY (197503022002121004@gmail.com) | Role: super-admin
User: HERMANSYAH (hermansyah_@live.com) | Role: admin
User: Ahmad Rizki (ahmad.rizki@perjadin.test) | Role: bendahara-pengeluaran
User: Maya Sari (maya.sari@perjadin.test) | Role: bendahara-pengeluaran-pembantu
User: Joko Widodo (joko.widodo@perjadin.test) | Role: sekretariat
```

## 4. Tabel `role_has_permissions`
**Fungsi**: Menghubungkan roles dengan permissions (menentukan permission apa saja yang dimiliki oleh setiap role)

**Struktur**:
- `permission_id` (bigint, foreign key ke tabel `permissions`)
- `role_id` (bigint, foreign key ke tabel `roles`)

## 5. Tabel `model_has_permissions` (Opsional)
**Fungsi**: Memberikan permission langsung ke user tanpa melalui role (jarang digunakan)

**Struktur**:
- `permission_id` (bigint, foreign key ke tabel `permissions`)
- `model_type` (varchar) - Tipe model (default: 'App\\Models\\User')
- `model_id` (bigint) - ID user dari tabel `users`

## Cara Kerja Sistem

### 1. **Menentukan Role User**
Role seorang user ditentukan oleh data di tabel **`model_has_roles`**:
```sql
SELECT r.name as role_name 
FROM model_has_roles mhr
JOIN roles r ON mhr.role_id = r.id
WHERE mhr.model_id = [USER_ID] AND mhr.model_type = 'App\\Models\\User'
```

### 2. **Menentukan Permissions User**
Permissions user ditentukan oleh:
1. **Permissions dari role** (melalui `role_has_permissions`)
2. **Permissions langsung** (melalui `model_has_permissions` - opsional)

```sql
-- Permissions dari role
SELECT DISTINCT p.name as permission_name
FROM model_has_roles mhr
JOIN role_has_permissions rhp ON mhr.role_id = rhp.role_id
JOIN permissions p ON rhp.permission_id = p.id
WHERE mhr.model_id = [USER_ID] AND mhr.model_type = 'App\\Models\\User'

-- Permissions langsung (jika ada)
UNION
SELECT p.name as permission_name
FROM model_has_permissions mhp
JOIN permissions p ON mhp.permission_id = p.id
WHERE mhp.model_id = [USER_ID] AND mhp.model_type = 'App\\Models\\User'
```

## Contoh Query untuk Mengetahui Role dan Permissions User

### Query untuk melihat role user:
```sql
SELECT 
    u.name as user_name,
    u.email,
    r.name as role_name
FROM users u
JOIN model_has_roles mhr ON u.id = mhr.model_id
JOIN roles r ON mhr.role_id = r.id
WHERE u.id = [USER_ID];
```

### Query untuk melihat permissions user:
```sql
SELECT DISTINCT p.name as permission_name
FROM users u
JOIN model_has_roles mhr ON u.id = mhr.model_id
JOIN role_has_permissions rhp ON mhr.role_id = rhp.role_id
JOIN permissions p ON rhp.permission_id = p.id
WHERE u.id = [USER_ID];
```

## Cara Mengelola Roles dan Permissions

### 1. **Assign Role ke User**
```php
$user = User::find(1);
$user->assignRole('admin');
```

### 2. **Remove Role dari User**
```php
$user = User::find(1);
$user->removeRole('admin');
```

### 3. **Check Role User**
```php
$user = User::find(1);
if ($user->hasRole('admin')) {
    // User memiliki role admin
}
```

### 4. **Check Permission User**
```php
$user = User::find(1);
if ($user->can('nota-dinas.view')) {
    // User memiliki permission nota-dinas.view
}
```

### 5. **Give Permission ke Role**
```php
$role = Role::findByName('admin');
$role->givePermissionTo('nota-dinas.view');
```

### 6. **Revoke Permission dari Role**
```php
$role = Role::findByName('admin');
$role->revokePermissionTo('nota-dinas.view');
```

## Unit Scope Filtering

Selain roles dan permissions, sistem juga menggunakan **unit scope filtering** berdasarkan:
- Tabel `users.unit_id` - Menentukan unit/bidang user
- Logic di `PermissionHelper::canAccessAllData()` - Menentukan apakah user dapat akses semua data

**User yang dapat akses semua data** (tidak ada unit scope restriction):
- Super Admin
- Admin  
- Bendahara Pengeluaran

**User yang dibatasi berdasarkan unit**:
- Bendahara Pengeluaran Pembantu
- Sekretariat

## Kesimpulan

**Tabel yang menentukan role seorang user adalah `model_has_roles`** dengan struktur:
- `role_id` → Menunjuk ke role apa
- `model_id` → Menunjuk ke user mana
- `model_type` → Tipe model (biasanya 'App\\Models\\User')

Sistem ini memungkinkan:
1. **Flexible role assignment** - User bisa memiliki multiple roles
2. **Permission inheritance** - Permissions diwariskan dari roles
3. **Direct permission assignment** - Bisa memberikan permission langsung ke user
4. **Unit-based access control** - Kontrol akses berdasarkan unit/bidang
