# Sistem Snapshot Data User untuk Dokumen Perjalanan Dinas

## 📋 **Overview**

Sistem snapshot dirancang untuk menjaga integritas data historis dokumen perjalanan dinas. Ketika data user (nama, pangkat, jabatan, unit) berubah di masa depan, dokumen lama tetap menampilkan data yang benar sesuai waktu dokumen dibuat.

## 🏗️ **Arsitektur Sistem**

### **1. Nota Dinas sebagai Master Snapshot**
- **Nota Dinas** adalah dokumen utama yang menyimpan snapshot data user
- Semua dokumen terkait (SPT, SPPD, Trip Report, Receipt) merujuk ke snapshot Nota Dinas
- Pendekatan ini memastikan konsistensi data dan efisiensi storage

### **2. Struktur Snapshot Data**

#### **A. Snapshot untuk Penandatangan (from_user)**
```sql
-- Nota Dinas table
from_user_name_snapshot
from_user_gelar_depan_snapshot
from_user_gelar_belakang_snapshot
from_user_nip_snapshot
from_user_unit_id_snapshot
from_user_unit_name_snapshot
from_user_position_id_snapshot
from_user_position_name_snapshot
from_user_position_desc_snapshot
from_user_rank_id_snapshot
from_user_rank_name_snapshot
from_user_rank_code_snapshot
```

#### **B. Snapshot untuk Tujuan (to_user)**
```sql
-- Nota Dinas table
to_user_name_snapshot
to_user_gelar_depan_snapshot
to_user_gelar_belakang_snapshot
to_user_nip_snapshot
to_user_unit_id_snapshot
to_user_unit_name_snapshot
to_user_position_id_snapshot
to_user_position_name_snapshot
to_user_position_desc_snapshot
to_user_rank_id_snapshot
to_user_rank_name_snapshot
to_user_rank_code_snapshot
```

#### **C. Snapshot untuk Peserta**
```sql
-- Nota Dinas Participants table
user_name_snapshot
user_gelar_depan_snapshot
user_gelar_belakang_snapshot
user_nip_snapshot
user_unit_id_snapshot
user_unit_name_snapshot
user_position_id_snapshot
user_position_name_snapshot
user_position_desc_snapshot
user_rank_id_snapshot
user_rank_name_snapshot
user_rank_code_snapshot
```

## 🔧 **Implementasi Model**

### **1. NotaDinas Model**

#### **Accessor Methods**
```php
// Mengambil snapshot data penandatangan
$notaDinas->from_user_snapshot

// Mengambil snapshot data tujuan
$notaDinas->to_user_snapshot
```

#### **Snapshot Creation**
```php
// Membuat snapshot saat Nota Dinas disimpan
$notaDinas->createUserSnapshot();
```

### **2. NotaDinasParticipant Model**

#### **Accessor Methods**
```php
// Mengambil snapshot data peserta
$participant->user_snapshot
```

#### **Snapshot Creation**
```php
// Membuat snapshot saat peserta ditambahkan
$participant->createUserSnapshot();
```

### **3. Dokumen Terkait (SPT, SPPD, Trip Report, Receipt)**

#### **SPT Model**
```php
// Mengambil snapshot peserta dari Nota Dinas
$spt->getParticipantsSnapshot();

// Mengambil snapshot penandatangan dari Nota Dinas
$spt->getFromUserSnapshot();
$spt->getToUserSnapshot();
```

#### **SPPD Model**
```php
// Mengambil snapshot user SPPD dari Nota Dinas
$sppd->getUserSnapshot();

// Mengambil snapshot penandatangan dari Nota Dinas
$sppd->getSignedByUserSnapshot();
```

#### **TripReport Model**
```php
// Mengambil snapshot peserta dari Nota Dinas
$tripReport->getParticipantsSnapshot();

// Mengambil snapshot pembuat laporan dari Nota Dinas
$tripReport->getCreatedByUserSnapshot();
```

#### **Receipt Model**
```php
// Mengambil snapshot penerima pembayaran dari Nota Dinas
$receipt->getPayeeUserSnapshot();
```

## 📊 **Cara Kerja Sistem**

### **1. Saat Membuat Nota Dinas**
```php
// 1. Nota Dinas dibuat
$notaDinas = NotaDinas::create([...]);

// 2. Snapshot data user dibuat otomatis
$notaDinas->createUserSnapshot();

// 3. Snapshot data peserta dibuat
foreach ($participants as $participant) {
    $participant->createUserSnapshot();
}
```

### **2. Saat Mengakses Data di Dokumen Terkait**
```php
// SPT mengakses snapshot dari Nota Dinas
$participants = $spt->getParticipantsSnapshot();

// SPPD mengakses snapshot dari Nota Dinas
$userData = $sppd->getUserSnapshot();

// Trip Report mengakses snapshot dari Nota Dinas
$participants = $tripReport->getParticipantsSnapshot();

// Receipt mengakses snapshot dari Nota Dinas
$payeeData = $receipt->getPayeeUserSnapshot();
```

