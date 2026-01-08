-- Setup MySQL Database untuk PD System 2026

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

-- Show databases
SHOW DATABASES;

-- Show users
SELECT User, Host FROM mysql.user WHERE User = 'pdsystemuser';

