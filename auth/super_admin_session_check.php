<?php
// Super Admin Session Check API
// Returns current super admin session status

header('Content-Type: application/json');

// Allow both GET and POST requests
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once 'super_admin_session_config.php';

try {
    // Check if super admin is logged in
    if (!isSuperAdminLoggedIn()) {
        echo json_encode([
            'logged_in' => false,
            'message' => 'Super admin not logged in'
        ]);
        exit;
    }
    
    // Check session timeout
    if (!checkSuperAdminSessionTimeout()) {
        echo json_encode([
            'logged_in' => false,
            'message' => 'Session expired'
        ]);
        exit;
    }
    
    // Update last activity to keep session alive
    $_SESSION['super_admin_last_activity'] = time();
    
    // Get super admin data
    $superAdmin = getCurrentSuperAdmin();
    $timeout = 5400; // 1.5 hours
    $remaining = $timeout - (time() - $_SESSION['super_admin_last_activity']);
    
    echo json_encode([
        'logged_in' => true,
        'super_admin' => $superAdmin,
        'remaining_time' => max(0, $remaining),
        'timeout' => $timeout
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'logged_in' => false,
        'message' => 'Session check failed: ' . $e->getMessage()
    ]);
}
?>
