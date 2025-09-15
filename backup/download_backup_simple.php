<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Super Admin session authentication required
require_once '../auth/super_admin_session_config.php';

// Check if super admin is logged in
if (!isSuperAdminLoggedIn()) {
    http_response_code(401);
    header('Content-Type: text/plain');
    die('Super admin access required');
}

// Handle file download
if (isset($_GET['file']) && !empty($_GET['file'])) {
    $filename = basename($_GET['file']); // Sanitize filename
    $filepath = __DIR__ . '/backups/' . $filename;
    
    // Debug information
    error_log("Download request for file: " . $filename);
    error_log("Full file path: " . $filepath);
    error_log("File exists: " . (file_exists($filepath) ? 'Yes' : 'No'));
    
    if (file_exists($filepath) && strpos($filename, 'ssi_bundle_') === 0) {
        // Clear any previous output
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Set headers for file download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        
        // Output the file
        readfile($filepath);
        exit;
    } else {
        http_response_code(404);
        header('Content-Type: text/plain');
        die('File not found: ' . $filename);
    }
} else {
    http_response_code(400);
    header('Content-Type: text/plain');
    die('No file specified');
}
?>
