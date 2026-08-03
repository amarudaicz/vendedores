<?php

/**
 * Cleanup Execution Script - Cotización
 *
 * This script executes the cleanup to drop products.ars_usd column
 * after the migration has been validated.
 *
 * Usage: php execute-cleanup.php
 *
 * WARNING: This is a destructive operation!
 * Ensure you have a backup before running this script.
 */

// Load Composer autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    require_once __DIR__ . '/models/Connection.php';
}

use models\Connection;

echo "=== COTIZACIÓN CLEANUP EXECUTION ===\n";
echo "This will DROP the products.ars_usd column.\n\n";

// Safety check: Ask for confirmation
echo "⚠️  WARNING: This is a destructive operation!\n";
echo "⚠️  products.ars_usd column will be permanently removed!\n";
echo "\n";
echo "Before proceeding, ensure you have:\n";
echo "  1. ✓ Created a database backup (see docs/migration-backup-guide.md)\n";
echo "  2. ✓ Validated the migration (run verify-migration.php)\n";
echo "  3. ✓ Tested the application with the new cotizacion table\n";
echo "\n";

// Check if running in interactive mode
if (php_sapi_name() === 'cli') {
    echo "Type 'yes' to continue, or anything else to abort: ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);

    if (trim($line) !== 'yes') {
        echo "\n✗ Cleanup aborted by user.\n";
        exit(1);
    }
    echo "\n";
}

try {
    $conn = Connection::getConn();
    echo "✓ Connected to database\n\n";

    // Safety check 1: Verify cotizacion table exists and has data
    echo "=== SAFETY CHECK 1: Verifying cotizacion table ===\n";
    $result = $conn->query("SELECT * FROM cotizacion WHERE id = 1");
    if (!$result || $result->num_rows === 0) {
        echo "✗ SAFETY CHECK FAILED: cotizacion table has no data!\n";
        echo "→ Cannot proceed with cleanup.\n";
        echo "→ Please run the migration first.\n";
        exit(1);
    }

    $row = $result->fetch_assoc();
    echo "✓ cotizacion table exists with data:\n";
    echo "  - id: {$row['id']}\n";
    echo "  - valor: {$row['valor']}\n";
    echo "  - created_at: {$row['created_at']}\n";
    echo "\n";

    // Safety check 2: Verify products.ars_usd column exists
    echo "=== SAFETY CHECK 2: Verifying products.ars_usd column ===\n";
    $columnCheck = $conn->query("SHOW COLUMNS FROM products LIKE 'ars_usd'");
    if (!$columnCheck || $columnCheck->num_rows === 0) {
        echo "✗ products.ars_usd column does not exist!\n";
        echo "→ Cleanup has already been executed or column was dropped manually.\n";
        echo "→ No action needed.\n";
        exit(0);
    }
    echo "✓ products.ars_usd column exists (ready to be dropped)\n\n";

    // Final confirmation
    echo "=== FINAL CONFIRMATION ===\n";
    echo "About to execute:\n";
    echo "  ALTER TABLE products DROP COLUMN ars_usd;\n";
    echo "\n";

    // If not interactive, add a small delay to allow reading
    if (php_sapi_name() === 'cli') {
        echo "Press Enter to execute, or Ctrl+C to abort... ";
        $handle = fopen("php://stdin", "r");
        fgets($handle);
        fclose($handle);
    }

    echo "\n=== EXECUTING CLEANUP ===\n";

    // Execute DROP COLUMN
    echo "Dropping products.ars_usd column...\n";
    if (!$conn->query("ALTER TABLE products DROP COLUMN ars_usd")) {
        throw new Exception("Failed to drop column: " . $conn->error);
    }
    echo "✓ products.ars_usd column dropped successfully\n\n";

    // Verify the column was dropped
    echo "=== VERIFICATION ===\n";
    $verify = $conn->query("SHOW COLUMNS FROM products LIKE 'ars_usd'");
    if ($verify && $verify->num_rows > 0) {
        throw new Exception("Column still exists after DROP operation!");
    }
    echo "✓ Verified: products.ars_usd column no longer exists\n\n";

    // Final verification: Ensure cotizacion still works
    echo "=== FINAL VERIFICATION ===\n";
    $finalCheck = $conn->query("SELECT * FROM cotizacion WHERE id = 1");
    if (!$finalCheck || $finalCheck->num_rows === 0) {
        throw new Exception("cotizacion table lost data during cleanup!");
    }
    $finalRow = $finalCheck->fetch_assoc();
    echo "✓ cotizacion table still has data:\n";
    echo "  - id: {$finalRow['id']}\n";
    echo "  - valor: {$finalRow['valor']}\n";
    echo "  - created_at: {$finalRow['created_at']}\n\n";

    echo "=== CLEANUP COMPLETED SUCCESSFULLY ===\n";
    echo "✓ products.ars_usd column has been dropped\n";
    echo "✓ cotizacion table is fully functional\n";
    echo "✓ Migration and cleanup are complete!\n\n";

    echo "Next steps:\n";
    echo "1. Test the application thoroughly\n";
    echo "2. Verify all dollar exchange rate operations work correctly\n";
    echo "3. Update admin exchange rate via the UI if needed\n";
    echo "4. Remove backup files only after confirming everything works\n\n";

} catch (Exception $e) {
    echo "\n✗ Cleanup failed:\n";
    echo "  " . $e->getMessage() . "\n";
    echo "\nIMPORTANT: The cleanup may have partially completed.\n";
    echo "Please check the database state and restore from backup if necessary.\n";
    exit(1);
}
