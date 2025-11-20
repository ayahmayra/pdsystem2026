# Panduan Setup Docker untuk Sistem Perjalanan Dinas

## 📋 Overview

Dokumen ini menjelaskan cara setup dan menjalankan sistem Perjalanan Dinas menggunakan Docker Compose dengan MariaDB, PHP-FPM, dan Nginx.

## 🏗️ Arsitektur

Sistem menggunakan arsitektur multi-container:
- **MariaDB 10.11**: Database server (optimized untuk 2GB RAM, 2 CPU cores)
- **PHP 8.3-FPM**: Application server (optimized untuk 6GB RAM, 3.5 CPU cores)
- **Nginx Alpine**: Web server
- **Node.js 20** (optional): Untuk development asset building

### Spesifikasi Server yang Disarankan
- **Storage**: 220GB
- **Memory**: 7.8GB
- **CPU**: 4 cores

## 📦 Prasyarat

1. **Docker** versi 20.10 atau lebih baru
2. **Docker Compose** versi 2.0 atau lebih baru
3. **Git** (untuk clone repository)

### Cek Versi
```bash
docker --version
docker compose version
```

## 🚀 Setup Awal

### 1. Clone Repository
```bash
git clone <repository-url>
cd pdsystem
```

### 2. Setup Environment File

Salin file environment example:
```bash
cp docker-compose.env.example .env
```

Edit file `.env` dan sesuaikan konfigurasi:
```env
# Application
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
APP_PORT=80

# Database
DB_DATABASE=perjadin_db
DB_USERNAME=perjadin_user
DB_PASSWORD=perjadin_pass
DB_ROOT_PASSWORD=rootpassword

# Application Key (akan di-generate otomatis jika kosong)
APP_KEY=
```

### 3. Build Docker Images

```bash
docker compose build
```

Proses build akan:
- Install semua PHP extensions yang diperlukan
- Install Composer dependencies
- Build frontend assets (npm install & npm run build)

**Note**: Proses build pertama kali mungkin memakan waktu 10-15 menit.

### 4. Start Services

```bash
docker compose up -d
```

Command ini akan:
- Start MariaDB container
- Start PHP-FPM container
- Start Nginx container
- Run entrypoint script (migrations, cache, dll)

### 5. Cek Status Services

```bash
docker compose ps
```

Anda harus melihat semua container berstatus "Up":
```
NAME                   STATUS          PORTS
pdsystem_mariadb       Up (healthy)    0.0.0.0:3306->3306/tcp
pdsystem_nginx         Up              0.0.0.0:80->80/tcp
pdsystem_php_fpm       Up              9000/tcp
```

### 6. Akses Aplikasi

Buka browser dan akses:
```
http://localhost
```

## 📝 Perintah Penting

### Start Services
```bash
docker compose up -d
```

### Stop Services
```bash
docker compose down
```

### Stop Services (dengan menghapus volumes)
```bash
docker compose down -v
```

### View Logs
```bash
# Semua services
docker compose logs -f

# Specific service
docker compose logs -f php-fpm
docker compose logs -f nginx
docker compose logs -f mariadb
```

### Execute Commands di Container

#### PHP Artisan Commands
```bash
docker compose exec php-fpm php artisan migrate
docker compose exec php-fpm php artisan db:seed
docker compose exec php-fpm php artisan cache:clear
docker compose exec php-fpm php artisan config:cache
```

#### Composer Commands
```bash
docker compose exec php-fpm composer install
docker compose exec php-fpm composer update
```

#### Database Access
```bash
# MariaDB CLI
docker compose exec mariadb mariadb -u perjadin_user -p perjadin_db

# Atau sebagai root
docker compose exec mariadb mariadb -u root -p
```

### Rebuild Container
```bash
# Rebuild setelah perubahan Dockerfile
docker compose build --no-cache php-fpm
docker compose up -d
```

## 🔧 Konfigurasi

### Port Configuration

Ubah port di file `.env`:
```env
APP_PORT=8080          # Nginx port
DB_PORT=3307          # MariaDB port (default: 3306)
```

Kemudian update `docker-compose.yml`:
```yaml
ports:
  - "${APP_PORT:-80}:80"
  - "${DB_PORT:-3306}:3306"
```

### PHP Configuration

Edit `docker/php/php.ini` untuk mengubah:
- `memory_limit`
- `max_execution_time`
- `upload_max_filesize`
- `post_max_size`

Setelah perubahan, restart container:
```bash
docker compose restart php-fpm
```

### Nginx Configuration

Edit `docker/nginx/default.conf` untuk:
- Menambah virtual host
- Mengubah SSL settings
- Menambah security headers

Setelah perubahan, restart container:
```bash
docker compose restart nginx
```

### Database Configuration

Edit `docker-compose.yml` untuk mengubah:
- MariaDB version
- Database credentials
- Volume mounts

**Note**: Perubahan credentials database memerlukan recreate database container:
```bash
docker compose down -v
docker compose up -d
```

## 🗄️ Database Management

