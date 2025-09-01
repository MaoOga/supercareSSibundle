<?php
// OTP Generation and Email Sending System
require_once '../email/email_config.php';
require_once '../phpmailer/PHPMailer.php';
require_once '../phpmailer/SMTP.php';
require_once '../phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
    // Check for OTP requests in the last 5 minutes
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM super_admin_otp_logs 
        WHERE email = ? 
        AND created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
        AND status IN ('pending', 'used', 'expired')
    ");
    $stmt->execute([$email]);
    $result = $stmt->fetch();
    
    // Allow maximum 3 OTP requests per 5 minutes
    return $result['count'] < 3;
}

function getTimeUntilNextOTP($email, $pdo) {
    // Get the time until next OTP can be requested
    $stmt = $pdo->prepare("
        SELECT created_at 
        FROM super_admin_otp_logs 
        WHERE email = ? 
        AND created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
        AND status IN ('pending', 'used', 'expired')
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $result = $stmt->fetch();
    
    if ($result) {
        $lastRequest = strtotime($result['created_at']);
        $nextAllowed = $lastRequest + (5 * 60); // 5 minutes
        $timeRemaining = $nextAllowed - time();
        return max(0, $timeRemaining);
    }
    
    return 0;
}

function cleanupOldOTPs($pdo) {
    // Clean up OTPs older than 24 hours
    $stmt = $pdo->prepare("
        DELETE FROM super_admin_otp_logs 
        WHERE created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)
        AND status IN ('expired', 'used')
    ");
    $stmt->execute();
}

function sendOTPEmail($email, $otp, $name) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ahensananingthemcha@gmail.com';
        $mail->Password = 'irelgxhyraptvexn';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Disable SSL certificate verification for local development
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Recipients
        $mail->setFrom('ahensananingthemcha@gmail.com', 'SuperCare System');
        $mail->addAddress($email, $name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Super Admin Login OTP Code';
        
        $emailBody = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #f8f9fa; padding: 20px; text-align: center;'>
                <h2 style='color: #333; margin: 0;'>Super Admin Access</h2>
            </div>
            <div style='padding: 30px; background: white;'>
                <h3 style='color: #333;'>Hello $name,</h3>
                <p style='color: #666; line-height: 1.6;'>
                    You have requested to access the Super Admin panel. Please use the following OTP code to complete your login:
                </p>
                <div style='background: #e9ecef; padding: 20px; text-align: center; margin: 20px 0; border-radius: 5px;'>
                    <h1 style='color: #007bff; font-size: 32px; margin: 0; letter-spacing: 5px;'>$otp</h1>
                </div>
                <p style='color: #666; line-height: 1.6;'>
                    <strong>Important:</strong>
                </p>
                <ul style='color: #666; line-height: 1.6;'>
                    <li>This OTP is valid for 10 minutes only</li>
                    <li>Do not share this code with anyone</li>
                    <li>If you didn't request this, please ignore this email</li>
                </ul>
                <p style='color: #666; line-height: 1.6;'>
                    Best regards,<br>
                    SuperCare System
                </p>
            </div>
            <div style='background: #f8f9fa; padding: 15px; text-align: center; color: #666; font-size: 12px;'>
                This is an automated message. Please do not reply to this email.
            </div>
        </div>";
        
        $mail->Body = $emailBody;
        $mail->AltBody = "Your Super Admin OTP code is: $otp\n\nThis code is valid for 10 minutes only.";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        error_log("Email error details: " . $e->getMessage());
        return false;
    }
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
        // Clean up old OTPs first
        cleanupOldOTPs($pdo);
        
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
        
        // First, invalidate all previous pending OTPs for this user
        $stmt = $pdo->prepare("UPDATE super_admin_otp_logs SET status = 'expired' WHERE email = ? AND status = 'pending'");
        $stmt->execute([$email]);
        
        // Log new OTP attempt
        $stmt = $pdo->prepare("INSERT INTO super_admin_otp_logs (email, otp_code, ip_address, status) VALUES (?, ?, ?, 'pending')");
        $stmt->execute([$email, $otp, $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
        
        // Send OTP email
        if (sendOTPEmail($email, $otp, $user['name'])) {
            echo json_encode([
                'success' => true, 
                'message' => 'OTP sent to your email address',
                'email' => $email
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send OTP email']);
        }
        
    } catch (Exception $e) {
        error_log("OTP generation error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'System error occurred']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
