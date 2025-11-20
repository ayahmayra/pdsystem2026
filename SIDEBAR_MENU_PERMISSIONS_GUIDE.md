# Panduan Sidebar Menu Permissions

## Overview

Sistem telah diperluas untuk mengelola permissions akses ke menu di sidebar. Setiap menu di sidebar sekarang memiliki permission yang dapat dikelola melalui role management.

## Menu Permissions

### 1. **Menu Permissions yang Tersedia**

```php
'menu.dashboard'        // Akses Dashboard
'menu.documents'        // Akses Menu Dokumen
'menu.master-data'      // Akses Menu Master Data
'menu.location-routes'  // Akses Menu Lokasi & Rute
'menu.reference-rates'  // Akses Menu Referensi Tarif
'menu.rekap'           // Akses Menu Rekap
'menu.configuration'   // Akses Menu Konfigurasi
'menu.organization'    // Akses Menu Organisasi
'menu.ranks'           // Akses Menu Data Pangkat
'menu.doc-number-formats' // Akses Menu Format Penomoran Dokumen
'menu.number-sequences'   // Akses Menu Number Sequence
'menu.document-numbers'   // Akses Menu Riwayat Nomor Dokumen
```

### 2. **Default Menu Permissions per Role**

#### **A. Super Admin**
```php
[
    'menu.dashboard', 'menu.documents', 'menu.master-data', 
    'menu.location-routes', 'menu.reference-rates', 'menu.rekap',
    'menu.configuration', 'menu.organization', 'menu.ranks', 
    'menu.doc-number-formats', 'menu.number-sequences', 'menu.document-numbers'
]
```
- ✅ Akses ke semua menu

#### **B. Admin**
```php
[
    'menu.dashboard', 'menu.master-data', 'menu.reference-rates', 'menu.rekap',
    'menu.configuration', 'menu.organization', 'menu.ranks', 
    'menu.doc-number-formats', 'menu.number-sequences', 'menu.document-numbers'
]
```
- ✅ Dashboard, Master Data, Referensi Tarif, Rekap, Konfigurasi
- ❌ Dokumen, Lokasi & Rute

#### **C. Bendahara Pengeluaran**
```php
[
    'menu.dashboard', 'menu.documents', 'menu.rekap'
]
```
- ✅ Dashboard, Dokumen, Rekap
- ❌ Master Data, Lokasi & Rute, Referensi Tarif

#### **D. Bendahara Pengeluaran Pembantu**
```php
[
    'menu.dashboard', 'menu.documents', 'menu.rekap'
]
```
- ✅ Dashboard, Dokumen, Rekap
- ❌ Master Data, Lokasi & Rute, Referensi Tarif

#### **E. Sekretariat**
```php
[
    'menu.dashboard', 'menu.rekap'
]
```
- ✅ Dashboard, Rekap
- ❌ Dokumen, Master Data, Lokasi & Rute, Referensi Tarif, Konfigurasi

## Cara Kerja

### 1. **Sidebar Menu Checking**

Setiap menu di sidebar sekarang menggunakan permission checking:

```blade
@if(\App\Helpers\PermissionHelper::can('menu.dashboard'))
<flux:navlist.group :heading="__('Platform')" class="grid">
    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
</flux:navlist.group>
@endif
```

### 2. **Menu yang Dikontrol**

#### **A. Platform Menu**
- **Dashboard**: `menu.dashboard`

#### **B. Dokumen Menu**
- **Dokumen**: `menu.documents`

#### **C. Master Data Menu**
- **Master Data**: `menu.master-data`

#### **D. Reference Menu**
- **Ref Lokasi & Rute**: `menu.location-routes`
- **Referensi Tarif**: `menu.reference-rates`

#### **E. Rekap Menu**
- **Rekapitulasi**: `menu.rekap`

#### **F. Configuration Menu**
- **Configuration**: `menu.configuration`
- **Organisasi**: `menu.organization`
- **Data Pangkat**: `menu.ranks`
- **Format Penomoran Dokumen**: `menu.doc-number-formats`
- **Number Sequence**: `menu.number-sequences`
- **Riwayat Nomor Dokumen**: `menu.document-numbers`

### 3. **Role Management Interface**

Menu permissions ditampilkan di role management dengan display names yang user-friendly:

- `menu.dashboard` → "Akses Dashboard"
- `menu.documents` → "Akses Menu Dokumen"
- `menu.master-data` → "Akses Menu Master Data"
- `menu.location-routes` → "Akses Menu Lokasi & Rute"
- `menu.reference-rates` → "Akses Menu Referensi Tarif"
- `menu.rekap` → "Akses Menu Rekap"
- `menu.configuration` → "Akses Menu Konfigurasi"
- `menu.organization` → "Akses Menu Organisasi"
- `menu.ranks` → "Akses Menu Data Pangkat"
- `menu.doc-number-formats` → "Akses Menu Format Penomoran Dokumen"
- `menu.number-sequences` → "Akses Menu Number Sequence"
- `menu.document-numbers` → "Akses Menu Riwayat Nomor Dokumen"

## Testing Results

