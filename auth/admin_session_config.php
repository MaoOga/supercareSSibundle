<?php
/**
 * Admin Session Configuration for SSI Bundle System
 * Separate session management for admin users
 */

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    // Configure session security
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
    
    // Set session timeout (1.5 hours)
    ini_set('session.gc_maxlifetime', 5400);
    ini_set('session.cookie_lifetime', 5400);
    
    // Start session
    session_start();
}

/**
 * Check if admin is logged in
 * @return bool
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && 
           isset($_SESSION['admin_type']) && 
           isset($_SESSION['admin_logged_in']) && 
           $_SESSION['admin_logged_in'] === true;
}

/**
 * Get current admin role
 * @return string|null
 */
function getCurrentAdminRole() {
    if (!isAdminLoggedIn()) {
        return null;
    }
    return $_SESSION['admin_type'] ?? 'admin';
}

/**
 * Check if current admin has specific role
 * @param string $role
 * @return bool
 */
function hasAdminRole($role) {
    return getCurrentAdminRole() === $role;
}

/**
 * Get current admin data
 * @return array|null
 */
function getCurrentAdmin() {
    if (!isAdminLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['admin_id'],
        'type' => $_SESSION['admin_type'],
        'username' => $_SESSION['admin_username'] ?? '',
        'email' => $_SESSION['admin_email'] ?? '',
        'login_time' => $_SESSION['admin_login_time'] ?? time(),
        'last_activity' => $_SESSION['last_activity'] ?? time()
    ];
}

/**
 * Redirect to admin login page with message
 * @param string $message
 */
function redirectToAdminLogin($message = '') {
    $url = '../admin/admin_login_new.html';
    if ($message) {
        $url .= '?msg=' . urlencode($message);
    }
    header('Location: ' . $url);
    exit;
}

/**
 * Redirect based on admin role
 */
function redirectByAdminRole() {
    if (!isAdminLoggedIn()) {
        redirectToAdminLogin('Please log in');
        return;
    }
    
    $role = getCurrentAdminRole();
    switch ($role) {
        case 'admin':
            header('Location: ../admin/admin.php');
            break;
        case 'super_admin':
            header('Location: ../super admin/super_admin_dashboard_simple.html');
            break;
        default:
            redirectToAdminLogin('Invalid admin role');
    }
    exit;
}

/**
 * Set admin session data
 * @param array $adminData
 */
function setAdminSession($adminData) {
    $_SESSION['admin_id'] = $adminData['id'];
    $_SESSION['admin_type'] = $adminData['type'] ?? 'admin';
    $_SESSION['admin_username'] = $adminData['username'] ?? '';
    $_SESSION['admin_email'] = $adminData['email'] ?? '';
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_login_time'] = time();
    $_SESSION['admin_last_activity'] = time();
    
    // Regenerate session ID for security
    session_regenerate_id(true);
}

/**
 * Clear admin session data
 */
function clearAdminSession() {
    // Clear all admin session variables
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_type']);
    unset($_SESSION['admin_username']);
    unset($_SESSION['admin_email']);
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_login_time']);
    unset($_SESSION['admin_last_activity']);
    
    // Keep other session data (nurse, super admin) intact
    // Only clear admin specific data
}

/**
 * Update admin activity timestamp
 */
function updateAdminActivity() {
    if (isAdminLoggedIn()) {
        $_SESSION['admin_last_activity'] = time();
    }
}
?>
