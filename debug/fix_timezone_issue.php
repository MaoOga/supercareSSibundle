<?php
require_once '../database/config.php';

echo "<h2>Timezone Issue Fix</h2>";

try {
    // Check current timezone settings
    echo "<h3>Current Timezone Settings:</h3>";
    echo "<p>PHP Default Timezone: " . date_default_timezone_get() . "</p>";
    echo "<p>Server Time: " . date('Y-m-d H:i:s') . "</p>";
    echo "<p>Server Timezone: " . date('T') . "</p>";
    
    // Check MySQL timezone
    $stmt = $pdo->query("SELECT @@global.time_zone, @@session.time_zone, NOW() as mysql_time");
    $timezoneInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<p>MySQL Global Timezone: " . $timezoneInfo['@@global.time_zone'] . "</p>";
    echo "<p>MySQL Session Timezone: " . $timezoneInfo['@@session.time_zone'] . "</p>";
    echo "<p>MySQL Current Time: " . $timezoneInfo['mysql_time'] . "</p>";
    
    // Show all nurses with reset tokens
    $stmt = $pdo->query("SELECT id, nurse_id, name, email, reset_token, reset_expiry FROM nurses WHERE reset_token IS NOT NULL ORDER BY reset_expiry DESC");
    $nursesWithTokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Current Reset Tokens:</h3>";
    if (count($nursesWithTokens) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nurse ID</th><th>Name</th><th>Email</th><th>Reset Expiry (Database)</th><th>Status</th><th>Action</th></tr>";
        foreach ($nursesWithTokens as $nurse) {
            $currentTime = date('Y-m-d H:i:s');
            $isExpired = $nurse['reset_expiry'] <= $currentTime;
            $status = $isExpired ? "EXPIRED" : "VALID";
            $statusColor = $isExpired ? "red" : "green";
            
            echo "<tr>";
            echo "<td>" . $nurse['id'] . "</td>";
            echo "<td>" . $nurse['nurse_id'] . "</td>";
            echo "<td>" . $nurse['name'] . "</td>";
            echo "<td>" . $nurse['email'] . "</td>";
            echo "<td>" . $nurse['reset_expiry'] . "</td>";
            echo "<td style='color: " . $statusColor . ";'>" . $status . "</td>";
            echo "<td>";
            if ($isExpired) {
                echo "<a href='?extend_token=" . $nurse['id'] . "'>Extend Token</a>";
            } else {
                echo "Valid";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No nurses with reset tokens found.</p>";
    }
    
    // Handle token extension
    if (isset($_GET['extend_token'])) {
        $nurseId = $_GET['extend_token'];
        
        // Get nurse details
        $stmt = $pdo->prepare("SELECT id, nurse_id, name, email FROM nurses WHERE id = ?");
        $stmt->execute([$nurseId]);
        $nurse = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($nurse) {
            echo "<h3>Extending Token for: " . $nurse['name'] . "</h3>";
            
            // Generate new expiry time (1 hour from now)
            $newExpiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Update the expiry time
            $stmt = $pdo->prepare("UPDATE nurses SET reset_expiry = ? WHERE id = ?");
            $stmt->execute([$newExpiry, $nurse['id']]);
            
            echo "<p style='color: green;'>✅ Token expiry extended to: " . $newExpiry . "</p>";
            echo "<p>You can now use the reset link again.</p>";
            
            // Show the reset link
            $stmt = $pdo->prepare("SELECT reset_token FROM nurses WHERE id = ?");
            $stmt->execute([$nurse['id']]);
            $tokenResult = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($tokenResult) {
                $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/reset_password.html?token=" . $tokenResult['reset_token'];
                echo "<p><strong>Reset Link:</strong> <a href='" . $resetLink . "' target='_blank'>" . $resetLink . "</a></p>";
            }
        }
    }
    
    // Set proper timezone for future requests
    echo "<h3>Setting Proper Timezone:</h3>";
    
    // Set timezone to Asia/Kolkata (IST)
    date_default_timezone_set('Asia/Kolkata');
    echo "<p>✅ Timezone set to: " . date_default_timezone_get() . "</p>";
    echo "<p>Current time in IST: " . date('Y-m-d H:i:s') . "</p>";
    
    // Update MySQL session timezone
    $pdo->exec("SET time_zone = '+05:30'");
    echo "<p>✅ MySQL session timezone set to +05:30 (IST)</p>";
    
    // Verify the change
    $stmt = $pdo->query("SELECT NOW() as mysql_time");
    $newTime = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>MySQL time after change: " . $newTime['mysql_time'] . "</p>";
    
    echo "<h3>Recommendations:</h3>";
    echo "<ul>";
    echo "<li>Add timezone configuration to your config.php file</li>";
    echo "<li>Set MySQL timezone to IST (+05:30) permanently</li>";
    echo "<li>Use the extended token link above to reset your password</li>";
    echo "<li>For future requests, the timezone will be properly set</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