### 1. **Super Admin Test**
```bash
User: H. AREADY (Super Admin)
Can access dashboard: Yes ✅
Can access documents: Yes ✅
Can access master-data: Yes ✅
Can access location-routes: Yes ✅
Can access reference-rates: Yes ✅
Can access rekap: Yes ✅
Can access configuration: Yes ✅
Can access organization: Yes ✅
Can access ranks: Yes ✅
Can access doc-number-formats: Yes ✅
Can access number-sequences: Yes ✅
Can access document-numbers: Yes ✅
```

### 2. **Admin Test**
```bash
User: admin (User 54)
Can access dashboard: Yes ✅
Can access documents: No ❌
Can access master-data: Yes ✅
Can access location-routes: No ❌
Can access reference-rates: Yes ✅
Can access rekap: Yes ✅
Can access configuration: Yes ✅
Can access organization: Yes ✅
Can access ranks: Yes ✅
Can access doc-number-formats: Yes ✅
Can access number-sequences: Yes ✅
Can access document-numbers: Yes ✅
```

## Cara Menggunakan

### 1. **Kelola Menu Permissions**

1. Login sebagai super-admin
2. Buka Master Data → Manajemen Role
3. Pilih role yang akan dikelola
4. Klik "Kelola Permissions"
5. Centang/hilangkan menu permissions yang diinginkan
6. Klik "Simpan Permissions"

### 2. **Menu Permissions di Interface**

Menu permissions ditampilkan dalam grup "Menu Access" di role management interface:

```
Menu Access
├── ☑️ Akses Dashboard
├── ☑️ Akses Menu Dokumen
├── ☑️ Akses Menu Master Data
├── ☑️ Akses Menu Lokasi & Rute
├── ☑️ Akses Menu Referensi Tarif
├── ☑️ Akses Menu Rekap
├── ☑️ Akses Menu Konfigurasi
├── ☑️ Akses Menu Organisasi
├── ☑️ Akses Menu Data Pangkat
├── ☑️ Akses Menu Format Penomoran Dokumen
├── ☑️ Akses Menu Number Sequence
└── ☑️ Akses Menu Riwayat Nomor Dokumen
```

### 3. **Aksi Cepat**

- **Reset ke Default**: Mengembalikan menu permissions ke default role
- **Toggle All**: Toggle semua menu permissions dalam grup
- **Hapus Semua**: Menghapus semua menu permissions
- **Pilih Semua**: Memilih semua menu permissions

## Files yang Dimodifikasi

### 1. **New Files**
- `database/seeders/MenuPermissionsSeeder.php` - Seeder untuk menu permissions

### 2. **Modified Files**
- `app/Livewire/Roles/ManageRolePermissions.php` - Added menu permissions to default permissions and display names
- `resources/views/components/layouts/app/sidebar.blade.php` - Added permission checking for menu items

## Keuntungan

### 1. **Granular Control**
- Kontrol akses yang sangat spesifik untuk setiap menu
- User hanya melihat menu yang diizinkan
- Interface yang lebih bersih dan focused

### 2. **Security Enhancement**
- Mencegah akses tidak sah ke menu tertentu
- Role-based menu access control
- Consistent dengan permission system

### 3. **User Experience**
- Interface yang lebih sederhana untuk user
- Menu yang relevan dengan role user
- Reduced cognitive load

### 4. **Flexibility**
- Mudah mengubah menu access per role
- Scalable untuk menu baru
- Centralized management

## Best Practices

### 1. **Menu Permission Assignment**
- Berikan menu permissions minimal yang diperlukan
- Sesuaikan dengan job function role
- Test menu access setelah perubahan

### 2. **Role Design**
- Design role dengan menu access yang konsisten
- Hindari role yang terlalu restrictive
- Consider user workflow requirements

### 3. **Testing**
- Test menu access dengan berbagai role
- Verify sidebar rendering dengan permissions
- Check navigation flow

## Troubleshooting

### 1. **Menu Tidak Muncul**
- Cek apakah role memiliki menu permission yang sesuai
- Cek apakah permission sudah di-assign ke role
- Test dengan super-admin untuk memastikan menu ada

### 2. **Permission Tidak Tersimpan**
- Cek apakah menu permissions sudah ada di database
- Jalankan `MenuPermissionsSeeder` jika diperlukan
- Cek role permissions di database

### 3. **Sidebar Rendering Issues**
- Cek syntax Blade di sidebar
- Cek apakah `PermissionHelper::can()` berfungsi
- Test dengan user yang memiliki permissions

## Migration Guide

### 1. **Untuk Existing Roles**
1. Jalankan `MenuPermissionsSeeder`
2. Assign menu permissions ke role yang ada
3. Test menu access dengan berbagai role

### 2. **Untuk New Roles**
1. Gunakan default permissions yang sudah include menu permissions
2. Customize menu permissions sesuai kebutuhan
3. Test dan verify menu access

## Kesimpulan

Sistem menu permissions memberikan kontrol granular untuk akses menu di sidebar. Setiap role dapat memiliki akses menu yang berbeda, membuat interface lebih focused dan secure.

**Sistem ini meningkatkan security dan user experience dengan menu access control yang fleksibel!** ✅
