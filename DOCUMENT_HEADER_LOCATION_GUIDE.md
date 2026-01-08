# Panduan Lokasi Bagian Kop/Header pada Dokumen PDF

## Ringkasan

Dokumen ini menjelaskan lokasi bagian kop (header) pada setiap file view PDF dalam sistem PD System 2026.

## Dokumen dengan Kop Standar

### 1. Nota Dinas

**File:** `resources/views/nota-dinas/pdf.blade.php`  
**Lokasi Kop:** Baris 57-72

```php
<!-- Header -->
<div class="header">
    <table style="border-bottom: 2px solid black;">
        <tr>
            <td style="width: 22mm;">
                <img src="{{ public_path('logobengkalis.png') }}" alt="Logo" class="logo">
            </td>
            <td class="header-text" >
                <h3>PEMERINTAH KABUPATEN BENGKALIS</h3>
                <h1>{{ \DB::table('org_settings')->value('name') }}</h1>
                <p>{{ \DB::table('org_settings')->value('address') }}</p>
                <p>Telepon  {{ \DB::table('org_settings')->value('phone') }} e-mail : {{ \DB::table('org_settings')->value('email') }}</p>
            </td>
            
            <td style="width: 22mm;"></td>
        </tr>
    </table>
</div>
```

**Styling Header:** Baris 12-20

```css
.header { text-align: center; margin-bottom: 4mm; }
.header table { width: 100%; border-collapse: collapse; }
.header td { vertical-align: middle; padding: 0; }
.logo { height: 20mm; max-width: 100%; }
.header-text { text-align: center; }
.header-text h1 { font-size: 14pt; margin: 0 0 1pt 0; text-transform: uppercase; letter-spacing: 0.5pt; }
.header-text h3 { font-size: 12pt; margin: 0 0 1pt 0; text-transform: uppercase; letter-spacing: 0.5pt; }
.header-text .unit { font-size: 12pt; font-weight: 700; margin: 0 0 2pt 0; text-transform: uppercase; }
.header-text p { font-size: 9pt; margin: 1pt 0; }
```

---

### 2. Surat Perintah Tugas (SPT)

**File:** `resources/views/spt/pdf.blade.php`  
**Lokasi Kop:** Baris 58-73

```php
<!-- Header -->
<div class="header">
    <table style="border-bottom: 2px solid black;">
        <tr>
            <td style="width: 22mm;">
                <img src="{{ public_path('logobengkalis.png') }}" alt="Logo" class="logo">
            </td>
            <td class="header-text" >
                <h3>PEMERINTAH KABUPATEN BENGKALIS</h3>
                <h1>{{ \DB::table('org_settings')->value('name') }}</h1>
                <p>{{ \DB::table('org_settings')->value('address') }}</p>
                <p>Telepon {{ \DB::table('org_settings')->value('phone') }} e-mail : {{ \DB::table('org_settings')->value('email') }}</p>
            </td>
            <td style="width: 22mm;"></td>
        </tr>
    </table>
</div>
```

**Styling Header:** Baris 13-21

```css
.header { text-align: center; margin-bottom: 3mm; }
.header table { width: 100%; border-collapse: collapse; }
.header td { vertical-align: middle; padding: 0; }
.logo { height: 20mm; max-width: 100%; }
.header-text { text-align: center; }
.header-text h1 { font-size: 14pt; margin: 0 0 1pt 0; text-transform: uppercase; letter-spacing: 0.5pt; }
.header-text h3 { font-size: 12pt; margin: 0 0 1pt 0; text-transform: uppercase; letter-spacing: 0.5pt; }
.header-text .unit { font-size: 12pt; font-weight: 700; margin: 0 0 2pt 0; text-transform: uppercase; }
.header-text p { font-size: 9pt; margin: 1pt 0; }
```

---

### 3. Surat Perjalanan Dinas (SPPD)

**File:** `resources/views/sppd/pdf.blade.php`  
**Lokasi Kop:** Baris 100-115

```php
<!-- Header - sama dengan Nota Dinas dan SPT -->
<div class="header">
    <table style="border-bottom: 2px solid black;">
        <tr>
            <td style="width: 22mm;">
                <img src="{{ public_path('logobengkalis.png') }}" alt="Logo" class="logo">
            </td>
            <td class="header-text">
                <h3>PEMERINTAH KABUPATEN BENGKALIS</h3>
                <h1>{{ \DB::table('org_settings')->value('name') }}</h1>
                <p>{{ \DB::table('org_settings')->value('address') }}</p>
                <p>Telepon {{ \DB::table('org_settings')->value('phone') }} e-mail : {{ \DB::table('org_settings')->value('email') }}</p>
            </td>
            <td style="width: 22mm;"></td>
        </tr>
    </table>
</div>
```

**Styling Header:** Baris 14-22

