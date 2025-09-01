<?php
require_once '../database/config.php';

echo "<h2>Form Loading Fix Verification</h2>";

try {
    // Get the first patient
    $stmt = $pdo->query("SELECT patient_id, name, uhid FROM patients LIMIT 1");
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$patient) {
        echo "<p>❌ No patients found in database.</p>";
        exit;
    }
    
    $patient_id = $patient['patient_id'];
    echo "<p>Testing with patient: {$patient['name']} (UHID: {$patient['uhid']})</p>";
    
    // Test the API response
    $api_url = "get_patient_data_api.php?patient_id=$patient_id";
    echo "<h3>Step 1: Test API Response</h3>";
    echo "<p><a href='$api_url' target='_blank'>Click here to test API response</a></p>";
    echo "<p>This should return JSON with all the data including risk_factors and infection_prevention_notes.</p>";
    
    echo "<h3>Step 2: Test Form Loading</h3>";
    echo "<p><a href='../forms/form.html' target='_blank'>Open the form</a></p>";
    echo "<p>Then click 'View/Edit' for patient: {$patient['name']}</p>";
    echo "<p>Check the browser console (F12) for any error messages.</p>";
    
    echo "<h3>Step 3: What to Check</h3>";
    echo "<ul>";
    echo "<li>✅ RISK FACTOR fields should be filled (weight, height, steroids, tuberculosis, others)</li>";
    echo "<li>✅ Infection Prevention Control Nurse Notes should be filled</li>";
    echo "<li>✅ Signature field should be filled</li>";
    echo "<li>✅ Wound complications checkbox should be checked (if data exists)</li>";
    echo "</ul>";
    
    echo "<h3>Step 4: If Still Not Working</h3>";
    echo "<p>Check the browser console (F12) and look for:</p>";
    echo "<ul>";
    echo "<li>Any JavaScript errors</li>";
    echo "<li>The console.log messages showing data loading</li>";
    echo "<li>Whether the API response contains the expected data</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h3>❌ Error occurred:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3 { color: #333; }
p { margin: 10px 0; }
ul { margin: 20px 0; }
li { margin: 5px 0; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
