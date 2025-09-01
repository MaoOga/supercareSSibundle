<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $uhid = $_POST['uhid'] ?? '';
    
    if (empty($uhid)) {
        echo json_encode(['success' => false, 'message' => 'UHID is required']);
        exit;
    }

    // Check if UHID already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE uhid = ?");
    $stmt->execute([$uhid]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo json_encode([
            'success' => true, 
            'available' => false, 
            'message' => 'UHID already exists. Please use a unique UHID.'
        ]);
    } else {
        echo json_encode([
            'success' => true, 
            'available' => true, 
            'message' => 'UHID is available'
        ]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>
