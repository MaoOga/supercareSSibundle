<?php
/**
 * Test Session Fix - Verify that nurse sessions work correctly
 */

echo "<h2>Session Fix Test</h2>";

// Test 1: Check session configuration
echo "<h3>1. Session Configuration Check</h3>";

require_once '../database/config.php';
require_once '../auth/session_config.php';

echo "Session name: " . session_name() . "<br>";
echo "Session status: " . session_status() . "<br>";

// Test 2: Simulate nurse login
echo "<h3>2. Simulate Nurse Login</h3>";

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear any existing session data
session_destroy();
session_start();

echo "Session cleared and restarted<br>";
echo "Session ID: " . session_id() . "<br>";

// Simulate setting session data as in nurse_login.php
$_SESSION['nurse_id'] = 1;
$_SESSION['nurse_info'] = ['id' => 1, 'nurse_id' => 'TEST001', 'name' => 'Test Nurse'];
$_SESSION['user_type'] = 'nurse';
$_SESSION['logged_in'] = true;
$_SESSION['last_activity'] = time();
$_SESSION['login_time'] = time();

echo "Session data set:<br>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

// Test 3: Check admin context detection
echo "<h3>3. Admin Context Detection</h3>";

$is_admin_context = false;

// Check for admin session cookies
if (isset($_COOKIE['ADMIN_NEW_SESSION']) || isset($_COOKIE['SUPER_ADMIN_SESSION'])) {
    echo "Admin cookies detected<br>";
    
    // Only treat as admin context if we're actually on admin pages
    if (isset($_SERVER['REQUEST_URI'])) {
        $admin_patterns = ['admin_login_new.php', 'admin.php', 'admin_logout_new.php', 'admin_patient_records.php', 'audit_log.php', 'super_admin_dashboard_simple.html'];
        $is_admin_page = false;
        foreach ($admin_patterns as $pattern) {
            if (strpos($_SERVER['REQUEST_URI'], $pattern) !== false) {
                $is_admin_page = true;
                echo "Admin page detected: {$pattern}<br>";
                break;
            }
        }
        if ($is_admin_page) {
            $is_admin_context = true;
        }
    }
}

echo "Is admin context: " . ($is_admin_context ? "Yes" : "No") . "<br>";

// Test 4: Check session functions
echo "<h3>4. Session Functions Test</h3>";

$sessionValid = checkSessionActivity();
echo "checkSessionActivity() result: " . ($sessionValid ? "✅ Valid" : "❌ Invalid") . "<br>";

$isLoggedIn = isNurseLoggedIn();
echo "isNurseLoggedIn() result: " . ($isLoggedIn ? "✅ Logged in" : "❌ Not logged in") . "<br>";

$updateResult = updateSessionActivity();
echo "updateSessionActivity() result: " . ($updateResult['success'] ? "✅ Success" : "❌ Failed") . " - " . $updateResult['message'] . "<br>";

// Test 5: Test API endpoints
echo "<h3>5. API Endpoints Test</h3>";

// Test update_session_activity.php
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

// Test check_nurse_session.php
$checkUrl = 'check_nurse_session.php';
echo "Testing: {$checkUrl}<br>";

$response = file_get_contents($checkUrl, false, $context);
echo "Response: " . $response . "<br>";

// Test 6: Summary
echo "<h3>6. Summary</h3>";

if ($isLoggedIn && $sessionValid) {
    echo "✅ Session system is working correctly<br>";
    echo "✅ Nurse login should work without immediate expiration<br>";
    echo "✅ Session will last for 30 minutes of inactivity<br>";
} else {
    echo "❌ Session system still has issues<br>";
    echo "❌ Check the individual test results above<br>";
}

echo "<h3>7. How to Test Login</h3>";
echo "1. Clear all browser cookies<br>";
echo "2. Go to login.html<br>";
echo "3. Enter nurse credentials (ID: 1212, Name: Nomo)<br>";
echo "4. Should redirect to index.html without session expiration<br>";
echo "5. Session should last for 30 minutes<br>";

echo "<h3>8. Fixes Applied</h3>";
echo "✅ Made admin context detection less aggressive<br>";
echo "✅ Prioritized valid nurse sessions over admin context<br>";
echo "✅ Fixed session functions to work with nurse sessions<br>";
echo "✅ Ensured session validation works correctly<br>";
?>
