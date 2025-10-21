-- Add form_access column to nurses table
-- This allows nurses to be assigned to specific forms (SSI, CAUTI, Both, or All)

-- Check if column doesn't exist before adding it
ALTER TABLE nurses 
ADD COLUMN IF NOT EXISTS form_access VARCHAR(50) DEFAULT 'ssi' AFTER email;

-- Update existing nurses to have SSI access by default
UPDATE nurses 
SET form_access = 'ssi' 
WHERE form_access IS NULL OR form_access = '';

-- Display result
SELECT 'Form access column added successfully. All existing nurses have been set to SSI access by default.' as Status;

