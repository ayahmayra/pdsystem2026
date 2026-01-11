# Budget User Role - Panduan Implementasi

## Deskripsi
Fitur ini menambahkan field **budget_user_role** pada tabel users untuk menentukan apakah seorang pegawai memiliki peran sebagai **Pengguna Anggaran** atau **Kuasa Pengguna Anggaran**. Field ini bersifat nullable (dapat bernilai null) jika pegawai tidak memiliki peran dalam pengelolaan anggaran.

## Perubahan yang Dilakukan

### 1. Database

#### Perubahan Tabel `users`
- Menambahkan kolom `budget_user_role` dengan tipe ENUM
- Nilai yang diperbolehkan:
  - `'pengguna_anggaran'` - Pengguna Anggaran
  - `'kuasa_pengguna_anggaran'` - Kuasa Pengguna Anggaran
  - `NULL` - Tidak memiliki role anggaran
- Kolom ini diletakkan setelah kolom `is_non_staff`
- Memiliki comment: "Role user dalam pengelolaan anggaran: Pengguna Anggaran atau Kuasa Pengguna Anggaran"

### 2. Model

#### Update Model `App\Models\User`
**Lokasi:** `app/Models/User.php`

**Perubahan:**
- Menambahkan `budget_user_role` ke array fillable

**Method Baru:**

1. **getBudgetUserRoleLabel(): ?string**
   - Mengembalikan label dalam bahasa Indonesia
   - Return: 'Pengguna Anggaran', 'Kuasa Pengguna Anggaran', atau null

2. **isPenggunaAnggaran(): bool**
   - Mengecek apakah user adalah Pengguna Anggaran
   - Return: true jika budget_user_role === 'pengguna_anggaran'

3. **isKuasaPenggunaAnggaran(): bool**
   - Mengecek apakah user adalah Kuasa Pengguna Anggaran
   - Return: true jika budget_user_role === 'kuasa_pengguna_anggaran'

**Cara Penggunaan:**
```php
// Mendapatkan label role anggaran
$roleLabel = $user->getBudgetUserRoleLabel();
// Return: "Pengguna Anggaran", "Kuasa Pengguna Anggaran", atau null

// Mengecek apakah user adalah Pengguna Anggaran
if ($user->isPenggunaAnggaran()) {
    // User adalah Pengguna Anggaran
}

// Mengecek apakah user adalah Kuasa Pengguna Anggaran
if ($user->isKuasaPenggunaAnggaran()) {
    // User adalah Kuasa Pengguna Anggaran
}

// Mengakses langsung
$budgetRole = $user->budget_user_role; // 'pengguna_anggaran', 'kuasa_pengguna_anggaran', atau null
```

### 3. Livewire Components

#### Update Component: `App\Livewire\Users\Create`
**Lokasi:** `app/Livewire/Users/Create.php`

**Perubahan:**
- Menambahkan property `budget_user_role`
- Menambahkan mutator `setBudgetUserRoleProperty()` untuk handle empty string menjadi null
- Menambahkan validasi `budget_user_role` (nullable, in:pengguna_anggaran,kuasa_pengguna_anggaran)
- Menambahkan `budget_user_role` ke array nullable fields

#### Update Component: `App\Livewire\Users\Edit`
**Lokasi:** `app/Livewire/Users/Edit.php`

**Perubahan:**
- Menambahkan property `budget_user_role`
- Menambahkan mutator `setBudgetUserRoleProperty()` untuk handle empty string menjadi null
- Menambahkan validasi `budget_user_role` (nullable, in:pengguna_anggaran,kuasa_pengguna_anggaran)
- Menambahkan `budget_user_role` ke array nullable fields
- Set `budget_user_role` dari user pada method mount

### 4. Views

#### Update Views Pegawai
**Lokasi:** 
- `resources/views/livewire/users/create.blade.php`
- `resources/views/livewire/users/edit.blade.php`

