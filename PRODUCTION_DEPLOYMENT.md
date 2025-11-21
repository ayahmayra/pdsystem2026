# 🚀 Panduan Deployment Production (Tanpa Docker)

## 📋 Daftar Isi
1. [Persyaratan Server](#persyaratan-server)
2. [Persiapan Server](#persiapan-server)
3. [Setup FrankenPHP](#setup-frankenphp)
4. [Setup Database MariaDB](#setup-database-mariadb)
5. [Deploy Aplikasi](#deploy-aplikasi)
6. [Konfigurasi Web Server](#konfigurasi-web-server)
7. [Setup SSL/HTTPS](#setup-sslhttps)
8. [Optimasi Production](#optimasi-production)
9. [Maintenance dan Backup](#maintenance-dan-backup)

---

## 🖥️ Persyaratan Server

### Minimum Requirements
- **OS**: Ubuntu 22.04 LTS / Debian 12 / CentOS 8+
- **CPU**: 2 cores (disarankan 4 cores)
- **RAM**: 4GB (disarankan 8GB+)
- **Storage**: 50GB (disarankan 100GB+)
- **Network**: Koneksi internet stabil

### Software yang Sudah Terinstall
- ✅ PHP 8.4
- ✅ FrankenPHP
- ✅ MariaDB
- ✅ Nginx atau Apache
- ✅ Composer
- ✅ Node.js dan NPM

---

## 🔧 Persiapan Server

### 1. Update Sistem

```bash
sudo apt update && sudo apt upgrade -y
```

### 2. Install Dependencies Tambahan (jika belum ada)

```bash
# PHP Extensions yang diperlukan
sudo apt install -y \
    php8.4-cli \
    php8.4-fpm \
    php8.4-mysql \
    php8.4-mbstring \
    php8.4-xml \
    php8.4-curl \
    php8.4-zip \
    php8.4-gd \
    php8.4-intl \
    php8.4-bcmath \
    php8.4-redis \
    php8.4-opcache

# Tools lainnya
sudo apt install -y \
    git \
    curl \
    unzip \
    supervisor
```

### 3. Verifikasi Instalasi

```bash
# Cek PHP version
php -v

# Cek FrankenPHP
frankenphp version

# Cek MariaDB
mariadb --version

# Cek Composer
composer --version

# Cek Node.js
node -v
npm -v
```

---

## 🐘 Setup FrankenPHP

### 1. Konfigurasi FrankenPHP

Edit file konfigurasi FrankenPHP:

```bash
sudo nano /etc/frankenphp/frankenphp.ini
```

Atau jika menggunakan konfigurasi PHP:

```bash
sudo nano /etc/php/8.4/frankenphp/php.ini
```

Pastikan konfigurasi berikut:

```ini
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 50M
post_max_size = 50M

; OPcache untuk production
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1

; Timezone
date.timezone = Asia/Jakarta
```

### 2. Restart FrankenPHP

```bash
sudo systemctl restart frankenphp
sudo systemctl enable frankenphp
```

### 3. Cek Status

```bash
sudo systemctl status frankenphp
```

---

## 🗄️ Setup Database MariaDB

### 1. Buat Database dan User

```bash
sudo mariadb
```

Di dalam MariaDB:

```sql
-- Buat database
CREATE DATABASE pdsystem_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Buat user
CREATE USER 'pdsystem_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';

-- Berikan privileges
GRANT ALL PRIVILEGES ON pdsystem_production.* TO 'pdsystem_user'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;

-- Exit
EXIT;
```

### 2. Optimasi MariaDB untuk Production

Edit `/etc/mysql/mariadb.conf.d/50-server.cnf`:

```ini
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
max_connections = 500
query_cache_type = 1
query_cache_size = 64M
```

Restart MariaDB:

```bash
sudo systemctl restart mariadb
```

---

## 📦 Deploy Aplikasi

### 1. Clone atau Upload Kode

```bash
# Buat direktori aplikasi
sudo mkdir -p /var/www/pdsystem
cd /var/www/pdsystem

# Clone repository (jika menggunakan Git)
sudo git clone <repository-url> .

# Atau upload kode menggunakan SCP/SFTP
# scp -r /path/to/local/code user@server:/var/www/pdsystem
```

### 2. Set Ownership dan Permissions

```bash
# Set ownership ke user web server (biasanya www-data)
sudo chown -R www-data:www-data /var/www/pdsystem

# Set permissions
sudo chmod -R 755 /var/www/pdsystem
sudo chmod -R 775 /var/www/pdsystem/storage
sudo chmod -R 775 /var/www/pdsystem/bootstrap/cache
```

### 3. Install Dependencies

```bash
cd /var/www/pdsystem

# Install PHP dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies
npm install

# Build assets
npm run build
```

### 4. Setup Environment

```bash
# Copy environment file
cp .env.example .env

# Edit environment
nano .env
```

Konfigurasi `.env` untuk production:

```env
APP_NAME="PD System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pdsystem_production
DB_USERNAME=pdsystem_user
DB_PASSWORD=STRONG_PASSWORD_HERE

# Timezone
TZ=Asia/Jakarta

# Cache
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Flux Pro Token (jika menggunakan Flux Pro)
FLUX_PRO_TOKEN=your_flux_pro_token_here
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Run Migrations

```bash
# Run migrations
php artisan migrate --force

# Run seeders (jika diperlukan)
php artisan db:seed --force
```

### 7. Setup Storage Link

```bash
php artisan storage:link
```

### 8. Install dan Aktifkan Flux Pro (jika menggunakan)

```bash
# Install livewire/flux
composer require livewire/flux:^2.2 --no-interaction

# Aktifkan Flux Pro (interaktif - akan meminta token)
php artisan flux:activate
# Paste token ketika diminta

# Verifikasi
php artisan flux:status
```

### 9. Optimasi untuk Production

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer install --no-dev --optimize-autoloader --no-interaction
```

---

## 🌐 Konfigurasi Web Server

### Opsi 1: Nginx dengan FrankenPHP

Buat file konfigurasi Nginx:

```bash
sudo nano /etc/nginx/sites-available/pdsystem
```

Konfigurasi:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/pdsystem/public;

    index index.php index.html;

    charset utf-8;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Laravel routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # FrankenPHP
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/frankenphp.sock;
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

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/pdsystem /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Opsi 2: Apache dengan FrankenPHP

Buat file konfigurasi Apache:

```bash
sudo nano /etc/apache2/sites-available/pdsystem.conf
```

Konfigurasi:

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/pdsystem/public

    <Directory /var/www/pdsystem/public>
        AllowOverride All
        Require all granted
    </Directory>

    # FrankenPHP
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/var/run/frankenphp.sock|fcgi://localhost"
    </FilesMatch>

    ErrorLog ${APACHE_LOG_DIR}/pdsystem_error.log
    CustomLog ${APACHE_LOG_DIR}/pdsystem_access.log combined
</VirtualHost>
```

Enable site:

```bash
sudo a2ensite pdsystem.conf
sudo a2enmod proxy_fcgi setenvif
sudo systemctl reload apache2
```

---

## 🔒 Setup SSL/HTTPS

### Menggunakan Let's Encrypt (Gratis)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y
# Atau untuk Apache:
# sudo apt install certbot python3-certbot-apache -y

# Generate certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
# Atau untuk Apache:
# sudo certbot --apache -d yourdomain.com -d www.yourdomain.com

# Test auto-renewal
sudo certbot renew --dry-run
```

### Setup Auto-Renewal

Certbot sudah setup cron job otomatis. Verifikasi:

```bash
sudo systemctl status certbot.timer
```

---

## ⚡ Optimasi Production

### 1. Setup Supervisor untuk Queue (jika menggunakan)

```bash
sudo nano /etc/supervisor/conf.d/pdsystem-queue.conf
```

```ini
[program:pdsystem-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/pdsystem/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/pdsystem/storage/logs/queue.log
stopwaitsecs=3600
```

Reload supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start pdsystem-queue:*
```

### 2. Setup Log Rotation

```bash
sudo nano /etc/logrotate.d/pdsystem
```

```
/var/www/pdsystem/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

### 3. Setup Cron Job untuk Laravel Scheduler

```bash
sudo crontab -e -u www-data
```

Tambahkan:

```
* * * * * cd /var/www/pdsystem && php artisan schedule:run >> /dev/null 2>&1
```

---

## 💾 Maintenance dan Backup

### 1. Backup Database

Buat script backup:

```bash
sudo nano /usr/local/bin/backup-pdsystem-db.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/pdsystem"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="pdsystem_production"
DB_USER="pdsystem_user"
DB_PASS="YOUR_PASSWORD"

mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Hapus backup lebih dari 30 hari
find $BACKUP_DIR -name "db_*.sql.gz" -mtime +30 -delete

echo "Backup completed: db_$DATE.sql.gz"
```

```bash
sudo chmod +x /usr/local/bin/backup-pdsystem-db.sh

# Setup cron job (backup setiap hari jam 2 pagi)
sudo crontab -e
# Tambahkan:
0 2 * * * /usr/local/bin/backup-pdsystem-db.sh
```

### 2. Backup Files

```bash
sudo nano /usr/local/bin/backup-pdsystem-files.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/pdsystem"
DATE=$(date +%Y%m%d_%H%M%S)
APP_DIR="/var/www/pdsystem"

mkdir -p $BACKUP_DIR

# Backup storage
tar -czf $BACKUP_DIR/files_$DATE.tar.gz -C $APP_DIR storage/app/public

# Hapus backup lebih dari 30 hari
find $BACKUP_DIR -name "files_*.tar.gz" -mtime +30 -delete

echo "Files backup completed: files_$DATE.tar.gz"
```

```bash
sudo chmod +x /usr/local/bin/backup-pdsystem-files.sh
```

### 3. Update Aplikasi

```bash
cd /var/www/pdsystem

# Backup database dulu
/usr/local/bin/backup-pdsystem-db.sh

# Pull update (jika menggunakan Git)
sudo -u www-data git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader --no-interaction
npm install
npm run build

# Run migrations
php artisan migrate --force

# Clear dan rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl restart frankenphp
sudo systemctl reload nginx  # atau apache2
```

---

## 🔍 Monitoring dan Troubleshooting

### 1. Cek Logs

```bash
# Laravel logs
tail -f /var/www/pdsystem/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log

# FrankenPHP logs
sudo journalctl -u frankenphp -f

# MariaDB logs
sudo tail -f /var/log/mysql/error.log
```

### 2. Cek Status Services

```bash
# FrankenPHP
sudo systemctl status frankenphp

# Nginx
sudo systemctl status nginx

# MariaDB
sudo systemctl status mariadb

# Supervisor (jika menggunakan queue)
sudo supervisorctl status
```

### 3. Common Issues

#### Permission Issues

```bash
sudo chown -R www-data:www-data /var/www/pdsystem
sudo chmod -R 755 /var/www/pdsystem
sudo chmod -R 775 /var/www/pdsystem/storage
sudo chmod -R 775 /var/www/pdsystem/bootstrap/cache
```

#### 500 Error

```bash
# Cek Laravel logs
tail -50 /var/www/pdsystem/storage/logs/laravel.log

# Cek PHP errors
tail -50 /var/log/php8.4-fpm.log  # atau frankenphp log

# Cek permissions
ls -la /var/www/pdsystem/storage
ls -la /var/www/pdsystem/bootstrap/cache
```

#### Database Connection Error

```bash
# Test connection
mysql -u pdsystem_user -p pdsystem_production

# Cek .env
cat /var/www/pdsystem/.env | grep DB_
```

---

## 📝 Checklist Deployment

- [ ] Server sudah disiapkan dengan semua dependencies
- [ ] FrankenPHP sudah dikonfigurasi dan running
- [ ] MariaDB sudah dikonfigurasi dan database dibuat
- [ ] Kode aplikasi sudah di-deploy
- [ ] File `.env` sudah dikonfigurasi untuk production
- [ ] Application key sudah di-generate
- [ ] Database sudah di-migrate
- [ ] Storage link sudah dibuat
- [ ] Assets sudah di-build
- [ ] Flux Pro sudah diaktifkan (jika menggunakan)
- [ ] Web server (Nginx/Apache) sudah dikonfigurasi
- [ ] SSL/HTTPS sudah di-setup
- [ ] Cache sudah di-optimize
- [ ] Queue worker sudah di-setup (jika menggunakan)
- [ ] Cron job untuk scheduler sudah di-setup
- [ ] Backup otomatis sudah dikonfigurasi
- [ ] Monitoring sudah di-setup

---

## 🆘 Support

Jika mengalami masalah:

1. Cek logs: `tail -f /var/www/pdsystem/storage/logs/laravel.log`
2. Cek status services: `sudo systemctl status frankenphp nginx mariadb`
3. Cek permissions: `ls -la /var/www/pdsystem/storage`
4. Cek konfigurasi: `cat /var/www/pdsystem/.env`

---

**Catatan**: Pastikan untuk mengganti semua placeholder (yourdomain.com, password, dll) dengan nilai yang sesuai dengan environment Anda.

