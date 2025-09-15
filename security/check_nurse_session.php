<?php
require_once '../database/config.php';

header('Content-Type: application/json');

// Session management removed - no authentication required
$response = [
    'success' => true,
    'logged_in' => true,
    'session_valid' => true,
    'user_type' => 'nurse',
    'nurse_id' => 'SYSTEM'
];

echo json_encode($response);
?>
