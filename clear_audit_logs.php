<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

try {
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST requests are allowed');
    }

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['confirm']) || $input['confirm'] !== true) {
        throw new Exception('Confirmation required to clear all logs');
    }

    // Clear all audit logs
    $stmt = $pdo->prepare("DELETE FROM admin_audit_logs");
    $result = $stmt->execute();

    if ($result) {
        $deletedCount = $stmt->rowCount();
        echo json_encode([
            'success' => true,
            'message' => "Successfully cleared {$deletedCount} audit log entries",
            'deleted_count' => $deletedCount
        ]);
    } else {
        throw new Exception('Failed to clear audit logs');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
