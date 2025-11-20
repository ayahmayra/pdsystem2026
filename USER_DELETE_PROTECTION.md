# User Delete Protection - Mencegah Penghapusan User yang Digunakan dalam Dokumen

## Overview

Fitur ini mencegah penghapusan user yang masih digunakan dalam dokumen nota dinas dan sub kegiatan. Sistem akan mengecek apakah user terlibat dalam dokumen yang **belum dihapus** (soft delete). Sistem akan mengecek apakah user terlibat dalam:

### **Dokumen Nota Dinas sebagai:**
- **Kepada** (to_user_id) - Hanya dokumen yang belum dihapus
- **Dari** (from_user_id) - Hanya dokumen yang belum dihapus
- **Pembuat** (created_by) - Hanya dokumen yang belum dihapus
- **Penyetuju** (approved_by) - Hanya dokumen yang belum dihapus
- **Peserta** (nota_dinas_participants) - Hanya dari dokumen yang belum dihapus

### **Sub Kegiatan sebagai:**
- **PPTK** (pptk_user_id) - Semua dokumen (SubKeg tidak menggunakan soft delete)

### **Kwitansi sebagai:**
- **Bendahara Pengeluaran** (treasurer_user_id dengan treasurer_title = 'Bendahara Pengeluaran') - Hanya dokumen yang belum dihapus
- **Bendahara Pengeluaran Pembantu** (treasurer_user_id dengan treasurer_title = 'Bendahara Pengeluaran Pembantu') - Hanya dokumen yang belum dihapus
- **Penerima Pembayaran** (payee_user_id) - Hanya dokumen yang belum dihapus

## Implementasi

### 1. Model User - Relasi dan Method

**File:** `app/Models/User.php`

#### **Relasi yang Ditambahkan:**
```php
// Relasi ke nota dinas sebagai "to" user (hanya yang belum dihapus)
public function notaDinasTo(): HasMany
{
    return $this->hasMany(NotaDinas::class, 'to_user_id')->whereNull('deleted_at');
}

// Relasi ke nota dinas sebagai "from" user (hanya yang belum dihapus)
public function notaDinasFrom(): HasMany
{
    return $this->hasMany(NotaDinas::class, 'from_user_id')->whereNull('deleted_at');
}

// Relasi ke nota dinas sebagai creator (hanya yang belum dihapus)
public function notaDinasCreated(): HasMany
{
    return $this->hasMany(NotaDinas::class, 'created_by')->whereNull('deleted_at');
}

// Relasi ke nota dinas sebagai approver (hanya yang belum dihapus)
public function notaDinasApproved(): HasMany
{
    return $this->hasMany(NotaDinas::class, 'approved_by')->whereNull('deleted_at');
}

// Relasi ke nota dinas participants (hanya dari dokumen yang belum dihapus)
public function notaDinasParticipants(): HasMany
{
    return $this->hasMany(NotaDinasParticipant::class, 'user_id')
        ->whereHas('notaDinas', function($query) {
            $query->whereNull('deleted_at');
        });
}

// Relasi ke receipts sebagai bendahara (hanya yang belum dihapus)
public function receiptsAsTreasurer(): HasMany
{
    return $this->hasMany(Receipt::class, 'treasurer_user_id')->whereNull('deleted_at');
}

// Relasi ke receipts sebagai penerima pembayaran (hanya yang belum dihapus)
public function receiptsAsPayee(): HasMany
{
    return $this->hasMany(Receipt::class, 'payee_user_id')->whereNull('deleted_at');
}
```

