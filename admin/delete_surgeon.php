<?php
header('Content-Type: application/json');
require_once '../database/config.php';
require_once '../audit/audit_logger.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get POST data
    $id = $_POST['id'] ?? '';
    
    // Validate input
    if (empty($id) || !is_numeric($id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid surgeon ID']);
        exit;
    }
    
    // Check if surgeon exists
    $stmt = $pdo->prepare("SELECT id, name FROM surgeons WHERE id = ?");
    $stmt->execute([$id]);
    $surgeon = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$surgeon) {
        echo json_encode(['success' => false, 'message' => 'Surgeon not found']);
        exit;
    }
    
    // Log the audit event before deletion
    $auditLogger = new AuditLogger($pdo);
    $auditLogger->logSurgeonDelete('admin', $surgeon['id'], $surgeon['name'], $surgeon);
    
    // Delete surgeon
    $stmt = $pdo->prepare("DELETE FROM surgeons WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode(['success' => true, 'message' => 'Surgeon deleted successfully']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
