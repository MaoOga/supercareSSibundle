<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../database/config.php';

try {
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST requests are allowed');
    }

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['audit_id'])) {
        throw new Exception('Audit ID is required');
    }

    $auditId = $input['audit_id'];

    // Validate audit ID
    if (!is_numeric($auditId) || $auditId <= 0) {
        throw new Exception('Invalid audit ID');
    }

    // Check if the audit log exists
    $checkStmt = $pdo->prepare("SELECT audit_id FROM admin_audit_logs WHERE audit_id = ?");
    $checkStmt->execute([$auditId]);
    
    if (!$checkStmt->fetch()) {
        throw new Exception('Audit log entry not found');
    }

    // Delete the specific audit log
    $stmt = $pdo->prepare("DELETE FROM admin_audit_logs WHERE audit_id = ?");
    $result = $stmt->execute([$auditId]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Audit log entry deleted successfully',
            'audit_id' => $auditId
        ]);
    } else {
        throw new Exception('Failed to delete audit log entry');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
