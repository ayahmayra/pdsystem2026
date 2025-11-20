# 🔐 Setup Autentikasi Flux Pro untuk Docker Build

## Masalah

Package `livewire/flux-pro` adalah package premium yang memerlukan autentikasi saat diinstall melalui Composer. Error yang muncul:

```
The 'https://composer.fluxui.dev/download/...' URL required authentication (HTTP 401).
```

## Solusi

Ada 3 cara untuk mengatasi masalah ini:

---

## Opsi 1: Menggunakan Build Arg (Direkomendasikan untuk Production)

### Langkah 1: Dapatkan Token Flux Pro

1. Login ke [Flux UI Dashboard](https://flux.laravel.com)
2. Buka halaman **Account Settings** atau **API Tokens**
3. Generate atau copy token autentikasi Anda

### Langkah 2: Setup Token di Environment

Tambahkan token ke file `.env` atau `docker-compose.env.example`:

```env
FLUX_PRO_TOKEN=your_flux_pro_token_here
```

### Langkah 3: Build dengan Token

```bash
# Build dengan token dari environment
docker compose build --build-arg FLUX_PRO_TOKEN=${FLUX_PRO_TOKEN}

# Atau langsung build (akan membaca dari .env)
docker compose build
```

**Catatan**: Token akan digunakan saat build dan disimpan di dalam image. Pastikan untuk tidak commit file `.env` yang berisi token ke repository public.

---

## Opsi 2: Menggunakan auth.json (Alternatif)

### Langkah 1: Buat File auth.json

Buat file `auth.json` di root project:

```json
{
    "http-basic": {
        "composer.fluxui.dev": {
            "username": "token",
            "password": "your_flux_pro_token_here"
        }
    }
}
```

### Langkah 2: Update Dockerfile

Tambahkan copy auth.json sebelum composer install:

```dockerfile
# Copy auth.json if exists
COPY auth.json* /root/.composer/auth.json
```

### Langkah 3: Build

```bash
docker compose build
```

**Catatan**: 
- Pastikan `auth.json` ada di `.gitignore` agar tidak ter-commit
- File ini akan di-copy ke dalam image

---

## Opsi 3: Install Setelah Container Running (Fleksibel)

Jika Anda tidak ingin menyertakan token di dalam image, Anda bisa menginstall Flux Pro setelah container running.

### Langkah 1: Build Tanpa Flux Pro

Modifikasi `composer.json` sementara untuk skip flux-pro:

```json
{
    "require": {
        "livewire/flux": "^2.2"
        // Comment atau hapus: "livewire/flux-pro": "^2.2"
    }
}
```

### Langkah 2: Build Image

```bash
docker compose build
docker compose up -d
```

### Langkah 3: Setup Auth dan Install Flux Pro

```bash
# Masuk ke container
docker compose exec php-fpm bash

# Setup auth.json
mkdir -p /root/.composer
echo '{"http-basic": {"composer.fluxui.dev": {"username": "token", "password": "YOUR_TOKEN"}}}' > /root/.composer/auth.json

# Install flux-pro
composer require livewire/flux-pro:^2.2 --no-interaction

# Exit container
exit

# Restart container
docker compose restart php-fpm
```

---

## Rekomendasi untuk Production

**Gunakan Opsi 1 (Build Arg)** karena:
- ✅ Token tidak ter-commit ke repository
- ✅ Build lebih cepat (semua dependencies terinstall saat build)
- ✅ Image siap digunakan tanpa setup tambahan
- ✅ Lebih aman (token hanya ada di environment server)

---

## Troubleshooting

### Error: Token Invalid

Pastikan token yang digunakan valid dan masih aktif. Cek di Flux UI Dashboard.

### Error: Token Expired

Generate token baru dan update di `.env`, kemudian rebuild image.

### Error: Package Not Found

Pastikan:
1. Token sudah benar
2. Repository `flux-pro` sudah dikonfigurasi di `composer.json`
3. Package name dan version sesuai dengan lisensi Anda

### Skip Flux Pro (Tidak Direkomendasikan)

Jika Anda tidak memiliki lisensi Flux Pro, Anda bisa:

1. Hapus `livewire/flux-pro` dari `composer.json`
2. Hapus semua penggunaan komponen Flux Pro dari views
3. Ganti dengan komponen alternatif atau buat custom components

**Catatan**: Ini akan memerlukan perubahan kode yang signifikan.

---

## Security Best Practices

1. **Jangan commit token ke repository**
   - Pastikan `.env` ada di `.gitignore`
   - Jangan commit `auth.json` jika berisi token

2. **Gunakan environment variables**
   - Simpan token di environment variables server
   - Jangan hardcode token di Dockerfile

3. **Rotate token secara berkala**
   - Generate token baru setiap 6-12 bulan
   - Revoke token lama yang tidak digunakan

4. **Limit access**
   - Hanya user yang memerlukan akses build yang memiliki token
   - Jangan share token melalui chat/email yang tidak aman

---

## Contoh Workflow Production

```bash
# 1. Setup environment
cd /opt/apps/pdsystem2026
cp docker-compose.env.example .env
nano .env  # Tambahkan FLUX_PRO_TOKEN

# 2. Build dengan token
docker compose build --build-arg FLUX_PRO_TOKEN=${FLUX_PRO_TOKEN}

# 3. Start services
docker compose up -d

# 4. Verify
docker compose exec php-fpm composer show livewire/flux-pro
```

---

## Referensi

- [Flux UI Documentation](https://flux.laravel.com/docs)
- [Composer Authentication](https://getcomposer.org/doc/articles/authentication-for-private-packages.md)
- [Docker Build Args](https://docs.docker.com/engine/reference/commandline/build/#build-arg)

