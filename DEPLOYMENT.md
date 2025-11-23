# Panduan Deployment PD System 2026 ke Ubuntu Server 24.04

Dokumen ini berisi panduan persiapan server production untuk aplikasi PD System 2026.

**Spesifikasi Server:**
- **OS:** Ubuntu Server 24.04 LTS
- **CPU:** 4 Cores
- **RAM:** 8 GB
- **Storage:** 200 GB

## 1. Persiapan Sistem Operasi

Update paket sistem ke versi terbaru:

```bash
sudo apt update && sudo apt upgrade -y
```

Set timezone ke Asia/Jakarta (WIB):

```bash
sudo timedatectl set-timezone Asia/Jakarta
```

## 2. Instalasi Web Server (Nginx)

Nginx direkomendasikan karena performanya yang ringan dan cepat.

```bash
sudo apt install nginx -y
sudo systemctl enable nginx
sudo systemctl start nginx
```

## 3. Instalasi PHP 8.4

Ubuntu 24.04 mungkin belum menyertakan PHP 8.4 secara default. Gunakan PPA dari Ondrej Sury.

```bash
# Install dependencies
sudo apt install software-properties-common -y

# Tambahkan PPA
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.4 dan ekstensi yang dibutuhkan Laravel
sudo apt install php8.4 php8.4-fpm php8.4-mysql php8.4-mbstring php8.4-xml php8.4-bcmath php8.4-curl php8.4-zip php8.4-intl php8.4-gd php8.4-sqlite3 php8.4-tokenizer -y
```

**Optimasi PHP-FPM:**
Edit file `/etc/php/8.4/fpm/php.ini`:
- `memory_limit = 512M`
- `upload_max_filesize = 64M`
- `post_max_size = 64M`
- `max_execution_time = 300`
- `opcache.enable=1`
- `opcache.jit_buffer_size=100M` (Untuk performa maksimal)

## 4. Instalasi Database (MariaDB)

Dengan RAM 8GB, MariaDB sangat mumpuni.

```bash
sudo apt install mariadb-server -y
sudo systemctl enable mariadb
sudo systemctl start mariadb

# Amankan instalasi
sudo mysql_secure_installation
```

**Buat Database & User:**

```sql
sudo mysql -u root -p

CREATE DATABASE pdsystem;
CREATE USER 'pdsystem_user'@'localhost' IDENTIFIED BY 'password_kuat_anda';
GRANT ALL PRIVILEGES ON pdsystem.* TO 'pdsystem_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 5. Instalasi Cache & Queue (Redis)

Redis sangat disarankan untuk session, cache, dan antrian (queue) agar aplikasi terasa responsif.

```bash
sudo apt install redis-server -y
sudo apt install php8.4-redis -y
```

Edit `/etc/redis/redis.conf` dan pastikan `supervised systemd`.

## 6. Instalasi Tools Pendukung

**Composer:**
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

**Node.js & NPM (Untuk build assets):**
```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

**Git:**
```bash
sudo apt install git -y
```

**Supervisor (Untuk menjalankan Queue Worker):**
```bash
sudo apt install supervisor -y
```

## 7. Konfigurasi Nginx

Buat file konfigurasi di `/etc/nginx/sites-available/pdsystem`:

```nginx
server {
    listen 80;
    server_name domain-anda.com; # Ganti dengan domain atau IP server
    root /var/www/pdsystem2026/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan situs:
```bash
sudo ln -s /etc/nginx/sites-available/pdsystem /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 8. Langkah Deployment Aplikasi

1.  **Clone Repository:**
    ```bash
    cd /var/www
    sudo git clone https://github.com/ayahmayra/pdsystem2026.git
    sudo chown -R www-data:www-data pdsystem2026
    sudo chmod -R 775 pdsystem2026/storage pdsystem2026/bootstrap/cache
    ```

2.  **Install Dependencies:**
    ```bash
    cd pdsystem2026
    # Login sebagai user biasa (bukan root) atau gunakan sudo -u www-data
    composer install --optimize-autoloader --no-dev
    npm install
    npm run build
    ```

3.  **Konfigurasi Environment:**
    ```bash
    cp .env.example .env
    nano .env
    ```
    *   Set `APP_ENV=production`
    *   Set `APP_DEBUG=false`
    *   Set `DB_CONNECTION=mysql` (atau mariadb) dan kredensial database.
    *   Set `CACHE_STORE=redis`, `SESSION_DRIVER=redis`, `QUEUE_CONNECTION=redis`.

4.  **Finalisasi:**
    ```bash
    php artisan key:generate
    php artisan migrate --seed --force
    php artisan storage:link
    php artisan config:cache
    php artisan event:cache
    php artisan route:cache
    php artisan view:cache
    ```

## 9. Konfigurasi Supervisor (Queue Worker)

Buat file `/etc/supervisor/conf.d/pdsystem-worker.conf`:

```ini
[program:pdsystem-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/pdsystem2026/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/pdsystem2026/storage/logs/worker.log
stopwaitsecs=3600
```

Jalankan supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start pdsystem-worker:*
```

## 10. SSL (HTTPS)

Jika menggunakan domain, amankan dengan Let's Encrypt:

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d domain-anda.com
```

## 11. Instalasi phpMyAdmin (Opsional)

Untuk manajemen database via web.

1.  **Install phpMyAdmin:**
    ```bash
    sudo apt install phpmyadmin -y
    ```
    *   Saat diminta memilih web server, tekan **TAB** lalu **ENTER** (jangan pilih apache2 atau lighttpd karena kita pakai Nginx).
    *   Pilih **Yes** untuk konfigurasi database dbconfig-common.
    *   Masukkan password untuk user phpmyadmin.

2.  **Buat Symbolic Link:**
    Agar bisa diakses dari web, buat link ke folder public aplikasi:
    ```bash
    sudo ln -s /usr/share/phpmyadmin /var/www/pdsystem2026/public/phpmyadmin
    ```

3.  **Akses:**
    Buka browser dan akses `http://domain-anda.com/phpmyadmin`.
