<?php
// Simple test to check patient data retrieval
require_once 'config.php';

// Test with a sample patient ID (you can change this)
$test_patient_id = 1;

echo "<h2>Testing Patient Data Retrieval</h2>";
echo "<p>Testing with patient_id: $test_patient_id</p>";

try {
    // Get patient basic info
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE patient_id = ?");
    $stmt->execute([$test_patient_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        echo "<p style='color: red;'>Patient not found!</p>";
        exit;
    }

    echo "<h3>Patient Found:</h3>";
    echo "<pre>";
    print_r($patient);
    echo "</pre>";

    // Test the API endpoint
    echo "<h3>Testing API Endpoint:</h3>";
    
    // Simulate AJAX request
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
    $_GET['patient_id'] = $test_patient_id;
    
    // Capture output
    ob_start();
    include 'get_patient_data.php';
    $output = ob_get_clean();
    
    echo "<h4>API Response:</h4>";
    echo "<pre>";
    echo htmlspecialchars($output);
    echo "</pre>";

} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
