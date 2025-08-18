<?php
// Set timezone to UTC for consistency
date_default_timezone_set('UTC');

// Load PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if PHPMailer exists, if not, we'll handle it gracefully
$phpmailerExists = file_exists(__DIR__ . '/phpmailer/PHPMailer.php');
if ($phpmailerExists) {
    require __DIR__ . '/phpmailer/PHPMailer.php';
    require __DIR__ . '/phpmailer/SMTP.php';
    require __DIR__ . '/phpmailer/Exception.php';
}

// Load configuration
require_once 'config.php';
require_once 'email_config.php';
require_once 'audit_logger.php';

// --- Configuration ---
$dbHost     = $host;
$dbUser     = $username;
$dbPass     = $password;
$dbName     = $dbname;

$backupDir  = __DIR__ . '/backups/';
$deleteOld  = true;
$retainDays = 90; // Keep backups for 3 months (90 days)
$compress   = false;

// Email configuration using email_config.php
$emailFrom     = EMAIL_USERNAME;
$emailTo       = EMAIL_USERNAME; // Send notifications to the same email
$emailPassword = EMAIL_PASSWORD;
$enableEmail   = true; // Enable email notifications by default

// --- Ensure Backup Directory Exists ---
if (!is_dir($backupDir)) {
    $mkdirSuccess = mkdir($backupDir, 0755, true);
    if (!$mkdirSuccess && !is_dir($backupDir)) {
        logMessage("Error: Backup directory {$backupDir} could not be created. Please create it manually and check permissions.", true);
        exit(1);
    }
}

$date = date('Y-m-d_H-i-s');
$backupFileName = "ssi_bundle_{$date}.sql";
$backupFilePath = $backupDir . $backupFileName;

// Try to find mysqldump in common locations
$mysqldumpPath = null;
$possiblePaths = [
    'C:\\xampp\\mysql\\bin\\mysqldump.exe',
    'C:\\wamp64\\bin\\mysql\\mysql8.0.31\\bin\\mysqldump.exe',
    'C:\\wamp\\bin\\mysql\\mysql8.0.31\\bin\\mysqldump.exe',
    '/usr/bin/mysqldump',
    '/usr/local/bin/mysqldump',
    '/opt/mysql/bin/mysqldump'
];

foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        $mysqldumpPath = $path;
        break;
    }
}

if (!$mysqldumpPath) {
    // Try to find mysqldump in PATH
    $output = shell_exec('which mysqldump 2>/dev/null');
    if ($output) {
        $mysqldumpPath = trim($output);
    } else {
        logMessage("Error: mysqldump not found. Please install MySQL or verify the path.", true);
        exit(1);
    }
}

// Create backup command
$command = "\"{$mysqldumpPath}\" --host={$dbHost} --user={$dbUser} --password=\"{$dbPass}\" {$dbName} > \"{$backupFilePath}\"";

$output = null;
$returnVar = null;

exec($command, $output, $returnVar);

