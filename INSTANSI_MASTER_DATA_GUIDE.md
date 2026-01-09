# Master Data Instansi - Panduan Implementasi

## Deskripsi
Fitur ini menambahkan master data **Instansi** yang dapat digunakan sebagai atribut dari pegawai. Jika pegawai memiliki instansi, maka nama instansi akan digunakan. Jika tidak, sistem akan menggunakan nama organisasi dari pengaturan sistem (org_settings).

## Perubahan yang Dilakukan

### 1. Database

#### Tabel Baru: `instansis`
```sql
- id (bigint, primary key)
- name (varchar) - Nama instansi (wajib)
- code (varchar, 50) - Kode instansi (opsional)
- address (text) - Alamat instansi (opsional)
- phone (varchar) - Telepon instansi (opsional)
- website (varchar) - Website instansi (opsional)
- created_at (timestamp)
- updated_at (timestamp)
```

#### Perubahan Tabel `users`
- Menambahkan kolom `instansi_id` (bigint, nullable, foreign key ke tabel `instansis`)
- Kolom ini diletakkan setelah kolom `unit_id`

### 2. Model

#### Model Baru: `App\Models\Instansi`
**Lokasi:** `app/Models/Instansi.php`

**Fitur:**
- Fillable: name, code, address, phone, website
- Relasi: `users()` - HasMany ke User
- Method: `fullName()` - Mengembalikan kode dan nama instansi

#### Update Model `App\Models\User`
**Lokasi:** `app/Models/User.php`

**Perubahan:**
- Menambahkan `instansi_id` ke array fillable
- Menambahkan relasi `instansi()` - BelongsTo ke Instansi
- Menambahkan method `getInstansiName()` - Mengembalikan nama instansi pegawai atau nama organisasi default

**Cara Penggunaan:**
```php
// Mendapatkan nama instansi pegawai
$instansiName = $user->getInstansiName();
// Akan return nama instansi jika ada, atau nama organisasi dari org_settings jika null
```

### 3. Controller

#### Controller Baru: `App\Http\Controllers\InstansiController`
**Lokasi:** `app/Http/Controllers/InstansiController.php`

**Methods:**
- `index()` - Menampilkan daftar instansi dengan fitur search dan pagination
- `create()` - Menampilkan form tambah instansi
- `store()` - Menyimpan data instansi baru
- `edit($instansi)` - Menampilkan form edit instansi
- `update($instansi)` - Mengupdate data instansi
- `destroy($instansi)` - Menghapus data instansi (dengan validasi: tidak bisa dihapus jika masih digunakan oleh pegawai)

### 4. Routes

**Lokasi:** `routes/web.php`

**Routes yang ditambahkan:**
```php
Route::get('instansis', [InstansiController::class, 'index'])->name('instansis.index');
Route::get('instansis/create', [InstansiController::class, 'create'])->name('instansis.create');
Route::post('instansis', [InstansiController::class, 'store'])->name('instansis.store');
Route::get('instansis/{instansi}/edit', [InstansiController::class, 'edit'])->name('instansis.edit');
Route::put('instansis/{instansi}', [InstansiController::class, 'update'])->name('instansis.update');
Route::delete('instansis/{instansi}', [InstansiController::class, 'destroy'])->name('instansis.destroy');
```

### 5. Views

#### Views Baru
**Lokasi:** `resources/views/instansis/`

1. **index.blade.php** - Halaman daftar instansi
   - Menampilkan tabel instansi dengan kolom: Kode, Nama, Alamat, Kontak, Jumlah Pegawai, Aksi
   - Fitur search untuk mencari instansi berdasarkan kode, nama, atau alamat
   - Pagination
   - Button untuk tambah, edit, dan hapus instansi

2. **create.blade.php** - Form tambah instansi
   - Field: Kode (opsional), Nama (wajib), Alamat (opsional), Telepon (opsional), Website (opsional)

3. **edit.blade.php** - Form edit instansi
   - Field yang sama dengan create

#### Update Views Pegawai
**Lokasi:** 
- `resources/views/livewire/users/create.blade.php`
- `resources/views/livewire/users/edit.blade.php`

**Perubahan:**
- Menambahkan dropdown Instansi pada section "Informasi Organisasi"
- Dropdown instansi bersifat opsional
- Menampilkan keterangan: "Jika dikosongkan, maka instansi pegawai akan mengikuti nama organisasi pada pengaturan sistem"

#### Update Halaman Master Data
**Lokasi:** `resources/views/master-data/index.blade.php`

**Perubahan:**
- Menambahkan card "Data Instansi" setelah card "Data Unit"
- Hanya ditampilkan untuk role Admin dan Super Admin (tidak untuk Bendahara)

### 6. Livewire Components

#### Update Component: `App\Livewire\Users\Create`
**Lokasi:** `app/Livewire/Users/Create.php`

**Perubahan:**
- Menambahkan property `instansi_id`
- Menambahkan mutator `setInstansiIdProperty()`
- Menambahkan validasi `instansi_id` (nullable, exists:instansis,id)
- Menambahkan `instansi_id` ke array nullable fields
- Menambahkan `instansis` ke render method

#### Update Component: `App\Livewire\Users\Edit`
**Lokasi:** `app/Livewire/Users/Edit.php`

**Perubahan:**
- Menambahkan property `instansi_id`
- Menambahkan mutator `setInstansiIdProperty()`
- Menambahkan validasi `instansi_id` (nullable, exists:instansis,id)
- Menambahkan `instansi_id` ke array nullable fields
- Menambahkan `instansis` ke render method
- Set `instansi_id` dari user pada method mount

## Cara Menggunakan

### Mengelola Data Instansi

