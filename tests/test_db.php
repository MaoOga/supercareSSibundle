<?php
require_once '../database/config.php';

echo "<h1>Database Connection Test</h1>";

try {
    // Test connection
    echo "<h2>Connection Status:</h2>";
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    
    // Show database info
    echo "<h2>Database Information:</h2>";
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $dbInfo = $stmt->fetch();
    echo "<p><strong>Database:</strong> " . $dbInfo['db_name'] . "</p>";
    
    // Show tables
    echo "<h2>Tables in Database:</h2>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "<p style='color: red;'>No tables found in database. Please create the tables first.</p>";
    } else {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    }
    
    // Show table structure for patients table
    if (in_array('patients', $tables)) {
        echo "<h2>Patients Table Structure:</h2>";
        $stmt = $pdo->query("DESCRIBE patients");
        $columns = $stmt->fetchAll();
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
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
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { color: #333; }
h2 { color: #666; margin-top: 30px; }
table { margin-top: 10px; }
th { background-color: #f5f5f5; padding: 8px; }
td { padding: 8px; }
ul { line-height: 1.6; }
</style>
