<?php

/**
 * Migration Verification Script - Cotización
 *
 * This script verifies if the cotizacion table exists and has valid data
 *
 * Usage: php verify-migration.php
 */

// Load Composer autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    require_once __DIR__ . '/models/Connection.php';
}

use models\Connection;

echo "Verifying cotización migration status...\n\n";

try {
    $conn = Connection::getConn();

    // Check if cotizacion table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'cotizacion'");
    if (!$tableCheck || $tableCheck->num_rows === 0) {
        echo "✗ cotizacion table does not exist\n";
        echo "→ You need to run the migration first\n";
        exit(1);
    }

    echo "✓ cotizacion table exists\n";

    // Check table structure
    $structure = $conn->query("DESCRIBE cotizacion");
    echo "\nTable structure:\n";
    while ($row = $structure->fetch_assoc()) {
        echo "  - {$row['Field']}: {$row['Type']} " . ($row['Null'] === 'NO' ? 'NOT NULL' : 'NULL') . "\n";
    }

    // Check for existing data
    $result = $conn->query("SELECT * FROM cotizacion WHERE id = 1");
    if (!$result || $result->num_rows === 0) {
        echo "\n⚠ No cotizacion record found with id=1\n";
        echo "→ The table exists but is not properly seeded\n";
        exit(1);
    }

    echo "\n✓ cotizacion record found (id=1)\n";

    $row = $result->fetch_assoc();
    echo "\nCurrent data:\n";
    echo "  id: {$row['id']}\n";
    echo "  valor: {$row['valor']}\n";
    echo "  created_at: {$row['created_at']}\n";
    echo "  updated_at: {$row['updated_at']}\n";

    // Check if products.ars_usd still exists
    $arsCheck = $conn->query("SHOW COLUMNS FROM products LIKE 'ars_usd'");
    if ($arsCheck && $arsCheck->num_rows > 0) {
        echo "\n⚠ products.ars_usd column still exists\n";
        echo "→ Cleanup has not been executed yet\n";

        // Get the current value from products.ars_usd
        $productsVal = $conn->query("SELECT ars_usd FROM products WHERE deleted = 0 AND ars_usd IS NOT NULL LIMIT 1");
        if ($productsVal && $prodRow = $productsVal->fetch_assoc()) {
            echo "  Current products.ars_usd: {$prodRow['ars_usd']}\n";
        }
    } else {
        echo "\n✓ products.ars_usd column has been dropped\n";
        echo "→ Cleanup phase has been completed\n";
    }

    // Validation check
    echo "\n=== VALIDATION SUMMARY ===\n";
    echo "✓ Migration appears to be successfully executed\n";
    echo "✓ cotizacion table exists and has data\n";

    if ($arsCheck && $arsCheck->num_rows > 0) {
        echo "⏭️  Next step: Execute cleanup (DROP products.ars_usd)\n";
    } else {
        echo "✓ All phases complete\n";
    }

} catch (Exception $e) {
    echo "\n✗ Verification failed:\n";
    echo "  " . $e->getMessage() . "\n";
    exit(1);
}