1. **Mengakses Halaman Instansi:**
   - Login sebagai Admin atau Super Admin
   - Buka menu "Master Data"
   - Klik "Data Instansi"

2. **Menambah Instansi Baru:**
   - Klik tombol "Tambah Instansi"
   - Isi form (minimal Nama Instansi wajib diisi)
   - Klik "Simpan"

3. **Mengedit Instansi:**
   - Pada halaman daftar instansi, klik icon edit (pensil) pada instansi yang ingin diubah
   - Update data yang diperlukan
   - Klik "Update"

4. **Menghapus Instansi:**
   - Pada halaman daftar instansi, klik icon hapus (tempat sampah)
   - Konfirmasi penghapusan
   - **Catatan:** Instansi tidak dapat dihapus jika masih digunakan oleh pegawai

### Menambahkan Instansi pada Pegawai

1. **Saat Membuat Pegawai Baru:**
   - Buka "Data Pegawai" > "Tambah Pegawai"
   - Pada section "Informasi Organisasi", pilih instansi dari dropdown "Instansi"
   - Jika tidak dipilih (dikosongkan), pegawai akan menggunakan nama organisasi default

2. **Saat Mengedit Pegawai:**
   - Buka "Data Pegawai" > Edit pegawai yang ingin diubah
   - Pada section "Informasi Organisasi", pilih atau ubah instansi
   - Klik "Update"

### Menggunakan Nama Instansi dalam Kode

```php
// Mendapatkan nama instansi pegawai (atau fallback ke org_settings)
$instansiName = $user->getInstansiName();

// Mendapatkan relasi instansi
$instansi = $user->instansi; // Returns Instansi object atau null

// Contoh penggunaan dalam dokumen PDF
$instansiName = $user->getInstansiName();
// Hasilnya: "Nama Instansi Pegawai" atau "Nama Organisasi dari Settings"
```

### Update Dokumen yang Menggunakan Nama Organisasi

Jika Anda ingin menggunakan nama instansi pegawai pada dokumen (misalnya PDF), gunakan method `getInstansiName()`:

**Contoh pada view PDF:**
```php
<!-- Sebelum -->
{{ \DB::table('org_settings')->value('name') }}

<!-- Sesudah -->
{{ $user->getInstansiName() }}
```

## Validasi dan Aturan Bisnis

1. **Nama Instansi** adalah field wajib
2. **Kode Instansi** harus unik jika diisi
3. Instansi tidak dapat dihapus jika masih digunakan oleh pegawai
4. Field `instansi_id` pada pegawai bersifat nullable (opsional)
5. Jika `instansi_id` null, sistem akan menggunakan nama organisasi dari `org_settings.name`

## Hak Akses

- **Admin dan Super Admin:**
  - Dapat mengakses menu Data Instansi
  - Dapat CRUD (Create, Read, Update, Delete) data instansi
  - Dapat menambahkan/mengubah instansi pada pegawai

- **Bendahara Pengeluaran dan Bendahara Pengeluaran Pembantu:**
  - Tidak dapat mengakses menu Data Instansi
  - Dapat melihat dropdown instansi saat mengelola pegawai (jika memiliki akses)

## Catatan Teknis

1. **Foreign Key:** Kolom `instansi_id` di tabel `users` tidak menggunakan constraint ON DELETE CASCADE, sehingga instansi tidak dapat dihapus jika masih digunakan
2. **Nullable:** Field `instansi_id` nullable untuk backward compatibility dengan data pegawai yang sudah ada
3. **Fallback Mechanism:** Method `getInstansiName()` menyediakan fallback ke `org_settings.name` untuk memastikan selalu ada nilai yang ditampilkan
4. **Code Optional:** Kode instansi bersifat opsional untuk memberikan fleksibilitas dalam pengelolaan data

## File yang Dibuat/Diubah

### File Baru:
- `database/migrations/2026_01_09_090411_create_instansis_table.php`
- `database/migrations/2026_01_09_090441_add_instansi_id_to_users_table.php`
- `app/Models/Instansi.php`
- `app/Http/Controllers/InstansiController.php`
- `resources/views/instansis/index.blade.php`
- `resources/views/instansis/create.blade.php`
- `resources/views/instansis/edit.blade.php`

### File yang Diubah:
- `app/Models/User.php`
- `app/Livewire/Users/Create.php`
- `app/Livewire/Users/Edit.php`
- `resources/views/livewire/users/create.blade.php`
- `resources/views/livewire/users/edit.blade.php`
- `resources/views/master-data/index.blade.php`
- `routes/web.php`

## Troubleshooting

### Masalah: Dropdown instansi tidak muncul di form pegawai
**Solusi:** 
- Pastikan migration sudah dijalankan dengan benar
- Clear cache: `php artisan cache:clear`
- Clear view cache: `php artisan view:clear`

### Masalah: Error saat menghapus instansi
**Solusi:**
- Periksa apakah instansi masih digunakan oleh pegawai
- Jika ya, hapus atau ubah instansi pegawai terlebih dahulu, baru hapus data instansi

### Masalah: Nama organisasi tidak muncul saat instansi_id null
**Solusi:**
- Pastikan tabel `org_settings` memiliki data dan field `name` terisi
- Periksa apakah method `getInstansiName()` dipanggil dengan benar

## Pengembangan Selanjutnya

Beberapa ide untuk pengembangan fitur ini:
1. Menambahkan logo instansi
2. Menambahkan field untuk pimpinan instansi
3. Menambahkan field untuk alamat email instansi
4. Integrasi dengan dokumen PDF untuk menampilkan informasi instansi lengkap
5. Menambahkan statistik pegawai per instansi
