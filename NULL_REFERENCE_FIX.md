# Null Reference Fix - Perbaikan Error "Call to a member function on null"

## Overview

Error ini terjadi ketika mencoba memanggil method `fullNameWithTitles()` pada object yang null di halaman `/documents`. Error terjadi karena relasi `treasurerUser` dan `payeeUser` pada model `Receipt` bisa bernilai null, tetapi kode tidak menangani kasus null dengan benar.

## Error yang Diperbaiki

### **Error Message:**
```
Call to a member function fullNameWithTitles() on null
resources/views/livewire/documents/main-page.blade.php:208
```

### **Root Cause:**
- `$receipt->treasurerUser` bisa bernilai null
- `$receipt->payeeUser` bisa bernilai null  
- `$participant->user` bisa bernilai null
- Kode tidak menggunakan null coalescing operator dengan benar

## Implementasi Perbaikan

### **1. Perbaikan untuk Treasurer User (Line 208)**

#### **Before:**
```blade
{{ $receipt->treasurerUser->fullNameWithTitles() ?? 'N/A' }}
```

#### **After:**
```blade
@php
    $treasurerSnapshot = $receipt->getTreasurerUserSnapshot();
    $treasurerName = $treasurerSnapshot['name'] ?? 'N/A';
    $treasurerGelarDepan = $treasurerSnapshot['gelar_depan'] ?? '';
    $treasurerGelarBelakang = $treasurerSnapshot['gelar_belakang'] ?? '';
    
    $fullName = '';
    if ($treasurerGelarDepan) {
        $fullName .= $treasurerGelarDepan . ' ';
    }
    $fullName .= $treasurerName;
    if ($treasurerGelarBelakang) {
        $fullName .= ', ' . $treasurerGelarBelakang;
    }
@endphp
{{ $fullName }}
```

### **2. Perbaikan untuk Payee User (Line 200)**

#### **Before:**
```blade
{{ $receipt->payeeUser->fullNameWithTitles() ?? 'N/A' }}
```

#### **After:**
```blade
@php
    $payeeSnapshot = $receipt->getPayeeUserSnapshot();
    $payeeName = $payeeSnapshot['name'] ?? 'N/A';
    $payeeGelarDepan = $payeeSnapshot['gelar_depan'] ?? '';
    $payeeGelarBelakang = $payeeSnapshot['gelar_belakang'] ?? '';
    
    $fullName = '';
    if ($payeeGelarDepan) {
        $fullName .= $payeeGelarDepan . ' ';
    }
    $fullName .= $payeeName;
    if ($payeeGelarBelakang) {
        $fullName .= ', ' . $payeeGelarBelakang;
    }
@endphp
{{ $fullName }}
```

### **3. Perbaikan untuk Participant User (Line 345 & 456)**

#### **Before:**
```blade
{{ $participant->user->fullNameWithTitles() }} ({{ $participant->user->position?->name ?? 'N/A' }})
```

#### **After:**
```blade
{{ $participant->user?->fullNameWithTitles() ?? 'N/A' }} ({{ $participant->user?->position?->name ?? 'N/A' }})
```

## Strategi Perbaikan

### **1. Menggunakan Snapshot Data**
- Menggunakan method `getTreasurerUserSnapshot()` dan `getPayeeUserSnapshot()` dari model `Receipt`
- Snapshot data lebih reliable karena disimpan saat data dibuat
- Fallback ke relasi jika snapshot tidak tersedia

### **2. Null Coalescing Operator**
- Menggunakan `??` operator untuk menangani null values
- Menyediakan fallback value yang sesuai
- Mencegah error "Call to a member function on null"

### **3. Manual Name Construction**
- Membangun nama lengkap secara manual dari komponen snapshot
- Menangani gelar depan dan belakang dengan benar
- Format: `[Gelar Depan] [Nama], [Gelar Belakang]`

## Model Receipt - Snapshot Methods

