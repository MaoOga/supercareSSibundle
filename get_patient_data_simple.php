<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $patient_id = $_GET['patient_id'] ?? '';
    if (empty($patient_id)) {
        echo json_encode(['success' => false, 'message' => 'Patient ID is required']);
        exit;
    }

    // Get patient basic info
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        echo json_encode(['success' => false, 'message' => 'Patient not found']);
        exit;
    }

    // Get surgical_details data
    $stmt = $pdo->prepare("SELECT * FROM surgical_details WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $surgical_details = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get surgical_skin_preparation data
    $stmt = $pdo->prepare("SELECT * FROM surgical_skin_preparation WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $surgical_skin_preparation = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get risk_factors data
    $stmt = $pdo->prepare("SELECT * FROM risk_factors WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $risk_factors = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get implanted_materials data
    $stmt = $pdo->prepare("SELECT * FROM implanted_materials WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $implanted_materials = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get drains data (can be multiple, fetchAll)
    $stmt = $pdo->prepare("SELECT * FROM drains WHERE patient_id = ? ORDER BY drain_number ASC");
    $stmt->execute([$patient_id]);
    $drains = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get antibiotic_usage data (can be multiple, fetchAll)
    $stmt = $pdo->prepare("SELECT * FROM antibiotic_usage WHERE patient_id = ? ORDER BY serial_no ASC");
    $stmt->execute([$patient_id]);
    $antibiotic_usage = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get post_operative_monitoring data (can be multiple, fetchAll)
    $stmt = $pdo->prepare("SELECT * FROM post_operative_monitoring WHERE patient_id = ? ORDER BY day ASC");
    $stmt->execute([$patient_id]);
    $post_operative_monitoring = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get cultural_dressing data
    $stmt = $pdo->prepare("SELECT * FROM cultural_dressing WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $cultural_dressing = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get wound_complications data
    $stmt = $pdo->prepare("SELECT * FROM wound_complications WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $wound_complications = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get review_sutures data
    $stmt = $pdo->prepare("SELECT * FROM review_sutures WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $review_sutures = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get review_phone data
    $stmt = $pdo->prepare("SELECT * FROM review_phone WHERE patient_id = ?");
    $stmt->execute([$patient_id]);
    $review_phone = $stmt->fetch(PDO::FETCH_ASSOC);

    // Combine all data
    $patientData = [
        'patient' => $patient,
        'surgical_details' => $surgical_details,
        'surgical_skin_preparation' => $surgical_skin_preparation,
        'risk_factors' => $risk_factors,
        'implanted_materials' => $implanted_materials,
        'drains' => $drains,
        'antibiotic_usage' => $antibiotic_usage,
        'post_operative_monitoring' => $post_operative_monitoring,
        'cultural_dressing' => $cultural_dressing,
        'wound_complications' => $wound_complications,
        'review_sutures' => $review_sutures,
        'review_phone' => $review_phone
    ];

    echo json_encode([
        'success' => true,
        'data' => $patientData
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
?>
