<?php
// Test Admin Panel Without Session Validation
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Admin Panel No-Session Test</h2>";

// Test 1: Check if admin.php is accessible
echo "<h3>Test 1: Admin Panel Access</h3>";
echo "<a href='admin.php' target='_blank'>Access Admin Panel</a><br>";

// Test 2: Check if get_nurses_simple.php works
echo "<h3>Test 2: Nurses API</h3>";
$nurses_url = 'get_nurses_simple.php';
$nurses_response = file_get_contents($nurses_url);
$nurses_data = json_decode($nurses_response, true);

if ($nurses_data && isset($nurses_data['success'])) {
    echo "✅ Nurses API working: " . ($nurses_data['success'] ? 'Success' : 'Failed') . "<br>";
    if ($nurses_data['success']) {
        echo "Total nurses: " . ($nurses_data['total_count'] ?? 0) . "<br>";
    } else {
        echo "Error: " . ($nurses_data['message'] ?? 'Unknown error') . "<br>";
    }
} else {
    echo "❌ Nurses API not working<br>";
}

// Test 3: Check if get_surgeons.php works
echo "<h3>Test 3: Surgeons API</h3>";
$surgeons_url = 'get_surgeons.php';
$surgeons_response = file_get_contents($surgeons_url);
$surgeons_data = json_decode($surgeons_response, true);

if ($surgeons_data && isset($surgeons_data['success'])) {
    echo "✅ Surgeons API working: " . ($surgeons_data['success'] ? 'Success' : 'Failed') . "<br>";
    if ($surgeons_data['success']) {
        echo "Total surgeons: " . ($surgeons_data['total_count'] ?? 0) . "<br>";
    } else {
        echo "Error: " . ($surgeons_data['message'] ?? 'Unknown error') . "<br>";
    }
} else {
    echo "❌ Surgeons API not working<br>";
}

// Test 4: Check if get_patients.php works
echo "<h3>Test 4: Patients API</h3>";
$patients_url = 'get_patients.php';
$patients_response = file_get_contents($patients_url);
$patients_data = json_decode($patients_response, true);

if ($patients_data && isset($patients_data['success'])) {
    echo "✅ Patients API working: " . ($patients_data['success'] ? 'Success' : 'Failed') . "<br>";
    if ($patients_data['success']) {
        echo "Total patients: " . ($patients_data['total_count'] ?? 0) . "<br>";
    } else {
        echo "Error: " . ($patients_data['message'] ?? 'Unknown error') . "<br>";
    }
} else {
    echo "❌ Patients API not working<br>";
}

// Test 5: Check if update_session_activity.php works
echo "<h3>Test 5: Session Activity API</h3>";
$activity_url = 'update_session_activity.php';
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode(['timestamp' => time()])
    ]
]);
$activity_response = file_get_contents($activity_url, false, $context);
$activity_data = json_decode($activity_response, true);

if ($activity_data && isset($activity_data['success'])) {
    echo "✅ Session Activity API working: " . ($activity_data['success'] ? 'Success' : 'Failed') . "<br>";
} else {
    echo "❌ Session Activity API not working<br>";
}

echo "<h3>Summary</h3>";
echo "All session validation has been removed from the admin panel.<br>";
echo "The admin panel should now work without any login requirements.<br>";
echo "<br><strong>Next Steps:</strong><br>";
echo "1. <a href='admin.php' target='_blank'>Try accessing the admin panel directly</a><br>";
echo "2. <a href='admin_login_new.html' target='_blank'>Try the login page</a><br>";
echo "3. Login with valid credentials and see if it redirects to admin.php without issues<br>";
?>
