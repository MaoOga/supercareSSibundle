-- Script to change monitoring_date column from DATE to TEXT
-- This allows storing any string or number values instead of just valid dates

-- Backup the existing data before making changes
-- Note: If you have important data, consider backing up the table first

-- Alter the monitoring_date column from DATE to TEXT
ALTER TABLE post_operative_monitoring 
MODIFY COLUMN monitoring_date TEXT;

-- Verify the changes
DESCRIBE post_operative_monitoring;

-- Show a sample of existing data (if any)
SELECT post_op_id, patient_id, day, monitoring_date, dosage, discharge_fluid, tenderness_pain, swelling, fever 
FROM post_operative_monitoring 
LIMIT 5;