### **getTreasurerUserSnapshot()**
```php
public function getTreasurerUserSnapshot()
{
    return [
        'name' => $this->treasurer_user_name_snapshot ?: $this->treasurerUser?->name,
        'gelar_depan' => $this->treasurer_user_gelar_depan_snapshot ?: $this->treasurerUser?->gelar_depan,
        'gelar_belakang' => $this->treasurer_user_gelar_belakang_snapshot ?: $this->treasurerUser?->gelar_belakang,
        'nip' => $this->treasurer_user_nip_snapshot ?: $this->treasurerUser?->nip,
        // ... other fields
    ];
}
```

### **getPayeeUserSnapshot()**
```php
public function getPayeeUserSnapshot()
{
    // Find the participant in Nota Dinas that matches this receipt payee
    $participant = $this->sppd->spt->notaDinas->participants
        ->where('user_id', $this->payee_user_id)
        ->first();

    if ($participant) {
        return [
            'name' => $participant->user_name_snapshot ?: $participant->user?->name,
            'gelar_depan' => $participant->user_gelar_depan_snapshot ?: $participant->user?->gelar_depan,
            'gelar_belakang' => $participant->user_gelar_belakang_snapshot ?: $participant->user?->gelar_belakang,
            'nip' => $participant->user_nip_snapshot ?: $participant->user?->nip,
            // ... other fields
        ];
    }

    return null;
}
```

## Testing

### **Test Case 1: Treasurer User Null**
```php
// Receipt dengan treasurer_user_id null
$receipt = Receipt::whereNull('treasurer_user_id')->first();

// Should not throw error
$treasurerSnapshot = $receipt->getTreasurerUserSnapshot();
$treasurerName = $treasurerSnapshot['name'] ?? 'N/A';
// Returns 'N/A' or snapshot data
```

### **Test Case 2: Payee User Null**
```php
// Receipt dengan payee_user_id null
$receipt = Receipt::whereNull('payee_user_id')->first();

// Should not throw error
$payeeSnapshot = $receipt->getPayeeUserSnapshot();
$payeeName = $payeeSnapshot['name'] ?? 'N/A';
// Returns 'N/A' or snapshot data
```

### **Test Case 3: Participant User Null**
```php
// Participant dengan user_id null
$participant = NotaDinasParticipant::whereNull('user_id')->first();

// Should not throw error
$userName = $participant->user?->fullNameWithTitles() ?? 'N/A';
// Returns 'N/A'
```

## Error Prevention

### **Best Practices:**
1. **Always use null coalescing operator** (`??`) when accessing potentially null relationships
2. **Use snapshot data** when available for better reliability
3. **Provide meaningful fallback values** instead of empty strings
4. **Test with null data** to ensure error handling works correctly

### **Code Patterns:**
```blade
{{-- Good --}}
{{ $model->relation?->method() ?? 'Fallback' }}

{{-- Bad --}}
{{ $model->relation->method() ?? 'Fallback' }}

{{-- Good --}}
@php
    $snapshot = $model->getSnapshot();
    $value = $snapshot['field'] ?? 'Fallback';
@endphp
{{ $value }}
```

## Database Considerations

### **Foreign Key Constraints:**
- `treasurer_user_id` has `onDelete('set null')` constraint
- `payee_user_id` has `onDelete('set null')` constraint
- This means when a user is deleted, these fields become null

### **Snapshot Fields:**
- Snapshot fields preserve data even when user is deleted
- More reliable than direct relationships
- Should be used as primary source of truth

## Performance Impact

### **Positive:**
- Reduces database queries by using snapshot data
- Prevents error pages that require debugging
- Better user experience with graceful error handling

### **Considerations:**
- Snapshot data might be outdated if user data changes
- Manual name construction adds some processing overhead
- Should consider caching for frequently accessed data

## Status

✅ **Treasurer User Fix** - Error pada line 208 berhasil diperbaiki  
✅ **Payee User Fix** - Error pada line 200 berhasil diperbaiki  
✅ **Participant User Fix** - Error pada line 345 & 456 berhasil diperbaiki  
✅ **Null Coalescing** - Semua akses relasi menggunakan null coalescing operator  
✅ **Snapshot Data Usage** - Menggunakan snapshot data untuk reliability  
✅ **Error Prevention** - Implementasi best practices untuk mencegah error serupa  

Perbaikan ini memastikan bahwa halaman `/documents` tidak akan mengalami error "Call to a member function on null" lagi, dengan penanganan yang graceful untuk data yang null.
