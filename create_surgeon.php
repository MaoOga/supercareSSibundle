<?php
header('Content-Type: application/json');
require_once 'config.php';
require_once 'audit_logger.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get POST data
    $name = trim($_POST['name'] ?? '');
    
    // Validate input
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Surgeon name is required']);
        exit;
    }
    
    // Check if surgeon already exists
    $stmt = $pdo->prepare("SELECT id FROM surgeons WHERE name = ?");
    $stmt->execute([$name]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Surgeon already exists']);
        exit;
    }
    
    // Insert new surgeon
    $stmt = $pdo->prepare("INSERT INTO surgeons (name) VALUES (?)");
    $stmt->execute([$name]);
    
    $insertedId = $pdo->lastInsertId();
    
    // Log the audit event
    $auditLogger = new AuditLogger($pdo);
    $surgeonData = [
        'id' => $insertedId,
        'name' => $name
    ];
    $auditLogger->logSurgeonCreate('admin', $surgeonData);
    
    echo json_encode(['success' => true, 'message' => 'Surgeon added successfully']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
