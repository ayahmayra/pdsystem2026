# Dokumentasi Model User/Pegawai untuk API

## Ringkasan
Dokumen ini menganalisis dan mendokumentasikan semua model yang terkait dengan data User/Pegawai dalam sistem. Dokumentasi ini akan menjadi dasar untuk pembuatan API yang dapat digunakan oleh aplikasi lain untuk mengakses data user dan tabel-tabel terkait.

---

## 1. Model Utama: User

### 1.1 Deskripsi
Model `User` adalah model utama yang merepresentasikan data pegawai/user dalam sistem. Model ini menggunakan Laravel Authentication dan Spatie Permission untuk manajemen role dan permission.

**File:** `app/Models/User.php`

### 1.2 Atribut Utama

#### Identitas Dasar
- `id` - Primary key
- `name` - Nama lengkap pegawai
- `email` - Email pegawai
- `nip` - Nomor Induk Pegawai
- `nik` - Nomor Induk Kependudukan
- `gelar_depan` - Gelar depan (Dr., Prof., dll)
- `gelar_belakang` - Gelar belakang (S.Kom., M.Kom., dll)
- `employee_type` - Tipe pegawai (PNS, PPPK, PPPK PW, Non ASN)

#### Kontak
- `phone` - Nomor telepon
- `whatsapp` - Nomor WhatsApp
- `address` - Alamat

#### Organisasi & Jabatan
- `unit_id` - Foreign key ke tabel `units` (Unit kerja)
- `instansi_id` - Foreign key ke tabel `instansis` (Instansi)
- `position_id` - Foreign key ke tabel `positions` (Jabatan)
- `position_desc` - Deskripsi jabatan tambahan
- `rank_id` - Foreign key ke tabel `ranks` (Pangkat/Golongan)
- `travel_grade_id` - Foreign key ke tabel `travel_grades` (Golongan perjalanan dinas)

#### Keuangan
- `npwp` - Nomor Pokok Wajib Pajak
- `bank_name` - Nama bank
- `bank_account_no` - Nomor rekening bank
- `bank_account_name` - Nama pemilik rekening
- `budget_user_role` - Role anggaran (pengguna_anggaran, kuasa_pengguna_anggaran)

#### Data Pribadi
- `birth_date` - Tanggal lahir
- `gender` - Jenis kelamin
- `signature_path` - Path file tanda tangan
- `photo_path` - Path file foto

#### Status & Flag
- `is_signer` - Apakah user dapat menandatangani dokumen
- `is_non_staff` - Apakah user non-staff
- `password` - Password (hidden)
- `remember_token` - Remember token (hidden)

---

## 2. Model Master/Referensi yang Terkait Langsung

### 2.1 Unit
**File:** `app/Models/Unit.php`

**Relasi dengan User:**
- `User` belongsTo `Unit` (via `unit_id`)
- `Unit` hasMany `User`

**Atribut:**
- `id` - Primary key
- `code` - Kode unit
- `name` - Nama unit
- `parent_id` - Foreign key ke `units` (untuk hierarki unit)

**Kegunaan:** Menyimpan data unit kerja/organisasi tempat pegawai bekerja.

---

### 2.2 Instansi
**File:** `app/Models/Instansi.php`

**Relasi dengan User:**
- `User` belongsTo `Instansi` (via `instansi_id`)
- `Instansi` hasMany `User`

**Atribut:**
- `id` - Primary key
- `name` - Nama instansi
- `code` - Kode instansi
- `address` - Alamat instansi
- `phone` - Telepon instansi
- `website` - Website instansi

**Kegunaan:** Menyimpan data instansi/organisasi utama.

---

### 2.3 Position (Jabatan)
**File:** `app/Models/Position.php`

**Relasi dengan User:**
- `User` belongsTo `Position` (via `position_id`)
- `Position` hasMany `User`
- `Position` belongsTo `Echelon` (via `echelon_id`)

**Atribut:**
- `id` - Primary key
- `name` - Nama jabatan
- `type` - Tipe jabatan
- `echelon_id` - Foreign key ke `echelons`

**Kegunaan:** Menyimpan data jabatan pegawai.

---

### 2.4 Echelon
**File:** `app/Models/Echelon.php`

**Relasi dengan User:**
- `Echelon` hasMany `Position`
- `Position` belongsTo `Echelon`
- `User` → `Position` → `Echelon` (relasi tidak langsung)

**Atribut:**
- `id` - Primary key
- `code` - Kode eselon
- `name` - Nama eselon

**Kegunaan:** Menyimpan data eselon yang terkait dengan jabatan.

---

### 2.5 Rank (Pangkat/Golongan)
**File:** `app/Models/Rank.php`

**Relasi dengan User:**
- `User` belongsTo `Rank` (via `rank_id`)
- `Rank` hasMany `User`

