<?php
// Simple IP detection test
echo "<h2>IP Detection Test</h2>";

// Get client IP using the same function as super admin
function getClientIP() {
    $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    foreach ($ipKeys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

$detectedIP = getClientIP();

echo "<h3>Detected IP Addresses:</h3>";
echo "<p><strong>System Detected IP:</strong> <code>$detectedIP</code></p>";

echo "<h3>All Server Variables:</h3>";
echo "<pre>";
foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'IP') !== false || strpos($key, 'REMOTE') !== false) {
        echo "$key: $value\n";
    }
}
echo "</pre>";

echo "<h3>IP Validation Test:</h3>";
$isValidIPv4 = filter_var($detectedIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
$isValidIPv6 = filter_var($detectedIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);

echo "<p>Is Valid IPv4: " . ($isValidIPv4 ? "YES" : "NO") . "</p>";
echo "<p>Is Valid IPv6: " . ($isValidIPv6 ? "YES" : "NO") . "</p>";

echo "<h3>Super Admin IP Check:</h3>";
$ALLOWED_IPS = [
    '127.0.0.1',           // Localhost (for testing)
    '::1',                 // IPv6 localhost
    '2405:201:ac09:bb08:884b:45a0:7cdf:2893',  // Customer's IPv6 address
];

$isAllowed = in_array($detectedIP, $ALLOWED_IPS);
echo "<p>Is IP in Whitelist: " . ($isAllowed ? "YES" : "NO") . "</p>";

if ($isAllowed) {
    echo "<p style='color: green;'>✅ Your IP is in the whitelist!</p>";
} else {
    echo "<p style='color: red;'>❌ Your IP is NOT in the whitelist</p>";
    echo "<p>Whitelisted IPs:</p><ul>";
    foreach ($ALLOWED_IPS as $ip) {
        echo "<li><code>$ip</code></li>";
    }
    echo "</ul>";
}
?>
