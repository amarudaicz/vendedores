# Cleanup Guide - Cotización Migration

## Overview
This guide provides step-by-step instructions for executing the cleanup phase of the cotización (dollar exchange rate) migration.

## What Does Cleanup Do?
The cleanup phase drops the deprecated `products.ars_usd` column from the database, as all exchange rate operations now use the new `cotizacion` table.

## Prerequisites

Before executing cleanup, ensure you have:

1. ✅ **Database Backup** - Created using the backup guide
2. ✅ **Migration Executed** - `cotizacion` table exists and has data
3. ✅ **Migration Validated** - `verify-migration.php` passed all checks
4. ✅ **Application Tested** - The new cotización system works correctly

## Validation Checklist

Run these checks before proceeding:

```bash
# Verify migration is complete
php verify-migration.php
```

Expected output:
```
✓ cotizacion table exists
✓ cotizacion record found (id=1)
⚠ products.ars_usd column still exists
→ Cleanup has not been executed yet
```

If you see any errors, **DO NOT PROCEED** with cleanup.

## Safety Precautions

⚠️ **WARNING: This is a destructive operation!**

- The `products.ars_usd` column will be permanently removed
- This operation cannot be undone without restoring from backup
- Ensure all safety checks pass before executing
- Test in staging environment first if available

## Cleanup Instructions

### Option 1: Interactive Execution (Recommended)

```bash
# Navigate to project directory
cd C:\Users\PC AMD\Desktop\tyme-rosario

# Execute cleanup with interactive prompts
php execute-cleanup.php
```

**You will be prompted to:**
1. Confirm you have a backup
2. Type 'yes' to confirm you want to proceed
3. Press Enter to execute the final DROP COLUMN operation

### Option 2: Automated Execution (For Staging/Testing)

```bash
# Execute cleanup without interactive prompts
php execute-cleanup.php < /dev/null
```

**Note:** This is **NOT recommended** for production. Always use interactive mode for production databases.

### Option 3: Manual SQL Execution

If you prefer to execute the SQL directly:

```bash
# Connect to MySQL
mysql -h 193.203.175.226 -P 3306 -u u918235402_tyme -p u918235402_tyme
```

Then run:
```sql
-- Safety check: Ensure cotizacion has data
SELECT * FROM cotizacion WHERE id = 1;

-- If the above returns a valid record, proceed:
ALTER TABLE products DROP COLUMN ars_usd;

-- Verify the column was dropped
SHOW COLUMNS FROM products LIKE 'ars_usd';
-- Expected: Empty set (no rows)

-- Final verification
SELECT * FROM cotizacion WHERE id = 1;
```

## Post-Cleanup Verification

After cleanup, run these verification steps:

### 1. Verify Database State

```bash
php verify-migration.php
```

Expected output:
```
✓ cotizacion table exists
✓ cotizacion record found (id=1)
✓ products.ars_usd column has been dropped
→ Cleanup phase has been completed
✓ All phases complete
```

### 2. Test Application Functionality

- Navigate to the admin panel
- Go to the "Cotización" module
- Verify you can view the current exchange rate
- Try updating the exchange rate
- Save the changes and verify they persist
- Test the API endpoints:
  - `GET /api/v1/cotizacion/dolar` - Should return current rate
  - `PUT /api/v1/cotizacion/dolar` - Should update the rate (admin only)

### 3. Check for Errors

Review application logs for any errors related to:
- Database queries accessing `products.ars_usd`
- Exchange rate calculations
- API responses

## Rollback Procedure (If Needed)

If cleanup causes issues, you can rollback:

```bash
# Restore from backup
mysql -h 193.203.175.226 -P 3306 -u u918235402_tyme -p u918235402_tyme < backups/cotizacion-backup-YYYYMMDD-HHMMSS.sql
```

**Note:** This will revert ALL database changes made since the backup.

## Troubleshooting

### Cleanup script won't start

**Error:** "cotizacion table has no data!"

**Solution:**
- Run the migration: `php execute-migration-fix.php`
- Validate migration: `php verify-migration.php`

### Cleanup fails to drop column

**Error:** "Column still exists after DROP operation!"

**Solution:**
- Check database permissions
- Verify there are no locks on the products table
- Check for foreign key constraints referencing ars_usd
- Try manual SQL execution

### Application shows errors after cleanup

**Symptoms:** 500 errors, database errors, or missing data

**Possible causes:**
1. Code still referencing `products.ars_usd`
2. Cached data or configurations
3. Active database connections using old schema

**Solutions:**
1. Search codebase for `ars_usd` references and update them
2. Clear application cache
3. Restart web server
4. Review error logs for specific issues

## What to Do If Cleanup Fails

1. **DO NOT PANIC** - The `cotizacion` table should still be intact
2. **Check the error** - Note the exact error message
3. **Investigate** - Use the troubleshooting steps above
4. **Consider rollback** - If issues persist, restore from backup
5. **Contact support** - If needed, seek assistance

## Completion Checklist

After successful cleanup, verify:

- [ ] `products.ars_usd` column no longer exists
- [ ] `cotizacion` table exists and has correct data
- [ ] Admin cotización module works
- [ ] API endpoints function correctly
- [ ] No errors in application logs
- [ ] All dollar exchange rate operations work
- [ ] Exchange rate updates persist correctly

## Migration Complete! 🎉

Once cleanup is verified and all tests pass:

1. ✅ **Archive backup files** - Move to secure storage
2. ✅ **Document the migration** - Update internal documentation
3. ✅ **Monitor for issues** - Watch for any problems in production
4. ✅ **Remove cleanup scripts** - Optional, can keep for reference

## Support

If you encounter issues during cleanup:

1. Check this guide's troubleshooting section
2. Review error logs and messages
3. Verify all safety checks pass
4. Consider rollback if needed
5. Contact your database administrator or developer team

## Important Notes

- **Never skip the backup step** - It's your safety net
- **Test in staging first** - Whenever possible
- **Document any issues** - For future reference
- **Keep backups** - For at least 30 days after migration
- **Monitor closely** - Watch for issues in the days following cleanup

---

**Remember:** The cleanup is the final step of the migration. Take your time, verify everything, and only proceed when confident!
