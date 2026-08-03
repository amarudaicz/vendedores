-- Migration: Create cotizacion table for dollar exchange rate management
-- Date: 2025-03-25
-- Description: Creates a single-row table to store the dollar exchange rate
-- DO NOT EXECUTE - This is for manual execution after backup

-- Create the cotizacion table
CREATE TABLE cotizacion (
  id INT PRIMARY KEY AUTO_INCREMENT,
  valor DECIMAL(10,2) NOT NULL CHECK (valor > 0),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert initial value from products.ars_usd (first non-deleted product with valid rate)
INSERT INTO cotizacion (id, valor)
SELECT 1, ars_usd
FROM products
WHERE deleted = 0
  AND ars_usd IS NOT NULL
  AND ars_usd > 0
LIMIT 1;

-- NOTE: The following DROP COLUMN should be executed AFTER validating the migration
-- ALTER TABLE products DROP COLUMN ars_usd;

-- Validation queries (run after migration to verify):
-- SELECT * FROM cotizacion WHERE id = 1;
-- The result should show the copied valor from products.ars_usd
