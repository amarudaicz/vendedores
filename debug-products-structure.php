<?php

/**
 * Debug Script - Check products table structure
 */

// Load Composer autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    require_once __DIR__ . '/models/Connection.php';
}

use models\Connection;

echo "Checking products table structure...\n\n";

try {
    $conn = Connection::getConn();

    // Show table structure
    echo "Products table structure:\n";
    $structure = $conn->query("DESCRIBE products");
    while ($row = $structure->fetch_assoc()) {
        echo "  - {$row['Field']}: {$row['Type']} " . ($row['Null'] === 'NO' ? 'NOT NULL' : 'NULL') . "\n";
    }

    // Check sample values
    echo "\nSample products (first 5):\n";
    $samples = $conn->query("SELECT * FROM products WHERE deleted = 0 LIMIT 5");
    $fields = $samples->fetch_fields();
    while ($row = $samples->fetch_assoc()) {
        echo "\nProduct:\n";
        foreach ($row as $key => $value) {
            if ($value !== null && strlen($value) > 50) {
                $value = substr($value, 0, 50) . '...';
            }
            echo "  {$key}: " . ($value === null ? 'NULL' : $value) . "\n";
        }
    }

} catch (Exception $e) {
    echo "\n✗ Error:\n";
    echo "  " . $e->getMessage() . "\n";
    exit(1);
}
