# PD System 2026

Sistem Manajemen Perjalanan Dinas (PD System) - Aplikasi Laravel untuk mengelola dokumen perjalanan dinas.

## 📋 Persyaratan

- PHP 8.4 atau lebih tinggi
- MariaDB 10.5+ atau MySQL 8.0+
- Composer
- Node.js 20+ dan NPM
- FrankenPHP (untuk production)
- Nginx atau Apache

## 🚀 Instalasi

Lihat panduan lengkap di [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md)

### Quick Start

```bash
# Clone repository
git clone <repository-url>
cd pdsystem2026

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Konfigurasi database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_DATABASE=your_database
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed

# Build assets
npm run build

# Start development server
php artisan serve
```

## 🚢 Deployment Production

Untuk deployment ke server production, lihat panduan lengkap di [PRODUCTION_DEPLOYMENT.md](PRODUCTION_DEPLOYMENT.md)

Panduan mencakup:
- Setup FrankenPHP
- Konfigurasi Nginx/Apache
- Setup SSL/HTTPS
- Optimasi production
- Backup dan maintenance

## 📚 Dokumentasi

- [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md) - Panduan instalasi lengkap
- [PRODUCTION_DEPLOYMENT.md](PRODUCTION_DEPLOYMENT.md) - Panduan deployment production

## 🔧 Fitur Utama

- Manajemen Nota Dinas
- Surat Perintah Tugas (SPT)
- Surat Perintah Perjalanan Dinas (SPPD)
- Kwitansi
- Laporan Perjalanan
- Manajemen Master Data
- Sistem Role dan Permission
- Export PDF

## 📝 License

MIT License

