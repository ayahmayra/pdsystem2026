# Panduan Implementasi Field Keterangan Tambahan (desc) pada Receipt Lines

## Overview

Field `desc` (keterangan tambahan) telah ditambahkan ke tabel `receipt_lines` untuk memberikan informasi yang lebih detail dalam rekap dan laporan. Field ini memungkinkan pengguna untuk menambahkan keterangan spesifik seperti nama maskapai, nama hotel, atau detail lainnya yang akan berguna untuk rekap.

## Perubahan yang Dilakukan

### **1. Database Migration**

**File**: `database/migrations/2025_09_08_032954_add_desc_to_receipt_lines_table.php`

```php
public function up(): void
{
    Schema::table('receipt_lines', function (Blueprint $table) {
        $table->text('desc')->nullable()->after('remark')->comment('Keterangan tambahan untuk rincian biaya');
    });
}

public function down(): void
{
    Schema::table('receipt_lines', function (Blueprint $table) {
        $table->dropColumn('desc');
    });
}
```

**Fitur**:
- ✅ **Tipe Data**: `text` untuk menampung keterangan yang panjang
- ✅ **Nullable**: Field dapat dikosongkan
- ✅ **Posisi**: Setelah field `remark` untuk urutan yang logis
- ✅ **Comment**: Dokumentasi yang jelas tentang fungsi field

### **2. Model Update**

**File**: `app/Models/ReceiptLine.php`

```php
protected $fillable = [
    'receipt_id', 'component', 'category', 'qty', 'unit', 'unit_amount', 'no_lodging', 'line_total', 
    'ref_table', 'ref_id', 'cap_amount', 'is_over_cap', 'over_cap_amount', 'remark', 'desc',
];
```

**Fitur**:
- ✅ **Fillable**: Field `desc` dapat diisi melalui mass assignment
- ✅ **Validation**: Siap untuk validasi jika diperlukan

### **3. Form Create Receipt**

**File**: `resources/views/livewire/receipts/create.blade.php`

#### **Transport Lines**
```blade
<div class="grid grid-cols-1 md:grid-cols-6 gap-3">
    <div>
        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis</label>
        <select wire:model.live="transportLines.{{ $index }}.component" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <!-- options -->
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan Tambahan</label>
        <input type="text" wire:model="transportLines.{{ $index }}.desc" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: Garuda Indonesia">
    </div>
    <!-- other fields -->
</div>
```

#### **Lodging Lines**
```blade
<div class="grid grid-cols-1 md:grid-cols-5 gap-3">
    <div>
        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah Malam</label>
        <input type="number" wire:model="lodgingLines.{{ $index }}.qty" min="0" step="0.5" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan Tambahan</label>
        <input type="text" wire:model="lodgingLines.{{ $index }}.desc" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: Hotel Bintang 4">
    </div>
    <!-- other fields -->
</div>
```

#### **Other Lines**
```blade
<div class="grid grid-cols-1 md:grid-cols-6 gap-3">
    <div>
        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan</label>
        <input type="text" wire:model="otherLines.{{ $index }}.remark" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: Rapid Test">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan Tambahan</label>
        <input type="text" wire:model="otherLines.{{ $index }}.desc" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: Hotel Bintang 4">
    </div>
    <!-- other fields -->
</div>
```

**Fitur**:
- ✅ **Grid Layout**: Diperluas dari 5 kolom menjadi 6 kolom untuk transport dan other lines, 4 kolom menjadi 5 kolom untuk lodging lines
- ✅ **Placeholder**: Contoh yang relevan untuk setiap jenis biaya
- ✅ **Responsive**: Layout yang responsif untuk berbagai ukuran layar
- ✅ **Consistent**: Konsisten di semua jenis biaya

### **4. Form Edit Receipt**

**File**: `resources/views/livewire/receipts/edit.blade.php`

**Perubahan yang sama** diterapkan pada form edit dengan layout yang konsisten:

