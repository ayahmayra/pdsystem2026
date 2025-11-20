# Docker Quick Start Guide

Panduan cepat untuk menjalankan sistem Perjalanan Dinas menggunakan Docker.

## 🚀 Quick Start (5 Menit)

### 1. Setup Environment
```bash
cp docker-compose.env.example .env
```

Edit `.env` dan sesuaikan jika diperlukan (default sudah siap digunakan).

### 2. Build & Start
```bash
docker compose build
docker compose up -d
```

### 3. Cek Status
```bash
docker compose ps
```

Semua container harus berstatus "Up":
- ✅ pdsystem_mariadb (healthy)
- ✅ pdsystem_php_fpm (Up)
- ✅ pdsystem_nginx (Up)

### 4. Akses Aplikasi
```
http://localhost
```

## 📋 Checklist Setelah Start

### ✓ Database Ready?
```bash
docker compose logs mariadb | grep "ready for connections"
```

### ✓ PHP-FPM Ready?
```bash
docker compose logs php-fpm | grep "ready"
```

### ✓ Application Key Generated?
```bash
docker compose exec php-fpm php artisan key:show
```

### ✓ Migrations Run?
```bash
docker compose exec php-fpm php artisan migrate:status
```

## 🔧 Common Commands

### View Logs
```bash
# All services
docker compose logs -f

# Specific service
docker compose logs -f php-fpm
```

### Access Container
```bash
# PHP-FPM
docker compose exec php-fpm bash

# MariaDB
docker compose exec mariadb mariadb -u root -p
```

### Artisan Commands
```bash
docker compose exec php-fpm php artisan [command]
```

### Restart Service
```bash
docker compose restart [service-name]
# Example: docker compose restart php-fpm
```

### Stop All
```bash
docker compose down
```

### Clean Start (Remove Volumes)
```bash
docker compose down -v
docker compose up -d
```

## ⚠️ Troubleshooting

### Port Already in Use?
Edit `.env`:
```env
APP_PORT=8080
DB_PORT=3307
```

### Permission Denied?
```bash
docker compose exec php-fpm chown -R www-data:www-data storage bootstrap/cache
docker compose exec php-fpm chmod -R 775 storage bootstrap/cache
```

### Database Connection Failed?
1. Cek logs: `docker compose logs mariadb`
2. Pastikan credentials di `.env` sesuai
3. Restart: `docker compose restart mariadb`

### Application Not Loading?
1. Clear cache:
   ```bash
   docker compose exec php-fpm php artisan cache:clear
   docker compose exec php-fpm php artisan config:clear
   ```
2. Rebuild config cache:
   ```bash
   docker compose exec php-fpm php artisan config:cache
   ```

## 📚 Next Steps

1. Run migrations: `docker compose exec php-fpm php artisan migrate`
2. Run seeders: `docker compose exec php-fpm php artisan db:seed`
3. Create superadmin user (lihat INSTALLATION_GUIDE.md)

Untuk dokumentasi lengkap, lihat [DOCKER_SETUP.md](./DOCKER_SETUP.md)

