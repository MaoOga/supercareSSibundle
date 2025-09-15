<?php
// Super Admin Session Configuration
// Single session per browser with super admin role support

if (session_status() === PHP_SESSION_NONE) {
    // Configure session security settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
    
    // Set session timeout (1.5 hours)
    ini_set('session.gc_maxlifetime', 5400);
    ini_set('session.cookie_lifetime', 5400);
    
    session_start();
}

/**
 * Check if super admin is logged in
 */
function isSuperAdminLoggedIn() {
    return isset($_SESSION['super_admin_id']) && 
           isset($_SESSION['super_admin_username']) && 
           isset($_SESSION['super_admin_type']) &&
           $_SESSION['super_admin_type'] === 'super_admin';
}

/**
 * Get current super admin data
 */
function getCurrentSuperAdmin() {
    if (!isSuperAdminLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['super_admin_id'],
        'type' => $_SESSION['super_admin_type'],
        'username' => $_SESSION['super_admin_username'],
        'name' => $_SESSION['super_admin_name'] ?? '',
        'email' => $_SESSION['super_admin_email'] ?? ''
    ];
}

/**
 * Set super admin session data
 */
function setSuperAdminSession($superAdminData) {
    $_SESSION['super_admin_id'] = $superAdminData['id'];
    $_SESSION['super_admin_type'] = 'super_admin';
    $_SESSION['super_admin_username'] = $superAdminData['username'];
    $_SESSION['super_admin_name'] = $superAdminData['name'] ?? '';
    $_SESSION['super_admin_email'] = $superAdminData['email'] ?? '';
    $_SESSION['super_admin_last_activity'] = time();
    
    // Regenerate session ID for security
    session_regenerate_id(true);
}

/**
 * Clear super admin session data
 */
function clearSuperAdminSession() {
    unset($_SESSION['super_admin_id']);
    unset($_SESSION['super_admin_type']);
    unset($_SESSION['super_admin_username']);
    unset($_SESSION['super_admin_name']);
    unset($_SESSION['super_admin_email']);
    unset($_SESSION['super_admin_last_activity']);
    
    // Keep other session data (nurse, admin) intact
    // Only clear super admin specific data
}

/**
 * Redirect to super admin login page
 */
function redirectToSuperAdminLogin($message = '') {
    $url = '../super admin/super_admin_login_test.html';
    if ($message) {
        $url .= '?msg=' . urlencode($message);
    }
    header('Location: ' . $url);
    exit();
}

/**
 * Redirect based on super admin role (for future role expansion)
 */
function redirectBySuperAdminRole() {
    if (isSuperAdminLoggedIn()) {
        $superAdmin = getCurrentSuperAdmin();
        // For now, all super admins go to dashboard
        // Can be expanded for different super admin types
        header('Location: ../super admin/super_admin_dashboard_simple.html');
        exit();
    }
}

/**
 * Check session timeout and activity
 */
function checkSuperAdminSessionTimeout() {
    if (!isSuperAdminLoggedIn()) {
        return false;
    }
    
    $timeout = 5400; // 1.5 hours
    $lastActivity = $_SESSION['super_admin_last_activity'] ?? time();
    
    if ((time() - $lastActivity) > $timeout) {
        clearSuperAdminSession();
        return false;
    }
    
    // Update last activity
    $_SESSION['super_admin_last_activity'] = time();
    return true;
}
?>
