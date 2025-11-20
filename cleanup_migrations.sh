#!/bin/bash

echo "Cleaning up old migrations..."

# Backup migration directory
cp -r database/migrations database/migrations_backup_$(date +%Y%m%d_%H%M%S)

# Remove all old migration files except the clean ones
cd database/migrations

# Keep only the clean migration files
rm -f 0001_01_01_000000_create_users_table.php
rm -f 0001_01_01_000001_create_cache_table.php
rm -f 0001_01_01_000002_create_jobs_table.php
rm -f 2025_08_09_*.php
rm -f 2025_08_10_*.php
rm -f 2025_08_11_*.php
rm -f 2025_08_13_*.php
rm -f 2025_08_16_*.php
rm -f 2025_08_18_*.php
rm -f 2025_08_21_*.php
rm -f 2025_08_22_*.php
rm -f 2025_08_23_*.php
rm -f 2025_08_24_*.php
rm -f 2025_08_25_*.php
rm -f 2025_08_27_*.php
rm -f 2025_08_28_000001_*.php
rm -f 2025_08_28_000002_*.php
rm -f 2025_08_28_000003_*.php
rm -f 2025_08_28_000004_*.php
rm -f 2025_08_28_000005_*.php
rm -f 2025_08_28_000006_*.php
rm -f 2025_08_28_000007_*.php

# Keep only the clean migration files
echo "Keeping only clean migration files:"
ls -la 2025_08_28_000008_*.php
ls -la 2025_08_28_000009_*.php

echo "Migration cleanup completed!"
echo "Now you can run: php artisan migrate:fresh"
echo "This will create a clean database structure without conflicts."
