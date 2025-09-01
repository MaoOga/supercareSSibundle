<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simple database connection
$host = 'localhost';
$dbname = 'supercare_ssi';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful<br>";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "<br>";
    exit;
}

echo "<h2>Nurses Table Check</h2>";

// Check if nurses table exists
$tableCheck = $pdo->query("SHOW TABLES LIKE 'nurses'");
if ($tableCheck->rowCount() == 0) {
    echo "<p style='color: red;'>❌ Nurses table does not exist!</p>";
    
    // Show all tables
    echo "<h3>Available tables:</h3>";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "- $table<br>";
    }
} else {
    echo "<p style='color: green;'>✅ Nurses table exists</p>";
    
    // Get table structure
    echo "<h3>Table Structure:</h3>";
    $columns = $pdo->query("DESCRIBE nurses")->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
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
    
    // Get total count
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM nurses");
    $countStmt->execute();
    $totalCount = $countStmt->fetchColumn();
    echo "<h3>Total Nurses: $totalCount</h3>";
    
    // Get sample data
    if ($totalCount > 0) {
        echo "<h3>Sample Data (first 5 records):</h3>";
        $stmt = $pdo->prepare("SELECT * FROM nurses LIMIT 5");
        $stmt->execute();
        $nurses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($nurses)) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr>";
            foreach (array_keys($nurses[0]) as $header) {
                echo "<th>$header</th>";
            }
            echo "</tr>";
            
            foreach ($nurses as $nurse) {
                echo "<tr>";
                foreach ($nurse as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ No nurses found in the table</p>";
    }
}

echo "<br><a href='../super admin/super_admin_dashboard_simple.html'>Back to Dashboard</a>";
?>
