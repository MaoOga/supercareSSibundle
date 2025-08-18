<?php
require_once 'config.php';
require_once 'session_config.php';

echo "<h1>Form Session Test</h1>";

// Check if we're in a session
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p>✅ Session is active</p>";
    echo "<p>Session ID: " . session_id() . "</p>";
} else {
    echo "<p>❌ Session is not active</p>";
}

// Check session data
echo "<h2>Current Session Data:</h2>";
if (isset($_SESSION['nurse_info'])) {
    echo "<p>✅ Nurse info found in session:</p>";
    echo "<pre>" . print_r($_SESSION['nurse_info'], true) . "</pre>";
} else {
    echo "<p>❌ No nurse info in session</p>";
}

// Check all session data
echo "<h2>All Session Data:</h2>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

// Test setting session data
echo "<h2>Testing Session Write:</h2>";
$_SESSION['test_time'] = time();
$_SESSION['test_data'] = 'Test value';

echo "<p>✅ Set test data in session</p>";

// Force session write
session_write_close();
echo "<p>✅ Session written and closed</p>";

// Reopen session
session_start();
echo "<p>✅ Session reopened</p>";

if (isset($_SESSION['test_data'])) {
    echo "<p>✅ Test data persisted: " . $_SESSION['test_data'] . "</p>";
} else {
    echo "<p>❌ Test data did not persist</p>";
}

// Check if nurse info still exists
if (isset($_SESSION['nurse_info'])) {
    echo "<p>✅ Nurse info still exists after session restart</p>";
} else {
    echo "<p>❌ Nurse info lost after session restart</p>";
}

echo "<h2>Session Configuration:</h2>";
echo "<p>Session save path: " . session_save_path() . "</p>";
echo "<p>Session name: " . session_name() . "</p>";
echo "<p>Session cookie lifetime: " . ini_get('session.cookie_lifetime') . "</p>";
echo "<p>Session use cookies: " . ini_get('session.use_cookies') . "</p>";
echo "<p>Session use only cookies: " . ini_get('session.use_only_cookies') . "</p>";
echo "<p>Session use strict mode: " . ini_get('session.use_strict_mode') . "</p>";

// Check if session files exist
$sessionFiles = glob(session_save_path() . '/sess_*');
echo "<p>Session files in save path: " . count($sessionFiles) . "</p>";

if (count($sessionFiles) > 0) {
    echo "<p>Session files:</p>";
    foreach (array_slice($sessionFiles, 0, 5) as $file) {
        echo "<p>" . basename($file) . " - " . date('Y-m-d H:i:s', filemtime($file)) . "</p>";
    }
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { color: #2563eb; }
h2 { color: #1f2937; margin-top: 30px; }
pre { background-color: #f3f4f6; padding: 10px; border-radius: 4px; }
</style>
