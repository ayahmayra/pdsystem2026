# 🔧 Troubleshooting Container Restart

## Masalah: Container PHP-FPM Terus Restart

Jika container terus restart dengan exit code 100, ikuti langkah berikut:

### 1. Cek Logs Container

```bash
# Cek logs terakhir
docker compose logs php-fpm --tail 100

# Cek logs real-time
docker compose logs -f php-fpm
```

### 2. Cek Logs dengan Detail

```bash
# Cek semua logs termasuk stderr
docker compose logs php-fpm 2>&1 | tail -200
```

### 3. Masuk ke Container Saat Masih Running

Jika container restart terlalu cepat, coba:

```bash
# Stop container dulu
docker compose stop php-fpm

# Start tanpa detach untuk melihat output langsung
docker compose up php-fpm
```

### 4. Debug Entrypoint Script

```bash
# Copy entrypoint script ke container dan jalankan manual
docker compose run --rm php-fpm bash

# Di dalam container, jalankan entrypoint script dengan debug
bash -x /usr/local/bin/entrypoint.sh php-fpm
```

### 5. Cek Environment Variables

```bash
# Cek apakah semua env vars ter-set
docker compose config | grep -A 20 "php-fpm:" | grep -A 20 "environment:"
```

### 6. Common Issues dan Solusi

#### Issue: Composer Install Gagal

**Gejala**: Error di logs tentang composer install

**Solusi**:
```bash
# Masuk ke container (jika bisa)
docker compose exec php-fpm bash

# Install manual
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
```

#### Issue: Flux Pro Auth Gagal

**Gejala**: HTTP 401 error untuk flux-pro

**Solusi**:
```bash
# Cek token
docker compose exec php-fpm env | grep FLUX_PRO_TOKEN

# Jika tidak ada, set di .env
nano .env
# Tambahkan: FLUX_PRO_TOKEN=your_token

# Restart
docker compose restart php-fpm
```

#### Issue: npm Build Gagal

**Gejala**: Error tentang flux.css tidak ditemukan

**Solusi**:
```bash
# Pastikan flux-pro terinstall dulu
docker compose exec php-fpm composer show livewire/flux-pro

# Jika tidak ada, install dulu
docker compose exec php-fpm composer require livewire/flux-pro:^2.2 --no-interaction
```

#### Issue: Permission Error

**Gejala**: Permission denied errors

**Solusi**:
```bash
docker compose exec php-fpm chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker compose exec php-fpm chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
```

### 7. Temporary Fix: Skip Problematic Steps

Jika ada bagian yang selalu gagal, edit entrypoint script sementara:

```bash
# Edit entrypoint
nano docker/entrypoint.sh

# Comment bagian yang bermasalah, contoh:
# npm run build || echo "Build failed, continuing..."

# Rebuild dan restart
docker compose build php-fpm
docker compose up -d php-fpm
```

### 8. Full Reset

Jika semua gagal, reset container:

```bash
# Stop semua
docker compose down

# Hapus container (tidak hapus volumes)
docker compose rm -f php-fpm

# Rebuild
docker compose build --no-cache php-fpm

# Start
docker compose up -d

# Cek logs
docker compose logs -f php-fpm
```

### 9. Check System Resources

```bash
# Cek disk space
df -h

# Cek memory
free -h

# Cek Docker resources
docker system df
```

### 10. Enable Verbose Logging

Edit `docker-compose.yml` untuk menambahkan logging:

```yaml
php-fpm:
  logging:
    driver: "json-file"
    options:
      max-size: "10m"
      max-file: "3"
```

Kemudian restart:
```bash
docker compose up -d
docker compose logs -f php-fpm
```

## Quick Diagnostic Command

Jalankan command ini untuk mendapatkan semua informasi:

```bash
echo "=== Container Status ==="
docker compose ps

echo ""
echo "=== PHP-FPM Logs (last 50 lines) ==="
docker compose logs php-fpm --tail 50

echo ""
echo "=== Environment Variables ==="
docker compose config | grep -A 30 "php-fpm:" | grep -A 30 "environment:"

echo ""
echo "=== Disk Space ==="
df -h | grep -E "Filesystem|/dev/"

echo ""
echo "=== Memory ==="
free -h
```

## Contact Support

Jika masalah masih berlanjut, siapkan informasi berikut:

1. Output dari diagnostic command di atas
2. Full logs: `docker compose logs php-fpm > php-fpm-logs.txt`
3. docker-compose.yml (tanpa password)
4. .env file (tanpa password/token)
5. Output: `docker compose config`