if ($returnVar === 0) {
    $fileSize = filesize($backupFilePath);
    $fileSizeFormatted = formatBytes($fileSize);
    
    $message = "MySQL backup for database '{$dbName}' successful: {$backupFilePath} (Size: {$fileSizeFormatted})";
    logMessage($message);

    // Log audit entry
    $auditLogger->logBackupCreate('SYSTEM', $backupFilePath, $fileSizeFormatted);

    // Send email notification if enabled
    if ($enableEmail && $phpmailerExists) {
        sendEmailNotification(
            "✅ SSI Bundle Database Backup Completed Successfully",
            createSuccessEmailBody($dbName, basename($backupFilePath), $fileSizeFormatted, $backupFilePath)
        );
    }
    
    // Return success response for AJAX calls
    if (isset($_GET['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => $message,
            'file' => basename($backupFilePath),
            'size' => $fileSizeFormatted,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
} else {
    $errorMessage = "MySQL backup for database '{$dbName}' FAILED. Return code: {$returnVar}.";
    if (!empty($output)) {
        $errorMessage .= " Output: " . implode("\n", $output);
    } else {
        $errorMessage .= " No output from command. Check mysqldump path and permissions.";
    }
    logMessage($errorMessage, true);
    
    // Log audit entry for failed backup
    $auditLogger->logBackupCreate('SYSTEM', 'backup_failed', '0 B', 'FAILED', $errorMessage);
    
    // Send error email notification if enabled
    if ($enableEmail && $phpmailerExists) {
        sendEmailNotification(
            "❌ SSI Bundle Database Backup Failed",
            createErrorEmailBody($dbName, $errorMessage)
        );
    }
    
    // Return error response for AJAX calls
    if (isset($_GET['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $errorMessage
        ]);
        exit;
    }
}

// --- Cleanup old backups ---
if ($deleteOld) {
    logMessage("Cleaning up old backups (older than {$retainDays} days) in {$backupDir}");
    try {
        $files = glob($backupDir . 'ssi_bundle_*.sql*');
        if ($files) {
            $deletedCount = 0;
            foreach ($files as $file) {
                if (is_file($file)) {
                    if (time() - filectime($file) >= ($retainDays * 24 * 60 * 60)) {
                        unlink($file);
                        logMessage("Deleted old backup: " . basename($file));
                        $deletedCount++;
                    }
                }
            }
            logMessage("Cleanup completed. Deleted {$deletedCount} old backup files.");
            
            // Send cleanup notification if files were deleted
            if ($deletedCount > 0 && $enableEmail && $phpmailerExists) {
                sendEmailNotification(
                    "🧹 SSI Bundle Backup Cleanup Completed",
                    createCleanupEmailBody($deletedCount, $retainDays)
                );
            }
        }
    } catch (Exception $e) {
        $message = "Error during backup cleanup: " . $e->getMessage();
        logMessage($message, true);
        
        // Send cleanup error notification
        if ($enableEmail && $phpmailerExists) {
            sendEmailNotification(
                "⚠️ SSI Bundle Backup Cleanup Failed",
                createCleanupErrorEmailBody($e->getMessage())
            );
        }
    }
}

// --- Helper Functions ---

function logMessage($message, $isError = false) {
    $logFile = __DIR__ . '/backup_log.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] " . ($isError ? "[ERROR] " : "[INFO] ") . $message . "\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Also output to console if not AJAX request
    if (!isset($_GET['ajax'])) {
        echo $logEntry;
    }
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

function sendEmailNotification($subject, $body) {
    global $emailFrom, $emailTo, $emailPassword, $phpmailerExists;
    
    if (!$phpmailerExists) {
        logMessage("PHPMailer not available. Email notification skipped.", true);
        return;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $emailFrom;
        $mail->Password   = $emailPassword;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Use professional email configuration
        $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
        $mail->addAddress($emailTo);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        logMessage("Email notification sent to {$emailTo}.");
    } catch (Exception $e) {
        logMessage("Failed to send email: " . $mail->ErrorInfo, true);
    }
}

function createSuccessEmailBody($dbName, $fileName, $fileSize, $filePath) {
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
                        <span class='value'>{$dbName}</span>
                    </div>
                    <div class='detail-row'>
                        <span class='label'>Backup File:</span>
                        <span class='value'>{$fileName}</span>
                    </div>
                    <div class='detail-row'>
                        <span class='label'>File Size:</span>
                        <span class='value'>{$fileSize}</span>
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

function createErrorEmailBody($dbName, $errorMessage) {
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
                    <p><strong>Database Name:</strong> {$dbName}</p>
                    <p><strong>Failure Time:</strong> {$currentTime}</p>
                    <p><strong>Server:</strong> {$serverName}</p>
                </div>
                
                <div class='error-box'>
                    <h3>Error Details:</h3>
                    <p>" . htmlspecialchars($errorMessage) . "</p>
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

function createCleanupEmailBody($deletedCount, $retainDays) {
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
            .header { background: #17a2b8; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f8f9fa; padding: 20px; border-radius: 0 0 8px 8px; }
            .cleanup-icon { font-size: 48px; margin-bottom: 10px; }
            .details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <div class='cleanup-icon'>🧹</div>
                <h1>Backup Cleanup Completed</h1>
                <p>SSI Bundle System - Maintenance Notification</p>
            </div>
            <div class='content'>
                <p>Dear System Administrator,</p>
                <p>The automated backup cleanup process for the <strong>SSI Bundle System</strong> has been completed successfully.</p>
                
                <div class='details'>
                    <p><strong>Files Deleted:</strong> {$deletedCount} old backup files</p>
                    <p><strong>Retention Policy:</strong> {$retainDays} days</p>
                    <p><strong>Cleanup Time:</strong> {$currentTime}</p>
                    <p><strong>Server:</strong> {$serverName}</p>
                </div>
                
                <p><strong>What this means:</strong></p>
                <ul>
                    <li>Old backup files older than {$retainDays} days have been removed</li>
                    <li>Disk space has been freed up</li>
                    <li>Only recent backups are retained for quick access</li>
                    <li>This helps maintain optimal system performance</li>
                </ul>
                
                <p><strong>Current Status:</strong> Your backup system is running efficiently with proper maintenance.</p>
                
                <div class='footer'>
                    <p>This is an automated maintenance notification from the SSI Bundle System.<br>
                    Please do not reply to this email.</p>
                </div>
            </div>
        </div>
    </body>
    </html>";
}

function createCleanupErrorEmailBody($errorMessage) {
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
            .header { background: #ffc107; color: #333; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f8f9fa; padding: 20px; border-radius: 0 0 8px 8px; }
            .warning-icon { font-size: 48px; margin-bottom: 10px; }
            .details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
            .error-box { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin: 15px 0; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <div class='warning-icon'>⚠️</div>
                <h1>Backup Cleanup Warning</h1>
                <p>SSI Bundle System - Maintenance Alert</p>
            </div>
            <div class='content'>
                <p>Dear System Administrator,</p>
                <p>The automated backup cleanup process for the <strong>SSI Bundle System</strong> encountered an issue.</p>
                
                <div class='details'>
                    <p><strong>Issue Time:</strong> {$currentTime}</p>
                    <p><strong>Server:</strong> {$serverName}</p>
                </div>
                
                <div class='error-box'>
                    <h3>Error Details:</h3>
                    <p>" . htmlspecialchars($errorMessage) . "</p>
                </div>
                
                <p><strong>Impact:</strong> Old backup files may not have been cleaned up properly, which could lead to disk space issues over time.</p>
                
                <p><strong>Recommended Action:</strong></p>
                <ul>
                    <li>Check backup directory permissions</li>
                    <li>Verify disk space availability</li>
                    <li>Review the backup log for detailed error information</li>
                    <li>Consider manual cleanup if necessary</li>
                </ul>
                
                <div class='footer'>
                    <p>This is an automated maintenance alert from the SSI Bundle System.<br>
                    Please do not reply to this email.</p>
                </div>
            </div>
        </div>
    </body>
    </html>";
}

// If not AJAX request, show a simple success page
if (!isset($_GET['ajax'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SSI Bundle - Backup System</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    </head>
    <body class="bg-gray-100 min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
            <div class="text-center">
                <i class="fas fa-database text-4xl text-green-500 mb-4"></i>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Backup System</h1>
                <p class="text-gray-600 mb-6">SSI Bundle Database Backup</p>
                
                <?php if ($returnVar === 0): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <i class="fas fa-check-circle mr-2"></i>
                        Backup completed successfully!
                    </div>
                    <p class="text-sm text-gray-600 mb-4">
                        File: <?php echo basename($backupFilePath); ?><br>
                        Size: <?php echo formatBytes(filesize($backupFilePath)); ?><br>
                        Time: <?php echo date('Y-m-d H:i:s'); ?>
                    </p>
                <?php else: ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Backup failed! Check the log for details.
                    </div>
                <?php endif; ?>
                
                <a href="admin.html" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Admin Panel
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>
