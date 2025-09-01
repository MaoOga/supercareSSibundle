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
    echo json_encode([
        'success' => false, 
        'message' => 'Unauthorized access - Please log in as super admin',
        'session_data' => $_SESSION
    ]);
    exit;
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

try {
    // Get backup files
    $backupDir = __DIR__ . '/backups/';
    $backupFiles = [];

    if (is_dir($backupDir)) {
        $files = glob($backupDir . 'ssi_bundle_*.sql*');
        foreach ($files as $file) {
            $backupFiles[] = [
                'name' => basename($file),
                'size' => formatBytes(filesize($file)),
                'date' => filectime($file),
                'path' => $file
            ];
        }
        
        // Sort by date (newest first)
        usort($backupFiles, function($a, $b) {
            return $b['date'] - $a['date'];
        });
    }

    echo json_encode([
        'success' => true,
        'files' => $backupFiles
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error retrieving backup files: ' . $e->getMessage()]);
}
?>
