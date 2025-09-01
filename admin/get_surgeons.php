<?php
header('Content-Type: application/json');
require_once '../database/config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all surgeons ordered by name
    $stmt = $pdo->prepare("SELECT id, name, created_at FROM surgeons ORDER BY name ASC");
    $stmt->execute();
    $surgeons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total count
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM surgeons");
    $stmt->execute();
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo json_encode([
        'success' => true,
        'surgeons' => $surgeons,
        'total_count' => $total
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
