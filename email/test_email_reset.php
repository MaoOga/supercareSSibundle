<?php
require_once '../database/config.php';
require_once 'email_config.php';

// Include PHPMailer files
require_once 'phpmailer/PHPMailer.php';
require_once 'phpmailer/SMTP.php';
require_once 'phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

echo "<h2>Email Reset Test</h2>";

try {
    // Test email configuration
    echo "<h3>Email Configuration:</h3>";
    echo "<p>SMTP Host: " . SMTP_HOST . "</p>";
    echo "<p>SMTP Port: " . SMTP_PORT . "</p>";
    echo "<p>SMTP Secure: " . SMTP_SECURE . "</p>";
    echo "<p>Email Username: " . EMAIL_USERNAME . "</p>";
    echo "<p>Email From: " . EMAIL_FROM . "</p>";
    
    // Show all nurses
    $stmt = $pdo->query("SELECT id, nurse_id, name, email FROM nurses ORDER BY id");
    $nurses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Available Nurses:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nurse ID</th><th>Name</th><th>Email</th><th>Action</th></tr>";
    foreach ($nurses as $nurse) {
        echo "<tr>";
        echo "<td>" . $nurse['id'] . "</td>";
        echo "<td>" . $nurse['nurse_id'] . "</td>";
        echo "<td>" . $nurse['name'] . "</td>";
        echo "<td>" . $nurse['email'] . "</td>";
        echo "<td><a href='?test_email=" . $nurse['id'] . "'>Test Reset Email</a></td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Handle test email request
    if (isset($_GET['test_email'])) {
        $nurseId = $_GET['test_email'];
        
        // Get nurse details
        $stmt = $pdo->prepare("SELECT id, nurse_id, name, email FROM nurses WHERE id = ?");
        $stmt->execute([$nurseId]);
        $nurse = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($nurse) {
            echo "<h3>Testing Reset Email for: " . $nurse['name'] . "</h3>";
            
            // Generate a unique reset token
            $resetToken = bin2hex(random_bytes(32));
            $resetExpiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store the reset token in the database
            $stmt = $pdo->prepare("UPDATE nurses SET reset_token = ?, reset_expiry = ? WHERE id = ?");
            $stmt->execute([$resetToken, $resetExpiry, $nurse['id']]);
            
            echo "<p>✅ Reset token generated and stored in database</p>";
            echo "<p>Token: " . $resetToken . "</p>";
            echo "<p>Expiry: " . $resetExpiry . "</p>";
            
            // Create the reset link
            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/reset_password.html?token=" . $resetToken;
            
            // Email content
            $subject = "SSI Bundle - Password Reset Test";
            $message = "
            <html>
            <head>
                <title>Password Reset Test</title>
            </head>
            <body>
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background-color: #1aaf51; color: white; padding: 20px; text-align: center;'>
                        <h1>SSI Bundle System</h1>
                        <h2>Password Reset Test</h2>
                    </div>
                    
                    <div style='padding: 20px; background-color: #f9f9f9;'>
                        <p>Hello {$nurse['name']},</p>
                        
                        <p>This is a test email for password reset functionality.</p>
                        
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
                        
                        <p>Best regards,<br>
                        SSI Bundle System Team</p>
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
                $mail->Host       = SMTP_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = EMAIL_USERNAME;
                $mail->Password   = EMAIL_PASSWORD;
                $mail->SMTPSecure = (SMTP_SECURE === 'tls') ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = SMTP_PORT;
                
                // Debug settings
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                
                // Recipients
                $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
                $mail->addAddress($nurse['email'], $nurse['name']);
                
                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $message;
                
                $mail->send();
                echo "<p style='color: green;'>✅ Email sent successfully!</p>";
                echo "<p>Check the email at: " . $nurse['email'] . "</p>";
                echo "<p>Reset link: <a href='" . $resetLink . "' target='_blank'>" . $resetLink . "</a></p>";
                
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Email failed to send: " . $mail->ErrorInfo . "</p>";
                echo "<p>This could be due to:</p>";
                echo "<ul>";
                echo "<li>Gmail app password has expired</li>";
                echo "<li>Gmail security settings blocking the connection</li>";
                echo "<li>Network connectivity issues</li>";
                echo "<li>SMTP configuration problems</li>";
                echo "</ul>";
            }
        } else {
            echo "<p style='color: red;'>Nurse not found</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
