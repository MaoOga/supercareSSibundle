<?php
require_once '../database/config.php';

echo "<h2>Fix Admin Session Timeout</h2>";

try {
    // Check current settings
    echo "<h3>Current Settings:</h3>";
    echo "<ul>";
    echo "<li><strong>PHP Session Timeout:</strong> " . ini_get('session.gc_maxlifetime') . " seconds (" . round(ini_get('session.gc_maxlifetime')/60, 1) . " minutes)</li>";
    echo "<li><strong>AdminSessionManager Timeout:</strong> 3600 seconds (60 minutes)</li>";
    echo "</ul>";
    
    // Fix the session timeout
    if (isset($_GET['fix_timeout'])) {
        // Set PHP session timeout to match AdminSessionManager
        ini_set('session.gc_maxlifetime', 3600);
        
        echo "<h3>✅ Session Timeout Fixed!</h3>";
        echo "<p><strong>New PHP Session Timeout:</strong> " . ini_get('session.gc_maxlifetime') . " seconds (" . round(ini_get('session.gc_maxlifetime')/60, 1) . " minutes)</p>";
        echo "<p>Your admin session will now last 60 minutes instead of 24 minutes.</p>";
        
        // Test current session
        if (session_status() === PHP_SESSION_NONE) {
            session_name('ADMIN_NEW_SESSION');
            session_start();
        }
        
        if (isset($_SESSION['admin_id'])) {
            // Extend current session
            $_SESSION['last_activity'] = time();
            $_SESSION['expires_at'] = time() + 3600;
            
            echo "<p style='color: green;'>✅ Current session extended to 60 minutes!</p>";
            echo "<p><strong>New Expiry:</strong> " . date('Y-m-d H:i:s', $_SESSION['expires_at']) . "</p>";
        }
        
    } else {
        echo "<h3>Issue Found:</h3>";
        echo "<p style='color: red;'>❌ PHP Session Timeout (24 minutes) is shorter than AdminSessionManager Timeout (60 minutes)</p>";
        echo "<p>This causes sessions to expire after 24 minutes even though the AdminSessionManager is set to 60 minutes.</p>";
        
        echo "<h3>Solution:</h3>";
        echo "<p><a href='?fix_timeout=1' style='background-color: #1aaf51; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Fix Session Timeout</a></p>";
    }
    
    // Show additional recommendations
    echo "<h3>Additional Recommendations:</h3>";
    echo "<ul>";
    echo "<li><strong>Permanent Fix:</strong> Edit your php.ini file and set <code>session.gc_maxlifetime = 3600</code></li>";
    echo "<li><strong>XAMPP php.ini Location:</strong> C:\\New Xampp\\php\\php.ini</li>";
    echo "<li><strong>Browser Settings:</strong> Ensure cookies are enabled and not being cleared automatically</li>";
    echo "<li><strong>Multiple Tabs:</strong> Avoid having multiple admin tabs open simultaneously</li>";
    echo "</ul>";
    
    // Show current session status
    echo "<h3>Current Session Status:</h3>";
    if (session_status() === PHP_SESSION_NONE) {
        session_name('ADMIN_NEW_SESSION');
        session_start();
    }
    
    if (isset($_SESSION['admin_id'])) {
        echo "<p style='color: green;'>✅ Admin session is active</p>";
        echo "<ul>";
        echo "<li><strong>Admin:</strong> " . ($_SESSION['admin_username'] ?? 'Unknown') . "</li>";
        echo "<li><strong>Login Time:</strong> " . (isset($_SESSION['login_time']) ? date('Y-m-d H:i:s', $_SESSION['login_time']) : 'Unknown') . "</li>";
        echo "<li><strong>Last Activity:</strong> " . (isset($_SESSION['last_activity']) ? date('Y-m-d H:i:s', $_SESSION['last_activity']) : 'Unknown') . "</li>";
        echo "<li><strong>Expires At:</strong> " . (isset($_SESSION['expires_at']) ? date('Y-m-d H:i:s', $_SESSION['expires_at']) : 'Unknown') . "</li>";
        echo "</ul>";
        
        if (isset($_SESSION['expires_at'])) {
            $timeRemaining = $_SESSION['expires_at'] - time();
            echo "<p><strong>Time Remaining:</strong> " . round($timeRemaining/60, 1) . " minutes</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ No active admin session found</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
