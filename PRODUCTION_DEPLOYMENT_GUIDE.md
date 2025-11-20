# 🚀 Panduan Deployment Production dengan Docker

## 📋 Daftar Isi
1. [Persiapan Server](#persiapan-server)
2. [Setup Docker Permission](#setup-docker-permission)
3. [Persiapan Repository](#persiapan-repository)
4. [Konfigurasi Environment Production](#konfigurasi-environment-production)
5. [Build dan Deploy](#build-dan-deploy)
6. [Setup SSL/HTTPS](#setup-sslhttps)
7. [Optimasi Production](#optimasi-production)
8. [Backup dan Maintenance](#backup-dan-maintenance)
9. [Monitoring dan Troubleshooting](#monitoring-dan-troubleshooting)
10. [Security Best Practices](#security-best-practices)

---

## 🖥️ Persiapan Server

### 1. Persyaratan Sistem Minimum

- **OS**: Ubuntu 20.04 LTS atau lebih baru / Debian 11 atau lebih baru
- **CPU**: 4 cores (disarankan)
- **RAM**: 8GB minimum (disarankan 16GB)
- **Storage**: 50GB minimum (disarankan 100GB+)
- **Network**: Koneksi internet stabil

### 2. Install Dependencies

```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo apt install docker-compose-plugin -y

# Verifikasi instalasi
docker --version
docker compose version
```

### 3. Setup Firewall

```bash
# Install UFW jika belum ada
sudo apt install ufw -y

# Allow SSH (penting!)
sudo ufw allow 22/tcp

# Allow HTTP dan HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Enable firewall
sudo ufw enable

# Cek status
sudo ufw status
```

---

## 🔐 Setup Docker Permission

### Masalah: Permission Denied

Jika mendapat error:
```
permission denied while trying to connect to the Docker daemon socket
```

### Solusi 1: Tambahkan User ke Grup Docker (Direkomendasikan)

```bash
# Tambahkan user ke grup docker
sudo usermod -aG docker $USER

# Atau untuk user tertentu (contoh: sysadmin)
sudo usermod -aG docker sysadmin

# Logout dan login kembali, atau jalankan:
newgrp docker

# Verifikasi
docker ps
```

### Solusi 2: Gunakan Sudo (Alternatif)

Jika tidak bisa menambahkan ke grup, gunakan `sudo`:

```bash
sudo docker compose build
sudo docker compose up -d
```

**Catatan**: Untuk production, lebih baik menggunakan Solusi 1 agar tidak perlu `sudo` setiap kali.

---

## 📦 Persiapan Repository

### 1. Clone atau Upload Kode ke Server

```bash
# Buat direktori aplikasi
sudo mkdir -p /opt/apps
cd /opt/apps

# Clone repository (jika menggunakan Git)
git clone <repository-url> pdsystem2026
cd pdsystem2026

# Atau upload kode menggunakan SCP/SFTP
# scp -r /path/to/local/code user@server:/opt/apps/pdsystem2026
```

### 2. Setup Ownership

```bash
# Set ownership ke user yang akan menjalankan Docker
sudo chown -R $USER:$USER /opt/apps/pdsystem2026

# Atau untuk user tertentu
sudo chown -R sysadmin:sysadmin /opt/apps/pdsystem2026
```

---

## ⚙️ Konfigurasi Environment Production

### 1. Buat File Environment

```bash
cd /opt/apps/pdsystem2026

# Copy dari example
cp docker-compose.env.example .env

# Edit file .env
nano .env
```

### 2. Konfigurasi Environment Production

Edit file `.env` dengan konfigurasi berikut:

```env
# Application Environment
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_PORT=80
APP_PORT_SSL=443

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=pdsystem_production
DB_USERNAME=pdsystem_user
DB_PASSWORD=CHANGE_THIS_STRONG_PASSWORD
DB_ROOT_PASSWORD=CHANGE_THIS_STRONG_ROOT_PASSWORD

# Application Key (akan di-generate otomatis)
APP_KEY=

# Timezone
TZ=Asia/Jakarta

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="PD System"

# Cache Configuration (Production)
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
```

**⚠️ PENTING**: 
- Ganti semua password dengan password yang kuat
- Ganti `yourdomain.com` dengan domain Anda
- Set `APP_DEBUG=false` untuk production
- Set `LOG_LEVEL=error` untuk mengurangi log

### 3. Secure File .env

```bash
# Set permission yang aman
chmod 600 .env

# Pastikan tidak ter-commit ke Git
echo ".env" >> .gitignore
```

---

## 🏗️ Build dan Deploy

### 1. Build Docker Images

```bash
cd /opt/apps/pdsystem2026

# Build images (pertama kali akan memakan waktu 10-15 menit)
docker compose build --no-cache

# Atau jika menggunakan sudo
sudo docker compose build --no-cache
```

### 2. Start Services

```bash
# Start semua services
docker compose up -d

# Cek status
docker compose ps

# Cek logs
docker compose logs -f
```

### 3. Verifikasi Services

```bash
# Cek apakah semua container running
docker compose ps

# Expected output:
# NAME                   STATUS          PORTS
# pdsystem_mariadb       Up (healthy)    0.0.0.0:3306->3306/tcp
# pdsystem_nginx         Up              0.0.0.0:80->80/tcp, 0.0.0.0:443->443/tcp
# pdsystem_php_fpm       Up              9000/tcp
```

### 4. Setup Database

```bash
# Generate application key
docker compose exec php-fpm php artisan key:generate --force

# Run migrations
docker compose exec php-fpm php artisan migrate --force

# Run seeders (jika diperlukan)
docker compose exec php-fpm php artisan db:seed --force
```

### 5. Optimasi untuk Production

```bash
# Clear semua cache
docker compose exec php-fpm php artisan cache:clear
docker compose exec php-fpm php artisan config:clear
docker compose exec php-fpm php artisan route:clear
docker compose exec php-fpm php artisan view:clear

# Rebuild cache untuk production
docker compose exec php-fpm php artisan config:cache
docker compose exec php-fpm php artisan route:cache
docker compose exec php-fpm php artisan view:cache

# Optimize autoloader
docker compose exec php-fpm composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
docker compose exec php-fpm chown -R www-data:www-data storage bootstrap/cache
docker compose exec php-fpm chmod -R 775 storage bootstrap/cache
```

---

## 🔒 Setup SSL/HTTPS

### Opsi 1: Menggunakan Let's Encrypt (Gratis, Direkomendasikan)

#### 1. Install Certbot

```bash
sudo apt install certbot python3-certbot-nginx -y
```

#### 2. Generate SSL Certificate

```bash
# Stop nginx container sementara
docker compose stop nginx

# Generate certificate
sudo certbot certonly --standalone -d yourdomain.com -d www.yourdomain.com

# Certificate akan tersimpan di:
# /etc/letsencrypt/live/yourdomain.com/fullchain.pem
# /etc/letsencrypt/live/yourdomain.com/privkey.pem
```

#### 3. Update Nginx Configuration

Buat file konfigurasi SSL untuk production:

```bash
nano docker/nginx/default-ssl.conf
```

Tambahkan konfigurasi berikut:

```nginx
# HTTP to HTTPS redirect
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

# HTTPS server
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/html/public;

    index index.php index.html index.htm;

    charset utf-8;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    # Security headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Laravel routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM configuration
    location ~ \.php$ {
        fastcgi_pass php-fpm:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Deny access to sensitive files
    location ~ /\.(env|git|svn|hg) {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Storage and bootstrap cache
    location ~* ^/(storage|bootstrap/cache)/ {
        deny all;
    }

    # Error pages
    error_page 404 /index.php;
    error_page 500 502 503 504 /50x.html;
    
    location = /50x.html {
        root /usr/share/nginx/html;
    }
}
```

#### 4. Update docker-compose.yml untuk Mount SSL Certificates

Edit `docker-compose.yml`, tambahkan volume untuk SSL certificates di service nginx:

```yaml
nginx:
  image: nginx:alpine
  container_name: pdsystem_nginx
  restart: unless-stopped
  ports:
    - "${APP_PORT:-80}:80"
    - "${APP_PORT_SSL:-443}:443"
  volumes:
    - .:/var/www/html
    - ./docker/nginx/nginx.conf:/etc/nginx/nginx.conf
    - ./docker/nginx/default-ssl.conf:/etc/nginx/conf.d/default.conf
    - /etc/letsencrypt:/etc/letsencrypt:ro  # Mount SSL certificates
  depends_on:
    - php-fpm
  networks:
    - pdsystem_network
```

#### 5. Restart Nginx

```bash
# Restart nginx dengan konfigurasi baru
docker compose restart nginx

# Cek logs
docker compose logs nginx
```

#### 6. Setup Auto-Renewal SSL

```bash
# Test renewal
sudo certbot renew --dry-run

# Setup cron job untuk auto-renewal
sudo crontab -e

# Tambahkan baris berikut (renew setiap hari jam 3 pagi)
0 3 * * * certbot renew --quiet --deploy-hook "docker compose -f /opt/apps/pdsystem2026/docker-compose.yml restart nginx"
```

### Opsi 2: Menggunakan SSL Certificate dari Provider

Jika Anda memiliki SSL certificate dari provider lain:

1. Upload certificate ke server
2. Mount certificate ke nginx container
3. Update konfigurasi nginx dengan path certificate yang benar

---

## ⚡ Optimasi Production

### 1. Optimasi PHP-FPM

Edit `docker/php/php-fpm.conf` untuk production:

```ini
[www]
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
```

### 2. Optimasi PHP

Edit `docker/php/php.ini` untuk production:

```ini
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 50M
post_max_size = 50M
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### 3. Optimasi MariaDB

Edit `docker/mariadb/my.cnf` untuk production:

```ini
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
max_connections = 500
query_cache_type = 1
query_cache_size = 64M
```

### 4. Setup Log Rotation

Buat script untuk rotate logs:

```bash
nano /opt/apps/pdsystem2026/docker/logrotate.sh
```

```bash
#!/bin/bash
# Rotate Laravel logs
find /opt/apps/pdsystem2026/storage/logs -name "*.log" -mtime +7 -delete
```

```bash
chmod +x /opt/apps/pdsystem2026/docker/logrotate.sh

# Tambahkan ke crontab
crontab -e
# Tambahkan: 0 2 * * * /opt/apps/pdsystem2026/docker/logrotate.sh
```

---

## 💾 Backup dan Maintenance

### 1. Backup Database

Buat script backup otomatis:

```bash
nano /opt/apps/pdsystem2026/docker/backup-db.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/opt/backups/pdsystem"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="pdsystem_production"
DB_USER="pdsystem_user"
DB_PASS="your_password"

mkdir -p $BACKUP_DIR

# Backup database
docker compose exec -T mariadb mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Hapus backup lebih dari 30 hari
find $BACKUP_DIR -name "db_*.sql.gz" -mtime +30 -delete

echo "Backup completed: db_$DATE.sql.gz"
```

```bash
chmod +x /opt/apps/pdsystem2026/docker/backup-db.sh

# Setup cron job (backup setiap hari jam 2 pagi)
crontab -e
# Tambahkan: 0 2 * * * /opt/apps/pdsystem2026/docker/backup-db.sh
```

### 2. Backup Files

```bash
nano /opt/apps/pdsystem2026/docker/backup-files.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/opt/backups/pdsystem"
DATE=$(date +%Y%m%d_%H%M%S)
APP_DIR="/opt/apps/pdsystem2026"

mkdir -p $BACKUP_DIR

# Backup storage dan uploads
tar -czf $BACKUP_DIR/files_$DATE.tar.gz -C $APP_DIR storage/app/public

# Hapus backup lebih dari 30 hari
find $BACKUP_DIR -name "files_*.tar.gz" -mtime +30 -delete

echo "Files backup completed: files_$DATE.tar.gz"
```

### 3. Restore Database

```bash
# Restore dari backup
gunzip < /opt/backups/pdsystem/db_20250101_020000.sql.gz | docker compose exec -T mariadb mysql -u pdsystem_user -p'your_password' pdsystem_production
```

### 4. Update Aplikasi

```bash
cd /opt/apps/pdsystem2026

# Backup database dulu
./docker/backup-db.sh

# Pull update dari Git (jika menggunakan Git)
git pull origin main

# Rebuild images jika ada perubahan Dockerfile
docker compose build --no-cache php-fpm

# Restart services
docker compose up -d

# Run migrations
docker compose exec php-fpm php artisan migrate --force

# Clear dan rebuild cache
docker compose exec php-fpm php artisan config:cache
docker compose exec php-fpm php artisan route:cache
docker compose exec php-fpm php artisan view:cache
```

---

## 📊 Monitoring dan Troubleshooting

### 1. Monitor Container Status

```bash
# Cek status semua container
docker compose ps

# Cek resource usage
docker stats

# Cek logs real-time
docker compose logs -f

# Cek logs service tertentu
docker compose logs -f php-fpm
docker compose logs -f nginx
docker compose logs -f mariadb
```

### 2. Monitor Disk Space

```bash
# Cek disk usage
df -h

# Cek Docker disk usage
docker system df

# Clean up unused Docker resources
docker system prune -a --volumes
```

### 3. Monitor Database

```bash
# Akses database
docker compose exec mariadb mariadb -u pdsystem_user -p pdsystem_production

# Cek ukuran database
docker compose exec mariadb mariadb -u root -p -e "SELECT table_schema AS 'Database', ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)' FROM information_schema.tables WHERE table_schema = 'pdsystem_production' GROUP BY table_schema;"
```

### 4. Troubleshooting Common Issues

#### Container Tidak Start

```bash
# Cek logs
docker compose logs

# Restart semua services
docker compose restart

# Rebuild jika diperlukan
docker compose down
docker compose build --no-cache
docker compose up -d
```

#### Database Connection Error

```bash
# Cek apakah database container running
docker compose ps mariadb

# Test connection
docker compose exec php-fpm php -r "try { \$pdo = new PDO('mysql:host=mariadb;dbname=pdsystem_production', 'pdsystem_user', 'password'); echo 'OK'; } catch (Exception \$e) { echo 'Error: ' . \$e->getMessage(); }"
```

#### Permission Issues

```bash
# Fix storage permissions
docker compose exec php-fpm chown -R www-data:www-data storage bootstrap/cache
docker compose exec php-fpm chmod -R 775 storage bootstrap/cache
```

#### High Memory Usage

```bash
# Cek memory usage
docker stats

# Restart services
docker compose restart

# Atau scale down jika diperlukan
```

---

## 🔐 Security Best Practices

### 1. Firewall Configuration

```bash
# Hanya allow port yang diperlukan
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw deny 3306/tcp   # Database (jangan expose ke public)
```

### 2. Database Security

- Gunakan password yang kuat
- Jangan expose port database ke public
- Gunakan user dengan privilege minimal
- Backup database secara rutin

### 3. Application Security

- Set `APP_DEBUG=false` di production
- Set `LOG_LEVEL=error` untuk mengurangi informasi sensitif
- Gunakan HTTPS/SSL
- Update dependencies secara rutin
- Monitor logs untuk aktivitas mencurigakan

### 4. Docker Security

```bash
# Jangan run container sebagai root
# Gunakan user non-root

# Update Docker images secara rutin
docker compose pull
docker compose up -d

# Scan images untuk vulnerabilities
docker scout cves <image-name>
```

### 5. File Permissions

```bash
# Set permission yang aman
chmod 600 .env
chmod 755 storage
chmod 755 bootstrap/cache
```

### 6. Regular Updates

```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Update Docker
sudo apt install docker-ce docker-ce-cli containerd.io -y

# Update aplikasi
cd /opt/apps/pdsystem2026
git pull
docker compose build
docker compose up -d
```

---

## 📝 Checklist Deployment

- [ ] Server sudah disiapkan dengan spesifikasi yang cukup
- [ ] Docker dan Docker Compose sudah terinstall
- [ ] User sudah ditambahkan ke grup docker
- [ ] Firewall sudah dikonfigurasi
- [ ] Repository sudah di-clone/upload ke server
- [ ] File `.env` sudah dikonfigurasi untuk production
- [ ] Password database sudah diganti dengan yang kuat
- [ ] Docker images sudah di-build
- [ ] Services sudah running
- [ ] Database sudah di-migrate
- [ ] Application key sudah di-generate
- [ ] Cache sudah di-optimize
- [ ] SSL/HTTPS sudah di-setup
- [ ] Backup otomatis sudah dikonfigurasi
- [ ] Monitoring sudah di-setup
- [ ] Security best practices sudah diterapkan

---

## 🆘 Support dan Troubleshooting

Jika mengalami masalah:

1. **Cek Logs**: `docker compose logs -f`
2. **Cek Status**: `docker compose ps`
3. **Cek Resource**: `docker stats`
4. **Cek Disk Space**: `df -h`
5. **Restart Services**: `docker compose restart`
6. **Rebuild**: `docker compose down && docker compose build --no-cache && docker compose up -d`

---

## 📚 Referensi

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Let's Encrypt Documentation](https://letsencrypt.org/docs/)
- [Nginx Documentation](https://nginx.org/en/docs/)
- [MariaDB Documentation](https://mariadb.com/kb/en/)

---

**Catatan**: Panduan ini dibuat untuk deployment production. Pastikan untuk:
- Mengganti semua placeholder (yourdomain.com, password, dll)
- Menyesuaikan konfigurasi dengan kebutuhan server Anda
- Melakukan testing di environment staging sebelum production
- Backup data secara rutin
- Monitor sistem secara berkala

