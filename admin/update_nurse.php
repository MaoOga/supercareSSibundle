<?php
require_once '../database/config.php';
require_once '../audit/audit_logger.php';

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
    $id = $_POST['id'] ?? '';
    $nurseId = trim($_POST['nurseId'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'nurse';

    // Validation
    if (empty($id)) {
        throw new Exception('Nurse ID is required');
    }
    if (empty($nurseId)) {
        throw new Exception('Nurse ID is required');
    }
    
    // Password validation (only if password is provided)
    if (!empty($password)) {
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
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Get current nurse data for audit logging
    $stmt = $pdo->prepare("SELECT id, nurse_id, name, email, role FROM nurses WHERE id = ?");
    $stmt->execute([$id]);
    $currentNurse = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$currentNurse) {
        throw new Exception('Nurse not found');
    }

    // Check if nurse ID already exists for different nurse
    $stmt = $pdo->prepare("SELECT id FROM nurses WHERE nurse_id = ? AND id != ?");
    $stmt->execute([$nurseId, $id]);
    if ($stmt->fetch()) {
        throw new Exception('Nurse ID already exists');
    }

    // Build update query
    $updateFields = [];
    $params = [];
    
    $updateFields[] = "nurse_id = ?";
    $params[] = $nurseId;
    
    $updateFields[] = "name = ?";
    $params[] = $name;
    
    $updateFields[] = "email = ?";
    $params[] = $email;
    
    $updateFields[] = "role = ?";
    $params[] = $role;

    // Add password update if provided
    if (!empty($password)) {
        $updateFields[] = "password = ?";
        $params[] = password_hash($password, PASSWORD_DEFAULT);
    }

    $updateFields[] = "updated_at = NOW()";
    $params[] = $id; // For WHERE clause

    $sql = "UPDATE nurses SET " . implode(", ", $updateFields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Nurse not found');
    }

    // Prepare data for audit logging
    $updatedNurseData = [
        'id' => $id,
        'nurse_id' => $nurseId,
        'name' => $name,
        'email' => $email,
        'role' => $role
    ];

    // Log the audit event
    $auditLogger = new AuditLogger($pdo);
    $auditLogger->logNurseUpdate('admin', $id, $name, $currentNurse, $updatedNurseData);

    echo json_encode([
        'success' => true,
        'message' => 'Nurse account updated successfully'
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
