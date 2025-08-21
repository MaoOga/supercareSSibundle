<?php
// Database Update Script
// This script will update the database with the new fields

require_once 'config.php';

echo "<h2>Database Update Script</h2>";
echo "<p>Updating database with new fields...</p>";

try {
    $pdo->beginTransaction();
    
    // Check if the new field already exists
    $checkField = $pdo->query("SHOW COLUMNS FROM wound_complications LIKE 'organism_identified_deep'");
    $fieldExists = $checkField->rowCount() > 0;
    
    if (!$fieldExists) {
        // Add new field to wound_complications table
        $pdo->exec("ALTER TABLE wound_complications ADD COLUMN organism_identified_deep BOOLEAN AFTER organism_identified_superficial");
        echo "<p>✓ Added organism_identified_deep field to wound_complications table</p>";
    } else {
        echo "<p>✓ organism_identified_deep field already exists</p>";
    }
    
    // Check if risk_factors table exists
    $checkRiskTable = $pdo->query("SHOW TABLES LIKE 'risk_factors'");
    $riskTableExists = $checkRiskTable->rowCount() > 0;
    
    if (!$riskTableExists) {
        // Create new table for risk factors
        $pdo->exec("CREATE TABLE risk_factors (
            risk_id INT AUTO_INCREMENT PRIMARY KEY,
            patient_id INT,
            weight TEXT,
            height TEXT,
            steroids TEXT,
            tuberculosis TEXT,
            others TEXT,
            FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
        )");
        echo "<p>✓ Created risk_factors table</p>";
    } else {
        echo "<p>✓ risk_factors table already exists</p>";
    }
    
    // Check if infection_prevention_notes table exists
    $checkTable = $pdo->query("SHOW TABLES LIKE 'infection_prevention_notes'");
    $tableExists = $checkTable->rowCount() > 0;
    
    if (!$tableExists) {
        // Create new table for infection prevention notes and signature
        $pdo->exec("CREATE TABLE infection_prevention_notes (
            note_id INT AUTO_INCREMENT PRIMARY KEY,
            patient_id INT,
            infection_prevention_notes TEXT,
            signature VARCHAR(255),
            FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
        )");
        echo "<p>✓ Created infection_prevention_notes table</p>";
    } else {
        echo "<p>✓ infection_prevention_notes table already exists</p>";
    }
    
    // Check if signature field exists in patients table
    $checkSignature = $pdo->query("SHOW COLUMNS FROM patients LIKE 'signature'");
    $signatureExists = $checkSignature->rowCount() > 0;
    
    if (!$signatureExists) {
        // Add signature field to patients table
        $pdo->exec("ALTER TABLE patients ADD COLUMN signature VARCHAR(255) AFTER date_completed");
        echo "<p>✓ Added signature field to patients table</p>";
    } else {
        echo "<p>✓ signature field already exists in patients table</p>";
    }
    
    // Update existing records to set default values
    $pdo->exec("UPDATE wound_complications SET organism_identified_deep = 0 WHERE organism_identified_deep IS NULL");
    echo "<p>✓ Updated existing wound_complications records with default values</p>";
    
    $pdo->commit();
    
    echo "<h3>✅ Database update completed successfully!</h3>";
    echo "<p>All new fields have been added and are ready to use.</p>";
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "<h3>❌ Error occurred during database update:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2 { color: #333; }
h3 { color: #28a745; }
p { margin: 10px 0; }
</style>
