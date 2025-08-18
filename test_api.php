<?php
// Simple test to verify the API works
require_once 'config.php';
require_once 'audit_logger.php';

try {
    $auditLogger = new AuditLogger($pdo);
    $result = $auditLogger->getAuditLogs([], 1, 10);
    
    echo "API Test Result:\n";
    echo "Success: true\n";
    echo "Logs count: " . count($result['logs']) . "\n";
    echo "Total count: " . $result['total_count'] . "\n";
    echo "Current page: " . $result['page'] . "\n";
    echo "Total pages: " . $result['total_pages'] . "\n";
    
    if (count($result['logs']) > 0) {
        echo "First log entry:\n";
        print_r($result['logs'][0]);
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
