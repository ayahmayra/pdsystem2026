# Struktur Database Perjadin

## Tabel Utama

### 1. nota_dinas
- **id** (bigint unsigned) - Primary Key
- **doc_no** (varchar(255)) - Nomor dokumen
- **number_is_manual** (tinyint(1)) - Apakah nomor manual
- **number_manual_reason** (text) - Alasan nomor manual
- **number_format_id** (bigint unsigned) - Foreign key ke doc_number_formats
- **number_sequence_id** (bigint unsigned) - Foreign key ke number_sequences
- **number_scope_unit_id** (bigint unsigned) - Foreign key ke units
- **to_user_id** (bigint unsigned) - Foreign key ke users (kepada)
- **from_user_id** (bigint unsigned) - Foreign key ke users (dari)
- **tembusan** (text) - Tembusan
- **nd_date** (date) - Tanggal nota dinas
- **sifat** (varchar(255)) - Sifat dokumen
- **lampiran_count** (int) - Jumlah lampiran
- **hal** (varchar(255)) - Perihal
- **dasar** (text) - Dasar hukum
- **maksud** (text) - Maksud dan tujuan
- **destination_city_id** (bigint unsigned) - Foreign key ke cities (tujuan)
- **origin_place_id** (bigint unsigned) - Foreign key ke org_places (asal)
- **start_date** (date) - Tanggal mulai
- **end_date** (date) - Tanggal selesai
- **trip_type** (enum) - Jenis perjalanan (LUAR_DAERAH, DALAM_DAERAH_GT8H, DALAM_DAERAH_LE8H, DIKLAT)
- **requesting_unit_id** (bigint unsigned) - Foreign key ke units (unit pemohon)
- **status** (enum) - Status (DRAFT, SUBMITTED, APPROVED, REJECTED)
- **created_by** (bigint unsigned) - Foreign key ke users (pembuat)
- **approved_by** (bigint unsigned) - Foreign key ke users (penyetuju)
- **approved_at** (timestamp) - Waktu disetujui
- **notes** (text) - Catatan
- **created_at** (timestamp) - Waktu dibuat
- **updated_at** (timestamp) - Waktu diupdate
- **deleted_at** (timestamp) - Waktu dihapus (soft delete)

### 2. spt (Surat Perintah Tugas)
- **id** (bigint unsigned) - Primary Key
- **doc_no** (varchar(255)) - Nomor dokumen
- **number_is_manual** (tinyint(1)) - Apakah nomor manual
- **number_manual_reason** (text) - Alasan nomor manual
- **number_format_id** (bigint unsigned) - Foreign key ke doc_number_formats
- **number_sequence_id** (bigint unsigned) - Foreign key ke number_sequences
- **number_scope_unit_id** (bigint unsigned) - Foreign key ke units
- **spt_date** (date) - Tanggal SPT
- **nota_dinas_id** (bigint unsigned) - Foreign key ke nota_dinas
- **signed_by_user_id** (bigint unsigned) - Foreign key ke users (penandatangan)
- **assignment_title** (text) - Judul penugasan
- **notes** (text) - Catatan
- **created_at** (timestamp) - Waktu dibuat
- **updated_at** (timestamp) - Waktu diupdate
- **deleted_at** (timestamp) - Waktu dihapus (soft delete)

### 3. sppd (Surat Perintah Perjalanan Dinas)
- **id** (bigint unsigned) - Primary Key
- **doc_no** (varchar(255)) - Nomor dokumen
- **number_is_manual** (tinyint(1)) - Apakah nomor manual
- **number_manual_reason** (text) - Alasan nomor manual
- **number_format_id** (bigint unsigned) - Foreign key ke doc_number_formats
- **number_sequence_id** (bigint unsigned) - Foreign key ke number_sequences
- **number_scope_unit_id** (bigint unsigned) - Foreign key ke units
- **sppd_date** (date) - Tanggal SPPD
- **spt_id** (bigint unsigned) - Foreign key ke spt
- **signed_by_user_id** (bigint unsigned) - Foreign key ke users (penandatangan)
- **assignment_title** (text) - Judul penugasan
- **user_id** (bigint unsigned) - Foreign key ke users (pegawai yang ditugaskan)
- **funding_source** (varchar(255)) - Sumber dana
- **created_at** (timestamp) - Waktu dibuat
- **updated_at** (timestamp) - Waktu diupdate
- **deleted_at** (timestamp) - Waktu dihapus (soft delete)

