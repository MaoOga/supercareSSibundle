<?php
require_once 'config.php';
require_once 'audit_logger.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get form data
    $nurseId = trim($_POST['nurseId'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'nurse';

    // Validation
    if (empty($nurseId)) {
        throw new Exception('Nurse ID is required');
    }
    if (empty($password)) {
        throw new Exception('Password is required');
    }
    
    // Password validation
    if (strlen($password) < 8) {
        throw new Exception('Password must be at least 8 characters long');
    }
    if (!preg_match('/[A-Z]/', $password)) {
        throw new Exception('Password must contain at least one uppercase letter');
    }
    if (!preg_match('/[a-z]/', $password)) {
        throw new Exception('Password must contain at least one lowercase letter');
    }
    if (!preg_match('/[0-9]/', $password)) {
        throw new Exception('Password must contain at least one number');
    }
    if (!preg_match('/[!@#$%^&*]/', $password)) {
        throw new Exception('Password must contain at least one special character (!@#$%^&*)');
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Check if nurse ID already exists
    $stmt = $pdo->prepare("SELECT id FROM nurses WHERE nurse_id = ?");
    $stmt->execute([$nurseId]);
    if ($stmt->fetch()) {
        throw new Exception('Nurse ID already exists');
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new nurse
    $stmt = $pdo->prepare("
        INSERT INTO nurses (nurse_id, name, email, password, role, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([$nurseId, $name, $email, $hashedPassword, $role]);

    $insertedId = $pdo->lastInsertId();

    // Log the audit event
    $auditLogger = new AuditLogger($pdo);
    $nurseData = [
        'id' => $insertedId,
        'nurse_id' => $nurseId,
        'name' => $name,
        'email' => $email,
        'role' => $role
    ];
    $auditLogger->logNurseCreate('admin', $nurseData);

    echo json_encode([
        'success' => true,
        'message' => 'Nurse account created successfully',
        'nurse_id' => $insertedId
    ]);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
