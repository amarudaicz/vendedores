<?php

/**
 * Debug Script - Check products table
 *
 * This script checks the products table to see if there are valid ars_usd values
 */

// Load Composer autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    require_once __DIR__ . '/models/Connection.php';
}

use models\Connection;

echo "Checking products table for ars_usd values...\n\n";

try {
    $conn = Connection::getConn();

    // Count all products
    $total = $conn->query("SELECT COUNT(*) as count FROM products");
    $totalRow = $total->fetch_assoc();
    echo "Total products: {$totalRow['count']}\n";

    // Count non-deleted products
    $active = $conn->query("SELECT COUNT(*) as count FROM products WHERE deleted = 0");
    $activeRow = $active->fetch_assoc();
    echo "Active products (deleted=0): {$activeRow['count']}\n";

    // Count products with ars_usd value
    $withArs = $conn->query("SELECT COUNT(*) as count FROM products WHERE deleted = 0 AND ars_usd IS NOT NULL");
    $withArsRow = $withArs->fetch_assoc();
    echo "Active products with ars_usd value: {$withArsRow['count']}\n";

    // Count products with valid ars_usd (> 0)
    $valid = $conn->query("SELECT COUNT(*) as count FROM products WHERE deleted = 0 AND ars_usd IS NOT NULL AND ars_usd > 0");
    $validRow = $valid->fetch_assoc();
    echo "Active products with valid ars_usd (> 0): {$validRow['count']}\n\n";

    // Show sample values
    if ($validRow['count'] > 0) {
        echo "Sample ars_usd values:\n";
        $samples = $conn->query("SELECT id, name, ars_usd FROM products WHERE deleted = 0 AND ars_usd IS NOT NULL AND ars_usd > 0 LIMIT 5");
        while ($row = $samples->fetch_assoc()) {
            echo "  - ID {$row['id']}: {$row['name']} = {$row['ars_usd']}\n";
        }

        echo "\nFirst valid value will be used for cotizacion.\n";
    } else {
        echo "⚠ No valid ars_usd values found in products table!\n";
        echo "→ Will need to seed cotizacion with a default value manually\n";
    }

} catch (Exception $e) {
    echo "\n✗ Error:\n";
    echo "  " . $e->getMessage() . "\n";
    exit(1);
}
