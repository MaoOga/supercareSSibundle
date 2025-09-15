<?php
// Set content type at the very beginning
header('Content-Type: application/json');

// Super Admin session authentication required
require_once '../auth/super_admin_session_config.php';

// Check if super admin is logged in
if (!isSuperAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Super admin access required']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get POST data from FormData
$admin_username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$admin_password = $_POST['password'] ?? '';

// Validate input
if (empty($admin_username) || empty($email) || empty($admin_password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

if (strlen($admin_password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit();
}

// Include database configuration
require_once '../database/config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit();
    }
    
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE admin_username = ?");
    $stmt->execute([$admin_username]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        exit();
    }
    
    // Hash password
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    
    // Insert new admin
    $stmt = $pdo->prepare("INSERT INTO admin_users (admin_username, email, password, status, created_at) VALUES (?, ?, ?, 'active', NOW())");
    $stmt->execute([$admin_username, $email, $hashed_password]);
    
    $admin_id = $pdo->lastInsertId();
    
    // Log the creation
    $log_stmt = $pdo->prepare("INSERT INTO admin_login_logs (email, status, message, ip_address, user_agent, created_at) VALUES (?, 'success', 'Admin account created', ?, ?, NOW())");
    $log_stmt->execute([$email, $_SERVER['REMOTE_ADDR'] ?? 'unknown', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown']);
    
    echo json_encode([
        'success' => true,
        'message' => 'Admin account created successfully',
        'admin_id' => $admin_id
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in create_admin_simple.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
