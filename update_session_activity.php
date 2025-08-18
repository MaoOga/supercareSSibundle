<?php
// Session Activity Update with Proper Session Management
require_once 'admin_session_manager.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Validate admin session
if (!$adminSession->validateSession()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Session activity updated'
]);
?>
