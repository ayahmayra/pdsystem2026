# Panduan Proteksi Superadmin

## Overview
Sistem telah diimplementasikan dengan proteksi khusus untuk user dengan role `super-admin`. User superadmin hanya dapat diubah atau dihapus oleh superadmin lainnya, bukan oleh admin biasa.

## Fitur yang Diimplementasikan

### 1. **Model User - Method Baru**
- `isSuperAdmin()`: Mengecek apakah user memiliki role super-admin
- `canBeModifiedBy(User $currentUser)`: Mengecek apakah user dapat dimodifikasi oleh user tertentu

### 2. **Proteksi Penghapusan User**
- **File**: `app/Livewire/Users/Index.php`
- **Method**: `delete()`
- **Proteksi**: Mencegah penghapusan superadmin oleh non-superadmin
- **Pesan Error**: "User dengan role superadmin hanya dapat dihapus oleh superadmin lainnya."

### 3. **Proteksi Edit User**
- **File**: `app/Livewire/Users/Edit.php`
- **Methods**: `mount()` dan `update()`
- **Proteksi**: Mencegah akses dan edit superadmin oleh non-superadmin
- **Response**: HTTP 403 Forbidden atau flash error message

### 4. **UI Protection**
- **File**: `resources/views/livewire/users/index.blade.php`
- **Fitur**: Menyembunyikan tombol aksi untuk superadmin dari non-superadmin
- **Visual**: Menampilkan icon kunci dengan teks "Terkunci"

## Logika Proteksi

### Kondisi yang Dilindungi
```php
// User superadmin hanya bisa diubah oleh superadmin lainnya
if ($user->isSuperAdmin() && !$currentUser->isSuperAdmin()) {
    // Blok akses
}
```

### Level Proteksi
1. **Database Level**: Foreign key constraints tetap berlaku
2. **Application Level**: Validasi di controller/Livewire
3. **UI Level**: Tombol aksi disembunyikan
4. **Permission Level**: Role-based access control

## Testing Scenarios

### 1. **Superadmin → Superadmin**
- ✅ Dapat edit superadmin lain
- ✅ Dapat hapus superadmin lain
- ✅ Dapat akses semua fitur

### 2. **Admin → Superadmin**
- ❌ Tidak dapat edit superadmin
- ❌ Tidak dapat hapus superadmin
- ❌ Tombol aksi disembunyikan
- ❌ HTTP 403 jika akses langsung

### 3. **Admin → Admin/User Biasa**
- ✅ Dapat edit user biasa
- ✅ Dapat hapus user biasa (jika tidak terkait dokumen)
- ✅ Tombol aksi tersedia

## Error Messages

### Penghapusan
```
"User dengan role superadmin hanya dapat dihapus oleh superadmin lainnya."
```

### Edit
```
"User dengan role superadmin hanya dapat diubah oleh superadmin lainnya."
```

### UI
```
"User superadmin hanya dapat diubah oleh superadmin lainnya"
```

## Security Benefits

### 1. **Privilege Escalation Prevention**
- Mencegah admin biasa mengubah superadmin
- Mencegah penghapusan akun superadmin secara tidak sengaja

### 2. **Role Integrity**
- Memastikan hanya superadmin yang dapat mengelola sesama superadmin
- Mencegah downgrade role superadmin oleh admin biasa

### 3. **Audit Trail**
- Semua perubahan superadmin tercatat dengan jelas
- Hanya superadmin yang dapat melakukan perubahan

## Implementation Details

### Model User
```php
public function isSuperAdmin(): bool
{
    return $this->hasRole('super-admin');
}

public function canBeModifiedBy(User $currentUser): bool
{
    if ($this->isSuperAdmin()) {
        return $currentUser->isSuperAdmin();
    }
    
    return $currentUser->hasAnyRole(['admin', 'super-admin']);
}
```

### Livewire Protection
```php
// Di method delete()
if ($user->isSuperAdmin() && !Auth::user()->isSuperAdmin()) {
    session()->flash('error', 'User dengan role superadmin hanya dapat dihapus oleh superadmin lainnya.');
    return;
}

// Di method mount() dan update()
if ($user->isSuperAdmin() && !Auth::user()->isSuperAdmin()) {
    abort(403, 'User dengan role superadmin hanya dapat diubah oleh superadmin lainnya.');
}
```

### View Protection
```blade
@if($user->isSuperAdmin() && !Auth::user()->isSuperAdmin())
    <span class="text-gray-400 text-sm" title="User superadmin hanya dapat diubah oleh superadmin lainnya">
        <svg class="w-5 h-5 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
        </svg>
        Terkunci
    </span>
@else
    <!-- Tombol aksi normal -->
@endif
```

## Best Practices

### 1. **Superadmin Management**
- Selalu ada minimal 2 superadmin aktif
- Dokumentasikan semua superadmin
- Monitor aktivitas superadmin

### 2. **Role Assignment**
- Hanya superadmin yang dapat assign role super-admin
- Verifikasi identitas sebelum memberikan role super-admin
- Regular audit role assignments

### 3. **Security Monitoring**
- Monitor failed access attempts ke superadmin
- Log semua perubahan superadmin
- Alert jika ada perubahan role super-admin

## Troubleshooting

### Error: "User dengan role superadmin hanya dapat diubah oleh superadmin lainnya"
**Penyebab**: User yang login bukan superadmin mencoba mengubah superadmin
**Solusi**: Login sebagai superadmin untuk mengubah superadmin

### Error: "User dengan role superadmin hanya dapat dihapus oleh superadmin lainnya"
**Penyebab**: User yang login bukan superadmin mencoba menghapus superadmin
**Solusi**: Login sebagai superadmin untuk menghapus superadmin

### Tombol Aksi Tidak Muncul
**Penyebab**: User yang login bukan superadmin melihat superadmin
**Solusi**: Normal behavior - tombol disembunyikan untuk keamanan

## Maintenance

### Regular Tasks
1. **Audit superadmin list**: Pastikan hanya user yang berwenang yang memiliki role super-admin
2. **Monitor access logs**: Periksa siapa yang mencoba akses superadmin
3. **Update documentation**: Dokumentasikan perubahan superadmin

### Emergency Procedures
1. **Lost superadmin access**: Gunakan database langsung untuk assign role
2. **Compromised superadmin**: Segera hapus role dan buat superadmin baru
3. **System recovery**: Restore dari backup yang aman

## Conclusion

Proteksi superadmin telah diimplementasikan dengan multi-layer security:
- **Database level**: Foreign key constraints
- **Application level**: Business logic validation
- **UI level**: Visual indicators dan hidden controls
- **Permission level**: Role-based access control

Sistem ini memastikan bahwa hanya superadmin yang dapat mengelola sesama superadmin, mencegah privilege escalation dan menjaga integritas sistem.
