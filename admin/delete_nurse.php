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

    // Get nurse ID
    $id = $_POST['id'] ?? '';

    // Validation
    if (empty($id)) {
        throw new Exception('Nurse ID is required');
    }

    // Check if nurse exists
    $stmt = $pdo->prepare("SELECT id, nurse_id, name FROM nurses WHERE id = ?");
    $stmt->execute([$id]);
    $nurse = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$nurse) {
        throw new Exception('Nurse not found');
    }

    // Log the audit event before deletion
    $auditLogger = new AuditLogger($pdo);
    $auditLogger->logNurseDelete('admin', $nurse['id'], $nurse['name'], $nurse);

    // Delete nurse
    $stmt = $pdo->prepare("DELETE FROM nurses WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode([
        'success' => true,
        'message' => 'Nurse account deleted successfully',
        'deleted_nurse' => $nurse
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
