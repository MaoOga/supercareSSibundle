<?php
// Test Email Configuration for Super Admin OTP
header('Content-Type: application/json');

// Include required files
require_once '../email/email_config.php';
require_once '../phpmailer/PHPMailer.php';
require_once '../phpmailer/SMTP.php';
require_once '../phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

try {
    $mail = new PHPMailer(true);
    
    // Enable debug output
    $mail->SMTPDebug = 2;
    
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
    $mail->addAddress('ahensananingthemcha@gmail.com', 'Test User');
    
    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email - Super Admin OTP System';
    
    $emailBody = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <div style='background: #007bff; color: white; padding: 20px; text-align: center;'>
            <h1>Test Email</h1>
            <p>SuperCare System - OTP Test</p>
        </div>
        <div style='padding: 20px;'>
            <p>This is a test email to verify that the OTP system is working correctly.</p>
            <p>If you receive this email, the email configuration is working properly.</p>
            <p>Test OTP Code: <strong>123456</strong></p>
        </div>
    </div>";
    
    $mail->Body = $emailBody;
    $mail->AltBody = "Test email from SuperCare System OTP. Test OTP: 123456";
    
    $mail->send();
    
    echo json_encode([
        'success' => true,
        'message' => 'Test email sent successfully!',
        'details' => 'Email configuration is working properly'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Email test failed',
        'error' => $mail->ErrorInfo,
        'details' => $e->getMessage()
    ]);
}
?>
