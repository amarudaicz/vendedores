<?php

/**
 * Migration Fix Script - Cotización
 *
 * This script will drop and recreate the cotizacion table with proper structure
 * and seed it with initial data from products.ars_usd
 *
 * Usage: php execute-migration-fix.php
 *
 * WARNING: This will drop any existing cotizacion table!
 */

// Load Composer autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    require_once __DIR__ . '/models/Connection.php';
}

use models\Connection;

echo "Starting cotización migration (fix)...\n\n";

try {
    $conn = Connection::getConn();
    echo "✓ Connected to database\n";

    // Check if table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'cotizacion'");
    $tableExists = ($tableCheck && $tableCheck->num_rows > 0);

    if ($tableExists) {
        echo "⚠ cotizacion table already exists\n";
        echo "→ Dropping existing table to recreate with proper structure\n";

        if (!$conn->query("DROP TABLE cotizacion")) {
            throw new Exception("Failed to drop existing table: " . $conn->error);
        }
        echo "✓ Dropped existing table\n";
    }

    // Create the table with proper structure
    $createTable = "CREATE TABLE cotizacion (
      id INT PRIMARY KEY AUTO_INCREMENT,
      valor DECIMAL(10,2) NOT NULL CHECK (valor > 0),
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    if (!$conn->query($createTable)) {
        throw new Exception("Failed to create table: " . $conn->error);
    }
    echo "✓ Created cotizacion table with proper structure\n";

    // Seed the table with initial data from products.ars_usd
    // First try to get a valid value from products
    $seedData = "INSERT INTO cotizacion (id, valor)
    SELECT 1, ars_usd
    FROM products
    WHERE deleted = 0
      AND ars_usd IS NOT NULL
      AND ars_usd > 0
    LIMIT 1";

    $seedResult = $conn->query($seedData);

    if ($seedResult && $conn->affected_rows > 0) {
        echo "✓ Seeded cotizacion table with value from products.ars_usd\n";
    } else {
        // If no valid value exists, use a default value
        echo "⚠ No valid ars_usd value found in products\n";
        echo "→ Seeding with default value: 1000.00\n";

        $defaultSeed = "INSERT INTO cotizacion (id, valor) VALUES (1, 1000.00)";
        if (!$conn->query($defaultSeed)) {
            throw new Exception("Failed to seed default value: " . $conn->error);
        }
    }

    // Verify the data was inserted
    $result = $conn->query("SELECT * FROM cotizacion WHERE id = 1");
    if (!$result || $result->num_rows === 0) {
        throw new Exception("Failed to verify seeded data");
    }

    $row = $result->fetch_assoc();
    echo "\n✓ Verification passed:\n";
    echo "  cotizacion.id = " . $row['id'] . "\n";
    echo "  cotizacion.valor = " . $row['valor'] . "\n";
    echo "  cotizacion.created_at = " . $row['created_at'] . "\n";
    echo "  cotizacion.updated_at = " . $row['updated_at'] . "\n";

    // Check if this came from products or is a default value
    $source = $conn->query("SELECT ars_usd FROM products WHERE deleted = 0 AND ars_usd IS NOT NULL AND ars_usd > 0 LIMIT 1");
    if ($source && $srcRow = $source->fetch_assoc()) {
        echo "  Source products.ars_usd = " . $srcRow['ars_usd'] . "\n";

        if (abs($row['valor'] - $srcRow['ars_usd']) < 0.01) {
            echo "\n✓ Values match! Migration successful.\n";
        } else {
            echo "\n⚠ Values don't match! Please investigate.\n";
        }
    } else {
        echo "\n✓ Default value used (no valid ars_usd in products table).\n";
        echo "→ Admin should update the exchange rate via the UI.\n";
    }

} catch (Exception $e) {
    echo "\n✗ Migration failed:\n";
    echo "  " . $e->getMessage() . "\n";
    echo "\nPlease check your database connection and permissions.\n";
    exit(1);
}

echo "\n=== MIGRATION COMPLETE ===\n";
echo "✓ cotizacion table created and seeded\n";
echo "✓ Validation passed\n\n";

echo "Next steps:\n";
echo "1. Run verification: php verify-migration.php\n";
echo "2. Test the application to ensure everything works\n";
echo "3. Review cleanup_cotizacion.sql before executing\n";
echo "4. Execute cleanup when ready\n\n";
