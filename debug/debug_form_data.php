<?php
// Debug script to check form data submission
header('Content-Type: text/plain');

echo "=== FORM DATA DEBUG ===\n\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST data received:\n";
    echo "Total POST fields: " . count($_POST) . "\n\n";
    
    // Check specific fields
    echo "=== DRAINS ===\n";
    for ($i = 1; $i <= 5; $i++) {
        $drainDesc = $_POST["drain-description_$i"] ?? 'NOT SET';
        echo "drain-description_$i: $drainDesc\n";
    }
    
    echo "\n=== ANTIBIOTICS ===\n";
    for ($i = 1; $i <= 5; $i++) {
        $drugName = $_POST["drug-name_$i"] ?? 'NOT SET';
        $dosage = $_POST["dosage_$i"] ?? 'NOT SET';
        $startedOn = $_POST['antibiotic_usage']["startedon_$i"] ?? 'NOT SET';
        $stoppedOn = $_POST['antibiotic_usage']["stoppeon_$i"] ?? 'NOT SET';
        echo "Row $i: Drug=$drugName, Dosage=$dosage, Started=$startedOn, Stopped=$stoppedOn\n";
    }
    
    echo "\n=== POST-OPERATIVE MONITORING ===\n";
    for ($i = 1; $i <= 5; $i++) {
        $date = $_POST['post-operative']["date_$i"] ?? 'NOT SET';
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
            echo "$key: " . print_r($value, true) . "\n";
        } else {
            echo "$key: $value\n";
        }
    }
} else {
    echo "No POST data received. Use this script to test form submission.\n";
}
?>
