<?php
// Debug script to test delete_row.php
echo "=== DEBUGGING DELETE_ROW.PHP ===\n\n";

// Test if required files exist
$requiredFiles = ['../database/config.php', '../audit/audit_logger.php'];
foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "✓ $file exists\n";
    } else {
        echo "✗ $file NOT FOUND\n";
    }
}

echo "\n=== TESTING DELETE_ROW.PHP DIRECTLY ===\n";

// Simulate POST data
$_POST = [
    'patient_id' => '1',
    'table_type' => 'drains',
    'row_number' => '1'
];

// Capture output
ob_start();

// Include the delete_row.php file
try {
    include '../forms/delete_row.php';
    $output = ob_get_contents();
    ob_end_clean();
    
    echo "Output received:\n";
    echo "Length: " . strlen($output) . " characters\n";
    echo "Content:\n";
    echo $output . "\n";
    
    // Try to decode JSON
    $jsonData = json_decode($output, true);
    if ($jsonData === null) {
        echo "JSON decode error: " . json_last_error_msg() . "\n";
    } else {
        echo "JSON decoded successfully:\n";
        print_r($jsonData);
    }
    
} catch (Exception $e) {
    ob_end_clean();
    echo "Exception caught: " . $e->getMessage() . "\n";
} catch (Error $e) {
    ob_end_clean();
    echo "Error caught: " . $e->getMessage() . "\n";
}

echo "\n=== END DEBUG ===\n";
?>
