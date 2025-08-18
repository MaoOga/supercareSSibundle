<?php
// Test script for backup email notifications
require_once 'email_config.php';

// Load PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if PHPMailer exists
$phpmailerExists = file_exists(__DIR__ . '/phpmailer/PHPMailer.php');
if ($phpmailerExists) {
    require __DIR__ . '/phpmailer/PHPMailer.php';
    require __DIR__ . '/phpmailer/SMTP.php';
    require __DIR__ . '/phpmailer/Exception.php';
}

function testEmailNotification($subject, $body) {
    global $phpmailerExists;
    
    if (!$phpmailerExists) {
        echo "❌ PHPMailer not available. Cannot test email notifications.\n";
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = EMAIL_USERNAME;
        $mail->Password   = EMAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Use professional email configuration
        $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
        $mail->addAddress(EMAIL_USERNAME); // Send to yourself for testing

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        echo "✅ Email sent successfully!\n";
        return true;
    } catch (Exception $e) {
        echo "❌ Failed to send email: " . $mail->ErrorInfo . "\n";
        return false;
    }
}

function createTestSuccessEmailBody() {
    $currentTime = date('Y-m-d H:i:s');
    $serverName = $_SERVER['SERVER_NAME'] ?? 'SSI Bundle System';
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #28a745; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f8f9fa; padding: 20px; border-radius: 0 0 8px 8px; }
            .success-icon { font-size: 48px; margin-bottom: 10px; }
            .details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
            .detail-row { display: flex; justify-content: space-between; margin: 8px 0; }
            .label { font-weight: bold; color: #555; }
            .value { color: #333; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <div class='success-icon'>✅</div>
                <h1>Database Backup Completed Successfully</h1>
                <p>SSI Bundle System - Automated Backup Notification</p>
            </div>
            <div class='content'>
                <p>Dear System Administrator,</p>
                <p>The automated database backup for the <strong>SSI Bundle System</strong> has been completed successfully.</p>
                
                <div class='details'>
                    <div class='detail-row'>
                        <span class='label'>Database Name:</span>
                        <span class='value'>ssi_bundle_test</span>
                    </div>
                    <div class='detail-row'>
                        <span class='label'>Backup File:</span>
                        <span class='value'>ssi_bundle_2025-01-27_15-30-00.sql</span>
                    </div>
                    <div class='detail-row'>
                        <span class='label'>File Size:</span>
                        <span class='value'>2.5 MB</span>
                    </div>
                    <div class='detail-row'>
                        <span class='label'>Backup Time:</span>
                        <span class='value'>{$currentTime}</span>
                    </div>
                    <div class='detail-row'>
                        <span class='label'>Server:</span>
                        <span class='value'>{$serverName}</span>
                    </div>
                </div>
                
                <p><strong>What's included in this backup:</strong></p>
                <ul>
                    <li>All patient records and medical data</li>
                    <li>Nurse and surgeon account information</li>
                    <li>System configuration and settings</li>
                    <li>Audit logs and activity records</li>
                </ul>
                
                <p><strong>Next Steps:</strong></p>
                <ul>
                    <li>Verify the backup file integrity</li>
                    <li>Store a copy in a secure off-site location</li>
                    <li>Test the backup restoration process</li>
                </ul>
                
                <p>This backup is part of our automated daily backup schedule to ensure data security and business continuity.</p>
                
                <div class='footer'>
                    <p>This is an automated message from the SSI Bundle System.<br>
                    Please do not reply to this email.</p>
                </div>
            </div>
        </div>
    </body>
    </html>";
}

function createTestErrorEmailBody() {
    $currentTime = date('Y-m-d H:i:s');
    $serverName = $_SERVER['SERVER_NAME'] ?? 'SSI Bundle System';
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #dc3545; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f8f9fa; padding: 20px; border-radius: 0 0 8px 8px; }
            .error-icon { font-size: 48px; margin-bottom: 10px; }
            .details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
            .error-box { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 15px 0; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <div class='error-icon'>❌</div>
                <h1>Database Backup Failed</h1>
                <p>SSI Bundle System - Critical Alert</p>
            </div>
            <div class='content'>
                <p>Dear System Administrator,</p>
                <p><strong>URGENT:</strong> The automated database backup for the <strong>SSI Bundle System</strong> has failed.</p>
                
                <div class='details'>
                    <p><strong>Database Name:</strong> ssi_bundle_test</p>
                    <p><strong>Failure Time:</strong> {$currentTime}</p>
                    <p><strong>Server:</strong> {$serverName}</p>
                </div>
                
                <div class='error-box'>
                    <h3>Error Details:</h3>
                    <p>This is a test error message to verify email notification functionality.</p>
                </div>
                
                <p><strong>Immediate Action Required:</strong></p>
                <ul>
                    <li>Check database connectivity and permissions</li>
                    <li>Verify mysqldump installation and path</li>
                    <li>Review server disk space and backup directory permissions</li>
                    <li>Attempt manual backup to verify the issue</li>
                    <li>Contact system administrator if the problem persists</li>
                </ul>
                
                <p><strong>Impact:</strong> Without successful backups, your data may be at risk in case of system failure or data corruption.</p>
                
                <div class='footer'>
                    <p>This is an automated alert from the SSI Bundle System.<br>
                    Please do not reply to this email.</p>
                </div>
            </div>
        </div>
    </body>
    </html>";
}

// Test the email notifications
echo "🧪 Testing SSI Bundle Backup Email Notifications\n";
echo "================================================\n\n";

echo "📧 Email Configuration:\n";
echo "   From: " . EMAIL_FROM . " (" . EMAIL_FROM_NAME . ")\n";
echo "   To: " . EMAIL_USERNAME . "\n";
echo "   SMTP: smtp.gmail.com:587\n\n";

echo "1️⃣ Testing Success Email Notification...\n";
$successResult = testEmailNotification(
    "✅ SSI Bundle Database Backup Completed Successfully (TEST)",
    createTestSuccessEmailBody()
);

echo "\n2️⃣ Testing Error Email Notification...\n";
$errorResult = testEmailNotification(
    "❌ SSI Bundle Database Backup Failed (TEST)",
    createTestErrorEmailBody()
);

echo "\n📊 Test Results:\n";
echo "   Success Email: " . ($successResult ? "✅ PASSED" : "❌ FAILED") . "\n";
echo "   Error Email: " . ($errorResult ? "✅ PASSED" : "❌ FAILED") . "\n";

if ($successResult && $errorResult) {
    echo "\n🎉 All email tests passed! Your backup notification system is ready.\n";
    echo "   Check your email inbox for the test messages.\n";
} else {
    echo "\n⚠️  Some tests failed. Please check your email configuration.\n";
}

echo "\n💡 Next Steps:\n";
echo "   1. Verify you received the test emails\n";
echo "   2. Run a real backup to test the actual notification system\n";
echo "   3. Check the backup log for email delivery confirmation\n";
?>
