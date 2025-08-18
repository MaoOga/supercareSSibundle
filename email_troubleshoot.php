<?php
// Email Troubleshooting Script for SSI Bundle System
// This script will help identify and fix email configuration issues

require_once 'config.php';
require_once 'email_config.php';

// Include PHPMailer files directly
require_once 'phpmailer/PHPMailer.php';
require_once 'phpmailer/SMTP.php';
require_once 'phpmailer/Exception.php';

// Use PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

echo "<h2>Email Configuration Troubleshooting</h2>";

// Test 1: Check current configuration
echo "<h3>1. Current Email Configuration</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
echo "<tr><th>Setting</th><th>Value</th><th>Status</th></tr>";
echo "<tr><td>SMTP Host</td><td>" . SMTP_HOST . "</td><td>" . (defined('SMTP_HOST') ? "✓" : "✗") . "</td></tr>";
echo "<tr><td>SMTP Port</td><td>" . SMTP_PORT . "</td><td>" . (defined('SMTP_PORT') ? "✓" : "✗") . "</td></tr>";
echo "<tr><td>SMTP Security</td><td>" . SMTP_SECURE . "</td><td>" . (defined('SMTP_SECURE') ? "✓" : "✗") . "</td></tr>";
echo "<tr><td>Email Username</td><td>" . EMAIL_USERNAME . "</td><td>" . (defined('EMAIL_USERNAME') ? "✓" : "✗") . "</td></tr>";
echo "<tr><td>Email Password</td><td>" . (strlen(EMAIL_PASSWORD) > 0 ? "***Set***" : "***NOT SET***") . "</td><td>" . (strlen(EMAIL_PASSWORD) > 0 ? "✓" : "✗") . "</td></tr>";
echo "<tr><td>From Email</td><td>" . EMAIL_FROM . "</td><td>" . (defined('EMAIL_FROM') ? "✓" : "✗") . "</td></tr>";
echo "</table>";

// Test 2: Check if credentials are still default
echo "<h3>2. Configuration Check</h3>";
if (EMAIL_USERNAME === 'your-email@gmail.com') {
    echo "<p style='color: red;'><strong>⚠️ WARNING:</strong> You're still using the default email username. Please update <code>email_config.php</code> with your actual email address.</p>";
} else {
    echo "<p style='color: green;'>✓ Email username is configured</p>";
}

if (EMAIL_PASSWORD === 'your-app-password') {
    echo "<p style='color: red;'><strong>⚠️ WARNING:</strong> You're still using the default password. Please update <code>email_config.php</code> with your actual email password or app password.</p>";
} else {
    echo "<p style='color: green;'>✓ Email password is configured</p>";
}

// Test 3: Test SMTP connection without authentication
echo "<h3>3. SMTP Connection Test (Without Authentication)</h3>";
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->Port = SMTP_PORT;
    $mail->SMTPSecure = (SMTP_SECURE === 'tls') ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
    
    // Disable authentication for connection test
    $mail->SMTPAuth = false;
    
    // Enable debug output
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    
    // Set a short timeout
    $mail->Timeout = 10;
    
    echo "<p>Testing connection to " . SMTP_HOST . ":" . SMTP_PORT . "...</p>";
    
    // Try to connect
    $mail->smtpConnect();
    echo "<p style='color: green;'>✓ SMTP connection successful</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ SMTP connection failed: " . $e->getMessage() . "</p>";
}

// Test 4: Test with authentication
echo "<h3>4. SMTP Authentication Test</h3>";
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->Port = SMTP_PORT;
    $mail->SMTPSecure = (SMTP_SECURE === 'tls') ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
    $mail->SMTPAuth = true;
    $mail->Username = EMAIL_USERNAME;
    $mail->Password = EMAIL_PASSWORD;
    
    // Enable debug output
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    
    // Set a short timeout
    $mail->Timeout = 10;
    
    echo "<p>Testing authentication with " . EMAIL_USERNAME . "...</p>";
    
    // Try to connect and authenticate
    $mail->smtpConnect();
    echo "<p style='color: green;'>✓ SMTP authentication successful</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ SMTP authentication failed: " . $e->getMessage() . "</p>";
    
    // Provide specific guidance based on error
    if (strpos($e->getMessage(), 'Could not authenticate') !== false) {
        echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
        echo "<h4>🔧 Authentication Fix Steps:</h4>";
        echo "<ol>";
        echo "<li><strong>For Gmail:</strong>";
        echo "<ul>";
        echo "<li>Enable 2-Factor Authentication on your Google account</li>";
        echo "<li>Go to Google Account Settings → Security → App Passwords</li>";
        echo "<li>Generate an App Password for 'Mail'</li>";
        echo "<li>Use this App Password instead of your regular password</li>";
        echo "</ul></li>";
        echo "<li><strong>For Outlook/Hotmail:</strong>";
        echo "<ul>";
        echo "<li>Use your regular email password</li>";
        echo "<li>Make sure 'Less secure app access' is enabled (if available)</li>";
        echo "</ul></li>";
        echo "<li><strong>For Yahoo:</strong>";
        echo "<ul>";
        echo "<li>Enable 2-Factor Authentication</li>";
        echo "<li>Generate an App Password</li>";
        echo "<li>Use the App Password instead of regular password</li>";
        echo "</ul></li>";
        echo "</ol>";
        echo "</div>";
    }
}

// Test 5: Common SMTP settings for different providers
echo "<h3>5. Recommended SMTP Settings by Provider</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Provider</th><th>SMTP Host</th><th>Port</th><th>Security</th><th>Authentication</th></tr>";
echo "<tr><td>Gmail</td><td>smtp.gmail.com</td><td>587</td><td>TLS</td><td>App Password Required</td></tr>";
echo "<tr><td>Outlook/Hotmail</td><td>smtp-mail.outlook.com</td><td>587</td><td>TLS</td><td>Regular Password</td></tr>";
echo "<tr><td>Yahoo</td><td>smtp.mail.yahoo.com</td><td>587</td><td>TLS</td><td>App Password Required</td></tr>";
echo "<tr><td>Custom SMTP</td><td>your-smtp.com</td><td>587</td><td>TLS</td><td>Varies</td></tr>";
echo "</table>";

// Test 6: Quick configuration fix
echo "<h3>6. Quick Configuration Fix</h3>";
echo "<p>If you're using Gmail, update your <code>email_config.php</code> with these settings:</p>";
echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
echo "// Gmail Configuration\n";
echo "define('SMTP_HOST', 'smtp.gmail.com');\n";
echo "define('SMTP_PORT', 587);\n";
echo "define('SMTP_SECURE', 'tls');\n";
echo "define('EMAIL_USERNAME', 'your-actual-gmail@gmail.com');\n";
echo "define('EMAIL_PASSWORD', 'your-16-digit-app-password');\n";
echo "define('EMAIL_FROM', 'noreply@supercare.com');\n";
echo "define('EMAIL_FROM_NAME', 'SSI Bundle System');\n";
echo "</pre>";

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Update your email settings in <code>email_config.php</code></li>";
echo "<li>For Gmail: Generate an App Password and use it instead of your regular password</li>";
echo "<li>Test again using <a href='test_email.php'>test_email.php</a></li>";
echo "<li>If still having issues, try a different email provider</li>";
echo "</ol>";

echo "<p><a href='test_email.php' style='background: #1aaf51; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test Email Configuration</a></p>";
?>
