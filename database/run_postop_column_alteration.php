<?php
// Script to alter post_operative_monitoring table monitoring_date column from DATE to TEXT
require_once 'config.php';

echo "<h2>Altering Post-Operative Monitoring Date Column</h2>";
echo "<p>Changing monitoring_date from DATE to TEXT...</p>";

try {
    $pdo->beginTransaction();
    
    // Check current structure
    echo "<h3>Current Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE post_operative_monitoring");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    foreach ($columns as $column) {
        $highlight = ($column['Field'] == 'monitoring_date') ? "style='background-color: #ffffcc;'" : "";
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
    $countStmt = $pdo->query("SELECT COUNT(*) FROM post_operative_monitoring");
    $recordCount = $countStmt->fetchColumn();
    echo "<p><strong>Current records in post_operative_monitoring table:</strong> $recordCount</p>";
    
    if ($recordCount > 0) {
        echo "<h4>Sample of existing data:</h4>";
        $sampleStmt = $pdo->query("SELECT post_op_id, patient_id, day, monitoring_date, dosage, discharge_fluid, tenderness_pain, swelling, fever FROM post_operative_monitoring LIMIT 5");
        $samples = $sampleStmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($samples)) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Patient ID</th><th>Day</th><th>Monitoring Date</th><th>Dosage</th><th>Discharge Fluid</th><th>Tenderness/Pain</th><th>Swelling</th><th>Fever</th></tr>";
            foreach ($samples as $sample) {
                echo "<tr>";
                echo "<td>" . $sample['post_op_id'] . "</td>";
                echo "<td>" . $sample['patient_id'] . "</td>";
                echo "<td>" . $sample['day'] . "</td>";
                echo "<td>" . $sample['monitoring_date'] . "</td>";
                echo "<td>" . $sample['dosage'] . "</td>";
                echo "<td>" . $sample['discharge_fluid'] . "</td>";
                echo "<td>" . $sample['tenderness_pain'] . "</td>";
                echo "<td>" . $sample['swelling'] . "</td>";
                echo "<td>" . $sample['fever'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    // Alter the column
    echo "<h3>Making Changes...</h3>";
    
    // Alter monitoring_date column
    echo "<p>Altering monitoring_date column from DATE to TEXT...</p>";
    $pdo->exec("ALTER TABLE post_operative_monitoring MODIFY COLUMN monitoring_date TEXT");
    echo "<p style='color: green;'>✓ monitoring_date column altered successfully</p>";
    
    // Verify the changes
    echo "<h3>New Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE post_operative_monitoring");
    $newColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    foreach ($newColumns as $column) {
        $highlight = ($column['Field'] == 'monitoring_date') ? "style='background-color: #ccffcc;'" : "";
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
    echo "<li>✅ You can now enter any text or number in the monitoring_date field</li>";
    echo "<li>✅ Examples: 'Day 1', 'Week 2', '111', '243523', 'Ongoing', etc.</li>";
    echo "<li>✅ No more date format restrictions</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "<h3 style='color: red;'>❌ Error occurred:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?>
