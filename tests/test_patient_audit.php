<?php
require_once '../database/config.php';
require_once '../audit/audit_logger.php';

echo "<h1>Test Patient Audit Logging</h1>";

try {
    // Start session to simulate nurse login
    session_start();
    
    // Simulate nurse session data
    $_SESSION['nurse_id'] = 'test_nurse_123';
    $_SESSION['nurse_info'] = [
        'id' => 'test_nurse_123',
        'nurse_id' => 'NURSE001',
        'name' => 'Test Nurse',
        'email' => 'test@hospital.com',
        'role' => 'nurse'
    ];
    $_SESSION['logged_in'] = true;
    
    echo "<h2>✅ Session created with nurse info:</h2>";
    echo "<pre>" . print_r($_SESSION['nurse_info'], true) . "</pre>";
    
    // Test audit logging
    $auditLogger = new AuditLogger($pdo);
    
    // Test patient create
    echo "<h2>Testing Patient Create Audit Log:</h2>";
    $patientData = [
        'patient_id' => 'TEST_PATIENT_001',
        'uhid' => 'UHID123456',
        'name' => 'John Doe',
        'action_type' => 'CREATE'
    ];
    
    $result = $auditLogger->logPatientCreate(
        $_SESSION['nurse_info']['nurse_id'],
        'TEST_PATIENT_001',
        'John Doe',
        $patientData
    );
    
    if ($result) {
        echo "<p>✅ Patient create audit log created successfully</p>";
    } else {
        echo "<p>❌ Failed to create patient audit log</p>";
    }
    
    // Test patient update
    echo "<h2>Testing Patient Update Audit Log:</h2>";
    $patientData = [
        'patient_id' => 'TEST_PATIENT_001',
        'uhid' => 'UHID123456',
        'name' => 'John Doe Updated',
        'action_type' => 'UPDATE'
    ];
    
    $result = $auditLogger->logPatientUpdate(
        $_SESSION['nurse_info']['nurse_id'],
        'TEST_PATIENT_001',
        'John Doe Updated',
        $patientData
    );
    
    if ($result) {
        echo "<p>✅ Patient update audit log created successfully</p>";
    } else {
        echo "<p>❌ Failed to create patient update audit log</p>";
    }
    
    // Check if logs were created
    echo "<h2>Checking Audit Logs:</h2>";
    $logs = $pdo->query("SELECT * FROM admin_audit_logs WHERE entity_type = 'PATIENT' ORDER BY timestamp DESC LIMIT 5")->fetchAll();
    
    if (count($logs) > 0) {
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
        echo "<p>❌ No patient audit logs found</p>";
    }
    
    // Test the API endpoint
    echo "<h2>Testing API Endpoint:</h2>";
    $apiUrl = "get_audit_logs.php?entity_type=PATIENT&action_type=CREATE,UPDATE";
    echo "<p>API URL: {$apiUrl}</p>";
    
    // Simulate API call
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<p>HTTP Code: {$httpCode}</p>";
    echo "<p>Response:</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    if ($response) {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "<p>✅ API returned success</p>";
            echo "<p>Logs count: " . count($data['data']['logs']) . "</p>";
        } else {
            echo "<p>❌ API returned error: " . ($data['message'] ?? 'Unknown error') . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "Error: " . htmlspecialchars($e->getMessage());
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
</style>
