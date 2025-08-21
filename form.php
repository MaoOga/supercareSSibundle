<?php
require_once 'config.php';

// Check if this is an admin context by looking for admin session
$is_admin_context = false;

// Try to detect admin session first
if (isset($_COOKIE['ADMIN_NEW_SESSION']) || isset($_COOKIE['SUPER_ADMIN_SESSION'])) {
    $is_admin_context = true;
}

// Also check if we're coming from admin pages
if (isset($_SERVER['HTTP_REFERER'])) {
    $admin_patterns = ['admin.php', 'admin_login_new.html', 'admin_patient_records.php'];
    foreach ($admin_patterns as $pattern) {
        if (strpos($_SERVER['HTTP_REFERER'], $pattern) !== false) {
            $is_admin_context = true;
            break;
        }
    }
}

if ($is_admin_context) {
    // Use admin session system
    require_once 'admin_session_manager.php';
    
    // Check if admin session is valid
    if (!$adminSession->validateSession()) {
        // Admin session invalid, redirect to admin login
        header('Location: admin_login_new.html?msg=session_expired');
        exit;
    }
    
    // Admin is logged in, include the form
    include 'form_template.html';
} else {
    // Use nurse/form session system
    require_once 'session_config.php';
    
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        // User is not logged in, redirect to login page
        header('Location: login.html');
        exit;
    }

    // Check session activity
    if (!checkSessionActivity()) {
        // Session expired, redirect to login page
        header('Location: login.html');
        exit;
    }

    // User is logged in, include the form
    include 'form_template.html';
}
?>
