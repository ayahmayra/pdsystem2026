# Panduan Location & Routes Permissions

## Overview

Sistem telah diperluas untuk mengelola permissions akses ke semua submenu Lokasi & Rute. Setiap submenu di halaman Lokasi & Rute sekarang memiliki permission yang dapat dikelola melalui role management.

## Location & Routes Submenu Permissions

### 1. **Submenu Permissions yang Tersedia**

```php
'menu.provinces'        // Akses Menu Data Provinsi
'menu.cities'          // Akses Menu Data Kota/Kabupaten
'menu.districts'       // Akses Menu Data Kecamatan
'menu.org-places'      // Akses Menu Data Kedudukan
'menu.transport-modes' // Akses Menu Data Moda Transportasi
'menu.travel-routes'   // Akses Menu Data Rute Perjalanan
```

### 2. **Default Submenu Permissions per Role**

#### **A. Super Admin**
```php
[
    'menu.provinces', 'menu.cities', 'menu.districts', 
    'menu.org-places', 'menu.transport-modes', 'menu.travel-routes'
]
```
- ✅ Akses ke semua submenu Lokasi & Rute

#### **B. Admin**
```php
[
    'menu.provinces', 'menu.cities', 'menu.districts', 
    'menu.org-places', 'menu.transport-modes', 'menu.travel-routes'
]
```
- ✅ Akses ke semua submenu Lokasi & Rute

#### **C. Bendahara Pengeluaran**
```php
[]
```
- ❌ Tidak ada akses ke submenu Lokasi & Rute

#### **D. Bendahara Pengeluaran Pembantu**
```php
[]
```
- ❌ Tidak ada akses ke submenu Lokasi & Rute

#### **E. Sekretariat**
```php
[]
```
- ❌ Tidak ada akses ke submenu Lokasi & Rute

## Cara Kerja

### 1. **Location & Routes Menu Checking**

Setiap submenu di halaman Lokasi & Rute menggunakan permission checking:

```blade
@if(\App\Helpers\PermissionHelper::can('menu.provinces'))
<div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                <!-- Icon -->
            </div>
        </div>
        <div class="ml-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                <a href="{{ route('provinces.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                    Data Provinsi
                </a>
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Kelola data provinsi di Indonesia
            </p>
        </div>
    </div>
</div>
@endif
```

### 2. **Submenu yang Dikontrol**

#### **A. Data Provinsi**
- **Permission**: `menu.provinces`
- **Route**: `provinces.index`
- **Description**: Kelola data provinsi di Indonesia

#### **B. Data Kota/Kabupaten**
- **Permission**: `menu.cities`
- **Route**: `cities.index`
- **Description**: Kelola data kota dan kabupaten

#### **C. Data Kecamatan**
- **Permission**: `menu.districts`
- **Route**: `districts.index`
- **Description**: Kelola data kecamatan

#### **D. Data Kedudukan**
- **Permission**: `menu.org-places`
- **Route**: `org-places.index`
- **Description**: Kelola data kedudukan organisasi

#### **E. Data Moda Transportasi**
- **Permission**: `menu.transport-modes`
- **Route**: `transport-modes.index`
- **Description**: Kelola data moda transportasi

#### **F. Data Rute Perjalanan**
- **Permission**: `menu.travel-routes`
- **Route**: `travel-routes.index`
- **Description**: Kelola data rute perjalanan

### 3. **Role Management Interface**

Submenu permissions ditampilkan di role management dengan display names yang user-friendly:

- `menu.provinces` → "Akses Menu Data Provinsi"
- `menu.cities` → "Akses Menu Data Kota/Kabupaten"
- `menu.districts` → "Akses Menu Data Kecamatan"
- `menu.org-places` → "Akses Menu Data Kedudukan"
- `menu.transport-modes` → "Akses Menu Data Moda Transportasi"
- `menu.travel-routes` → "Akses Menu Data Rute Perjalanan"

## Testing Results

### 1. **Super Admin Test**
```bash
User: H. AREADY (Super Admin)
Can access provinces: Yes ✅
Can access cities: Yes ✅
Can access districts: Yes ✅
Can access org-places: Yes ✅
Can access transport-modes: Yes ✅
Can access travel-routes: Yes ✅
```

### 2. **Admin Test**
```bash
User: admin (User 54)
Can access provinces: Yes ✅
Can access cities: Yes ✅
Can access districts: Yes ✅
Can access org-places: Yes ✅
Can access transport-modes: Yes ✅
Can access travel-routes: Yes ✅
```

## Cara Menggunakan

