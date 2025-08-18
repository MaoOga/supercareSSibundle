<?php
// Session configuration to ensure sessions work properly
// This file is used by nurse and form systems, NOT admin system

// Check if we're in an admin context to avoid conflicts
$is_admin_context = false;
if (isset($_SERVER['REQUEST_URI'])) {
    $admin_patterns = ['admin_login_new.php', 'admin.php', 'admin_logout_new.php'];
    foreach ($admin_patterns as $pattern) {
        if (strpos($_SERVER['REQUEST_URI'], $pattern) !== false) {
            $is_admin_context = true;
            break;
        }
    }
}

// Only configure sessions if we're not in admin context
if (!$is_admin_context) {
    ini_set('session.cookie_lifetime', 0);
    ini_set('session.use_cookies', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    ini_set('session.use_trans_sid', 0);
    ini_set('session.cache_limiter', 'nocache');

    // Start session if not already started and not in admin context
    if (session_status() === PHP_SESSION_NONE) {
        // Set session name before starting
        session_name('SSI_BUNDLE_SESSION');
        session_start();
    }

    // Debug: Log session information
    error_log("Session config loaded (non-admin) - Session name: " . session_name() . ", Session ID: " . session_id() . ", Session status: " . session_status());
} else {
    // In admin context, don't interfere with admin session management
    error_log("Session config: Admin context detected, skipping session configuration");
}

define('SESSION_TIMEOUT', 600);

// Function to check and update session activity
function checkSessionActivity() {
    global $is_admin_context;
    
    // Don't interfere with admin sessions
    if ($is_admin_context) {
        return true;
    }
    
    $currentTime = time();
    
    // If this is the first activity, set the initial activity time
    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = $currentTime;
        return true;
    }
    
    // Check if session has expired due to inactivity
    if (($currentTime - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
        // Session expired, destroy it
        session_destroy();
        return false;
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = $currentTime;
    return true;
}

// Function to force session timeout (for logout)
function forceSessionTimeout() {
    global $is_admin_context;
    
    // Don't interfere with admin sessions
    if ($is_admin_context) {
        return;
    }
    
    session_destroy();
    // Clear any remember me cookies
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
}

// Check session activity on every request (only for non-admin contexts)
if (!$is_admin_context && isset($_SESSION['user_type'])) {
    // Only check session activity if we're not already checking expires_at
    if (!isset($_SESSION['expires_at'])) {
        if (!checkSessionActivity()) {
            // Session expired, redirect to appropriate login
            if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'nurse') {
                header('Location: nurse_login.php?msg=session_expired');
            } else {
                header('Location: index.html?msg=session_expired');
            }
            exit();
        }
    } else {
        // Use the expires_at timeout instead
        if (time() > $_SESSION['expires_at']) {
            session_destroy();
            if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'nurse') {
                header('Location: nurse_login.php?msg=session_expired');
            } else {
                header('Location: index.html?msg=session_expired');
            }
            exit();
        }
        // Update last activity for sessions
        $_SESSION['last_activity'] = time();
    }
} else if (!$is_admin_context) {
    // No user_type set, but don't redirect immediately
    // This allows the login process to complete
    error_log("Session config: No user_type found in session (non-admin context)");
}

// Function to update session activity via AJAX
function updateSessionActivity() {
    global $is_admin_context;
    
    // Don't interfere with admin sessions
    if ($is_admin_context) {
        return ['success' => false, 'message' => 'Admin context detected'];
    }
    
    if (isset($_SESSION['user_type'])) {
        $_SESSION['last_activity'] = time();
        return ['success' => true, 'message' => 'Activity updated'];
    }
    return ['success' => false, 'message' => 'No active session'];
}
?>
