<?php
require_once 'config.php';

echo "<h2>Checking Patients in Database</h2>";

try {
    // Check if patients table exists and has data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM patients");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<p>Total patients in database: " . $result['count'] . "</p>";
    
    if ($result['count'] > 0) {
        // Show first few patients
        $stmt = $pdo->query("SELECT patient_id, name, uhid FROM patients LIMIT 5");
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Sample Patients:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Patient ID</th><th>Name</th><th>UHID</th></tr>";
        foreach ($patients as $patient) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($patient['patient_id']) . "</td>";
            echo "<td>" . htmlspecialchars($patient['name']) . "</td>";
            echo "<td>" . htmlspecialchars($patient['uhid']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>No patients found in database!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
