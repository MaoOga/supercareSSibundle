-- Create CAUTI Problem table
-- This table stores problem records (symptoms) for CAUTI patients

CREATE TABLE IF NOT EXISTS cauti_problem (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    patient_id INT(11) NOT NULL,
    problem_date DATE,
    problem_time TIME,
    types_of_symptoms TEXT,
    pain_burning_sensation TEXT,
    fever_temperature DECIMAL(4,1),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Foreign key to link with patient info
    FOREIGN KEY (patient_id) REFERENCES cauti_patient_info(id) ON DELETE CASCADE,

    -- Indexes for faster queries
    INDEX idx_patient_id (patient_id),
    INDEX idx_problem_date (problem_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

