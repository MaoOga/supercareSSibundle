-- Create CAUTI Urine Output table
-- This table stores urine output records for CAUTI patients (24-hour monitoring)

CREATE TABLE IF NOT EXISTS cauti_urine_output (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    patient_id INT(11) NOT NULL,
    output_date DATE,
    output_time TIME,
    amount TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Foreign key to link with patient info
    FOREIGN KEY (patient_id) REFERENCES cauti_patient_info(id) ON DELETE CASCADE,

    -- Indexes for faster queries
    INDEX idx_patient_id (patient_id),
    INDEX idx_output_date (output_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

