<?php
// Test script to verify radio button data is being saved and retrieved correctly
header('Content-Type: text/plain');

echo "=== RADIO BUTTON TEST ===\n\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST data received:\n";
    echo "Total POST fields: " . count($_POST) . "\n\n";
    
    // Check all radio button fields
    $radioFields = [
        'drain-used',
        'implanted_used',
        'surgical_skin_preparation[pre_op_bath]',
        'surgical_skin_preparation[hair-removal]',
        'surgical_skin_preparation[removal-done]',
        'reviewp',
        'reviewppain',
        'reviewpus',
        'reviewbleed',
        'reviewother'
    ];
    
    echo "=== RADIO BUTTON VALUES ===\n";
    foreach ($radioFields as $field) {
        $value = $_POST[$field] ?? 'NOT SET';
        echo "$field: '$value'\n";
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
    echo "No POST data received. Use this script to test radio button submission.\n";
    echo "To test: Submit a form with radio buttons and check what data is being sent.\n";
}
?>
