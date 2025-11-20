# Documents Real-Time Refresh Fix

## Masalah
Setelah menghapus dokumen turunan (SPT, SPPD, Kwitansi, Dokumen Pendukung, Laporan Perjalanan), dokumen induk (Nota Dinas) tidak otomatis menampilkan link hapus. Halaman perlu di-refresh terlebih dahulu untuk melihat perubahan.

## Root Cause
1. **Data Caching**: Data `spt` dan `supportingDocuments` dimuat dengan eager loading dan tersimpan di cache/memory
2. **Event Propagation**: Setelah menghapus dokumen turunan, event `refreshAll` tidak diteruskan ke semua komponen yang terkait
3. **Missing Listeners**: Beberapa komponen tidak memiliki listener untuk event `refreshAll`

## Solusi

### 1. MainPage Component - Dispatch Event
**File:** `app/Livewire/Documents/MainPage.php`

#### **Method yang Diperbarui:**
- `deleteSupportingDocument()` - Menambahkan `$this->dispatch('refreshAll')`
- `deleteReceipt()` - Menambahkan `$this->dispatch('refreshAll')`
- `deleteTripReport()` - Menambahkan `$this->dispatch('refreshAll')`

```php
public function deleteSupportingDocument($documentId)
{
    try {
        $document = \App\Models\SupportingDocument::findOrFail($documentId);
        $documentTitle = $document->title;
        $document->delete();
        
        session()->flash('message', "Dokumen '{$documentTitle}' berhasil dihapus.");
        $this->refreshData();
        
        // NEW: Dispatch event to refresh all child components
        $this->dispatch('refreshAll');
        
    } catch (\Exception $e) {
        session()->flash('error', 'Gagal menghapus dokumen pendukung: ' . $e->getMessage());
    }
}
```

### 2. NotaDinasList Component - Event Listener
**File:** `app/Livewire/Documents/NotaDinasList.php`

#### **Listener yang Diperbarui:**
```php
protected $listeners = [
    'refreshList' => '$refresh',
    'refreshAll' => '$refresh'  // NEW: Added refreshAll listener
];
```

### 3. SptTable Component - Event Listener
**File:** `app/Livewire/Documents/SptTable.php`

#### **Import yang Ditambahkan:**
```php
use Livewire\Attributes\On;
```

#### **Method yang Ditambahkan:**
```php
#[On('refreshAll')]
public function refreshData()
{
    // This will trigger a re-render of the component
}
```

### 4. SppdTable Component - Event Listener
**File:** `app/Livewire/Documents/SppdTable.php`

#### **Import yang Ditambahkan:**
```php
use Livewire\Attributes\On;
```

#### **Method yang Ditambahkan:**
```php
#[On('refreshAll')]
public function refreshData()
{
    // This will trigger a re-render of the component
}
```

## Flow Event Propagation

### **Before Fix:**
```
Delete Supporting Document → MainPage.refreshData() → ❌ No event propagation
Delete Receipt → MainPage.refreshData() → ❌ No event propagation  
Delete Trip Report → MainPage.refreshData() → ❌ No event propagation
```

### **After Fix:**
```
Delete Supporting Document → MainPage.refreshData() → dispatch('refreshAll') → All components refresh
Delete Receipt → MainPage.refreshData() → dispatch('refreshAll') → All components refresh
Delete Trip Report → MainPage.refreshData() → dispatch('refreshAll') → All components refresh
```

## Komponen yang Terpengaruh

### **1. NotaDinasList**
- **Before**: Data `spt` dan `supportingDocuments` tidak diperbarui setelah penghapusan
- **After**: Data diperbarui secara real-time melalui event `refreshAll`

### **2. SptTable**
- **Before**: Tidak ada listener untuk event refresh
- **After**: Memiliki listener `#[On('refreshAll')]` untuk refresh otomatis

### **3. SppdTable**
- **Before**: Tidak ada listener untuk event refresh
- **After**: Memiliki listener `#[On('refreshAll')]` untuk refresh otomatis

