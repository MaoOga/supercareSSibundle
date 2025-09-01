<?php
// Test Email Configuration
header('Content-Type: application/json');

// Include email configuration
require_once '../email/email_config.php';
require_once '../phpmailer/PHPMailer.php';
require_once '../phpmailer/SMTP.php';
require_once '../phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

try {
    $mail = new PHPMailer(true);
    
    // Server settings
    $mail->isSMTP();
    $mail->Host = $emailConfig['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $emailConfig['username'];
    $mail->Password = $emailConfig['password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $emailConfig['port'];
    
    // Enable debug output
    $mail->SMTPDebug = 2;
    
    // Recipients
    $mail->setFrom($emailConfig['from_email'], $emailConfig['from_name']);
    $mail->addAddress('ahensananingthemcha@gmail.com', 'Test User');
    
    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Email Test - SuperCare System';
    
    $emailBody = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <div style='background: #f8f9fa; padding: 20px; text-align: center;'>
            <h2 style='color: #333; margin: 0;'>Email Test</h2>
        </div>
        <div style='padding: 30px; background: white;'>
            <h3 style='color: #333;'>Hello!</h3>
            <p style='color: #666; line-height: 1.6;'>
                This is a test email to verify that the email configuration is working properly.
            </p>
            <p style='color: #666; line-height: 1.6;'>
                <strong>Test Details:</strong><br>
                - SMTP Host: {$emailConfig['host']}<br>
                - Port: {$emailConfig['port']}<br>
                - Username: {$emailConfig['username']}<br>
                - From Email: {$emailConfig['from_email']}
            </p>
            <p style='color: #666; line-height: 1.6;'>
                If you receive this email, the email system is working correctly!
            </p>
        </div>
        <div style='background: #f8f9fa; padding: 15px; text-align: center; color: #666; font-size: 12px;'>
            This is a test email from SuperCare System.
        </div>
    </div>";
    
    $mail->Body = $emailBody;
    $mail->AltBody = "This is a test email to verify email configuration.";
    
    $mail->send();
    
    echo json_encode([
        'success' => true,
        'message' => 'Test email sent successfully!',
        'email_config' => [
            'host' => $emailConfig['host'],
            'port' => $emailConfig['port'],
            'username' => $emailConfig['username'],
            'from_email' => $emailConfig['from_email']
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Email test failed: ' . $mail->ErrorInfo,
        'error_details' => $e->getMessage()
    ]);
}
?>
