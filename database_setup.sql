-- Supercare SSI Bundle Database Setup
-- Database: supercare_ssi

-- Creating table for patient information
CREATE TABLE patients (
    patient_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    age INT,
    sex ENUM('Male', 'Female') NOT NULL,
    uhid VARCHAR(50) UNIQUE,
    phone VARCHAR(15),
    bed_ward VARCHAR(50),
    address TEXT,
    primary_diagnosis TEXT,
    surgical_procedure TEXT,
    date_completed DATE
);

-- Creating table for surgical details
CREATE TABLE surgical_details (
    surgical_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    doa DATE,
    dos DATE,
    dod DATE,
    surgeon VARCHAR(255),
    operation_duration TEXT,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
);

-- Creating table for surgical skin preparation
CREATE TABLE surgical_skin_preparation (
    preparation_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    pre_op_bath ENUM('Yes', 'No'),
    pre_op_bath_reason TEXT,
    hair_removal ENUM('Razor', 'Trimmer', 'Not Done'),
    hair_removal_reason TEXT,
    hair_removal_location ENUM('Ward', 'ICU/HDU', 'OT/LR'),
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
);

-- Creating table for implanted materials
CREATE TABLE implanted_materials (
    implant_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    implanted_used ENUM('Yes', 'No'),
    metal TEXT,
    graft TEXT,
    patch TEXT,
    shunt_stent TEXT,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
);

-- Creating table for drains
CREATE TABLE drains (
    drain_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    drain_used ENUM('Yes', 'No'),
    drain_description TEXT,
    drain_number INT, -- To indicate drain 1, 2, or 3
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
);

-- Creating table for antibiotic usage
CREATE TABLE antibiotic_usage (
    antibiotic_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    serial_no INT,
    drug_name TEXT,
    dosage_route_frequency TEXT,
    started_on DATE,
    stopped_on DATE,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
);

-- Creating table for post-operative wound monitoring
CREATE TABLE post_operative_monitoring (
    post_op_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    day INT,
    monitoring_date DATE,
    dosage TEXT,
    discharge_fluid TEXT,
    tenderness_pain TEXT,
    swelling TEXT,
    fever TEXT,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
);

-- Creating table for wound complications
CREATE TABLE wound_complications (
    complication_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    complication_date DATE,
    wound_dehiscence BOOLEAN,
    allergic_reaction BOOLEAN,
    bleeding_haemorrhage BOOLEAN,
    other_complication BOOLEAN,
    other_specify TEXT,
    notes TEXT,
    superficial_ssi BOOLEAN,
    deep_si BOOLEAN,
    organ_space_ssi BOOLEAN,
    purulent_discharge_superficial BOOLEAN,
    purulent_discharge_deep BOOLEAN,
    purulent_discharge_organ BOOLEAN,
    organism_identified_superficial BOOLEAN,
    organism_identified_organ BOOLEAN,
    clinical_diagnosis_ssi BOOLEAN,
    deep_incision_reopening BOOLEAN,
    abscess_evidence_organ BOOLEAN,
    deliberate_opening_symptoms BOOLEAN,
    abscess_evidence_deep BOOLEAN,
    not_infected_conditions BOOLEAN,
    surgeon_opinion_superficial TEXT,
    surgeon_opinion_deep TEXT,
    surgeon_opinion_organ TEXT,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
);

-- Creating table for cultural swap and dressing
CREATE TABLE cultural_dressing (
    cultural_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    cultural_swap TEXT,
    dressing_finding TEXT,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
);

-- Creating table for review and sutures
CREATE TABLE review_sutures (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    review_on DATE,
    sutures_removed_on DATE,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
);

-- Creating table for review or phone call
CREATE TABLE review_phone (
    review_phone_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT,
    review_date DATE,
    patient_identification ENUM('Yes', 'No'),
    pain ENUM('Yes', 'No'),
    pus ENUM('Yes', 'No'),
    bleeding ENUM('Yes', 'No'),
    other ENUM('Yes', 'No'),
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
);

-- Creating table for nurse accounts
CREATE TABLE nurses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nurse_id VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255),
    email VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    role ENUM('nurse', 'supervisor', 'admin') DEFAULT 'nurse',
    reset_token VARCHAR(64) NULL,
    reset_expiry TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add index for reset token for better performance
CREATE INDEX idx_reset_token ON nurses(reset_token);

-- Creating table for surgeons directory
CREATE TABLE surgeons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Creating table for audit logs
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(50) NOT NULL,
    entity VARCHAR(50) NOT NULL,
    entity_id VARCHAR(50),
    details JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
