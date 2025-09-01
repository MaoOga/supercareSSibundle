<?php
// Check Patient Count and Surgery Dates
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

echo "<h2>Patient Count Analysis</h2>";

try {
    // Check total patients
    $totalQuery = "SELECT COUNT(*) as total FROM patients";
    $totalStmt = $pdo->prepare($totalQuery);
    $totalStmt->execute();
    $totalPatients = $totalStmt->fetch()['total'];
    
    echo "<h3>Total Patients in Database: $totalPatients</h3>";
    
    // Check patients with surgery details
    $surgeryQuery = "
        SELECT 
            p.patient_id,
            p.name,
            p.uhid,
            sd.dos,
            DATEDIFF(CURDATE(), sd.dos) as days_ago
        FROM patients p
        LEFT JOIN surgical_details sd ON p.patient_id = sd.patient_id
        ORDER BY sd.dos DESC
    ";
    
    $surgeryStmt = $pdo->prepare($surgeryQuery);
    $surgeryStmt->execute();
    $surgeryPatients = $surgeryStmt->fetchAll();
    
    echo "<h3>Patients with Surgery Details:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Patient ID</th><th>Name</th><th>UHID</th><th>Surgery Date</th><th>Days Ago</th><th>In Last 7 Days</th></tr>";
    
    $inLast7Days = 0;
    $totalWithSurgery = 0;
    
    foreach ($surgeryPatients as $patient) {
        $totalWithSurgery++;
        $daysAgo = $patient['days_ago'] ?? 'No surgery date';
        $inLast7DaysFlag = ($patient['days_ago'] !== null && $patient['days_ago'] <= 7) ? 'Yes' : 'No';
        
        if ($patient['days_ago'] !== null && $patient['days_ago'] <= 7) {
            $inLast7Days++;
        }
        
        echo "<tr>";
        echo "<td>" . $patient['patient_id'] . "</td>";
        echo "<td>" . $patient['name'] . "</td>";
        echo "<td>" . $patient['uhid'] . "</td>";
        echo "<td>" . ($patient['dos'] ?? 'No date') . "</td>";
        echo "<td>" . $daysAgo . "</td>";
        echo "<td>" . $inLast7DaysFlag . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<h3>Summary:</h3>";
    echo "<ul>";
    echo "<li>Total patients in database: $totalPatients</li>";
    echo "<li>Patients with surgery details: $totalWithSurgery</li>";
    echo "<li>Patients with surgery in last 7 days: $inLast7Days</li>";
    echo "<li>Patients that would show in admin panel: $inLast7Days</li>";
    echo "</ul>";
    
    // Check patients without surgery details
    $noSurgeryQuery = "
        SELECT COUNT(*) as count
        FROM patients p
        LEFT JOIN surgical_details sd ON p.patient_id = sd.patient_id
        WHERE sd.patient_id IS NULL
    ";
    
    $noSurgeryStmt = $pdo->prepare($noSurgeryQuery);
    $noSurgeryStmt->execute();
    $noSurgeryCount = $noSurgeryStmt->fetch()['count'];
    
    echo "<p><strong>Patients without surgery details: $noSurgeryCount</strong></p>";
    
    if ($noSurgeryCount > 0) {
        echo "<h3>Patients without surgery details:</h3>";
        $noSurgeryDetailsQuery = "
            SELECT p.patient_id, p.name, p.uhid
            FROM patients p
            LEFT JOIN surgical_details sd ON p.patient_id = sd.patient_id
            WHERE sd.patient_id IS NULL
        ";
        
        $noSurgeryDetailsStmt = $pdo->prepare($noSurgeryDetailsQuery);
        $noSurgeryDetailsStmt->execute();
        $noSurgeryDetails = $noSurgeryDetailsStmt->fetchAll();
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Patient ID</th><th>Name</th><th>UHID</th></tr>";
        
        foreach ($noSurgeryDetails as $patient) {
            echo "<tr>";
            echo "<td>" . $patient['patient_id'] . "</td>";
            echo "<td>" . $patient['name'] . "</td>";
            echo "<td>" . $patient['uhid'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
