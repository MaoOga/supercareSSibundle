<?php
// Debug script to see exactly what POST data is being received
header('Content-Type: text/plain');

echo "=== POST DATA DEBUG ===\n\n";

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
    
} else {
    echo "No POST data received.\n";
}
?>
