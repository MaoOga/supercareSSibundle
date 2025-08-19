<?php
// Test script to see exactly what form data is being submitted
header('Content-Type: text/plain');

echo "=== FORM SUBMISSION TEST ===\n\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST data received:\n";
    echo "Total POST fields: " . count($_POST) . "\n\n";
    
    // Check if arrays exist
    echo "=== ARRAY FIELDS ===\n";
    if (isset($_POST['antibiotic_usage'])) {
        echo "antibiotic_usage array exists: " . print_r($_POST['antibiotic_usage'], true) . "\n";
    } else {
        echo "antibiotic_usage array NOT found\n";
    }
    
    if (isset($_POST['post-operative'])) {
        echo "post-operative array exists: " . print_r($_POST['post-operative'], true) . "\n";
    } else {
        echo "post-operative array NOT found\n";
    }
    
    echo "\n=== SPECIFIC FIELDS ===\n";
    
    // Check drains
    echo "DRAINS:\n";
    for ($i = 1; $i <= 3; $i++) {
        $drainDesc = $_POST["drain-description_$i"] ?? 'NOT SET';
        echo "drain-description_$i: $drainDesc\n";
    }
    
    // Check antibiotics
    echo "\nANTIBIOTICS:\n";
    for ($i = 1; $i <= 3; $i++) {
        $drugName = $_POST["drug-name_$i"] ?? 'NOT SET';
        $dosage = $_POST["dosage_$i"] ?? 'NOT SET';
        
        // Check array access
        $startedOn = 'NOT SET';
        $stoppedOn = 'NOT SET';
        if (isset($_POST['antibiotic_usage']) && is_array($_POST['antibiotic_usage'])) {
            $startedOn = $_POST['antibiotic_usage']["startedon_$i"] ?? 'NOT SET';
            $stoppedOn = $_POST['antibiotic_usage']["stoppeon_$i"] ?? 'NOT SET';
        }
        
        echo "Row $i: Drug=$drugName, Dosage=$dosage, Started=$startedOn, Stopped=$stoppedOn\n";
    }
    
    // Check post-operative monitoring
    echo "\nPOST-OPERATIVE MONITORING:\n";
    for ($i = 1; $i <= 3; $i++) {
        $date = 'NOT SET';
        if (isset($_POST['post-operative']) && is_array($_POST['post-operative'])) {
            $date = $_POST['post-operative']["date_$i"] ?? 'NOT SET';
        }
        
        $dosage = $_POST["post-dosage_$i"] ?? 'NOT SET';
        $discharge = $_POST["type-ofdischarge_$i"] ?? 'NOT SET';
        $tenderness = $_POST["tenderness-pain_$i"] ?? 'NOT SET';
        $swelling = $_POST["swelling_$i"] ?? 'NOT SET';
        $fever = $_POST["Fever_$i"] ?? 'NOT SET';
        
        echo "Row $i: Date=$date, Dosage=$dosage, Discharge=$discharge, Tenderness=$tenderness, Swelling=$swelling, Fever=$fever\n";
    }
    
    echo "\n=== ALL POST DATA ===\n";
    foreach ($_POST as $key => $value) {
        if (is_array($value)) {
            echo "$key (array): " . print_r($value, true) . "\n";
        } else {
            echo "$key: $value\n";
        }
    }
} else {
    echo "No POST data received. Use this script to test form submission.\n";
    echo "To test: Submit a form and check what data is being sent.\n";
}
?>