```css
/* Header - sama dengan Nota Dinas dan SPT */
.header { text-align: center; margin-bottom: 3mm; }
.header table { width: 100%; border-collapse: collapse; }
.header td { vertical-align: middle; padding: 0; }
.logo { height: 18mm; max-width: 100%; }
.header-text { text-align: center; }
.header-text h1 { font-size: 13pt; margin: 0 0 1pt 0; text-transform: uppercase; letter-spacing: 0.5pt; }
.header-text h3 { font-size: 11pt; margin: 0 0 1pt 0; text-transform: uppercase; letter-spacing: 0.5pt; }
.header-text .unit { font-size: 11pt; font-weight: 700; margin: 0 0 2pt 0; text-transform: uppercase; }
.header-text p { font-size: 8pt; margin: 1pt 0; }
```

---

### 4. Laporan Hasil Perjalanan Dinas

**File:** `resources/views/trip-reports/pdf.blade.php`  
**Lokasi Kop:** Baris 66-81

```php
<!-- Header -->
<div class="header">
    <table style="border-bottom: 2px solid black;">
        <tr>
            <td style="width: 22mm;">
                <img src="{{ public_path('logobengkalis.png') }}" alt="Logo" class="logo">
            </td>
            <td class="header-text" >
                <h3>PEMERINTAH KABUPATEN BENGKALIS</h3>
                <h1>{{ \DB::table('org_settings')->value('name') }}</h1>
                <p>{{ \DB::table('org_settings')->value('address') }}</p>
                <p>Telepon {{ \DB::table('org_settings')->value('phone') }} e-mail : {{ \DB::table('org_settings')->value('email') }}</p>
            </td>
            <td style="width: 22mm;"></td>
        </tr>
    </table>
</div>
```

**Styling Header:** Baris 13-21

```css
.header { text-align: center; margin-bottom: 4mm; }
.header table { width: 100%; border-collapse: collapse; }
.header td { vertical-align: middle; padding: 0; }
.logo { height: 20mm; max-width: 100%; }
.header-text { text-align: center; }
.header-text h1 { font-size: 14pt; margin: 0 0 1pt 0; text-transform: uppercase; letter-spacing: 0.5pt; }
.header-text h3 { font-size: 12pt; margin: 0 0 1pt 0; text-transform: uppercase; letter-spacing: 0.5pt; }
.header-text .unit { font-size: 12pt; font-weight: 700; margin: 0 0 2pt 0; text-transform: uppercase; }
.header-text p { font-size: 9pt; margin: 1pt 0; }
```

---

## Dokumen Tanpa Kop Standar

### 5. Kwitansi (Receipt)

**File:** `resources/views/receipts/pdf.blade.php`  
**Status:** ❌ Tidak memiliki kop header standar

**Alasan:**
- Kwitansi memiliki format yang berbeda
- Tidak menggunakan kop surat resmi
- Format lebih sederhana untuk bukti pembayaran

---

### 6. Rekap Pegawai

**File:** `resources/views/rekap/pegawai/pdf.blade.php`  
**Status:** ❌ Tidak memiliki kop header standar

**Header Sederhana:** Baris 15-32

```css
.header {
    text-align: center;
    margin-bottom: 25px;
    border-bottom: 2px solid #000;
    padding-bottom: 15px;
}

.header h1 {
    margin: 0;
    font-size: 16px;
    font-weight: bold;
    text-transform: uppercase;
}

.header p {
    margin: 5px 0 0 0;
    font-size: 12px;
}
```

**Alasan:**
- Format rekap tidak memerlukan kop surat resmi
- Fokus pada data tabel
- Header sederhana sudah cukup

---

## Struktur Kop Standar

### Komponen Kop:

1. **Logo Kabupaten**
   - File: `public/logobengkalis.png`
   - Ukuran: 18-20mm tinggi
   - Posisi: Kiri

2. **Text Header**
   - Baris 1: "PEMERINTAH KABUPATEN BENGKALIS"
   - Baris 2: Nama organisasi dari `org_settings`
   - Baris 3: Alamat dari `org_settings`
   - Baris 4: Telepon dan email dari `org_settings`

3. **Garis Pembatas**
   - Border bottom 2px solid black
   - Memisahkan header dari konten

### Data Dinamis dari Database:

```php
{{ \DB::table('org_settings')->value('name') }}      // Nama organisasi
{{ \DB::table('org_settings')->value('address') }}   // Alamat
{{ \DB::table('org_settings')->value('phone') }}     // Telepon
{{ \DB::table('org_settings')->value('email') }}     // Email
```

---

## Perbandingan Ukuran Header