### **3. Fallback Logic**
```php
// Jika snapshot tidak ada, gunakan data live
$name = $participant->user_name_snapshot ?: $participant->user?->name;
$position = $participant->user_position_name_snapshot ?: $participant->user?->position?->name;
```

## 🎯 **Keuntungan Sistem**

### **1. Integritas Data Historis**
- Dokumen lama tetap menampilkan data yang benar
- Tidak terpengaruh perubahan data user di masa depan
- Audit trail yang akurat

### **2. Konsistensi Data**
- Semua dokumen terkait menggunakan data yang sama
- Tidak ada inkonsistensi antar dokumen
- Single source of truth

### **3. Efisiensi Storage**
- Tidak ada duplikasi snapshot di setiap tabel
- Storage yang optimal
- Maintenance yang mudah

### **4. Fleksibilitas**
- Bisa menggunakan snapshot atau data live
- Fallback mechanism yang robust
- Mudah untuk debugging

## 📝 **Contoh Penggunaan**

### **1. Template PDF Nota Dinas**
```php
// Menggunakan snapshot data
{{ $notaDinas->from_user_name_snapshot ?: $notaDinas->fromUser?->name }}
{{ $notaDinas->from_user_position_name_snapshot ?: $notaDinas->fromUser?->position?->name }}
{{ $notaDinas->from_user_unit_name_snapshot ?: $notaDinas->fromUser?->unit?->name }}
```

### **2. Template PDF SPT**
```php
// Menggunakan snapshot dari Nota Dinas
@foreach($spt->getParticipantsSnapshot() as $participant)
    {{ $participant['name'] }}
    {{ $participant['position_name'] }}
    {{ $participant['unit_name'] }}
@endforeach
```

### **3. Template PDF SPPD**
```php
// Menggunakan snapshot dari Nota Dinas
{{ $sppd->getUserSnapshot()['name'] }}
{{ $sppd->getUserSnapshot()['position_name'] }}
{{ $sppd->getUserSnapshot()['unit_name'] }}
```

## 🔄 **Migration dan Seeding**

### **1. Migration Files**
- `add_snapshot_fields_to_nota_dinas_table.php`
- `add_snapshot_fields_to_nota_dinas_participants_table.php`
- `populate_snapshot_data_for_existing_nota_dinas.php`
- `populate_snapshot_data_for_existing_participants.php`

### **2. Data Population**
```bash
# Menjalankan migration
php artisan migrate

# Data snapshot akan terisi otomatis untuk data existing
```

## 🛠️ **Maintenance**

### **1. Backup Snapshot Data**
```sql
-- Backup snapshot data sebelum maintenance
SELECT * FROM nota_dinas WHERE from_user_name_snapshot IS NOT NULL;
SELECT * FROM nota_dinas_participants WHERE user_name_snapshot IS NOT NULL;
```

### **2. Recreate Snapshot (jika diperlukan)**
```php
// Recreate snapshot untuk Nota Dinas tertentu
$notaDinas = NotaDinas::find($id);
$notaDinas->createUserSnapshot();

foreach ($notaDinas->participants as $participant) {
    $participant->createUserSnapshot();
}
```

### **3. Cleanup (jika diperlukan)**
```sql
-- Clear snapshot data (hati-hati!)
UPDATE nota_dinas SET 
    from_user_name_snapshot = NULL,
    from_user_position_name_snapshot = NULL,
    -- ... other fields
WHERE id = ?;
```

## ⚠️ **Best Practices**

### **1. Selalu Gunakan Snapshot di Template**
```php
// ✅ Benar - menggunakan snapshot
{{ $notaDinas->from_user_name_snapshot ?: $notaDinas->fromUser?->name }}

// ❌ Salah - langsung menggunakan data live
{{ $notaDinas->fromUser?->name }}
```

### **2. Test Fallback Mechanism**
```php
// Test dengan data yang tidak memiliki snapshot
$notaDinas->from_user_name_snapshot = null;
$name = $notaDinas->from_user_name_snapshot ?: $notaDinas->fromUser?->name;
```

### **3. Monitor Snapshot Creation**
```php
// Log snapshot creation untuk debugging
Log::info('Snapshot created for Nota Dinas: ' . $notaDinas->id);
```

## 🎉 **Kesimpulan**

Sistem snapshot ini memberikan solusi yang robust untuk menjaga integritas data historis dokumen perjalanan dinas. Dengan Nota Dinas sebagai master snapshot, semua dokumen terkait akan konsisten dan tidak terpengaruh perubahan data user di masa depan.
