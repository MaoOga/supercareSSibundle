<?php
require_once '../database/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $token = $_POST['token'] ?? '';

    if (empty($token)) {
        throw new Exception('Token is required');
    }

    // Get nurse info from token
    $stmt = $pdo->prepare("SELECT form_access FROM nurses WHERE reset_token = ? AND reset_expiry > NOW()");
    $stmt->execute([$token]);
    $nurse = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$nurse) {
        throw new Exception('Invalid or expired token');
    }

    $formAccess = $nurse['form_access'] ?? 'ssi';
    
    echo json_encode([
        'success' => true,
        'form_access' => $formAccess
    ]);

} catch (PDOException $e) {
    error_log("Database error in get_token_info.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

