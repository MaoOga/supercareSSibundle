<?php
// Start output buffering to capture any potential output
ob_start();

// Suppress error reporting to prevent HTML error messages from corrupting JSON output
error_reporting(0);
ini_set('display_errors', 0);

require_once 'config.php';

// Clear any output that might have been generated
ob_clean();

header('Content-Type: application/json');

try {
    // Fetch all patients with their related data
    $query = "
        SELECT 
            p.patient_id,
            p.name,
            p.age,
            p.sex,
            p.uhid,
            p.phone,
            p.bed_ward,
            p.address,
            p.primary_diagnosis,
            p.surgical_procedure,
            p.date_completed,
            sd.doa,
            sd.dos,
            sd.dod,
            sd.surgeon,
            sd.operation_duration,
            wc.complication_date,
            wc.wound_dehiscence,
            wc.allergic_reaction,
            wc.bleeding_haemorrhage,
            wc.other_complication,
            wc.superficial_ssi,
            wc.deep_si,
            wc.organ_space_ssi,
            rs.review_on,
            rs.sutures_removed_on,
            rp.review_date
        FROM patients p
        LEFT JOIN surgical_details sd ON p.patient_id = sd.patient_id
        LEFT JOIN wound_complications wc ON p.patient_id = wc.patient_id
        LEFT JOIN review_sutures rs ON p.patient_id = rs.patient_id
        LEFT JOIN review_phone rp ON p.patient_id = rp.patient_id
        ORDER BY sd.dos DESC, p.patient_id DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $patients = $stmt->fetchAll();
    
    // Process the data to determine status
    $processedPatients = [];
    $totalPatients = 0;
    $complicationsCount = 0;
    $recentSurgeriesCount = 0;
    $pendingReviewsCount = 0;
    
    // Get total statistics for all patients (not just last 7 days)
    $statsQuery = "
        SELECT 
            COUNT(*) as total_patients,
            SUM(CASE WHEN wc.wound_dehiscence OR wc.allergic_reaction OR wc.bleeding_haemorrhage OR wc.other_complication OR wc.superficial_ssi OR wc.deep_si OR wc.organ_space_ssi THEN 1 ELSE 0 END) as complications,
            SUM(CASE WHEN sd.dos >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as recent_surgeries,
            SUM(CASE WHEN rs.review_on IS NULL AND rs.sutures_removed_on IS NULL THEN 1 ELSE 0 END) as pending_reviews
        FROM patients p
        LEFT JOIN surgical_details sd ON p.patient_id = sd.patient_id
        LEFT JOIN wound_complications wc ON p.patient_id = wc.patient_id
        LEFT JOIN review_sutures rs ON p.patient_id = rs.patient_id
    ";
    
    $statsStmt = $pdo->prepare($statsQuery);
    $statsStmt->execute();
    $stats = $statsStmt->fetch();
    
    foreach ($patients as $patient) {
        $totalPatients++;
        
        // Determine if there are complications
        $hasComplications = false;
        if ($patient['wound_dehiscence'] || $patient['allergic_reaction'] || 
            $patient['bleeding_haemorrhage'] || $patient['other_complication'] ||
            $patient['superficial_ssi'] || $patient['deep_si'] || $patient['organ_space_ssi']) {
            $hasComplications = true;
        }
        
        // Determine review status
        $reviewStatus = 'Pending';
        if ($patient['review_on'] && $patient['sutures_removed_on']) {
            $reviewStatus = 'Completed';
        } elseif ($patient['review_on'] || $patient['sutures_removed_on']) {
            $reviewStatus = 'In Progress';
        }
        
        // Format dates
        $dosFormatted = $patient['dos'] ? date('d/m/Y', strtotime($patient['dos'])) : '';
        $dateCompletedFormatted = $patient['date_completed'] ? date('d/m/Y', strtotime($patient['date_completed'])) : '';
        
        $processedPatients[] = [
            'patient_id' => $patient['patient_id'],
            'uhid' => $patient['uhid'],
            'name' => $patient['name'],
            'age' => $patient['age'],
            'sex' => $patient['sex'],
            'ward' => $patient['bed_ward'],
            'surgeon' => $patient['surgeon'],
            'dos' => $dosFormatted,
            'date_completed' => $dateCompletedFormatted,
            'has_complications' => $hasComplications,
            'review_status' => $reviewStatus,
            'diagnosis' => $patient['primary_diagnosis'],
            'procedure' => $patient['surgical_procedure']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'patients' => $processedPatients,
        'total_count' => count($processedPatients),
        'statistics' => [
            'total_patients' => $stats['total_patients'] ?? 0,
            'complications' => $stats['complications'] ?? 0,
            'recent_surgeries' => $stats['recent_surgeries'] ?? 0,
            'pending_reviews' => $stats['pending_reviews'] ?? 0
        ]
    ]);

} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching data: ' . $e->getMessage(),
        'patients' => [],
        'total_count' => 0
    ]);
}

// End output buffering and flush
ob_end_flush();
?>
