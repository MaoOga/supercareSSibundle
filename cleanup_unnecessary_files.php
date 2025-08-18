<?php
// Cleanup script to remove unnecessary files
echo "Starting cleanup of unnecessary files...\n\n";

$filesToDelete = [
    // Old Super Admin Files
    'super_admin_dashboard.html',
    'secure_super_admin_login.html', 
    'secure_super_admin.php',
    'super_admin_logout.php',
    'get_admins.php',
    'get_nurses.php',
    'create_admin.php',
    'delete_admin.php',
    
    // Test Files
    'test_php_simple.php',
    'test_php_ultra_simple.php',
    'test_web_php.php',
    'test_super_admin_login.html',
    'test_super_admin_simple.html',
    'test_admin_table.php',
    'test_db_connection.php',
    'troubleshoot.php',
    'test_php.php',
    
    // Setup Files (already used)
    'create_admin_table.sql',
    'create_admin_table_safe.php',
    'check_admin_table.php',
    'setup_admin_table.php',
    'create_audit_logs_table.php',
    'setup_super_admin_system.php'
];

$deletedCount = 0;
$errorCount = 0;

foreach ($filesToDelete as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "✓ Deleted: $file\n";
            $deletedCount++;
        } else {
            echo "✗ Failed to delete: $file\n";
            $errorCount++;
        }
    } else {
        echo "- File not found: $file\n";
    }
}

echo "\n=== Cleanup Summary ===\n";
echo "Files deleted: $deletedCount\n";
echo "Errors: $errorCount\n";
echo "Cleanup completed!\n";

echo "\n=== Current Working Files ===\n";
echo "✓ secure_super_admin_login_simple.html - Login page\n";
echo "✓ secure_super_admin_simple.php - Login handler\n";
echo "✓ super_admin_dashboard_simple.html - Dashboard\n";
echo "✓ get_admins_simple.php - Get admins\n";
echo "✓ get_nurses_simple.php - Get nurses\n";
echo "✓ create_admin_simple.php - Create admin\n";
echo "✓ delete_admin_simple.php - Delete admin\n";
echo "✓ super_admin_logout_simple.php - Logout\n";
echo "✓ check_ip.php - IP checker\n";
echo "✓ super_admin_access.log - Access logs\n";
?>
