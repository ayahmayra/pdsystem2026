# Panduan Filter Non-Staff di Rekap Pegawai

## Overview
Sistem rekap pegawai telah diperbarui untuk mengecualikan user dengan `is_non_staff = true` dari tampilan rekap. Ini memastikan bahwa hanya pegawai staff yang ditampilkan dalam rekap perjalanan dinas.

## Perubahan yang Diimplementasikan

### 1. **Livewire Component - Rekap Pegawai**
- **File**: `app/Livewire/Rekap/Pegawai.php`
- **Method**: `render()`
- **Perubahan**: Menambahkan filter `->where('users.is_non_staff', false)`

```php
$query = User::with([
    'unit',
    'position.echelon',
    'rank',
    'travelGrade'
])->where('users.is_non_staff', false); // Exclude non-staff users
```

### 2. **Controller - Rekap Pegawai**
- **File**: `app/Http/Controllers/RekapPegawaiController.php`
- **Methods**: `index()` dan `generatePdf()`
- **Perubahan**: Menambahkan filter `->where('is_non_staff', false)` di kedua method

```php
// Di method index()
$query = User::with([
    'unit',
    'position.echelon',
    'rank',
    'travelGrade'
])->where('is_non_staff', false); // Exclude non-staff users

// Di method generatePdf()
$query = User::with([
    'unit',
    'position.echelon',
    'rank',
    'travelGrade'
])->where('is_non_staff', false); // Exclude non-staff users
```

## Logika Filter

### Kondisi yang Dikecualikan
```php
// User dengan is_non_staff = true tidak akan ditampilkan
->where('users.is_non_staff', false)
```

### User yang Ditampilkan
- ✅ **Staff Pegawai**: `is_non_staff = false` atau `is_non_staff = null`
- ❌ **Non-Staff**: `is_non_staff = true`

## Dampak Perubahan

### 1. **Rekap Pegawai Web Interface**
- User non-staff tidak muncul dalam daftar rekap
- Filter dan pencarian tidak akan menemukan user non-staff
- Pagination hanya menghitung user staff

### 2. **PDF Export**
- PDF rekap pegawai tidak mencakup user non-staff
- File PDF hanya berisi pegawai staff yang relevan

### 3. **Data Consistency**
- Rekap hanya menampilkan pegawai yang seharusnya ada dalam sistem perjalanan dinas
- Non-staff (seperti kontraktor, konsultan) tidak tercampur dengan pegawai tetap

## Testing Scenarios

### 1. **User Staff (is_non_staff = false)**
- ✅ Muncul dalam rekap pegawai
- ✅ Dapat difilter dan dicari
- ✅ Muncul dalam PDF export
- ✅ Jadwal perjalanan dinas ditampilkan

### 2. **User Non-Staff (is_non_staff = true)**
- ❌ Tidak muncul dalam rekap pegawai
- ❌ Tidak dapat difilter atau dicari
- ❌ Tidak muncul dalam PDF export
- ❌ Jadwal perjalanan dinas tidak ditampilkan

### 3. **User dengan is_non_staff = null**
- ✅ Muncul dalam rekap pegawai (default dianggap staff)
- ✅ Dapat difilter dan dicari
- ✅ Muncul dalam PDF export

## Business Logic

### Mengapa Non-Staff Dikecualikan?

1. **Rekap Pegawai Khusus Staff**
   - Rekap pegawai dimaksudkan untuk pegawai tetap
   - Non-staff biasanya tidak memiliki jadwal perjalanan dinas reguler

2. **Data Integrity**
   - Mencegah kontraktor/konsultan tercampur dengan pegawai
   - Memastikan rekap hanya berisi pegawai yang relevan

3. **Reporting Accuracy**
   - Laporan perjalanan dinas lebih akurat
   - Fokus pada pegawai yang memiliki kewajiban perjalanan dinas

## Implementation Details

### Database Query
```sql
-- Query yang dihasilkan
SELECT users.* 
FROM users 
WHERE users.is_non_staff = false
-- ... other filters
```

### Filter Chain
```php
// Urutan filter yang diterapkan:
1. Exclude non-staff users
2. Apply unit scope (if applicable)
3. Apply search filters
4. Apply unit/position/rank filters
5. Apply sorting
6. Apply pagination
```

## Migration Considerations

### Existing Data
- User yang sudah ada dengan `is_non_staff = null` akan tetap ditampilkan
- User dengan `is_non_staff = true` akan disembunyikan
- Tidak ada data yang hilang, hanya filter yang diterapkan

### New Users
- User baru dengan `is_non_staff = true` otomatis dikecualikan
- User baru dengan `is_non_staff = false` atau `null` akan ditampilkan

## Monitoring

### Log Changes
```php
// Untuk monitoring, bisa ditambahkan log:
\Log::info("Rekap pegawai filtered: excluding non-staff users");
```

### Performance Impact
- Query menjadi lebih efisien karena filter diterapkan di level database
- Mengurangi jumlah data yang diproses
- Meningkatkan performa pagination

## Troubleshooting

### Issue: User Staff Tidak Muncul
**Penyebab**: User mungkin memiliki `is_non_staff = true`
**Solusi**: Periksa dan update field `is_non_staff` di database

### Issue: User Non-Staff Masih Muncul
**Penyebab**: Cache atau filter tidak diterapkan
**Solusi**: Clear cache dan pastikan filter diterapkan di semua method

### Issue: PDF Export Masih Mengandung Non-Staff
**Penyebab**: Controller PDF tidak diupdate
**Solusi**: Pastikan filter diterapkan di method `generatePdf()`

## Best Practices

### 1. **Data Management**
- Pastikan field `is_non_staff` diisi dengan benar
- Regular audit untuk memastikan data konsisten

### 2. **User Classification**
- Staff: `is_non_staff = false` atau `null`
- Non-Staff: `is_non_staff = true`

### 3. **Reporting**
- Gunakan rekap pegawai untuk staff
- Buat laporan terpisah untuk non-staff jika diperlukan

## Conclusion

Filter non-staff di rekap pegawai telah berhasil diimplementasikan dengan:
- **Consistency**: Filter diterapkan di semua level (Livewire, Controller, PDF)
- **Performance**: Query lebih efisien dengan filter database
- **Accuracy**: Rekap hanya menampilkan pegawai yang relevan
- **Maintainability**: Filter mudah dipahami dan dimodifikasi

Sistem sekarang memastikan bahwa rekap pegawai hanya menampilkan pegawai staff yang memiliki kewajiban perjalanan dinas, memberikan laporan yang lebih akurat dan relevan.
