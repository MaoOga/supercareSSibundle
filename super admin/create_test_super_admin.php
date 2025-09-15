<?php
// Create Test Super Admin Account
echo "<h2>🔧 Create Test Super Admin Account</h2>";

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
    $password = $_POST['password'] ?? '';
    $name = $_POST['name'] ?? '';
    
    if (!empty($email) && !empty($password) && !empty($name)) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            // Check if user already exists
            $stmt = $pdo->prepare("SELECT id FROM super_admin_users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                // Update existing user
                $stmt = $pdo->prepare("UPDATE super_admin_users SET password = ?, name = ?, status = 'active' WHERE email = ?");
                $stmt->execute([$hashedPassword, $name, $email]);
                echo "<p style='color: blue;'>🔄 Updated existing super admin account</p>";
            } else {
                // Insert new user
                $stmt = $pdo->prepare("INSERT INTO super_admin_users (email, password, name, status) VALUES (?, ?, ?, 'active')");
                $stmt->execute([$email, $hashedPassword, $name]);
                echo "<p style='color: green;'>✅ Created new super admin account</p>";
            }
            
            echo "<div style='background: #e8f5e8; border: 1px solid #4caf50; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>🎉 Account Created Successfully!</h3>";
            echo "<p><strong>Email:</strong> $email</p>";
            echo "<p><strong>Password:</strong> $password</p>";
            echo "<p><strong>Name:</strong> $name</p>";
            echo "<p style='color: red;'><strong>⚠️ Save this password - it won't be shown again!</strong></p>";
            echo "</div>";
            
            echo "<div style='background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>🚀 Next Steps:</h3>";
            echo "<p><a href='../auth/super_admin_login.html' style='color: #007bff; font-weight: bold;'>→ Test Login Now</a></p>";
            echo "<p><a href='../debug/fix_super_admin_tables.php' style='color: #007bff;'>→ Run Full System Test</a></p>";
            echo "</div>";
            
        } catch (PDOException $e) {
            echo "<p style='color: red;'>❌ Error creating account: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Please fill all fields</p>";
    }
}

// Show current super admin users
echo "<h3>Current Super Admin Users:</h3>";
try {
    $stmt = $pdo->query("SELECT id, email, name, status, created_at FROM super_admin_users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr style='background: #f8f9fa;'><th>ID</th><th>Email</th><th>Name</th><th>Status</th><th>Created</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['name']}</td>";
            echo "<td style='color: " . ($user['status'] === 'active' ? 'green' : 'red') . ";'>{$user['status']}</td>";
            echo "<td>{$user['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ No super admin users found</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error fetching users: " . $e->getMessage() . "</p>";
}

// Create account form
echo "<h3>Create New Super Admin Account:</h3>";
echo "<form method='POST' style='background: #f8f9fa; padding: 20px; border-radius: 5px;'>";
echo "<p><label>Email: <input type='email' name='email' placeholder='admin@example.com' required style='width: 100%; padding: 8px; margin: 5px 0;'></label></p>";
echo "<p><label>Password: <input type='password' name='password' placeholder='Enter password' required style='width: 100%; padding: 8px; margin: 5px 0;'></label></p>";
echo "<p><label>Name: <input type='text' name='name' placeholder='Super Admin Name' required style='width: 100%; padding: 8px; margin: 5px 0;'></label></p>";
echo "<button type='submit' style='background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Create Account</button>";
echo "</form>";

echo "<h3>Quick Links:</h3>";
echo "<p><a href='../auth/super_admin_login.html' style='color: #007bff;'>→ Test Super Admin Login</a></p>";
echo "<p><a href='../debug/fix_super_admin_tables.php' style='color: #007bff;'>→ Run System Test</a></p>";
echo "<p><a href='generate_super_admin_password.php' style='color: #007bff;'>→ Generate Password Hash</a></p>";
?>
