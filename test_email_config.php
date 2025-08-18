<?php
// Test Email Configuration
echo "<h2>🔧 Test Email Configuration</h2>";

// Include email config
require_once 'email_config.php';
require_once 'phpmailer/PHPMailer.php';
require_once 'phpmailer/SMTP.php';
require_once 'phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $testEmail = $_POST['test_email'] ?? '';
    
    if (!empty($testEmail)) {
        echo "<h3>Testing Email Configuration:</h3>";
        
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->SMTPDebug = 2; // Enable verbose debug output
            $mail->isSMTP();
            $mail->Host = $emailConfig['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $emailConfig['username'];
            $mail->Password = $emailConfig['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $emailConfig['port'];
            
            // Recipients
            $mail->setFrom($emailConfig['from_email'], $emailConfig['from_name']);
            $mail->addAddress($testEmail);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'SuperCare Email Test';
            $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: #f8f9fa; padding: 20px; text-align: center;'>
                    <h2 style='color: #333; margin: 0;'>SuperCare Email Test</h2>
                </div>
                <div style='padding: 30px; background: white;'>
                    <h3 style='color: #333;'>Hello!</h3>
                    <p style='color: #666; line-height: 1.6;'>
                        This is a test email to verify that the SuperCare email configuration is working correctly.
                    </p>
                    <p style='color: #666; line-height: 1.6;'>
                        <strong>Test Details:</strong><br>
                        • Sent from: {$emailConfig['from_email']}<br>
                        • SMTP Server: {$emailConfig['host']}<br>
                        • Port: {$emailConfig['port']}<br>
                        • Time: " . date('Y-m-d H:i:s') . "
                    </p>
                    <p style='color: #666; line-height: 1.6;'>
                        If you received this email, the OTP system should work correctly!
                    </p>
                </div>
                <div style='background: #f8f9fa; padding: 15px; text-align: center; color: #666; font-size: 12px;'>
                    This is a test email from SuperCare System
                </div>
            </div>";
            
            $mail->AltBody = "This is a test email from SuperCare System. If you received this, the email configuration is working correctly.";
            
            $mail->send();
            echo "<div style='background: #e8f5e8; border: 1px solid #4caf50; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>✅ Email Sent Successfully!</h3>";
            echo "<p>Test email sent to: <strong>$testEmail</strong></p>";
            echo "<p>Check your email inbox (and spam folder) for the test message.</p>";
            echo "</div>";
            
        } catch (Exception $e) {
            echo "<div style='background: #f8d7da; border: 1px solid #dc3545; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>❌ Email Test Failed</h3>";
            echo "<p><strong>Error:</strong> " . $mail->ErrorInfo . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p style='color: red;'>❌ Please enter a test email address</p>";
    }
}

// Show current email configuration
echo "<h3>Current Email Configuration:</h3>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<p><strong>SMTP Host:</strong> {$emailConfig['host']}</p>";
echo "<p><strong>SMTP Port:</strong> {$emailConfig['port']}</p>";
echo "<p><strong>Username:</strong> {$emailConfig['username']}</p>";
echo "<p><strong>From Email:</strong> {$emailConfig['from_email']}</p>";
echo "<p><strong>From Name:</strong> {$emailConfig['from_name']}</p>";
echo "</div>";

// Test email form
echo "<h3>Send Test Email:</h3>";
echo "<form method='POST' style='background: #f8f9fa; padding: 20px; border-radius: 5px;'>";
echo "<p><label>Test Email Address: <input type='email' name='test_email' placeholder='your-email@example.com' required style='width: 100%; padding: 8px; margin: 5px 0;'></label></p>";
echo "<button type='submit' style='background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Send Test Email</button>";
echo "</form>";

echo "<h3>Common Email Issues & Solutions:</h3>";
echo "<div style='background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<h4>Gmail Issues:</h4>";
echo "<ul>";
echo "<li>Make sure 2-factor authentication is enabled</li>";
echo "<li>Generate an App Password (not your regular password)</li>";
echo "<li>Allow less secure apps (if not using App Password)</li>";
echo "</ul>";
echo "<h4>Other Providers:</h4>";
echo "<ul>";
echo "<li>Check if SMTP is enabled in your email settings</li>";
echo "<li>Verify the correct SMTP server and port</li>";
echo "<li>Ensure the password is correct</li>";
echo "</ul>";
echo "</div>";

echo "<h3>Quick Links:</h3>";
echo "<p><a href='super_admin_login.html' style='color: #007bff;'>→ Test Super Admin Login</a></p>";
echo "<p><a href='create_test_super_admin.php' style='color: #007bff;'>→ Create Super Admin Account</a></p>";
?>
