<?php
// Test that simulates actual form submission
require_once '../database/config.php';

echo "<h2>Testing Form Submission Simulation</h2>";

try {
    // Simulate $_POST data exactly as it would come from the form
    $_POST = [
        'patient_id' => '40',
        'name' => 'Test Patient',
        'uhid' => 'TEST123',
        'age' => '30',
        'sex' => 'Male',
        'phone' => '1234567890',
        'bed' => '1',
        'address' => 'Test Address',
        'diagnosis' => 'Test Diagnosis',
        'surgical_procedure' => 'Test Procedure',
        'date_completed' => '2024-01-01',
        
        // Antibiotic data - exactly as form would send it
        'drug-name_1' => 'Amoxicillin',
        'dosage_1' => '500mg TID',
        'antibiotic_usage[startedon]_1' => '01/01/2024',
        'antibiotic_usage[stoppeon]_1' => '05/01/2024',
        
        'drug-name_2' => 'Ciprofloxacin',
        'dosage_2' => '250mg BID',
        'antibiotic_usage[startedon]_2' => '06/01/2024',
        'antibiotic_usage[stoppeon]_2' => '10/01/2024',
        
        'drug-name_3' => 'Metronidazole',
        'dosage_3' => '400mg TID',
        'antibiotic_usage[startedon]_3' => '11/01/2024',
        'antibiotic_usage[stoppeon]_3' => '15/01/2024'
    ];
    
    echo "<h3>Simulated Form Data:</h3>";
    echo "<pre>" . json_encode($_POST, JSON_PRETTY_PRINT) . "</pre>";
    
    // Process exactly like the main form
    $formData = $_POST;
    $patientId = $formData['patient_id'] ?? null;
    
    echo "<h3>Processing Antibiotic Data:</h3>";
    
    // Delete existing data
    $deleteStmt = $pdo->prepare("DELETE FROM antibiotic_usage WHERE patient_id = ?");
    $deleteStmt->execute([$patientId]);
    echo "<p>✅ Cleared existing data for patient $patientId</p>";
    
    // Process antibiotic data
    for ($i = 1; $i <= 10; $i++) {
        $drugName = $formData["drug-name_$i"] ?? null;
        if (!empty($drugName)) {
            // Get started_on and stopped_on exactly like drug_name and dosage (simple field access)
            $startedOn = $formData["antibiotic_usage[startedon]_$i"] ?? null;
            $stoppedOn = $formData["antibiotic_usage[stoppeon]_$i"] ?? null;
            
            echo "<p><strong>Processing Row $i:</strong></p>";
            echo "<ul>";
            echo "<li>Drug Name: '$drugName'</li>";
            echo "<li>Dosage: '" . ($formData["dosage_$i"] ?? 'NULL') . "'</li>";
            echo "<li>Started On: '$startedOn'</li>";
            echo "<li>Stopped On: '$stoppedOn'</li>";
            echo "</ul>";
            
            $antibioticStmt = $pdo->prepare("INSERT INTO antibiotic_usage (patient_id, serial_no, drug_name, dosage_route_frequency, started_on, stopped_on) VALUES (?, ?, ?, ?, ?, ?)");
            $result = $antibioticStmt->execute([
                $patientId,
                $i,
                $drugName,
                $formData["dosage_$i"] ?? null,
                $startedOn,
                $stoppedOn
            ]);
            
            if ($result) {
                echo "<p>✅ Successfully inserted row $i</p>";
            } else {
                echo "<p>❌ Failed to insert row $i</p>";
            }
        }
    }
    
    // Verify results
    echo "<h3>Final Database Results:</h3>";
    $verifyStmt = $pdo->prepare("SELECT * FROM antibiotic_usage WHERE patient_id = ? ORDER BY serial_no");
    $verifyStmt->execute([$patientId]);
    $results = $verifyStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($results)) {
        echo "<p>❌ No data found in database</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>Serial No</th><th>Drug Name</th><th>Dosage</th><th>Started On</th><th>Stopped On</th>";
        echo "</tr>";
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
        
        echo "<h3>✅ Test Results:</h3>";
        if (count($results) == 3) {
            echo "<p>✅ Correct number of rows inserted (3)</p>";
        } else {
            echo "<p>❌ Wrong number of rows: " . count($results) . " (expected 3)</p>";
        }
        
        $allDifferent = true;
        $startedValues = array_column($results, 'started_on');
        $stoppedValues = array_column($results, 'stopped_on');
        
        if (count(array_unique($startedValues)) == count($startedValues)) {
            echo "<p>✅ All 'Started On' values are different</p>";
        } else {
            echo "<p>❌ Some 'Started On' values are the same</p>";
            $allDifferent = false;
        }
        
        if (count(array_unique($stoppedValues)) == count($stoppedValues)) {
            echo "<p>✅ All 'Stopped On' values are different</p>";
        } else {
            echo "<p>❌ Some 'Stopped On' values are the same</p>";
            $allDifferent = false;
        }
        
        if ($allDifferent) {
            echo "<p><strong>🎉 SUCCESS: All fields are working correctly!</strong></p>";
        } else {
            echo "<p><strong>❌ ISSUE: Some fields are sharing values</strong></p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
