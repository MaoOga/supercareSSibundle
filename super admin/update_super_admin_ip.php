<?php
// Tool to update super admin IP whitelist
echo "<h2>Super Admin IP Whitelist Update Tool</h2>";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newIP = $_POST['new_ip'] ?? '';
    $action = $_POST['action'] ?? '';
    
    if (!empty($newIP) && filter_var($newIP, FILTER_VALIDATE_IP)) {
        // Read current configuration
        $configFile = 'secure_super_admin_simple.php';
        $content = file_get_contents($configFile);
        
        if ($action === 'add') {
            // Add new IP to whitelist
            $pattern = '/\$ALLOWED_IPS = \[(.*?)\];/s';
            $replacement = '$ALLOWED_IPS = [$1' . "\n    '$newIP',        // Added via update tool\n];";
            $newContent = preg_replace($pattern, $replacement, $content);
            
            if (file_put_contents($configFile, $newContent)) {
                echo "<p style='color: green;'>✅ IP $newIP added to whitelist successfully!</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to update whitelist</p>";
            }
        } elseif ($action === 'replace') {
            // Replace customer's IPv6
            $pattern = '/\'2405:201:ac09:bb08:884b:45a0:7cdf:2893\',  \/\/ Customer\'s IPv6 address/';
            $replacement = "'$newIP',  // Customer's IPv6 address (updated)";
            $newContent = preg_replace($pattern, $replacement, $content);
            
            if (file_put_contents($configFile, $newContent)) {
                echo "<p style='color: green;'>✅ Customer's IP updated to $newIP successfully!</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to update IP</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ Invalid IP address</p>";
    }
}

// Show current whitelist
echo "<h3>Current IP Whitelist:</h3>";
$content = file_get_contents('secure_super_admin_simple.php');
preg_match('/\$ALLOWED_IPS = \[(.*?)\];/s', $content, $matches);
if (isset($matches[1])) {
    echo "<pre>" . htmlspecialchars($matches[1]) . "</pre>";
}

// Show current client IP
echo "<h3>Your Current IP:</h3>";
$clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
echo "<p><strong>Your IP:</strong> $clientIP</p>";

// Form to update IP
echo "<h3>Update IP Whitelist:</h3>";
echo "<form method='POST'>";
echo "<p><label>New IP Address: <input type='text' name='new_ip' placeholder='Enter IP address' required></label></p>";
echo "<p><label>Action: ";
echo "<select name='action'>";
echo "<option value='add'>Add to Whitelist</option>";
echo "<option value='replace'>Replace Customer's IPv6</option>";
echo "</select>";
echo "</label></p>";
echo "<button type='submit'>Update Whitelist</button>";
echo "</form>";

echo "<h3>Instructions:</h3>";
echo "<ol>";
echo "<li>Check your current IP address above</li>";
echo "<li>If the customer's IP has changed, use 'Replace Customer's IPv6'</li>";
echo "<li>If you want to add temporary access, use 'Add to Whitelist'</li>";
echo "<li>After updating, test the super admin login</li>";
echo "</ol>";

echo "<p><a href='../auth/super_admin_login.html'>Test Super Admin Login</a></p>";
?>
