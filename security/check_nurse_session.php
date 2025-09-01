<?php
require_once '../database/config.php';
require_once '../auth/session_config.php';

header('Content-Type: application/json');

// Check if user is logged in
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// Check session activity
$session_valid = checkSessionActivity();

$response = [
    'success' => $logged_in && $session_valid,
    'logged_in' => $logged_in,
    'session_valid' => $session_valid,
    'user_type' => $_SESSION['user_type'] ?? null,
    'nurse_id' => $_SESSION['nurse_id'] ?? null
];

echo json_encode($response);
?>
