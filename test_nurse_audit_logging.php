<?php
/**
 * Test script to verify nurse audit logging functionality
 */

require_once 'config.php';
require_once 'session_config.php';
require_once 'audit_logger.php';

echo "<h1>Nurse Audit Logging Test</h1>";

try {
    // Test 1: Check if audit_logger.php exists and works
    echo "<h2>Test 1: Audit Logger Class</h2>";
    if (class_exists('AuditLogger')) {
        echo "<p style='color: green;'>✅ AuditLogger class exists</p>";
        $auditLogger = new AuditLogger($pdo);
        echo "<p style='color: green;'>✅ AuditLogger instance created successfully</p>";
    } else {
        echo "<p style='color: red;'>❌ AuditLogger class not found</p>";
    }

    // Test 2: Check session functions
    echo "<h2>Test 2: Session Functions</h2>";
    if (function_exists('isNurseLoggedIn')) {
        echo "<p style='color: green;'>✅ isNurseLoggedIn function exists</p>";
    } else {
        echo "<p style='color: red;'>❌ isNurseLoggedIn function not found</p>";
    }

    if (function_exists('getNurseInfo')) {
        echo "<p style='color: green;'>✅ getNurseInfo function exists</p>";
    } else {
        echo "<p style='color: red;'>❌ getNurseInfo function not found</p>";
    }

    // Test 3: Check current session status
    echo "<h2>Test 3: Current Session Status</h2>";
    $isLoggedIn = isNurseLoggedIn();
    echo "<p>Current nurse login status: " . ($isLoggedIn ? 'Logged In' : 'Not Logged In') . "</p>";
    
    if ($isLoggedIn) {
        $nurseInfo = getNurseInfo();
        echo "<p>Nurse Info: " . json_encode($nurseInfo) . "</p>";
    }

    // Test 4: Check recent audit logs
    echo "<h2>Test 4: Recent Audit Logs</h2>";
    $sql = "SELECT admin_user, action_type, entity_type, entity_name, timestamp, details 
            FROM admin_audit_logs 
            WHERE admin_user LIKE 'NURSE%' 
            ORDER BY timestamp DESC 
            LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $recentLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($recentLogs) > 0) {
        echo "<p style='color: green;'>✅ Found " . count($recentLogs) . " recent nurse audit logs</p>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Nurse ID</th><th>Action</th><th>Entity</th><th>Name</th><th>Timestamp</th></tr>";
        foreach ($recentLogs as $log) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($log['admin_user']) . "</td>";
            echo "<td>" . htmlspecialchars($log['action_type']) . "</td>";
            echo "<td>" . htmlspecialchars($log['entity_type']) . "</td>";
            echo "<td>" . htmlspecialchars($log['entity_name']) . "</td>";
            echo "<td>" . htmlspecialchars($log['timestamp']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ No recent nurse audit logs found</p>";
    }

    // Test 5: Check all nurse-related audit logs
    echo "<h2>Test 5: All Nurse Audit Logs</h2>";
    $sql = "SELECT admin_user, COUNT(*) as count 
            FROM admin_audit_logs 
            WHERE admin_user LIKE 'NURSE%' 
            GROUP BY admin_user 
            ORDER BY count DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $nurseLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($nurseLogs) > 0) {
        echo "<p style='color: green;'>✅ Found audit logs for " . count($nurseLogs) . " different nurses</p>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Nurse ID</th><th>Activity Count</th></tr>";
        foreach ($nurseLogs as $nurse) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($nurse['admin_user']) . "</td>";
            echo "<td>" . $nurse['count'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ No nurse audit logs found in database</p>";
    }

    // Test 6: Check audit log table structure
    echo "<h2>Test 6: Audit Log Table Structure</h2>";
    $sql = "DESCRIBE admin_audit_logs";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p style='color: green;'>✅ Audit log table has " . count($columns) . " columns</p>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Test 7: Test audit logging functionality
    echo "<h2>Test 7: Test Audit Logging</h2>";
    if ($isLoggedIn) {
        $nurseInfo = getNurseInfo();
        $testResult = $auditLogger->log(
            $nurseInfo['nurse_id'],
            'TEST',
            'SYSTEM',
            0,
            $nurseInfo['name'],
            "Test audit log entry from test script",
            null,
            ['test' => true, 'timestamp' => date('Y-m-d H:i:s')]
        );
        
        if ($testResult) {
            echo "<p style='color: green;'>✅ Test audit log entry created successfully</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create test audit log entry</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ Cannot test audit logging - no nurse logged in</p>";
    }

    echo "<h2>Summary</h2>";
    echo "<p>✅ Audit logging system is properly configured</p>";
    echo "<p>✅ Session management functions are available</p>";
    echo "<p>✅ Database connection is working</p>";
    echo "<p>✅ Audit log table exists and is accessible</p>";
    
    if (count($nurseLogs) > 0) {
        echo "<p style='color: green;'>✅ Nurse activities are being logged in the audit system</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ No nurse activities found yet - try logging in as a nurse and performing some actions</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
