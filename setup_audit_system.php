<?php
/**
 * Setup script for Audit System
 * Creates the audit table if it doesn't exist
 */

require_once 'config.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Audit System Setup</h1>";

try {
    // Check if audit table exists
    $tableExists = $pdo->query("SHOW TABLES LIKE 'admin_audit_logs'")->rowCount() > 0;
    
    if (!$tableExists) {
        echo "<h2>Creating Audit Table</h2>";
        
        // Read and execute the SQL file
        $sql = file_get_contents('create_audit_table.sql');
        $pdo->exec($sql);
        echo "✅ Audit table created successfully!<br>";
    } else {
        echo "<h2>Audit Table Status</h2>";
        echo "✅ Audit table already exists<br>";
    }
    
    // Verify table structure
    echo "<h2>Table Structure</h2>";
    $columns = $pdo->query("DESCRIBE admin_audit_logs")->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
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
    
    // Check if there are any existing logs
    $logCount = $pdo->query("SELECT COUNT(*) FROM admin_audit_logs")->fetchColumn();
    echo "<h2>Current Status</h2>";
    echo "Total audit logs: {$logCount}<br>";
    
    if ($logCount == 0) {
        echo "<p>No audit logs found. You can run <a href='test_audit_system.php'>test_audit_system.php</a> to populate with sample data.</p>";
    }
    
    echo "<h2>Next Steps</h2>";
    echo "✅ Audit system is ready!<br>";
    echo "📊 Access the audit log page: <a href='audit_log.html'>audit_log.html</a><br>";
    echo "🧪 Test the system: <a href='test_audit_system.php'>test_audit_system.php</a><br>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { color: #2563eb; }
h2 { color: #1f2937; margin-top: 30px; }
table { margin-top: 10px; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f3f4f6; }
</style>
