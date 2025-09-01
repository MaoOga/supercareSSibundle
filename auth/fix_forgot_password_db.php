<?php
// Quick fix for forgot password functionality
require_once '../database/config.php';

echo "<h2>🔧 Fixing Forgot Password Database Structure</h2>";

try {
    // Check if reset_token column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM nurses LIKE 'reset_token'");
    $resetTokenColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$resetTokenColumn) {
        echo "<p style='color: orange;'>⚠️ reset_token column missing. Adding it now...</p>";
        $pdo->exec("ALTER TABLE nurses ADD COLUMN reset_token VARCHAR(64) NULL");
        echo "<p style='color: green;'>✅ reset_token column added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ reset_token column already exists</p>";
    }
    
    // Check if reset_expiry column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM nurses LIKE 'reset_expiry'");
    $resetExpiryColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$resetExpiryColumn) {
        echo "<p style='color: orange;'>⚠️ reset_expiry column missing. Adding it now...</p>";
        $pdo->exec("ALTER TABLE nurses ADD COLUMN reset_expiry TIMESTAMP NULL");
        echo "<p style='color: green;'>✅ reset_expiry column added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ reset_expiry column already exists</p>";
    }
    
    // Check if index exists
    $stmt = $pdo->query("SHOW INDEX FROM nurses WHERE Key_name = 'idx_reset_token'");
    $indexExists = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$indexExists) {
        echo "<p style='color: orange;'>⚠️ reset_token index missing. Adding it now...</p>";
        $pdo->exec("CREATE INDEX idx_reset_token ON nurses(reset_token)");
        echo "<p style='color: green;'>✅ reset_token index added successfully</p>";
    } else {
        echo "<p style='color: green;'>✅ reset_token index already exists</p>";
    }
    
    echo "<h3>🎉 Database structure is now ready for forgot password functionality!</h3>";
    echo "<p><a href='../auth/forgot_password.html' style='color: blue;'>→ Test Forgot Password Now</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