### Backup Database
```bash
docker compose exec mariadb mysqldump -u root -p${DB_ROOT_PASSWORD} perjadin_db > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restore Database
```bash
docker compose exec -i mariadb mariadb -u root -p${DB_ROOT_PASSWORD} perjadin_db < backup_file.sql
```

### Run Migrations
```bash
docker compose exec php-fpm php artisan migrate
```

### Run Seeders
```bash
docker compose exec php-fpm php artisan db:seed
```

### Fresh Migration (HATI-HATI!)
```bash
docker compose exec php-fpm php artisan migrate:fresh --seed
```

## 🔍 Troubleshooting

### Container Tidak Start

#### 1. Cek Logs
```bash
docker compose logs
```

#### 2. Cek Port Conflict
```bash
# Cek apakah port sudah digunakan
lsof -i :80
lsof -i :3306

# Atau ubah port di .env
```

#### 3. Rebuild Container
```bash
docker compose down
docker compose build --no-cache
docker compose up -d
```

### Database Connection Error

#### 1. Cek Database Container
```bash
docker compose ps mariadb
```

#### 2. Cek Database Logs
```bash
docker compose logs mariadb
```

#### 3. Test Connection
```bash
docker compose exec php-fpm php -r "
try {
    \$pdo = new PDO('mysql:host=mariadb;dbname=perjadin_db', 'perjadin_user', 'perjadin_pass');
    echo 'Connection OK';
} catch (Exception \$e) {
    echo 'Connection Failed: ' . \$e->getMessage();
}
"
```

#### 4. Verifikasi Credentials
Pastikan credentials di `.env` sesuai dengan `docker-compose.yml`:
```env
DB_HOST=mariadb
DB_DATABASE=perjadin_db
DB_USERNAME=perjadin_user
DB_PASSWORD=perjadin_pass
```

### Permission Issues

#### Fix Storage Permissions
```bash
docker compose exec php-fpm chown -R www-data:www-data storage bootstrap/cache
docker compose exec php-fpm chmod -R 775 storage bootstrap/cache
```

### Application Key Missing

Generate application key:
```bash
docker compose exec php-fpm php artisan key:generate
```

### Cache Issues

Clear all cache:
```bash
docker compose exec php-fpm php artisan cache:clear
docker compose exec php-fpm php artisan config:clear
docker compose exec php-fpm php artisan route:clear
docker compose exec php-fpm php artisan view:clear
```

Kemudian rebuild cache:
```bash
docker compose exec php-fpm php artisan config:cache
docker compose exec php-fpm php artisan route:cache
docker compose exec php-fpm php artisan view:cache
```

## 📊 Development Mode

### Enable Development Assets (Node.js)

Untuk development dengan hot reload assets:
```bash
docker compose --profile dev up -d
```

Ini akan start Node.js container untuk `npm run dev`.

### Access Tinker
```bash
docker compose exec php-fpm php artisan tinker
```

### Run Tests
```bash
docker compose exec php-fpm php artisan test
```

## 🚢 Production Deployment

### 1. Update Environment
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

### 2. Generate Application Key
```bash
docker compose exec php-fpm php artisan key:generate --force
```

### 3. Optimize Application
```bash
docker compose exec php-fpm php artisan config:cache
docker compose exec php-fpm php artisan route:cache
docker compose exec php-fpm php artisan view:cache
docker compose exec php-fpm composer install --no-dev --optimize-autoloader
```

### 4. Build Production Assets
```bash
docker compose exec php-fpm npm run build
```

### 5. Setup SSL (Recommended)
Tambahkan SSL configuration di `docker/nginx/default.conf` untuk production.

## 📁 Struktur File Docker

```
.
├── docker/
│   ├── nginx/
│   │   ├── nginx.conf          # Main Nginx config
│   │   └── default.conf        # Virtual host config
│   ├── php/
│   │   ├── php.ini             # PHP configuration
│   │   └── php-fpm.conf        # PHP-FPM pool config
│   ├── mariadb/
│   │   └── init/               # SQL init scripts
│   └── entrypoint.sh           # Container entrypoint
├── Dockerfile                  # PHP-FPM Dockerfile
├── docker-compose.yml          # Docker Compose config
├── docker-compose.env.example  # Environment variables example
└── .dockerignore              # Docker ignore file
```

## 🔐 Security Considerations

1. **Change Default Passwords**: Update semua password default di `.env`
2. **Use Strong Passwords**: Gunakan password yang kuat untuk database
3. **Limit Network Access**: Jangan expose database port ke public
4. **Enable HTTPS**: Setup SSL untuk production
5. **Update Regularly**: Keep Docker images updated

## 📚 Resources

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Nginx Documentation](https://nginx.org/en/docs/)
- [MariaDB Documentation](https://mariadb.com/kb/en/)

## 🆘 Support

Jika mengalami masalah:
1. Cek logs: `docker compose logs`
2. Cek status: `docker compose ps`
3. Cek documentation di repository
4. Create issue di repository GitHub

