<?php
require_once 'config.php';

echo "<h2>Reset Token Issue Fix</h2>";

try {
    // Check if reset_token column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM nurses LIKE 'reset_token'");
    $resetTokenColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$resetTokenColumn) {
        echo "<p style='color: orange;'>⚠️ reset_token column does NOT exist. Adding it now...</p>";
        
        // Add reset_token column
        $pdo->exec("ALTER TABLE nurses ADD COLUMN reset_token VARCHAR(64) NULL");
        echo "<p style='color: green;'>✅ reset_token column added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ reset_token column already exists</p>";
    }
    
    // Check if reset_expiry column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM nurses LIKE 'reset_expiry'");
    $resetExpiryColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$resetExpiryColumn) {
        echo "<p style='color: orange;'>⚠️ reset_expiry column does NOT exist. Adding it now...</p>";
        
        // Add reset_expiry column
        $pdo->exec("ALTER TABLE nurses ADD COLUMN reset_expiry TIMESTAMP NULL");
        echo "<p style='color: green;'>✅ reset_expiry column added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ reset_expiry column already exists</p>";
    }
    
    // Check if index exists
    $stmt = $pdo->query("SHOW INDEX FROM nurses WHERE Key_name = 'idx_reset_token'");
    $indexExists = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$indexExists) {
        echo "<p style='color: orange;'>⚠️ reset_token index does NOT exist. Adding it now...</p>";
        
        // Add index
        $pdo->exec("CREATE INDEX idx_reset_token ON nurses(reset_token)");
        echo "<p style='color: green;'>✅ reset_token index added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ reset_token index already exists</p>";
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
            echo "<p>Current time: " . $currentTime . "</p>";
            echo "<p>Time until expiry: " . strtotime($tokenResult['reset_expiry']) - time() . " seconds</p>";
        } else {
            echo "<p style='color: red;'>❌ Token is EXPIRED</p>";
            echo "<p>Current time: " . $currentTime . "</p>";
            echo "<p>Token expired: " . $tokenResult['reset_expiry'] . "</p>";
            
            // Clear expired token
            $stmt = $pdo->prepare("UPDATE nurses SET reset_token = NULL, reset_expiry = NULL WHERE id = ?");
            $stmt->execute([$tokenResult['id']]);
            echo "<p style='color: orange;'>⚠️ Expired token cleared from database</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Token NOT found in database</p>";
        echo "<p>This could mean:</p>";
        echo "<ul>";
        echo "<li>The token has already been used</li>";
        echo "<li>The token has expired and been cleared</li>";
        echo "<li>The token was never generated properly</li>";
        echo "</ul>";
    }
    
    // Show all nurses with reset tokens
    $stmt = $pdo->query("SELECT id, nurse_id, name, reset_token, reset_expiry FROM nurses WHERE reset_token IS NOT NULL");
    $nursesWithTokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>All Nurses with Reset Tokens:</h3>";
    if (count($nursesWithTokens) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nurse ID</th><th>Name</th><th>Reset Token (first 20 chars)</th><th>Reset Expiry</th><th>Status</th></tr>";
        foreach ($nursesWithTokens as $nurse) {
            $currentTime = date('Y-m-d H:i:s');
            $isExpired = $nurse['reset_expiry'] <= $currentTime;
            $status = $isExpired ? "EXPIRED" : "VALID";
            $statusColor = $isExpired ? "red" : "green";
            
            echo "<tr>";
            echo "<td>" . $nurse['id'] . "</td>";
            echo "<td>" . $nurse['nurse_id'] . "</td>";
            echo "<td>" . $nurse['name'] . "</td>";
            echo "<td>" . substr($nurse['reset_token'], 0, 20) . "..." . "</td>";
            echo "<td>" . $nurse['reset_expiry'] . "</td>";
            echo "<td style='color: " . $statusColor . ";'>" . $status . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No nurses with reset tokens found.</p>";
    }
    
    echo "<h3>Next Steps:</h3>";
    echo "<p>1. If the token was expired, request a new password reset link</p>";
    echo "<p>2. If the token was not found, the password reset link may have already been used</p>";
    echo "<p>3. Go to <a href='forgot_password.html'>forgot_password.html</a> to request a new reset link</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
