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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $query = $_GET['query'] ?? '';
    $status = $_GET['status'] ?? 'all';
    $startDate = $_GET['startDate'] ?? '';
    $endDate = $_GET['endDate'] ?? '';
    
    // Build the base query
    $baseQuery = "
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
    ";
    
    $whereConditions = [];
    $params = [];
    
    // Add search query conditions
    if (!empty($query)) {
        $whereConditions[] = "(p.uhid LIKE ? OR p.name LIKE ? OR sd.surgeon LIKE ?)";
        $searchTerm = "%$query%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    // Add status filter
    if ($status !== 'all') {
        switch ($status) {
            case 'complications':
                $whereConditions[] = "(wc.wound_dehiscence = 1 OR wc.allergic_reaction = 1 OR wc.bleeding_haemorrhage = 1 OR wc.other_complication = 1 OR wc.superficial_ssi = 1 OR wc.deep_si = 1 OR wc.organ_space_ssi = 1)";
                break;
            case 'no-complications':
                $whereConditions[] = "(wc.wound_dehiscence = 0 OR wc.wound_dehiscence IS NULL) AND (wc.allergic_reaction = 0 OR wc.allergic_reaction IS NULL) AND (wc.bleeding_haemorrhage = 0 OR wc.bleeding_haemorrhage IS NULL) AND (wc.other_complication = 0 OR wc.other_complication IS NULL) AND (wc.superficial_ssi = 0 OR wc.superficial_ssi IS NULL) AND (wc.deep_si = 0 OR wc.deep_si IS NULL) AND (wc.organ_space_ssi = 0 OR wc.organ_space_ssi IS NULL)";
                break;
            case 'pending-review':
                $whereConditions[] = "(rs.review_on IS NULL OR rs.sutures_removed_on IS NULL)";
                break;
            case 'completed-review':
                $whereConditions[] = "(rs.review_on IS NOT NULL AND rs.sutures_removed_on IS NOT NULL)";
                break;
        }
    }
    
    // Add date range filter
    if (!empty($startDate) && !empty($endDate)) {
        $whereConditions[] = "p.date_completed BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
    } elseif (!empty($startDate)) {
        $whereConditions[] = "p.date_completed >= ?";
        $params[] = $startDate;
    } elseif (!empty($endDate)) {
        $whereConditions[] = "p.date_completed <= ?";
        $params[] = $endDate;
    }
    
    // Build the complete query
    $sql = $baseQuery;
    if (!empty($whereConditions)) {
        $sql .= " WHERE " . implode(" AND ", $whereConditions);
    }
    $sql .= " ORDER BY p.date_completed DESC, p.patient_id DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $patients = $stmt->fetchAll();
    
    // Debug: Log the query and results
    error_log("Search query: " . $sql);
    error_log("Search params: " . json_encode($params));
    error_log("Search results count: " . count($patients));
    
    // Process the data to determine status
    $processedPatients = [];
    $totalPatients = 0;
    
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
        'search_params' => [
            'query' => $query,
            'status' => $status,
            'startDate' => $startDate,
            'endDate' => $endDate
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
