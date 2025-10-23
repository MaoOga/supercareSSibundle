-- Create CAUTI Urine Culture / Foley's Tip table
-- This table stores urine culture and foley's tip test records for CAUTI patients

CREATE TABLE IF NOT EXISTS cauti_urine_culture (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    patient_id INT(11) NOT NULL,
    sending_date DATE,
    reporting_date DATE,
    sample_type TEXT,
    result TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Foreign key to link with patient info
    FOREIGN KEY (patient_id) REFERENCES cauti_patient_info(id) ON DELETE CASCADE,

    -- Indexes for faster queries
    INDEX idx_patient_id (patient_id),
    INDEX idx_sending_date (sending_date),
    INDEX idx_reporting_date (reporting_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

