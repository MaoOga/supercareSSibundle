<?php
/**
 * Test Login Flow and Session Creation
 * Verify that nurse login creates proper sessions
 */

echo "<h2>Login Flow and Session Test</h2>";

// Test 1: Check if nurses table exists and has data
echo "<h3>1. Database Check</h3>";

require_once 'config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if nurses table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'nurses'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Nurses table exists<br>";
        
        // Check if there are any nurses
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM nurses");
        $result = $stmt->fetch();
        echo "Number of nurses in database: " . $result['count'] . "<br>";
        
        if ($result['count'] > 0) {
            // Get a sample nurse for testing
            $stmt = $pdo->query("SELECT id, nurse_id, name FROM nurses LIMIT 1");
            $nurse = $stmt->fetch();
            echo "Sample nurse: ID=" . $nurse['id'] . ", Nurse ID=" . $nurse['nurse_id'] . ", Name=" . $nurse['name'] . "<br>";
        } else {
            echo "❌ No nurses found in database<br>";
        }
    } else {
        echo "❌ Nurses table does not exist<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 2: Check session configuration
echo "<h3>2. Session Configuration Check</h3>";

require_once 'session_config.php';

echo "Session name: " . session_name() . "<br>";
echo "Session status: " . session_status() . "<br>";
echo "NURSE_SESSION_TIMEOUT: " . NURSE_SESSION_TIMEOUT . " seconds<br>";
echo "SESSION_TIMEOUT: " . SESSION_TIMEOUT . " seconds<br>";

// Test 3: Simulate login process
echo "<h3>3. Simulate Login Process</h3>";

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

// Test 4: Check session validation
echo "<h3>4. Session Validation Test</h3>";

$sessionValid = checkSessionActivity();
echo "checkSessionActivity() result: " . ($sessionValid ? "✅ Valid" : "❌ Invalid") . "<br>";

// Test 5: Check isNurseLoggedIn function
echo "<h3>5. isNurseLoggedIn Function Test</h3>";

$isLoggedIn = isNurseLoggedIn();
echo "isNurseLoggedIn() result: " . ($isLoggedIn ? "✅ Logged in" : "❌ Not logged in") . "<br>";

// Test 6: Test update_session_activity.php
echo "<h3>6. Update Session Activity Test</h3>";

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

// Test 7: Test check_nurse_session.php
echo "<h3>7. Check Nurse Session Test</h3>";

$checkUrl = 'check_nurse_session.php';
echo "Testing: {$checkUrl}<br>";

$response = file_get_contents($checkUrl, false, $context);
echo "Response: " . $response . "<br>";

// Test 8: Check session timeout
echo "<h3>8. Session Timeout Test</h3>";

$timeSinceActivity = time() - $_SESSION['last_activity'];
$timeout = NURSE_SESSION_TIMEOUT;

echo "Time since last activity: {$timeSinceActivity} seconds<br>";
echo "Timeout: {$timeout} seconds<br>";
echo "Session expires in: " . ($timeout - $timeSinceActivity) . " seconds<br>";

if ($timeSinceActivity > $timeout) {
    echo "❌ Session would be expired<br>";
} else {
    echo "✅ Session is still valid<br>";
}

// Test 9: Check for session conflicts
echo "<h3>9. Session Conflict Check</h3>";

$is_admin_context = false;

// Check for admin session cookies
if (isset($_COOKIE['ADMIN_NEW_SESSION']) || isset($_COOKIE['SUPER_ADMIN_SESSION'])) {
    $is_admin_context = true;
    echo "⚠️ Admin context detected via cookies - this might cause conflicts<br>";
}

// Check URL patterns
if (isset($_SERVER['REQUEST_URI'])) {
    $admin_patterns = ['admin_login_new.php', 'admin.php', 'admin_logout_new.php', 'admin_patient_records.php', 'audit_log.php'];
    foreach ($admin_patterns as $pattern) {
        if (strpos($_SERVER['REQUEST_URI'], $pattern) !== false) {
            $is_admin_context = true;
            echo "⚠️ Admin context detected via URL pattern: {$pattern}<br>";
            break;
        }
    }
}

echo "Is admin context: " . ($is_admin_context ? "Yes" : "No") . "<br>";

// Test 10: Recommendations
echo "<h3>10. Recommendations</h3>";

if ($is_admin_context) {
    echo "⚠️ Admin context detected - this might interfere with nurse sessions<br>";
    echo "Consider clearing admin cookies before testing nurse login<br>";
}

if (SESSION_TIMEOUT < 1800) {
    echo "⚠️ SESSION_TIMEOUT is less than 30 minutes - consider increasing it<br>";
}

echo "<h3>11. How to Test Login</h3>";
echo "1. Clear all browser cookies<br>";
echo "2. Go to login.html<br>";
echo "3. Enter valid nurse credentials<br>";
echo "4. Check if you're redirected to index.html<br>";
echo "5. If session expires immediately, check the debug output above<br>";

echo "<h3>12. Quick Fixes Applied</h3>";
echo "✅ Fixed update_session_activity.php to use nurse session system<br>";
echo "✅ Increased SESSION_TIMEOUT from 10 to 30 minutes<br>";
echo "✅ Modified session checking to be less aggressive<br>";
echo "✅ Created debug script to identify issues<br>";
?>