**Perubahan:**
- Menambahkan section "Role Pengelolaan Anggaran" setelah section "Pengaturan Khusus"
- Menampilkan dropdown untuk memilih role anggaran dengan 3 opsi:
  1. "-- Tidak Ada Role Anggaran --" (value: empty/null)
  2. "Pengguna Anggaran" (value: pengguna_anggaran)
  3. "Kuasa Pengguna Anggaran" (value: kuasa_pengguna_anggaran)
- Menambahkan keterangan: "Tentukan apakah pegawai ini adalah Pengguna Anggaran atau Kuasa Pengguna Anggaran. Kosongkan jika tidak memiliki role tersebut."

## Cara Menggunakan

### Menambahkan Role Anggaran pada Pegawai

1. **Saat Membuat Pegawai Baru:**
   - Buka "Data Pegawai" > "Tambah Pegawai"
   - Scroll ke section "Role Pengelolaan Anggaran"
   - Pilih role dari dropdown:
     - Pilih "Pengguna Anggaran" jika pegawai adalah Pengguna Anggaran
     - Pilih "Kuasa Pengguna Anggaran" jika pegawai adalah Kuasa Pengguna Anggaran
     - Kosongkan jika pegawai tidak memiliki role anggaran
   - Klik "Simpan"

2. **Saat Mengedit Pegawai:**
   - Buka "Data Pegawai" > Edit pegawai yang ingin diubah
   - Scroll ke section "Role Pengelolaan Anggaran"
   - Ubah role sesuai kebutuhan
   - Klik "Update"

### Menggunakan dalam Kode

#### 1. Mendapatkan Label Role
```php
$user = User::find(1);
$roleLabel = $user->getBudgetUserRoleLabel();

// Contoh output:
// "Pengguna Anggaran" jika budget_user_role = 'pengguna_anggaran'
// "Kuasa Pengguna Anggaran" jika budget_user_role = 'kuasa_pengguna_anggaran'
// null jika budget_user_role = null
```

#### 2. Mengecek Role Anggaran
```php
$user = User::find(1);

// Cek apakah Pengguna Anggaran
if ($user->isPenggunaAnggaran()) {
    echo "User adalah Pengguna Anggaran";
}

// Cek apakah Kuasa Pengguna Anggaran
if ($user->isKuasaPenggunaAnggaran()) {
    echo "User adalah Kuasa Pengguna Anggaran";
}
```

#### 3. Filter Users Berdasarkan Role Anggaran
```php
// Ambil semua Pengguna Anggaran
$penggunaAnggaran = User::where('budget_user_role', 'pengguna_anggaran')->get();

// Ambil semua Kuasa Pengguna Anggaran
$kuasaPenggunaAnggaran = User::where('budget_user_role', 'kuasa_pengguna_anggaran')->get();

// Ambil semua user yang memiliki role anggaran (tidak null)
$usersWithBudgetRole = User::whereNotNull('budget_user_role')->get();

// Ambil semua user yang tidak memiliki role anggaran
$usersWithoutBudgetRole = User::whereNull('budget_user_role')->get();
```

#### 4. Menampilkan dalam View
```blade
<!-- Menampilkan label role -->
@if($user->budget_user_role)
    <span class="badge">{{ $user->getBudgetUserRoleLabel() }}</span>
@else
    <span class="text-gray-400">Tidak ada role anggaran</span>
@endif

<!-- Conditional rendering berdasarkan role -->
@if($user->isPenggunaAnggaran())
    <p>User ini adalah Pengguna Anggaran</p>
@elseif($user->isKuasaPenggunaAnggaran())
    <p>User ini adalah Kuasa Pengguna Anggaran</p>
@else
    <p>User tidak memiliki role anggaran</p>
@endif
```

