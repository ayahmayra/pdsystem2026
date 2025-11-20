# User Delete Protection - Foreign Key Constraint Fix

## Masalah yang Diperbaiki

### **Error yang Terjadi:**
```
Gagal menghapus data pegawai. SQLSTATE[23000]: Integrity constraint violation: 1451 Cannot delete or update a parent row: a foreign key constraint fails (`perjadindb`.`receipts`, CONSTRAINT `receipts_payee_user_id_foreign` FOREIGN KEY (`payee_user_id`) REFERENCES `users` (`id`))
```

### **Root Cause:**
- User Delete Protection hanya mengecek dokumen yang belum di-soft delete
- Foreign key constraint berlaku untuk semua data, termasuk yang sudah di-soft delete
- Ada data di tabel `receipts` yang sudah di-soft delete tetapi masih mereferensi user sebagai `payee_user_id`
- Relasi Eloquent `receiptsAsPayee()` menggunakan `whereNull('deleted_at')` yang tidak menangkap data soft deleted

## Solusi yang Diimplementasikan

### **1. Relasi Baru untuk Soft Deleted Data**

#### **Model User - Relasi Tambahan:**
```php
/**
 * Get receipts where this user is the treasurer (bendahara) (including soft deleted)
 */
public function receiptsAsTreasurerWithTrashed(): HasMany
{
    return $this->hasMany(Receipt::class, 'treasurer_user_id')->withTrashed();
}

/**
 * Get receipts where this user is the payee (penerima pembayaran) (including soft deleted)
 */
public function receiptsAsPayeeWithTrashed(): HasMany
{
    return $this->hasMany(Receipt::class, 'payee_user_id')->withTrashed();
}
```

### **2. Method Validasi Baru**

#### **Method untuk Mengecek Penggunaan (Termasuk Soft Deleted):**
```php
/**
 * Check if user is used in any receipts (including soft deleted) - for foreign key constraint validation
 */
public function isUsedInReceiptsWithTrashed(): bool
{
    return $this->receiptsAsTreasurerWithTrashed()->exists() || $this->receiptsAsPayeeWithTrashed()->exists();
}

/**
 * Check if user is used in any documents (including soft deleted receipts) - for foreign key constraint validation
 */
public function isUsedInDocumentsWithTrashed(): bool
{
    return $this->isUsedInNotaDinas() || $this->isUsedInSubKegiatan() || $this->isUsedInReceiptsWithTrashed();
}
```

### **3. Method Detail Penggunaan (Termasuk Soft Deleted)**

#### **Method untuk Mendapatkan Detail Penggunaan:**
```php
/**
 * Get all receipts where this user is involved as treasurer (including soft deleted)
 */
public function getAllTreasurerInvolvementWithTrashed(): array
{
    $involvements = [];

    // Check as Bendahara Pengeluaran
    $bendaharaReceipts = $this->receiptsAsTreasurerWithTrashed()->where('treasurer_title', 'Bendahara Pengeluaran')->get();
    if ($bendaharaReceipts->count() > 0) {
        $involvements[] = [
            'type' => 'Bendahara Pengeluaran',
            'count' => $bendaharaReceipts->count(),
            'documents' => $bendaharaReceipts->pluck('receipt_no')->toArray()
        ];
    }

    // Check as Bendahara Pengeluaran Pembantu
    $bendaharaPembantuReceipts = $this->receiptsAsTreasurerWithTrashed()->where('treasurer_title', 'Bendahara Pengeluaran Pembantu')->get();
    if ($bendaharaPembantuReceipts->count() > 0) {
        $involvements[] = [
            'type' => 'Bendahara Pengeluaran Pembantu',
            'count' => $bendaharaPembantuReceipts->count(),
            'documents' => $bendaharaPembantuReceipts->pluck('receipt_no')->toArray()
        ];
    }

    return $involvements;
}

/**
 * Get all receipts where this user is involved as payee (including soft deleted)
 */
public function getAllPayeeInvolvementWithTrashed(): array
{
    $involvements = [];

    // Check as Payee
    $payeeReceipts = $this->receiptsAsPayeeWithTrashed()->get();
    if ($payeeReceipts->count() > 0) {
        $involvements[] = [
            'type' => 'Penerima Pembayaran',
            'count' => $payeeReceipts->count(),
            'documents' => $payeeReceipts->pluck('receipt_no')->toArray()
        ];
    }

    return $involvements;
}

/**
 * Get all document involvements including soft deleted receipts (for foreign key constraint validation)
 */
public function getAllDocumentInvolvementsWithTrashed(): array
{
    $involvements = [];

    // Get nota dinas involvements
    $notaDinasInvolvements = $this->getAllNotaDinasInvolvement();
    if (!empty($notaDinasInvolvements)) {
        $involvements['nota_dinas'] = $notaDinasInvolvements;
    }

    // Get sub kegiatan involvements
    $subKegiatanInvolvements = $this->getAllSubKegiatanInvolvement();
    if (!empty($subKegiatanInvolvements)) {
        $involvements['sub_kegiatan'] = $subKegiatanInvolvements;
    }

    // Get treasurer involvements (including soft deleted)
    $treasurerInvolvements = $this->getAllTreasurerInvolvementWithTrashed();
    if (!empty($treasurerInvolvements)) {
        $involvements['receipts'] = $treasurerInvolvements;
    }

    // Get payee involvements (including soft deleted)
    $payeeInvolvements = $this->getAllPayeeInvolvementWithTrashed();
    if (!empty($payeeInvolvements)) {
        $involvements['receipts'] = array_merge($involvements['receipts'] ?? [], $payeeInvolvements);
    }

    return $involvements;
}
```

