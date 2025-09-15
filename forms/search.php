<?php
/**
 * Search Page - Protected Page
 * Serves search.html with session protection
 */

require_once '../auth/session_config.php';

// Protect this page - only logged in users can access
if (!isLoggedIn()) {
    header('Location: ../auth/login.html?msg=' . urlencode('Please log in to access the search page'));
    exit;
}

// Get current user info for potential use in the HTML
$user = getCurrentUser();

// Serve the search.html content
$search_html = file_get_contents('search.html');

// Set content type to HTML
header('Content-Type: text/html; charset=UTF-8');

// Output the search.html content
echo $search_html;
?>

