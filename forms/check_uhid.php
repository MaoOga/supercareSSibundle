<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Include database configuration
require_once '../database/config.php';

// Set JSON header
header('Content-Type: application/json');

// Support both GET and POST methods
$uhid = '';
$patient_id = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uhid = isset($_POST['uhid']) ? trim($_POST['uhid']) : '';
    $patient_id = isset($_POST['patient_id']) ? intval($_POST['patient_id']) : 0;
} else {
    $uhid = isset($_GET['uhid']) ? trim($_GET['uhid']) : '';
    $patient_id = isset($_GET['patient_id']) ? intval($_GET['patient_id']) : 0;
}

// Check if UHID is provided
if (empty($uhid)) {
    echo json_encode([
        'success' => false,
        'message' => 'UHID parameter is required'
    ]);
    exit;
}

try {
    if (!$pdo) {
        throw new Exception('Database connection not available.');
    }

    $exists = false;
    $existsIn = [];

    // Check in patients table (SSI Bundle)
    // Note: We don't exclude any patient here because patient_id could be from CAUTI table
    // The SSI form will check its own duplicates separately
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM patients WHERE uhid = ?");
    $stmt->execute([$uhid]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        $exists = true;
        $existsIn[] = 'SSI Bundle';
    }

    // Check in cauti_patient_info table (CAUTI)
    // Exclude current patient if editing (patient_id provided)
    if ($patient_id > 0) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cauti_patient_info WHERE uhid = ? AND id != ?");
        $stmt->execute([$uhid, $patient_id]);
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cauti_patient_info WHERE uhid = ?");
        $stmt->execute([$uhid]);
    }
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        $exists = true;
        $existsIn[] = 'CAUTI';
    }

    if ($exists) {
        $location = implode(' and ', $existsIn);
        echo json_encode([
            'success' => true,
            'available' => false,
            'exists' => true,
            'message' => "⚠️ UHID already exists in {$location} system!",
            'location' => $existsIn
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'available' => true,
            'exists' => false,
            'message' => 'UHID is available'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error checking UHID: ' . $e->getMessage()
    ]);
}
?>

