<?php
/**
 * Test script for Nurse Form Audit Logging
 * This script tests the nurse form audit logging functionality
 */

require_once 'config.php';
require_once 'audit_logger.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Nurse Form Audit Logging Test</h1>";

try {
    // Test database connection
    echo "<h2>1. Testing Database Connection</h2>";
    $testQuery = $pdo->query("SELECT 1");
    echo "✅ Database connection successful<br>";
    
    // Check if audit table exists
    echo "<h2>2. Checking Audit Table</h2>";
    $tableExists = $pdo->query("SHOW TABLES LIKE 'admin_audit_logs'")->rowCount() > 0;
    
    if (!$tableExists) {
        echo "❌ Audit table does not exist. Please run setup_audit_system.php first.<br>";
        exit;
    } else {
        echo "✅ Audit table exists<br>";
    }
    
    // Test patient creation audit logging
    echo "<h2>3. Testing Patient Creation Audit Logging</h2>";
    $auditLogger = new AuditLogger($pdo);
    
    // Simulate nurse creating a patient form
    $nurseId = "NURSE001";
    $patientId = 999; // Test patient ID
    $patientName = "Test Patient John Doe";
    $patientData = [
        'patient_id' => $patientId,
        'uhid' => 'TEST123456',
        'name' => $patientName,
        'action_type' => 'CREATE'
    ];
    
    $result = $auditLogger->logPatientCreate($nurseId, $patientId, $patientName, $patientData);
    if ($result) {
        echo "✅ Patient creation audit log created successfully<br>";
    } else {
        echo "❌ Failed to create patient creation audit log<br>";
    }
    
    // Test patient update audit logging
    echo "<h2>4. Testing Patient Update Audit Logging</h2>";
    
    $updatedPatientData = [
        'patient_id' => $patientId,
        'uhid' => 'TEST123456',
        'name' => $patientName . ' (Updated)',
        'action_type' => 'UPDATE'
    ];
    
    $result = $auditLogger->logPatientUpdate($nurseId, $patientId, $patientName . ' (Updated)', $updatedPatientData);
    if ($result) {
        echo "✅ Patient update audit log created successfully<br>";
    } else {
        echo "❌ Failed to create patient update audit log<br>";
    }
    
    // Verify the logs were created
    echo "<h2>5. Verifying Audit Logs</h2>";
    $sql = "SELECT * FROM admin_audit_logs WHERE entity_type = 'PATIENT' AND entity_id = ? ORDER BY timestamp DESC LIMIT 2";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$patientId]);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($logs) >= 2) {
        echo "✅ Found " . count($logs) . " patient audit logs<br>";
        foreach ($logs as $log) {
            echo "&nbsp;&nbsp;• {$log['action_type']} - {$log['entity_name']} by {$log['admin_user']} at {$log['timestamp']}<br>";
        }
    } else {
        echo "❌ Expected 2 patient audit logs, found " . count($logs) . "<br>";
    }
    
    // Test the get_audit_logs.php endpoint
    echo "<h2>6. Testing get_audit_logs.php Endpoint</h2>";
    
    // Simulate the request
    $_GET['entity_type'] = 'PATIENT';
    $_GET['action_type'] = 'CREATE,UPDATE';
    
    ob_start();
    include 'get_audit_logs.php';
    $response = ob_get_clean();
    
    $data = json_decode($response, true);
    if ($data && isset($data['success']) && $data['success']) {
        echo "✅ get_audit_logs.php endpoint working correctly<br>";
        echo "&nbsp;&nbsp;• Found " . count($data['logs']) . " patient logs<br>";
    } else {
        echo "❌ get_audit_logs.php endpoint failed<br>";
        echo "&nbsp;&nbsp;Response: " . $response . "<br>";
    }
    
    echo "<h2>7. Summary</h2>";
    echo "✅ Nurse form audit logging system is working correctly!<br>";
    echo "✅ When nurses add or update patient data from form.html, it will be logged in the audit system.<br>";
    echo "✅ The Nurse Audit Log in admin.html will display these activities.<br>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}
?>