### 1. **Kelola Submenu Permissions**

1. Login sebagai super-admin
2. Buka Master Data → Manajemen Role
3. Pilih role yang akan dikelola
4. Klik "Kelola Permissions"
5. Centang/hilangkan submenu permissions yang diinginkan
6. Klik "Simpan Permissions"

### 2. **Submenu Permissions di Interface**

Submenu permissions ditampilkan dalam grup "Menu Access" di role management interface:

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
├── ☑️ Akses Menu Riwayat Nomor Dokumen
├── ☑️ Akses Menu Data Provinsi
├── ☑️ Akses Menu Data Kota/Kabupaten
├── ☑️ Akses Menu Data Kecamatan
├── ☑️ Akses Menu Data Kedudukan
├── ☑️ Akses Menu Data Moda Transportasi
└── ☑️ Akses Menu Data Rute Perjalanan
```

### 3. **Aksi Cepat**

- **Reset ke Default**: Mengembalikan submenu permissions ke default role
- **Toggle All**: Toggle semua submenu permissions dalam grup
- **Hapus Semua**: Menghapus semua submenu permissions
- **Pilih Semua**: Memilih semua submenu permissions

## Files yang Dimodifikasi

### 1. **Modified Files**
- `database/seeders/MenuPermissionsSeeder.php` - Added location-routes submenu permissions
- `app/Livewire/Roles/ManageRolePermissions.php` - Added submenu permissions to default permissions dan display names
- `resources/views/livewire/location-routes/index.blade.php` - Added permission checking untuk setiap submenu

## Keuntungan

### 1. **Granular Control**
- Kontrol akses yang sangat spesifik untuk setiap submenu Lokasi & Rute
- User hanya melihat submenu yang diizinkan
- Interface yang lebih bersih dan focused

### 2. **Security Enhancement**
- Mencegah akses tidak sah ke submenu tertentu
- Role-based submenu access control
- Consistent dengan permission system

### 3. **User Experience**
- Interface yang lebih sederhana untuk user
- Submenu yang relevan dengan role user
- Reduced cognitive load

### 4. **Flexibility**
- Mudah mengubah submenu access per role
- Scalable untuk submenu baru
- Centralized management

## Best Practices

### 1. **Submenu Permission Assignment**
- Berikan submenu permissions minimal yang diperlukan
- Sesuaikan dengan job function role
- Test submenu access setelah perubahan

### 2. **Role Design**
- Design role dengan submenu access yang konsisten
- Hindari role yang terlalu restrictive
- Consider user workflow requirements

### 3. **Testing**
- Test submenu access dengan berbagai role
- Verify location-routes page rendering dengan permissions
- Check navigation flow

## Troubleshooting

### 1. **Submenu Tidak Muncul**
- Cek apakah role memiliki submenu permission yang sesuai
- Cek apakah permission sudah di-assign ke role
- Test dengan super-admin untuk memastikan submenu ada

### 2. **Permission Tidak Tersimpan**
- Cek apakah submenu permissions sudah ada di database
- Jalankan `MenuPermissionsSeeder` jika diperlukan
- Cek role permissions di database

### 3. **Location-Routes Page Rendering Issues**
- Cek syntax Blade di location-routes page
- Cek apakah `PermissionHelper::can()` berfungsi
- Test dengan user yang memiliki permissions

## Migration Guide

### 1. **Untuk Existing Roles**
1. Jalankan `MenuPermissionsSeeder`
2. Assign submenu permissions ke role yang ada
3. Test submenu access dengan berbagai role

### 2. **Untuk New Roles**
1. Gunakan default permissions yang sudah include submenu permissions
2. Customize submenu permissions sesuai kebutuhan
3. Test dan verify submenu access

## Kesimpulan

Sistem submenu permissions memberikan kontrol granular untuk akses submenu di halaman Lokasi & Rute. Setiap role dapat memiliki akses submenu yang berbeda, membuat interface lebih focused dan secure.

**Sistem ini meningkatkan security dan user experience dengan submenu access control yang fleksibel!** ✅

## Submenu yang Dikontrol

- ✅ **Data Provinsi** - Kelola data provinsi di Indonesia
- ✅ **Data Kota/Kabupaten** - Kelola data kota dan kabupaten
- ✅ **Data Kecamatan** - Kelola data kecamatan
- ✅ **Data Kedudukan** - Kelola data kedudukan organisasi
- ✅ **Data Moda Transportasi** - Kelola data moda transportasi
- ✅ **Data Rute Perjalanan** - Kelola data rute perjalanan