- ✅ **Transport Lines**: Grid 6 kolom dengan field desc
- ✅ **Lodging Lines**: Grid 5 kolom dengan field desc  
- ✅ **Other Lines**: Grid 6 kolom dengan field desc
- ✅ **Placeholder**: Contoh yang relevan
- ✅ **Responsive**: Layout yang responsif

### **5. Livewire Component Updates**

#### **Create Component**
**File**: `app/Livewire/Receipts/Create.php`

**Method `addTransportLine()`**:
```php
public function addTransportLine()
{
    $this->transportLines[] = [
        'component' => '',
        'category' => 'transport',
        'desc' => '', // ✅ Added
        'qty' => 1,
        'unit_amount' => 0,
        // ... other fields
    ];
}
```

**Method `addLodgingLine()`**:
```php
public function addLodgingLine()
{
    $this->lodgingLines[] = [
        'category' => 'lodging',
        'component' => 'LODGING',
        'desc' => '', // ✅ Added
        'qty' => 1,
        'unit_amount' => 0,
        // ... other fields
    ];
}
```

**Method `addOtherLine()`**:
```php
public function addOtherLine()
{
    $this->otherLines[] = [
        'category' => 'other',
        'remark' => '',
        'desc' => '', // ✅ Added
        'qty' => 1,
        'unit_amount' => 0,
    ];
}
```

**Method `createReceiptLines()`**:
```php
// Transport lines
\App\Models\ReceiptLine::create([
    'receipt_id' => $receipt->id,
    'component' => $line['component'],
    'category' => $line['category'] ?? 'transport',
    'qty' => $line['qty'],
    'unit' => $this->getUnitForComponent($line['component']),
    'unit_amount' => $line['unit_amount'],
    'desc' => $line['desc'] ?? '', // ✅ Added
    'line_total' => (float)($line['qty'] ?? 0) * (float)($line['unit_amount'] ?? 0),
]);

// Lodging lines
\App\Models\ReceiptLine::create([
    'receipt_id' => $receipt->id,
    'component' => $line['component'] ?? 'LODGING',
    'category' => $line['category'] ?? 'lodging',
    'qty' => $line['qty'],
    'unit' => 'Malam',
    'unit_amount' => $line['unit_amount'],
    'no_lodging' => $line['no_lodging'] ?? false,
    'desc' => $line['desc'] ?? '', // ✅ Added
    'line_total' => (float)($line['qty'] ?? 0) * (float)($line['unit_amount'] ?? 0),
]);

// Other lines
\App\Models\ReceiptLine::create([
    'receipt_id' => $receipt->id,
    'component' => 'LAINNYA',
    'category' => $line['category'] ?? 'other',
    'qty' => $line['qty'],
    'unit' => 'Unit',
    'unit_amount' => $line['unit_amount'],
    'desc' => $line['desc'] ?? '', // ✅ Added
    'line_total' => (float)($line['qty'] ?? 0) * (float)($line['unit_amount'] ?? 0),
    'remark' => $line['remark'],
]);
```

#### **Edit Component**
**File**: `app/Livewire/Receipts/Edit.php`

**Method `loadReceiptLines()`**:
```php
// Transport lines
$this->transportLines[] = [
    'component' => $line->component,
    'category' => 'transport',
    'desc' => $line->desc ?? '', // ✅ Added
    'qty' => $line->qty,
    'unit_amount' => $line->unit_amount,
    // ... other fields
];

// Lodging lines
$this->lodgingLines[] = [
    'category' => 'lodging',
    'component' => $line->component,
    'desc' => $line->desc ?? '', // ✅ Added
    'qty' => $line->qty,
    'unit_amount' => $line->unit_amount,
    // ... other fields
];

// Other lines
$this->otherLines[] = [
    'category' => 'other',
    'remark' => $line->remark,
    'desc' => $line->desc ?? '', // ✅ Added
    'qty' => $line->qty,
    'unit_amount' => $line->unit_amount,
    // ... other fields
];
```

