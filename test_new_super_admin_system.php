<?php
// Test script for new Super Admin System with Email/Password + OTP
echo "<h2>New Super Admin System Test</h2>";

// Database connection test
echo "<h3>1. Database Connection Test:</h3>";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=supercare_ssi;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Database connection successful</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Check super_admin_users table
echo "<h3>2. Super Admin Users Table:</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'super_admin_users'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ super_admin_users table exists</p>";
        
        // Check table structure
        $stmt = $pdo->query("DESCRIBE super_admin_users");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h4>Table Structure:</h4>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>{$column['Field']}</td>";
            echo "<td>{$column['Type']}</td>";
            echo "<td>{$column['Null']}</td>";
            echo "<td>{$column['Key']}</td>";
            echo "<td>{$column['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check existing users
        $stmt = $pdo->query("SELECT id, email, name, status, created_at FROM super_admin_users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h4>Existing Super Admin Users:</h4>";
        if (count($users) > 0) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Email</th><th>Name</th><th>Status</th><th>Created</th></tr>";
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>{$user['id']}</td>";
                echo "<td>{$user['email']}</td>";
                echo "<td>{$user['name']}</td>";
                echo "<td>{$user['status']}</td>";
                echo "<td>{$user['created_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>⚠️ No super admin users found</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ super_admin_users table does not exist</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error checking table: " . $e->getMessage() . "</p>";
}

// Check super_admin_otp_logs table
echo "<h3>3. OTP Logs Table:</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'super_admin_otp_logs'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ super_admin_otp_logs table exists</p>";
        
        // Check recent OTP logs
        $stmt = $pdo->query("SELECT email, otp_code, ip_address, status, created_at FROM super_admin_otp_logs ORDER BY created_at DESC LIMIT 5");
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($logs) > 0) {
            echo "<h4>Recent OTP Logs:</h4>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>Email</th><th>OTP</th><th>IP</th><th>Status</th><th>Created</th></tr>";
            foreach ($logs as $log) {
                echo "<tr>";
                echo "<td>{$log['email']}</td>";
                echo "<td>{$log['otp_code']}</td>";
                echo "<td>{$log['ip_address']}</td>";
                echo "<td>{$log['status']}</td>";
                echo "<td>{$log['created_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: gray;'>No OTP logs found</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ super_admin_otp_logs table does not exist</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error checking OTP logs: " . $e->getMessage() . "</p>";
}

// Check email configuration
echo "<h3>4. Email Configuration:</h3>";
if (file_exists('email_config.php')) {
    echo "<p style='color: green;'>✅ email_config.php exists</p>";
    
    // Check if PHPMailer files exist
    $phpmailerFiles = [
        'phpmailer/PHPMailer.php',
        'phpmailer/SMTP.php',
        'phpmailer/Exception.php'
    ];
    
    foreach ($phpmailerFiles as $file) {
        if (file_exists($file)) {
            echo "<p style='color: green;'>✅ $file exists</p>";
        } else {
            echo "<p style='color: red;'>❌ $file missing</p>";
        }
    }
} else {
    echo "<p style='color: red;'>❌ email_config.php missing</p>";
}

// Check new system files
echo "<h3>5. New System Files:</h3>";
$newFiles = [
    'super_admin_login.html' => 'New Super Admin Login Page',
    'send_otp.php' => 'OTP Generation Script',
    'verify_otp.php' => 'OTP Verification Script',
    'generate_super_admin_password.php' => 'Password Hash Generator',
    'create_super_admin_table.sql' => 'Database Schema'
];

foreach ($newFiles as $file => $description) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $file - $description</p>";
    } else {
        echo "<p style='color: red;'>❌ $file - $description (missing)</p>";
    }
}

// Test password hash generation
echo "<h3>6. Password Hash Test:</h3>";
$testPassword = "TestPassword123";
$hashedPassword = password_hash($testPassword, PASSWORD_DEFAULT);
$isValid = password_verify($testPassword, $hashedPassword);

if ($isValid) {
    echo "<p style='color: green;'>✅ Password hashing and verification working correctly</p>";
} else {
    echo "<p style='color: red;'>❌ Password hashing test failed</p>";
}

// Test OTP generation
echo "<h3>7. OTP Generation Test:</h3>";
$testOTP = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
if (strlen($testOTP) === 6 && is_numeric($testOTP)) {
    echo "<p style='color: green;'>✅ OTP generation working correctly (Sample: $testOTP)</p>";
} else {
    echo "<p style='color: red;'>❌ OTP generation test failed</p>";
}

echo "<h3>8. Quick Actions:</h3>";
echo "<div style='margin: 20px 0;'>";
echo "<a href='generate_super_admin_password.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Generate Password Hash</a>";
echo "<a href='super_admin_login.html' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Test New Login</a>";
echo "<a href='create_super_admin_table.sql' style='background: #ffc107; color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Database Schema</a>";
echo "</div>";

echo "<h3>9. Setup Instructions:</h3>";
echo "<ol>";
echo "<li><strong>Create Database Tables:</strong> Run the SQL commands in 'create_super_admin_table.sql'</li>";
echo "<li><strong>Generate Password Hash:</strong> Use 'generate_super_admin_password.php' to create secure passwords</li>";
echo "<li><strong>Configure Email:</strong> Update 'email_config.php' with your SMTP settings</li>";
echo "<li><strong>Add Super Admin Users:</strong> Insert users into the database using the generated SQL</li>";
echo "<li><strong>Test the System:</strong> Try logging in with the new system</li>";
echo "</ol>";

echo "<h3>10. Security Features:</h3>";
echo "<ul>";
echo "<li>✅ Email/Password authentication</li>";
echo "<li>✅ 6-digit OTP verification</li>";
echo "<li>✅ 10-minute OTP expiration</li>";
echo "<li>✅ Secure password hashing</li>";
echo "<li>✅ OTP attempt logging</li>";
echo "<li>✅ Session management</li>";
echo "<li>✅ IP address tracking</li>";
echo "</ul>";

echo "<div style='background: #e8f5e8; border: 1px solid #4caf50; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
echo "<h3>🎯 Next Steps:</h3>";
echo "<p>1. Run the database setup script</p>";
echo "<p>2. Generate password hashes for your super admin users</p>";
echo "<p>3. Configure email settings for OTP delivery</p>";
echo "<p>4. Test the complete login flow</p>";
echo "<p>5. Share credentials with your customer</p>";
echo "</div>";
?>
