<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_name('SUPER_ADMIN_SESSION');
session_start();

// Log the logout
$username = $_SESSION['username'] ?? 'unknown';
$logEntry = date('Y-m-d H:i:s') . " - Super Admin Logout: $username\n";
file_put_contents('super_admin_access.log', $logEntry, FILE_APPEND | LOCK_EX);

// Destroy session
session_destroy();

// Redirect to login page
header('Location: super_admin_login.html');
exit;
?>
