<?php
// Database Structure Check for SSI Bundle System
// This script verifies that all required columns exist

require_once 'config.php';

echo "<h2>Database Structure Check</h2>";

try {
    // Check if nurses table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'nurses'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "<p style='color: green;'>✓ Nurses table exists</p>";
        
        // Get table structure
        $stmt = $pdo->query("DESCRIBE nurses");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Nurses Table Structure:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        $requiredColumns = ['id', 'nurse_id', 'name', 'email', 'password', 'role', 'reset_token', 'reset_expiry', 'created_at', 'updated_at'];
        $foundColumns = [];
        
        foreach ($columns as $column) {
            $foundColumns[] = $column['Field'];
            echo "<tr>";
            echo "<td>" . $column['Field'] . "</td>";
            echo "<td>" . $column['Type'] . "</td>";
            echo "<td>" . $column['Null'] . "</td>";
            echo "<td>" . $column['Key'] . "</td>";
            echo "<td>" . $column['Default'] . "</td>";
            echo "<td>" . $column['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check for missing columns
        $missingColumns = array_diff($requiredColumns, $foundColumns);
        if (empty($missingColumns)) {
            echo "<p style='color: green;'>✓ All required columns exist</p>";
        } else {
            echo "<p style='color: red;'>✗ Missing columns: " . implode(', ', $missingColumns) . "</p>";
            
            // Provide SQL to add missing columns
            echo "<h3>SQL to Add Missing Columns:</h3>";
            echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
            foreach ($missingColumns as $column) {
                switch ($column) {
                    case 'reset_token':
                        echo "ALTER TABLE nurses ADD COLUMN reset_token VARCHAR(64) NULL;\n";
                        break;
                    case 'reset_expiry':
                        echo "ALTER TABLE nurses ADD COLUMN reset_expiry TIMESTAMP NULL;\n";
                        break;
                    case 'created_at':
                        echo "ALTER TABLE nurses ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;\n";
                        break;
                    case 'updated_at':
                        echo "ALTER TABLE nurses ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;\n";
                        break;
                }
            }
            echo "</pre>";
        }
        
        // Check for indexes
        echo "<h3>Indexes:</h3>";
        $stmt = $pdo->query("SHOW INDEX FROM nurses");
        $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($indexes) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>Index Name</th><th>Column</th><th>Non-Unique</th></tr>";
            foreach ($indexes as $index) {
                echo "<tr>";
                echo "<td>" . $index['Key_name'] . "</td>";
                echo "<td>" . $index['Column_name'] . "</td>";
                echo "<td>" . $index['Non_unique'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No indexes found.</p>";
        }
        
        // Check if reset_token index exists
        $resetTokenIndexExists = false;
        foreach ($indexes as $index) {
            if ($index['Key_name'] === 'idx_reset_token') {
                $resetTokenIndexExists = true;
                break;
            }
        }
        
        if ($resetTokenIndexExists) {
            echo "<p style='color: green;'>✓ Reset token index exists</p>";
        } else {
            echo "<p style='color: orange;'>⚠ Reset token index missing. Add with: CREATE INDEX idx_reset_token ON nurses(reset_token);</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ Nurses table does not exist</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Test data insertion
echo "<h3>Test Data Insertion</h3>";
try {
    // Check if we can insert a test record
    $testNurseId = 'TEST_' . time();
    $testEmail = 'test_' . time() . '@example.com';
    
    $stmt = $pdo->prepare("INSERT INTO nurses (nurse_id, name, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $hashedPassword = password_hash('test123', PASSWORD_DEFAULT);
    $stmt->execute([$testNurseId, 'Test Nurse', $testEmail, $hashedPassword, 'nurse']);
    
    echo "<p style='color: green;'>✓ Test record inserted successfully</p>";
    
    // Clean up test record
    $stmt = $pdo->prepare("DELETE FROM nurses WHERE nurse_id = ?");
    $stmt->execute([$testNurseId]);
    echo "<p style='color: green;'>✓ Test record cleaned up</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error inserting test record: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>If any columns are missing, run the provided SQL commands</li>";
echo "<li>Test the password reset functionality again</li>";
echo "<li>Use <a href='reset_token_debug.php'>reset_token_debug.php</a> to troubleshoot token issues</li>";
echo "</ol>";
?>
