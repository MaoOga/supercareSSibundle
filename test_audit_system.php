<?php
/**
 * Test script for Audit System
 * This script will test the audit system functionality and populate it with sample data
 */

require_once 'config.php';
require_once 'audit_logger.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Audit System Test</h1>";

try {
    // Test database connection
    echo "<h2>1. Testing Database Connection</h2>";
    $testQuery = $pdo->query("SELECT 1");
    echo "✅ Database connection successful<br>";
    
    // Check if audit table exists
    echo "<h2>2. Checking Audit Table</h2>";
    $tableExists = $pdo->query("SHOW TABLES LIKE 'admin_audit_logs'")->rowCount() > 0;
    
    if (!$tableExists) {
        echo "❌ Audit table does not exist. Creating it...<br>";
        
        // Read and execute the SQL file
        $sql = file_get_contents('create_audit_table.sql');
        $pdo->exec($sql);
        echo "✅ Audit table created successfully<br>";
    } else {
        echo "✅ Audit table exists<br>";
    }
    
    // Test audit logger
    echo "<h2>3. Testing Audit Logger</h2>";
    $auditLogger = new AuditLogger($pdo);
    echo "✅ Audit logger initialized<br>";
    
    // Populate with sample data
    echo "<h2>4. Populating Sample Data</h2>";
    
    // Sample admin users
    $adminUsers = ['admin', 'supervisor', 'manager'];
    
    // Sample actions
    $actions = [
        ['type' => 'CREATE', 'entity' => 'NURSE', 'name' => 'John Doe', 'desc' => 'Created new nurse account'],
        ['type' => 'UPDATE', 'entity' => 'SURGEON', 'name' => 'Dr. Smith', 'desc' => 'Updated surgeon information'],
        ['type' => 'DELETE', 'entity' => 'NURSE', 'name' => 'Jane Wilson', 'desc' => 'Deleted nurse account'],
        ['type' => 'LOGIN', 'entity' => 'SYSTEM', 'name' => 'Admin Login', 'desc' => 'Admin login successful'],
        ['type' => 'BACKUP', 'entity' => 'BACKUP', 'name' => 'Database Backup', 'desc' => 'Database backup created'],
        ['type' => 'EXPORT', 'entity' => 'SYSTEM', 'name' => 'Data Export', 'desc' => 'Exported patient data'],
        ['type' => 'IMPORT', 'entity' => 'SYSTEM', 'name' => 'Data Import', 'desc' => 'Imported nurse data']
    ];
    
    // Generate sample data for the last 30 days
    $sampleCount = 0;
    for ($i = 0; $i < 50; $i++) {
        $action = $actions[array_rand($actions)];
        $adminUser = $adminUsers[array_rand($adminUsers)];
        
        // Random timestamp within last 30 days
        $timestamp = date('Y-m-d H:i:s', strtotime('-' . rand(0, 30) . ' days -' . rand(0, 23) . ' hours -' . rand(0, 59) . ' minutes'));
        
        // Insert sample audit log
        $sql = "INSERT INTO admin_audit_logs (
            timestamp, admin_user, action_type, entity_type, entity_name, 
            description, ip_address, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $timestamp,
            $adminUser,
            $action['type'],
            $action['entity'],
            $action['name'],
            $action['desc'],
            '192.168.1.' . rand(1, 255),
            rand(1, 10) > 1 ? 'SUCCESS' : 'FAILED' // 90% success rate
        ]);
        
        $sampleCount++;
    }
    
    echo "✅ Generated {$sampleCount} sample audit entries<br>";
    
    // Test API endpoints
    echo "<h2>5. Testing API Endpoints</h2>";
    
    // Test get_audit_stats.php
    echo "<h3>Testing get_audit_stats.php</h3>";
    $statsUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/get_audit_stats.php?days=30';
    $statsResponse = file_get_contents($statsUrl);
    $statsData = json_decode($statsResponse, true);
    
    if ($statsData && $statsData['success']) {
        echo "✅ get_audit_stats.php working - Found {$statsData['data']['summary']['total_activities']} activities<br>";
    } else {
        echo "❌ get_audit_stats.php failed<br>";
        echo "Response: " . htmlspecialchars($statsResponse) . "<br>";
    }
    
    // Test get_audit_logs.php
    echo "<h3>Testing get_audit_logs.php</h3>";
    $logsUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/get_audit_logs.php?page=1&limit=10';
    $logsResponse = file_get_contents($logsUrl);
    $logsData = json_decode($logsResponse, true);
    
    if ($logsData && $logsData['success']) {
        echo "✅ get_audit_logs.php working - Found " . count($logsData['data']['logs']) . " logs<br>";
    } else {
        echo "❌ get_audit_logs.php failed<br>";
        echo "Response: " . htmlspecialchars($logsResponse) . "<br>";
    }
    
    // Show current audit log count
    echo "<h2>6. Current Audit Log Status</h2>";
    $countQuery = $pdo->query("SELECT COUNT(*) as total FROM admin_audit_logs");
    $totalLogs = $countQuery->fetch()['total'];
    echo "Total audit logs: {$totalLogs}<br>";
    
    // Show recent logs
    $recentQuery = $pdo->query("SELECT admin_user, action_type, entity_type, entity_name, timestamp, status 
                                FROM admin_audit_logs 
                                ORDER BY timestamp DESC 
                                LIMIT 5");
    $recentLogs = $recentQuery->fetchAll();
    
    echo "<h3>Recent Audit Logs:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Timestamp</th><th>Admin</th><th>Action</th><th>Entity</th><th>Name</th><th>Status</th></tr>";
    
    foreach ($recentLogs as $log) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($log['timestamp']) . "</td>";
        echo "<td>" . htmlspecialchars($log['admin_user']) . "</td>";
        echo "<td>" . htmlspecialchars($log['action_type']) . "</td>";
        echo "<td>" . htmlspecialchars($log['entity_type']) . "</td>";
        echo "<td>" . htmlspecialchars($log['entity_name']) . "</td>";
        echo "<td>" . htmlspecialchars($log['status']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>7. Next Steps</h2>";
    echo "✅ Audit system is now ready!<br>";
    echo "📊 You can now access the audit log page at: <a href='audit_log.html'>audit_log.html</a><br>";
    echo "🔧 The system will automatically log all admin activities<br>";
    
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
