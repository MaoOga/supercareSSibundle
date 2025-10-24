-- Create CAUTI Immuno Suppressants table
-- This table stores immuno suppressants medication records for CAUTI patients

CREATE TABLE IF NOT EXISTS cauti_immuno_suppressants (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    patient_id INT(11) NOT NULL,
    record_date DATE,
    injection_name TEXT,
    start_on TEXT,
    stop_on TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Foreign key to link with patient info
    FOREIGN KEY (patient_id) REFERENCES cauti_patient_info(id) ON DELETE CASCADE,

    -- Indexes for faster queries
    INDEX idx_patient_id (patient_id),
    INDEX idx_record_date (record_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

