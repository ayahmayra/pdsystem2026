# Perbaikan Format Jabatan Peserta pada Cetakan Nota Dinas

## Ringkasan

Menambahkan nama unit setelah nama jabatan pada kolom "Jabatan" di tabel peserta yang ditugaskan dalam cetakan PDF Nota Dinas.

## Perubahan

### File: `resources/views/nota-dinas/pdf.blade.php`

**Lokasi:** Baris 205 (Tabel Peserta - Kolom Jabatan)

#### Before ❌
```php
<td>{{ $p->user_position_desc_snapshot ?: ($p->user->position_desc ?: ($p->user->position?->name ?? '-')) }}</td>
```

**Format Output:**
```
Staf
Sekretaris
Kepala Bidang
```

#### After ✅
```php
<td>{{ $p->user_position_desc_snapshot ?: ($p->user->position_desc ?: ($p->user->position?->name ?? '-')) }} {{ $p->user_unit_name_snapshot ?: $p->user->unit?->name ?? '-' }}</td>
```

**Format Output:**
```
Staf Bidang Keuangan
Sekretaris Sekretariat
Kepala Bidang Bidang Anggaran
```

## Konteks Tabel Peserta

### Struktur Tabel di PDF:

| No | Nama/NIP/Pangkat | Jabatan | Keterangan |
|----|------------------|---------|------------|
| 1 | Ahmad<br>Penata (III/c)<br>NIP 123456 | **Staf Bidang Keuangan** | |
| 2 | Siti<br>Penata Muda (III/a)<br>NIP 789012 | **Sekretaris Sekretariat** | |

### Before (❌):
- Kolom Jabatan hanya menampilkan: **"Staf"**, **"Sekretaris"**
- Kurang informatif
- Tidak jelas dari unit mana

### After (✅):
- Kolom Jabatan menampilkan: **"Staf Bidang Keuangan"**, **"Sekretaris Sekretariat"**
- Lebih informatif
- Jelas dari unit mana peserta tersebut

## Data Snapshot yang Digunakan

### Field yang Digunakan:

1. **Jabatan:**
   - `user_position_desc_snapshot` (prioritas 1)
   - `user->position_desc` (prioritas 2)
   - `user->position->name` (prioritas 3)

2. **Unit (Baru ditambahkan):**
   - `user_unit_name_snapshot` (prioritas 1)
   - `user->unit->name` (prioritas 2)

### Database Schema:

**Tabel:** `nota_dinas_participants`

| Field | Type | Description |
|-------|------|-------------|
| `user_position_id_snapshot` | bigint | ID jabatan (snapshot) |
| `user_position_name_snapshot` | varchar | Nama jabatan (snapshot) |
| `user_position_desc_snapshot` | varchar | Deskripsi jabatan (snapshot) |
| `user_unit_id_snapshot` | bigint | ID unit (snapshot) |
| **`user_unit_name_snapshot`** | varchar | **Nama unit (snapshot)** ✅ |

**Migration:** `2025_08_29_093825_add_snapshot_fields_to_nota_dinas_participants_table.php`

## Konsistensi Format

Perubahan ini konsisten dengan format yang digunakan di bagian lain Nota Dinas:

### 1. Bagian "Yth." (Baris 81):
```php
{{ $notaDinas->to_user_position_name_snapshot ?: $notaDinas->toUser?->position?->name ?? '-' }} 
{{ $notaDinas->to_user_unit_name_snapshot ?: $notaDinas->toUser?->unit?->name ?? '-' }}
```
Format: **[Jabatan] [Unit]**

### 2. Bagian "Dari" (Baris 86):
```php
($notaDinas->from_user_position_name_snapshot ?: $notaDinas->fromUser?->position?->name ?? '-') . ' ' . 
($notaDinas->from_user_unit_name_snapshot ?: $notaDinas->fromUser?->unit?->name ?? '-')
```
Format: **[Jabatan] [Unit]**

### 3. Tabel Peserta (Baris 205 - Sekarang):
```php
{{ $p->user_position_desc_snapshot ?: ($p->user->position_desc ?: ($p->user->position?->name ?? '-')) }} 
{{ $p->user_unit_name_snapshot ?: $p->user->unit?->name ?? '-' }}
```
Format: **[Jabatan] [Unit]** ✅

## Contoh Output

### Contoh 1: Pegawai dengan Data Lengkap

**Data:**
- Nama: Ahmad Yani
- NIP: 198001012005011001
- Pangkat: Penata Muda Tk.I (III/b)
- Jabatan: Staf
- Unit: Bidang Keuangan

