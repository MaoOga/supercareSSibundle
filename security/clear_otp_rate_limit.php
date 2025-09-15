<?php
// Clear OTP Rate Limit - For Testing Purposes
echo "<h2>🔧 Clear OTP Rate Limit</h2>";

// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=supercare_ssi;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Database connection successful</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $action = $_POST['action'] ?? '';
    
    if ($action === 'clear_specific' && !empty($email)) {
        // Clear rate limit for specific email
        try {
            $stmt = $pdo->prepare("DELETE FROM super_admin_otp_logs WHERE email = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)");
            $stmt->execute([$email]);
            $deleted = $stmt->rowCount();
            
            echo "<div style='background: #e8f5e8; border: 1px solid #4caf50; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>✅ Rate Limit Cleared!</h3>";
            echo "<p>Deleted $deleted OTP log entries for: <strong>$email</strong></p>";
            echo "<p>You can now request a new OTP.</p>";
            echo "</div>";
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error clearing rate limit: " . $e->getMessage() . "</p>";
        }
    } elseif ($action === 'clear_all') {
        // Clear all recent OTP logs
        try {
            $stmt = $pdo->prepare("DELETE FROM super_admin_otp_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)");
            $stmt->execute();
            $deleted = $stmt->rowCount();
            
            echo "<div style='background: #e8f5e8; border: 1px solid #4caf50; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>✅ All Rate Limits Cleared!</h3>";
            echo "<p>Deleted $deleted OTP log entries for all users.</p>";
            echo "<p>All users can now request new OTPs.</p>";
            echo "</div>";
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error clearing rate limits: " . $e->getMessage() . "</p>";
        }
    }
}

// Show current OTP logs
echo "<h3>Current OTP Logs (Last 10 minutes):</h3>";
try {
    $stmt = $pdo->query("
        SELECT email, otp_code, ip_address, status, created_at 
        FROM super_admin_otp_logs 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)
        ORDER BY created_at DESC
    ");
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($logs) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr style='background: #f8f9fa;'><th>Email</th><th>OTP</th><th>IP</th><th>Status</th><th>Created</th></tr>";
        foreach ($logs as $log) {
            echo "<tr>";
            echo "<td>{$log['email']}</td>";
            echo "<td>{$log['otp_code']}</td>";
            echo "<td>{$log['ip_address']}</td>";
            echo "<td style='color: " . ($log['status'] === 'used' ? 'green' : 'orange') . ";'>{$log['status']}</td>";
            echo "<td>{$log['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ No recent OTP logs found</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error fetching logs: " . $e->getMessage() . "</p>";
}

// Clear rate limit forms
echo "<h3>Clear Rate Limits:</h3>";
echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 10px 0;'>";
echo "<form method='POST' style='margin-bottom: 20px;'>";
echo "<h4>Clear for Specific Email:</h4>";
echo "<p><label>Email: <input type='email' name='email' placeholder='user@example.com' required style='width: 100%; padding: 8px; margin: 5px 0;'></label></p>";
echo "<input type='hidden' name='action' value='clear_specific'>";
echo "<button type='submit' style='background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Clear Rate Limit</button>";
echo "</form>";

echo "<form method='POST'>";
echo "<h4>Clear All Rate Limits:</h4>";
echo "<p style='color: red;'>⚠️ This will clear rate limits for ALL users!</p>";
echo "<input type='hidden' name='action' value='clear_all'>";
echo "<button type='submit' style='background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;' onclick='return confirm(\"Are you sure you want to clear ALL rate limits?\")'>Clear All Rate Limits</button>";
echo "</form>";
echo "</div>";

echo "<h3>Rate Limit Rules:</h3>";
echo "<div style='background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<ul>";
echo "<li><strong>Maximum:</strong> 3 OTP requests per 10 minutes per email</li>";
echo "<li><strong>Window:</strong> 10-minute rolling window</li>";
echo "<li><strong>Reset:</strong> After 10 minutes, the limit resets</li>";
echo "<li><strong>Logging:</strong> All OTP requests are logged with IP address</li>";
echo "</ul>";
echo "</div>";

echo "<h3>Quick Links:</h3>";
echo "<p><a href='../auth/super_admin_login.html' style='color: #007bff;'>→ Super Admin Login</a></p>";
echo "<p><a href='../auth/create_test_super_admin.php' style='color: #007bff;'>→ Create Super Admin Account</a></p>";
?>

