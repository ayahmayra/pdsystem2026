# Panduan Integrasi Logo Organisasi ke Halaman Auth

## Ringkasan Perubahan

Logo pada halaman autentikasi (Login, Register, Forgot Password) telah diperbarui untuk menggunakan logo organisasi yang dapat diatur melalui halaman **Organization Settings** (`/settings/organization`).

## Perubahan yang Dilakukan

### 1. Component Baru: `auth-logo.blade.php`

**File:** `resources/views/components/auth-logo.blade.php`

Component ini menampilkan logo organisasi dari database, dengan fallback ke logo default jika belum ada logo yang di-upload.

```php
@php
    $orgSettings = \App\Models\OrgSettings::getInstance();
    $logoPath = $orgSettings->logo_path;
@endphp

@if($logoPath && \Storage::disk('public')->exists($logoPath))
    <img src="{{ \Storage::url($logoPath) }}" alt="{{ $orgSettings->short_name ?: 'Logo' }}" {{ $attributes->merge(['class' => 'object-contain']) }} />
@else
    <x-app-logo-icon {{ $attributes }} />
@endif
```

**Fitur:**
- ✅ Mengambil logo dari `OrgSettings::getInstance()`
- ✅ Validasi keberadaan file di storage
- ✅ Fallback ke logo default (`app-logo-icon`) jika belum ada logo
- ✅ Support atribut dinamis untuk styling

### 2. Update Layout Auth Simple

**File:** `resources/views/components/layouts/auth/simple.blade.php`

**Perubahan:**
- Logo diperbesar dari `size-9` menjadi `size-16` (dari 36px ke 64px)
- Menggunakan component `<x-auth-logo />` yang dinamis

**Before:**
```html
<span class="flex h-9 w-9 mb-1 items-center justify-center rounded-md">
    <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
</span>
```

**After:**
```html
<span class="flex h-16 w-16 mb-1 items-center justify-center rounded-md">
    <x-auth-logo class="size-16 fill-current text-black dark:text-white" />
</span>
```

### 3. Update Layout Auth Card

**File:** `resources/views/components/layouts/auth/card.blade.php`

**Perubahan:**
- Logo diperbesar dari `size-9` menjadi `size-16`
- Menggunakan component `<x-auth-logo />` yang dinamis

### 4. Update Layout Auth Split

**File:** `resources/views/components/layouts/auth/split.blade.php`

**Perubahan:**
- Logo di sidebar (desktop) diperbesar dan menggunakan logo organisasi
- Logo di mobile view diperbesar dari `size-9` menjadi `size-16`
- Nama aplikasi menggunakan `short_name` dari organization settings

**Desktop View (Sidebar):**
```php
@php
    $orgSettings = \App\Models\OrgSettings::getInstance();
@endphp
<a href="{{ route('dashboard') }}" class="relative z-20 flex items-center text-lg font-medium" wire:navigate>
    <span class="flex h-12 w-12 items-center justify-center rounded-md me-2">
        <x-auth-logo class="h-10 fill-current text-white" />
    </span>
    {{ $orgSettings->short_name ?: config('app.name', 'Laravel') }}
</a>
```

**Mobile View:**
```html
<span class="flex h-16 w-16 items-center justify-center rounded-md">
    <x-auth-logo class="size-16 fill-current text-black dark:text-white" />
</span>
```

## Halaman yang Terpengaruh

✅ **Login** (`/login`)
- Logo organisasi ditampilkan di atas form login
- Ukuran: 64x64px

✅ **Register** (`/register`)
- Logo organisasi ditampilkan di atas form registrasi
- Ukuran: 64x64px

✅ **Forgot Password** (`/forgot-password`)
- Logo organisasi ditampilkan di atas form reset password
- Ukuran: 64x64px

✅ **Reset Password** (`/password/reset/*`)
- Menggunakan layout yang sama, logo otomatis terupdate

## Cara Mengatur Logo

### Untuk Admin:

1. Login ke sistem sebagai admin
2. Buka menu **Settings** → **Organization Settings**
3. Scroll ke bagian **Logo Aplikasi**
4. Upload file logo (format: JPG, PNG, max 2MB)
5. Klik **Simpan Pengaturan**

### Format Logo yang Disarankan:

- **Format:** PNG dengan background transparan (recommended) atau JPG
- **Ukuran:** Minimal 256x256px, maksimal 1024x1024px
- **Aspect Ratio:** Square (1:1) untuk hasil terbaik
- **Ukuran File:** Maksimal 2MB
- **Lokasi Storage:** `storage/app/public/logos/`

## Perilaku Logo

