<?php
require_once 'config.php';

echo "<h2>Database Setup</h2>";
echo "<p>Setting up database tables and fields for the new features...</p>";

try {
    $pdo->beginTransaction();
    
    // Create risk_factors table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS risk_factors (
        risk_id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT,
        weight TEXT,
        height TEXT,
        steroids TEXT,
        tuberculosis TEXT,
        others TEXT,
        FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
    )");
    echo "<p>✓ Risk Factors table ready</p>";
    
    // Create infection_prevention_notes table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS infection_prevention_notes (
        note_id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT,
        infection_prevention_notes TEXT,
        signature VARCHAR(255),
        FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
    )");
    echo "<p>✓ Infection Prevention Notes table ready</p>";
    
    // Add organism_identified_deep field to wound_complications if it doesn't exist
    $stmt = $pdo->query("SHOW COLUMNS FROM wound_complications LIKE 'organism_identified_deep'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE wound_complications ADD COLUMN organism_identified_deep BOOLEAN AFTER organism_identified_superficial");
        echo "<p>✓ Added organism_identified_deep field to wound_complications</p>";
    } else {
        echo "<p>✓ organism_identified_deep field already exists</p>";
    }
    
    // Add signature field to patients table if it doesn't exist
    $stmt = $pdo->query("SHOW COLUMNS FROM patients LIKE 'signature'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE patients ADD COLUMN signature VARCHAR(255) AFTER date_completed");
        echo "<p>✓ Added signature field to patients table</p>";
    } else {
        echo "<p>✓ signature field already exists in patients table</p>";
    }
    
    // Set default values for existing records
    $pdo->exec("UPDATE wound_complications SET organism_identified_deep = 0 WHERE organism_identified_deep IS NULL");
    
    $pdo->commit();
    
    echo "<h3>✅ Database setup completed successfully!</h3>";
    echo "<p>All new fields are now available:</p>";
    echo "<ul>";
    echo "<li>✅ New checkbox in wound complications: \"Identification of an organism from the surgical site\" under Deep SI</li>";
    echo "<li>✅ Infection Prevention Control Nurse Notes field</li>";
    echo "<li>✅ Signature field</li>";
    echo "<li>✅ Form validation and data saving for all new fields</li>";
    echo "<li>✅ Data retrieval when editing existing patient records</li>";
    echo "<li>✅ Database compatibility with existing data</li>";
    echo "</ul>";
    echo "<p><a href='../forms/form_template.html'>Go to form</a></p>";
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "<h3>❌ Error occurred:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3 { color: #333; }
p { margin: 10px 0; }
ul { margin: 20px 0; }
li { margin: 5px 0; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
