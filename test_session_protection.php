<?php
require_once 'config.php';
require_once 'session_config.php';

echo "<h1>Session Protection Test</h1>";

// Check if user is logged in
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$session_valid = checkSessionActivity();

echo "<h2>Session Status:</h2>";
echo "<p>Logged in: " . ($logged_in ? "Yes" : "No") . "</p>";
echo "<p>Session valid: " . ($session_valid ? "Yes" : "No") . "</p>";
echo "<p>User type: " . ($_SESSION['user_type'] ?? "Not set") . "</p>";
echo "<p>Nurse ID: " . ($_SESSION['nurse_id'] ?? "Not set") . "</p>";

if ($logged_in && $session_valid) {
    echo "<h2>✅ Session is valid - Access granted</h2>";
    echo "<p><a href='index.php'>Go to Index Page</a></p>";
    echo "<p><a href='search.php'>Go to Search Page</a></p>";
} else {
    echo "<h2>❌ Session is invalid - Access denied</h2>";
    echo "<p><a href='login.html'>Go to Login Page</a></p>";
}

echo "<h2>Test Direct Access:</h2>";
echo "<p><a href='index.html' target='_blank'>Try direct access to index.html</a></p>";
echo "<p><a href='search.html' target='_blank'>Try direct access to search.html</a></p>";
echo "<p><a href='index.php' target='_blank'>Try access to index.php</a></p>";
echo "<p><a href='search.php' target='_blank'>Try access to search.php</a></p>";
?>
