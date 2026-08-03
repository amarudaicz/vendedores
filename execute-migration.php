<?php

/**
 * Migration Executor Script - Cotización
 *
 * This script executes the migration to create the cotizacion table
 * and populate it with initial data from products.ars_usd
 *
 * Usage: php execute-migration.php
 */

// Load Composer autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    require_once __DIR__ . '/models/Connection.php';
}

use models\Connection;

echo "Starting cotización migration...\n\n";

try {
    // Get database connection
    $conn = Connection::getConn();
    echo "✓ Connected to database\n";

    // Read migration SQL file
    $migrationFile = __DIR__ . '/migration_cotizacion.sql';
    if (!file_exists($migrationFile)) {
        throw new Exception("Migration file not found: $migrationFile");
    }

    $sql = file_get_contents($migrationFile);
    echo "✓ Read migration file: " . basename($migrationFile) . "\n";

    // Split SQL into individual statements
    // Remove comments first
    $sql = preg_replace('/--.*$/m', '', $sql);
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

    // Split by semicolon, ignoring those within strings
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    echo "✓ Found " . count($statements) . " SQL statements to execute\n\n";

    // Execute each statement
    $executed = 0;
    foreach ($statements as $index => $statement) {
        if (empty($statement)) {
            continue;
        }

        echo "Executing statement " . ($index + 1) . "...\n";

        // Skip the ALTER TABLE statement (will be executed separately in cleanup)
        if (stripos($statement, 'ALTER TABLE') !== false) {
            echo "  → Skipping ALTER TABLE (will be executed in cleanup phase)\n";
            continue;
        }

        if ($conn->query($statement)) {
            echo "  ✓ Success\n";
            $executed++;
        } else {
            throw new Exception("SQL Error: " . $conn->error . "\nStatement: " . substr($statement, 0, 100) . "...");
        }
    }

    echo "\n✓ Migration completed successfully\n";
    echo "  Executed $executed statements\n";

    // Verify the cotizacion table was created and has data
    $result = $conn->query("SELECT * FROM cotizacion WHERE id = 1");
    if ($result && $row = $result->fetch_assoc()) {
        echo "\n✓ Verification passed:\n";
        echo "  cotizacion.id = " . $row['id'] . "\n";
        echo "  cotizacion.valor = " . $row['valor'] . "\n";
        echo "  cotizacion.created_at = " . $row['created_at'] . "\n";
    } else {
        echo "\n⚠ Warning: Could not verify cotizacion table data\n";
    }

} catch (Exception $e) {
    echo "\n✗ Migration failed:\n";
    echo "  " . $e->getMessage() . "\n";
    echo "\nPlease check your database connection and permissions.\n";
    echo "If the migration partially completed, you may need to manually restore from backup.\n";
    exit(1);
}

echo "\nNext steps:\n";
echo "1. Verify the data: SELECT * FROM cotizacion WHERE id = 1;\n";
echo "2. Test the application to ensure everything works\n";
echo "3. Review cleanup_cotizacion.sql before executing it\n";
echo "4. Execute cleanup when ready: php execute-cleanup.php\n\n";