| Dokumen | Logo Height | H1 Font | H3 Font | P Font | Margin Bottom |
|---------|-------------|---------|---------|--------|---------------|
| Nota Dinas | 20mm | 14pt | 12pt | 9pt | 4mm |
| SPT | 20mm | 14pt | 12pt | 9pt | 3mm |
| SPPD | 18mm | 13pt | 11pt | 8pt | 3mm |
| Trip Report | 20mm | 14pt | 12pt | 9pt | 4mm |

**Catatan:** SPPD memiliki font sedikit lebih kecil untuk mengakomodasi konten yang lebih padat.

---

## Cara Mengubah Kop Header

### Jika Ingin Mengubah Logo:

1. Ganti file `public/logobengkalis.png` dengan logo baru
2. Pastikan ukuran logo sekitar 300x300px atau lebih
3. Format: PNG dengan background transparan (recommended)

### Jika Ingin Mengubah Data Organisasi:

1. Buka menu **Settings** → **Organization Settings**
2. Update field:
   - Nama Organisasi
   - Alamat
   - Telepon
   - Email
3. Simpan perubahan
4. Refresh/regenerate PDF

### Jika Ingin Mengubah Styling Kop:

Edit file masing-masing dokumen:

**Untuk Nota Dinas:**
```bash
# File: resources/views/nota-dinas/pdf.blade.php
# CSS: Baris 12-20
# HTML: Baris 57-72
```

**Untuk SPT:**
```bash
# File: resources/views/spt/pdf.blade.php
# CSS: Baris 13-21
# HTML: Baris 58-73
```

**Untuk SPPD:**
```bash
# File: resources/views/sppd/pdf.blade.php
# CSS: Baris 14-22
# HTML: Baris 100-115
```

**Untuk Trip Report:**
```bash
# File: resources/views/trip-reports/pdf.blade.php
# CSS: Baris 13-21
# HTML: Baris 66-81
```

---

## Rekomendasi: Membuat Component Header

### Masalah Saat Ini:

❌ Kop header di-copy paste di 4 file berbeda  
❌ Jika ada perubahan, harus update 4 file  
❌ Risiko inkonsistensi styling  
❌ Maintenance lebih sulit  

### Solusi: Buat Blade Component

**1. Buat Component:**

```bash
# File: resources/views/components/pdf-header.blade.php
```

```php
<div class="header">
    <table style="border-bottom: 2px solid black;">
        <tr>
            <td style="width: 22mm;">
                <img src="{{ public_path('logobengkalis.png') }}" alt="Logo" class="logo">
            </td>
            <td class="header-text">
                <h3>PEMERINTAH KABUPATEN BENGKALIS</h3>
                <h1>{{ \DB::table('org_settings')->value('name') }}</h1>
                <p>{{ \DB::table('org_settings')->value('address') }}</p>
                <p>Telepon {{ \DB::table('org_settings')->value('phone') }} e-mail : {{ \DB::table('org_settings')->value('email') }}</p>
            </td>
            <td style="width: 22mm;"></td>
        </tr>
    </table>
</div>
```

**2. Usage:**

```php
<!-- Di nota-dinas/pdf.blade.php -->
<x-pdf-header />

<!-- Di spt/pdf.blade.php -->
<x-pdf-header />

<!-- Di sppd/pdf.blade.php -->
<x-pdf-header />

<!-- Di trip-reports/pdf.blade.php -->
<x-pdf-header />
```

**Benefits:**
- ✅ Single source of truth
- ✅ Update sekali untuk semua dokumen
- ✅ Konsistensi terjamin
- ✅ Maintenance lebih mudah

---

## Summary

### Dokumen dengan Kop Standar (4):

| # | Dokumen | File | Lokasi Kop | Logo Size | Consistent |
|---|---------|------|------------|-----------|------------|
| 1 | Nota Dinas | `nota-dinas/pdf.blade.php` | 57-72 | 20mm | ✅ |
| 2 | SPT | `spt/pdf.blade.php` | 58-73 | 20mm | ✅ |
| 3 | SPPD | `sppd/pdf.blade.php` | 100-115 | 18mm | ⚠️ Sedikit berbeda |
| 4 | Trip Report | `trip-reports/pdf.blade.php` | 66-81 | 20mm | ✅ |

### Dokumen Tanpa Kop Standar (2):

| # | Dokumen | File | Alasan |
|---|---------|------|--------|
| 5 | Kwitansi | `receipts/pdf.blade.php` | Format berbeda |
| 6 | Rekap Pegawai | `rekap/pegawai/pdf.blade.php` | Header sederhana |

### Action Items:

1. ⚠️ Consider creating `<x-pdf-header />` component
2. ⚠️ Standardize SPPD header size to match others
3. ⚠️ Consider adding header to Receipt PDF (optional)

---

**Last Updated:** January 7, 2026  
**Total PDF Files:** 6  
**Files with Standard Header:** 4  
**Author:** PD System Development Team


