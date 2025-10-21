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
    $nurseId = trim($_POST['nurseId'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($nurseId)) {
        throw new Exception('Nurse ID is required');
    }
    if (empty($password)) {
        throw new Exception('Password is required');
    }

    // Find nurse by nurse_id - including form_access
    $stmt = $pdo->prepare("SELECT id, nurse_id, name, email, password, role, form_access FROM nurses WHERE nurse_id = ?");
    $stmt->execute([$nurseId]);
    $nurse = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$nurse) {
        // Log failed login attempt - nurse not found
        $auditLogger = new AuditLogger($pdo);
        $auditLogger->logNurseLogin('system', null, $nurseId, 'NURSE_NOT_FOUND', null, 'Nurse ID not found in system (CAUTI login)');
        throw new Exception('NURSE_NOT_FOUND');
    }

    // Verify password
    if (!password_verify($password, $nurse['password'])) {
        // Log failed login attempt - invalid password
        $auditLogger = new AuditLogger($pdo);
        $auditLogger->logNurseLogin('system', $nurse['id'], $nurse['nurse_id'], $nurse['name'], null, 'Invalid password provided (CAUTI login)');
        throw new Exception('INVALID_PASSWORD');
    }

    // Check form access - this is CAUTI login page
    $formAccess = $nurse['form_access'] ?? 'ssi';
    if ($formAccess !== 'cauti') {
        // Log access denied
        $auditLogger = new AuditLogger($pdo);
        $auditLogger->logNurseLogin('system', $nurse['id'], $nurse['nurse_id'], $nurse['name'], null, 'Access denied: No CAUTI form access (has: ' . $formAccess . ')');
        throw new Exception('You do not have access to the CAUTI form. Please contact your administrator.');
    }

    // Remove password from response for security
    unset($nurse['password']);

    // Log successful login
    $auditLogger = new AuditLogger($pdo);
    $auditLogger->logNurseLogin('system', $nurse['id'], $nurse['nurse_id'], $nurse['name'], $nurse, 'CAUTI nurse login successful');

    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'nurse' => $nurse,
        'redirect_url' => 'cauti_panel.php'
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

