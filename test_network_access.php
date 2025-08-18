<?php
echo "<h2>Network Access Test</h2>";

echo "<h3>Server Information:</h3>";
echo "<p><strong>Server IP:</strong> " . $_SERVER['SERVER_ADDR'] . "</p>";
echo "<p><strong>Server Name:</strong> " . $_SERVER['SERVER_NAME'] . "</p>";
echo "<p><strong>HTTP Host:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p><strong>Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";

echo "<h3>Client Information:</h3>";
echo "<p><strong>Client IP:</strong> " . $_SERVER['REMOTE_ADDR'] . "</p>";
echo "<p><strong>User Agent:</strong> " . $_SERVER['HTTP_USER_AGENT'] . "</p>";

echo "<h3>Network Test:</h3>";
echo "<p>If you can see this page, the server is accessible!</p>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";

echo "<h3>Instructions:</h3>";
echo "<ol>";
echo "<li>Find your computer's IP address using 'ipconfig'</li>";
echo "<li>Try accessing: http://[YOUR_COMPUTER_IP]/supercareSSibundle/test_network_access.php</li>";
echo "<li>If this works, you can access the super admin system</li>";
echo "</ol>";

echo "<h3>Quick Links:</h3>";
echo "<p><a href='secure_super_admin_login_simple.html'>Super Admin Login</a></p>";
echo "<p><a href='check_my_ip.php'>IP Detection Test</a></p>";
?>
