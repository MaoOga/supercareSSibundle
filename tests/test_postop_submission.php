<?php
// Test script specifically for post-operative monitoring submission
header('Content-Type: text/plain');

echo "=== POST-OPERATIVE MONITORING SUBMISSION TEST ===\n\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST data received:\n";
    echo "Total POST fields: " . count($_POST) . "\n\n";
    
    // Check post-operative monitoring fields specifically
    echo "=== POST-OPERATIVE MONITORING FIELDS ===\n";
    
    // Check if post-operative array exists
    if (isset($_POST['post-operative'])) {
        echo "post-operative array exists: " . print_r($_POST['post-operative'], true) . "\n";
    } else {
        echo "post-operative array NOT found\n";
    }
    
    // Check individual post-operative monitoring rows
    for ($i = 1; $i <= 5; $i++) {
        echo "\n--- Row $i ---\n";
        
        // Basic fields
        $dosage = $_POST["post-dosage_$i"] ?? 'NOT SET';
        $discharge = $_POST["type-ofdischarge_$i"] ?? 'NOT SET';
        $tenderness = $_POST["tenderness-pain_$i"] ?? 'NOT SET';
        $swelling = $_POST["swelling_$i"] ?? 'NOT SET';
        $fever = $_POST["Fever_$i"] ?? 'NOT SET';
        
        echo "Dosage: '$dosage'\n";
        echo "Discharge: '$discharge'\n";
        echo "Tenderness: '$tenderness'\n";
        echo "Swelling: '$swelling'\n";
        echo "Fever: '$fever'\n";
        
        // Date field - try multiple access methods
        $date = 'NOT SET';
        
        // Method 1: Array access
        if (isset($_POST['post-operative']) && is_array($_POST['post-operative'])) {
            // Check if it's the old format (without row numbers)
            if (isset($_POST['post-operative']['date']) && !isset($_POST['post-operative']["date_$i"])) {
                // Use the single values for all rows
                $date = $_POST['post-operative']['date'] ?? 'NOT SET (array - single)';
            } else {
                // Use the row-specific values
                $date = $_POST['post-operative']["date_$i"] ?? 'NOT SET (array - row)';
            }
        }
        
        // Method 2: Direct field access
        $dateKey = "post-operative[date]_$i";
        $dateDirect = $_POST[$dateKey] ?? 'NOT SET (direct)';
        
        // Method 3: Direct field names without array notation
        $dateSimple = $_POST["date_$i"] ?? 'NOT SET (simple)';
        
        echo "Date (array): '$date'\n";
        echo "Date (direct): '$dateDirect'\n";
        echo "Date (simple): '$dateSimple'\n";
    }
    
    // Check all fields containing 'post' or 'operative'
    echo "\n=== ALL POST-OPERATIVE RELATED FIELDS ===\n";
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'post') !== false || strpos($key, 'operative') !== false) {
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
    echo "No POST data received. Use this script to test post-operative monitoring submission.\n";
    echo "To test: Submit a form with post-operative monitoring data and check what is being sent.\n";
}
?>
