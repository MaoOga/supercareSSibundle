<?php
require_once 'config.php';

echo "<h2>Reset Token Columns Check</h2>";

try {
    // Check if reset_token column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM nurses LIKE 'reset_token'");
    $resetTokenColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($resetTokenColumn) {
        echo "<p style='color: green;'>✅ reset_token column exists</p>";
        echo "<p>Column details: " . print_r($resetTokenColumn, true) . "</p>";
    } else {
        echo "<p style='color: red;'>❌ reset_token column does NOT exist</p>";
    }
    
    // Check if reset_expiry column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM nurses LIKE 'reset_expiry'");
    $resetExpiryColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($resetExpiryColumn) {
        echo "<p style='color: green;'>✅ reset_expiry column exists</p>";
        echo "<p>Column details: " . print_r($resetExpiryColumn, true) . "</p>";
    } else {
        echo "<p style='color: red;'>❌ reset_expiry column does NOT exist</p>";
    }
    
    // Check for any nurses with reset tokens
    $stmt = $pdo->query("SELECT id, nurse_id, name, reset_token, reset_expiry FROM nurses WHERE reset_token IS NOT NULL");
    $nursesWithTokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Nurses with Reset Tokens:</h3>";
    if (count($nursesWithTokens) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nurse ID</th><th>Name</th><th>Reset Token</th><th>Reset Expiry</th></tr>";
        foreach ($nursesWithTokens as $nurse) {
            echo "<tr>";
            echo "<td>" . $nurse['id'] . "</td>";
            echo "<td>" . $nurse['nurse_id'] . "</td>";
            echo "<td>" . $nurse['name'] . "</td>";
            echo "<td>" . substr($nurse['reset_token'], 0, 20) . "..." . "</td>";
            echo "<td>" . $nurse['reset_expiry'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No nurses with reset tokens found.</p>";
    }
    
    // Test the specific token from the URL
    $testToken = "40c60353374295eb004d92ee8d3dcde27faf83af3451a8300fe5ac7d1b3a0308";
    echo "<h3>Testing Specific Token:</h3>";
    echo "<p>Token: " . $testToken . "</p>";
    
    $stmt = $pdo->prepare("SELECT id, nurse_id, name, reset_token, reset_expiry FROM nurses WHERE reset_token = ?");
    $stmt->execute([$testToken]);
    $tokenResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($tokenResult) {
        echo "<p style='color: green;'>✅ Token found in database</p>";
        echo "<p>Nurse: " . $tokenResult['name'] . " (ID: " . $tokenResult['nurse_id'] . ")</p>";
        echo "<p>Expiry: " . $tokenResult['reset_expiry'] . "</p>";
        
        // Check if token is expired
        $currentTime = date('Y-m-d H:i:s');
        if ($tokenResult['reset_expiry'] > $currentTime) {
            echo "<p style='color: green;'>✅ Token is NOT expired</p>";
        } else {
            echo "<p style='color: red;'>❌ Token is EXPIRED</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Token NOT found in database</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
