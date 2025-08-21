<?php
/**
 * Test Session Debug - Identify why sessions are expiring immediately
 */

echo "<h2>Session Debug Test</h2>";

// Test 1: Check session configuration
echo "<h3>1. Session Configuration Check</h3>";

require_once 'config.php';
require_once 'session_config.php';

echo "Session name: " . session_name() . "<br>";
echo "Session ID: " . session_id() . "<br>";
echo "Session status: " . session_status() . "<br>";
echo "NURSE_SESSION_TIMEOUT: " . NURSE_SESSION_TIMEOUT . " seconds (" . (NURSE_SESSION_TIMEOUT/60) . " minutes)<br>";
echo "SESSION_TIMEOUT: " . SESSION_TIMEOUT . " seconds (" . (SESSION_TIMEOUT/60) . " minutes)<br>";

// Test 2: Check current session data
echo "<h3>2. Current Session Data</h3>";

if (session_status() === PHP_SESSION_ACTIVE) {
    echo "Session is active<br>";
    echo "Session data:<br>";
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
    
    if (isset($_SESSION['last_activity'])) {
        $timeSinceActivity = time() - $_SESSION['last_activity'];
        echo "Time since last activity: {$timeSinceActivity} seconds<br>";
        
        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'nurse') {
            $timeout = NURSE_SESSION_TIMEOUT;
        } else {
            $timeout = SESSION_TIMEOUT;
        }
        
        echo "Timeout for this user type: {$timeout} seconds<br>";
        echo "Session expires in: " . ($timeout - $timeSinceActivity) . " seconds<br>";
        
        if ($timeSinceActivity > $timeout) {
            echo "❌ SESSION HAS EXPIRED!<br>";
        } else {
            echo "✅ Session is still valid<br>";
        }
    } else {
        echo "❌ No last_activity found in session<br>";
    }
} else {
    echo "❌ No active session<br>";
}

// Test 3: Check session activity function
echo "<h3>3. Session Activity Function Test</h3>";

$sessionValid = checkSessionActivity();
echo "checkSessionActivity() result: " . ($sessionValid ? "✅ Valid" : "❌ Invalid") . "<br>";

// Test 4: Check if we're in admin context
echo "<h3>4. Admin Context Check</h3>";

$is_admin_context = false;

// Check for admin session cookies
if (isset($_COOKIE['ADMIN_NEW_SESSION']) || isset($_COOKIE['SUPER_ADMIN_SESSION'])) {
    $is_admin_context = true;
    echo "Admin context detected via cookies<br>";
}

// Check URL patterns
if (isset($_SERVER['REQUEST_URI'])) {
    $admin_patterns = ['admin_login_new.php', 'admin.php', 'admin_logout_new.php', 'admin_patient_records.php', 'audit_log.php'];
    foreach ($admin_patterns as $pattern) {
        if (strpos($_SERVER['REQUEST_URI'], $pattern) !== false) {
            $is_admin_context = true;
            echo "Admin context detected via URL pattern: {$pattern}<br>";
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
            echo "Admin context detected via HTTP referer: {$pattern}<br>";
            break;
        }
    }
}

echo "Is admin context: " . ($is_admin_context ? "Yes" : "No") . "<br>";

// Test 5: Check session cookies
echo "<h3>5. Session Cookies Check</h3>";

echo "All cookies:<br>";
echo "<pre>" . print_r($_COOKIE, true) . "</pre>";

// Test 6: Check if session is being destroyed
echo "<h3>6. Session Destruction Check</h3>";

if (isset($_SESSION['user_type'])) {
    echo "User type: " . $_SESSION['user_type'] . "<br>";
    
    if ($_SESSION['user_type'] === 'nurse') {
        echo "Nurse session detected<br>";
        
        // Check if session is being destroyed by session_config.php
        if (!$is_admin_context && isset($_SESSION['user_type'])) {
            echo "Session config will check activity<br>";
            
            if (!isset($_SESSION['last_activity'])) {
                echo "❌ No last_activity - session will be considered expired<br>";
            } else {
                $timeSinceActivity = time() - $_SESSION['last_activity'];
                $timeout = NURSE_SESSION_TIMEOUT;
                
                if ($timeSinceActivity > $timeout) {
                    echo "❌ Session will be destroyed due to timeout<br>";
                } else {
                    echo "✅ Session should remain valid<br>";
                }
            }
        }
    }
}

// Test 7: Check update_session_activity.php
echo "<h3>7. Update Session Activity Test</h3>";

$updateUrl = 'update_session_activity.php';
echo "Testing: {$updateUrl}<br>";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'Cookie: ' . http_build_query($_COOKIE, '', '; ')
    ]
]);

$response = file_get_contents($updateUrl, false, $context);
echo "Response: " . $response . "<br>";

// Test 8: Check check_nurse_session.php
echo "<h3>8. Check Nurse Session Test</h3>";

$checkUrl = 'check_nurse_session.php';
echo "Testing: {$checkUrl}<br>";

$response = file_get_contents($checkUrl, false, $context);
echo "Response: " . $response . "<br>";

echo "<h3>9. Recommendations</h3>";

if (SESSION_TIMEOUT === 600) {
    echo "⚠️ SESSION_TIMEOUT is very short (10 minutes). Consider increasing it.<br>";
}

if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'nurse' && !isset($_SESSION['last_activity'])) {
    echo "❌ Nurse session missing last_activity timestamp<br>";
    echo "This will cause immediate session expiration<br>";
}

echo "<h3>10. Quick Fixes</h3>";
echo "1. Increase SESSION_TIMEOUT in session_config.php<br>";
echo "2. Ensure last_activity is set during login<br>";
echo "3. Check for session conflicts between admin and nurse systems<br>";
echo "4. Verify session cookies are being set correctly<br>";
?>
