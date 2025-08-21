<?php
// Session configuration to ensure sessions work properly
// This file is used by nurse and form systems, NOT admin system

// Check if we're in an admin context to avoid conflicts
$is_admin_context = false;

// Check for admin session cookies first - but only if they're actually active
if (isset($_COOKIE['ADMIN_NEW_SESSION']) || isset($_COOKIE['SUPER_ADMIN_SESSION'])) {
    // Only treat as admin context if we're actually on admin pages
    if (isset($_SERVER['REQUEST_URI'])) {
        $admin_patterns = ['admin_login_new.php', 'admin.php', 'admin_logout_new.php', 'admin_patient_records.php', 'audit_log.php', 'super_admin_dashboard_simple.html'];
        $is_admin_page = false;
        foreach ($admin_patterns as $pattern) {
            if (strpos($_SERVER['REQUEST_URI'], $pattern) !== false) {
                $is_admin_page = true;
                break;
            }
        }
        if ($is_admin_page) {
            $is_admin_context = true;
        }
    }
}

// Check URL patterns
if (isset($_SERVER['REQUEST_URI'])) {
    $admin_patterns = ['admin_login_new.php', 'admin.php', 'admin_logout_new.php', 'admin_patient_records.php', 'audit_log.php'];
    foreach ($admin_patterns as $pattern) {
        if (strpos($_SERVER['REQUEST_URI'], $pattern) !== false) {
            $is_admin_context = true;
            break;
        }
    }
}

// Check HTTP referer
if (isset($_SERVER['HTTP_REFERER'])) {
    $admin_patterns = ['admin.php', 'admin_login_new.html', 'admin_patient_records.php'];
    foreach ($admin_patterns as $pattern) {
        if (strpos($_SERVER['HTTP_REFERER'], $pattern) !== false) {
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

// 30 minutes timeout for nurse sessions
define('NURSE_SESSION_TIMEOUT', 1800); // 30 minutes in seconds
define('SESSION_TIMEOUT', 1800); // 30 minutes for other sessions (increased from 10 minutes)

// Function to check and update session activity
function checkSessionActivity() {
    global $is_admin_context;
    
    // If we have a valid nurse session, check it regardless of admin context
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'nurse') {
        $currentTime = time();
        
        // If this is the first activity, set the initial activity time
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = $currentTime;
            return true;
        }
        
        // Use nurse timeout
        $timeout = NURSE_SESSION_TIMEOUT;
        
        // Check if session has expired due to inactivity
        if (($currentTime - $_SESSION['last_activity']) > $timeout) {
            // Session expired, destroy it
            session_destroy();
            return false;
        }
        
        // Update last activity time
        $_SESSION['last_activity'] = $currentTime;
        return true;
    }
    
    // Only check admin context if no valid nurse session exists
    if ($is_admin_context) {
        return true;
    }
    
    $currentTime = time();
    
    // If this is the first activity, set the initial activity time
    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = $currentTime;
        return true;
    }
    
    // Determine timeout based on user type
    $timeout = SESSION_TIMEOUT;
    
    // Check if session has expired due to inactivity
    if (($currentTime - $_SESSION['last_activity']) > $timeout) {
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
        // Don't check session activity on every request - let the client-side handle it
        // This prevents immediate session expiration
        if (isset($_SESSION['last_activity'])) {
            // Only update last activity, don't check for expiration here
            $_SESSION['last_activity'] = time();
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
    
    // If we have a valid nurse session, update activity regardless of admin context
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'nurse') {
        $_SESSION['last_activity'] = time();
        return ['success' => true, 'message' => 'Activity updated'];
    }
    
    // Only check admin context if no valid nurse session exists
    if ($is_admin_context) {
        return ['success' => false, 'message' => 'Admin context detected'];
    }
    
    return ['success' => false, 'message' => 'No active session'];
}

// Function to check if nurse is logged in
function isNurseLoggedIn() {
    global $is_admin_context;
    
    // If we have valid nurse session data, allow it regardless of admin context
    // This prevents conflicts when testing or when both systems are used
    if (isset($_SESSION['user_type']) && 
        $_SESSION['user_type'] === 'nurse' && 
        isset($_SESSION['nurse_id']) && 
        isset($_SESSION['logged_in']) && 
        $_SESSION['logged_in'] === true) {
        return true;
    }
    
    // Only check admin context if no valid nurse session exists
    if ($is_admin_context) {
        return false;
    }
    
    return false;
}

// Function to get nurse info from session
function getNurseInfo() {
    if (isNurseLoggedIn()) {
        return $_SESSION['nurse_info'] ?? null;
    }
    return null;
}
?>
