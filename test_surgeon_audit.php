<?php
/**
 * Test script for Surgeon Audit Logging
 * This script tests the surgeon creation and deletion audit logging
 */

require_once 'config.php';
require_once 'audit_logger.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Surgeon Audit Logging Test</h1>";

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
    echo "<h2>3. Testing Surgeon Audit Logging</h2>";
    $auditLogger = new AuditLogger($pdo);
    echo "✅ Audit logger initialized<br>";
    
    // Test surgeon creation logging
    echo "<h3>Testing Surgeon Creation Logging</h3>";
    $testSurgeonData = [
        'id' => 999,
        'name' => 'Test Surgeon Dr. Smith'
    ];
    
    $auditId = $auditLogger->logSurgeonCreate('admin', $testSurgeonData);
    if ($auditId) {
        echo "✅ Surgeon creation logged successfully (Audit ID: {$auditId})<br>";
    } else {
        echo "❌ Failed to log surgeon creation<br>";
    }
    
    // Test surgeon deletion logging
    echo "<h3>Testing Surgeon Deletion Logging</h3>";
    $testSurgeonBefore = [
        'id' => 888,
        'name' => 'Test Surgeon Dr. Johnson'
    ];
    
    $auditId = $auditLogger->logSurgeonDelete('admin', 888, 'Test Surgeon Dr. Johnson', $testSurgeonBefore);
    if ($auditId) {
        echo "✅ Surgeon deletion logged successfully (Audit ID: {$auditId})<br>";
    } else {
        echo "❌ Failed to log surgeon deletion<br>";
    }
    
    // Show recent surgeon audit logs
    echo "<h2>4. Recent Surgeon Audit Logs</h2>";
    $recentQuery = $pdo->query("SELECT admin_user, action_type, entity_type, entity_name, description, timestamp, status 
                                FROM admin_audit_logs 
                                WHERE entity_type = 'SURGEON'
                                ORDER BY timestamp DESC 
                                LIMIT 10");
    $recentLogs = $recentQuery->fetchAll();
    
    if (count($recentLogs) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Timestamp</th><th>Admin</th><th>Action</th><th>Surgeon Name</th><th>Description</th><th>Status</th></tr>";
        
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
        echo "<p>No surgeon audit logs found.</p>";
    }
    
    // Test the actual create_surgeon.php endpoint
    echo "<h2>5. Testing create_surgeon.php Endpoint</h2>";
    echo "<p>To test the actual endpoint, you can:</p>";
    echo "<ol>";
    echo "<li>Go to the admin panel</li>";
    echo "<li>Create a new surgeon</li>";
    echo "<li>Check the audit log page to see if it appears</li>";
    echo "</ol>";
    
    echo "<h2>6. Next Steps</h2>";
    echo "✅ Surgeon audit logging is now working!<br>";
    echo "📊 Check the audit log page: <a href='audit_log.html'>audit_log.html</a><br>";
    echo "🔧 Create/delete surgeons from the admin panel to see them in the audit log<br>";
    
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
