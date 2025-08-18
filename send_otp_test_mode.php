<?php
// OTP Generation - Test Mode (shows OTP on screen)
header('Content-Type: application/json');

// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=supercare_ssi;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

function generateOTP() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function checkRateLimit($email, $pdo) {
    // Check for OTP requests in the last 10 minutes
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM super_admin_otp_logs 
        WHERE email = ? 
        AND created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)
        AND status IN ('pending', 'used')
    ");
    $stmt->execute([$email]);
    $result = $stmt->fetch();
    
    // Allow maximum 3 OTP requests per 10 minutes
    return $result['count'] < 3;
}

function getTimeUntilNextOTP($email, $pdo) {
    // Get the time until next OTP can be requested
    $stmt = $pdo->prepare("
        SELECT created_at 
        FROM super_admin_otp_logs 
        WHERE email = ? 
        AND created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $result = $stmt->fetch();
    
    if ($result) {
        $lastRequest = strtotime($result['created_at']);
        $nextAllowed = $lastRequest + (10 * 60); // 10 minutes
        $timeRemaining = $nextAllowed - time();
        return max(0, $timeRemaining);
    }
    
    return 0;
}

// Handle OTP request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        exit;
    }
    
    try {
        // Check if user exists and password is correct
        $stmt = $pdo->prepare("SELECT id, email, password, name, status FROM super_admin_users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
            exit;
        }
        
        // Check rate limit
        if (!checkRateLimit($email, $pdo)) {
            $timeRemaining = getTimeUntilNextOTP($email, $pdo);
            $minutes = ceil($timeRemaining / 60);
            echo json_encode([
                'success' => false, 
                'message' => "Too many OTP requests. Please wait $minutes minutes before requesting another OTP.",
                'rate_limited' => true,
                'time_remaining' => $timeRemaining
            ]);
            exit;
        }
        
        // Generate OTP
        $otp = generateOTP();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        
        // Save OTP to database
        $stmt = $pdo->prepare("UPDATE super_admin_users SET otp_code = ?, otp_expires_at = ? WHERE id = ?");
        $stmt->execute([$otp, $expiresAt, $user['id']]);
        
        // Log OTP attempt
        $stmt = $pdo->prepare("INSERT INTO super_admin_otp_logs (email, otp_code, ip_address, status) VALUES (?, ?, ?, 'pending')");
        $stmt->execute([$email, $otp, $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
        
        // In test mode, return OTP in response
        echo json_encode([
            'success' => true, 
            'message' => 'OTP generated successfully (TEST MODE)',
            'email' => $email,
            'otp_code' => $otp, // This will be shown on screen
            'expires_at' => $expiresAt,
            'test_mode' => true
        ]);
        
    } catch (Exception $e) {
        error_log("OTP generation error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'System error occurred']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
