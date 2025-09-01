<?php
require_once '../database/config.php';
require_once 'session_config.php';
require_once '../audit/audit_logger.php';

header('Content-Type: application/json');

try {
    // Log the logout if nurse was logged in
    if (isNurseLoggedIn()) {
        $nurseInfo = getNurseInfo();
        $auditLogger = new AuditLogger($pdo);
        $auditLogger->log(
            $nurseInfo['nurse_id'],
            'LOGOUT',
            'NURSE',
            $nurseInfo['id'],
            $nurseInfo['name'],
            "Nurse logged out: {$nurseInfo['name']} ({$nurseInfo['nurse_id']})",
            null,
            null
        );
    }
    
    // Force session timeout
    forceSessionTimeout();
    
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
