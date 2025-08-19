<?php
// Debug script for wound complication date
header('Content-Type: application/json');

// Start output buffering to prevent any output
ob_start();

try {
    // Log all POST data
    error_log("DEBUG WOUND DATE - All POST data: " . print_r($_POST, true));
    
    // Check specifically for the wound complication date
    $woundDate = $_POST['Wound-Complication_Date'] ?? 'NOT FOUND';
    error_log("DEBUG WOUND DATE - Wound-Complication_Date value: '$woundDate'");
    
    // Check if the key exists
    $hasKey = array_key_exists('Wound-Complication_Date', $_POST);
    error_log("DEBUG WOUND DATE - Key exists: " . ($hasKey ? 'YES' : 'NO'));
    
    // Check all keys that contain 'Wound' or 'Date'
    $woundKeys = [];
    $dateKeys = [];
    foreach (array_keys($_POST) as $key) {
        if (strpos($key, 'Wound') !== false) {
            $woundKeys[] = $key;
        }
        if (strpos($key, 'Date') !== false) {
            $dateKeys[] = $key;
        }
    }
    error_log("DEBUG WOUND DATE - Keys containing 'Wound': " . implode(', ', $woundKeys));
    error_log("DEBUG WOUND DATE - Keys containing 'Date': " . implode(', ', $dateKeys));
    
    // Test date parsing
    if (!empty($woundDate) && $woundDate !== 'NOT FOUND') {
        $parsedDate = date('Y-m-d', strtotime(str_replace('/', '-', $woundDate)));
        error_log("DEBUG WOUND DATE - Parsed date: '$parsedDate'");
    } else {
        error_log("DEBUG WOUND DATE - No date to parse");
    }
    
    // Clean output buffer
    ob_clean();
    
    echo json_encode([
        'success' => true,
        'debug_info' => [
            'wound_date_value' => $woundDate,
            'key_exists' => $hasKey,
            'wound_keys' => $woundKeys,
            'date_keys' => $dateKeys,
            'total_post_fields' => count($_POST)
        ]
    ]);

} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

ob_end_flush();
?>
