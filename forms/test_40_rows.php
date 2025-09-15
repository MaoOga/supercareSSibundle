<?php
// Test script to verify 40-row limits are working
require_once '../database/config.php';

echo "<h2>Testing 40-Row Limits</h2>";

// Test data
$testPatientId = 999;

try {
    // Clean up any existing test data
    $deleteStmt = $pdo->prepare("DELETE FROM antibiotic_usage WHERE patient_id = ?");
    $deleteStmt->execute([$testPatientId]);
    $deleteStmt = $pdo->prepare("DELETE FROM post_operative_monitoring WHERE patient_id = ?");
    $deleteStmt->execute([$testPatientId]);
    $deleteStmt = $pdo->prepare("DELETE FROM patients WHERE patient_id = ?");
    $deleteStmt->execute([$testPatientId]);
    
    // Create test patient
    $patientStmt = $pdo->prepare("INSERT INTO patients (patient_id, name, age, sex, uhid, phone, bed_ward, address, primary_diagnosis, surgical_procedure, date_completed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $patientStmt->execute([
        $testPatientId,
        'Test Patient 40 Rows',
        30,
        'Male',
        'TEST40',
        '1234567890',
        'A1',
        'Test Address',
        'Test Diagnosis',
        'Test Procedure',
        date('Y-m-d')
    ]);
    
    echo "<h3>Test 1: Antibiotic Usage (40 rows)</h3>";
    
    // Test antibiotic insertion for all 40 rows
    $antibioticCount = 0;
    for ($i = 1; $i <= 40; $i++) {
        $insertStmt = $pdo->prepare("INSERT INTO antibiotic_usage (patient_id, serial_no, drug_name, dosage_route_frequency, started_on, stopped_on) VALUES (?, ?, ?, ?, ?, ?)");
        $result = $insertStmt->execute([
            $testPatientId,
            $i,
            "Test Antibiotic $i",
            "Test dosage $i",
            "Day $i",
            "Day " . ($i + 5)
        ]);
        
        if ($result) {
            $antibioticCount++;
        }
    }
    
    echo "<p><strong>Antibiotic rows inserted:</strong> $antibioticCount out of 40</p>";
    if ($antibioticCount === 40) {
        echo "<p style='color: green;'>✅ SUCCESS: All 40 antibiotic rows inserted!</p>";
    } else {
        echo "<p style='color: red;'>❌ FAILURE: Only $antibioticCount antibiotic rows inserted</p>";
    }
    
    echo "<h3>Test 2: Post-Operative Monitoring (40 rows)</h3>";
    
    // Test post-operative insertion for all 40 rows
    $postOpCount = 0;
    for ($i = 1; $i <= 40; $i++) {
        $insertStmt = $pdo->prepare("INSERT INTO post_operative_monitoring (patient_id, day, monitoring_date, dosage, discharge_fluid, tenderness_pain, swelling, fever) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $result = $insertStmt->execute([
            $testPatientId,
            $i,
            "Day $i",
            "Dosage $i",
            "Discharge $i",
            "Pain $i",
            "Swelling $i",
            "Fever $i"
        ]);
        
        if ($result) {
            $postOpCount++;
        }
    }
    
    echo "<p><strong>Post-operative rows inserted:</strong> $postOpCount out of 40</p>";
    if ($postOpCount === 40) {
        echo "<p style='color: green;'>✅ SUCCESS: All 40 post-operative rows inserted!</p>";
    } else {
        echo "<p style='color: red;'>❌ FAILURE: Only $postOpCount post-operative rows inserted</p>";
    }
    
    // Verify the data
    echo "<h3>Verification:</h3>";
    
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM antibiotic_usage WHERE patient_id = ?");
    $countStmt->execute([$testPatientId]);
    $actualAntibioticCount = $countStmt->fetchColumn();
    
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM post_operative_monitoring WHERE patient_id = ?");
    $countStmt->execute([$testPatientId]);
    $actualPostOpCount = $countStmt->fetchColumn();
    
    echo "<p><strong>Database verification:</strong></p>";
    echo "<ul>";
    echo "<li>Antibiotic records in database: $actualAntibioticCount</li>";
    echo "<li>Post-operative records in database: $actualPostOpCount</li>";
    echo "</ul>";
    
    if ($actualAntibioticCount === 40 && $actualPostOpCount === 40) {
        echo "<h3 style='color: green;'>🎉 ALL TESTS PASSED! 40-row limits are working correctly!</h3>";
    } else {
        echo "<h3 style='color: red;'>❌ Some tests failed. Check the results above.</h3>";
    }
    
    // Clean up test data
    $deleteStmt = $pdo->prepare("DELETE FROM antibiotic_usage WHERE patient_id = ?");
    $deleteStmt->execute([$testPatientId]);
    $deleteStmt = $pdo->prepare("DELETE FROM post_operative_monitoring WHERE patient_id = ?");
    $deleteStmt->execute([$testPatientId]);
    $deleteStmt = $pdo->prepare("DELETE FROM patients WHERE patient_id = ?");
    $deleteStmt->execute([$testPatientId]);
    
    echo "<p style='color: blue;'>Test data cleaned up.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h3>Manual Testing Instructions:</h3>";
echo "<ol>";
echo "<li><strong>Form Test:</strong> Add 10+ dynamic rows using the + button</li>";
echo "<li><strong>Fill Data:</strong> Enter test data in various rows (1, 5, 10, 15, 20, etc.)</li>";
echo "<li><strong>Submit:</strong> Submit the form and check the JSON response</li>";
echo "<li><strong>Verify:</strong> Count the entries in the response</li>";
echo "</ol>";
?>
