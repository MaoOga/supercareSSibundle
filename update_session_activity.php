<?php
require_once 'config.php';
require_once 'session_config.php';

header('Content-Type: application/json');

// Update session activity for nurse sessions
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'nurse') {
    // Update last activity time
    $_SESSION['last_activity'] = time();
    echo json_encode(['success' => true, 'message' => 'Session activity updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'No active nurse session']);
}
?>
