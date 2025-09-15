<?php
// OTP Verification and Super Admin Login
header('Content-Type: application/json');

// Include session configuration
require_once 'super_admin_session_config.php';

// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=supercare_ssi;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $otp = $_POST['otp'] ?? '';
    
    if (empty($email) || empty($password) || empty($otp)) {
        echo json_encode(['success' => false, 'message' => 'Email, password, and OTP are required']);
        exit;
    }
    
    try {
        // Clean up old OTPs first
        $stmt = $pdo->prepare("
            DELETE FROM super_admin_otp_logs 
            WHERE created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)
            AND status IN ('expired', 'used')
        ");
        $stmt->execute();
        
        // Check if user exists and password is correct
        $stmt = $pdo->prepare("SELECT id, email, password, name, status, otp_code, otp_expires_at FROM super_admin_users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
            exit;
        }
        
        // Check for valid OTP in the logs table (this handles multiple OTP requests)
        $stmt = $pdo->prepare("
            SELECT id, otp_code, created_at 
            FROM super_admin_otp_logs 
            WHERE email = ? 
            AND otp_code = ? 
            AND status = 'pending'
            AND created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$email, $otp]);
        $otpLog = $stmt->fetch();
        
        if (!$otpLog) {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP code. Please request a new code.']);
            exit;
        }
        
        // Check if OTP is expired (10 minutes from creation)
        $otpCreated = strtotime($otpLog['created_at']);
        if (time() - $otpCreated > 600) { // 10 minutes = 600 seconds
            // Mark expired OTP as expired
            $stmt = $pdo->prepare("UPDATE super_admin_otp_logs SET status = 'expired' WHERE id = ?");
            $stmt->execute([$otpLog['id']]);
            
            echo json_encode(['success' => false, 'message' => 'OTP has expired. Please request a new code.']);
            exit;
        }
        
        // OTP is valid - set super admin session
        setSuperAdminSession([
            'id' => $user['id'],
            'type' => 'super_admin',
            'username' => $user['email'], // Using email as username
            'name' => $user['name'],
            'email' => $user['email']
        ]);
        
        // Update last login time
        $stmt = $pdo->prepare("UPDATE super_admin_users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        // Mark this specific OTP as used
        $stmt = $pdo->prepare("UPDATE super_admin_otp_logs SET status = 'used', used_at = NOW() WHERE id = ?");
        $stmt->execute([$otpLog['id']]);
        
        // Mark all other pending OTPs for this user as expired
        $stmt = $pdo->prepare("UPDATE super_admin_otp_logs SET status = 'expired' WHERE email = ? AND status = 'pending' AND id != ?");
        $stmt->execute([$email, $otpLog['id']]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Super admin authentication successful',
            'user_type' => 'super_admin',
            'user_name' => $user['name']
        ]);
        
    } catch (Exception $e) {
        error_log("OTP verification error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'System error occurred']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