**Atribut:**
- `id` - Primary key
- `code` - Kode pangkat
- `name` - Nama pangkat

**Kegunaan:** Menyimpan data pangkat/golongan pegawai.

---

### 2.6 TravelGrade (Golongan Perjalanan Dinas)
**File:** `app/Models/TravelGrade.php`

**Relasi dengan User:**
- `User` belongsTo `TravelGrade` (via `travel_grade_id`)
- `TravelGrade` hasMany `User`

**Atribut:**
- `id` - Primary key
- `code` - Kode golongan
- `name` - Nama golongan

**Kegunaan:** Menentukan golongan perjalanan dinas yang mempengaruhi tarif perjalanan, akomodasi, dan representasi.

---

## 3. Model Transaksi yang Menggunakan User

### 3.1 NotaDinas
**File:** `app/Models/NotaDinas.php`

**Relasi dengan User:**
- `NotaDinas` belongsTo `User` sebagai `toUser` (via `to_user_id`) - Tujuan nota dinas
- `NotaDinas` belongsTo `User` sebagai `fromUser` (via `from_user_id`) - Penandatangan nota dinas
- `NotaDinas` belongsTo `User` sebagai `createdBy` (via `created_by`) - Pembuat nota dinas
- `NotaDinas` belongsTo `User` sebagai `approvedBy` (via `approved_by`) - Penyetuju nota dinas
- `NotaDinas` hasMany `NotaDinasParticipant` - Peserta nota dinas (yang juga terkait dengan User)

**Kegunaan:** Dokumen nota dinas yang melibatkan user dalam berbagai peran.

**Snapshot Fields:** NotaDinas menyimpan snapshot data user untuk menjaga integritas data historis.

---

### 3.2 NotaDinasParticipant
**File:** `app/Models/NotaDinasParticipant.php`

**Relasi dengan User:**
- `NotaDinasParticipant` belongsTo `User` (via `user_id`)
- `NotaDinasParticipant` belongsTo `NotaDinas`

**Atribut:**
- `nota_dinas_id` - Foreign key ke `nota_dinas`
- `user_id` - Foreign key ke `users`
- `role_in_trip` - Peran dalam perjalanan

**Kegunaan:** Menyimpan daftar peserta dalam nota dinas.

**Snapshot Fields:** Menyimpan snapshot data user untuk menjaga integritas data historis.

---

### 3.3 Spt (Surat Perintah Tugas)
**File:** `app/Models/Spt.php`

**Relasi dengan User:**
- `Spt` belongsTo `User` sebagai `signedByUser` (via `signed_by_user_id`) - Penandatangan SPT
- `Spt` belongsTo `NotaDinas` - SPT dibuat berdasarkan NotaDinas
- `Spt` hasMany `SptMember` - Anggota SPT (yang juga terkait dengan User)

**Kegunaan:** Dokumen Surat Perintah Tugas yang melibatkan user.

**Snapshot Fields:** Menyimpan snapshot data user penandatangan.

---

### 3.4 SptMember
**File:** `app/Models/SptMember.php`

**Relasi dengan User:**
- `SptMember` belongsTo `User` (via `user_id`)
- `SptMember` belongsTo `Spt`

**Atribut:**
- `spt_id` - Foreign key ke `spt`
- `user_id` - Foreign key ke `users`

**Kegunaan:** Menyimpan daftar anggota dalam SPT.

---

### 3.5 Sppd (Surat Perintah Perjalanan Dinas)
**File:** `app/Models/Sppd.php`

**Relasi dengan User:**
- `Sppd` belongsTo `User` sebagai `signedByUser` (via `signed_by_user_id`) - Penandatangan SPPD
- `Sppd` belongsTo `Spt` - SPPD dibuat berdasarkan SPT
- `Sppd` belongsTo `SubKeg` - Sub kegiatan yang terkait

**Kegunaan:** Dokumen Surat Perintah Perjalanan Dinas.

**Snapshot Fields:** Menyimpan snapshot data user penandatangan.

---

### 3.6 SubKeg (Sub Kegiatan)
**File:** `app/Models/SubKeg.php`

**Relasi dengan User:**
- `SubKeg` belongsTo `User` sebagai `pptkUser` (via `pptk_user_id`) - PPTK (Pejabat Pengelola Teknis Kegiatan)
- `SubKeg` belongsTo `Unit` (via `id_unit`)

**Atribut:**
- `id` - Primary key
- `kode_subkeg` - Kode sub kegiatan
- `nama_subkeg` - Nama sub kegiatan
- `id_unit` - Foreign key ke `units`
- `pptk_user_id` - Foreign key ke `users`

**Kegunaan:** Menyimpan data sub kegiatan dengan PPTK yang bertanggung jawab.

---

