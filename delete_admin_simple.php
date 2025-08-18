<?php
// Set content type at the very beginning
header('Content-Type: application/json');

// Start session for super admin
session_name('SUPER_ADMIN_SESSION');
session_start();

// Check if super admin is logged in
if (!isset($_SESSION['super_admin_logged_in']) || $_SESSION['super_admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get POST data from FormData
$admin_id = (int)($_POST['id'] ?? 0);

if ($admin_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid admin ID']);
    exit();
}

// Database configuration
$host = 'localhost';
$dbname = 'supercare_ssi';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT id, admin_username, email FROM admin_users WHERE id = ? AND status = 'active'");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch();
    
    if (!$admin) {
        echo json_encode(['success' => false, 'message' => 'Admin not found']);
        exit();
    }
    
    // Permanently delete the admin record
    $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
    $stmt->execute([$admin_id]);
    
    // Log the deletion
    $log_stmt = $pdo->prepare("INSERT INTO admin_login_logs (email, status, message, ip_address, user_agent, created_at) VALUES (?, 'success', 'Admin account deleted', ?, ?, NOW())");
    $log_stmt->execute([$admin['email'], $_SERVER['REMOTE_ADDR'] ?? 'unknown', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown']);
    
    echo json_encode([
        'success' => true,
        'message' => 'Admin account deleted successfully',
        'deleted_admin' => $admin
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in delete_admin_simple.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
