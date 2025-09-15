<?php
/**
 * Form Page - Protected Page
 * Serves form_template.html with session protection
 */

require_once '../auth/session_config.php';

// Protect this page - only logged in users can access
if (!isLoggedIn()) {
    header('Location: ../auth/login.html?msg=' . urlencode('Please log in to access the form'));
    exit;
}

// Get current user info for potential use in the form
$user = getCurrentUser();

// Include the form template
include 'form_template.html';
?>
