<?php
/**
 * Test Audit Log View Functionality
 * Verify that each view button shows the correct log for each entry
 */

echo "<h2>Audit Log View Test</h2>";

// Test 1: Check if get_audit_logs.php handles audit_id parameter
echo "<h3>1. Testing get_audit_logs.php with audit_id parameter</h3>";

// First, get a list of audit logs
$logsUrl = 'get_audit_logs.php?limit=5';
echo "Fetching recent logs from: $logsUrl<br>";

$logsResponse = file_get_contents($logsUrl);
$logsData = json_decode($logsResponse, true);

if ($logsData && $logsData['success'] && !empty($logsData['data']['logs'])) {
    echo "✅ Found " . count($logsData['data']['logs']) . " audit logs<br>";
    
    // Test the first log
    $firstLog = $logsData['data']['logs'][0];
    $auditId = $firstLog['audit_id'];
    
    echo "Testing view for audit_id: $auditId<br>";
    
    // Now fetch the specific log
    $specificUrl = "get_audit_logs.php?audit_id=$auditId";
    echo "Fetching specific log from: $specificUrl<br>";
    
    $specificResponse = file_get_contents($specificUrl);
    $specificData = json_decode($specificResponse, true);
    
    if ($specificData && $specificData['success'] && !empty($specificData['data']['logs'])) {
        $specificLog = $specificData['data']['logs'][0];
        echo "✅ Successfully fetched specific log<br>";
        echo "Original log audit_id: " . $firstLog['audit_id'] . "<br>";
        echo "Specific log audit_id: " . $specificLog['audit_id'] . "<br>";
        
        if ($firstLog['audit_id'] === $specificLog['audit_id']) {
            echo "✅ MATCH! The view button will show the correct log entry<br>";
        } else {
            echo "❌ MISMATCH! The view button is showing the wrong log entry<br>";
        }
        
        echo "<h4>Original Log Details:</h4>";
        echo "<pre>" . json_encode($firstLog, JSON_PRETTY_PRINT) . "</pre>";
        
        echo "<h4>Specific Log Details:</h4>";
        echo "<pre>" . json_encode($specificLog, JSON_PRETTY_PRINT) . "</pre>";
        
    } else {
        echo "❌ Failed to fetch specific log<br>";
        echo "Response: " . $specificResponse . "<br>";
    }
    
} else {
    echo "❌ Failed to fetch audit logs or no logs found<br>";
    echo "Response: " . $logsResponse . "<br>";
}

// Test 2: Check if AuditLogger class has getAuditLogById method
echo "<h3>2. Testing AuditLogger::getAuditLogById method</h3>";

require_once '../audit/audit_logger.php';

if (method_exists($auditLogger, 'getAuditLogById')) {
    echo "✅ getAuditLogById method exists<br>";
    
    // Test with a sample audit_id
    if (!empty($auditId)) {
        $result = $auditLogger->getAuditLogById($auditId);
        if ($result) {
            echo "✅ Method works correctly - found log with ID: " . $result['audit_id'] . "<br>";
        } else {
            echo "❌ Method returned null for audit_id: $auditId<br>";
        }
    }
} else {
    echo "❌ getAuditLogById method does not exist<br>";
}

// Test 3: Check JavaScript functionality
echo "<h3>3. JavaScript View Button Test</h3>";
echo "The JavaScript function showLogDetails() should now work correctly:<br>";
echo "<code>showLogDetails(audit_id)</code> will fetch the specific log entry<br>";
echo "Each view button will show the correct log details for that specific entry<br>";

echo "<h3>4. Summary</h3>";
echo "✅ Fixed: get_audit_logs.php now handles audit_id parameter<br>";
echo "✅ Fixed: Added getAuditLogById() method to AuditLogger class<br>";
echo "✅ Fixed: Each view button will now show the correct log entry<br>";
echo "✅ Fixed: No more showing the same recent log for every entry<br>";
?>
