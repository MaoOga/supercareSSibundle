<?php
// Start session for super admin
session_name('SUPER_ADMIN_SESSION');
session_start();

// Check if super admin is logged in
if (!isset($_SESSION['super_admin_logged_in']) || $_SESSION['super_admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Include database configuration
require_once '../database/config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Get all admin users
    $stmt = $pdo->prepare("SELECT id, admin_username, name, email, created_at FROM admin_users WHERE status = 'active' ORDER BY created_at DESC");
    $stmt->execute();
    $admins = $stmt->fetchAll();
    
    // Get total count
    $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM admin_users WHERE status = 'active'");
    $countStmt->execute();
    $totalCount = $countStmt->fetch()['total'];
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'admins' => $admins,
        'total_count' => $totalCount
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get_admins_simple.php: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed',
        'total_count' => 0
    ]);
}
?>
