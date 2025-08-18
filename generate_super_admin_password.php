<?php
// Tool to generate password hash for super admin
echo "<h2>Super Admin Password Hash Generator</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';
    $name = $_POST['name'] ?? '';
    
    if (!empty($password) && !empty($email) && !empty($name)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        echo "<div style='background: #e8f5e8; border: 1px solid #4caf50; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h3>✅ Generated SQL Query:</h3>";
        echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 3px; overflow-x: auto;'>";
        echo "-- Insert Super Admin User\n";
        echo "INSERT INTO `super_admin_users` (`email`, `password`, `name`, `status`) VALUES \n";
        echo "('$email', '$hashedPassword', '$name', 'active');\n\n";
        echo "-- Or update existing user\n";
        echo "UPDATE `super_admin_users` SET \n";
        echo "    `password` = '$hashedPassword',\n";
        echo "    `name` = '$name',\n";
        echo "    `status` = 'active'\n";
        echo "WHERE `email` = '$email';";
        echo "</pre>";
        
        echo "<h3>📧 Login Credentials:</h3>";
        echo "<p><strong>Email:</strong> $email</p>";
        echo "<p><strong>Password:</strong> $password</p>";
        echo "<p><strong>Name:</strong> $name</p>";
        
        echo "<h3>🔐 Security Note:</h3>";
        echo "<p>• Save the password securely - it won't be shown again</p>";
        echo "<p>• Run the SQL query in your database</p>";
        echo "<p>• Test the login after adding to database</p>";
        echo "</div>";
    } else {
        echo "<p style='color: red;'>❌ Please fill all fields</p>";
    }
}

echo "<form method='POST'>";
echo "<h3>Generate Password Hash:</h3>";
echo "<p><label>Email: <input type='email' name='email' placeholder='admin@example.com' required></label></p>";
echo "<p><label>Password: <input type='password' name='password' placeholder='Enter password' required></label></p>";
echo "<p><label>Name: <input type='text' name='name' placeholder='Super Admin Name' required></label></p>";
echo "<button type='submit'>Generate Hash & SQL</button>";
echo "</form>";

echo "<h3>Instructions:</h3>";
echo "<ol>";
echo "<li>Enter the email and password you want to use</li>";
echo "<li>Click 'Generate Hash & SQL'</li>";
echo "<li>Copy the SQL query and run it in your database</li>";
echo "<li>Share the email and password with your customer</li>";
echo "<li>Test the login system</li>";
echo "</ol>";

echo "<h3>Quick Links:</h3>";
echo "<p><a href='secure_super_admin_login_simple.html'>Test Super Admin Login</a></p>";
echo "<p><a href='create_super_admin_table.sql'>View Database Schema</a></p>";
?>
