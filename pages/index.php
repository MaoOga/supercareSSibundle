<?php
/**
 * Nurse Portal - Protected Page
 * Serves index.html with session protection
 */

require_once '../auth/session_config.php';

// Protect this page - only logged in users can access
if (!isLoggedIn()) {
    header('Location: ../auth/login.html?msg=' . urlencode('Please log in to access the nurse portal'));
    exit;
}

// Get current user info for potential use in the HTML
$user = getCurrentUser();

// Serve the index.html content
$index_html = file_get_contents('index.html');

// Set content type to HTML
header('Content-Type: text/html; charset=UTF-8');

// Output the index.html content
echo $index_html;
?>

