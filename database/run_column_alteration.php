<?php
// Script to alter antibiotic_usage table columns from DATE to TEXT
require_once 'config.php';

echo "<h2>Altering Antibiotic Date Columns</h2>";
echo "<p>Changing started_on and stopped_on from DATE to TEXT...</p>";

try {
    $pdo->beginTransaction();
    
    // Check current structure
    echo "<h3>Current Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE antibiotic_usage");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    foreach ($columns as $column) {
        $highlight = ($column['Field'] == 'started_on' || $column['Field'] == 'stopped_on') ? "style='background-color: #ffffcc;'" : "";
        echo "<tr $highlight>";
        echo "<td><strong>" . $column['Field'] . "</strong></td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . ($column['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show existing data before changes
    $countStmt = $pdo->query("SELECT COUNT(*) FROM antibiotic_usage");
    $recordCount = $countStmt->fetchColumn();
    echo "<p><strong>Current records in antibiotic_usage table:</strong> $recordCount</p>";
    
    if ($recordCount > 0) {
        echo "<h4>Sample of existing data:</h4>";
        $sampleStmt = $pdo->query("SELECT antibiotic_id, patient_id, drug_name, started_on, stopped_on FROM antibiotic_usage LIMIT 5");
        $samples = $sampleStmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($samples)) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Patient ID</th><th>Drug Name</th><th>Started On</th><th>Stopped On</th></tr>";
            foreach ($samples as $sample) {
                echo "<tr>";
                echo "<td>" . $sample['antibiotic_id'] . "</td>";
                echo "<td>" . $sample['patient_id'] . "</td>";
                echo "<td>" . $sample['drug_name'] . "</td>";
                echo "<td>" . $sample['started_on'] . "</td>";
                echo "<td>" . $sample['stopped_on'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    // Alter the columns
    echo "<h3>Making Changes...</h3>";
    
    // Alter started_on column
    echo "<p>Altering started_on column from DATE to TEXT...</p>";
    $pdo->exec("ALTER TABLE antibiotic_usage MODIFY COLUMN started_on TEXT");
    echo "<p style='color: green;'>✓ started_on column altered successfully</p>";
    
    // Alter stopped_on column
    echo "<p>Altering stopped_on column from DATE to TEXT...</p>";
    $pdo->exec("ALTER TABLE antibiotic_usage MODIFY COLUMN stopped_on TEXT");
    echo "<p style='color: green;'>✓ stopped_on column altered successfully</p>";
    
    // Verify the changes
    echo "<h3>New Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE antibiotic_usage");
    $newColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    foreach ($newColumns as $column) {
        $highlight = ($column['Field'] == 'started_on' || $column['Field'] == 'stopped_on') ? "style='background-color: #ccffcc;'" : "";
        echo "<tr $highlight>";
        echo "<td><strong>" . $column['Field'] . "</strong></td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . ($column['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    $pdo->commit();
    echo "<h3 style='color: green;'>✅ Database alteration completed successfully!</h3>";
    
    echo "<h3>What this means:</h3>";
    echo "<ul>";
    echo "<li>✅ You can now enter any text or number in started_on and stopped_on fields</li>";
    echo "<li>✅ Examples: '111', '243523', 'Day 5', 'Week 2', 'Ongoing', etc.</li>";
    echo "<li>✅ No more date format restrictions</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "<h3 style='color: red;'>❌ Error occurred:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?>
