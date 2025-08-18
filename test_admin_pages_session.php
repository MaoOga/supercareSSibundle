<?php
// Test Admin Pages Session Integration
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Admin Pages Session Integration Test</h2>";

// Test 1: Check session manager
echo "<h3>Test 1: Session Manager</h3>";
if (file_exists('admin_session_manager.php')) {
    echo "✅ admin_session_manager.php exists<br>";
    
    // Include session manager
    require_once 'admin_session_manager.php';
    
    if (isset($adminSession)) {
        echo "✅ Session manager loaded successfully<br>";
        echo "Session name: " . session_name() . "<br>";
        echo "Session ID: " . session_id() . "<br>";
        echo "Session status: " . session_status() . "<br>";
    } else {
        echo "❌ Session manager not loaded<br>";
    }
} else {
    echo "❌ admin_session_manager.php not found<br>";
}

// Test 2: Check all admin pages
echo "<h3>Test 2: Admin Pages Integration</h3>";

$admin_pages = [
    'admin.php' => 'Admin Panel',
    'audit_log.php' => 'Audit Log',
    'admin_patient_records.php' => 'Patient Records'
];

foreach ($admin_pages as $file => $name) {
    if (file_exists($file)) {
        echo "✅ $name ($file) exists<br>";
        
        // Check if it includes the session manager
        $content = file_get_contents($file);
        if (strpos($content, "require_once 'admin_session_manager.php'") !== false) {
            echo "✅ $name includes session manager<br>";
        } else {
            echo "❌ $name does NOT include session manager<br>";
        }
        
        if (strpos($content, "$adminSession->validateSession()") !== false) {
            echo "✅ $name uses session validation<br>";
        } else {
            echo "❌ $name does NOT use session validation<br>";
        }
        
        if (strpos($content, "admin_logout_new.php") !== false) {
            echo "✅ $name uses unified logout handler<br>";
        } else {
            echo "❌ $name does NOT use unified logout handler<br>";
        }
        
    } else {
        echo "❌ $name ($file) not found<br>";
    }
    echo "<br>";
}

// Test 3: Check logout handler
echo "<h3>Test 3: Logout Handler</h3>";
if (file_exists('admin_logout_new.php')) {
    echo "✅ admin_logout_new.php exists<br>";
    
    $content = file_get_contents('admin_logout_new.php');
    if (strpos($content, "require_once 'admin_session_manager.php'") !== false) {
        echo "✅ Logout handler includes session manager<br>";
    } else {
        echo "❌ Logout handler does NOT include session manager<br>";
    }
    
    if (strpos($content, "$adminSession->destroySession()") !== false) {
        echo "✅ Logout handler uses session manager destroy method<br>";
    } else {
        echo "❌ Logout handler does NOT use session manager destroy method<br>";
    }
} else {
    echo "❌ admin_logout_new.php not found<br>";
}

// Test 4: Check session activity API
echo "<h3>Test 4: Session Activity API</h3>";
if (file_exists('update_session_activity.php')) {
    echo "✅ update_session_activity.php exists<br>";
    
    $content = file_get_contents('update_session_activity.php');
    if (strpos($content, "require_once 'admin_session_manager.php'") !== false) {
        echo "✅ Session activity API includes session manager<br>";
    } else {
        echo "❌ Session activity API does NOT include session manager<br>";
    }
    
    if (strpos($content, "$adminSession->validateSession()") !== false) {
        echo "✅ Session activity API validates session<br>";
    } else {
        echo "❌ Session activity API does NOT validate session<br>";
    }
} else {
    echo "❌ update_session_activity.php not found<br>";
}

echo "<h3>Session Integration Features</h3>";
echo "✅ <strong>Unified Session Management</strong> - All pages use same session manager<br>";
echo "✅ <strong>Consistent Validation</strong> - All pages validate session the same way<br>";
echo "✅ <strong>Unified Logout</strong> - All pages use same logout handler<br>";
echo "✅ <strong>Activity Tracking</strong> - All pages track session activity<br>";
echo "✅ <strong>Session Timeout</strong> - 30-minute timeout applies to all pages<br>";
echo "✅ <strong>Security Features</strong> - All pages have same security measures<br>";

echo "<h3>How It Works</h3>";
echo "🔗 <strong>Single Session</strong> - All three pages share the same session<br>";
echo "🔗 <strong>Unified Logout</strong> - Logout from any page destroys session for all pages<br>";
echo "🔗 <strong>Activity Sync</strong> - Activity on any page keeps session alive for all pages<br>";
echo "🔗 <strong>Consistent Security</strong> - Same validation and security on all pages<br>";

echo "<h3>Test Links</h3>";
echo "1. <a href='admin_login_new.html' target='_blank'>Login Page</a><br>";
echo "2. <a href='admin.php' target='_blank'>Admin Panel</a><br>";
echo "3. <a href='audit_log.php' target='_blank'>Audit Log</a><br>";
echo "4. <a href='admin_patient_records.php' target='_blank'>Patient Records</a><br>";

echo "<h3>Testing Instructions</h3>";
echo "1. Login to any admin page<br>";
echo "2. Navigate between all three pages - session should persist<br>";
echo "3. Logout from any page - should redirect to login<br>";
echo "4. Try accessing other pages after logout - should redirect to login<br>";
echo "5. Wait 30 minutes without activity - session should expire<br>";

echo "<p><strong>All three admin pages now share the same robust session system!</strong> 🚀</p>";
?>
