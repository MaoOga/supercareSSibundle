<?php
session_name('SUPER_ADMIN_SESSION');
session_start();
header('Content-Type: application/json');

// Check if user is logged in as super admin
if (!isset($_SESSION['super_admin_logged_in']) || $_SESSION['super_admin_logged_in'] !== true) {
    echo json_encode([
        'success' => false,
        'is_super_admin' => false,
        'message' => 'Not authenticated as super admin'
    ]);
    exit();
}

// Check session timeout (30 minutes)
$session_timeout = 30 * 60; // 30 minutes in seconds
$current_time = time();

if (isset($_SESSION['last_activity']) && ($current_time - $_SESSION['last_activity']) > $session_timeout) {
    // Session has expired
    session_destroy();
    echo json_encode([
        'success' => false,
        'is_super_admin' => false,
        'message' => 'Session expired due to inactivity',
        'expired' => true
    ]);
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = $current_time;

// Calculate remaining session time
$remaining_time = $session_timeout - ($current_time - $_SESSION['last_activity']);

echo json_encode([
    'success' => true,
    'is_super_admin' => true,
    'user_id' => $_SESSION['super_admin_id'] ?? null,
    'username' => $_SESSION['super_admin_username'] ?? null,
    'remaining_time' => $remaining_time,
    'session_timeout' => $session_timeout
]);
?>
