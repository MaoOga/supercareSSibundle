<?php
/**
 * Admin Logout Handler with Session Management
 */

// Include admin session configuration
require_once 'admin_session_config.php';

// Clear admin session
clearAdminSession();

// Redirect to admin login page
header('Location: ../admin/admin_login_new.html?msg=' . urlencode('You have been logged out successfully'));
exit();
?>
