<?php
/**
 * Admin Logout Handler with Proper Session Management
 */

// Include session manager
require_once 'admin_session_manager.php';

// Destroy the session (this will also log the logout)
$adminSession->destroySession();

// Redirect to login page
header('Location: admin_login_new.html?msg=logout_success');
exit();
?>
