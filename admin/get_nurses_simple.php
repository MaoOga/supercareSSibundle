<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session for super admin
session_name('SUPER_ADMIN_SESSION');
session_start();

// Check if super admin is logged in
if (!isset($_SESSION['super_admin_logged_in']) || $_SESSION['super_admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode([
        'success' => false, 
        'message' => 'Unauthorized access - Please log in as super admin'
    ]);
    exit;
}

header('Content-Type: application/json');

try {
    // Database connection
    require_once '../database/config.php';
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if nurses table exists
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'nurses'");
    if ($tableCheck->rowCount() == 0) {
        echo json_encode([
            'success' => true,
            'nurses' => [],
            'total_count' => 0,
            'message' => 'Nurses table does not exist'
        ]);
        exit;
    }

    // Get all nurses - using only basic columns that should exist
    $stmt = $pdo->prepare("SELECT id, nurse_id, name, email, created_at FROM nurses ORDER BY created_at DESC");
    $stmt->execute();
    $nurses = $stmt->fetchAll();

    // Get total count
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM nurses");
    $countStmt->execute();
    $totalCount = $countStmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'nurses' => $nurses,
        'total_count' => $totalCount
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error retrieving nurse data: ' . $e->getMessage()]);
}
?>
