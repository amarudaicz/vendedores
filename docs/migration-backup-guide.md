# Database Backup Guide - Cotización Migration

## Overview
This guide provides step-by-step instructions for backing up your database before executing the cotización (dollar exchange rate) migration.

## Why Backup?
The migration creates a new `cotizacion` table and will eventually drop the `products.ars_usd` column. While the migration is designed to be safe, having a backup ensures you can restore your database if anything unexpected occurs.

## Backup Instructions

### Option 1: Using mysqldump (Recommended)

```bash
# Navigate to project directory
cd C:\Users\PC AMD\Desktop\tyme-rosario

# Create backup directory if it doesn't exist
mkdir -p backups

# Execute backup using database credentials from config/DatabaseConfiguration.php
mysqldump -h 193.203.175.226 -P 3306 -u u918235402_tyme -p u918235402_tyme > backups/cotizacion-backup-$(date +%Y%m%d-%H%M%S).sql
```

**When prompted, enter the password:** `1Ao&X1|[Q]`

### Option 2: Backup specific tables only

```bash
# Backup only products table (source of data)
mysqldump -h 193.203.175.226 -P 3306 -u u918235402_tyme -p u918235402_tyme products > backups/products-backup-$(date +%Y%m%d-%H%M%S).sql

# Backup all tables (safer, recommended)
mysqldump -h 193.203.175.226 -P 3306 -u u918235402_tyme -p u918235402_tyme > backups/full-backup-$(date +%Y%m%d-%H%M%S).sql
```

### Option 3: Using phpMyAdmin or other GUI tools

1. Open phpMyAdmin or your MySQL administration tool
2. Select the `u918235402_tyme` database
3. Click "Export" tab
4. Choose "Quick" export method
5. Format: SQL
6. Click "Go" to download the backup file

## Verify Backup

Always verify your backup before proceeding:

```bash
# Check backup file size (should be > 0)
ls -lh backups/*.sql

# Verify backup is valid SQL
head -n 20 backups/cotizacion-backup-*.sql
```

The backup should start with something like:
```
-- MySQL dump 10.13  Distrib 5.7.xx, for Linux (x86_64)
--
-- Host: 193.203.175.226    Database: u918235402_tyme
-- ------------------------------------------------------
```

## Restore Instructions (If Needed)

### Restore from mysqldump backup

```bash
# WARNING: This will overwrite existing data! Use with caution.
mysql -h 193.203.175.226 -P 3306 -u u918235402_tyme -p u918235402_tyme < backups/cotizacion-backup-YYYYMMDD-HHMMSS.sql
```

### Restore from GUI tool (phpMyAdmin, etc.)

1. Open phpMyAdmin or your MySQL administration tool
2. Select the `u918235402_tyme` database
3. Click "Import" tab
4. Choose your backup SQL file
5. Click "Go" to restore

## Migration Steps After Backup

Once you have verified your backup:

1. ✅ **Backup completed** (you are here)
2. ⏭️ Execute migration: `mysql -h 193.203.175.226 -P 3306 -u u918235402_tyme -p u918235402_tyme < migration_cotizacion.sql`
3. ⏭️ Validate migration: `SELECT * FROM cotizacion WHERE id = 1;`
4. ⏭️ If validation passes, review cleanup script: `cleanup_cotizacion.sql`
5. ⏭️ Execute cleanup when ready: `mysql -h 193.203.175.226 -P 3306 -u u918235402_tyme -p u918235402_tyme < cleanup_cotizacion.sql`

## Important Notes

- **Never skip the backup step** - migrations are irreversible without a backup
- **Test the restore process** in a staging environment before production
- **Keep multiple backups** - maintain at least 2-3 recent backups
- **Document the restore procedure** for your team
- **Schedule backups** regularly if this will be an ongoing maintenance task

## Troubleshooting

### mysqldump: command not found
Install MySQL client tools or use a GUI alternative (phpMyAdmin, MySQL Workbench).

### Access denied
Double-check your database credentials in `config/DatabaseConfiguration.php`.

### Backup file is empty
Check for permission issues and ensure the backups directory is writable.

## Contact

If you encounter issues, check:
- Database credentials in `config/DatabaseConfiguration.php`
- MySQL server accessibility
- Database permissions
