<?php
/**
 * Temporarily Disable Rate Limiting
 * This script modifies admin_login.php to bypass rate limiting
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Rate Limiting Control</h2>";

// Check if admin_login.php exists
if (!file_exists('admin_login.php')) {
    echo "<p style='color: red;'>❌ admin_login.php file not found!</p>";
    exit;
}

// Read the current admin_login.php file
$content = file_get_contents('admin_login.php');

if ($content === false) {
    echo "<p style='color: red;'>❌ Could not read admin_login.php file!</p>";
    exit;
}

// Check current status
$isRateLimited = strpos($content, '// Check rate limiting') !== false && 
                 strpos($content, 'if ($rateLimiter->isRateLimited($clientIP))') !== false;

echo "<p>Current rate limiting status: <strong>" . ($isRateLimited ? "ENABLED" : "DISABLED") . "</strong></p>";

// Handle actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action === 'disable') {
        // Comment out rate limiting code
        $content = str_replace(
            '    // Check rate limiting',
            '    // Check rate limiting (TEMPORARILY DISABLED)',
            $content
        );
        $content = str_replace(
            '    if ($rateLimiter->isRateLimited($clientIP)) {',
            '    // if ($rateLimiter->isRateLimited($clientIP)) {',
            $content
        );
        $content = str_replace(
            '        throw new Exception(\'Too many failed login attempts. Please try again later.\');',
            '        // throw new Exception(\'Too many failed login attempts. Please try again later.\');',
            $content
        );
        $content = str_replace(
            '    }',
            '    // }',
            $content
        );
        
        // Write back to file
        if (file_put_contents('admin_login.php', $content)) {
            echo "<p style='color: green;'>✅ Rate limiting has been DISABLED.</p>";
            echo "<p>You can now try logging in without restrictions.</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to write to admin_login.php file!</p>";
        }
        
    } elseif ($action === 'enable') {
        // Uncomment rate limiting code
        $content = str_replace(
            '    // Check rate limiting (TEMPORARILY DISABLED)',
            '    // Check rate limiting',
            $content
        );
        $content = str_replace(
            '    // if ($rateLimiter->isRateLimited($clientIP)) {',
            '    if ($rateLimiter->isRateLimited($clientIP)) {',
            $content
        );
        $content = str_replace(
            '        // throw new Exception(\'Too many failed login attempts. Please try again later.\');',
            '        throw new Exception(\'Too many failed login attempts. Please try again later.\');',
            $content
        );
        $content = str_replace(
            '    // }',
            '    }',
            $content
        );
        
        // Write back to file
        if (file_put_contents('admin_login.php', $content)) {
            echo "<p style='color: green;'>✅ Rate limiting has been ENABLED.</p>";
            echo "<p>Rate limiting is now active again.</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to write to admin_login.php file!</p>";
        }
    }
}

// Show current status again
$content = file_get_contents('admin_login.php');
$isRateLimited = strpos($content, '// Check rate limiting') !== false && 
                 strpos($content, 'if ($rateLimiter->isRateLimited($clientIP))') !== false;

echo "<p>Current rate limiting status: <strong>" . ($isRateLimited ? "ENABLED" : "DISABLED") . "</strong></p>";

// Show action buttons
echo "<h3>Actions:</h3>";
if ($isRateLimited) {
    echo "<a href='?action=disable' style='background: #dc2626; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Disable Rate Limiting</a>";
} else {
    echo "<a href='?action=enable' style='background: #059669; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Enable Rate Limiting</a>";
}

echo "<a href='clear_failed_logins.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Clear Failed Logins</a>";

echo "<h3>Rate Limiting Information:</h3>";
echo "<ul>";
echo "<li><strong>Max Attempts:</strong> 5 failed attempts</li>";
echo "<li><strong>Time Window:</strong> 5 minutes (300 seconds)</li>";
echo "<li><strong>Block Duration:</strong> Until failed attempts expire</li>";
echo "</ul>";

echo "<h3>Recommendations:</h3>";
echo "<ol>";
echo "<li>First try <strong>Clear Failed Logins</strong> to remove your blocked attempts</li>";
echo "<li>If that doesn't work, temporarily <strong>Disable Rate Limiting</strong></li>";
echo "<li>After successful login, <strong>Enable Rate Limiting</strong> again for security</li>";
echo "</ol>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3 { color: #333; }
p { margin: 10px 0; }
ul, ol { margin: 10px 0; padding-left: 20px; }
li { margin: 5px 0; }
</style>