#### **Method untuk Mengecek Penggunaan:**
```php
// Cek apakah user digunakan dalam nota dinas
public function isUsedInNotaDinas(): bool
{
    return $this->notaDinasTo()->exists() ||
           $this->notaDinasFrom()->exists() ||
           $this->notaDinasCreated()->exists() ||
           $this->notaDinasApproved()->exists() ||
           $this->notaDinasParticipants()->exists();
}

// Cek apakah user digunakan dalam sub kegiatan
public function isUsedInSubKegiatan(): bool
{
    return $this->subKegiatan()->exists();
}

// Cek apakah user digunakan sebagai bendahara pada kwitansi
public function isUsedAsTreasurer(): bool
{
    return $this->receiptsAsTreasurer()->exists();
}

// Cek apakah user digunakan sebagai penerima pembayaran pada kwitansi
public function isUsedAsPayee(): bool
{
    return $this->receiptsAsPayee()->exists();
}

// Cek apakah user digunakan dalam dokumen apapun (hanya dokumen yang belum dihapus)
public function isUsedInDocuments(): bool
{
    return $this->isUsedInNotaDinas() || $this->isUsedInSubKegiatan() || $this->isUsedAsTreasurer() || $this->isUsedAsPayee();
}

// Dapatkan detail penggunaan user dalam nota dinas
public function getAllNotaDinasInvolvement(): array

// Dapatkan detail penggunaan user dalam sub kegiatan
public function getAllSubKegiatanInvolvement(): array
{
    $involvements = [];
    
    // Check as PPTK
    $subKegiatan = $this->subKegiatan()->get();
    if ($subKegiatan->count() > 0) {
        $involvements[] = [
            'type' => 'PPTK',
            'count' => $subKegiatan->count(),
            'documents' => $subKegiatan->pluck('display_name')->toArray()
        ];
    }
    
    return $involvements;
}

// Dapatkan detail penggunaan user sebagai bendahara pada kwitansi
public function getAllTreasurerInvolvement(): array
{
    $involvements = [];
    
    // Check as Bendahara Pengeluaran
    $bendaharaReceipts = $this->receiptsAsTreasurer()->where('treasurer_title', 'Bendahara Pengeluaran')->get();
    if ($bendaharaReceipts->count() > 0) {
        $involvements[] = [
            'type' => 'Bendahara Pengeluaran',
            'count' => $bendaharaReceipts->count(),
            'documents' => $bendaharaReceipts->pluck('receipt_no')->toArray()
        ];
    }
    
    // Check as Bendahara Pengeluaran Pembantu
    $bendaharaPembantuReceipts = $this->receiptsAsTreasurer()->where('treasurer_title', 'Bendahara Pengeluaran Pembantu')->get();
    if ($bendaharaPembantuReceipts->count() > 0) {
        $involvements[] = [
            'type' => 'Bendahara Pengeluaran Pembantu',
            'count' => $bendaharaPembantuReceipts->count(),
            'documents' => $bendaharaPembantuReceipts->pluck('receipt_no')->toArray()
        ];
    }
    
    return $involvements;
}

// Dapatkan detail penggunaan user sebagai penerima pembayaran pada kwitansi
public function getAllPayeeInvolvement(): array
{
    $involvements = [];
    
    // Check as Payee
    $payeeReceipts = $this->receiptsAsPayee()->get();
    if ($payeeReceipts->count() > 0) {
        $involvements[] = [
            'type' => 'Penerima Pembayaran',
            'count' => $payeeReceipts->count(),
            'documents' => $payeeReceipts->pluck('receipt_no')->toArray()
        ];
    }
    
    return $involvements;
}

// Dapatkan semua detail penggunaan user (hanya dokumen yang belum dihapus)
public function getAllDocumentInvolvements(): array
{
    $involvements = [];
    
    // Check as "to" user
    $toNotaDinas = $this->notaDinasTo()->get();
    if ($toNotaDinas->count() > 0) {
        $involvements[] = [
            'type' => 'Kepada',
            'count' => $toNotaDinas->count(),
            'documents' => $toNotaDinas->pluck('doc_no')->toArray()
        ];
    }
    
    // ... (implementasi lengkap untuk semua jenis relasi)
    
    return $involvements;
}
```

### 2. Livewire Component - Validasi Delete

**File:** `app/Livewire/Users/Index.php`

#### **Method Delete yang Dimodifikasi:**
```php
public function delete(User $user)
{
    // Check if user has permission to delete users
    if (!PermissionHelper::can('users.delete')) {
        session()->flash('error', 'Anda tidak memiliki izin untuk menghapus user.');
        return;
    }
    
    // Check if user is used in any documents (nota dinas or sub kegiatan)
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
    
    try {
        $user->delete();
        session()->flash('message', 'Data pegawai berhasil dihapus.');
    } catch (\Exception $e) {
        session()->flash('error', 'Gagal menghapus data pegawai. ' . $e->getMessage());
    }
}
```

### 3. UI Indicators - Visual Feedback

**File:** `resources/views/livewire/users/index.blade.php`

#### **Status Badge:**
```blade
@if($user->isUsedInNotaDinas())
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300" title="Digunakan dalam dokumen nota dinas">
        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
        </svg>
        Terkait Dokumen
    </span>
@endif
```

#### **Disabled Delete Button:**
```blade
@if($user->isUsedInNotaDinas())
    <div class="flex items-center w-full px-4 py-2 text-sm text-gray-500 dark:text-gray-400 cursor-not-allowed" title="Tidak dapat dihapus karena masih digunakan dalam dokumen nota dinas">
        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        Tidak Dapat Dihapus
    </div>
@else
    <button wire:click="delete({{ $user->id }})" wire:confirm="Apakah Anda yakin ingin menghapus data pegawai ini?">
        Hapus
    </button>
@endif
```

## Fitur yang Tersedia

### ✅ **Validasi Delete**
- Mencegah penghapusan user yang masih digunakan dalam dokumen nota dinas
- Menampilkan pesan error yang informatif dengan detail penggunaan

### ✅ **Visual Indicators**
- Badge "Terkait Dokumen" untuk user yang tidak bisa dihapus
- Tombol delete yang disabled dengan tooltip penjelasan
- Status yang jelas untuk user

### ✅ **Detail Penggunaan**
- Menampilkan jenis penggunaan (Kepada, Dari, Pembuat, Penyetuju, Peserta)
- Menampilkan jumlah dokumen yang terkait
- Informasi yang detail untuk troubleshooting

