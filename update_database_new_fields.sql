-- Database Update Script for New Fields
-- Run this script to add the new fields to existing databases

USE supercare_ssi;

-- Add new field to wound_complications table for the additional "Identification of an organism from the surgical site"
ALTER TABLE wound_complications 
ADD COLUMN organism_identified_deep BOOLEAN AFTER organism_identified_superficial;

-- Create new table for risk factors
CREATE TABLE IF NOT EXISTS risk_factors (
    risk_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    weight TEXT,
    height TEXT,
    steroids TEXT,
    tuberculosis TEXT,
    others TEXT,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
);

-- Create new table for infection prevention notes and signature
CREATE TABLE IF NOT EXISTS infection_prevention_notes (
    note_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    infection_prevention_notes TEXT,
    signature VARCHAR(255),
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
);

-- Add signature field to patients table if it doesn't exist
ALTER TABLE patients 
ADD COLUMN signature VARCHAR(255) AFTER date_completed;

-- Add SGA column to risk_factors table
ALTER TABLE risk_factors ADD COLUMN sga TEXT AFTER height;

-- Update existing records to set default values
UPDATE wound_complications SET organism_identified_deep = 0 WHERE organism_identified_deep IS NULL;

-- Display confirmation
SELECT 'Database update completed successfully!' as status;
