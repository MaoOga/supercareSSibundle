<?php
/**
 * Clear Failed Login Attempts
 * This script clears failed login attempts from the admin_login_logs table
 * to allow login after being rate limited
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$config = [
    'host' => 'localhost',
    'dbname' => 'supercare_ssi',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

try {
    // Connect to database
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    // Get client IP
    $clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    // Check if admin_login_logs table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'admin_login_logs'");
    if ($stmt->rowCount() === 0) {
        echo "<h2>Table 'admin_login_logs' does not exist</h2>";
        echo "<p>This means rate limiting is not active. You should be able to login normally.</p>";
        exit;
    }
    
    // Count failed attempts for this IP
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as failed_attempts 
        FROM admin_login_logs 
        WHERE ip_address = ? AND status = 'failed'
    ");
    $stmt->execute([$clientIP]);
    $result = $stmt->fetch();
    $failedAttempts = $result['failed_attempts'];
    
    echo "<h2>Failed Login Attempts for IP: {$clientIP}</h2>";
    echo "<p>Current failed attempts: <strong>{$failedAttempts}</strong></p>";
    
    if ($failedAttempts > 0) {
        // Clear failed attempts for this IP
        $stmt = $pdo->prepare("
            DELETE FROM admin_login_logs 
            WHERE ip_address = ? AND status = 'failed'
        ");
        $stmt->execute([$clientIP]);
        $deletedRows = $stmt->rowCount();
        
        echo "<p style='color: green;'>✅ Successfully cleared <strong>{$deletedRows}</strong> failed login attempts.</p>";
        echo "<p>You can now try logging in again.</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ No failed login attempts found for your IP address.</p>";
    }
    
    // Show recent login attempts (last 10)
    echo "<h3>Recent Login Attempts (Last 10):</h3>";
    $stmt = $pdo->prepare("
        SELECT email, status, message, ip_address, created_at 
        FROM admin_login_logs 
        WHERE ip_address = ? 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $stmt->execute([$clientIP]);
    $recentAttempts = $stmt->fetchAll();
    
    if (empty($recentAttempts)) {
        echo "<p>No recent login attempts found.</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>Email</th><th>Status</th><th>Message</th><th>Time</th>";
        echo "</tr>";
        
        foreach ($recentAttempts as $attempt) {
            $statusColor = $attempt['status'] === 'success' ? 'green' : 'red';
            echo "<tr>";
            echo "<td>{$attempt['email']}</td>";
            echo "<td style='color: {$statusColor};'>{$attempt['status']}</td>";
            echo "<td>{$attempt['message']}</td>";
            echo "<td>{$attempt['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Clear all failed attempts (optional)
    echo "<h3>Clear All Failed Attempts (All IPs)</h3>";
    echo "<p>If you want to clear ALL failed login attempts from ALL IP addresses:</p>";
    echo "<a href='?clear_all=1' style='background: #dc2626; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Clear All Failed Attempts</a>";
    
    if (isset($_GET['clear_all']) && $_GET['clear_all'] === '1') {
        $stmt = $pdo->prepare("DELETE FROM admin_login_logs WHERE status = 'failed'");
        $stmt->execute();
        $allDeleted = $stmt->rowCount();
        
        echo "<p style='color: green; margin-top: 10px;'>✅ Cleared <strong>{$allDeleted}</strong> failed login attempts from all IP addresses.</p>";
    }
    
} catch (Exception $e) {
    echo "<h2>Error</h2>";
    echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2 { color: #333; }
p { margin: 10px 0; }
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
</style>
