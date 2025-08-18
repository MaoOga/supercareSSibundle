<?php
// Fix Super Admin Tables
echo "<h2>🔧 Fix Super Admin Tables</h2>";

// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=supercare_ssi;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Database connection successful</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Check current tables
echo "<h3>Current Tables Status:</h3>";
$tables = ['super_admin_users', 'super_admin_otp_logs'];

foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: orange;'>⚠️ Table '$table' exists (may have issues)</p>";
        } else {
            echo "<p style='color: red;'>❌ Table '$table' does not exist</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Error checking table '$table': " . $e->getMessage() . "</p>";
    }
}

// Fix tables
echo "<h3>Fixing Tables:</h3>";

// Drop existing tables if they exist
try {
    $pdo->exec("DROP TABLE IF EXISTS `super_admin_users`");
    echo "<p style='color: blue;'>🗑️ Dropped existing super_admin_users table</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error dropping super_admin_users: " . $e->getMessage() . "</p>";
}

try {
    $pdo->exec("DROP TABLE IF EXISTS `super_admin_otp_logs`");
    echo "<p style='color: blue;'>🗑️ Dropped existing super_admin_otp_logs table</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error dropping super_admin_otp_logs: " . $e->getMessage() . "</p>";
}

// Create tables properly
$sqlCommands = [
    "CREATE TABLE `super_admin_users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `email` varchar(100) NOT NULL,
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
    
    "CREATE TABLE `super_admin_otp_logs` (
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

foreach ($sqlCommands as $index => $sql) {
    try {
        $pdo->exec($sql);
        $tableName = ($index === 0) ? "super_admin_users" : "super_admin_otp_logs";
        echo "<p style='color: green;'>✅ Created table: $tableName</p>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Error creating table: " . $e->getMessage() . "</p>";
    }
}

// Verify tables
echo "<h3>Final Verification:</h3>";
foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✅ Table '$table' exists and is working</p>";
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

echo "<p style='color: green;'>🎉 <strong>Tables are now fixed and ready to use!</strong></p>";
?>