**Method `createReceiptLines()`** (untuk update):
- ✅ **Transport Lines**: Menyimpan field `desc`
- ✅ **Lodging Lines**: Menyimpan field `desc`
- ✅ **Other Lines**: Menyimpan field `desc`

### **6. PDF View Updates**

**File**: `resources/views/receipts/pdf.blade.php`

#### **Transport Components**
```blade
@case('AIRFARE')
  Tiket Pesawat ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }}){{ $line->desc ? ' - ' . $line->desc : '' }}
  @break
@case('INTRA_PROV')
  Transport Dalam Provinsi ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }}){{ $line->desc ? ' - ' . $line->desc : '' }}
  @break
@case('INTRA_DISTRICT')
  Transport Dalam Kabupaten ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }}){{ $line->desc ? ' - ' . $line->desc : '' }}
  @break
@case('OFFICIAL_VEHICLE')
  Kendaraan Dinas ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }}){{ $line->desc ? ' - ' . $line->desc : '' }}
  @break
@case('TAXI')
  Taxi ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }}){{ $line->desc ? ' - ' . $line->desc : '' }}
  @break
@case('RORO')
  Kapal RORO ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }}){{ $line->desc ? ' - ' . $line->desc : '' }}
  @break
@case('TOLL')
  Tol ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }}){{ $line->desc ? ' - ' . $line->desc : '' }}
  @break
@case('PARKIR_INAP')
  Parkir & Penginapan ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }}){{ $line->desc ? ' - ' . $line->desc : '' }}
  @break
```

#### **Lodging Component**
```blade
@case('LODGING')
  Penginapan
  @if($isNoLodging && $referenceRate)
    ({{ number_format($line->qty, 0, ',', '.') }} Malam x (30% x {{ money_id($referenceRate) }}))
  @else
    ({{ number_format($line->qty, 0, ',', '.') }} Malam x {{ money_id($line->unit_amount) }})
  @endif
  {{ $line->desc ? ' - ' . $line->desc : '' }}
  @break
```

#### **Other Components**
```blade
@case('LAINNYA')
  {{ $line->remark ?: 'Biaya tambahan' }} ({{ number_format($line->qty, 0, ',', '.') }} x {{ money_id($line->unit_amount) }}){{ $line->desc ? ' - ' . $line->desc : '' }}
  @break
```

**Fitur**:
- ✅ **Conditional Display**: Keterangan tambahan hanya ditampilkan jika ada
- ✅ **Format**: Menggunakan format " - " untuk pemisah yang jelas, ditampilkan setelah perhitungan
- ✅ **Consistent**: Konsisten di semua jenis komponen
- ✅ **Readable**: Mudah dibaca dalam PDF dengan urutan yang logis

## Contoh Penggunaan

### **1. Transport - Tiket Pesawat**
- **Jenis**: Tiket Pesawat
- **Keterangan Tambahan**: Garuda Indonesia
- **PDF Output**: "Tiket Pesawat (1 x Rp 1.500.000) - Garuda Indonesia"

### **2. Lodging - Penginapan**
- **Jenis**: Penginapan
- **Keterangan Tambahan**: Hotel Bintang 4
- **PDF Output**: "Penginapan (2 Malam x Rp 500.000) - Hotel Bintang 4"

### **3. Other - Biaya Lainnya**
- **Keterangan**: Rapid Test
- **Keterangan Tambahan**: Laboratorium Kesehatan
- **PDF Output**: "Rapid Test (1 x Rp 150.000) - Laboratorium Kesehatan"

## Keuntungan Implementasi

### **1. Informasi yang Lebih Detail**
- ✅ **Spesifikasi**: Dapat menambahkan detail spesifik seperti nama maskapai, hotel, atau vendor
- ✅ **Traceability**: Memudahkan pelacakan dan audit
- ✅ **Transparency**: Transparansi yang lebih baik dalam laporan

