-- Add notes column to cauti_patient_info table
-- This column will store nurse notes/observations for the patient
-- Note: If column already exists, you'll get an error (which is fine - just means it's already there)

ALTER TABLE cauti_patient_info 
ADD COLUMN nurse_notes TEXT AFTER diagnosis;

