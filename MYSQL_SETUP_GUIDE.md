# Panduan Setup MySQL untuk PD System 2026

## Ringkasan

Panduan ini menjelaskan cara setup dan migrasi dari SQLite ke MySQL untuk PD System 2026.

## Konfigurasi Database

### File `.env` Configuration

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=pdsystemdb
DB_USERNAME=pdsystemuser
DB_PASSWORD=pdsystempass
```

**Catatan:**
- Port `3307` digunakan untuk Laravel Herd
- Jika menggunakan MySQL standard, ubah port ke `3306`
- Jika menggunakan MAMP, port biasanya `8889` atau `3306`

## Setup Database dan User

### Opsi 1: Setup Otomatis (Recommended)

Buat file `setup_mysql.sql`:

```sql
-- Buat database
CREATE DATABASE IF NOT EXISTS pdsystemdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Buat user dan set password
CREATE USER IF NOT EXISTS 'pdsystemuser'@'localhost' IDENTIFIED BY 'pdsystempass';
CREATE USER IF NOT EXISTS 'pdsystemuser'@'127.0.0.1' IDENTIFIED BY 'pdsystempass';
CREATE USER IF NOT EXISTS 'pdsystemuser'@'%' IDENTIFIED BY 'pdsystempass';

-- Grant privileges
GRANT ALL PRIVILEGES ON pdsystemdb.* TO 'pdsystemuser'@'localhost';
GRANT ALL PRIVILEGES ON pdsystemdb.* TO 'pdsystemuser'@'127.0.0.1';
GRANT ALL PRIVILEGES ON pdsystemdb.* TO 'pdsystemuser'@'%';

-- Flush privileges
FLUSH PRIVILEGES;
```

Jalankan dengan command:

```bash
# Untuk Laravel Herd (port 3307)
mysql -h 127.0.0.1 -P 3307 -u root < setup_mysql.sql

# Untuk MySQL standard (port 3306)
mysql -h 127.0.0.1 -P 3306 -u root -p < setup_mysql.sql
```

### Opsi 2: Manual via MySQL CLI

```bash
# Login ke MySQL
mysql -h 127.0.0.1 -P 3307 -u root

# Jalankan perintah SQL
mysql> CREATE DATABASE IF NOT EXISTS pdsystemdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
mysql> CREATE USER IF NOT EXISTS 'pdsystemuser'@'localhost' IDENTIFIED BY 'pdsystempass';
mysql> GRANT ALL PRIVILEGES ON pdsystemdb.* TO 'pdsystemuser'@'localhost';
mysql> FLUSH PRIVILEGES;
mysql> EXIT;
```

### Opsi 3: Via phpMyAdmin

1. Buka phpMyAdmin
2. Buat database baru: `pdsystemdb`
   - Collation: `utf8mb4_unicode_ci`
3. Buka tab "Privileges"
4. Klik "Add user account"
   - Username: `pdsystemuser`
   - Host: `localhost` (atau `%` untuk akses dari mana saja)
   - Password: `pdsystempass`
5. Di "Database for user account", pilih "Grant all privileges on database pdsystemdb"
6. Klik "Go"

## Verifikasi Setup

### 1. Test Koneksi MySQL

```bash
# Test koneksi dengan user baru
mysql -h 127.0.0.1 -P 3307 -u pdsystemuser -ppdsystempass -e "SHOW DATABASES;"
```

**Expected output:**
```
+--------------------+
| Database           |
+--------------------+
| information_schema |
| pdsystemdb         |
+--------------------+
```

### 2. Test Koneksi Laravel

```bash
php artisan db:show
```

**Expected output:**
```
MySQL ............................................................... 8.0.33  
Connection ........................................................... mysql  
Database ........................................................ pdsystemdb  
Host ............................................................. 127.0.0.1  
Port .................................................................. 3307  
Username ...................................................... pdsystemuser  
```

### 3. Verify Tables

```bash
php artisan db:table --database=mysql
```

## Migration dari SQLite ke MySQL

### Jika Database Baru (Belum Ada Data)

```bash
# 1. Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 2. Jalankan migration
php artisan migrate

# 3. Jalankan seeder
php artisan db:seed
```

### Jika Migrasi dari SQLite (Sudah Ada Data)

#### Metode 1: Export/Import Manual

**Export dari SQLite:**

```bash
# Install sqlite3 jika belum ada
brew install sqlite3