### 3.7 Receipt (Kwitansi/Bukti Penerimaan)
**File:** `app/Models/Receipt.php`

**Relasi dengan User:**
- `Receipt` belongsTo `User` sebagai `payeeUser` (via `payee_user_id`) - Penerima pembayaran
- `Receipt` belongsTo `User` sebagai `treasurerUser` (via `treasurer_user_id`) - Bendahara
- `Receipt` belongsTo `Sppd` - Kwitansi terkait dengan SPPD

**Kegunaan:** Dokumen kwitansi/bukti penerimaan pembayaran.

**Snapshot Fields:** Menyimpan snapshot data user bendahara.

---

### 3.8 TripReport (Laporan Perjalanan Dinas)
**File:** `app/Models/TripReport.php`

**Relasi dengan User:**
- `TripReport` belongsTo `User` sebagai `createdByUser` (via `created_by_user_id`) - Pembuat laporan
- `TripReport` belongsTo `Spt` - Laporan terkait dengan SPT
- `TripReport` hasMany `TripReportSigner` - Penandatangan laporan

**Kegunaan:** Dokumen laporan perjalanan dinas.

---

## 4. Diagram Relasi Model User

```
User (Model Utama)
│
├─── BelongsTo ────> Unit (unit_id)
│                    └───> Unit.parent (hierarki)
│
├─── BelongsTo ────> Instansi (instansi_id)
│
├─── BelongsTo ────> Position (position_id)
│                    └───> Position.echelon (echelon_id) ────> Echelon
│
├─── BelongsTo ────> Rank (rank_id)
│
├─── BelongsTo ────> TravelGrade (travel_grade_id)
│
├─── HasMany ────> SubKeg (pptk_user_id)
│
├─── HasMany ────> NotaDinas (to_user_id, from_user_id, created_by, approved_by)
│                    └───> NotaDinasParticipant (user_id)
│
├─── HasMany ────> Spt (signed_by_user_id)
│                    └───> SptMember (user_id)
│
├─── HasMany ────> Sppd (signed_by_user_id)
│
├─── HasMany ────> Receipt (payee_user_id, treasurer_user_id)
│
└─── HasMany ────> TripReport (created_by_user_id)
```

---

## 5. Klasifikasi Model untuk API

### 5.1 Model Master/Referensi (Read-Only untuk Aplikasi Lain)
Model-model ini biasanya hanya dibaca oleh aplikasi lain:
- `Unit`
- `Instansi`
- `Position`
- `Echelon`
- `Rank`
- `TravelGrade`

### 5.2 Model User (Read/Write)
Model utama yang dapat diakses untuk:
- Membaca data user
- Membaca data user dengan relasi lengkap
- Update data user (tergantung permission)

### 5.3 Model Transaksi (Read-Only untuk Aplikasi Lain)
Model-model transaksi biasanya hanya dibaca:
- `NotaDinas`
- `NotaDinasParticipant`
- `Spt`
- `SptMember`
- `Sppd`
- `SubKeg`
- `Receipt`
- `TripReport`

---

## 6. Endpoint API yang Disarankan

### 6.1 User Endpoints
```
GET    /api/users                    - List semua user (dengan filter & pagination)
GET    /api/users/{id}               - Detail user dengan relasi lengkap
GET    /api/users/{id}/unit          - Data unit user
GET    /api/users/{id}/position      - Data jabatan user
GET    /api/users/{id}/rank          - Data pangkat user
GET    /api/users/{id}/instansi      - Data instansi user
GET    /api/users/{id}/travel-grade  - Data golongan perjalanan user
GET    /api/users/{id}/sub-kegiatan  - Sub kegiatan yang dikelola user (sebagai PPTK)
GET    /api/users/{id}/nota-dinas     - Nota dinas yang melibatkan user
GET    /api/users/{id}/spt            - SPT yang melibatkan user
GET    /api/users/{id}/sppd           - SPPD yang melibatkan user
GET    /api/users/{id}/receipts       - Kwitansi yang melibatkan user
GET    /api/users/{id}/trip-reports   - Laporan perjalanan yang dibuat user
```

### 6.2 Master Data Endpoints
```
GET    /api/units                    - List semua unit
GET    /api/units/{id}                - Detail unit dengan hierarki
GET    /api/units/{id}/users          - User dalam unit tersebut

GET    /api/instansis                - List semua instansi
GET    /api/instansis/{id}            - Detail instansi
GET    /api/instansis/{id}/users      - User dalam instansi tersebut

GET    /api/positions                 - List semua jabatan
GET    /api/positions/{id}            - Detail jabatan dengan eselon
GET    /api/positions/{id}/users      - User dengan jabatan tersebut

GET    /api/ranks                     - List semua pangkat
GET    /api/ranks/{id}                 - Detail pangkat
GET    /api/ranks/{id}/users          - User dengan pangkat tersebut

GET    /api/travel-grades             - List semua golongan perjalanan
GET    /api/travel-grades/{id}         - Detail golongan perjalanan
GET    /api/travel-grades/{id}/users  - User dengan golongan tersebut
```

