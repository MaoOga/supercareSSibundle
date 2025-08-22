<?php
require_once 'config.php';
require_once 'session_config.php';

echo "<h1>Nurse Display Fix Test</h1>";

// Check current session status
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$session_valid = checkSessionActivity();
$nurse_info = getNurseInfo();

echo "<h2>Current Session Status</h2>";
echo "<p>Logged in: " . ($logged_in ? "✅ Yes" : "❌ No") . "</p>";
echo "<p>Session valid: " . ($session_valid ? "✅ Yes" : "❌ No") . "</p>";
echo "<p>Nurse ID: " . ($nurse_info['nurse_id'] ?? "Not set") . "</p>";
echo "<p>Nurse Name: " . ($nurse_info['name'] ?? "Not set") . "</p>";

if ($logged_in && $session_valid) {
    echo "<h2>✅ Session is valid - Testing nurse display</h2>";
    
    echo "<h3>Test Navigation (should show nurse ID):</h3>";
    echo "<ul>";
    echo "<li><a href='index.php' target='_blank'>Test index.php (should show nurse ID)</a></li>";
    echo "<li><a href='search.php' target='_blank'>Test search.php (should show nurse ID)</a></li>";
    echo "</ul>";
    
    echo "<h3>Test Form Submission Flow:</h3>";
    echo "<p>1. <a href='form.php' target='_blank'>Go to form.php</a></p>";
    echo "<p>2. Submit a form (it will redirect to index.php)</p>";
    echo "<p>3. Check if nurse ID is displayed on index.php after redirect</p>";
    
    echo "<h3>Expected Behavior:</h3>";
    echo "<ul>";
    echo "<li>✅ index.php should show nurse ID: <strong>" . htmlspecialchars($nurse_info['nurse_id']) . "</strong></li>";
    echo "<li>✅ search.php should show nurse ID: <strong>" . htmlspecialchars($nurse_info['nurse_id']) . "</strong></li>";
    echo "<li>✅ After form submission, redirect should show nurse ID</li>";
    echo "</ul>";
    
    echo "<h3>What was fixed:</h3>";
    echo "<ul>";
    echo "<li>✅ PHP wrappers now inject nurse information directly into HTML</li>";
    echo "<li>✅ Nurse ID displays are shown by default (not hidden)</li>";
    echo "<li>✅ sessionStorage is pre-populated for JavaScript compatibility</li>";
    echo "<li>✅ No more 'Loading...' text - actual nurse ID is displayed</li>";
    echo "</ul>";
    
} else {
    echo "<h2>❌ Session is not valid</h2>";
    echo "<p>Please log in first to test the nurse display functionality.</p>";
    echo "<p><a href='login.html'>Go to login page</a></p>";
}

echo "<h2>Technical Details</h2>";
echo "<p>The fix involves:</p>";
echo "<ol>";
echo "<li><strong>Direct HTML Injection</strong>: Nurse ID is injected directly into the HTML elements</li>";
echo "<li><strong>Display Control</strong>: Nurse ID displays are shown by default (style='display: flex;')</li>";
echo "<li><strong>JavaScript Compatibility</strong>: sessionStorage is pre-populated with nurse data</li>";
echo "<li><strong>Fallback Support</strong>: Both PHP-injected and JavaScript-loaded data work</li>";
echo "</ol>";

echo "<h2>Test Steps</h2>";
echo "<ol>";
echo "<li>Login as a nurse</li>";
echo "<li>Go to form.php and submit a form</li>";
echo "<li>Check that index.php shows the nurse ID after redirect</li>";
echo "<li>Navigate to search.php and verify nurse ID is displayed</li>";
echo "<li>Test that session management still works (logout, timeout, etc.)</li>";
echo "</ol>";

echo "<hr>";
echo "<p><small>Test completed at: " . date('Y-m-d H:i:s') . "</small></p>";
?>