#### 5. Penggunaan dalam Dokumen/PDF
```blade
{{-- Contoh dalam dokumen PDF --}}
<div>
    <strong>Pengguna Anggaran:</strong>
    @php
        $penggunaAnggaran = \App\Models\User::where('budget_user_role', 'pengguna_anggaran')->first();
    @endphp
    
    @if($penggunaAnggaran)
        {{ $penggunaAnggaran->fullNameWithTitles() }}<br>
        NIP: {{ $penggunaAnggaran->nip }}
    @else
        <span class="text-gray-400">(Belum ditentukan)</span>
    @endif
</div>

<div>
    <strong>Kuasa Pengguna Anggaran:</strong>
    @php
        $kuasaPenggunaAnggaran = \App\Models\User::where('budget_user_role', 'kuasa_pengguna_anggaran')->first();
    @endphp
    
    @if($kuasaPenggunaAnggaran)
        {{ $kuasaPenggunaAnggaran->fullNameWithTitles() }}<br>
        NIP: {{ $kuasaPenggunaAnggaran->nip }}
    @else
        <span class="text-gray-400">(Belum ditentukan)</span>
    @endif
</div>
```

## Validasi dan Aturan Bisnis

1. **Field budget_user_role** bersifat nullable (opsional)
2. Hanya boleh berisi nilai: `'pengguna_anggaran'`, `'kuasa_pengguna_anggaran'`, atau `NULL`
3. Satu pegawai hanya bisa memiliki satu role anggaran
4. Tidak ada validasi unik - bisa ada lebih dari satu Pengguna Anggaran atau Kuasa Pengguna Anggaran dalam sistem
5. Field ini tidak mempengaruhi hak akses sistem (untuk hak akses, gunakan Roles & Permissions)

## Perbedaan dengan Roles & Permissions

**Budget User Role** berbeda dengan **Roles & Permissions** dalam sistem:

| Aspek | Budget User Role | Roles & Permissions |
|-------|------------------|-------------------|
| **Tujuan** | Mengidentifikasi peran pegawai dalam pengelolaan anggaran untuk keperluan dokumen/laporan | Mengatur hak akses dan permission dalam sistem |
| **Penggunaan** | Digunakan dalam dokumen, laporan, dan tanda tangan | Mengatur siapa bisa mengakses menu/fitur apa |
| **Jumlah** | Hanya 1 role per user (atau null) | User bisa memiliki multiple roles |
| **Lokasi Data** | Kolom `budget_user_role` di tabel `users` | Tabel terpisah dengan Spatie Permission |
| **Contoh** | "Pengguna Anggaran", "Kuasa Pengguna Anggaran" | "super-admin", "admin", "bendahara" |

## Contoh Use Case

### 1. Dokumen SPT/SPPD
Dalam dokumen perjalanan dinas, mungkin perlu dicantumkan nama Pengguna Anggaran atau Kuasa Pengguna Anggaran sebagai penandatangan atau penanggung jawab.

```blade
<div class="signature-section">
    <div>
        <strong>Pengguna Anggaran:</strong><br>
        @php
            $pa = \App\Models\User::where('budget_user_role', 'pengguna_anggaran')->first();
        @endphp
        {{ $pa?->fullNameWithTitles() ?? '(Belum ditentukan)' }}<br>
        NIP: {{ $pa?->nip ?? '-' }}
    </div>
</div>
```

### 2. Laporan Keuangan
Dalam laporan keuangan, nama dan tanda tangan Pengguna Anggaran atau Kuasa Pengguna Anggaran mungkin diperlukan.

```php
// Dalam controller atau component
$penggunaAnggaran = User::where('budget_user_role', 'pengguna_anggaran')->first();
$kuasaPenggunaAnggaran = User::where('budget_user_role', 'kuasa_pengguna_anggaran')->first();

return view('reports.financial', compact('penggunaAnggaran', 'kuasaPenggunaAnggaran'));
```

