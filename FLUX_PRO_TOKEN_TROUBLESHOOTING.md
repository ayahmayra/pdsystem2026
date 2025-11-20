# 🔍 Troubleshooting Flux Pro Token Authentication

## Masalah: HTTP 401 Authentication Required

Jika Anda mendapat error HTTP 401 saat install flux-pro, ikuti langkah troubleshooting berikut:

## Langkah 1: Verifikasi Token di Flux UI Dashboard

1. Login ke [Flux UI Dashboard](https://flux.laravel.com)
2. Buka **Account Settings** → **API Tokens**
3. Pastikan token masih **Active** dan tidak expired
4. Pastikan token memiliki akses ke package **flux-pro**

## Langkah 2: Test Token dengan cURL

```bash
# Test token langsung
TOKEN="your_token_here"
curl -u "token:$TOKEN" https://composer.fluxui.dev/packages.json

# Jika mendapat HTTP 200, token valid
# Jika mendapat HTTP 401, token tidak valid atau expired
```

## Langkah 3: Verifikasi Token di Server

```bash
# Di server production
cd /opt/apps/pdsystem2026

# Cek apakah token ter-set di environment
docker compose config | grep FLUX_PRO_TOKEN

# Atau cek di .env
grep FLUX_PRO_TOKEN .env
```

## Langkah 4: Test Authentication di Container

```bash
# Masuk ke container
docker compose exec php-fpm bash

# Cek auth.json
cat /root/.composer/auth.json

# Test dengan composer config
composer config --global --list | grep fluxui

# Test authentication
composer show -a livewire/flux-pro
```

## Langkah 5: Setup Authentication Manual

Jika auth otomatis tidak bekerja, setup manual:

```bash
# Masuk ke container
docker compose exec php-fpm bash

# Method 1: Gunakan composer config (direkomendasikan)
composer config --global http-basic.composer.fluxui.dev token "YOUR_TOKEN"

# Method 2: Buat auth.json manual
mkdir -p /root/.composer
cat > /root/.composer/auth.json <<EOF
{
    "http-basic": {
        "composer.fluxui.dev": {
            "username": "token",
            "password": "YOUR_TOKEN"
        }
    }
}
EOF
chmod 600 /root/.composer/auth.json

# Verify
composer show -a livewire/flux-pro
```

## Langkah 6: Generate Token Baru

Jika token tidak valid:

1. Login ke [Flux UI Dashboard](https://flux.laravel.com)
2. Buka **Account Settings** → **API Tokens**
3. **Revoke** token lama (jika ada)
4. **Generate** token baru
5. **Copy** token baru
6. Update di `.env`:
   ```env
   FLUX_PRO_TOKEN=new_token_here
   ```
7. Restart container:
   ```bash
   docker compose restart php-fpm
   ```

## Langkah 7: Cek Format Token

Token harus:
- Format UUID (36 karakter dengan dashes)
- Contoh: `84c037a3-b832-4a6a-97df-a308413a5420`
- Tidak ada spasi di awal/akhir
- Tidak ada karakter tambahan

## Langkah 8: Debug Authentication

```bash
# Enable verbose output
docker compose exec php-fpm composer show -a livewire/flux-pro -vvv

# Cek composer config
docker compose exec php-fpm composer config --global --list

# Cek auth.json format
docker compose exec php-fpm php -r "print_r(json_decode(file_get_contents('/root/.composer/auth.json'), true));"
```

## Common Issues

### Issue 1: Token Valid tapi Masih 401

**Kemungkinan penyebab:**
- Auth.json tidak di lokasi yang benar
- Composer tidak membaca auth.json
- Format auth.json salah

**Solusi:**
```bash
# Gunakan composer config instead
docker compose exec php-fpm composer config --global http-basic.composer.fluxui.dev token "YOUR_TOKEN"
```

### Issue 2: Token Expired

**Gejala:** Token bekerja sebelumnya, sekarang tidak

**Solusi:** Generate token baru di dashboard

### Issue 3: Token Tidak Memiliki Akses

**Gejala:** Token valid tapi tidak bisa akses flux-pro

**Solusi:** 
- Pastikan akun memiliki lisensi Flux Pro
- Pastikan token memiliki permission untuk flux-pro package
- Hubungi support Flux UI jika perlu

### Issue 4: Multiple Auth Methods Conflict

**Gejala:** Auth.json dan composer config berbeda

**Solusi:**
```bash
# Clear semua auth
docker compose exec php-fpm rm -f /root/.composer/auth.json
docker compose exec php-fpm composer config --global --unset http-basic.composer.fluxui.dev

# Setup ulang dengan satu method
docker compose exec php-fpm composer config --global http-basic.composer.fluxui.dev token "YOUR_TOKEN"
```

## Verifikasi Final

Setelah setup authentication, verifikasi:

```bash
# 1. Test authentication
docker compose exec php-fpm composer show -a livewire/flux-pro

# 2. Install flux-pro
docker compose exec php-fpm composer require livewire/flux-pro:^2.2 --no-interaction

# 3. Activate
docker compose exec php-fpm php artisan flux:activate

# 4. Verify
docker compose exec php-fpm php artisan flux:status
```

## Script Otomatis

Gunakan script untuk test dan setup:

```bash
# Test token
./docker/verify-flux-token.sh YOUR_TOKEN

# Check dan setup auth
./docker/check-flux-auth.sh
```

## Still Not Working?

Jika semua langkah di atas sudah dicoba tapi masih error:

1. **Cek Logs Lengkap:**
   ```bash
   docker compose logs php-fpm | grep -i "auth\|flux\|401" > debug.log
   ```

2. **Cek Token di Dashboard:**
   - Pastikan token masih active
   - Pastikan tidak ada restriction
   - Coba generate token baru

3. **Contact Support:**
   - Siapkan informasi:
     - Token preview (first 10 chars)
     - Error message lengkap
     - Output dari `composer show -a livewire/flux-pro -vvv`
     - Output dari `composer config --global --list`

## Prevention

Untuk mencegah masalah di masa depan:

1. **Rotate token secara berkala** (setiap 6-12 bulan)
2. **Gunakan environment variables** untuk token (jangan hardcode)
3. **Monitor token expiration** di dashboard
4. **Backup token** di tempat yang aman (password manager)

