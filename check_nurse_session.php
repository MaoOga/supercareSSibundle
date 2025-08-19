<?php
require_once 'config.php';
require_once 'session_config.php';

header('Content-Type: application/json');

// Check if nurse is logged in
if (isNurseLoggedIn()) {
    $nurseInfo = getNurseInfo();
    $timeRemaining = NURSE_SESSION_TIMEOUT - (time() - $_SESSION['last_activity']);
    
    echo json_encode([
        'success' => true,
        'logged_in' => true,
        'nurse_info' => $nurseInfo,
        'time_remaining' => $timeRemaining,
        'session_active' => $timeRemaining > 0
    ]);
} else {
    echo json_encode([
        'success' => false,
        'logged_in' => false,
        'message' => 'Session expired or not logged in'
    ]);
}
?>