**Output di PDF:**

| No | Nama/NIP/Pangkat | Jabatan | Keterangan |
|----|------------------|---------|------------|
| 1 | Ahmad Yani<br>Penata Muda Tk.I (III/b)<br>NIP 198001012005011001 | **Staf Bidang Keuangan** | |

### Contoh 2: Pegawai dengan Snapshot Data

**Snapshot Data (saat Nota Dinas dibuat):**
- Position Desc Snapshot: "STAF BPKAD"
- Unit Name Snapshot: "Bidang Anggaran"

**Output di PDF:**

| No | Nama/NIP/Pangkat | Jabatan | Keterangan |
|----|------------------|---------|------------|
| 1 | Siti Nurhaliza<br>Penata (III/c)<br>NIP 199002152012012001 | **STAF BPKAD Bidang Anggaran** | |

### Contoh 3: Multiple Participants

**Output di PDF:**

| No | Nama/NIP/Pangkat | Jabatan | Keterangan |
|----|------------------|---------|------------|
| 1 | Dr. Ahmad, M.Si<br>Pembina (IV/a)<br>NIP 197505102000031001 | **Kepala Badan Sekretariat** | |
| 2 | Ir. Budi Santoso<br>Penata Tk.I (III/d)<br>NIP 198203152005011002 | **Kepala Bidang Bidang Keuangan** | |
| 3 | Siti Aminah, S.E<br>Penata Muda (III/a)<br>NIP 199008202015022001 | **Staf Bidang Anggaran** | |

## Fallback Mechanism

System memiliki fallback bertingkat:

### Untuk Jabatan:
1. `user_position_desc_snapshot` (data snapshot)
2. `user->position_desc` (deskripsi live)
3. `user->position->name` (nama live)
4. `'-'` (default)

### Untuk Unit:
1. `user_unit_name_snapshot` (data snapshot)
2. `user->unit->name` (nama live)
3. `'-'` (default)

## Benefits

### ✅ Informasi Lebih Lengkap
- User langsung tahu peserta dari unit mana
- Tidak perlu cross-reference ke data lain

### ✅ Konsistensi Format
- Format kolom Jabatan sekarang konsisten dengan format Yth. dan Dari
- Semua menggunakan pola: [Jabatan] [Unit]

### ✅ Menggunakan Snapshot
- Data yang ditampilkan adalah data pada saat Nota Dinas dibuat
- Meskipun peserta pindah unit, data historis tetap akurat

### ✅ Professional Output
- PDF lebih informatif dan profesional
- Mudah dibaca dan dipahami

## Impact

### ✅ Positive Impact
- PDF lebih informatif
- User experience meningkat
- Konsistensi format terjaga

### ⚠️ No Breaking Changes
- Tidak mengubah database
- Tidak mengubah model
- Hanya mengubah tampilan PDF
- Backward compatible

## Testing Checklist

### ✅ Test Scenarios:

1. **Nota Dinas dengan Snapshot Data**
   - Verify jabatan + unit ditampilkan dari snapshot
   - Format: `[Position Desc Snapshot] [Unit Name Snapshot]`

2. **Nota Dinas tanpa Snapshot (Live Data)**
   - Verify jabatan + unit ditampilkan dari live data
   - Format: `[Position Name] [Unit Name]`

3. **Multiple Participants**
   - Verify semua peserta menampilkan jabatan + unit
   - Verify tidak ada overlap data

4. **Peserta tanpa Unit**
   - Verify fallback ke "-" jika unit null
   - Format: `[Position] -`

## Related Changes

### Previous Related Fixes:

1. ✅ **NOTA_DINAS_YTH_FIX.md**
   - Fixed "Yth." format to show position + unit
   - Date: January 6, 2026

2. ✅ **RECEIPT_PDF_FILENAME_FIX.md**
   - Fixed PDF filename sanitization
   - Date: January 7, 2026

### This Change:

3. ✅ **NOTA_DINAS_PARTICIPANTS_POSITION_FIX.md**
   - Fixed participants table to show position + unit
   - Date: January 7, 2026

## Status

✅ **Implementation:** Complete  
✅ **Field Available:** `user_unit_name_snapshot` exists in database  
✅ **Backward Compatible:** Yes  
✅ **Breaking Changes:** None  
✅ **Ready for Production:** Yes

---

**Last Updated:** January 7, 2026  
**Version:** 1.0.0  
**File Modified:** `resources/views/nota-dinas/pdf.blade.php` (1 line)  
**Author:** PD System Development Team