# Export data
sqlite3 database/database.sqlite .dump > sqlite_backup.sql
```

**Import ke MySQL:**

```bash
# Convert format SQLite ke MySQL (manual editing diperlukan)
# Ubah syntax yang tidak kompatibel:
# - INTEGER PRIMARY KEY AUTOINCREMENT → INT AUTO_INCREMENT PRIMARY KEY
# - DATETIME('now') → NOW()
# - Boolean values: 0/1 tetap sama

# Import ke MySQL
mysql -h 127.0.0.1 -P 3307 -u pdsystemuser -ppdsystempass pdsystemdb < sqlite_backup.sql
```

#### Metode 2: Menggunakan Laravel Backup Package

```bash
# Install backup package
composer require spatie/laravel-backup

# Backup dari SQLite
php artisan backup:run --only-db

# Restore ke MySQL (setelah switch config)
php artisan backup:restore
```

#### Metode 3: Fresh Migration + Seed (Data Hilang!)

**⚠️ Warning: Metode ini akan menghapus semua data existing!**

```bash
# 1. Backup data penting terlebih dahulu!

# 2. Drop semua tabel dan recreate
php artisan migrate:fresh --seed

# 3. Verify
php artisan db:show
```

## Troubleshooting

### Error: Access denied for user

**Problem:**
```
SQLSTATE[HY000] [1045] Access denied for user 'pdsystemuser'@'localhost' (using password: YES)
```

**Solution:**

1. **Check user exists:**
   ```bash
   mysql -h 127.0.0.1 -P 3307 -u root -e "SELECT User, Host FROM mysql.user WHERE User='pdsystemuser';"
   ```

2. **Recreate user if not exists:**
   ```sql
   DROP USER IF EXISTS 'pdsystemuser'@'localhost';
   CREATE USER 'pdsystemuser'@'localhost' IDENTIFIED BY 'pdsystempass';
   GRANT ALL PRIVILEGES ON pdsystemdb.* TO 'pdsystemuser'@'localhost';
   FLUSH PRIVILEGES;
   ```

3. **Check password:**
   ```bash
   # Test manual login
   mysql -h 127.0.0.1 -P 3307 -u pdsystemuser -ppdsystempass
   ```

4. **Check .env configuration:**
   ```bash
   cat .env | grep DB_
   ```

5. **Clear Laravel cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

### Error: Unknown database

**Problem:**
```
SQLSTATE[HY000] [1049] Unknown database 'pdsystemdb'
```

**Solution:**

```bash
# Create database
mysql -h 127.0.0.1 -P 3307 -u root -e "CREATE DATABASE pdsystemdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Error: Connection refused

**Problem:**
```
SQLSTATE[HY000] [2002] Connection refused
```

**Solution:**

1. **Check MySQL is running:**
   ```bash
   # For Laravel Herd
   herd status
   
   # For Homebrew MySQL
   brew services list | grep mysql
   
   # Check port
   lsof -i :3307
   ```

2. **Start MySQL if stopped:**
   ```bash
   # Laravel Herd
   herd start
   
   # Homebrew MySQL
   brew services start mysql
   ```

3. **Check port in .env:**
   ```env
   DB_PORT=3307  # For Herd
   DB_PORT=3306  # For standard MySQL
   ```

### Error: Can't connect to MySQL server on '127.0.0.1'

**Problem:**
```
SQLSTATE[HY000] [2002] Can't connect to MySQL server on '127.0.0.1'
```

**Solution:**

1. **Check MySQL socket:**
   ```bash
   mysql_config --socket
   ```

2. **Try using localhost instead:**
   ```env
   DB_HOST=localhost
   ```

3. **Check firewall:**
   ```bash
   # macOS
   sudo pfctl -sr | grep mysql
   ```

## Database Schema Information

Setelah setup berhasil, database akan memiliki:

- **53 Tables**
- **Total Size:** ~2.44 MB (dengan sample data)

### Tabel Utama:

| Schema/Table | Description |
|--------------|-------------|
| `users` | Data pengguna sistem |
| `nota_dinas` | Data nota dinas |
| `spt` | Surat Perintah Tugas |
| `sppd` | Surat Perjalanan Dinas |
| `receipts` | Kwitansi perjalanan |
| `trip_reports` | Laporan perjalanan |
| `units` | Unit organisasi |
| `positions` | Jabatan |
| `ranks` | Pangkat |

