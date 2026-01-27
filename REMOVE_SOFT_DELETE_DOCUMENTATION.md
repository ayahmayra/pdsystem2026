# Dokumentasi Penghapusan Soft Delete dari Dokumen Utama

## Alasan Perubahan

Soft delete dihapus dari dokumen utama untuk **menghindari konflik penomoran**. Ketika dokumen di-soft delete, nomor dokumennya masih "terpakai" dan dapat menyebabkan konflik saat membuat dokumen baru dengan nomor yang sama.

## Dokumen yang Terpengaruh

Dokumen utama berikut **tidak lagi menggunakan soft delete** dan akan dihapus secara permanen (hard delete):

1. **NotaDinas** - Nota Dinas
2. **Spt** - Surat Perintah Tugas
3. **Sppd** - Surat Perintah Perjalanan Dinas
4. **Receipt** - Kwitansi/Bukti Penerimaan
5. **TripReport** - Laporan Perjalanan Dinas

## Perubahan yang Dilakukan

### 1. Model Changes

**File yang diubah:**
- `app/Models/NotaDinas.php` - Removed `SoftDeletes` trait
- `app/Models/Spt.php` - Removed `SoftDeletes` trait
- `app/Models/Sppd.php` - Removed `SoftDeletes` trait
- `app/Models/Receipt.php` - Removed `SoftDeletes` trait
- `app/Models/TripReport.php` - Removed `SoftDeletes` trait

**Sebelum:**
```php
use Illuminate\Database\Eloquent\SoftDeletes;

class NotaDinas extends Model
{
    use HasFactory, SoftDeletes;
}
```

**Sesudah:**
```php
class NotaDinas extends Model
{
    use HasFactory;
}
```

### 2. Database Migration

**File:** `database/migrations/2026_01_27_000002_remove_soft_deletes_from_documents.php`

Migration ini menghapus kolom `deleted_at` dari tabel:
- `nota_dinas`
- `spt`
- `sppd`
- `receipts`
- `trip_reports`

**Jalankan migration:**
```bash
php artisan migrate
```

### 3. Model User - Relasi Updates

**File:** `app/Models/User.php`

**Perubahan:**
- Menghapus filter `whereNull('deleted_at')` dari semua relasi
- Menghapus method `receiptsAsTreasurerWithTrashed()` dan `receiptsAsPayeeWithTrashed()`
- Menghapus method `willCauseForeignKeyViolation()`
- Menghapus method `cleanupSoftDeletedReferences()`
- Menghapus method `getAllTreasurerInvolvementWithTrashed()`
- Menghapus method `getAllPayeeInvolvementWithTrashed()`
- Menghapus method `getAllDocumentInvolvementsWithTrashed()`

**Sebelum:**
```php
public function notaDinasTo(): HasMany
{
    return $this->hasMany(NotaDinas::class, 'to_user_id')->whereNull('deleted_at');
}
```

**Sesudah:**
```php
public function notaDinasTo(): HasMany
{
    return $this->hasMany(NotaDinas::class, 'to_user_id');
}
```

## Dampak Perubahan

### ✅ Keuntungan

1. **Tidak ada konflik penomoran** - Nomor dokumen yang dihapus dapat digunakan kembali
2. **Konsistensi data** - Tidak ada dokumen "terhapus" yang masih tersimpan
3. **Query lebih sederhana** - Tidak perlu filter `whereNull('deleted_at')`
4. **Performance** - Query lebih cepat tanpa filter soft delete

### ⚠️ Perhatian

1. **Data yang dihapus tidak dapat dikembalikan** - Pastikan user benar-benar ingin menghapus
2. **Backup penting** - Lakukan backup sebelum menghapus data penting
3. **Confirmation dialog** - Pastikan ada konfirmasi sebelum menghapus dokumen

## Cara Menggunakan

### Menghapus Dokumen

Dokumen sekarang akan dihapus secara permanen:

```php
// NotaDinas
$notaDinas->delete(); // Hard delete, tidak dapat dikembalikan

// SPT
$spt->delete(); // Hard delete

// SPPD
$sppd->delete(); // Hard delete

// Receipt
$receipt->delete(); // Hard delete

// TripReport
$tripReport->delete(); // Hard delete
```

### Query Dokumen

Tidak perlu filter soft delete lagi:

```php
// Sebelum (dengan soft delete)
$notaDinas = NotaDinas::whereNull('deleted_at')->get();

// Sesudah (tanpa soft delete)
$notaDinas = NotaDinas::all();
```

### Relasi User

Relasi sekarang mengembalikan semua dokumen tanpa filter:

```php
// Sebelum
$user->notaDinasTo()->get(); // Hanya yang tidak di-soft delete

// Sesudah
$user->notaDinasTo()->get(); // Semua nota dinas
```

## Migration Steps

1. **Backup database** terlebih dahulu
2. **Jalankan migration:**
   ```bash
   php artisan migrate
   ```
3. **Verifikasi** bahwa kolom `deleted_at` sudah dihapus
4. **Test** fungsi delete pada dokumen

## Rollback

Jika perlu rollback, migration sudah menyediakan method `down()`:

```bash
php artisan migrate:rollback --step=1
```

**Catatan:** Rollback akan menambahkan kembali kolom `deleted_at`, tapi data yang sudah dihapus tidak dapat dikembalikan.

## Checklist

- [x] Removed `SoftDeletes` trait dari 5 model dokumen utama
- [x] Created migration untuk menghapus kolom `deleted_at`
- [x] Updated relasi di model `User`
- [x] Removed method terkait soft delete dari model `User`
- [ ] Jalankan migration di production
- [ ] Test fungsi delete pada semua dokumen
- [ ] Update dokumentasi user jika diperlukan
- [ ] Pastikan backup database dilakukan sebelum migration

## Catatan Penting

1. **Data yang sudah di-soft delete** sebelum migration akan tetap ada di database dengan `deleted_at` tidak null. Setelah migration, kolom ini akan dihapus dan data tersebut akan tetap ada sebagai data normal.

2. **Jika ingin menghapus data yang sudah di-soft delete**, lakukan sebelum migration:
   ```php
   // Hapus data yang sudah di-soft delete
   NotaDinas::onlyTrashed()->forceDelete();
   Spt::onlyTrashed()->forceDelete();
   Sppd::onlyTrashed()->forceDelete();
   Receipt::onlyTrashed()->forceDelete();
   TripReport::onlyTrashed()->forceDelete();
   ```

3. **Nomor dokumen** yang digunakan oleh dokumen yang sudah dihapus sekarang dapat digunakan kembali untuk dokumen baru.

---

**Tanggal Implementasi:** 27 Januari 2026  
**Alasan:** Menghindari konflik penomoran dokumen
