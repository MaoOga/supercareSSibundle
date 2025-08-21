<?php
/**
 * Session Detection Test
 * Test which session system is being used
 */

echo "<h2>Session Detection Test</h2>";

// Test 1: Check all session cookies
echo "<h3>1. Session Cookies</h3>";
echo "All cookies:<br>";
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";

// Test 2: Check for specific session cookies
echo "<h3>2. Specific Session Cookies</h3>";
$session_cookies = [
    'ADMIN_NEW_SESSION' => 'Admin Session',
    'SUPER_ADMIN_SESSION' => 'Super Admin Session', 
    'SSI_BUNDLE_SESSION' => 'Nurse/Form Session'
];

foreach ($session_cookies as $cookie_name => $description) {
    if (isset($_COOKIE[$cookie_name])) {
        echo "✅ <strong>$description</strong> ($cookie_name): " . $_COOKIE[$cookie_name] . "<br>";
    } else {
        echo "❌ <strong>$description</strong> ($cookie_name): Not found<br>";
    }
}

// Test 3: Check HTTP Referer
echo "<h3>3. HTTP Referer</h3>";
if (isset($_SERVER['HTTP_REFERER'])) {
    echo "Referer: " . $_SERVER['HTTP_REFERER'] . "<br>";
    
    $admin_patterns = ['admin.php', 'admin_login_new.html', 'admin_patient_records.php'];
    foreach ($admin_patterns as $pattern) {
        if (strpos($_SERVER['HTTP_REFERER'], $pattern) !== false) {
            echo "✅ Detected admin pattern: $pattern<br>";
        }
    }
} else {
    echo "No HTTP referer found<br>";
}

// Test 4: Test session detection logic
echo "<h3>4. Session Detection Logic</h3>";
$is_admin_context = false;

// Check cookies
if (isset($_COOKIE['ADMIN_NEW_SESSION']) || isset($_COOKIE['SUPER_ADMIN_SESSION'])) {
    $is_admin_context = true;
    echo "✅ Admin context detected via cookies<br>";
}

// Check referer
if (isset($_SERVER['HTTP_REFERER'])) {
    $admin_patterns = ['admin.php', 'admin_login_new.html', 'admin_patient_records.php'];
    foreach ($admin_patterns as $pattern) {
        if (strpos($_SERVER['HTTP_REFERER'], $pattern) !== false) {
            $is_admin_context = true;
            echo "✅ Admin context detected via referer: $pattern<br>";
            break;
        }
    }
}

if ($is_admin_context) {
    echo "<strong>Result: ADMIN CONTEXT DETECTED</strong><br>";
} else {
    echo "<strong>Result: NURSE/FORM CONTEXT DETECTED</strong><br>";
}

// Test 5: Current session info
echo "<h3>5. Current Session Info</h3>";
echo "Session name: " . session_name() . "<br>";
echo "Session ID: " . session_id() . "<br>";
echo "Session status: " . session_status() . "<br>";

// Test 6: Try to start different sessions
echo "<h3>6. Session System Test</h3>";

// Test admin session
echo "<h4>Testing Admin Session:</h4>";
session_name('ADMIN_NEW_SESSION');
session_start();
echo "Admin session data:<br>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Test super admin session
echo "<h4>Testing Super Admin Session:</h4>";
session_name('SUPER_ADMIN_SESSION');
session_start();
echo "Super admin session data:<br>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Test nurse session
echo "<h4>Testing Nurse Session:</h4>";
session_name('SSI_BUNDLE_SESSION');
session_start();
echo "Nurse session data:<br>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>
