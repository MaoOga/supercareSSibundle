<?php
// Test Email Script for SSI Bundle System
// Use this to test your email configuration

require_once '../database/config.php';
require_once 'email_config.php';

// Include PHPMailer files directly
require_once 'phpmailer/PHPMailer.php';
require_once 'phpmailer/SMTP.php';
require_once 'phpmailer/Exception.php';

// Use PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Test email address (change this to your email)
$testEmail = 'your-test-email@gmail.com'; // Change this to your email

echo "<h2>Email Configuration Test</h2>";
echo "<p>Testing email configuration...</p>";

try {
    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = EMAIL_USERNAME;
    $mail->Password   = EMAIL_PASSWORD;
    $mail->SMTPSecure = (SMTP_SECURE === 'tls') ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = SMTP_PORT;
    
    // Enable debug output
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;

    // Recipients
    $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
    $mail->addAddress($testEmail, 'Test User');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'SSI Bundle System - Email Test';
    $mail->Body    = '
    <html>
    <head>
        <title>Email Test</title>
    </head>
    <body>
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #1aaf51; color: white; padding: 20px; text-align: center;">
                <h1>SSI Bundle System</h1>
                <h2>Email Configuration Test</h2>
            </div>
            
            <div style="padding: 20px; background-color: #f9f9f9;">
                <p>Hello,</p>
                
                <p>This is a test email to verify that your email configuration is working correctly.</p>
                
                <p>If you received this email, your SMTP settings are configured properly!</p>
                
                <p><strong>Configuration Details:</strong></p>
                <ul>
                    <li>SMTP Host: ' . SMTP_HOST . '</li>
                    <li>SMTP Port: ' . SMTP_PORT . '</li>
                    <li>SMTP Security: ' . SMTP_SECURE . '</li>
                    <li>From Email: ' . EMAIL_FROM . '</li>
                </ul>
                
                <p>Best regards,<br>
                SSI Bundle System Team</p>
            </div>
            
            <div style="background-color: #333; color: white; padding: 15px; text-align: center; font-size: 12px;">
                <p>This is a test message. Please delete after verification.</p>
            </div>
        </div>
    </body>
    </html>
    ';

    $mail->send();
    echo "<p style='color: green;'><strong>✓ SUCCESS:</strong> Test email sent successfully to {$testEmail}</p>";
    echo "<p>Please check your email inbox to confirm receipt.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>✗ ERROR:</strong> Failed to send test email</p>";
    echo "<p><strong>Error Details:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Mail Error Info:</strong> " . $mail->ErrorInfo . "</p>";
}

echo "<hr>";
echo "<h3>Troubleshooting Tips:</h3>";
echo "<ul>";
echo "<li>Make sure you've updated the email settings in <code>email_config.php</code></li>";
echo "<li>For Gmail: Enable 2-factor authentication and generate an App Password</li>";
echo "<li>Check that your SMTP server and port are correct</li>";
echo "<li>Verify your email username and password</li>";
echo "<li>Make sure your server allows outbound SMTP connections</li>";
echo "</ul>";

echo "<h3>Common SMTP Settings:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Provider</th><th>SMTP Host</th><th>Port</th><th>Security</th></tr>";
echo "<tr><td>Gmail</td><td>smtp.gmail.com</td><td>587</td><td>TLS</td></tr>";
echo "<tr><td>Outlook/Hotmail</td><td>smtp-mail.outlook.com</td><td>587</td><td>TLS</td></tr>";
echo "<tr><td>Yahoo</td><td>smtp.mail.yahoo.com</td><td>587</td><td>TLS</td></tr>";
echo "</table>";
?>
