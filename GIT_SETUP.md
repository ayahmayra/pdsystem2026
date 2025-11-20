# Setup Git Repository PDSYSTEM2026

## 📋 Overview

Panduan untuk setup repository Git baru dengan nama **PDSYSTEM2026**.

## 🚀 Setup Remote Repository

### Opsi 1: Membuat Repository Baru di GitHub/GitLab

#### A. Buat Repository di GitHub/GitLab
1. Login ke GitHub/GitLab
2. Buat repository baru dengan nama: **PDSYSTEM2026**
3. **Jangan** initialize dengan README, .gitignore, atau license (karena sudah ada)

#### B. Update Remote URL

Setelah repository dibuat, update remote URL:

```bash
# Ganti URL dengan URL repository Anda
git remote set-url origin https://github.com/username/PDSYSTEM2026.git

# Atau jika menggunakan SSH
git remote set-url origin git@github.com:username/PDSYSTEM2026.git

# Verifikasi remote
git remote -v
```

#### C. Push ke Repository Baru

```bash
# Push semua branch
git push -u origin main

# Atau jika branch bernama master
git push -u origin master
```

### Opsi 2: Menambahkan Remote Baru (Tanpa Mengganti yang Lama)

Jika ingin tetap mempertahankan remote lama dan menambahkan remote baru:

```bash
# Tambahkan remote baru dengan nama 'neworigin'
git remote add neworigin https://github.com/username/PDSYSTEM2026.git

# Push ke remote baru
git push -u neworigin main

# Verifikasi
git remote -v
```

### Opsi 3: Fresh Start (Repository Baru Penuh)

Jika ingin membuat repository baru dari awal tanpa history lama:

```bash
# 1. Hapus folder .git
rm -rf .git

# 2. Inisialisasi repository baru
git init

# 3. Tambahkan semua file
git add .

# 4. Buat initial commit
git commit -m "Initial commit: PDSYSTEM2026 - Sistem Perjalanan Dinas dengan Docker"

# 5. Rename branch ke main (jika perlu)
git branch -M main

# 6. Tambahkan remote
git remote add origin https://github.com/username/PDSYSTEM2026.git

# 7. Push ke repository
git push -u origin main
```

## 📝 Commit Message yang Disarankan

Setelah melakukan setup Docker, gunakan commit message yang deskriptif:

```bash
git commit -m "feat: Setup Docker configuration dengan PHP 8.3, MariaDB, dan Nginx

- Upgrade PHP dari 8.2 ke 8.3
- Konfigurasi Docker Compose terbaru (tanpa version)
- Optimasi resource allocation untuk server 7.8GB RAM, 4 cores
- PHP memory limit: 1536M
- PHP-FPM: 80 max children, optimized untuk high traffic
- MariaDB: 1GB buffer pool, 500 max connections
- Dokumentasi lengkap untuk setup dan deployment"
```

## 🔧 Konfigurasi Git (Opsional)

### Setup User Info (Jika Belum)
```bash
git config user.name "Your Name"
git config user.email "your.email@example.com"
```

### Setup Branch Protection
Di GitHub/GitLab, aktifkan branch protection untuk `main` branch:
- Require pull request reviews
- Require status checks to pass
- Require branches to be up to date

### Setup .gitattributes (Jika Perlu)
```bash
# Buat file .gitattributes
cat > .gitattributes << EOF
* text=auto
*.php text eol=lf
*.js text eol=lf
*.css text eol=lf
*.md text eol=lf
*.json text eol=lf
*.yml text eol=lf
*.yaml text eol=lf
EOF
```

## 📦 File yang Sudah Ditambahkan

File berikut sudah di-stage dan siap untuk commit:

- **Docker Configuration**:
  - `Dockerfile` - PHP 8.3 FPM image
  - `docker-compose.yml` - Multi-container setup
  - `.dockerignore` - Docker build context ignore
  
- **Docker Config Files**:
  - `docker/nginx/nginx.conf` - Nginx main config
  - `docker/nginx/default.conf` - Laravel virtual host
  - `docker/php/php.ini` - PHP configuration (1536M memory)
  - `docker/php/php-fpm.conf` - PHP-FPM pool config (80 max children)
  - `docker/mariadb/my.cnf` - MariaDB optimization (1GB buffer pool)
  - `docker/entrypoint.sh` - Container entrypoint script
  
- **Environment & Documentation**:
  - `docker-compose.env.example` - Environment variables template
  - `DOCKER_SETUP.md` - Full documentation
  - `DOCKER_QUICKSTART.md` - Quick start guide
  - `.gitignore` - Updated dengan Docker exclusions

## ✅ Checklist Sebelum Push

- [ ] Semua file penting sudah di-commit
- [ ] `.env` tidak ter-commit (sudah di .gitignore)
- [ ] `vendor/` tidak ter-commit (sudah di .gitignore)
- [ ] `node_modules/` tidak ter-commit (sudah di .gitignore)
- [ ] Remote URL sudah benar
- [ ] Branch protection sudah di-setup (optional)

## 🚨 Penting

**JANGAN** commit file berikut:
- `.env` - Berisi credentials
- `vendor/` - Composer dependencies
- `node_modules/` - NPM dependencies
- File dengan credentials atau secrets

File-file ini sudah ada di `.gitignore`.

## 📚 Next Steps

Setelah repository setup:

1. **Clone di server production**:
   ```bash
   git clone https://github.com/username/PDSYSTEM2026.git
   cd PDSYSTEM2026
   ```

2. **Setup environment**:
   ```bash
   cp docker-compose.env.example .env
   # Edit .env sesuai kebutuhan production
   ```

3. **Deploy dengan Docker**:
   ```bash
   docker compose build
   docker compose up -d
   ```

4. **Setup aplikasi**:
   ```bash
   docker compose exec php-fpm php artisan migrate
   docker compose exec php-fpm php artisan db:seed
   ```