### **4. Update Livewire Component**

#### **Users/Index.php - Method Delete:**
```php
public function delete(User $user)
{
    // Check if user has permission to delete users
    if (!PermissionHelper::can('users.delete')) {
        session()->flash('error', 'Anda tidak memiliki izin untuk menghapus user.');
        return;
    }
    
    // Check if user is used in any documents (including soft deleted receipts for foreign key constraint)
    if ($user->isUsedInDocumentsWithTrashed()) {
        $allInvolvements = $user->getAllDocumentInvolvementsWithTrashed();
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
    
    try {
        $user->delete();
        session()->flash('message', 'Data pegawai berhasil dihapus.');
    } catch (\Exception $e) {
        session()->flash('error', 'Gagal menghapus data pegawai. ' . $e->getMessage());
    }
}
```

## Perbedaan Antara Method Lama dan Baru

### **Method Lama (Hanya Dokumen Aktif):**
- `isUsedInDocuments()` - Hanya mengecek dokumen yang belum di-soft delete
- `getAllDocumentInvolvements()` - Hanya menampilkan dokumen yang belum di-soft delete
- **Masalah**: Foreign key constraint masih berlaku untuk data soft deleted

### **Method Baru (Termasuk Soft Deleted):**
- `isUsedInDocumentsWithTrashed()` - Mengecek semua dokumen termasuk yang sudah di-soft delete
- `getAllDocumentInvolvementsWithTrashed()` - Menampilkan semua dokumen termasuk yang sudah di-soft delete
- **Solusi**: Mencegah foreign key constraint violation

## Test Case

### **Test Case 1: User dengan Soft Deleted Receipts**
```php
$user = User::find(45);
echo 'Is used in documents (normal): ' . ($user->isUsedInDocuments() ? 'Yes' : 'No');
// Output: No

echo 'Is used in documents (with trashed): ' . ($user->isUsedInDocumentsWithTrashed() ? 'Yes' : 'No');
// Output: Yes

$involvements = $user->getAllDocumentInvolvementsWithTrashed();
// Output: {
//   "receipts": [
//     {
//       "type": "Bendahara Pengeluaran Pembantu",
//       "count": 2,
//       "documents": [null, null]
//     },
//     {
//       "type": "Penerima Pembayaran", 
//       "count": 3,
//       "documents": [null, null, null]
//     }
//   ]
// }
```

### **Test Case 2: Error Message yang Benar**
```
"Data pegawai tidak dapat dihapus karena masih digunakan dalam kwitansi sebagai: Bendahara Pengeluaran Pembantu (2 kwitansi), Penerima Pembayaran (3 kwitansi)."
```

## Benefits

### **✅ Mencegah Foreign Key Constraint Violation**
- Sistem sekarang mengecek semua data termasuk yang sudah di-soft delete
- Foreign key constraint tidak akan gagal karena validasi dilakukan sebelum delete

### **✅ Error Message yang Informatif**
- User mendapat informasi lengkap tentang dokumen yang masih mereferensi user
- Menampilkan jumlah dokumen dan jenis penggunaan

### **✅ Konsistensi Data**
- Mencegah penghapusan user yang masih memiliki referensi di database
- Mempertahankan integritas referensial database

### **✅ User Experience yang Lebih Baik**
- Error message yang jelas dan informatif
- Tidak ada lagi error database yang membingungkan

## Files yang Dimodifikasi

1. **`app/Models/User.php`**
   - Added `receiptsAsTreasurerWithTrashed()` relation
   - Added `receiptsAsPayeeWithTrashed()` relation
   - Added `isUsedInReceiptsWithTrashed()` method
   - Added `isUsedInDocumentsWithTrashed()` method
   - Added `getAllTreasurerInvolvementWithTrashed()` method
   - Added `getAllPayeeInvolvementWithTrashed()` method
   - Added `getAllDocumentInvolvementsWithTrashed()` method

2. **`app/Livewire/Users/Index.php`**
   - Updated `delete()` method to use `isUsedInDocumentsWithTrashed()`
   - Updated to use `getAllDocumentInvolvementsWithTrashed()`

## Kesimpulan

Perbaikan ini menyelesaikan masalah foreign key constraint violation yang terjadi ketika user masih memiliki referensi di tabel `receipts` meskipun dokumen tersebut sudah di-soft delete. Sistem sekarang mengecek semua data termasuk yang sudah di-soft delete untuk memastikan tidak ada foreign key constraint violation saat menghapus user.
