-- Migration: Fix overnight shift support
-- Changes clock_in_time and clock_out_time from TIME to DATETIME
-- This ensures accurate calculations for shifts that span midnight

-- Step 1: Add new DATETIME columns
ALTER TABLE `HR_timesheet_details` 
ADD COLUMN `clock_in_datetime` DATETIME NULL AFTER `clock_in_time`,
ADD COLUMN `clock_out_datetime` DATETIME NULL AFTER `clock_out_time`;

-- Step 2: Migrate existing data (combine roster_date with time)
-- For clock_in_datetime: use roster_date + clock_in_time
UPDATE `HR_timesheet_details` 
SET `clock_in_datetime` = CONCAT(roster_date, ' ', clock_in_time)
WHERE clock_in_time IS NOT NULL AND roster_date IS NOT NULL;

-- For clock_out_datetime: 
-- If clock_out_time < clock_in_time (overnight shift), add 1 day to roster_date
UPDATE `HR_timesheet_details` 
SET `clock_out_datetime` = CASE 
    WHEN clock_out_time < clock_in_time THEN 
        CONCAT(DATE_ADD(roster_date, INTERVAL 1 DAY), ' ', clock_out_time)
    ELSE 
        CONCAT(roster_date, ' ', clock_out_time)
END
WHERE clock_out_time IS NOT NULL AND roster_date IS NOT NULL;

-- Step 3: Rename old columns (backup)
ALTER TABLE `HR_timesheet_details` 
CHANGE COLUMN `clock_in_time` `clock_in_time_old` TIME NULL,
CHANGE COLUMN `clock_out_time` `clock_out_time_old` TIME NULL;

-- Step 4: Rename new columns to original names
ALTER TABLE `HR_timesheet_details` 
CHANGE COLUMN `clock_in_datetime` `clock_in_time` DATETIME NULL,
CHANGE COLUMN `clock_out_datetime` `clock_out_time` DATETIME NULL;

-- Step 5: Add index for performance
CREATE INDEX `idx_clock_times` ON `HR_timesheet_details` (`clock_in_time`, `clock_out_time`);

-- NOTE: After verifying data is correct, you can drop the backup columns:
-- ALTER TABLE `HR_timesheet_details` DROP COLUMN `clock_in_time_old`, DROP COLUMN `clock_out_time_old`;
