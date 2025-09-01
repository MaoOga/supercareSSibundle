<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simple database connection
$host = 'localhost';
$dbname = 'supercare_ssi';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

header('Content-Type: application/json');

// Check if super admin is logged in
session_name('SUPER_ADMIN_SESSION');
session_start();
if (!isset($_SESSION['super_admin_logged_in']) || $_SESSION['super_admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Handle file deletion
if (isset($_POST['filename']) && !empty($_POST['filename'])) {
    $filename = basename($_POST['filename']); // Sanitize filename
    $filepath = __DIR__ . '/backups/' . $filename;
    
    if (file_exists($filepath) && strpos($filename, 'ssi_bundle_') === 0) {
        $fileSize = filesize($filepath);
        
        if (unlink($filepath)) {
            // Log the deletion (if audit logger exists)
            if (file_exists(__DIR__ . '/audit_logger.php')) {
                require_once '../audit/audit_logger.php';
                $auditLogger = new AuditLogger($pdo);
                $auditLogger->logBackupDelete('SUPER_ADMIN', $filepath, formatBytes($fileSize));
            }
            
            echo json_encode(['success' => true, 'message' => 'Backup file deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete backup file']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'File not found or invalid filename']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No filename provided']);
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>