### 4. trip_reports
- **id** (bigint unsigned) - Primary Key
- **doc_no** (varchar(255)) - Nomor dokumen
- **number_is_manual** (tinyint(1)) - Apakah nomor manual
- **number_manual_reason** (text) - Alasan nomor manual
- **number_format_id** (bigint unsigned) - Foreign key ke doc_number_formats
- **number_sequence_id** (bigint unsigned) - Foreign key ke number_sequences
- **number_scope_unit_id** (bigint unsigned) - Foreign key ke units
- **spt_id** (bigint unsigned) - Foreign key ke spt
- **report_no** (varchar(255)) - Nomor laporan
- **report_date** (date) - Tanggal laporan
- **place_from** (varchar(255)) - Tempat berangkat
- **place_to** (varchar(255)) - Tempat tujuan
- **depart_date** (date) - Tanggal berangkat
- **return_date** (date) - Tanggal kembali
- **activities** (longtext) - Kegiatan yang dilakukan
- **created_by_user_id** (bigint unsigned) - Foreign key ke users (pembuat)
- **created_at** (timestamp) - Waktu dibuat
- **updated_at** (timestamp) - Waktu diupdate
- **deleted_at** (timestamp) - Waktu dihapus (soft delete)

### 5. receipts (Kwitansi)
- **id** (bigint unsigned) - Primary Key
- **doc_no** (varchar(255)) - Nomor dokumen
- **number_is_manual** (tinyint(1)) - Apakah nomor manual
- **number_manual_reason** (text) - Alasan nomor manual
- **number_format_id** (bigint unsigned) - Foreign key ke doc_number_formats
- **number_sequence_id** (bigint unsigned) - Foreign key ke number_sequences
- **number_scope_unit_id** (bigint unsigned) - Foreign key ke units
- **sppd_id** (bigint unsigned) - Foreign key ke sppd
- **travel_grade_id** (bigint unsigned) - Foreign key ke travel_grades
- **receipt_no** (varchar(255)) - Nomor kwitansi
- **receipt_date** (date) - Tanggal kwitansi
- **payee_user_id** (bigint unsigned) - Foreign key ke users (penerima pembayaran)
- **total_amount** (decimal(16,2)) - Total jumlah
- **notes** (text) - Catatan
- **status** (enum) - Status (DRAFT, FINAL)
- **created_at** (timestamp) - Waktu dibuat
- **updated_at** (timestamp) - Waktu diupdate
- **deleted_at** (timestamp) - Waktu dihapus (soft delete)

## Tabel Relasi

### 6. sppd_transport_modes
- **id** (bigint unsigned) - Primary Key
- **sppd_id** (bigint unsigned) - Foreign key ke sppd
- **transport_mode_id** (bigint unsigned) - Foreign key ke transport_modes
- **created_at** (timestamp) - Waktu dibuat
- **updated_at** (timestamp) - Waktu diupdate

### 7. nota_dinas_participants
- **id** (bigint unsigned) - Primary Key
- **nota_dinas_id** (bigint unsigned) - Foreign key ke nota_dinas
- **user_id** (bigint unsigned) - Foreign key ke users
- **role_in_trip** (varchar(255)) - Peran dalam perjalanan
- **created_at** (timestamp) - Waktu dibuat
- **updated_at** (timestamp) - Waktu diupdate

### 8. spt_members
- **id** (bigint unsigned) - Primary Key
- **spt_id** (bigint unsigned) - Foreign key ke spt
- **user_id** (bigint unsigned) - Foreign key ke users
- **created_at** (timestamp) - Waktu dibuat
- **updated_at** (timestamp) - Waktu diupdate

## Tabel Referensi

### 9. users
- **id** (bigint unsigned) - Primary Key
- **name** (varchar(255)) - Nama lengkap
- **email** (varchar(255)) - Email
- **email_verified_at** (timestamp) - Waktu verifikasi email
- **password** (varchar(255)) - Password
- **remember_token** (varchar(100)) - Remember token
- **nip** (varchar(20)) - NIP
- **nik** (varchar(20)) - NIK
- **gelar_depan** (varchar(255)) - Gelar depan
- **gelar_belakang** (varchar(255)) - Gelar belakang
- **phone** (varchar(255)) - Nomor telepon
- **whatsapp** (varchar(255)) - Nomor WhatsApp
- **address** (text) - Alamat
- **unit_id** (bigint unsigned) - Foreign key ke units
- **position_id** (bigint unsigned) - Foreign key ke positions
- **position_desc** (varchar(255)) - Deskripsi jabatan
- **rank_id** (bigint unsigned) - Foreign key ke ranks
- **npwp** (varchar(25)) - NPWP
- **bank_name** (varchar(255)) - Nama bank
- **bank_account_no** (varchar(50)) - Nomor rekening
- **bank_account_name** (varchar(255)) - Nama pemilik rekening
- **birth_date** (date) - Tanggal lahir
- **gender** (varchar(10)) - Jenis kelamin
- **signature_path** (varchar(255)) - Path tanda tangan
- **photo_path** (varchar(255)) - Path foto
- **is_signer** (tinyint(1)) - Apakah penandatangan
- **created_at** (timestamp) - Waktu dibuat
- **updated_at** (timestamp) - Waktu diupdate

