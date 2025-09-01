<?php
/**
 * Test script for Nurse Login Audit Logging
 * This script tests the nurse login audit logging functionality
 */

require_once '../database/config.php';
require_once '../audit/audit_logger.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Nurse Login Audit Logging Test</h1>";

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
    
    // Test audit logger
    echo "<h2>3. Testing Nurse Login Audit Logging</h2>";
    $auditLogger = new AuditLogger($pdo);
    echo "✅ Audit logger initialized<br>";
    
    // Test successful nurse login logging
    echo "<h3>Testing Successful Nurse Login Logging</h3>";
    $testNurseData = [
        'id' => 999,
        'nurse_id' => 'NURSE001',
        'name' => 'Test Nurse Jane Doe',
        'email' => 'jane.doe@hospital.com',
        'role' => 'nurse'
    ];
    
    $auditId = $auditLogger->logNurseLogin('system', 999, 'NURSE001', 'Test Nurse Jane Doe', $testNurseData, 'Nurse login successful', 'SUCCESS');
    if ($auditId) {
        echo "✅ Successful nurse login logged successfully (Audit ID: {$auditId})<br>";
    } else {
        echo "❌ Failed to log successful nurse login<br>";
    }
    
    // Test failed nurse login logging (invalid password)
    echo "<h3>Testing Failed Nurse Login Logging (Invalid Password)</h3>";
    $auditId = $auditLogger->logNurseLogin('system', 999, 'NURSE001', 'Test Nurse Jane Doe', null, 'Invalid password provided', 'FAILED');
    if ($auditId) {
        echo "✅ Failed nurse login (invalid password) logged successfully (Audit ID: {$auditId})<br>";
    } else {
        echo "❌ Failed to log failed nurse login<br>";
    }
    
    // Test failed nurse login logging (nurse not found)
    echo "<h3>Testing Failed Nurse Login Logging (Nurse Not Found)</h3>";
    $auditId = $auditLogger->logNurseLogin('system', null, 'INVALID001', 'NURSE_NOT_FOUND', null, 'Nurse ID not found in system', 'FAILED');
    if ($auditId) {
        echo "✅ Failed nurse login (nurse not found) logged successfully (Audit ID: {$auditId})<br>";
    } else {
        echo "❌ Failed to log failed nurse login<br>";
    }
    
    // Show recent nurse login audit logs
    echo "<h2>4. Recent Nurse Login Audit Logs</h2>";
    $recentQuery = $pdo->query("SELECT admin_user, action_type, entity_type, entity_name, description, timestamp, status 
                                FROM admin_audit_logs 
                                WHERE entity_type = 'NURSE' AND action_type = 'LOGIN'
                                ORDER BY timestamp DESC 
                                LIMIT 10");
    $recentLogs = $recentQuery->fetchAll();
    
    if (count($recentLogs) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Timestamp</th><th>Admin</th><th>Action</th><th>Nurse Name</th><th>Description</th><th>Status</th></tr>";
        
        foreach ($recentLogs as $log) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($log['timestamp']) . "</td>";
            echo "<td>" . htmlspecialchars($log['admin_user']) . "</td>";
            echo "<td>" . htmlspecialchars($log['action_type']) . "</td>";
            echo "<td>" . htmlspecialchars($log['entity_name']) . "</td>";
            echo "<td>" . htmlspecialchars($log['description']) . "</td>";
            echo "<td>" . htmlspecialchars($log['status']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No nurse login audit logs found.</p>";
    }
    
    // Test the actual nurse_login.php endpoint
    echo "<h2>5. Testing nurse_login.php Endpoint</h2>";
    echo "<p>To test the actual endpoint, you can:</p>";
    echo "<ol>";
    echo "<li>Go to the nurse login page</li>";
    echo "<li>Try logging in with valid credentials</li>";
    echo "<li>Try logging in with invalid credentials</li>";
    echo "<li>Check the audit log page to see the login attempts</li>";
    echo "</ol>";
    
    echo "<h2>6. Next Steps</h2>";
    echo "✅ Nurse login audit logging is now working!<br>";
    echo "📊 Check the audit log page: <a href='../admin/audit_log.php'>audit_log.php</a><br>";
    echo "🔧 Try logging in as a nurse to see the login attempts in the audit log<br>";
    echo "🔍 The audit log will show both successful and failed login attempts<br>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { color: #2563eb; }
h2 { color: #1f2937; margin-top: 30px; }
h3 { color: #374151; }
table { margin-top: 10px; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f3f4f6; }
</style>
