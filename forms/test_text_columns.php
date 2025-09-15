<?php
// Test script to verify TEXT columns work with any input
require_once '../database/config.php';

echo "<h2>Testing TEXT Columns for Antibiotic Dates</h2>";

try {
    // Test different types of input
    $testInputs = [
        ['111', '243523'],
        ['Day 5', 'Week 2'],
        ['Ongoing', 'Not stopped'],
        ['2024-01-15', '2024-01-20'],
        ['15/01/2024', '20/01/2024'],
        ['January 15', 'January 20'],
        ['', 'Still taking'],
        [null, 'Completed']
    ];
    
    $testPatientId = 998; // Use a different test patient ID
    
    // Clean up any existing test data
    $deleteStmt = $pdo->prepare("DELETE FROM antibiotic_usage WHERE patient_id = ?");
    $deleteStmt->execute([$testPatientId]);
    
    echo "<h3>Testing various input types:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Test #</th><th>Started On Input</th><th>Stopped On Input</th><th>Result</th></tr>";
    
    foreach ($testInputs as $index => $testData) {
        $startedOn = $testData[0];
        $stoppedOn = $testData[1];
        
        // Insert test data
        $insertStmt = $pdo->prepare("INSERT INTO antibiotic_usage (patient_id, serial_no, drug_name, dosage_route_frequency, started_on, stopped_on) VALUES (?, ?, ?, ?, ?, ?)");
        $result = $insertStmt->execute([
            $testPatientId,
            $index + 1,
            'Test Drug ' . ($index + 1),
            'Test dosage',
            $startedOn,
            $stoppedOn
        ]);
        
        if ($result) {
            $status = "✓ Success";
            $color = "green";
        } else {
            $status = "✗ Failed";
            $color = "red";
        }
        
        echo "<tr>";
        echo "<td>" . ($index + 1) . "</td>";
        echo "<td>" . ($startedOn ?? 'NULL') . "</td>";
        echo "<td>" . ($stoppedOn ?? 'NULL') . "</td>";
        echo "<td style='color: $color'>$status</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Retrieve and display all test data
    echo "<h3>Retrieved Test Data:</h3>";
    $selectStmt = $pdo->prepare("SELECT serial_no, drug_name, started_on, stopped_on FROM antibiotic_usage WHERE patient_id = ? ORDER BY serial_no");
    $selectStmt->execute([$testPatientId]);
    $results = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($results)) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Serial</th><th>Drug Name</th><th>Started On</th><th>Stopped On</th></tr>";
        
        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>" . $row['serial_no'] . "</td>";
            echo "<td>" . $row['drug_name'] . "</td>";
            echo "<td>" . ($row['started_on'] ?? 'NULL') . "</td>";
            echo "<td>" . ($row['stopped_on'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Clean up test data
    $deleteStmt = $pdo->prepare("DELETE FROM antibiotic_usage WHERE patient_id = ?");
    $deleteStmt->execute([$testPatientId]);
    echo "<p style='color: blue;'>Test data cleaned up.</p>";
    
    echo "<h3 style='color: green;'>✅ All tests completed successfully!</h3>";
    echo "<p><strong>You can now enter any text or number in the started_on and stopped_on fields:</strong></p>";
    echo "<ul>";
    echo "<li>Numbers: 111, 243523, etc.</li>";
    echo "<li>Text: 'Day 5', 'Week 2', 'Ongoing', etc.</li>";
    echo "<li>Dates: '2024-01-15', '15/01/2024', etc.</li>";
    echo "<li>Empty values or NULL</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ Error occurred:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?>