---

## 7. Struktur Response yang Disarankan

### 7.1 User Response (Basic)
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john.doe@example.com",
  "nip": "198001012010011001",
  "nik": "3201010101800001",
  "gelar_depan": "Dr.",
  "gelar_belakang": "S.Kom., M.Kom.",
  "employee_type": "PNS",
  "phone": "081234567890",
  "whatsapp": "081234567890",
  "address": "Jl. Contoh No. 123",
  "unit_id": 1,
  "instansi_id": 1,
  "position_id": 1,
  "position_desc": "Kepala Bidang",
  "rank_id": 1,
  "travel_grade_id": 1,
  "npwp": "123456789012000",
  "bank_name": "Bank BCA",
  "bank_account_no": "1234567890",
  "bank_account_name": "John Doe",
  "birth_date": "1980-01-01",
  "gender": "L",
  "is_signer": true,
  "is_non_staff": false,
  "budget_user_role": "pengguna_anggaran"
}
```

### 7.2 User Response (With Relations)
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john.doe@example.com",
  "nip": "198001012010011001",
  "unit": {
    "id": 1,
    "code": "U001",
    "name": "Bidang Teknologi Informasi",
    "parent_id": null
  },
  "instansi": {
    "id": 1,
    "name": "Dinas Komunikasi dan Informatika",
    "code": "DISKOMINFO"
  },
  "position": {
    "id": 1,
    "name": "Kepala Bidang",
    "type": "struktural",
    "echelon": {
      "id": 1,
      "code": "E.II",
      "name": "Eselon II"
    }
  },
  "rank": {
    "id": 1,
    "code": "IV/a",
    "name": "Pembina Utama Muda"
  },
  "travel_grade": {
    "id": 1,
    "code": "A",
    "name": "Golongan A"
  }
}
```

---

## 8. Catatan Penting

### 8.1 Snapshot Fields
Beberapa model transaksi (NotaDinas, Spt, Sppd, Receipt) menyimpan snapshot data user untuk menjaga integritas data historis. Ini berarti:
- Data user yang digunakan dalam dokumen tersimpan snapshot-nya
- Perubahan data user tidak mempengaruhi dokumen yang sudah dibuat
- API harus menyediakan opsi untuk mengambil data snapshot atau data live

### 8.2 Soft Delete
Beberapa model menggunakan soft delete:
- `NotaDinas`
- `Receipt`
- `Spt`
- `Sppd`
- `TripReport`

API harus mempertimbangkan apakah akan menampilkan data yang sudah dihapus atau tidak.

### 8.3 Permission & Authorization
- User dengan role `super-admin` memiliki akses penuh
- User dengan role `admin` dapat mengakses sebagian besar data
- User biasa mungkin hanya dapat mengakses data mereka sendiri
- API harus mengimplementasikan proper authentication dan authorization

### 8.4 Performance Considerations
- Gunakan eager loading untuk relasi yang sering digunakan
- Implementasi pagination untuk list endpoint
- Gunakan filtering dan searching untuk mengurangi data yang dikembalikan
- Pertimbangkan caching untuk data master yang jarang berubah

---

## 9. Rekomendasi Implementasi

### 9.1 API Resources
Gunakan Laravel API Resources untuk:
- Mengontrol struktur response
- Menyembunyikan field sensitif (password, dll)
- Memformat data dengan konsisten
- Menambahkan computed attributes

### 9.2 API Versioning
Pertimbangkan untuk menggunakan API versioning:
- `/api/v1/users`
- `/api/v2/users`

### 9.3 Rate Limiting
Implementasikan rate limiting untuk mencegah abuse.

### 9.4 Documentation
Gunakan tools seperti:
- Laravel API Documentation Generator
- Swagger/OpenAPI
- Postman Collection

---

## 10. Kesimpulan

Model `User` adalah model sentral dalam sistem ini dengan banyak relasi ke:
- **5 Model Master**: Unit, Instansi, Position, Rank, TravelGrade
- **1 Model Indirect**: Echelon (melalui Position)
- **8+ Model Transaksi**: NotaDinas, NotaDinasParticipant, Spt, SptMember, Sppd, SubKeg, Receipt, TripReport

API yang akan dibuat harus mempertimbangkan:
1. Struktur relasi yang kompleks
2. Snapshot fields untuk integritas data historis
3. Soft delete pada beberapa model
4. Permission dan authorization
5. Performance optimization

Dokumentasi ini dapat digunakan sebagai referensi untuk implementasi API yang komprehensif dan efisien.
