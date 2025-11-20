# User Delete Protection - Soft Delete Logic Fix

## Masalah yang Diperbaiki

### **Logika yang Salah Sebelumnya:**
- User Delete Protection mengecek **semua** dokumen termasuk yang sudah di-soft delete
- User yang hanya digunakan dalam dokumen yang sudah di-soft delete **tidak bisa dihapus**
- Ini tidak masuk akal karena dokumen yang sudah di-soft delete = dokumen yang batal/dicabut

### **Logika yang Benar:**
- **Dokumen yang sudah di-soft delete = Dokumen yang batal/dicabut**
- **User yang hanya digunakan dalam dokumen yang sudah di-soft delete = Boleh dihapus**
- **User yang masih digunakan dalam dokumen aktif = Tidak boleh dihapus**

## Solusi yang Diimplementasikan

### **1. Pemisahan Validasi**

#### **Validasi Dokumen Aktif:**
```php
// Check if user is used in any active documents (only non-deleted documents)
if ($user->isUsedInDocuments()) {
    // User masih digunakan dalam dokumen aktif - TIDAK BOLEH DIHAPUS
    $allInvolvements = $user->getAllDocumentInvolvements();
    // ... show detailed error message
    return;
}
```

#### **Validasi Foreign Key Constraint:**
```php
// Check for foreign key constraint violation and clean up soft deleted references
if ($user->willCauseForeignKeyViolation()) {
    // User memiliki referensi hard di database - perlu cleanup
    $cleanedCount = $user->cleanupSoftDeletedReferences();
    // ... cleanup process
}
```

### **2. Method Cleanup untuk Soft Deleted References**

#### **Model User - Method Cleanup:**
```php
/**
 * Clean up soft deleted receipts that reference this user
 * This allows the user to be deleted if they're only referenced in soft deleted documents
 */
public function cleanupSoftDeletedReferences(): int
{
    $cleanedCount = 0;
    
    // Hard delete soft deleted receipts where user is payee (payee_user_id cannot be null)
    $payeeReceipts = \DB::table('receipts')
        ->where('payee_user_id', $this->id)
        ->whereNotNull('deleted_at')
        ->delete();
    $cleanedCount += $payeeReceipts;
    
    // Clean up soft deleted receipts where user is treasurer (treasurer_user_id can be null)
    $treasurerReceipts = \DB::table('receipts')
        ->where('treasurer_user_id', $this->id)
        ->whereNotNull('deleted_at')
        ->update(['treasurer_user_id' => null]);
    $cleanedCount += $treasurerReceipts;
    
    return $cleanedCount;
}
```

### **3. Method untuk Mengecek Foreign Key Constraint**

#### **Model User - Method Validasi:**
```php
/**
 * Check if user deletion will cause foreign key constraint violation
 * This checks for any hard references in the database, regardless of soft delete status
 */
public function willCauseForeignKeyViolation(): bool
{
    // Check for hard references in receipts table
    $receiptsCount = \DB::table('receipts')
        ->where('payee_user_id', $this->id)
        ->orWhere('treasurer_user_id', $this->id)
        ->count();
        
    return $receiptsCount > 0;
}
```

### **4. Update Livewire Component**

#### **Users/Index.php - Method Delete yang Diperbaiki:**
```php
public function delete(User $user)
{
    // Check if user has permission to delete users
    if (!PermissionHelper::can('users.delete')) {
        session()->flash('error', 'Anda tidak memiliki izin untuk menghapus user.');
        return;
    }
    
    // Check if user is used in any active documents (only non-deleted documents)
    if ($user->isUsedInDocuments()) {
        $allInvolvements = $user->getAllDocumentInvolvements();
        $involvementTexts = [];
        
        // Process nota dinas involvements
        if (isset($allInvolvements['nota_dinas'])) {
            $notaDinasText = collect($allInvolvements['nota_dinas'])->map(function ($involvement) {
                return $involvement['type'] . ' (' . $involvement['count'] . ' dokumen)';
            })->join(', ');
            $involvementTexts[] = 'dokumen nota dinas sebagai: ' . $notaDinasText;
        }
        
        // Process sub kegiatan involvements
        if (isset($allInvolvements['sub_kegiatan'])) {
            $subKegiatanText = collect($allInvolvements['sub_kegiatan'])->map(function ($involvement) {
                return $involvement['type'] . ' (' . $involvement['count'] . ' sub kegiatan)';
            })->join(', ');
            $involvementTexts[] = 'sub kegiatan sebagai: ' . $subKegiatanText;
        }
        
        // Process receipts involvements
        if (isset($allInvolvements['receipts'])) {
            $receiptsText = collect($allInvolvements['receipts'])->map(function ($involvement) {
                return $involvement['type'] . ' (' . $involvement['count'] . ' kwitansi)';
            })->join(', ');
            $involvementTexts[] = 'kwitansi sebagai: ' . $receiptsText;
        }
        
        $fullInvolvementText = implode(' dan ', $involvementTexts);
        session()->flash('error', 'Data pegawai tidak dapat dihapus karena masih digunakan dalam ' . $fullInvolvementText . '.');
        return;
    }
    
    // Check for foreign key constraint violation and clean up soft deleted references
    if ($user->willCauseForeignKeyViolation()) {
        // Try to clean up soft deleted references
        $cleanedCount = $user->cleanupSoftDeletedReferences();
        
        if ($cleanedCount > 0) {
            session()->flash('message', "Berhasil membersihkan {$cleanedCount} referensi dari dokumen yang sudah dihapus. Mencoba menghapus user...");
        }
        
        // Check again after cleanup
        if ($user->willCauseForeignKeyViolation()) {
            session()->flash('error', 'Data pegawai tidak dapat dihapus karena masih memiliki referensi dalam dokumen aktif. Silakan hapus atau edit dokumen yang masih menggunakan user ini terlebih dahulu.');
            return;
        }
    }
    
    try {
        $user->delete();
        session()->flash('message', 'Data pegawai berhasil dihapus.');
    } catch (\Exception $e) {
        session()->flash('error', 'Gagal menghapus data pegawai. ' . $e->getMessage());
    }
}
```

