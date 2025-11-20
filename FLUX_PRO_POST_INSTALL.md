# 🚀 Install Flux Pro Setelah Container Berjalan

## Overview

Pendekatan ini menginstall Flux Pro **setelah container berjalan**, bukan saat build Docker image. Ini lebih fleksibel karena:
- ✅ Tidak perlu token saat build
- ✅ Token bisa diubah tanpa rebuild image
- ✅ Build lebih cepat
- ✅ Lebih aman (token tidak tersimpan di image)

## Cara Kerja

1. **Saat Build**: Dockerfile akan mencoba install semua package. Jika flux-pro gagal karena auth, build tetap lanjut.
2. **Saat Container Start**: Entrypoint script akan:
   - Setup auth.json jika `FLUX_PRO_TOKEN` tersedia
   - Install flux-pro jika belum terinstall
   - **Aktifkan Flux Pro** dengan `php artisan flux:activate` (wajib setelah install)

## Setup

### 1. Tambahkan Token ke Environment

Edit file `.env` atau `docker-compose.env.example`:

```env
# Flux Pro Authentication Token
FLUX_PRO_TOKEN=your_flux_pro_token_here
```

**Cara mendapatkan token:**
1. Login ke [Flux UI Dashboard](https://flux.laravel.com)
2. Buka **Account Settings** → **API Tokens**
3. Generate atau copy token Anda

### 2. Build Docker Image

```bash
cd /opt/apps/pdsystem2026

# Build image (flux-pro akan di-skip jika tidak ada token)
docker compose build
```

**Catatan**: Build mungkin akan menunjukkan warning tentang flux-pro, tapi build akan tetap sukses.

### 3. Start Container

```bash
# Start container dengan token dari .env
docker compose up -d

# Atau set token langsung
FLUX_PRO_TOKEN=your_token docker compose up -d
```

### 4. Verifikasi Instalasi

```bash
# Cek apakah flux-pro terinstall
docker compose exec php-fpm composer show livewire/flux-pro

# Cek status aktivasi Flux Pro
docker compose exec php-fpm php artisan flux:status

# Cek logs entrypoint untuk melihat proses instalasi
docker compose logs php-fpm | grep -i flux
```

## Troubleshooting

### Flux Pro Tidak Terinstall

**Cek apakah token sudah di-set:**
```bash
docker compose exec php-fpm env | grep FLUX_PRO_TOKEN
```

**Install manual jika diperlukan:**
```bash
# Masuk ke container
docker compose exec php-fpm bash

# Setup auth
mkdir -p /root/.composer
echo '{"http-basic": {"composer.fluxui.dev": {"username": "token", "password": "YOUR_TOKEN"}}}' > /root/.composer/auth.json

# Install flux-pro
composer require livewire/flux-pro:^2.2 --no-interaction

# Aktifkan Flux Pro (WAJIB setelah install)
php artisan flux:activate

# Exit
exit
```

### Token Invalid atau Expired

1. Generate token baru di Flux UI Dashboard
2. Update di `.env`:
   ```env
   FLUX_PRO_TOKEN=new_token_here
   ```
3. Restart container:
   ```bash
   docker compose restart php-fpm
   ```

### Build Gagal Karena Flux Pro

Jika build masih gagal, Anda bisa:

**Opsi A: Skip flux-pro saat build (temporary)**

Edit `composer.json` sementara:
```json
{
    "require": {
        "livewire/flux": "^2.2"
        // Comment: "livewire/flux-pro": "^2.2"
    }
}
```

Build, lalu install flux-pro setelah container running.

**Opsi B: Gunakan build arg (alternatif)**

```bash
docker compose build --build-arg FLUX_PRO_TOKEN=your_token
```

## Workflow Production

```bash
# 1. Setup environment
cd /opt/apps/pdsystem2026
cp docker-compose.env.example .env
nano .env  # Tambahkan FLUX_PRO_TOKEN

# 2. Build image
docker compose build

# 3. Start services
docker compose up -d

# 4. Cek apakah flux-pro terinstall
docker compose exec php-fpm composer show livewire/flux-pro

# 5. Jika belum terinstall, cek logs
docker compose logs php-fpm | tail -50

# 6. Jika perlu, install manual (lihat troubleshooting di atas)
```

## Keuntungan Pendekatan Ini

1. **Fleksibilitas**: Token bisa diubah tanpa rebuild image
2. **Keamanan**: Token tidak tersimpan di Docker image
3. **Kecepatan**: Build lebih cepat (tidak perlu menunggu auth)
4. **Maintenance**: Lebih mudah untuk update token

## Catatan Penting

- Token harus valid dan aktif
- Token akan digunakan setiap kali container start
- Jika token tidak diset, aplikasi akan tetap berjalan tapi tanpa fitur Flux Pro
- Pastikan `.env` tidak ter-commit ke repository public

## Referensi

- [Flux UI Documentation](https://flux.laravel.com/docs)
- [Composer Authentication](https://getcomposer.org/doc/articles/authentication-for-private-packages.md)

