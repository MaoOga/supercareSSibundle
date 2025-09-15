<?php
// Simple test to check antibiotic data submission
require_once '../database/config.php';

echo "<h2>Testing Antibiotic Data Submission</h2>";

try {
    // Test data
    $testData = [
        'patient_id' => 40,
        'drug-name_1' => 'Test Drug 1',
        'dosage_1' => '100mg',
        'antibiotic_usage[startedon]_1' => '01/01/2024',
        'antibiotic_usage[stoppeon]_1' => '02/01/2024',
        
        'drug-name_2' => 'Test Drug 2', 
        'dosage_2' => '200mg',
        'antibiotic_usage[startedon]_2' => '03/01/2024',
        'antibiotic_usage[stoppeon]_2' => '04/01/2024'
    ];
    
    echo "<h3>Test Data:</h3>";
    echo "<pre>" . json_encode($testData, JSON_PRETTY_PRINT) . "</pre>";
    
    $patientId = $testData['patient_id'];
    
    // Delete existing test data
    $deleteStmt = $pdo->prepare("DELETE FROM antibiotic_usage WHERE patient_id = ?");
    $deleteStmt->execute([$patientId]);
    echo "<p>✅ Deleted existing data for patient $patientId</p>";
    
    // Process data exactly like the main form
    for ($i = 1; $i <= 2; $i++) {
        $drugName = $testData["drug-name_$i"] ?? null;
        if (!empty($drugName)) {
            $startedOn = $testData["antibiotic_usage[startedon]_$i"] ?? null;
            $stoppedOn = $testData["antibiotic_usage[stoppeon]_$i"] ?? null;
            
            echo "<p><strong>Row $i:</strong></p>";
            echo "<ul>";
            echo "<li>Drug: '$drugName'</li>";
            echo "<li>Dosage: '" . ($testData["dosage_$i"] ?? 'NULL') . "'</li>";
            echo "<li>Started: '$startedOn'</li>";
            echo "<li>Stopped: '$stoppedOn'</li>";
            echo "</ul>";
            
            $stmt = $pdo->prepare("INSERT INTO antibiotic_usage (patient_id, serial_no, drug_name, dosage_route_frequency, started_on, stopped_on) VALUES (?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([$patientId, $i, $drugName, $testData["dosage_$i"] ?? null, $startedOn, $stoppedOn]);
            
            if ($result) {
                echo "<p>✅ Inserted row $i successfully</p>";
            } else {
                echo "<p>❌ Failed to insert row $i</p>";
            }
        }
    }
    
    // Verify data
    echo "<h3>Database Results:</h3>";
    $verifyStmt = $pdo->prepare("SELECT * FROM antibiotic_usage WHERE patient_id = ? ORDER BY serial_no");
    $verifyStmt->execute([$patientId]);
    $results = $verifyStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($results)) {
        echo "<p>❌ No data found</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Serial</th><th>Drug</th><th>Dosage</th><th>Started</th><th>Stopped</th></tr>";
        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>{$row['serial_no']}</td>";
            echo "<td>{$row['drug_name']}</td>";
            echo "<td>{$row['dosage_route_frequency']}</td>";
            echo "<td>{$row['started_on']}</td>";
            echo "<td>{$row['stopped_on']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
