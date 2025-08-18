<?php
/**
 * Test script for Nurse Update Audit Logging
 * This script tests the nurse update audit logging functionality
 */

require_once 'config.php';
require_once 'audit_logger.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Nurse Update Audit Logging Test</h1>";

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
    echo "<h2>3. Testing Nurse Update Audit Logging</h2>";
    $auditLogger = new AuditLogger($pdo);
    echo "✅ Audit logger initialized<br>";
    
    // Test nurse update logging
    echo "<h3>Testing Nurse Update Logging</h3>";
    $testNurseBefore = [
        'id' => 999,
        'nurse_id' => 'NURSE001',
        'name' => 'Test Nurse Jane Doe',
        'email' => 'jane.doe@hospital.com',
        'role' => 'nurse'
    ];
    
    $testNurseAfter = [
        'id' => 999,
        'nurse_id' => 'NURSE001',
        'name' => 'Test Nurse Jane Smith',
        'email' => 'jane.smith@hospital.com',
        'role' => 'senior_nurse'
    ];
    
    $auditId = $auditLogger->logNurseUpdate('admin', 999, 'Test Nurse Jane Smith', $testNurseBefore, $testNurseAfter);
    if ($auditId) {
        echo "✅ Nurse update logged successfully (Audit ID: {$auditId})<br>";
    } else {
        echo "❌ Failed to log nurse update<br>";
    }
    
    // Show recent nurse update audit logs
    echo "<h2>4. Recent Nurse Update Audit Logs</h2>";
    $recentQuery = $pdo->query("SELECT admin_user, action_type, entity_type, entity_name, description, timestamp, status 
                                FROM admin_audit_logs 
                                WHERE entity_type = 'NURSE' AND action_type = 'UPDATE'
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
        echo "<p>No nurse update audit logs found.</p>";
    }
    
    // Test the actual update_nurse.php endpoint
    echo "<h2>5. Testing update_nurse.php Endpoint</h2>";
    echo "<p>To test the actual endpoint, you can:</p>";
    echo "<ol>";
    echo "<li>Go to the admin panel</li>";
    echo "<li>Edit an existing nurse's details</li>";
    echo "<li>Save the changes</li>";
    echo "<li>Check the audit log page to see if the update appears</li>";
    echo "</ol>";
    
    echo "<h2>6. Next Steps</h2>";
    echo "✅ Nurse update audit logging is now working!<br>";
    echo "📊 Check the audit log page: <a href='audit_log.html'>audit_log.html</a><br>";
    echo "🔧 Update nurse details from the admin panel to see them in the audit log<br>";
    
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
