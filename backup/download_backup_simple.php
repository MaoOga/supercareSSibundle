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
    die('Database connection failed');
}

// Check if super admin is logged in
session_name('SUPER_ADMIN_SESSION');
session_start();
if (!isset($_SESSION['super_admin_logged_in']) || $_SESSION['super_admin_logged_in'] !== true) {
    http_response_code(401);
    die('Unauthorized access');
}

// Handle file download
if (isset($_GET['file']) && !empty($_GET['file'])) {
    $filename = basename($_GET['file']); // Sanitize filename
    $filepath = __DIR__ . '/backups/' . $filename;
    
    if (file_exists($filepath) && strpos($filename, 'ssi_bundle_') === 0) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    } else {
        http_response_code(404);
        die('File not found');
    }
} else {
    http_response_code(400);
    die('No file specified');
}
?>
