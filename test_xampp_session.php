<?php
// Test XAMPP Session Configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>XAMPP Session Configuration Test</h2>";

// Test 1: Check PHP version and session support
echo "<h3>1. PHP Version and Session Support</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Session support: " . (function_exists('session_start') ? "✅ Available" : "❌ Not available") . "<br>";

// Test 2: Check session configuration
echo "<h3>2. Session Configuration</h3>";
echo "session.save_handler: " . ini_get('session.save_handler') . "<br>";
echo "session.save_path: " . ini_get('session.save_path') . "<br>";
echo "session.use_cookies: " . ini_get('session.use_cookies') . "<br>";
echo "session.cookie_lifetime: " . ini_get('session.cookie_lifetime') . "<br>";
echo "session.use_strict_mode: " . ini_get('session.use_strict_mode') . "<br>";
echo "session.cookie_httponly: " . ini_get('session.cookie_httponly') . "<br>";
echo "session.cookie_samesite: " . ini_get('session.cookie_samesite') . "<br>";

// Test 3: Check session directory
echo "<h3>3. Session Directory Check</h3>";
$session_path = ini_get('session.save_path');
if (empty($session_path)) {
    $session_path = sys_get_temp_dir();
}
echo "Session path: $session_path<br>";
echo "Directory exists: " . (is_dir($session_path) ? "✅ Yes" : "❌ No") . "<br>";
echo "Directory writable: " . (is_writable($session_path) ? "✅ Yes" : "❌ No") . "<br>";

// Test 4: Check if we can create session files
echo "<h3>4. Session File Creation Test</h3>";
$test_file = $session_path . '/test_session_' . time() . '.txt';
$write_result = file_put_contents($test_file, 'test');
if ($write_result !== false) {
    echo "✅ Can write to session directory<br>";
    unlink($test_file); // Clean up
} else {
    echo "❌ Cannot write to session directory<br>";
}

// Test 5: Basic session functionality
echo "<h3>5. Basic Session Functionality</h3>";
try {
    session_start();
    echo "✅ Session started successfully<br>";
    echo "Session name: " . session_name() . "<br>";
    echo "Session ID: " . session_id() . "<br>";
    
    // Set test data
    $_SESSION['test_data'] = 'test_value_' . time();
    echo "✅ Session data set<br>";
    
    // Write session
    session_write_close();
    echo "✅ Session written and closed<br>";
    
    // Start session again
    session_start();
    echo "✅ Session restarted<br>";
    
    // Check if data persists
    if (isset($_SESSION['test_data'])) {
        echo "✅ Session data persists: " . $_SESSION['test_data'] . "<br>";
    } else {
        echo "❌ Session data lost<br>";
    }
    
    session_destroy();
    echo "✅ Session destroyed<br>";
    
} catch (Exception $e) {
    echo "❌ Session error: " . $e->getMessage() . "<br>";
}

// Test 6: Check for common XAMPP issues
echo "<h3>6. XAMPP-Specific Checks</h3>";

// Check if session files are being created
$session_files = glob($session_path . '/sess_*');
echo "Current session files: " . count($session_files) . "<br>";

// Check PHP error log location
$error_log = ini_get('error_log');
echo "Error log location: " . ($error_log ? $error_log : 'Not set') . "<br>";

// Check if we're running under Apache
echo "Server software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";

// Test 7: Check admin session configuration
echo "<h3>7. Admin Session Configuration Test</h3>";

// Test the admin session configuration
if (file_exists('admin_session_config.php')) {
    echo "✅ admin_session_config.php exists<br>";
    
    try {
        require_once 'admin_session_config.php';
        configureAdminSession();
        echo "✅ Admin session configured successfully<br>";
        echo "Session name: " . session_name() . "<br>";
        echo "Session ID: " . session_id() . "<br>";
        
        // Test session validation
        $_SESSION['user_id'] = 1;
        $_SESSION['user_type'] = 'admin';
        $_SESSION['admin_username'] = 'test_admin';
        $_SESSION['expires_at'] = time() + 3600;
        
        $validation_result = validateAdminSession();
        echo "Session validation: " . ($validation_result ? "✅ Pass" : "❌ Fail") . "<br>";
        
        session_destroy();
        
    } catch (Exception $e) {
        echo "❌ Admin session error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ admin_session_config.php not found<br>";
}

// Test 8: Check browser cookies
echo "<h3>8. Browser Cookie Check</h3>";
echo "HTTP_COOKIE: " . ($_SERVER['HTTP_COOKIE'] ?? 'not set') . "<br>";

// Parse cookies
$cookies = [];
if (isset($_SERVER['HTTP_COOKIE'])) {
    foreach (explode(';', $_SERVER['HTTP_COOKIE']) as $cookie) {
        $parts = explode('=', trim($cookie), 2);
        if (count($parts) == 2) {
            $cookies[$parts[0]] = $parts[1];
        }
    }
}

echo "Admin session cookie: " . ($cookies['ADMIN_NEW_SESSION'] ?? 'not found') . "<br>";

// Test 9: Recommendations
echo "<h3>9. Recommendations</h3>";

$issues_found = [];

if (!is_writable($session_path)) {
    $issues_found[] = "Session directory is not writable";
}

if (count($session_files) > 10) {
    $issues_found[] = "Many session files found - consider cleanup";
}

if (empty($cookies['ADMIN_NEW_SESSION'])) {
    $issues_found[] = "No admin session cookie found";
}

if (empty($issues_found)) {
    echo "✅ No obvious issues found<br>";
    echo "✅ Session configuration appears correct<br>";
} else {
    echo "⚠️ Potential issues found:<br>";
    foreach ($issues_found as $issue) {
        echo "- $issue<br>";
    }
}

echo "<h3>10. Next Steps</h3>";
echo "1. If all tests pass, try logging into admin panel<br>";
echo "2. Check XAMPP error logs at: C:\\New Xampp\\apache\\logs\\error.log<br>";
echo "3. Check PHP error logs<br>";
echo "4. Clear browser cookies and try again<br>";
echo "5. Try a different browser<br>";

echo "<h3>11. Test Links</h3>";
echo "<a href='admin_login_new.html' target='_blank'>Test Admin Login</a><br>";
echo "<a href='test_admin_session_fix.php' target='_blank'>Run Detailed Session Test</a><br>";
?>
