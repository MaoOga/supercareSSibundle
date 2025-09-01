<?php
// OTP Rate Limit Reset Script
header('Content-Type: application/json');

// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=supercare_ssi;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get email from POST or GET parameter
$email = $_POST['email'] ?? $_GET['email'] ?? '';

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email parameter is required']);
    exit;
}

try {
    // Option 1: Delete recent OTP logs for this email (recommended)
    $stmt = $pdo->prepare("
        DELETE FROM super_admin_otp_logs 
        WHERE email = ? 
        AND created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)
        AND status IN ('pending', 'used')
    ");
    $stmt->execute([$email]);
    $deletedCount = $stmt->rowCount();
    
    // Option 2: Mark all recent OTPs as expired
    $stmt = $pdo->prepare("
        UPDATE super_admin_otp_logs 
        SET status = 'expired' 
        WHERE email = ? 
        AND created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)
        AND status IN ('pending', 'used')
    ");
    $stmt->execute([$email]);
    $expiredCount = $stmt->rowCount();
    
    echo json_encode([
        'success' => true, 
        'message' => "OTP rate limit reset successfully for $email",
        'deleted_records' => $deletedCount,
        'expired_records' => $expiredCount,
        'email' => $email
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error resetting rate limit: ' . $e->getMessage()]);
}
?>
