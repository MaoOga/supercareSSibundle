<?php
require_once '../database/config.php';

echo "<h2>Form Loading Test</h2>";

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
    
    // Test the API endpoint directly
    $api_url = "get_patient_data_api.php?patient_id=$patient_id";
    echo "<h3>API Response Test:</h3>";
    echo "<p><a href='$api_url' target='_blank'>Click here to test API response</a></p>";
    
    // Test the simple endpoint
    $simple_url = "get_patient_data_simple.php?patient_id=$patient_id";
    echo "<p><a href='$simple_url' target='_blank'>Click here to test Simple API response</a></p>";
    
    // Check what data exists in the database
    echo "<h3>Database Data Check:</h3>";
    
    // Check risk factors
    $stmt = $pdo->prepare("SELECT * FROM risk_factors WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $riskData = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p><strong>Risk Factors:</strong> " . ($riskData ? "✓ Found data" : "❌ No data") . "</p>";
    if ($riskData) {
        echo "<pre>" . print_r($riskData, true) . "</pre>";
    }
    
    // Check infection prevention notes
    $stmt = $pdo->prepare("SELECT * FROM infection_prevention_notes WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $infectionData = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p><strong>Infection Prevention Notes:</strong> " . ($infectionData ? "✓ Found data" : "❌ No data") . "</p>";
    if ($infectionData) {
        echo "<pre>" . print_r($infectionData, true) . "</pre>";
    }
    
    // Check wound complications
    $stmt = $pdo->prepare("SELECT * FROM wound_complications WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $woundData = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p><strong>Wound Complications:</strong> " . ($woundData ? "✓ Found data" : "❌ No data") . "</p>";
    if ($woundData) {
        echo "<pre>" . print_r($woundData, true) . "</pre>";
    }
    
    // Check patient signature
    $stmt = $pdo->prepare("SELECT signature FROM patients WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $patientData = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p><strong>Patient Signature:</strong> " . ($patientData['signature'] ? "✓ Found: " . $patientData['signature'] : "❌ No signature") . "</p>";
    
    echo "<h3>Form Test:</h3>";
    echo "<p><a href='../forms/form.html'>Go to form and test View/Edit</a></p>";
    
} catch (Exception $e) {
    echo "<h3>❌ Error occurred:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3 { color: #333; }
p { margin: 10px 0; }
pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
