<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);


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

    // Get all nurses - including form_access
    $stmt = $pdo->prepare("SELECT id, nurse_id, name, email, form_access, created_at FROM nurses ORDER BY created_at DESC");
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
