# Users Table Layout Update - Penggabungan Kolom Email & Status dengan Role

## Overview

Update ini menggabungkan kolom email dengan kolom status pada halaman daftar user (`/users`), dan menambahkan informasi role yang diberikan kepada user. Perubahan ini membuat tampilan tabel lebih kompak dan informatif.

## Perubahan yang Dilakukan

### **1. Header Tabel - Penggabungan Kolom**

#### **Before:**
```blade
<th>Email</th>
<th>Tingkatan Perjalanan</th>
<th>Status</th>
```

#### **After:**
```blade
<th>Email & Status</th>
<th>Tingkatan Perjalanan</th>
```

### **2. Konten Tabel - Struktur Baru**

#### **Before:**
```blade
<!-- Kolom Email -->
<td>{{ $user->email }}</td>

<!-- Kolom Status -->
<td>
    <div class="space-y-1">
        @if($user->is_non_staff)
            <span class="badge-red">Non-Staff</span>
        @else
            <span class="badge-green">Staff</span>
        @endif
        
        @if($user->isUsedInDocuments())
            <span class="badge-yellow">Terkait Dokumen</span>
        @endif
    </div>
</td>
```

#### **After:**
```blade
<!-- Kolom Email & Status (Gabungan) -->
<td>
    <div class="space-y-2">
        <!-- Email -->
        <div class="text-sm text-gray-900 dark:text-gray-300">
            {{ $user->email }}
        </div>
        
        <!-- Status Staff/Non-Staff -->
        <div class="flex flex-wrap gap-1">
            @if($user->is_non_staff)
                <span class="badge-red">Non-Staff</span>
            @else
                <span class="badge-green">Staff</span>
            @endif
            
            @if($user->isUsedInDocuments())
                <span class="badge-yellow">Terkait Dokumen</span>
            @endif
        </div>
        
        <!-- Roles -->
        @if($user->roles->count() > 0)
            <div class="flex flex-wrap gap-1">
                @foreach($user->roles as $role)
                    <span class="badge-purple">
                        {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                    </span>
                @endforeach
            </div>
        @else
            <span class="badge-gray">Tidak Ada Role</span>
        @endif
    </div>
</td>
```

### **3. Livewire Component - Eager Loading**

#### **Before:**
```php
$users = User::with(['unit', 'position.echelon', 'rank', 'travelGrade'])
```

#### **After:**
```php
$users = User::with(['unit', 'position.echelon', 'rank', 'travelGrade', 'roles'])
```

## Fitur yang Ditambahkan

### **✅ Informasi Role**
- Menampilkan semua role yang diberikan kepada user
- Format role name yang user-friendly (mengganti `-` dengan spasi dan capitalize)
- Badge dengan warna ungu untuk membedakan dari status lainnya

### **✅ Layout yang Lebih Kompak**
- Menggabungkan email dan status dalam satu kolom
- Mengurangi jumlah kolom dari 6 menjadi 5
- Lebih banyak ruang untuk informasi penting

### **✅ Visual Hierarchy**
- Email ditampilkan di bagian atas
- Status staff/non-staff dan terkait dokumen di tengah
- Role di bagian bawah
- Spacing yang konsisten dengan `space-y-2`

### **✅ Responsive Design**
- Menggunakan `flex flex-wrap gap-1` untuk badge
- Badge akan wrap ke baris baru jika tidak muat
- Tetap responsive di berbagai ukuran layar

## Styling Badge

### **Role Badge (Purple):**
```css
bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300
```

### **Status Badge (Green/Red):**
```css
bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
```

### **Document Badge (Yellow):**
```css
bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
```

### **No Role Badge (Gray):**
```css
bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300
```

## Role Name Formatting

### **Transformation Logic:**
```php
{{ ucfirst(str_replace('-', ' ', $role->name)) }}
```

### **Examples:**
- `super-admin` → `Super admin`
- `bendahara-pengeluaran` → `Bendahara pengeluaran`
- `bendahara-pengeluaran-pembantu` → `Bendahara pengeluaran pembantu`
- `admin` → `Admin`
- `sekretariat` → `Sekretariat`

## Database Impact

### **Eager Loading:**
- Menambahkan `roles` ke dalam `with()` clause
- Mencegah N+1 query problem
- Meningkatkan performa dengan loading relasi sekaligus

### **No Database Changes:**
- Tidak ada perubahan struktur database
- Menggunakan relasi yang sudah ada
- Tidak memerlukan migration

## Performance Considerations

### **Positive:**
- Mengurangi jumlah kolom = lebih cepat rendering
- Eager loading roles = lebih efisien query
- Kompak layout = lebih sedikit scroll

### **Considerations:**
- Loading roles untuk semua user = sedikit overhead
- Multiple badges per user = sedikit kompleksitas rendering
- Flex wrap = layout calculation overhead

## Testing

### **Test Case 1: User dengan Multiple Roles**
```php
$user = User::with('roles')->whereHas('roles')->first();
// Should display all roles as purple badges
```

### **Test Case 2: User tanpa Role**
```php
$user = User::with('roles')->whereDoesntHave('roles')->first();
// Should display "Tidak Ada Role" gray badge
```

### **Test Case 3: User dengan Status Terkait Dokumen**
```php
$user = User::with('roles')->whereHas('notaDinasTo')->first();
// Should display "Terkait Dokumen" yellow badge
```

### **Test Case 4: Layout Responsiveness**
```blade
{{-- Should wrap badges if too many --}}
@if($user->roles->count() > 3)
    {{-- Badges should wrap to new line --}}
@endif
```

## User Experience Improvements

### **✅ Informasi Lengkap**
- User bisa melihat email, status, dan role sekaligus
- Tidak perlu scroll horizontal untuk melihat semua info
- Badge color coding untuk quick identification

### **✅ Visual Clarity**
- Email di bagian atas (primary info)
- Status di tengah (secondary info)
- Role di bawah (tertiary info)
- Consistent spacing dan alignment

### **✅ Accessibility**
- Badge dengan kontras warna yang baik
- Text yang readable di light dan dark mode
- Proper semantic structure

## Migration Guide

### **Untuk Developer:**
1. Update query untuk include `roles` relasi
2. Update view untuk menggunakan struktur baru
3. Test dengan berbagai kombinasi role dan status

### **Untuk User:**
- Tidak ada perubahan yang perlu dilakukan
- Interface akan otomatis menampilkan informasi role
- Layout yang lebih kompak dan informatif

## Status

✅ **Header Update** - Kolom header berhasil digabungkan  
✅ **Content Restructure** - Konten email, status, dan role berhasil digabungkan  
✅ **Role Display** - Informasi role berhasil ditampilkan dengan badge  
✅ **Eager Loading** - Relasi roles berhasil ditambahkan ke query  
✅ **Styling** - Badge styling dengan warna yang konsisten  
✅ **Responsive Design** - Layout responsive dengan flex wrap  
✅ **Role Formatting** - Role name formatting yang user-friendly  

Update ini membuat halaman daftar user lebih informatif dan kompak, dengan menampilkan email, status, dan role dalam satu kolom yang terorganisir dengan baik.
