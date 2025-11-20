# Panduan Instalasi Sistem Perjadin

## Persiapan Instalasi

### 1. Persyaratan Sistem
- PHP 8.1 atau lebih tinggi
- MySQL 8.0 atau lebih tinggi / MariaDB 10.5 atau lebih tinggi
- Composer
- Node.js dan NPM (untuk frontend assets)

### 2. Clone Repository
```bash
git clone <repository-url>
cd perjadin
```

### 3. Install Dependencies
```bash
composer install
npm install
```

### 4. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Konfigurasi Database
Edit file `.env` dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perjadin_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## Instalasi Database

### Opsi 1: Instalasi Bersih (Direkomendasikan)
```bash
# Jalankan migration bersih
php artisan migrate

# Jalankan seeder untuk data awal
php artisan db:seed
```

### Opsi 2: Jika Ada Migration Lama
Jika Anda memiliki migration lama yang menyebabkan konflik:

1. **Backup database terlebih dahulu**
2. **Jalankan script cleanup:**
```bash
chmod +x cleanup_migrations.sh
./cleanup_migrations.sh
```

3. **Jalankan migration fresh:**
```bash
php artisan migrate:fresh --seed
```

## Struktur Migration Bersih

Sistem ini menggunakan **2 migration utama** yang menggantikan semua migration lama:

### 1. `2025_08_28_000008_create_clean_database_structure.php`
Migration ini membuat seluruh struktur database dalam satu file:
- **39 tabel** dengan struktur yang benar
- **Relasi yang tepat** antar tabel
- **Tidak ada duplikasi field**
- **Struktur yang normalisasi**

### 2. `2025_08_28_000009_cleanup_old_migrations.php`
Migration ini membersihkan tabel `migrations` dan menandai hanya migration bersih yang diperlukan.

## Keuntungan Migration Bersih

1. **Tidak ada konflik** - Satu migration menggantikan semua migration lama
2. **Instalasi cepat** - Hanya perlu menjalankan satu migration
3. **Struktur konsisten** - Tidak ada field yang duplikat atau tidak diperlukan
4. **Maintenance mudah** - Mudah untuk deployment di server baru
5. **Rollback sederhana** - Hanya perlu rollback satu migration

## Tabel yang Dibuat

### Tabel Master Data
- `users` - Data pegawai
- `units` - Unit organisasi
- `positions` - Jabatan
- `ranks` - Pangkat
- `echelons` - Eselon
- `provinces` - Provinsi
- `cities` - Kota/Kabupaten
- `districts` - Kecamatan
- `org_places` - Tempat organisasi
- `transport_modes` - Moda transportasi
- `travel_grades` - Golongan perjalanan

### Tabel Referensi Biaya
- `perdiem_rates` - Tarif per diem
- `lodging_caps` - Batas maksimal penginapan
- `representation_rates` - Tarif representasi
- `airfare_refs` - Referensi tarif pesawat
- `intra_province_transport_refs` - Referensi transport dalam provinsi
- `intra_district_transport_refs` - Referensi transport dalam kabupaten
- `official_vehicle_transport_refs` - Referensi transport kendaraan dinas
- `district_perdiem_rates` - Tarif per diem per kecamatan

### Tabel Dokumen
- `nota_dinas` - Nota Dinas (master document)
- `spt` - Surat Perintah Tugas
- `sppd` - Surat Perintah Perjalanan Dinas
- `receipts` - Kwitansi
- `trip_reports` - Laporan Perjalanan

### Tabel Pendukung
- `nota_dinas_participants` - Peserta Nota Dinas
- `spt_members` - Anggota SPT
- `sppd_transport_modes` - Relasi SPPD dengan moda transportasi
- `sppd_itineraries` - Rute perjalanan SPPD
- `sppd_divisum_signoffs` - Tanda tangan SPPD
- `receipt_lines` - Detail kwitansi
- `trip_report_signers` - Penandatangan laporan perjalanan
- `supporting_documents` - Dokumen pendukung

### Tabel Sistem
- `doc_number_formats` - Format nomor dokumen
- `number_sequences` - Sequence nomor dokumen
- `document_numbers` - Nomor dokumen yang sudah dibuat
- `org_settings` - Pengaturan organisasi
- `user_travel_grade_maps` - Mapping user dengan golongan perjalanan
- `atcost_components` - Komponen biaya
- `travel_routes` - Rute perjalanan

## Alur Data

```
Nota Dinas (master data)
    ↓ (origin_place, destination_city, start_date, end_date, trip_type)
SPT (menggunakan data dari Nota Dinas)
    ↓ (assignment_title, signed_by_user)
SPPD (menggunakan data dari SPT + funding_source per pegawai)
    ↓ (transport_modes via relasi many-to-many)
Receipts (kwitansi per SPPD)
```

## Troubleshooting

### Error: Table already exists
Jika terjadi error "Table already exists", jalankan:
```bash
php artisan migrate:fresh --seed
```

### Error: Migration not found
Jika terjadi error "Migration not found", jalankan:
```bash
php artisan migrate:status
php artisan migrate:reset
php artisan migrate
```

### Error: Foreign key constraint
Jika terjadi error foreign key constraint, pastikan urutan tabel sudah benar dalam migration.

## Backup dan Restore

### Backup Database
```bash
mysqldump -u username -p perjadin_db > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restore Database
```bash
mysql -u username -p perjadin_db < backup_file.sql
```

## Deployment ke Production

1. **Backup database production**
2. **Upload kode ke server**
3. **Jalankan migration:**
```bash
php artisan migrate --force
```
4. **Jalankan seeder jika diperlukan:**
```bash
php artisan db:seed --force
```

## Catatan Penting

- **Selalu backup database** sebelum menjalankan migration
- **Test di environment development** terlebih dahulu
- **Periksa foreign key constraints** jika ada error
- **Pastikan user database** memiliki privilege yang cukup
