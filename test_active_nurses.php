<?php
/**
 * Test script to verify active nurses count functionality
 */

require_once 'config.php';

echo "<h1>Active Nurses Count Test</h1>";

try {
    // Test the active nurses query
    $sql = "SELECT DISTINCT 
                CASE 
                    WHEN admin_user = 'SYSTEM' AND entity_type = 'NURSE' THEN entity_name
                    ELSE admin_user 
                END as nurse_identifier,
                COUNT(*) as count 
            FROM admin_audit_logs 
            WHERE timestamp >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            AND (entity_type = 'NURSE' OR admin_user LIKE 'NURSE%')
            GROUP BY nurse_identifier 
            ORDER BY count DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $activeNurses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Active Nurses (Last 30 Days):</h2>";
    echo "<p>Total active nurses: " . count($activeNurses) . "</p>";
    
    if (count($activeNurses) > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Nurse ID</th><th>Activity Count</th></tr>";
        foreach ($activeNurses as $nurse) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($nurse['nurse_identifier']) . "</td>";
            echo "<td>" . $nurse['count'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No active nurses found in the last 30 days.</p>";
    }
    
    // Show all admin_user entries for debugging
    echo "<h2>All Admin Users in Audit Log (Last 30 Days):</h2>";
    $sql = "SELECT DISTINCT admin_user, COUNT(*) as count 
            FROM admin_audit_logs 
            WHERE timestamp >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY admin_user 
            ORDER BY count DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>User</th><th>Activity Count</th><th>Type</th></tr>";
    foreach ($allUsers as $user) {
        $type = '';
        if ($user['admin_user'] === 'SYSTEM') {
            $type = 'System';
        } elseif (strpos($user['admin_user'], 'NURSE') === 0) {
            $type = 'Nurse';
        } else {
            $type = 'Admin';
        }
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user['admin_user']) . "</td>";
        echo "<td>" . $user['count'] . "</td>";
        echo "<td>" . $type . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Test Results:</h2>";
    echo "<p>✅ Active nurses count: " . count($activeNurses) . "</p>";
    echo "<p>✅ Total users in audit log: " . count($allUsers) . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
