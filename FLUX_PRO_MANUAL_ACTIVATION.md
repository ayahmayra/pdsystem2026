# 🔧 Manual Activation Flux Pro

## Masalah

Command `php artisan flux:activate` adalah **interaktif** dan memerlukan input token secara manual. Ini tidak bisa diotomatisasi sepenuhnya di entrypoint script.

## Solusi: Manual Activation

Setelah container running, aktifkan Flux Pro secara manual:

### Langkah 1: Pastikan livewire/flux Terinstall

```bash
# Cek apakah livewire/flux sudah terinstall
docker compose exec php-fpm composer show livewire/flux

# Jika belum, install
docker compose exec php-fpm composer require livewire/flux:^2.2 --no-interaction
```

### Langkah 2: Aktifkan Flux Pro Manual

```bash
# Masuk ke container
docker compose exec php-fpm bash

# Jalankan command interaktif
php artisan flux:activate

# Ketika diminta token, paste token Anda dari .env atau Flux UI Dashboard
# Token akan diinput secara interaktif

# Exit container
exit
```

### Langkah 3: Verifikasi

```bash
# Cek status aktivasi
docker compose exec php-fpm php artisan flux:status

# Cek apakah flux-pro terinstall
docker compose exec php-fpm composer show livewire/flux-pro
```

## Alternatif: Non-Interactive Activation

Jika Anda ingin mencoba non-interactive (mungkin tidak bekerja):

```bash
# Coba dengan echo token ke stdin
echo "YOUR_TOKEN" | docker compose exec -T php-fpm php artisan flux:activate

# Atau dengan heredoc
docker compose exec php-fpm bash -c 'echo "YOUR_TOKEN" | php artisan flux:activate'
```

## Workflow Production

```bash
# 1. Build dan start container
docker compose build
docker compose up -d

# 2. Install livewire/flux (jika belum)
docker compose exec php-fpm composer require livewire/flux:^2.2 --no-interaction

# 3. Aktifkan Flux Pro (INTERAKTIF - harus manual)
docker compose exec php-fpm php artisan flux:activate
# Paste token ketika diminta

# 4. Verifikasi
docker compose exec php-fpm php artisan flux:status
docker compose exec php-fpm composer show livewire/flux-pro

# 5. Build assets
docker compose exec php-fpm npm run build
```

## Catatan Penting

- **`flux:activate` adalah interaktif** - tidak bisa diotomatisasi sepenuhnya
- Token harus diinput **secara manual** saat command dijalankan
- Setelah aktivasi, token akan disimpan di konfigurasi Laravel
- Aktivasi hanya perlu dilakukan **sekali** per instalasi

## Update Flux

Setelah aktivasi, untuk update:

```bash
docker compose exec php-fpm composer update livewire/flux livewire/flux-pro
```

## Troubleshooting

### Activation Failed

Jika aktivasi gagal:

1. **Cek token valid:**
   ```bash
   curl -u "token:YOUR_TOKEN" https://composer.fluxui.dev/packages.json
   ```

2. **Cek logs:**
   ```bash
   docker compose logs php-fpm | grep -i flux
   ```

3. **Coba lagi:**
   ```bash
   docker compose exec php-fpm php artisan flux:activate
   ```

### Token Invalid

Jika mendapat error token invalid:

1. Generate token baru di [Flux UI Dashboard](https://flux.laravel.com)
2. Gunakan token baru untuk aktivasi
3. Token lama akan otomatis diganti

## Referensi

- [Flux UI Documentation](https://flux.laravel.com/docs/installation)
- [Flux Activation Guide](https://flux.laravel.com/docs/activation)