### Jika Logo Sudah Di-upload:
- Logo organisasi ditampilkan di semua halaman auth
- Logo menggunakan `object-contain` untuk menjaga proporsi
- Logo dapat diklik untuk kembali ke dashboard

### Jika Logo Belum Di-upload:
- Logo default (app-logo-icon) ditampilkan
- Logo default adalah icon SVG bawaan aplikasi
- Setelah logo di-upload, perubahan langsung terlihat (tidak perlu refresh cache)

## Testing

### ✅ Test Cases yang Sudah Dilakukan:

1. **Logo Display:**
   - ✅ Logo BPKAD muncul di halaman `/login`
   - ✅ Logo BPKAD muncul di halaman `/register`
   - ✅ Logo BPKAD muncul di halaman `/forgot-password`

2. **Responsive Design:**
   - ✅ Logo terlihat jelas di desktop (64x64px)
   - ✅ Logo terlihat jelas di mobile view
   - ✅ Logo proporsional dan tidak terdistorsi

3. **Fallback Behavior:**
   - ✅ Jika logo tidak ada, tampilkan logo default
   - ✅ Jika file logo terhapus, fallback ke logo default
   - ✅ Validasi keberadaan file di storage

## File yang Dimodifikasi

1. ✅ `resources/views/components/auth-logo.blade.php` (Baru)
2. ✅ `resources/views/components/layouts/auth/simple.blade.php`
3. ✅ `resources/views/components/layouts/auth/card.blade.php`
4. ✅ `resources/views/components/layouts/auth/split.blade.php`

## Dependencies

- `App\Models\OrgSettings` - Model untuk mengakses pengaturan organisasi
- `Storage::disk('public')` - Untuk mengakses file logo
- `Storage::url()` - Untuk generate URL publik logo

## Catatan Teknis

### Singleton Pattern:
```php
$orgSettings = \App\Models\OrgSettings::getInstance();
```
Menggunakan singleton pattern untuk efisiensi query database.

### Storage Path:
```php
$logoPath = $orgSettings->logo_path; // e.g., "logos/abc123.png"
$fullUrl = \Storage::url($logoPath); // e.g., "/storage/logos/abc123.png"
```

### Component Attributes:
Component `<x-auth-logo />` mendukung semua atribut HTML standar:
```html
<x-auth-logo class="size-16 fill-current text-black" />
<x-auth-logo class="h-10 w-10 object-cover rounded-full" />
```

## Troubleshooting

### Logo Tidak Muncul

**Problem:** Logo tidak muncul setelah upload

**Solusi:**
1. Pastikan storage link sudah dibuat:
   ```bash
   php artisan storage:link
   ```

2. Cek file ada di storage:
   ```bash
   ls -la storage/app/public/logos/
   ```

3. Cek permission folder:
   ```bash
   chmod -R 775 storage/app/public/logos
   ```

### Logo Terdistorsi

**Problem:** Logo terlihat stretch atau distorsi

**Solusi:**
- Gunakan logo dengan aspect ratio square (1:1)
- Component sudah menggunakan `object-contain` untuk menjaga proporsi
- Pastikan logo memiliki resolusi yang cukup (min 256x256px)

### Logo Lama Masih Muncul

**Problem:** Setelah update logo, logo lama masih muncul

**Solusi:**
1. Clear browser cache (Ctrl+Shift+R atau Cmd+Shift+R)
2. Clear Laravel cache:
   ```bash
   php artisan cache:clear
   php artisan view:clear
   ```

## Keuntungan Implementasi

✅ **Branding Konsisten:** Logo organisasi muncul di semua halaman auth
✅ **User Experience:** Pengguna langsung mengenali sistem saat login
✅ **Flexible:** Logo dapat diubah tanpa perlu edit kode
✅ **Fallback Safe:** Jika logo tidak ada, sistem tetap berfungsi normal
✅ **Responsive:** Logo terlihat baik di semua ukuran layar
✅ **Maintainable:** Perubahan logo hanya perlu dilakukan di satu tempat

## Screenshot Hasil

### Halaman Login
- Logo BPKAD ditampilkan dengan ukuran 64x64px
- Posisi centered di atas form login
- Background putih, logo terlihat jelas

### Halaman Register
- Logo BPKAD ditampilkan dengan ukuran 64x64px
- Posisi centered di atas form registrasi
- Konsisten dengan halaman login

### Halaman Forgot Password
- Logo BPKAD ditampilkan dengan ukuran 64x64px
- Posisi centered di atas form reset password
- Konsisten dengan halaman auth lainnya

## Status

✅ **Implementation:** Complete
✅ **Testing:** Passed
✅ **Documentation:** Complete
✅ **Deployment:** Ready for production

---

**Last Updated:** January 6, 2026
**Version:** 1.0.0
**Author:** PD System Development Team

