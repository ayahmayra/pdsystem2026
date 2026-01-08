# Perbaikan Error PDF Filename pada Receipt Controller

## Ringkasan

Memperbaiki error `InvalidArgumentException` yang terjadi saat generate PDF kwitansi karena filename mengandung karakter "/" dan "\\" yang tidak diperbolehkan.

## Error yang Terjadi

### Error Message:
```
InvalidArgumentException - Internal Server Error
The filename and the fallback cannot contain the "/" and "\" characters.
```

### Stack Trace:
```
vendor/symfony/http-foundation/HeaderUtils.php:187
vendor/barryvdh/laravel-dompdf/src/PDF.php:235
app/Http/Controllers/ReceiptController.php:70
```

### Trigger:
Error terjadi saat mengakses endpoint:
```
GET /receipts/4/pdf
```

### Root Cause:
Filename PDF menggunakan `receipt_no` yang mengandung karakter "/" (contoh: "001/2026"), yang tidak diperbolehkan dalam filename oleh sistem operasi dan HTTP headers.

**Kode Bermasalah (Baris 68 & 129):**
```php
$filename = 'Kwitansi_' . ($receipt->receipt_no ?: 'Manual') . '_' . date('Y-m-d') . '.pdf';
```

Jika `receipt_no = "001/2026"`, maka filename menjadi:
```
Kwitansi_001/2026_2026-01-07.pdf  ❌ Invalid!
```

## Solusi

### Perubahan Kode

**File:** `app/Http/Controllers/ReceiptController.php`

#### 1. Method `generatePdf()` (Baris 67-70)

**Before:**
```php
// Tampilkan preview PDF di browser menggunakan stream() dengan Attachment => false
$filename = 'Kwitansi_' . ($receipt->receipt_no ?: 'Manual') . '_' . date('Y-m-d') . '.pdf';

return $pdf->stream("$filename", ["Attachment" => false]);
```

**After:**
```php
// Tampilkan preview PDF di browser menggunakan stream() dengan Attachment => false
$receiptNo = $receipt->receipt_no ? str_replace(['/', '\\'], '-', $receipt->receipt_no) : 'Manual';
$filename = 'Kwitansi_' . $receiptNo . '_' . date('Y-m-d') . '.pdf';

return $pdf->stream("$filename", ["Attachment" => false]);
```

#### 2. Method `downloadPdf()` (Baris 128-131)

**Before:**
```php
// Download PDF dengan nama file yang sesuai
$filename = 'Kwitansi_' . ($receipt->receipt_no ?: 'Manual') . '_' . date('Y-m-d') . '.pdf';

return $pdf->download($filename);
```

**After:**
```php
// Download PDF dengan nama file yang sesuai
$receiptNo = $receipt->receipt_no ? str_replace(['/', '\\'], '-', $receipt->receipt_no) : 'Manual';
$filename = 'Kwitansi_' . $receiptNo . '_' . date('Y-m-d') . '.pdf';

return $pdf->download($filename);
```

### Penjelasan Perbaikan

**Fungsi `str_replace(['/', '\\'], '-', $receipt->receipt_no)`:**
- Mengganti karakter `/` (slash) dengan `-` (dash)
- Mengganti karakter `\` (backslash) dengan `-` (dash)
- Menghasilkan filename yang valid untuk sistem operasi

**Contoh Hasil:**

| Input Receipt No | Before (❌) | After (✅) |
|-----------------|-------------|------------|
| `001/2026` | `Kwitansi_001/2026_2026-01-07.pdf` | `Kwitansi_001-2026_2026-01-07.pdf` |
| `ND/123/2026` | `Kwitansi_ND/123/2026_2026-01-07.pdf` | `Kwitansi_ND-123-2026_2026-01-07.pdf` |
| `NULL` | `Kwitansi_Manual_2026-01-07.pdf` | `Kwitansi_Manual_2026-01-07.pdf` |

## Konsistensi dengan Controller Lain

Perbaikan ini mengikuti pola yang sudah digunakan di controller lain:

### ✅ NotaDinasController.php (Baris 47)
```php
$filename = 'Nota_Dinas_' . str_replace(['/', '\\'], '-', $notaDinas->doc_no) . '.pdf';
```

### ✅ SptController.php (Baris 33 & 60)
```php
$filename = 'Surat_Perintah_Tugas_' . str_replace(['/', '\\'], '-', $spt->doc_no) . '.pdf';
$filename = 'Surat_Perintah_Tugas_' . str_replace(['/', '\\'], '-', $spt->doc_no) . '_' . date('Y-m-d') . '.pdf';
```

### ✅ SppdController.php (Baris 37)
```php
$filename = 'Surat_Perintah_Perjalanan_Dinas_' . str_replace(['/', '\\'], '-', $sppd->doc_no) . '.pdf';
```

### ❌ ReceiptController.php (Sebelum fix)
```php
$filename = 'Kwitansi_' . ($receipt->receipt_no ?: 'Manual') . '_' . date('Y-m-d') . '.pdf';
// Tidak ada str_replace untuk sanitasi karakter
```

## Testing

### Test Case 1: Receipt dengan Nomor Normal

**Input:**
- `receipt_no = "001/2026"`

**Expected Result:**
- Filename: `Kwitansi_001-2026_2026-01-07.pdf` ✅
- PDF stream berhasil tanpa error ✅
- PDF dapat dibuka di browser ✅

### Test Case 2: Receipt dengan Nomor Kompleks

**Input:**
- `receipt_no = "ND/001/BPKAD/2026"`

**Expected Result:**
- Filename: `Kwitansi_ND-001-BPKAD-2026_2026-01-07.pdf` ✅
- Karakter "/" diganti semua dengan "-" ✅

### Test Case 3: Receipt Tanpa Nomor (Manual)

**Input:**
- `receipt_no = NULL`

**Expected Result:**
- Filename: `Kwitansi_Manual_2026-01-07.pdf` ✅
- Fallback ke "Manual" berfungsi ✅

### Test Case 4: Download PDF

**Action:**
- Akses `/receipts/{id}/download`

**Expected Result:**
- PDF ter-download dengan filename yang benar ✅
- Tidak ada error ✅

## Impact Analysis

### ✅ Positive Impact

1. **Error Fixed**
   - PDF receipt dapat di-generate tanpa error
   - Filename selalu valid untuk semua sistem operasi

2. **Konsistensi**
   - Menggunakan pola yang sama dengan controller lain
   - Code consistency meningkat

3. **User Experience**
   - User dapat preview dan download kwitansi
   - Filename informatif dan mudah dibaca

4. **Backward Compatible**
   - Tidak mengubah database schema
   - Tidak mempengaruhi data existing
   - Hanya mengubah filename output PDF

### ⚠️ No Breaking Changes

- Tidak ada perubahan pada model
- Tidak ada perubahan pada database
- Tidak ada perubahan pada API contract
- Hanya perbaikan internal filename generation

## Rekomendasi

### 1. Standardisasi Filename Sanitization

Buat helper function untuk filename sanitization:

```php
// app/Helpers/FilenameHelper.php
class FilenameHelper
{
    public static function sanitize(?string $filename, string $fallback = 'Document'): string
    {
        if (!$filename) {
            return $fallback;
        }
        
        // Remove invalid filename characters
        $filename = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $filename);
        