### **4. MainPage**
- **Before**: Hanya memanggil `refreshData()` internal
- **After**: Memanggil `refreshData()` + `dispatch('refreshAll')` untuk semua komponen

## Kondisi Delete Link yang Diperbarui

### **Nota Dinas Delete Link:**
```php
@php
    $hasActiveSpt = $notaDinas->spt && $notaDinas->spt->exists;
    $hasActiveSupportingDocs = $notaDinas->supportingDocuments && $notaDinas->supportingDocuments->where('is_active', true)->count() > 0;
@endphp
@if($hasActiveSpt || $hasActiveSupportingDocs)
    <!-- Disabled delete link -->
@else
    <!-- Enabled delete link -->
@endif
```

**Sekarang data ini diperbarui secara real-time setelah penghapusan dokumen turunan.**

## Testing

### **Test Case 1: Delete Supporting Document**
1. Buka halaman documents dengan Nota Dinas yang memiliki dokumen pendukung
2. Hapus dokumen pendukung
3. **Expected**: Link hapus Nota Dinas langsung muncul tanpa refresh

### **Test Case 2: Delete SPT**
1. Buka halaman documents dengan Nota Dinas yang memiliki SPT
2. Hapus SPT
3. **Expected**: Link hapus Nota Dinas langsung muncul tanpa refresh

### **Test Case 3: Delete SPPD**
1. Buka halaman documents dengan SPT yang memiliki SPPD
2. Hapus SPPD
3. **Expected**: Link hapus SPT langsung muncul tanpa refresh

### **Test Case 4: Delete Receipt**
1. Buka halaman documents dengan SPPD yang memiliki kwitansi
2. Hapus kwitansi
3. **Expected**: Link hapus SPPD langsung muncul tanpa refresh

### **Test Case 5: Delete Trip Report**
1. Buka halaman documents dengan SPT yang memiliki laporan perjalanan
2. Hapus laporan perjalanan
3. **Expected**: Link hapus SPT langsung muncul tanpa refresh

## Benefits

### **✅ Real-Time Updates**
- UI diperbarui secara otomatis tanpa perlu refresh manual
- User experience yang lebih smooth dan responsif

### **✅ Consistent State**
- Semua komponen memiliki data yang konsisten
- Tidak ada perbedaan antara data di cache dan database

### **✅ Event-Driven Architecture**
- Menggunakan Livewire event system yang proper
- Mudah untuk menambahkan komponen baru yang perlu refresh

### **✅ Performance**
- Hanya komponen yang perlu diperbarui yang akan re-render
- Tidak ada full page refresh yang tidak perlu

## Files Modified

1. **`app/Livewire/Documents/MainPage.php`**
   - Added `$this->dispatch('refreshAll')` to all delete methods

2. **`app/Livewire/Documents/NotaDinasList.php`**
   - Added `'refreshAll' => '$refresh'` to listeners

3. **`app/Livewire/Documents/SptTable.php`**
   - Added `use Livewire\Attributes\On`
   - Added `#[On('refreshAll')]` method

4. **`app/Livewire/Documents/SppdTable.php`**
   - Added `use Livewire\Attributes\On`
   - Added `#[On('refreshAll')]` method

## Status

✅ **MainPage Event Dispatch** - Semua method delete mengirim event refreshAll  
✅ **NotaDinasList Listener** - Memiliki listener untuk refreshAll  
✅ **SptTable Listener** - Memiliki listener untuk refreshAll  
✅ **SppdTable Listener** - Memiliki listener untuk refreshAll  
✅ **Event Propagation** - Event diteruskan ke semua komponen terkait  
✅ **Real-Time Updates** - UI diperbarui secara otomatis  

Fitur ini memastikan bahwa setelah menghapus dokumen turunan, dokumen induk langsung menampilkan link hapus tanpa perlu refresh halaman.
