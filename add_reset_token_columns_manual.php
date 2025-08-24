<?php
require_once 'config.php';

echo "<h2>Adding Reset Token Columns to Nurses Table</h2>";

try {
    // Add reset_token column
    $pdo->exec("ALTER TABLE nurses ADD COLUMN reset_token VARCHAR(64) NULL");
    echo "<p style='color: green;'>✅ reset_token column added successfully</p>";
    
    // Add reset_expiry column
    $pdo->exec("ALTER TABLE nurses ADD COLUMN reset_expiry TIMESTAMP NULL");
    echo "<p style='color: green;'>✅ reset_expiry column added successfully</p>";
    
    // Add index for better performance
    $pdo->exec("CREATE INDEX idx_reset_token ON nurses(reset_token)");
    echo "<p style='color: green;'>✅ reset_token index added successfully</p>";
    
    echo "<p style='color: green;'><strong>All reset token columns have been added successfully!</strong></p>";
    echo "<p>You can now use the password reset functionality.</p>";
    
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "<p style='color: orange;'>⚠️ Columns already exist. This is normal if they were already added.</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}
?>