        // Remove multiple consecutive dashes
        $filename = preg_replace('/-+/', '-', $filename);
        
        // Trim dashes from start and end
        return trim($filename, '-');
    }
}
```

**Usage:**
```php
$filename = 'Kwitansi_' . FilenameHelper::sanitize($receipt->receipt_no, 'Manual') . '_' . date('Y-m-d') . '.pdf';
```

### 2. Unit Testing

Tambahkan unit test untuk filename generation:

```php
// tests/Unit/FilenameHelperTest.php
public function test_sanitize_removes_slashes()
{
    $result = FilenameHelper::sanitize('001/2026');
    $this->assertEquals('001-2026', $result);
}

public function test_sanitize_removes_backslashes()
{
    $result = FilenameHelper::sanitize('001\\2026');
    $this->assertEquals('001-2026', $result);
}

public function test_sanitize_handles_null()
{
    $result = FilenameHelper::sanitize(null, 'Default');
    $this->assertEquals('Default', $result);
}
```

### 3. Validation pada Model

Tambahkan validation atau mutator pada model Receipt:

```php
// app/Models/Receipt.php
public function getFormattedReceiptNoAttribute(): string
{
    return $this->receipt_no 
        ? str_replace(['/', '\\'], '-', $this->receipt_no)
        : 'Manual';
}
```

**Usage:**
```php
$filename = 'Kwitansi_' . $receipt->formatted_receipt_no . '_' . date('Y-m-d') . '.pdf';
```

## Checklist

✅ **Bug Fixed:** InvalidArgumentException tidak terjadi lagi  
✅ **Code Updated:** ReceiptController.php (2 methods)  
✅ **Consistency:** Mengikuti pola dari controller lain  
✅ **Testing:** Manual testing passed  
✅ **Documentation:** Complete  
✅ **Backward Compatible:** Yes  
✅ **Breaking Changes:** None  

## Related Issues

### Similar Issues yang Sudah Diperbaiki:

- ✅ NotaDinasController: Fixed sejak awal implementasi
- ✅ SptController: Fixed sejak awal implementasi
- ✅ SppdController: Fixed sejak awal implementasi
- ✅ TripReportController: Perlu dicek (jika ada)

### Action Items:

1. ✅ Fix ReceiptController (Done)
2. ⚠️ Review TripReportController untuk issue yang sama
3. ⚠️ Review controller PDF lainnya (jika ada)
4. ⚠️ Implementasi FilenameHelper untuk standardisasi

## Changelog

### Version 1.0.1 - January 7, 2026

**Fixed:**
- ❌ Error InvalidArgumentException pada generate PDF kwitansi
- ❌ Filename PDF mengandung karakter "/" dan "\\"

**Changed:**
- ✅ Filename sanitization menggunakan `str_replace(['/', '\\'], '-', ...)`
- ✅ Konsisten dengan controller lain

**Impact:**
- Users dapat generate dan download PDF kwitansi tanpa error
- Filename lebih readable dengan separator "-"

---

**Last Updated:** January 7, 2026  
**Version:** 1.0.1  
**Fixed By:** PD System Development Team  
**Priority:** High (Blocking feature)  
**Status:** ✅ Resolved

