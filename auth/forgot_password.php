<?php
header('Content-Type: application/json');
require_once '../database/config.php';
require_once '../email/email_config.php';

// Include PHPMailer files
require_once '../phpmailer/PHPMailer.php';
require_once '../phpmailer/SMTP.php';
require_once '../phpmailer/Exception.php';

// Use PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

try {
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get and validate email
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        throw new Exception('Email is required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Check if email exists in the database
    $stmt = $pdo->prepare("SELECT id, nurse_id, name FROM nurses WHERE email = ?");
    $stmt->execute([$email]);
    $nurse = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$nurse) {
        throw new Exception('Email address not found in our records');
    }

    // Generate a unique reset token
    $resetToken = bin2hex(random_bytes(32));
    $resetExpiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour

    // Store the reset token in the database
    $stmt = $pdo->prepare("UPDATE nurses SET reset_token = ?, reset_expiry = ? WHERE id = ?");
    $stmt->execute([$resetToken, $resetExpiry, $nurse['id']]);

    // Create the reset link
    $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/reset_password.html?token=" . $resetToken;

    // Email content
    $subject = "Supercare Hospital - Password Reset Request";
    $message = "
    <html>
    <head>
        <title>Password Reset Request</title>
    </head>
    <body>
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background-color: #1aaf51; color: white; padding: 20px; text-align: center;'>
                <h1>Supercare Hospital</h1>
                <h2>Password Reset Request</h2>
            </div>
            
            <div style='padding: 20px; background-color: #f9f9f9;'>
                <p>Hello {$nurse['name']},</p>
                
                <p>We received a request to reset your password for the Supercare Hospital Nurse Portal.</p>
                
                <p>If you did not make this request, please ignore this email. Your password will remain unchanged.</p>
                
                <p>To reset your password, click the button below:</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$resetLink}' 
                       style='background-color: #1aaf51; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                        Reset Password
                    </a>
                </div>
                
                <p>Or copy and paste this link into your browser:</p>
                <p style='word-break: break-all; color: #666;'>{$resetLink}</p>
                
                <p><strong>This link will expire in 1 hour for security reasons.</strong></p>
                
                <p>If you have any questions, please contact your system administrator.</p>
                
                <p>Best regards,<br>
                Supercare Hospital IT Team</p>
            </div>
            
            <div style='background-color: #333; color: white; padding: 15px; text-align: center; font-size: 12px;'>
                <p>This is an automated message. Please do not reply to this email.</p>
                <p style='margin-top: 10px;'>&copy; 2024 Supercare Hospital. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ahensananingthemcha@gmail.com';
        $mail->Password   = 'irelgxhyraptvexn';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
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
        $mail->addAddress($email, $nurse['name']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
    } catch (Exception $e) {
        throw new Exception('Failed to send email: ' . $mail->ErrorInfo);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Password reset link has been sent to your email address.'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
