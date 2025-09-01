<?php
// Test script to verify delete row functionality
header('Content-Type: text/plain');

echo "=== DELETE ROW FUNCTIONALITY TEST ===\n\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST data received:\n";
    echo "Total POST fields: " . count($_POST) . "\n\n";
    
    // Check required parameters
    $patientId = $_POST['patient_id'] ?? 'NOT SET';
    $tableType = $_POST['table_type'] ?? 'NOT SET';
    $rowNumber = $_POST['row_number'] ?? 'NOT SET';
    
    echo "=== DELETE PARAMETERS ===\n";
    echo "Patient ID: '$patientId'\n";
    echo "Table Type: '$tableType'\n";
    echo "Row Number: '$rowNumber'\n\n";
    
    // Validate parameters
    if ($patientId === 'NOT SET' || $tableType === 'NOT SET' || $rowNumber === 'NOT SET') {
        echo "ERROR: Missing required parameters for delete operation\n";
    } else {
        echo "SUCCESS: All required parameters are present\n";
        echo "This would delete row $rowNumber from $tableType table for patient $patientId\n";
    }
    
    echo "\n=== ALL POST DATA ===\n";
    foreach ($_POST as $key => $value) {
        echo "$key: '$value'\n";
    }
    
} else {
    echo "No POST data received. Use this script to test delete row functionality.\n";
    echo "To test: Submit a delete request and check what data is being sent.\n";
}
?>
