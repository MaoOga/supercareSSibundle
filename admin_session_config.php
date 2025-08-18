<?php
/**
 * Admin Session Configuration
 * Centralized session configuration for admin system
 */

// Configure session parameters
function configureAdminSession() {
    // Only configure if session hasn't started yet
    if (session_status() === PHP_SESSION_NONE) {
        // Set session name
        session_name('ADMIN_NEW_SESSION');
        
        // Set secure cookie parameters
        session_set_cookie_params([
            'lifetime' => 0,                    // Session cookie (expires when browser closes)
            'path' => '/supercareSSibundle/',   // Available only in this directory
            'domain' => '',                     // Current domain
            'secure' => false,                  // Set to true if using HTTPS
            'httponly' => true,                 // Prevent JavaScript access
            'samesite' => 'Lax'                // CSRF protection
        ]);
        
        // Start session
        session_start();
    } else {
        // Session already started, just ensure it's the right name
        if (session_name() !== 'ADMIN_NEW_SESSION') {
            error_log("Warning: Session name mismatch. Expected: ADMIN_NEW_SESSION, Got: " . session_name());
        }
    }
}

// Validate admin session
function validateAdminSession() {
    // Check if session is active
    if (session_status() !== PHP_SESSION_ACTIVE) {
        error_log("Admin session validation: Session not active");
        return false;
    }
    
    // Check if user type is set and valid
    if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'super_admin')) {
        error_log("Admin session validation: Invalid user type - " . ($_SESSION['user_type'] ?? 'not set'));
        return false;
    }
    
    // Check if user ID exists
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        error_log("Admin session validation: Invalid user ID - " . ($_SESSION['user_id'] ?? 'not set'));
        return false;
    }
    
    // Check session timeout
    if (isset($_SESSION['expires_at']) && time() > $_SESSION['expires_at']) {
        error_log("Admin session validation: Session expired");
        return false;
    }
    
    // Update last activity
    $_SESSION['last_activity'] = time();
    
    return true;
}

// Get admin user info
function getAdminUserInfo() {
    if (!validateAdminSession()) {
        return null;
    }
    
    return [
        'user_id' => $_SESSION['user_id'],
        'admin_username' => $_SESSION['admin_username'] ?? '',
        'admin_name' => $_SESSION['admin_name'] ?? '',
        'admin_email' => $_SESSION['admin_email'] ?? '',
        'user_type' => $_SESSION['user_type'],
        'login_time' => $_SESSION['login_time'] ?? 0,
        'expires_at' => $_SESSION['expires_at'] ?? 0
    ];
}

// Destroy admin session
function destroyAdminSession() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        // Clear session data
        $_SESSION = array();
        
        // Destroy session
        session_destroy();
        
        // Delete session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
    }
}

// Redirect to admin login
function redirectToAdminLogin($reason = '') {
    $url = 'admin_login_new.html';
    if (!empty($reason)) {
        $url .= '?msg=' . urlencode($reason);
    }
    header('Location: ' . $url);
    exit();
}
?>
