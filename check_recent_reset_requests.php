<?php
require_once 'config.php';

echo "<h2>Recent Reset Token Requests Check</h2>";

try {
    // Show all nurses with reset tokens and their details
    $stmt = $pdo->query("SELECT id, nurse_id, name, email, reset_token, reset_expiry, created_at, updated_at FROM nurses WHERE reset_token IS NOT NULL ORDER BY reset_expiry DESC");
    $nursesWithTokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>All Nurses with Reset Tokens:</h3>";
    if (count($nursesWithTokens) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nurse ID</th><th>Name</th><th>Email</th><th>Reset Token (first 20 chars)</th><th>Reset Expiry</th><th>Created At</th><th>Updated At</th><th>Status</th></tr>";
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
            echo "<td>" . substr($nurse['reset_token'], 0, 20) . "..." . "</td>";
            echo "<td>" . $nurse['reset_expiry'] . "</td>";
            echo "<td>" . $nurse['created_at'] . "</td>";
            echo "<td>" . $nurse['updated_at'] . "</td>";
            echo "<td style='color: " . $statusColor . ";'>" . $status . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No nurses with reset tokens found.</p>";
    }
    
    // Show current time
    echo "<h3>Current Time:</h3>";
    echo "<p>Server time: " . date('Y-m-d H:i:s') . "</p>";
    echo "<p>Timezone: " . date_default_timezone_get() . "</p>";
    
    // Check if there are any recent requests in the last 10 minutes
    $tenMinutesAgo = date('Y-m-d H:i:s', strtotime('-10 minutes'));
    $stmt = $pdo->prepare("SELECT id, nurse_id, name, email, reset_token, reset_expiry, updated_at FROM nurses WHERE updated_at >= ? ORDER BY updated_at DESC");
    $stmt->execute([$tenMinutesAgo]);
    $recentRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Recent Activity (Last 10 minutes):</h3>";
    if (count($recentRequests) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nurse ID</th><th>Name</th><th>Email</th><th>Has Reset Token</th><th>Reset Expiry</th><th>Last Updated</th></tr>";
        foreach ($recentRequests as $nurse) {
            $hasToken = $nurse['reset_token'] ? "YES" : "NO";
            $tokenColor = $nurse['reset_token'] ? "green" : "red";
            
            echo "<tr>";
            echo "<td>" . $nurse['id'] . "</td>";
            echo "<td>" . $nurse['nurse_id'] . "</td>";
            echo "<td>" . $nurse['name'] . "</td>";
            echo "<td>" . $nurse['email'] . "</td>";
            echo "<td style='color: " . $tokenColor . ";'>" . $hasToken . "</td>";
            echo "<td>" . ($nurse['reset_expiry'] ?? 'N/A') . "</td>";
            echo "<td>" . $nurse['updated_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No recent activity found in the last 10 minutes.</p>";
    }
    
    // Check error logs for any issues
    echo "<h3>Possible Issues:</h3>";
    echo "<ul>";
    echo "<li>Check if the email was actually sent (check spam folder)</li>";
    echo "<li>Verify the email address you entered is correct</li>";
    echo "<li>Check if there were any PHP errors during the request</li>";
    echo "<li>The token might have been generated but email delivery failed</li>";
    echo "</ul>";
    
    echo "<h3>Debug Steps:</h3>";
    echo "<p>1. Check your email (including spam folder) for the reset link</p>";
    echo "<p>2. Verify the email address you used in the forgot password form</p>";
    echo "<p>3. Try requesting the reset link again</p>";
    echo "<p>4. Check the browser console for any JavaScript errors</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
