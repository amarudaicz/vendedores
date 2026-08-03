-- Migration: Add total_ars and total_usd columns to orders table
-- Date: 2025-03-25
-- Description: Calculate and store order totals in both ARS and USD currencies

-- Add new columns to orders table
ALTER TABLE orders
ADD COLUMN total_ars DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'Total en pesos argentinos' AFTER guest_id,
ADD COLUMN total_usd DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'Total en dólares estadounidenses' AFTER total_ars;

-- Calculate and populate totals for existing orders
UPDATE orders o
SET o.total_ars = (
    SELECT SUM(oi.price * oi.quantity)
    FROM order_items oi
    WHERE oi.order_id = o.id
),
o.total_usd = (
    SELECT (SUM(oi.price * oi.quantity) / COALESCE(c.valor, 1.00))
    FROM order_items oi
    CROSS JOIN cotizacion c
    WHERE oi.order_id = o.id AND c.id = 1
)
WHERE o.id IS NOT NULL;

-- Validation queries (run after migration)
-- Check if columns were added:
-- DESCRIBE orders;

-- Check totals for existing orders:
-- SELECT id, total_ars, total_usd FROM orders;

-- Check a specific order's calculation:
-- SELECT
--     o.id,
--     SUM(oi.price * oi.quantity) AS calculated_total_ars,
--     o.total_ars AS stored_total_ars,
--     (SUM(oi.price * oi.quantity) / c.valor) AS calculated_total_usd,
--     o.total_usd AS stored_total_usd,
--     c.valor AS cotizacion_actual
-- FROM orders o
-- LEFT JOIN order_items oi ON o.id = oi.order_id
-- CROSS JOIN cotizacion c
-- WHERE o.id = 1
-- GROUP BY o.id, o.total_ars, o.total_usd, c.valor;
