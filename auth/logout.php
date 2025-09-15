<?php
/**
 * Logout Handler - Destroys session and redirects to login
 */
require_once 'session_config.php';

// Destroy session if user is logged in
if (isLoggedIn()) {
    // Clear session data
    $_SESSION = array();
    
    // Delete session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy session
    session_destroy();
}

// Redirect to login page
header('Location: login.html?msg=' . urlencode('Logged out successfully'));
exit;
?>
