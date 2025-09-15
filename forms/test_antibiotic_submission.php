<?php
// Test script to submit antibiotic data directly to database
require_once '../database/config.php';

try {
    echo "<h2>Testing Antibiotic Data Submission</h2>\n";
    
    // Test data - simulating what should come from the form
    $testData = [
        'patient_id' => 40,
        'drug-name_1' => 'Test Drug 1',
        'dosage_1' => '100mg',
        'antibiotic_usage[startedon]_1' => '01/01/2024',
        'antibiotic_usage[stoppeon]_1' => '02/01/2024',
        
        'drug-name_2' => 'Test Drug 2', 
        'dosage_2' => '200mg',
        'antibiotic_usage[startedon]_2' => '03/01/2024',
        'antibiotic_usage[stoppeon]_2' => '04/01/2024',
        
        'drug-name_3' => 'Test Drug 3',
        'dosage_3' => '300mg', 
        'antibiotic_usage[startedon]_3' => '05/01/2024',
        'antibiotic_usage[stoppeon]_3' => '06/01/2024'
    ];
    
    echo "<h3>Test Data:</h3>\n";
    echo "<pre>" . json_encode($testData, JSON_PRETTY_PRINT) . "</pre>\n";
    
    $patientId = $testData['patient_id'];
    
    // First, delete existing test data
    $deleteStmt = $pdo->prepare("DELETE FROM antibiotic_usage WHERE patient_id = ?");
    $deleteStmt->execute([$patientId]);
    echo "<p>✅ Deleted existing antibiotic data for patient $patientId</p>\n";
    
    // Process antibiotic data exactly like the main form
    for ($i = 1; $i <= 10; $i++) {
        $drugName = $testData["drug-name_$i"] ?? null;
        if (!empty($drugName)) {
            // Get started_on and stopped_on exactly like drug_name and dosage (simple field access)
            $startedOn = $testData["antibiotic_usage[startedon]_$i"] ?? null;
            $stoppedOn = $testData["antibiotic_usage[stoppeon]_$i"] ?? null;
            
            echo "<p>Processing Row $i:</p>\n";
            echo "<ul>\n";
            echo "<li>Drug Name: '$drugName'</li>\n";
            echo "<li>Dosage: '" . ($testData["dosage_$i"] ?? 'NULL') . "'</li>\n";
            echo "<li>Started On: '$startedOn'</li>\n";
            echo "<li>Stopped On: '$stoppedOn'</li>\n";
            echo "</ul>\n";
            
            $antibioticStmt = $pdo->prepare("INSERT INTO antibiotic_usage (patient_id, serial_no, drug_name, dosage_route_frequency, started_on, stopped_on) VALUES (?, ?, ?, ?, ?, ?)");
            $result = $antibioticStmt->execute([
                $patientId,
                $i,
                $drugName,
                $testData["dosage_$i"] ?? null,
                $startedOn,
                $stoppedOn
            ]);
            
            if ($result) {
                echo "<p>✅ Successfully inserted row $i</p>\n";
            } else {
                echo "<p>❌ Failed to insert row $i</p>\n";
            }
        }
    }
    
    // Verify the data was inserted correctly
    echo "<h3>Verification - Data in Database:</h3>\n";
    $verifyStmt = $pdo->prepare("SELECT * FROM antibiotic_usage WHERE patient_id = ? ORDER BY serial_no");
    $verifyStmt->execute([$patientId]);
    $results = $verifyStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($results)) {
        echo "<p>❌ No data found in database</p>\n";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>\n";
        echo "<tr><th>Serial No</th><th>Drug Name</th><th>Dosage</th><th>Started On</th><th>Stopped On</th></tr>\n";
        foreach ($results as $row) {
            echo "<tr>\n";
            echo "<td>{$row['serial_no']}</td>\n";
            echo "<td>{$row['drug_name']}</td>\n";
            echo "<td>{$row['dosage_route_frequency']}</td>\n";
            echo "<td>{$row['started_on']}</td>\n";
            echo "<td>{$row['stopped_on']}</td>\n";
            echo "</tr>\n";
        }
        echo "</table>\n";
    }
    
    echo "<h3>Test Complete!</h3>\n";
    echo "<p>If you see different values in each row above, the database insertion is working correctly.</p>\n";
    echo "<p>If all rows show the same values, there's an issue with the form data processing.</p>\n";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>\n";
}
?>
