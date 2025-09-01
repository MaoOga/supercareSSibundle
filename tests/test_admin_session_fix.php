<?php
/**
 * Admin Session Fix Test
 * Test that admin session conflicts have been resolved
 */

echo "<h2>Admin Session Fix Test</h2>";

// Test 1: Check if admin session manager works
echo "<h3>1. Admin Session Manager Test</h3>";
require_once '../auth/admin_session_manager.php';

echo "Session name: " . session_name() . "<br>";
echo "Session ID: " . session_id() . "<br>";
echo "Session status: " . session_status() . "<br>";

// Test 2: Test get_nurses_simple.php
echo "<h3>2. Testing get_nurses_simple.php</h3>";
echo "This should work without session conflicts now.<br>";

// Test 3: Test update_session_activity.php
echo "<h3>3. Testing update_session_activity.php</h3>";
echo "This should work without session conflicts now.<br>";

// Test 4: Check session cookies
echo "<h3>4. Session Cookies</h3>";
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

// Test 5: Test API calls
echo "<h3>5. API Call Test</h3>";
echo '<button onclick="testAPICalls()">Test API Calls</button>';
echo '<div id="apiResults"></div>';

?>

<script>
async function testAPICalls() {
    const resultsDiv = document.getElementById('apiResults');
    resultsDiv.innerHTML = '<p>Testing API calls...</p>';
    
    try {
        // Test get_nurses_simple.php
        const nursesResponse = await fetch('../admin/get_nurses_simple.php');
        const nursesData = await nursesResponse.json();
        
        if (nursesResponse.ok) {
            resultsDiv.innerHTML += '<p>✅ get_nurses_simple.php: Success</p>';
        } else {
            resultsDiv.innerHTML += '<p>❌ get_nurses_simple.php: ' + nursesData.message + '</p>';
        }
        
        // Test update_session_activity.php
        const sessionResponse = await fetch('../security/update_session_activity.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        });
        const sessionData = await sessionResponse.json();
        
        if (sessionResponse.ok) {
            resultsDiv.innerHTML += '<p>✅ update_session_activity.php: Success</p>';
        } else {
            resultsDiv.innerHTML += '<p>❌ update_session_activity.php: ' + sessionData.message + '</p>';
        }
        
    } catch (error) {
        resultsDiv.innerHTML += '<p>❌ Error: ' + error.message + '</p>';
    }
}
</script>
