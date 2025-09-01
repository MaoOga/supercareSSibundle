<?php
require_once 'config.php';

echo "<h1>Audit Table Check</h1>";

try {
    // Check if audit table exists
    $tableExists = $pdo->query("SHOW TABLES LIKE 'admin_audit_logs'")->rowCount() > 0;
    
    if ($tableExists) {
        echo "<h2>✅ Audit table exists</h2>";
        
        // Check table structure
        echo "<h3>Table Structure:</h3>";
        $columns = $pdo->query("DESCRIBE admin_audit_logs")->fetchAll();
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Default']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check total count
        $totalCount = $pdo->query("SELECT COUNT(*) FROM admin_audit_logs")->fetchColumn();
        echo "<h3>Total audit logs: {$totalCount}</h3>";
        
        // Check recent logs
        echo "<h3>Recent Audit Logs (Last 10):</h3>";
        $recentLogs = $pdo->query("SELECT * FROM admin_audit_logs ORDER BY timestamp DESC LIMIT 10")->fetchAll();
        
        if (count($recentLogs) > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Timestamp</th><th>Admin User</th><th>Action</th><th>Entity Type</th><th>Entity ID</th><th>Description</th></tr>";
            
            foreach ($recentLogs as $log) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($log['audit_id']) . "</td>";
                echo "<td>" . htmlspecialchars($log['timestamp']) . "</td>";
                echo "<td>" . htmlspecialchars($log['admin_user']) . "</td>";
                echo "<td>" . htmlspecialchars($log['action_type']) . "</td>";
                echo "<td>" . htmlspecialchars($log['entity_type']) . "</td>";
                echo "<td>" . htmlspecialchars($log['entity_id']) . "</td>";
                echo "<td>" . htmlspecialchars($log['description']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No audit logs found.</p>";
        }
        
        // Check for patient-related logs specifically
        echo "<h3>Patient-related Audit Logs:</h3>";
        $patientLogs = $pdo->query("SELECT * FROM admin_audit_logs WHERE entity_type = 'PATIENT' ORDER BY timestamp DESC LIMIT 10")->fetchAll();
        
        if (count($patientLogs) > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Timestamp</th><th>Admin User</th><th>Action</th><th>Entity ID</th><th>Description</th></tr>";
            
            foreach ($patientLogs as $log) {
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
            echo "<p>No patient-related audit logs found.</p>";
        }
        
    } else {
        echo "<h2>❌ Audit table does not exist</h2>";
        echo "<p>Please run <a href='../audit/setup_audit_system.php'>setup_audit_system.php</a> to create the audit table.</p>";
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
h3 { color: #374151; margin-top: 20px; }
table { margin-top: 10px; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f3f4f6; }
</style>