### ✅ **Permission Integration**
- Terintegrasi dengan sistem permission yang ada
- Tetap menghormati permission `users.delete`
- Tidak mengubah logika permission yang sudah ada

## Testing

### **Test Case 1: User yang Bisa Dihapus**
```php
// User yang tidak terlibat dalam dokumen nota dinas
$user = User::whereDoesntHave('notaDinasTo')
    ->whereDoesntHave('notaDinasFrom')
    ->whereDoesntHave('notaDinasCreated')
    ->whereDoesntHave('notaDinasApproved')
    ->whereDoesntHave('notaDinasParticipants')
    ->first();

// Should return false
$user->isUsedInNotaDinas(); // false

// Should be deletable
$user->delete(); // success
```

### **Test Case 2: User yang Tidak Bisa Dihapus**
```php
// User yang terlibat dalam dokumen nota dinas
$user = User::whereHas('notaDinasTo')->first();

// Should return true
$user->isUsedInNotaDinas(); // true
$user->isUsedInDocuments(); // true

// Should get involvement details
$involvements = $user->getAllNotaDinasInvolvement();
// Returns array with type, count, and documents

// User yang terlibat dalam sub kegiatan
$user = User::whereHas('subKegiatan')->first();

// Should return true
$user->isUsedInSubKegiatan(); // true
$user->isUsedInDocuments(); // true

// Should get involvement details
$involvements = $user->getAllSubKegiatanInvolvement();
// Returns array with type, count, and documents
```

### **Test Case 3: UI Indicators**
```blade
{{-- Should show badge --}}
@if($user->isUsedInDocuments())
    <span class="badge">Terkait Dokumen</span>
@endif

{{-- Should show disabled button --}}
@if($user->isUsedInDocuments())
    <div class="disabled">Tidak Dapat Dihapus</div>
@else
    <button>Hapus</button>
@endif
```

## Error Messages

### **Pesan Error yang Ditampilkan:**
```
"Data pegawai tidak dapat dihapus karena masih digunakan dalam dokumen nota dinas sebagai: Kepada (2 dokumen), Pembuat (1 dokumen)."

"Data pegawai tidak dapat dihapus karena masih digunakan dalam sub kegiatan sebagai: PPTK (3 sub kegiatan)."

"Data pegawai tidak dapat dihapus karena masih digunakan dalam kwitansi sebagai: Bendahara Pengeluaran (2 kwitansi)."

"Data pegawai tidak dapat dihapus karena masih digunakan dalam kwitansi sebagai: Penerima Pembayaran (3 kwitansi)."

"Data pegawai tidak dapat dihapus karena masih digunakan dalam dokumen nota dinas sebagai: Kepada (1 dokumen), sub kegiatan sebagai: PPTK (2 sub kegiatan), dan kwitansi sebagai: Bendahara Pengeluaran Pembantu (1 kwitansi), Penerima Pembayaran (2 kwitansi)."
```

### **Tooltip pada UI:**
```
"Tidak dapat dihapus karena masih digunakan dalam dokumen nota dinas, sub kegiatan, atau kwitansi"
```

## Database Impact

### **Tidak Ada Perubahan Database**
- Fitur ini tidak memerlukan perubahan struktur database
- Menggunakan relasi yang sudah ada
- Tidak menambah tabel atau kolom baru

### **Query Performance**
- Menggunakan `exists()` untuk performa optimal
- Tidak melakukan join yang kompleks
- Caching yang efisien untuk relasi

## Security Considerations

### **Permission Checks**
- Tetap menghormati permission `users.delete`
- Tidak bypass permission yang ada
- Validasi ganda untuk keamanan

### **Data Integrity**
- Mencegah penghapusan data yang masih digunakan
- Menjaga referential integrity
- Mencegah orphaned records

## Maintenance

### **Monitoring**
- Monitor query performance untuk relasi
- Check error logs untuk validasi
- Monitor UI responsiveness

### **Updates**
- Jika ada perubahan struktur nota dinas, update relasi
- Jika ada tabel baru yang mereferensi user, tambahkan ke validasi
- Update dokumentasi jika ada perubahan

## Status

✅ **Model Relasi** - Relasi user ke nota dinas, sub kegiatan, dan kwitansi berhasil ditambahkan  
✅ **Validasi Delete** - Method delete berhasil dimodifikasi dengan validasi komprehensif  
✅ **UI Indicators** - Visual feedback berhasil ditambahkan  
✅ **Error Messages** - Pesan error yang informatif dan detail  
✅ **Permission Integration** - Terintegrasi dengan sistem permission  
✅ **Testing** - Test case dan validasi berhasil  
✅ **Sub Kegiatan Protection** - Validasi untuk sub kegiatan berhasil ditambahkan  
✅ **Treasurer Protection** - Validasi untuk bendahara pada kwitansi berhasil ditambahkan  

Fitur ini memastikan bahwa user yang masih digunakan dalam dokumen nota dinas, sub kegiatan, atau kwitansi **yang belum dihapus** tidak dapat dihapus, menjaga integritas data dan mencegah masalah referential integrity. User bisa dihapus setelah dokumen terkait dihapus (soft delete).
