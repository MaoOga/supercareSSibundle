<?php
/**
 * Admin Session Check API
 * Returns current admin session status and remaining time
 */

// Set content type to JSON
header('Content-Type: application/json');

// Allow both GET and POST requests
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Include admin session configuration
require_once 'admin_session_config.php';

try {
    if (!isAdminLoggedIn()) {
        echo json_encode([
            'logged_in' => false,
            'message' => 'Not logged in'
        ]);
        exit;
    }
    
    // Update last activity to keep session alive
    $_SESSION['admin_last_activity'] = time();
    
    $admin = getCurrentAdmin();
    $timeout = 5400; // 1.5 hours (90 minutes)
    $remaining = $timeout - (time() - $_SESSION['admin_last_activity']);
    
    echo json_encode([
        'logged_in' => true,
        'admin' => $admin,
        'remaining_time' => max(0, $remaining),
        'timeout' => $timeout
    ]);
    
} catch (Exception $e) {
    error_log("Admin session check error: " . $e->getMessage());
    echo json_encode([
        'logged_in' => false,
        'message' => 'Session check failed'
    ]);
}
?>
