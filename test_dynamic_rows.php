<?php
// Test script to verify dynamic row data is being saved and retrieved correctly
header('Content-Type: text/plain');

echo "=== DYNAMIC ROWS TEST ===\n\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST data received:\n";
    echo "Total POST fields: " . count($_POST) . "\n\n";
    
    // Check drains
    echo "=== DRAINS ===\n";
    $drainCount = 0;
    for ($i = 1; $i <= 10; $i++) {
        $drainDesc = $_POST["drain_$i"] ?? null;
        if (!empty($drainDesc)) {
            echo "Drain $i: '$drainDesc'\n";
            $drainCount++;
        }
    }
    echo "Total drains found: $drainCount\n";
    
    // Check antibiotics
    echo "\n=== ANTIBIOTICS ===\n";
    $antibioticCount = 0;
    for ($i = 1; $i <= 10; $i++) {
        $drugName = $_POST["drug-name_$i"] ?? null;
        if (!empty($drugName)) {
            echo "Antibiotic $i: '$drugName'\n";
            $antibioticCount++;
        }
    }
    echo "Total antibiotics found: $antibioticCount\n";
    
    // Check post-operative monitoring
    echo "\n=== POST-OPERATIVE MONITORING ===\n";
    $monitoringCount = 0;
    for ($i = 1; $i <= 10; $i++) {
        $date = $_POST["post-operative[date]_$i"] ?? null;
        if (!empty($date)) {
            echo "Monitoring $i: Date='$date'\n";
            $monitoringCount++;
        }
    }
    echo "Total monitoring entries found: $monitoringCount\n";
    
    echo "\n=== ALL POST DATA ===\n";
    foreach ($_POST as $key => $value) {
        if (is_array($value)) {
            echo "$key (array): " . print_r($value, true) . "\n";
        } else {
            echo "$key: '$value'\n";
        }
    }
    
} else {
    echo "No POST data received. Use this script to test dynamic row submission.\n";
    echo "To test: Submit a form with extra rows and check what data is being sent.\n";
}
?>
