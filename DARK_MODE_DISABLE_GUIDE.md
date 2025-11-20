# Dark Mode Disable Guide

## Overview

Panduan ini menjelaskan cara mendisable dark mode pada sistem Perjalanan Dinas. Dark mode telah dinonaktifkan secara permanen dan sistem akan selalu menggunakan light mode.

## Perubahan yang Dilakukan

### **1. HTML Layout Files**

#### **File yang Dimodifikasi:**
- `resources/views/components/layouts/app/sidebar.blade.php`
- `resources/views/components/layouts/app/header.blade.php`
- `resources/views/components/layouts/auth/split.blade.php`
- `resources/views/components/layouts/auth/simple.blade.php`
- `resources/views/components/layouts/auth/card.blade.php`

#### **Perubahan:**
```html
<!-- SEBELUM -->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<!-- SESUDAH -->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
```

**Penjelasan:** Menghapus class `dark` dari elemen `<html>` di semua layout files.

### **2. CSS Modifications**

#### **File: `resources/css/app.css`**
```css
/* Disable dark mode globally */
.dark {
    /* Override all dark mode styles to use light mode */
    --color-accent: var(--color-neutral-800);
    --color-accent-content: var(--color-neutral-800);
    --color-accent-foreground: var(--color-white);
}

/* Force light mode for all elements */
* {
    color-scheme: light !important;
}
```

**Penjelasan:** 
- Override semua CSS variables untuk dark mode agar menggunakan light mode
- Force `color-scheme: light` untuk semua elemen

### **3. JavaScript Modifications**

#### **File: `resources/js/app.js`**
```javascript
// Disable dark mode globally
document.addEventListener('DOMContentLoaded', function() {
    // Remove dark class from html element
    document.documentElement.classList.remove('dark');
    
    // Prevent dark mode from being applied
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                }
            }
        });
    });
    
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
    
    // Override Flux UI appearance if it exists
    if (window.Alpine && window.Alpine.store) {
        Alpine.store('flux', {
            appearance: 'light'
        });
    }
});
```

**Penjelasan:**
- Menghapus class `dark` dari HTML element saat DOM loaded
- Menggunakan MutationObserver untuk mencegah class `dark` ditambahkan kembali
- Override Flux UI appearance store untuk memaksa light mode

### **4. Appearance Settings Page**

#### **File: `resources/views/livewire/settings/appearance.blade.php`**
```blade
<div class="space-y-4">
    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-blue-800 font-medium">Dark Mode Dinonaktifkan</span>
        </div>
        <p class="text-blue-700 text-sm mt-1">Sistem saat ini menggunakan mode terang (light mode) secara permanen.</p>
    </div>
    
    <flux:radio.group x-data variant="segmented" x-model="$flux.appearance" disabled>
        <flux:radio value="light" icon="sun" checked>{{ __('Light') }}</flux:radio>
        <flux:radio value="dark" icon="moon" disabled>{{ __('Dark') }}</flux:radio>
        <flux:radio value="system" icon="computer-desktop" disabled>{{ __('System') }}</flux:radio>
    </flux:radio.group>
</div>
```

**Penjelasan:**
- Menampilkan notifikasi bahwa dark mode telah dinonaktifkan
- Disable semua opsi appearance kecuali light mode
- Light mode dipilih secara default dan tidak bisa diubah

## Cara Kerja

### **1. HTML Level**
- Class `dark` dihapus dari semua layout files
- Mencegah dark mode diterapkan dari level HTML

### **2. CSS Level**
- Override semua CSS variables untuk dark mode
- Force `color-scheme: light` untuk semua elemen
- Memastikan semua komponen menggunakan light mode styling

### **3. JavaScript Level**
- Menghapus class `dark` saat DOM loaded
- MutationObserver mencegah class `dark` ditambahkan kembali
- Override Flux UI appearance store

### **4. UI Level**
- Appearance settings page menampilkan notifikasi
- Opsi dark mode dan system mode di-disable
- Hanya light mode yang tersedia

