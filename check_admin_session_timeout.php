<?php
require_once 'config.php';

echo "<h2>Admin Session Timeout Diagnosis</h2>";

try {
    // Check current session settings
    echo "<h3>Current Session Settings:</h3>";
    echo "<ul>";
    echo "<li><strong>PHP Session Timeout:</strong> " . ini_get('session.gc_maxlifetime') . " seconds (" . round(ini_get('session.gc_maxlifetime')/60, 1) . " minutes)</li>";
    echo "<li><strong>Session Cookie Lifetime:</strong> " . ini_get('session.cookie_lifetime') . " seconds</li>";
    echo "<li><strong>Session Use Cookies:</strong> " . (ini_get('session.use_cookies') ? 'Yes' : 'No') . "</li>";
    echo "<li><strong>Session Use Only Cookies:</strong> " . (ini_get('session.use_only_cookies') ? 'Yes' : 'No') . "</li>";
    echo "<li><strong>Session Use Strict Mode:</strong> " . (ini_get('session.use_strict_mode') ? 'Yes' : 'No') . "</li>";
    echo "</ul>";
    
    // Check AdminSessionManager settings
    echo "<h3>AdminSessionManager Settings:</h3>";
    echo "<ul>";
    echo "<li><strong>Session Name:</strong> ADMIN_NEW_SESSION</li>";
    echo "<li><strong>Session Timeout:</strong> 3600 seconds (60 minutes)</li>";
    echo "<li><strong>Session Regeneration:</strong> Every 300 seconds (5 minutes)</li>";
    echo "</ul>";
    
    // Check if admin session is currently active
    echo "<h3>Current Session Status:</h3>";
    
    // Start session to check current state
    if (session_status() === PHP_SESSION_NONE) {
        session_name('ADMIN_NEW_SESSION');
        session_start();
    }
    
    if (session_status() === PHP_SESSION_ACTIVE) {
        echo "<p style='color: green;'>✅ Session is active</p>";
        echo "<p><strong>Session Name:</strong> " . session_name() . "</p>";
        echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
        
        if (isset($_SESSION['admin_id'])) {
            echo "<p style='color: green;'>✅ Admin session data found</p>";
            echo "<ul>";
            echo "<li><strong>Admin ID:</strong> " . $_SESSION['admin_id'] . "</li>";
            echo "<li><strong>Admin Username:</strong> " . ($_SESSION['admin_username'] ?? 'Not set') . "</li>";
            echo "<li><strong>User Type:</strong> " . ($_SESSION['user_type'] ?? 'Not set') . "</li>";
            echo "<li><strong>Login Time:</strong> " . (isset($_SESSION['login_time']) ? date('Y-m-d H:i:s', $_SESSION['login_time']) : 'Not set') . "</li>";
            echo "<li><strong>Last Activity:</strong> " . (isset($_SESSION['last_activity']) ? date('Y-m-d H:i:s', $_SESSION['last_activity']) : 'Not set') . "</li>";
            echo "<li><strong>Expires At:</strong> " . (isset($_SESSION['expires_at']) ? date('Y-m-d H:i:s', $_SESSION['expires_at']) : 'Not set') . "</li>";
            echo "</ul>";
            
            // Calculate time remaining
            if (isset($_SESSION['expires_at'])) {
                $timeRemaining = $_SESSION['expires_at'] - time();
                $status = $timeRemaining > 0 ? "VALID" : "EXPIRED";
                $color = $timeRemaining > 0 ? "green" : "red";
                
                echo "<p style='color: " . $color . ";'><strong>Session Status:</strong> " . $status . "</p>";
                echo "<p><strong>Time Remaining:</strong> " . round($timeRemaining/60, 1) . " minutes</p>";
            }
        } else {
            echo "<p style='color: orange;'>⚠️ No admin session data found</p>";
            echo "<p>Session contents: " . print_r($_SESSION, true) . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Session is not active</p>";
    }
    
    // Check for potential issues
    echo "<h3>Potential Issues:</h3>";
    echo "<ul>";
    
    // Check if session timeout is too short
    $sessionTimeout = ini_get('session.gc_maxlifetime');
    if ($sessionTimeout < 3600) {
        echo "<li style='color: red;'>❌ <strong>PHP Session Timeout Too Short:</strong> " . $sessionTimeout . " seconds. Should be at least 3600 seconds (60 minutes)</li>";
    } else {
        echo "<li style='color: green;'>✅ PHP Session Timeout is adequate: " . $sessionTimeout . " seconds</li>";
    }
    
    // Check if session cookie lifetime is 0 (session cookie)
    $cookieLifetime = ini_get('session.cookie_lifetime');
    if ($cookieLifetime == 0) {
        echo "<li style='color: orange;'>⚠️ <strong>Session Cookie Lifetime:</strong> 0 (session cookie - expires when browser closes)</li>";
    } else {
        echo "<li style='color: green;'>✅ Session Cookie Lifetime: " . $cookieLifetime . " seconds</li>";
    }
    
    // Check if session regeneration might be causing issues
    echo "<li style='color: blue;'>ℹ️ <strong>Session Regeneration:</strong> Every 5 minutes (this is normal)</li>";
    
    echo "</ul>";
    
    // Recommendations
    echo "<h3>Recommendations:</h3>";
    echo "<ul>";
    echo "<li><strong>Increase PHP Session Timeout:</strong> Set session.gc_maxlifetime to 3600 or higher in php.ini</li>";
    echo "<li><strong>Check Browser Settings:</strong> Ensure browser is not clearing cookies automatically</li>";
    echo "<li><strong>Check for Multiple Tabs:</strong> Having multiple admin tabs open might cause session conflicts</li>";
    echo "<li><strong>Check Server Time:</strong> Ensure server time is correct</li>";
    echo "</ul>";
    
    // Show current server time
    echo "<h3>Current Server Time:</h3>";
    echo "<p><strong>Server Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
    echo "<p><strong>Timezone:</strong> " . date_default_timezone_get() . "</p>";
    echo "<p><strong>Unix Timestamp:</strong> " . time() . "</p>";
    
    // Test session extension
    echo "<h3>Test Session Extension:</h3>";
    if (isset($_SESSION['admin_id'])) {
        echo "<p><a href='?extend_session=1'>Extend Current Session</a></p>";
        
        if (isset($_GET['extend_session'])) {
            // Extend session by updating expiry time
            $_SESSION['last_activity'] = time();
            $_SESSION['expires_at'] = time() + 3600; // 1 hour from now
            
            echo "<p style='color: green;'>✅ Session extended! New expiry: " . date('Y-m-d H:i:s', $_SESSION['expires_at']) . "</p>";
        }
    } else {
        echo "<p>No active admin session to extend.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
