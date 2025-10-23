-- Create CAUTI Urine RE Pus Cells table
-- This table stores urine RE pus cells test records for CAUTI patients

CREATE TABLE IF NOT EXISTS cauti_urine_re_pus (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    patient_id INT(11) NOT NULL,
    test_date DATE,
    test_time TIME,
    pus_cells TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Foreign key to link with patient info
    FOREIGN KEY (patient_id) REFERENCES cauti_patient_info(id) ON DELETE CASCADE,

    -- Indexes for faster queries
    INDEX idx_patient_id (patient_id),
    INDEX idx_test_date (test_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

