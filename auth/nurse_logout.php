<?php
require_once '../database/config.php';
require_once '../audit/audit_logger.php';

header('Content-Type: application/json');

try {
    // Session management removed - no authentication required
    echo json_encode([
        'success' => true,
        'message' => 'Logged out successfully'
    ]);
    
} catch (Exception $e) {
    error_log("Logout error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error during logout'
    ]);
}
?>
