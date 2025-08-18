<?php
require_once 'config.php';
require_once 'session_config.php';
require_once 'audit_logger.php';

echo "<h1>Nurse Login Flow Test</h1>";

try {
    // Step 1: Check if we have any nurses in the database
    echo "<h2>Step 1: Checking for Nurses in Database</h2>";
    
    $nurses = $pdo->query("SELECT id, nurse_id, name, email FROM nurses LIMIT 5")->fetchAll();
    
    if (count($nurses) > 0) {
        echo "<p>✅ Found " . count($nurses) . " nurses in database:</p>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Nurse ID</th><th>Name</th><th>Email</th></tr>";
        
        foreach ($nurses as $nurse) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($nurse['id']) . "</td>";
            echo "<td>" . htmlspecialchars($nurse['nurse_id']) . "</td>";
            echo "<td>" . htmlspecialchars($nurse['name']) . "</td>";
            echo "<td>" . htmlspecialchars($nurse['email']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Use the first nurse for testing
        $testNurse = $nurses[0];
        echo "<p>Using nurse: " . htmlspecialchars($testNurse['name']) . " (ID: " . htmlspecialchars($testNurse['nurse_id']) . ")</p>";
        
    } else {
        echo "<p>❌ No nurses found in database. Please create a nurse first.</p>";
        echo "<p><a href='admin.html'>Go to Admin Panel to create a nurse</a></p>";
        exit;
    }
    
    // Step 2: Simulate nurse login (like nurse_login.php)
    echo "<h2>Step 2: Simulating Nurse Login</h2>";
    
    // Clear any existing session
    session_destroy();
    session_start();
    
    // Simulate successful login (without password verification for testing)
    $_SESSION['nurse_id'] = $testNurse['id'];
    $_SESSION['nurse_info'] = [
        'id' => $testNurse['id'],
        'nurse_id' => $testNurse['nurse_id'],
        'name' => $testNurse['name'],
        'email' => $testNurse['email'],
        'role' => 'nurse'
    ];
    $_SESSION['logged_in'] = true;
    
    echo "<p>✅ Nurse session created:</p>";
    echo "<pre>" . print_r($_SESSION['nurse_info'], true) . "</pre>";
    
    // Step 3: Test session persistence
    echo "<h2>Step 3: Testing Session Persistence</h2>";
    
    // Force session write
    session_write_close();
    echo "<p>✅ Session written and closed</p>";
    
    // Reopen session
    session_start();
    echo "<p>✅ Session reopened</p>";
    
    if (isset($_SESSION['nurse_info'])) {
        echo "<p>✅ Nurse info still exists in session</p>";
        echo "<p>Nurse ID: " . htmlspecialchars($_SESSION['nurse_info']['nurse_id']) . "</p>";
        echo "<p>Nurse Name: " . htmlspecialchars($_SESSION['nurse_info']['name']) . "</p>";
    } else {
        echo "<p>❌ Nurse info lost from session</p>";
        exit;
    }
    
    // Step 4: Simulate form submission with audit logging
    echo "<h2>Step 4: Simulating Form Submission with Audit Logging</h2>";
    
    // Simulate form data
    $formData = [
        'name' => 'Test Patient',
        'age' => '30',
        'Sex' => 'Female',
        'uhid' => 'UHID' . time(),
        'phone' => '1234567890',
        'bed' => 'B101',
        'address' => 'Test Address',
        'diagnosis' => 'Test Diagnosis',
        'surgical_procedure' => 'Test Procedure'
    ];
    
    // Simulate patient ID generation
    $patientId = 'TEST_PATIENT_' . time();
    
    echo "<p>✅ Form data prepared</p>";
    echo "<p>Patient ID: {$patientId}</p>";
    
    // Get nurse info from session
    $nurseName = $_SESSION['nurse_info']['name'] ?? 'Unknown Nurse';
    $nurseId = $_SESSION['nurse_info']['nurse_id'] ?? 'Unknown';
    
    echo "<p>Nurse ID from session: {$nurseId}</p>";
    echo "<p>Nurse Name from session: {$nurseName}</p>";
    
    // Test audit logging
    $auditLogger = new AuditLogger($pdo);
    $patientData = [
        'patient_id' => $patientId,
        'uhid' => $formData['uhid'],
        'name' => $formData['name'],
        'action_type' => 'CREATE'
    ];
    
    $result = $auditLogger->logPatientCreate($nurseId, $patientId, $formData['name'], $patientData);
    
    if ($result) {
        echo "<p>✅ Patient create audit log created successfully</p>";
    } else {
        echo "<p>❌ Failed to create patient audit log</p>";
    }
    
    // Step 5: Verify audit log was created
    echo "<h2>Step 5: Verifying Audit Log</h2>";
    
    $logs = $pdo->query("SELECT * FROM admin_audit_logs WHERE entity_type = 'PATIENT' AND entity_id = '{$patientId}' ORDER BY timestamp DESC LIMIT 1")->fetchAll();
    
    if (count($logs) > 0) {
        echo "<p>✅ Audit log found in database:</p>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Timestamp</th><th>Admin User</th><th>Action</th><th>Entity ID</th><th>Description</th></tr>";
        
        foreach ($logs as $log) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($log['audit_id']) . "</td>";
            echo "<td>" . htmlspecialchars($log['timestamp']) . "</td>";
            echo "<td>" . htmlspecialchars($log['admin_user']) . "</td>";
            echo "<td>" . htmlspecialchars($log['action_type']) . "</td>";
            echo "<td>" . htmlspecialchars($log['entity_id']) . "</td>";
            echo "<td>" . htmlspecialchars($log['description']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No audit log found for patient ID: {$patientId}</p>";
    }
    
    // Step 6: Test API endpoint
    echo "<h2>Step 6: Testing API Endpoint</h2>";
    
    $apiUrl = "get_audit_logs.php?entity_type=PATIENT&action_type=CREATE,UPDATE";
    echo "<p>Testing API: {$apiUrl}</p>";
    
    // Simulate API call
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<p>HTTP Code: {$httpCode}</p>";
    
    if ($response) {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "<p>✅ API returned success</p>";
            echo "<p>Total logs: " . count($data['data']['logs']) . "</p>";
            
            // Check if our test log is in the response
            $found = false;
            foreach ($data['data']['logs'] as $log) {
                if ($log['entity_id'] === $patientId) {
                    $found = true;
                    echo "<p>✅ Our test log found in API response</p>";
                    break;
                }
            }
            
            if (!$found) {
                echo "<p>❌ Our test log not found in API response</p>";
            }
        } else {
            echo "<p>❌ API returned error: " . ($data['message'] ?? 'Unknown error') . "</p>";
        }
    } else {
        echo "<p>❌ No response from API</p>";
    }
    
    echo "<h2>✅ Test Complete</h2>";
    echo "<p>If all steps passed, the audit logging system should be working correctly.</p>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ul>";
    echo "<li>1. Login as a nurse through the actual login page</li>";
    echo "<li>2. Submit a patient form</li>";
    echo "<li>3. Check the admin panel for audit logs</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "Error: " . htmlspecialchars($e->getMessage());
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { color: #2563eb; }
h2 { color: #1f2937; margin-top: 30px; }
table { margin-top: 10px; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f3f4f6; }
pre { background-color: #f3f4f6; padding: 10px; border-radius: 4px; }
ul { margin-left: 20px; }
</style>