## Benefits

### **✅ Konsistensi Visual**
- Semua halaman menggunakan light mode
- Tidak ada perbedaan appearance antar halaman
- User experience yang konsisten

### **✅ Performance**
- Mengurangi CSS yang perlu di-load
- Tidak ada switching logic yang kompleks
- Rendering yang lebih cepat

### **✅ Maintenance**
- Lebih mudah untuk maintain styling
- Tidak perlu handle dark mode edge cases
- Code yang lebih sederhana

### **✅ User Experience**
- Interface yang lebih familiar untuk user
- Tidak ada confusion dengan mode switching
- Consistent branding

## Testing

### **Test Cases:**

#### **1. Layout Files**
- [ ] App sidebar layout menggunakan light mode
- [ ] App header layout menggunakan light mode
- [ ] Auth split layout menggunakan light mode
- [ ] Auth simple layout menggunakan light mode
- [ ] Auth card layout menggunakan light mode

#### **2. CSS Override**
- [ ] Dark mode CSS variables di-override
- [ ] Color scheme forced ke light
- [ ] Semua komponen menggunakan light styling

#### **3. JavaScript Prevention**
- [ ] Class `dark` dihapus saat page load
- [ ] MutationObserver mencegah class `dark` ditambahkan
- [ ] Flux UI appearance store di-override

#### **4. Settings Page**
- [ ] Notifikasi dark mode disabled ditampilkan
- [ ] Opsi dark mode dan system mode di-disable
- [ ] Light mode dipilih secara default

## Rollback (Jika Diperlukan)

### **Untuk Mengaktifkan Kembali Dark Mode:**

#### **1. Restore HTML Files**
```html
<!-- Kembalikan class dark di semua layout files -->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
```

#### **2. Remove CSS Override**
```css
/* Hapus bagian ini dari app.css */
/* Disable dark mode globally */
.dark {
    /* Override all dark mode styles to use light mode */
    --color-accent: var(--color-neutral-800);
    --color-accent-content: var(--color-neutral-800);
    --color-accent-foreground: var(--color-white);
}

/* Force light mode for all elements */
* {
    color-scheme: light !important;
}
```

#### **3. Remove JavaScript Prevention**
```javascript
// Hapus bagian ini dari app.js
// Disable dark mode globally
document.addEventListener('DOMContentLoaded', function() {
    // ... semua kode disable dark mode
});
```

#### **4. Restore Appearance Settings**
```blade
<!-- Kembalikan ke versi original -->
<flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
    <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
    <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
    <flux:radio value="system" icon="computer-desktop">{{ __('System') }}</flux:radio>
</flux:radio.group>
```

## Files yang Dimodifikasi

1. **`resources/views/components/layouts/app/sidebar.blade.php`** - Removed `class="dark"`
2. **`resources/views/components/layouts/app/header.blade.php`** - Removed `class="dark"`
3. **`resources/views/components/layouts/auth/split.blade.php`** - Removed `class="dark"`
4. **`resources/views/components/layouts/auth/simple.blade.php`** - Removed `class="dark"`
5. **`resources/views/components/layouts/auth/card.blade.php`** - Removed `class="dark"`
6. **`resources/css/app.css`** - Added dark mode override CSS
7. **`resources/js/app.js`** - Added dark mode prevention JavaScript
8. **`resources/views/livewire/settings/appearance.blade.php`** - Modified appearance settings

## Kesimpulan

Dark mode telah berhasil dinonaktifkan secara permanen dengan:

1. **HTML Level**: Menghapus class `dark` dari semua layout files
2. **CSS Level**: Override dark mode styles dan force light mode
3. **JavaScript Level**: Prevention mechanism untuk mencegah dark mode
4. **UI Level**: Modified appearance settings dengan notifikasi

Sistem sekarang akan selalu menggunakan light mode dengan konsistensi visual yang baik! 🎉✨
