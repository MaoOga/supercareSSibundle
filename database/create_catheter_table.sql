-- Table for CAUTI Catheter Information
CREATE TABLE IF NOT EXISTS cauti_catheter (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    patient_id INT(11) NOT NULL,
    catheter_date DATE,
    catheter_time TIME,
    catheter_changed_on DATE,
    catheter_removed_on DATE,
    catheter_out_date DATE,
    catheter_out_time TIME,
    total_catheter_days VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraint
    FOREIGN KEY (patient_id) REFERENCES cauti_patient_info(id) ON DELETE CASCADE,
    
    -- Indexes for better query performance
    INDEX idx_patient_id (patient_id),
    INDEX idx_catheter_date (catheter_date),
    INDEX idx_catheter_changed_on (catheter_changed_on),
    INDEX idx_catheter_removed_on (catheter_removed_on)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

