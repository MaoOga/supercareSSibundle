<?php
/**
 * Simple Session Configuration for SSI Bundle System
 * Works with existing login pages
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
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && 
           isset($_SESSION['user_type']) && 
           isset($_SESSION['logged_in']) && 
           $_SESSION['logged_in'] === true;
}

/**
 * Get current user role
 * @return string|null
 */
function getCurrentUserRole() {
    if (!isLoggedIn()) {
        return null;
    }
    return $_SESSION['user_type'];
}

/**
 * Check if current user has specific role
 * @param string $role
 * @return bool
 */
function hasRole($role) {
    return getCurrentUserRole() === $role;
}

/**
 * Get current user data
 * @return array|null
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'type' => $_SESSION['user_type'],
        'username' => $_SESSION['username'] ?? '',
        'name' => $_SESSION['name'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'login_time' => $_SESSION['login_time'] ?? time()
    ];
}

/**
 * Redirect to login page with message
 * @param string $message
 */
function redirectToLogin($message = '') {
    $url = '../auth/login.html';
    if ($message) {
        $url .= '?msg=' . urlencode($message);
    }
    header('Location: ' . $url);
    exit;
}

/**
 * Redirect based on user role
 */
function redirectByRole() {
    if (!isLoggedIn()) {
        redirectToLogin('Please log in');
        return;
    }
    
    $role = getCurrentUserRole();
    switch ($role) {
        case 'nurse':
            header('Location: ../pages/index.php');
            break;
        case 'admin':
            header('Location: ../admin/admin.php');
            break;
        case 'super_admin':
            header('Location: ../super admin/super_admin_dashboard_simple.html');
            break;
        default:
            redirectToLogin('Invalid user role');
    }
    exit;
}
?>
