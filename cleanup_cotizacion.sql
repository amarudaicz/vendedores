-- Cleanup Script: Drop products.ars_usd column
-- Date: 2025-03-25
-- Description: Removes the deprecated ars_usd column from products table after successful migration
-- WARNING: This is a destructive operation - DO NOT execute without backup!

-- Safety check: Ensure cotizacion table has data before proceeding
-- If this query returns no results, STOP and investigate!
SELECT 'Checking cotizacion table...' AS status;
SELECT * FROM cotizacion WHERE id = 1;

-- Only proceed if the above query returns a valid record
-- If you see no results above, ABORT the cleanup!

-- Drop the deprecated column
-- NOTE: This operation is irreversible once executed
SELECT 'Dropping products.ars_usd column...' AS status;
ALTER TABLE products DROP COLUMN ars_usd;

-- Verify the column was dropped
SELECT 'Verifying column was dropped...' AS status;
SHOW COLUMNS FROM products LIKE 'ars_usd';

-- Expected result: Empty set (no rows returned)
-- If you see a row above, the column was NOT dropped!

-- Final verification: Ensure cotizacion still works
SELECT 'Final verification - cotizacion table:' AS status;
SELECT * FROM cotizacion WHERE id = 1;

-- Expected result: Should show the cotizacion record with valor
-- If this query fails, the migration is broken - restore from backup!

-- Cleanup complete message
SELECT '✓ Cleanup completed successfully!' AS status;
SELECT 'products.ars_usd column has been dropped.' AS message;
SELECT 'All exchange rate operations now use cotizacion table.' AS note;
