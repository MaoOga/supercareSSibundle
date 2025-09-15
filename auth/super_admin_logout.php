<?php
// Super Admin Logout Handler
require_once 'super_admin_session_config.php';

// Clear super admin session data
clearSuperAdminSession();

// Redirect to super admin login page
header('Location: ../super admin/super_admin_login_test.html?msg=' . urlencode('You have been logged out successfully'));
exit();
?>