### 10. units
- **id** (bigint unsigned) - Primary Key
- **code** (varchar(20)) - Kode unit
- **name** (varchar(255)) - Nama unit
- **parent_id** (bigint unsigned) - Foreign key ke units (parent)
- **created_at** (timestamp) - Waktu dibuat
- **updated_at** (timestamp) - Waktu diupdate

### 11. cities
- **id** (bigint unsigned) - Primary Key
- **kemendagri_code** (varchar(10)) - Kode Kemendagri
- **province_id** (bigint unsigned) - Foreign key ke provinces
- **name** (varchar(120)) - Nama kota
- **type** (enum) - Tipe (KAB, KOTA)
- **created_at** (timestamp) - Waktu dibuat
- **updated_at** (timestamp) - Waktu diupdate

### 12. org_places
- **id** (bigint unsigned) - Primary Key
- **name** (varchar(120)) - Nama tempat
- **city_id** (bigint unsigned) - Foreign key ke cities
- **district_id** (bigint unsigned) - Foreign key ke districts
- **is_org_headquarter** (tinyint(1)) - Apakah kantor pusat
- **created_at** (timestamp) - Waktu dibuat
- **updated_at** (timestamp) - Waktu diupdate

### 13. transport_modes
- **id** (bigint unsigned) - Primary Key
- **code** (varchar(20)) - Kode transportasi
- **name** (varchar(100)) - Nama transportasi
- **created_at** (timestamp) - Waktu dibuat
- **updated_at** (timestamp) - Waktu diupdate

### 14. travel_grades
- **id** (bigint unsigned) - Primary Key
- **code** (varchar(50)) - Kode golongan
- **name** (varchar(200)) - Nama golongan
- **created_at** (timestamp) - Waktu dibuat
- **updated_at** (timestamp) - Waktu diupdate

## Tabel Lainnya

### 15. receipt_lines
- **id** (bigint unsigned) - Primary Key
- **receipt_id** (bigint unsigned) - Foreign key ke receipts
- **component** (enum) - Komponen biaya
- **qty** (decimal(10,2)) - Kuantitas
- **unit** (varchar(255)) - Satuan
- **unit_amount** (decimal(16,2)) - Harga satuan
- **line_total** (decimal(16,2)) - Total baris
- **ref_table** (varchar(255)) - Tabel referensi
- **ref_id** (bigint) - ID referensi
- **cap_amount** (decimal(16,2)) - Batas maksimal
- **is_over_cap** (tinyint(1)) - Apakah melebihi batas
- **over_cap_amount** (decimal(16,2)) - Jumlah kelebihan
- **remark** (varchar(255)) - Keterangan
- **created_at** (timestamp) - Waktu dibuat
- **updated_at** (timestamp) - Waktu diupdate

### 16. supporting_documents
- **id** (bigint unsigned) - Primary Key
- **nota_dinas_id** (bigint unsigned) - Foreign key ke nota_dinas
- **document_type** (varchar(255)) - Jenis dokumen
- **file_path** (varchar(255)) - Path file
- **file_name** (varchar(255)) - Nama file
- **file_size** (bigint) - Ukuran file
- **mime_type** (varchar(255)) - Tipe MIME
- **description** (text) - Deskripsi
- **created_at** (timestamp) - Waktu dibuat
- **updated_at** (timestamp) - Waktu diupdate

## Catatan Penting

1. **Trip Type** disimpan di tabel `nota_dinas` dan diwariskan ke SPPD melalui relasi SPT
2. **Origin Place** dan **Destination City** disimpan di tabel `nota_dinas` dan diwariskan ke SPPD melalui relasi SPT
3. **Start Date** dan **End Date** disimpan di tabel `nota_dinas` dan diwariskan ke SPPD melalui relasi SPT
4. **Transport Modes** menggunakan relasi many-to-many melalui tabel `sppd_transport_modes`
5. **Funding Source** disimpan di tabel `sppd` (spesifik per pegawai)
6. **Status** dihapus dari tabel `spt` dan `trip_reports` untuk menyederhanakan alur kerja
7. **Days Count** dihitung otomatis dari selisih start_date dan end_date di nota_dinas
