<?php
// Test script specifically for antibiotic usage submission
header('Content-Type: text/plain');

echo "=== ANTIBIOTIC USAGE SUBMISSION TEST ===\n\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST data received:\n";
    echo "Total POST fields: " . count($_POST) . "\n\n";
    
    // Check antibiotic usage fields specifically
    echo "=== ANTIBIOTIC USAGE FIELDS ===\n";
    
    // Check if antibiotic_usage array exists
    if (isset($_POST['antibiotic_usage'])) {
        echo "antibiotic_usage array exists: " . print_r($_POST['antibiotic_usage'], true) . "\n";
    } else {
        echo "antibiotic_usage array NOT found\n";
    }
    
    // Check individual antibiotic rows
    for ($i = 1; $i <= 5; $i++) {
        echo "\n--- Row $i ---\n";
        
        // Basic fields
        $drugName = $_POST["drug-name_$i"] ?? 'NOT SET';
        $dosage = $_POST["dosage_$i"] ?? 'NOT SET';
        echo "Drug Name: '$drugName'\n";
        echo "Dosage: '$dosage'\n";
        
        // Date fields - try both array and direct access
        $startedOn = 'NOT SET';
        $stoppedOn = 'NOT SET';
        
        // Method 1: Array access
        if (isset($_POST['antibiotic_usage']) && is_array($_POST['antibiotic_usage'])) {
            // Check if it's the old format (without row numbers)
            if (isset($_POST['antibiotic_usage']['startedon']) && !isset($_POST['antibiotic_usage']["startedon_$i"])) {
                // Use the single values for all rows
                $startedOn = $_POST['antibiotic_usage']['startedon'] ?? 'NOT SET (array - single)';
                $stoppedOn = $_POST['antibiotic_usage']['stoppeon'] ?? 'NOT SET (array - single)';
            } else {
                // Use the row-specific values
                $startedOn = $_POST['antibiotic_usage']["startedon_$i"] ?? 'NOT SET (array - row)';
                $stoppedOn = $_POST['antibiotic_usage']["stoppeon_$i"] ?? 'NOT SET (array - row)';
            }
        }
        
        // Method 2: Direct field access
        $startedOnKey = "antibiotic_usage[startedon]_$i";
        $stoppedOnKey = "antibiotic_usage[stoppeon]_$i";
        $startedOnDirect = $_POST[$startedOnKey] ?? 'NOT SET (direct)';
        $stoppedOnDirect = $_POST[$stoppedOnKey] ?? 'NOT SET (direct)';
        
        // Method 3: Direct field names without array notation
        $startedOnSimple = $_POST["startedon_$i"] ?? 'NOT SET (simple)';
        $stoppedOnSimple = $_POST["stoppeon_$i"] ?? 'NOT SET (simple)';
        
        echo "Started On (array): '$startedOn'\n";
        echo "Stopped On (array): '$stoppedOn'\n";
        echo "Started On (direct): '$startedOnDirect'\n";
        echo "Stopped On (direct): '$stoppedOnDirect'\n";
        echo "Started On (simple): '$startedOnSimple'\n";
        echo "Stopped On (simple): '$stoppedOnSimple'\n";
    }
    
    // Check all fields containing 'antibiotic'
    echo "\n=== ALL ANTIBIOTIC RELATED FIELDS ===\n";
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'antibiotic') !== false) {
            if (is_array($value)) {
                echo "$key (array): " . print_r($value, true) . "\n";
            } else {
                echo "$key: '$value'\n";
            }
        }
    }
    
    echo "\n=== ALL POST DATA ===\n";
    foreach ($_POST as $key => $value) {
        if (is_array($value)) {
            echo "$key (array): " . print_r($value, true) . "\n";
        } else {
            echo "$key: '$value'\n";
        }
    }
    
} else {
    echo "No POST data received. Use this script to test antibiotic usage submission.\n";
    echo "To test: Submit a form with antibiotic data and check what is being sent.\n";
}
?>
