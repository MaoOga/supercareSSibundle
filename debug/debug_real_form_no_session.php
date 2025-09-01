<?php
// Debug script without session requirements
header('Content-Type: text/plain');

echo "=== REAL FORM POST DATA DEBUG (NO SESSION) ===\n\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST data received:\n";
    echo "Total POST fields: " . count($_POST) . "\n\n";
    
    echo "=== ALL POST DATA ===\n";
    foreach ($_POST as $key => $value) {
        if (is_array($value)) {
            echo "$key (array): " . print_r($value, true) . "\n";
        } else {
            echo "$key: '$value'\n";
        }
    }
    
    echo "\n=== POST-OPERATIVE SPECIFIC FIELDS ===\n";
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'post-operative') !== false || strpos($key, 'post-dosage') !== false || strpos($key, 'type-ofdischarge') !== false || strpos($key, 'tenderness-pain') !== false || strpos($key, 'swelling') !== false || strpos($key, 'Fever') !== false) {
            if (is_array($value)) {
                echo "$key (array): " . print_r($value, true) . "\n";
            } else {
                echo "$key: '$value'\n";
            }
        }
    }
    
    echo "\n=== TESTING ARRAY ACCESS ===\n";
    for ($i = 1; $i <= 5; $i++) {
        $dateKey = "post-operative[date]_$i";
        $dateValue = $_POST[$dateKey] ?? 'NOT FOUND';
        echo "Key: '$dateKey' = '$dateValue'\n";
        
        $dosageKey = "post-dosage_$i";
        $dosageValue = $_POST[$dosageKey] ?? 'NOT FOUND';
        echo "Key: '$dosageKey' = '$dosageValue'\n";
        
        $dischargeKey = "type-ofdischarge_$i";
        $dischargeValue = $_POST[$dischargeKey] ?? 'NOT FOUND';
        echo "Key: '$dischargeKey' = '$dischargeValue'\n";
        
        $tendernessKey = "tenderness-pain_$i";
        $tendernessValue = $_POST[$tendernessKey] ?? 'NOT FOUND';
        echo "Key: '$tendernessKey' = '$tendernessValue'\n";
        
        $swellingKey = "swelling_$i";
        $swellingValue = $_POST[$swellingKey] ?? 'NOT FOUND';
        echo "Key: '$swellingKey' = '$swellingValue'\n";
        
        $feverKey = "Fever_$i";
        $feverValue = $_POST[$feverKey] ?? 'NOT FOUND';
        echo "Key: '$feverKey' = '$feverValue'\n";
        
        echo "---\n";
    }
    
} else {
    echo "No POST data received.\n";
}
?>