### 3. Filter Dropdown untuk Penandatangan
```blade
<select name="signer_id">
    <option value="">Pilih Penandatangan</option>
    
    <optgroup label="Pengguna Anggaran">
        @foreach(\App\Models\User::where('budget_user_role', 'pengguna_anggaran')->get() as $user)
            <option value="{{ $user->id }}">{{ $user->fullNameWithTitles() }}</option>
        @endforeach
    </optgroup>
    
    <optgroup label="Kuasa Pengguna Anggaran">
        @foreach(\App\Models\User::where('budget_user_role', 'kuasa_pengguna_anggaran')->get() as $user)
            <option value="{{ $user->id }}">{{ $user->fullNameWithTitles() }}</option>
        @endforeach
    </optgroup>
</select>
```

## File yang Dibuat/Diubah

### File Baru:
- `database/migrations/2026_01_11_024919_add_budget_user_role_to_users_table.php`
- `BUDGET_USER_ROLE_GUIDE.md` (dokumentasi ini)

### File yang Diubah:
- `app/Models/User.php` - Tambah fillable, method helper
- `app/Livewire/Users/Create.php` - Tambah property, validasi, mutator
- `app/Livewire/Users/Edit.php` - Tambah property, validasi, mutator, mount
- `resources/views/livewire/users/create.blade.php` - Tambah section dropdown
- `resources/views/livewire/users/edit.blade.php` - Tambah section dropdown

## Migration

**File:** `database/migrations/2026_01_11_024919_add_budget_user_role_to_users_table.php`

```php
Schema::table('users', function (Blueprint $table) {
    $table->enum('budget_user_role', ['pengguna_anggaran', 'kuasa_pengguna_anggaran'])
          ->nullable()
          ->after('is_non_staff')
          ->comment('Role user dalam pengelolaan anggaran');
});
```

## Troubleshooting

### Masalah: Dropdown tidak muncul di form pegawai
**Solusi:** 
- Pastikan migration sudah dijalankan dengan benar
- Clear cache: `php artisan cache:clear`
- Clear view cache: `php artisan view:clear`

### Masalah: Error saat menyimpan dengan nilai kosong
**Solusi:**
- Pastikan mutator `setBudgetUserRoleProperty()` sudah ditambahkan
- Pastikan `budget_user_role` ada di array nullable fields dalam method save/update

### Masalah: Method helper tidak ditemukan
**Solusi:**
- Pastikan perubahan pada model User sudah disimpan
- Clear cache: `php artisan cache:clear`
- Restart Laravel server jika menggunakan `php artisan serve`

## Best Practices

1. **Jangan hardcode value enum** - Gunakan method helper seperti `isPenggunaAnggaran()` daripada mengecek langsung `$user->budget_user_role === 'pengguna_anggaran'`

2. **Selalu handle null value** - Field ini nullable, jadi selalu cek null sebelum menggunakan:
   ```php
   if ($user->budget_user_role) {
       // Lakukan sesuatu
   }
   ```

3. **Gunakan method getBudgetUserRoleLabel()** untuk display - Daripada membuat switch/match sendiri, gunakan method yang sudah disediakan:
   ```php
   echo $user->getBudgetUserRoleLabel() ?? 'Tidak ada role';
   ```

4. **Dokumentasikan penggunaan** - Jika menggunakan field ini dalam dokumen atau laporan, dokumentasikan dengan jelas

## Kesimpulan

Field `budget_user_role` memberikan cara yang fleksibel untuk mengidentifikasi pegawai yang memiliki peran khusus dalam pengelolaan anggaran. Dengan nilai yang bisa null, field ini tidak memaksa semua pegawai untuk memiliki role anggaran, namun memberikan opsi untuk menandai pegawai yang memiliki tanggung jawab tersebut.

Field ini sangat berguna untuk:
- Dokumen resmi yang memerlukan tanda tangan Pengguna/Kuasa Pengguna Anggaran
- Laporan keuangan dan pertanggungjawaban
- Filter dan query khusus untuk pegawai dengan role anggaran
- Metadata pegawai untuk keperluan administratif