## Performance Tuning

### MySQL Configuration (Optional)

Edit file `my.cnf` atau `my.ini`:

```ini
[mysqld]
# Basic Settings
max_connections = 200
thread_cache_size = 8
table_open_cache = 2000

# Query Cache
query_cache_type = 1
query_cache_size = 32M
query_cache_limit = 2M

# Buffer Settings
innodb_buffer_pool_size = 256M
innodb_log_file_size = 64M
innodb_log_buffer_size = 16M

# Character Set
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci

# Timezone
default-time-zone = '+07:00'
```

### Laravel Optimization

```bash
# Cache routes
php artisan route:cache

# Cache config
php artisan config:cache

# Cache views
php artisan view:cache

# Optimize autoload
composer dump-autoload --optimize
```

## Backup Strategy

### Manual Backup

```bash
# Backup database
mysqldump -h 127.0.0.1 -P 3307 -u pdsystemuser -ppdsystempass pdsystemdb > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup dengan kompresi
mysqldump -h 127.0.0.1 -P 3307 -u pdsystemuser -ppdsystempass pdsystemdb | gzip > backup_$(date +%Y%m%d_%H%M%S).sql.gz
```

### Restore Backup

```bash
# Restore dari backup
mysql -h 127.0.0.1 -P 3307 -u pdsystemuser -ppdsystempass pdsystemdb < backup_20260106_143000.sql

# Restore dari backup terkompresi
gunzip < backup_20260106_143000.sql.gz | mysql -h 127.0.0.1 -P 3307 -u pdsystemuser -ppdsystempass pdsystemdb
```

### Automated Backup (Cron Job)

```bash
# Edit crontab
crontab -e

# Add backup job (daily at 2 AM)
0 2 * * * /usr/local/bin/mysqldump -h 127.0.0.1 -P 3307 -u pdsystemuser -ppdsystempass pdsystemdb | gzip > /path/to/backups/pdsystem_$(date +\%Y\%m\%d).sql.gz

# Keep only last 7 days
0 3 * * * find /path/to/backups -name "pdsystem_*.sql.gz" -mtime +7 -delete
```

## Security Recommendations

1. **Change Default Password:**
   ```sql
   ALTER USER 'pdsystemuser'@'localhost' IDENTIFIED BY 'new_secure_password';
   FLUSH PRIVILEGES;
   ```

2. **Restrict User to Localhost Only:**
   ```sql
   DROP USER IF EXISTS 'pdsystemuser'@'%';
   ```

3. **Use `.env` Protection:**
   ```bash
   # Ensure .env is in .gitignore
   echo ".env" >> .gitignore
   
   # Set proper permissions
   chmod 600 .env
   ```

4. **Enable SSL Connection (Optional):**
   ```env
   DB_SSL_CA=/path/to/ca-cert.pem
   DB_SSL_VERIFY_SERVER_CERT=true
   ```

## Monitoring

### Check Database Size

```sql
SELECT 
    table_schema AS 'Database',
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.tables
WHERE table_schema = 'pdsystemdb'
GROUP BY table_schema;
```

### Check Table Sizes

```sql
SELECT 
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)',
    table_rows AS 'Rows'
FROM information_schema.tables
WHERE table_schema = 'pdsystemdb'
ORDER BY (data_length + index_length) DESC
LIMIT 10;
```

### Check Slow Queries

```sql
-- Enable slow query log
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1;

-- View slow queries
SELECT * FROM mysql.slow_log LIMIT 10;
```

## Status Checklist

✅ **MySQL Server Running:** Port 3307  
✅ **Database Created:** pdsystemdb  
✅ **User Created:** pdsystemuser  
✅ **Privileges Granted:** ALL on pdsystemdb.*  
✅ **Laravel Connection:** Working  
✅ **Tables:** 53 tables created  
✅ **Total Size:** 2.44 MB  
✅ **Application:** Running successfully

---

**Last Updated:** January 6, 2026  
**Version:** 1.0.0  
**Database:** MySQL 8.0.33  
**Laravel:** 12.44.0  
**Author:** PD System Development Team