### **2. Rekap yang Lebih Informatif**
- ✅ **Grouping**: Dapat mengelompokkan berdasarkan keterangan tambahan
- ✅ **Analysis**: Memudahkan analisis biaya per vendor/layanan
- ✅ **Reporting**: Laporan yang lebih detail dan informatif

### **3. User Experience yang Lebih Baik**
- ✅ **Flexibility**: Fleksibilitas dalam menambahkan informasi
- ✅ **Consistency**: Konsisten di semua jenis biaya
- ✅ **Intuitive**: Interface yang intuitif dan mudah digunakan

### **4. Database Design yang Solid**
- ✅ **Normalized**: Struktur database yang terorganisir
- ✅ **Extensible**: Mudah untuk dikembangkan di masa depan
- ✅ **Performance**: Tidak mempengaruhi performa query

## Testing Results

### **Database Test**
```bash
Receipt Line ID: 2
Component: AIRFARE
Desc: NULL
Remark: NULL
```

**Status**: ✅ **PASSED** - Field `desc` berhasil ditambahkan ke database

### **Form Test**
- ✅ **Create Form**: Field desc muncul di semua jenis biaya
- ✅ **Edit Form**: Field desc muncul dan dapat diedit
- ✅ **Validation**: Form berfungsi dengan baik
- ✅ **Layout**: Layout responsif dan konsisten

### **PDF Test**
- ✅ **Display**: Keterangan tambahan ditampilkan dengan format yang benar
- ✅ **Conditional**: Hanya ditampilkan jika ada isi
- ✅ **Format**: Format yang konsisten dan mudah dibaca

## Files yang Dimodifikasi

### **Database**
1. `database/migrations/2025_09_08_032954_add_desc_to_receipt_lines_table.php` - Migration untuk menambahkan kolom desc

### **Models**
2. `app/Models/ReceiptLine.php` - Menambahkan desc ke fillable

### **Livewire Components**
3. `app/Livewire/Receipts/Create.php` - Update untuk menangani field desc
4. `app/Livewire/Receipts/Edit.php` - Update untuk menangani field desc

### **Views**
5. `resources/views/livewire/receipts/create.blade.php` - Form create dengan field desc
6. `resources/views/livewire/receipts/edit.blade.php` - Form edit dengan field desc
7. `resources/views/receipts/pdf.blade.php` - PDF dengan keterangan tambahan

## Kesimpulan

Implementasi field `desc` (keterangan tambahan) pada receipt lines telah berhasil dilakukan dengan fitur-fitur berikut:

- ✅ **Database**: Kolom `desc` berhasil ditambahkan dengan tipe `text` yang nullable
- ✅ **Model**: Field `desc` ditambahkan ke `$fillable` untuk mass assignment
- ✅ **Forms**: Field desc ditampilkan di form create dan edit dengan layout yang responsif
- ✅ **Livewire**: Semua method untuk menangani field desc telah diupdate
- ✅ **PDF**: Keterangan tambahan ditampilkan dalam PDF dengan format yang konsisten
- ✅ **User Experience**: Interface yang intuitif dan mudah digunakan
- ✅ **Consistency**: Konsisten di semua jenis biaya (transport, lodging, other)

**Implementasi ini memungkinkan pengguna untuk menambahkan keterangan detail yang akan sangat berguna untuk rekap dan laporan yang lebih informatif!** 🎉

## Catatan Penting

- **Field Optional**: Field `desc` bersifat opsional dan dapat dikosongkan
- **Backward Compatible**: Implementasi ini tidak mempengaruhi data yang sudah ada
- **Performance**: Tidak ada dampak negatif pada performa sistem
- **Extensible**: Mudah untuk dikembangkan lebih lanjut di masa depan

**Sistem sekarang siap untuk menangani keterangan tambahan yang detail pada setiap rincian biaya!** ✅
