<?php
session_start();
header('Content-Type: application/json');

require_once '../database/config.php';

try {
    // Check if PDO connection is available
    if (!$pdo) {
        throw new Exception('Database connection not available.');
    }

    // Fetch all patient records from cauti_patient_info table
    $sql = "SELECT 
                id,
                name,
                age,
                sex,
                uhid,
                bed_ward,
                date_of_admission,
                diagnosis,
                created_at,
                updated_at
            FROM cauti_patient_info 
            ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format patient data for display
    $formattedPatients = [];
    foreach ($patients as $patient) {
        $formattedPatients[] = [
            'patient_id' => $patient['id'],
            'name' => $patient['name'],
            'age' => $patient['age'],
            'sex' => $patient['sex'],
            'uhid' => $patient['uhid'],
            'ward' => $patient['bed_ward'],
            'catheter_date' => $patient['date_of_admission'], // Using admission date as placeholder
            'catheter_status' => 'Active', // Default status
            'cauti_status' => 'Negative', // Default status
            'review_status' => 'Pending', // Default status
            'created_at' => $patient['created_at']
        ];
    }

    // Calculate statistics
    $totalPatients = count($formattedPatients);
    $cautiCases = 0; // Count patients with CAUTI positive status
    $activeCatheters = $totalPatients; // For now, assume all have active catheters
    $pendingReviews = $totalPatients; // For now, assume all are pending review

    echo json_encode([
        'success' => true,
        'patients' => $formattedPatients,
        'total_count' => $totalPatients,
        'statistics' => [
            'total_patients' => $totalPatients,
            'cauti_cases' => $cautiCases,
            'active_catheters' => $activeCatheters,
            'pending_reviews' => $pendingReviews
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'patients' => [],
        'total_count' => 0
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'patients' => [],
        'total_count' => 0
    ]);
}
?>

