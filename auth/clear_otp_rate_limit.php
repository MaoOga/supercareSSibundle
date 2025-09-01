<?php
// Complete OTP Rate Limit Reset Script
header('Content-Type: application/json');

// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=supercare_ssi;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get action from parameter
$action = $_GET['action'] ?? 'reset_all';
$email = $_GET['email'] ?? '';

try {
    if ($action === 'reset_email' && !empty($email)) {
        // Reset rate limit for specific email
        $stmt = $pdo->prepare("
            DELETE FROM super_admin_otp_logs 
            WHERE email = ? 
            AND created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)
            AND status IN ('pending', 'used')
        ");
        $stmt->execute([$email]);
        $deletedCount = $stmt->rowCount();
        
        echo json_encode([
            'success' => true, 
            'message' => "Rate limit reset for $email",
            'deleted_records' => $deletedCount,
            'email' => $email
        ]);
        
    } elseif ($action === 'reset_all') {
        // Reset rate limit for all emails (clear all recent OTP logs)
        $stmt = $pdo->prepare("
            DELETE FROM super_admin_otp_logs 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)
            AND status IN ('pending', 'used')
        ");
        $stmt->execute();
        $deletedCount = $stmt->rowCount();
        
        echo json_encode([
            'success' => true, 
            'message' => "All OTP rate limits have been reset",
            'deleted_records' => $deletedCount
        ]);
        
    } elseif ($action === 'clear_all') {
        // Clear ALL OTP logs (nuclear option)
        $stmt = $pdo->prepare("DELETE FROM super_admin_otp_logs");
        $stmt->execute();
        $deletedCount = $stmt->rowCount();
        
        echo json_encode([
            'success' => true, 
            'message' => "All OTP logs have been cleared",
            'deleted_records' => $deletedCount
        ]);
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