## Perbedaan Antara Logika Lama dan Baru

### **Logika Lama (Salah):**
```php
// Mengecek SEMUA dokumen termasuk yang sudah di-soft delete
if ($user->isUsedInDocumentsWithTrashed()) {
    // User tidak bisa dihapus meskipun hanya digunakan dalam dokumen yang sudah di-soft delete
    return;
}
```

### **Logika Baru (Benar):**
```php
// 1. Cek dokumen AKTIF terlebih dahulu
if ($user->isUsedInDocuments()) {
    // User masih digunakan dalam dokumen aktif - TIDAK BOLEH DIHAPUS
    return;
}

// 2. Cek foreign key constraint dan cleanup jika perlu
if ($user->willCauseForeignKeyViolation()) {
    // Cleanup referensi dari dokumen yang sudah di-soft delete
    $cleanedCount = $user->cleanupSoftDeletedReferences();
    // ... proses cleanup
}
```

## Test Cases

### **Test Case 1: User dengan Soft Deleted Receipts (User ID 45)**
```
User: Basuki Tjahaja Purnama (ID: 45)
Is used in active documents: No
Will cause foreign key violation (before cleanup): Yes
Cleaning up soft deleted references...
Cleaned up: 4 references
Will cause foreign key violation (after cleanup): No
RESULT: Can delete
```

### **Test Case 2: User yang Bisa Dihapus (User ID 60)**
```
User: admin bpkad (ID: 60)
Is used in active documents: No
Will cause foreign key violation: No
RESULT: Can delete
```

### **Test Case 3: User dengan Dokumen Aktif**
```
User: [User dengan dokumen aktif]
Is used in active documents: Yes
RESULT: Cannot delete - used in active documents
```

## Handling Database Constraints

### **Constraint yang Ditemukan:**
- `payee_user_id`: `NOT NULL` - Tidak bisa di-set ke `null`
- `treasurer_user_id`: `NULL` - Bisa di-set ke `null`

### **Solusi Cleanup:**
```php
// Untuk payee_user_id (NOT NULL) - Hard delete receipts yang sudah di-soft delete
$payeeReceipts = \DB::table('receipts')
    ->where('payee_user_id', $this->id)
    ->whereNotNull('deleted_at')
    ->delete();

// Untuk treasurer_user_id (NULL) - Set ke null
$treasurerReceipts = \DB::table('receipts')
    ->where('treasurer_user_id', $this->id)
    ->whereNotNull('deleted_at')
    ->update(['treasurer_user_id' => null]);
```

## Benefits

### **✅ Logika yang Benar**
- User yang hanya digunakan dalam dokumen yang sudah di-soft delete bisa dihapus
- User yang masih digunakan dalam dokumen aktif tidak bisa dihapus
- Sesuai dengan logika bisnis yang benar

### **✅ Automatic Cleanup**
- Sistem otomatis membersihkan referensi dari dokumen yang sudah di-soft delete
- User tidak perlu manual cleanup
- Proses transparan untuk user

### **✅ Foreign Key Constraint Handling**
- Menangani constraint database dengan benar
- Hard delete untuk constraint NOT NULL
- Set null untuk constraint yang mengizinkan NULL

### **✅ User Experience yang Lebih Baik**
- Error message yang jelas dan informatif
- Proses cleanup otomatis
- Feedback yang tepat untuk setiap situasi

## Files yang Dimodifikasi

1. **`app/Models/User.php`**
   - Added `willCauseForeignKeyViolation()` method
   - Added `cleanupSoftDeletedReferences()` method

2. **`app/Livewire/Users/Index.php`**
   - Updated `delete()` method dengan logika yang benar
   - Added automatic cleanup process
   - Improved error messages

## Kesimpulan

Perbaikan ini menyelesaikan masalah logika yang salah dalam User Delete Protection. Sekarang sistem:

1. **Hanya mengecek dokumen aktif** untuk validasi delete user
2. **Otomatis membersihkan referensi** dari dokumen yang sudah di-soft delete
3. **Menangani foreign key constraint** dengan benar
4. **Memberikan user experience** yang lebih baik

Logika sekarang sudah benar: **User yang hanya digunakan dalam dokumen yang sudah di-soft delete = Boleh dihapus** ✅
