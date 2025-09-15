<?php
// Start output buffering to capture any potential output
ob_start();

// Suppress error reporting to prevent HTML error messages from corrupting JSON output
error_reporting(0);
ini_set('display_errors', 0);

require_once '../database/config.php';
require_once '../audit/audit_logger.php';

// Clear any output that might have been generated
ob_clean();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Check if nurse is logged in
    // Session management removed - no authentication required

    $pdo->beginTransaction();
    
    $formData = $_POST;
    $patientId = $formData['patient_id'] ?? null;
    $tableType = $formData['table_type'] ?? null; // 'drains', 'antibiotics', 'post_operative'
    $rowNumber = $formData['row_number'] ?? null;
    
    if (!$patientId || !$tableType || !$rowNumber) {
        throw new Exception('Missing required parameters: patient_id, table_type, or row_number');
    }
    
    // Session management removed - using default nurse info for audit logging
    $nurseIdCode = 'SYSTEM';
    $nurseName = 'System User';
    
    error_log("Deleting row - Patient ID: $patientId, Table: $tableType, Row: $rowNumber");
    error_log("Nurse ID: $nurseIdCode, Nurse Name: $nurseName");
    
    switch ($tableType) {
        case 'drains':
            $stmt = $pdo->prepare("DELETE FROM drains WHERE patient_id = ? AND drain_number = ?");
            $stmt->execute([$patientId, $rowNumber]);
            $deletedRows = $stmt->rowCount();
            error_log("Deleted $deletedRows drain row(s)");
            break;
            
        case 'antibiotics':
            $stmt = $pdo->prepare("DELETE FROM antibiotic_usage WHERE patient_id = ? AND serial_no = ?");
            $stmt->execute([$patientId, $rowNumber]);
            $deletedRows = $stmt->rowCount();
            error_log("Deleted $deletedRows antibiotic row(s)");
            break;
            
        case 'post_operative':
            $stmt = $pdo->prepare("DELETE FROM post_operative_monitoring WHERE patient_id = ? AND day = ?");
            $stmt->execute([$patientId, $rowNumber]);
            $deletedRows = $stmt->rowCount();
            error_log("Deleted $deletedRows post-operative monitoring row(s)");
            break;
            
        default:
            throw new Exception('Invalid table type: ' . $tableType);
    }
    
    // Log the audit event
    $auditLogger = new AuditLogger($pdo);
    $auditData = [
        'patient_id' => $patientId,
        'table_type' => $tableType,
        'row_number' => $rowNumber,
        'action_type' => 'DELETE_ROW',
        'deleted_rows' => $deletedRows
    ];
    
    $auditLogger->log(
        $nurseIdCode,
        'DELETE',
        'PATIENT_DATA',
        $patientId,
        $nurseName,
        "Deleted row $rowNumber from $tableType table for patient ID: $patientId",
        null,
        $auditData
    );
    
    error_log("Row delete audit log completed successfully");
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => "Row deleted successfully from $tableType table",
        'deleted_rows' => $deletedRows
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error deleting row: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
}

ob_end_flush();
?>
