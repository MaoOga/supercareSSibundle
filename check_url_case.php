<?php
// Check URL Case Sensitivity
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>URL Case Sensitivity Check</h2>";

// Check current URL
echo "<h3>Current URL Information</h3>";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'not set') . "<br>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'not set') . "<br>";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'not set') . "<br>";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'not set') . "<br>";

// Check if we're in the right directory
$current_dir = basename(dirname($_SERVER['SCRIPT_NAME']));
echo "Current directory: $current_dir<br>";

// Check if the directory exists with different cases
$document_root = $_SERVER['DOCUMENT_ROOT'] ?? '';
$possible_paths = [
    $document_root . '/supercareSSibundle',
    $document_root . '/SUPERCARESSIBUNDLE',
    $document_root . '/SupercareSSIBundle',
    $document_root . '/SUPERCARESSIBUNDLE'
];

echo "<h3>Directory Check</h3>";
foreach ($possible_paths as $path) {
    if (is_dir($path)) {
        echo "✅ Directory exists: $path<br>";
    } else {
        echo "❌ Directory not found: $path<br>";
    }
}

// Check session cookie path
echo "<h3>Session Cookie Check</h3>";
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

// Check session configuration
echo "<h3>Session Configuration</h3>";
echo "Session save path: " . ini_get('session.save_path') . "<br>";
echo "Session name: " . session_name() . "<br>";

// Test session with current path
echo "<h3>Session Test</h3>";
session_start();
echo "Session ID: " . session_id() . "<br>";
echo "Session status: " . session_status() . "<br>";

// Check session file
$session_path = ini_get('session.save_path');
if (empty($session_path)) {
    $session_path = sys_get_temp_dir();
}

$session_file = $session_path . '/sess_' . session_id();
echo "Session file: $session_file<br>";
echo "File exists: " . (file_exists($session_file) ? "✅ Yes" : "❌ No") . "<br>";

// Recommendations
echo "<h3>Recommendations</h3>";
echo "1. <strong>Always use the correct case:</strong> supercareSSibundle<br>";
echo "2. <strong>Clear browser cache</strong> for both URL variations<br>";
echo "3. <strong>Use consistent URLs</strong> in all links and redirects<br>";
echo "4. <strong>Check your bookmarks</strong> for incorrect case<br>";

// Test links with correct case
echo "<h3>Test Links (Correct Case)</h3>";
echo "<a href='/supercareSSibundle/admin_login_new.html' target='_blank'>Login Page (Correct)</a><br>";
echo "<a href='/supercareSSibundle/admin.php' target='_blank'>Admin Panel (Correct)</a><br>";
echo "<a href='/supercareSSibundle/test_admin_access.php' target='_blank'>Test Admin Access</a><br>";

// Test links with incorrect case
echo "<h3>Test Links (Incorrect Case - Should Not Work)</h3>";
echo "<a href='/SUPERCARESSIBUNDLE/admin_login_new.html' target='_blank'>Login Page (Incorrect)</a><br>";
echo "<a href='/SUPERCARESSIBUNDLE/admin.php' target='_blank'>Admin Panel (Incorrect)</a><br>";

echo "<h3>Next Steps</h3>";
echo "1. Use only the correct case URL: <strong>supercareSSibundle</strong><br>";
echo "2. Clear your browser cache completely<br>";
echo "3. Try logging in using the correct URL<br>";
echo "4. Check if this resolves the session issue<br>";
?>
