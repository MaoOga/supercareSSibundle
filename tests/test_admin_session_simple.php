<?php
/**
 * Simple Admin Session Test
 */

// Start session with correct name
session_name('ADMIN_NEW_SESSION');
session_start();

echo "<h2>Admin Session Test</h2>";

// Test 1: Basic session info
echo "<h3>1. Session Information</h3>";
echo "Session name: " . session_name() . "<br>";
echo "Session ID: " . session_id() . "<br>";
echo "Session status: " . session_status() . "<br>";
echo "Session save path: " . session_save_path() . "<br>";

// Test 2: Create test session data
echo "<h3>2. Creating Test Session Data</h3>";
$_SESSION['test_admin_id'] = 123;
$_SESSION['test_admin_username'] = 'testadmin';
$_SESSION['test_user_type'] = 'admin';
$_SESSION['test_login_time'] = time();
$_SESSION['test_expires_at'] = time() + 1800;

echo "Test session data created:<br>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Test 3: Check if session persists
echo "<h3>3. Session Persistence Test</h3>";
echo "Session data should persist across requests.<br>";
echo "Try refreshing this page to see if the session data is maintained.<br>";

// Test 4: Cookie information
echo "<h3>4. Cookie Information</h3>";
echo "Session cookie: " . ($_COOKIE[session_name()] ?? 'not found') . "<br>";
echo "All cookies:<br>";
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";

// Test 5: Session configuration
echo "<h3>5. Session Configuration</h3>";
echo "Cookie parameters:<br>";
echo "<pre>";
print_r(session_get_cookie_params());
echo "</pre>";

// Test 6: Clear session button
echo "<h3>6. Session Management</h3>";
echo '<form method="post">';
echo '<button type="submit" name="clear_session">Clear Session</button>';
echo '</form>';

if (isset($_POST['clear_session'])) {
    session_destroy();
    echo "<p>Session cleared!</p>";
    echo '<script>location.reload();</script>';
}
?>
