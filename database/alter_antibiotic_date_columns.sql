-- Script to change started_on and stopped_on columns from DATE to TEXT
-- This allows storing any string or number values instead of just valid dates

-- Backup the existing data before making changes
-- Note: If you have important data, consider backing up the table first

-- Alter the started_on column from DATE to TEXT
ALTER TABLE antibiotic_usage 
MODIFY COLUMN started_on TEXT;

-- Alter the stopped_on column from DATE to TEXT  
ALTER TABLE antibiotic_usage 
MODIFY COLUMN stopped_on TEXT;

-- Verify the changes
DESCRIBE antibiotic_usage;

-- Show a sample of existing data (if any)
SELECT antibiotic_id, patient_id, drug_name, started_on, stopped_on 
FROM antibiotic_usage 
LIMIT 5;
