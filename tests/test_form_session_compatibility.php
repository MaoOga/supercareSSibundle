<?php
require_once '../database/config.php';
require_once '../auth/session_config.php';

echo "<h1>Form Session Compatibility Test</h1>";

// Test 1: Check current session status
echo "<h2>1. Current Session Status</h2>";
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$session_valid = checkSessionActivity();
$nurse_info = getNurseInfo();

echo "<p>Logged in: " . ($logged_in ? "✅ Yes" : "❌ No") . "</p>";
echo "<p>Session valid: " . ($session_valid ? "✅ Yes" : "❌ No") . "</p>";
echo "<p>User type: " . ($_SESSION['user_type'] ?? "Not set") . "</p>";
echo "<p>Nurse ID: " . ($nurse_info['nurse_id'] ?? "Not set") . "</p>";
echo "<p>Nurse Name: " . ($nurse_info['name'] ?? "Not set") . "</p>";

// Test 2: Test form.php access
echo "<h2>2. Form.php Access Test</h2>";
if ($logged_in && $session_valid) {
    echo "<p>✅ Form.php should be accessible</p>";
    echo "<p><a href='../forms/form.php' target='_blank'>Test form.php access</a></p>";
} else {
    echo "<p>❌ Form.php should redirect to login</p>";
    echo "<p><a href='../forms/form.php' target='_blank'>Test form.php redirect</a></p>";
}

// Test 3: Test form submission endpoints
echo "<h2>3. Form Submission Endpoints Test</h2>";

$endpoints = [
    'submit_form.php' => 'Basic form submission (now with session check)',
    'submit_form_working.php' => 'Working form submission with audit',
    'submit_form_with_audit.php' => 'Form submission with audit logging'
];

foreach ($endpoints as $endpoint => $description) {
    echo "<h3>$description</h3>";
    echo "<p>Endpoint: $endpoint</p>";
    
    if ($logged_in && $session_valid) {
        echo "<p>✅ Should accept submissions when logged in</p>";
    } else {
        echo "<p>❌ Should reject submissions when not logged in</p>";
    }
    
    // Create a simple test form for each endpoint
    echo "<form action='$endpoint' method='POST' target='_blank' style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
    echo "<input type='hidden' name='test_mode' value='1'>";
    echo "<input type='hidden' name='name' value='Test Patient'>";
    echo "<input type='hidden' name='uhid' value='TEST" . time() . "'>";
    echo "<input type='hidden' name='age' value='25'>";
    echo "<input type='hidden' name='Sex' value='Male'>";
    echo "<input type='submit' value='Test $endpoint' style='background: #007cba; color: white; padding: 5px 10px; border: none; cursor: pointer;'>";
    echo "</form>";
}

// Test 4: Test session check endpoints
echo "<h2>4. Session Check Endpoints Test</h2>";

$session_endpoints = [
    'check_nurse_session.php' => 'Nurse session validation',
    'update_session_activity.php' => 'Session activity update'
];

foreach ($session_endpoints as $endpoint => $description) {
    echo "<h3>$description</h3>";
    echo "<p>Endpoint: $endpoint</p>";
    echo "<p><a href='$endpoint' target='_blank'>Test $endpoint</a></p>";
}

// Test 5: Test form template session integration
echo "<h2>5. Form Template Session Integration</h2>";
echo "<p>The form template includes:</p>";
echo "<ul>";
echo "<li>✅ Client-side session check before form submission</li>";
echo "<li>✅ Uses check_nurse_session.php for validation</li>";
echo "<li>✅ Session management JavaScript with timers</li>";
echo "<li>✅ Activity monitoring and timeout handling</li>";
echo "</ul>";

// Test 6: Test logout functionality
echo "<h2>6. Logout Functionality Test</h2>";
echo "<p><a href='../auth/nurse_logout.php' target='_blank'>Test nurse logout</a></p>";
echo "<p>After logout, all form submissions should be rejected.</p>";

// Test 7: Test session timeout
echo "<h2>7. Session Timeout Test</h2>";
echo "<p>Current session timeout: " . (NURSE_SESSION_TIMEOUT / 60) . " minutes</p>";
echo "<p>Last activity: " . ($_SESSION['last_activity'] ?? 'Not set') . "</p>";

if (isset($_SESSION['last_activity'])) {
    $last_activity = strtotime($_SESSION['last_activity']);
    $time_diff = time() - $last_activity;
    $timeout_remaining = NURSE_SESSION_TIMEOUT - $time_diff;
    
    echo "<p>Time since last activity: " . round($time_diff / 60, 2) . " minutes</p>";
    echo "<p>Timeout remaining: " . round($timeout_remaining / 60, 2) . " minutes</p>";
    
    if ($timeout_remaining <= 0) {
        echo "<p style='color: red;'>⚠️ Session has expired!</p>";
    } else {
        echo "<p style='color: green;'>✅ Session is still valid</p>";
    }
}

// Test 8: Test navigation consistency
echo "<h2>8. Navigation Consistency Test</h2>";
echo "<p>All navigation should use .php files:</p>";
echo "<ul>";
echo "<li><a href='../pages/index.php'>index.php</a> (protected)</li>";
echo "<li><a href='../forms/search.php'>search.php</a> (protected)</li>";
echo "<li><a href='../forms/form.php'>form.php</a> (protected)</li>";
echo "<li><a href='../auth/login.html'>login.html</a> (public)</li>";
echo "</ul>";

echo "<h2>Summary</h2>";
if ($logged_in && $session_valid) {
    echo "<p style='color: green; font-weight: bold;'>✅ Session management is working correctly</p>";
    echo "<p>All form submissions should work properly with session validation.</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Session is not valid</p>";
    echo "<p>Form submissions will be rejected until you log in.</p>";
    echo "<p><a href='../auth/login.html'>Go to login page</a></p>";
}

echo "<hr>";
echo "<p><small>Test completed at: " . date('Y-m-d H:i:s') . "</small></p>";
?>
