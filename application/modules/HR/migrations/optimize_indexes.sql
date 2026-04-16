-- ============================================================
-- HR Timesheet Database Optimization Migration
-- Run this on each tenant database
-- Idempotent: safe to re-run (checks before adding indexes)
-- ============================================================

-- 1. HR_employee: Composite index for ORDER BY first_name, last_name
SET @dbname = DATABASE();
SELECT COUNT(*) INTO @idx_exists FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'HR_employee' AND INDEX_NAME = 'idx_emp_name';
SET @sql = IF(@idx_exists > 0, 'SELECT 1', 'ALTER TABLE `HR_employee` ADD INDEX `idx_emp_name` (`first_name`, `last_name`)');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2. HR_timesheet_breaks: Index on employee_id (used in JOIN condition)
SELECT COUNT(*) INTO @idx_exists FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'HR_timesheet_breaks' AND INDEX_NAME = 'idx_breaks_employee';
SET @sql = IF(@idx_exists > 0, 'SELECT 1', 'ALTER TABLE `HR_timesheet_breaks` ADD INDEX `idx_breaks_employee` (`employee_id`)');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3. HR_timesheet_breaks: Index on timesheet_id (individual lookups)
SELECT COUNT(*) INTO @idx_exists FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'HR_timesheet_breaks' AND INDEX_NAME = 'idx_breaks_timesheet';
SET @sql = IF(@idx_exists > 0, 'SELECT 1', 'ALTER TABLE `HR_timesheet_breaks` ADD INDEX `idx_breaks_timesheet` (`timesheet_id`)');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 4. HR_emp_position: Convert MyISAM → InnoDB (safe to re-run, no-op if already InnoDB)
ALTER TABLE `HR_emp_position` ENGINE = InnoDB;

-- 5. HR_configuration: Convert MyISAM → InnoDB
ALTER TABLE `HR_configuration` ENGINE = InnoDB;

-- 6. HR_timesheet_comments: Convert MyISAM → InnoDB and add indexes
ALTER TABLE `HR_timesheet_comments` ENGINE = InnoDB;

SELECT COUNT(*) INTO @idx_exists FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'HR_timesheet_comments' AND INDEX_NAME = 'idx_comments_timesheet';
SET @sql = IF(@idx_exists > 0, 'SELECT 1', 'ALTER TABLE `HR_timesheet_comments` ADD INDEX `idx_comments_timesheet` (`timesheet_id`)');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT COUNT(*) INTO @idx_exists FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'HR_timesheet_comments' AND INDEX_NAME = 'idx_comments_employee';
SET @sql = IF(@idx_exists > 0, 'SELECT 1', 'ALTER TABLE `HR_timesheet_comments` ADD INDEX `idx_comments_employee` (`employee_id`)');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 7. HR_timesheet_details: Already has idx_timesheet_date_location(roster_date, location_id, is_deleted)
--    which covers the main WHERE clause. No additional index needed.
