<?php
/**
 * Simple Session Check API
 * Returns current session status for AJAX calls
 */

require_once 'session_config.php';

// Set content type for JSON response
header('Content-Type: application/json');

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    if (!isLoggedIn()) {
        echo json_encode([
            'logged_in' => false,
            'message' => 'Not logged in'
        ]);
        exit;
    }
    
    // Update last activity to keep session alive
    $_SESSION['last_activity'] = time();
    
     $user = getCurrentUser();
     $timeout = 5400; // 1.5 hours (90 minutes)
     $remaining = $timeout - (time() - $_SESSION['last_activity']);
    
    echo json_encode([
        'logged_in' => true,
        'user' => $user,
        'remaining_time' => max(0, $remaining),
        'timeout' => $timeout
    ]);
    
} catch (Exception $e) {
    error_log("Session check error: " . $e->getMessage());
    echo json_encode([
        'logged_in' => false,
        'message' => 'Session check failed'
    ]);
}
?>
