<?php
// Automatic Super Admin Tables Setup
echo "<h2>🔧 Super Admin Tables Setup</h2>";

// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=supercare_ssi;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Database connection successful</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// SQL commands to create tables
$sqlCommands = [
    "CREATE TABLE IF NOT EXISTS `super_admin_users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `email` varchar(100) NOT NULL UNIQUE,
        `password` varchar(255) NOT NULL,
        `name` varchar(100) NOT NULL,
        `status` enum('active', 'inactive') DEFAULT 'active',
        `otp_code` varchar(6) DEFAULT NULL,
        `otp_expires_at` timestamp NULL DEFAULT NULL,
        `last_login` timestamp NULL DEFAULT NULL,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    "CREATE TABLE IF NOT EXISTS `super_admin_otp_logs` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `email` varchar(100) NOT NULL,
        `otp_code` varchar(6) NOT NULL,
        `ip_address` varchar(45) NOT NULL,
        `attempt_count` int(11) DEFAULT 1,
        `status` enum('pending', 'used', 'expired', 'failed') DEFAULT 'pending',
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `used_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `email` (`email`),
        KEY `created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
];

// Execute SQL commands
echo "<h3>Creating Tables:</h3>";
foreach ($sqlCommands as $index => $sql) {
    try {
        $pdo->exec($sql);
        $tableName = ($index === 0) ? "super_admin_users" : "super_admin_otp_logs";
        echo "<p style='color: green;'>✅ Created table: $tableName</p>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Error creating table: " . $e->getMessage() . "</p>";
    }
}

// Check if tables exist now
echo "<h3>Verification:</h3>";
$tables = ['super_admin_users', 'super_admin_otp_logs'];

foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✅ Table '$table' exists</p>";
        } else {
            echo "<p style='color: red;'>❌ Table '$table' does not exist</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Error checking table '$table': " . $e->getMessage() . "</p>";
    }
}

echo "<h3>🎯 Next Steps:</h3>";
echo "<div style='background: #e8f5e8; border: 1px solid #4caf50; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<p><strong>1. Generate Password Hash:</strong> <a href='generate_super_admin_password.php' style='color: #007bff;'>Click here</a></p>";
echo "<p><strong>2. Test the System:</strong> <a href='super_admin_login.html' style='color: #007bff;'>Click here</a></p>";
echo "<p><strong>3. Run Full Test:</strong> <a href='test_new_super_admin_system.php' style='color: #007bff;'>Click here</a></p>";
echo "</div>";

echo "<h3>📋 What was created:</h3>";
echo "<ul>";
echo "<li><strong>super_admin_users</strong> - Stores super admin accounts with email/password</li>";
echo "<li><strong>super_admin_otp_logs</strong> - Tracks OTP attempts and usage</li>";
echo "</ul>";

echo "<p style='color: blue;'>💡 <strong>Tip:</strong> You can now use the password generator to create super admin accounts!</p>";
?>
