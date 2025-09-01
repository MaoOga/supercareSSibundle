<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
require_once 'config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful<br>";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "<br>";
    exit;
}

echo "<h2>Nurses Table Columns Check</h2>";

// Check if nurses table exists
$tableCheck = $pdo->query("SHOW TABLES LIKE 'nurses'");
if ($tableCheck->rowCount() == 0) {
    echo "<p style='color: red;'>❌ Nurses table does not exist!</p>";
    exit;
}

echo "<p style='color: green;'>✅ Nurses table exists</p>";

// Get table structure
echo "<h3>Available Columns:</h3>";
$columns = $pdo->query("DESCRIBE nurses")->fetchAll(PDO::FETCH_ASSOC);
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Column Name</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
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

// Test the corrected query
echo "<h3>Testing Corrected Query:</h3>";
try {
    $stmt = $pdo->prepare("SELECT id, name, email, created_at FROM nurses ORDER BY created_at DESC");
    $stmt->execute();
    $nurses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p style='color: green;'>✅ Query successful! Found " . count($nurses) . " nurses</p>";
    
    if (!empty($nurses)) {
        echo "<h4>Sample Data:</h4>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Created At</th></tr>";
        foreach (array_slice($nurses, 0, 3) as $nurse) {
            echo "<tr>";
            echo "<td>" . $nurse['id'] . "</td>";
            echo "<td>" . htmlspecialchars($nurse['name']) . "</td>";
            echo "<td>" . htmlspecialchars($nurse['email']) . "</td>";
            echo "<td>" . $nurse['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Query failed: " . $e->getMessage() . "</p>";
}

echo "<br><a href='../super_admin/super_admin_dashboard_simple.html'>Back to Dashboard</a>";
?>
