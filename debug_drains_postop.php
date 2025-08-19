<?php
// Focused debug script for drains and post-operative monitoring
header('Content-Type: text/plain');

echo "=== DRAINS AND POST-OPERATIVE MONITORING DEBUG ===\n\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST data received:\n";
    echo "Total POST fields: " . count($_POST) . "\n\n";
    
    // Check drains specifically
    echo "=== DRAINS DEBUG ===\n";
    echo "Drain used field: " . ($_POST['drain-used'] ?? 'NOT SET') . "\n";
    
    for ($i = 1; $i <= 5; $i++) {
        $drainDesc = $_POST["drain_$i"] ?? 'NOT SET';
        echo "drain_$i: '$drainDesc'\n";
    }
    
    // Check antibiotics specifically
    echo "\n=== ANTIBIOTICS DEBUG ===\n";
    for ($i = 1; $i <= 5; $i++) {
        $drugName = $_POST["drug-name_$i"] ?? 'NOT SET';
        $dosage = $_POST["dosage_$i"] ?? 'NOT SET';
        
        // Check array access
        $startedOn = 'NOT SET';
        $stoppedOn = 'NOT SET';
        if (isset($_POST['antibiotic_usage']) && is_array($_POST['antibiotic_usage'])) {
            // Check if it's the old format (without row numbers)
            if (isset($_POST['antibiotic_usage']['startedon']) && !isset($_POST['antibiotic_usage']["startedon_$i"])) {
                // Use the single values for all rows
                $startedOn = $_POST['antibiotic_usage']['startedon'] ?? 'NOT SET';
                $stoppedOn = $_POST['antibiotic_usage']['stoppeon'] ?? 'NOT SET';
            } else {
                // Use the row-specific values
                $startedOn = $_POST['antibiotic_usage']["startedon_$i"] ?? 'NOT SET';
                $stoppedOn = $_POST['antibiotic_usage']["stoppeon_$i"] ?? 'NOT SET';
            }
        } else {
            // Check direct field access
            $startedOnKey = "antibiotic_usage[startedon]_$i";
            $stoppedOnKey = "antibiotic_usage[stoppeon]_$i";
            $startedOn = $_POST[$startedOnKey] ?? 'NOT SET';
            $stoppedOn = $_POST[$stoppedOnKey] ?? 'NOT SET';
        }
        
        echo "Row $i: Drug=$drugName, Dosage=$dosage, Started=$startedOn, Stopped=$stoppedOn\n";
    }
    
    // Check post-operative monitoring specifically
    echo "\n=== POST-OPERATIVE MONITORING DEBUG ===\n";
    
    // Check if post-operative array exists
    if (isset($_POST['post-operative'])) {
        echo "post-operative array exists: " . print_r($_POST['post-operative'], true) . "\n";
    } else {
        echo "post-operative array NOT found\n";
    }
    
    for ($i = 1; $i <= 5; $i++) {
        $date = 'NOT SET';
        if (isset($_POST['post-operative']) && is_array($_POST['post-operative'])) {
            // Check if it's the old format (without row numbers)
            if (isset($_POST['post-operative']['date']) && !isset($_POST['post-operative']["date_$i"])) {
                // Use the single values for all rows
                $date = $_POST['post-operative']['date'] ?? 'NOT SET';
            } else {
                // Use the row-specific values
                $date = $_POST['post-operative']["date_$i"] ?? 'NOT SET';
            }
        } else {
            // Check direct field access
            $dateKey = "post-operative[date]_$i";
            $date = $_POST[$dateKey] ?? 'NOT SET';
            
            // If still not found, try direct field names without array notation
            if ($date === 'NOT SET') {
                $date = $_POST["date_$i"] ?? 'NOT SET';
            }
        }
        
        $dosage = $_POST["post-dosage_$i"] ?? 'NOT SET';
        $discharge = $_POST["type-ofdischarge_$i"] ?? 'NOT SET';
        $tenderness = $_POST["tenderness-pain_$i"] ?? 'NOT SET';
        $swelling = $_POST["swelling_$i"] ?? 'NOT SET';
        $fever = $_POST["Fever_$i"] ?? 'NOT SET';
        
        echo "Row $i:\n";
        echo "  Date: '$date'\n";
        echo "  Dosage: '$dosage'\n";
        echo "  Discharge: '$discharge'\n";
        echo "  Tenderness: '$tenderness'\n";
        echo "  Swelling: '$swelling'\n";
        echo "  Fever: '$fever'\n";
    }
    
    // Check all form fields that contain 'drain', 'post', or 'antibiotic'
    echo "\n=== ALL DRAIN, ANTIBIOTIC, AND POST-OPERATIVE RELATED FIELDS ===\n";
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'drain') !== false || strpos($key, 'post') !== false || strpos($key, 'operative') !== false || strpos($key, 'antibiotic') !== false) {
            if (is_array($value)) {
                echo "$key (array): " . print_r($value, true) . "\n";
            } else {
                echo "$key: '$value'\n";
            }
        }
    }
    
    echo "\n=== ALL POST DATA (first 20 fields) ===\n";
    $count = 0;
    foreach ($_POST as $key => $value) {
        if ($count >= 20) {
            echo "... (showing first 20 fields only)\n";
            break;
        }
        if (is_array($value)) {
            echo "$key (array): " . print_r($value, true) . "\n";
        } else {
            echo "$key: '$value'\n";
        }
        $count++;
    }
    
} else {
    echo "No POST data received. Use this script to test form submission.\n";
    echo "To test: Submit a form and check what data is being sent.\n";
}
?>
