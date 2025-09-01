<?php
// Simplified test version of delete_row.php
header('Content-Type: application/json');

// Basic error handling
try {
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }
    
    // Get parameters
    $patientId = $_POST['patient_id'] ?? null;
    $tableType = $_POST['table_type'] ?? null;
    $rowNumber = $_POST['row_number'] ?? null;
    
    // Validate parameters
    if (!$patientId || !$tableType || !$rowNumber) {
        throw new Exception('Missing required parameters: patient_id, table_type, or row_number');
    }
    
    // For testing, just return success without actually deleting
    echo json_encode([
        'success' => true,
        'message' => "Test: Would delete row $rowNumber from $tableType table for patient $patientId",
        'deleted_rows' => 1,
        'test_mode' => true
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
