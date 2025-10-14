-- ================================================================
-- Session Payload Column Fix for Production
-- ================================================================
-- Purpose: Fix "Data too long for column 'payload'" error
-- Issue: Session payload exceeds TEXT column limit (64KB)
-- Solution: Change column type from TEXT to LONGTEXT (4GB limit)
-- ================================================================

-- Step 1: Check current column type
SHOW COLUMNS FROM sessions LIKE 'payload';

-- Step 2: Backup sessions table (optional but recommended)
-- CREATE TABLE sessions_backup_20250114 AS SELECT * FROM sessions;

-- Step 3: Alter column type to LONGTEXT
ALTER TABLE sessions 
MODIFY COLUMN payload LONGTEXT NOT NULL;

-- Step 4: Verify the change
SHOW COLUMNS FROM sessions LIKE 'payload';
-- Expected result: Type = longtext

-- Step 5: Record migration in migrations table
-- Get current max batch number first
SELECT MAX(batch) FROM migrations;
-- Then insert (replace <MAX_BATCH + 1> with actual number)
INSERT INTO migrations (migration, batch) 
VALUES ('2025_01_14_000001_fix_sessions_payload_column', <MAX_BATCH + 1>);

-- Step 6: Optional - Clear old sessions
-- TRUNCATE TABLE sessions;

-- ================================================================
-- Verification Queries
-- ================================================================

-- Check sessions table structure
DESCRIBE sessions;

-- Check migration was recorded
SELECT * FROM migrations WHERE migration LIKE '%session%';

-- Check table size
SELECT 
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.TABLES
WHERE table_schema = DATABASE()
  AND table_name = 'sessions';

-- ================================================================
-- Rollback (Only if needed - NOT RECOMMENDED)
-- ================================================================
-- This will cause the error to return!
-- ALTER TABLE sessions MODIFY COLUMN payload TEXT NOT NULL;

-- ================================================================
-- Expected Results
-- ================================================================
-- Before Fix:
--   Field   | Type | Null | Key | Default | Extra
--   payload | text | NO   |     | NULL    |
--
-- After Fix:
--   Field   | Type     | Null | Key | Default | Extra
--   payload | longtext | NO   |     | NULL    |
-- ================================================================

-- Done! Test by accessing https://gerobaks.dumeg.com/openapi.yaml
