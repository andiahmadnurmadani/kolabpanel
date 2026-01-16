-- Add hasDatabase column to sites table
-- This column tracks whether a site has an associated MySQL database

ALTER TABLE sites ADD COLUMN hasDatabase BOOLEAN DEFAULT FALSE;

-- Update existing sites that have databases (if any)
-- This is safe to run even if no sites exist
UPDATE sites SET hasDatabase = TRUE WHERE subdomain IN (
    SELECT REPLACE(REPLACE(SCHEMA_NAME, 'db_', ''), '_', '') 
    FROM information_schema.SCHEMATA 
    WHERE SCHEMA_NAME LIKE 'db_%'
);
