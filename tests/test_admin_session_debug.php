<?php
/**
 * Admin Session Debug Script
 * Test admin session creation and validation
 */

// Include session manager
require_once '../auth/admin_session_manager.php';

echo "<h2>Admin Session Debug</h2>";

// Test 1: Check if session manager is working
echo "<h3>1. Session Manager Test</h3>";
echo "Session name: " . session_name() . "<br>";
echo "Session ID: " . session_id() . "<br>";
echo "Session status: " . session_status() . "<br>";

// Test 2: Check if admin exists in database
echo "<h3>2. Database Connection Test</h3>";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=supercare_ssi;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT id, admin_username, email, status FROM admin_users WHERE status = 'active' LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "Found admin: " . $admin['admin_username'] . " (" . $admin['email'] . ")<br>";
        
        // Test 3: Try to create a session
        echo "<h3>3. Session Creation Test</h3>";
        if ($adminSession->createSession($admin)) {
            echo "Session created successfully!<br>";
            echo "Session data:<br>";
            echo "<pre>";
            print_r($_SESSION);
            echo "</pre>";
            
            // Test 4: Validate session
            echo "<h3>4. Session Validation Test</h3>";
            if ($adminSession->validateSession()) {
                echo "Session validation: SUCCESS<br>";
                $adminInfo = $adminSession->getAdminInfo();
                echo "Admin info:<br>";
                echo "<pre>";
                print_r($adminInfo);
                echo "</pre>";
            } else {
                echo "Session validation: FAILED<br>";
            }
            
            // Test 5: Check session timeout
            echo "<h3>5. Session Timeout Test</h3>";
            echo "Current time: " . time() . "<br>";
            echo "Session expires at: " . ($_SESSION['expires_at'] ?? 'not set') . "<br>";
            echo "Time until expiry: " . (($_SESSION['expires_at'] ?? 0) - time()) . " seconds<br>";
            
        } else {
            echo "Failed to create session<br>";
        }
    } else {
        echo "No active admin found in database<br>";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
}

// Test 6: Check cookies
echo "<h3>6. Cookie Test</h3>";
echo "All cookies:<br>";
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";

echo "Session cookie: " . ($_COOKIE[session_name()] ?? 'not found') . "<br>";

// Test 7: Check session configuration
echo "<h3>7. Session Configuration Test</h3>";
echo "Session save path: " . session_save_path() . "<br>";
echo "Session cookie params:<br>";
echo "<pre>";
print_r(session_get_cookie_params());
echo "</pre>";
?>
