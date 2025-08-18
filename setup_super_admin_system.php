<?php
// Setup Super Admin System
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Super Admin System Setup</h2>";
echo "<pre>";

$host = 'localhost';
$dbname = 'supercare_ssi';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to database successfully\n\n";
    
    // Create super admin tables
    echo "Setting up super admin tables...\n";
    
    // Create super_admin_users table
    $sql = "CREATE TABLE IF NOT EXISTS `super_admin_users` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "✅ super_admin_users table created\n";
    
    // Create super_admin_otp_logs table
    $sql = "CREATE TABLE IF NOT EXISTS `super_admin_otp_logs` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "✅ super_admin_otp_logs table created\n";
    
    // Check if super admin user exists
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM super_admin_users");
    $result = $stmt->fetch();
    
    if ($result['count'] == 0) {
        echo "\nCreating default super admin user...\n";
        
        // Create default super admin with password: SuP3rC@r3$up3rAdm1n!
        $defaultPassword = password_hash('SuP3rC@r3$up3rAdm1n!', PASSWORD_DEFAULT);
        $sql = "INSERT INTO super_admin_users (email, password, name, status) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['superadmin@supercare.com', $defaultPassword, 'Super Administrator', 'active']);
        
        echo "✅ Default super admin user created\n";
    } else {
        echo "✅ Super admin users already exist\n";
    }
    
    // Show super admin users
    echo "\nSuper Admin Users:\n";
    $stmt = $pdo->query("SELECT id, email, name, status, created_at FROM super_admin_users");
    $users = $stmt->fetchAll();
    
    foreach ($users as $user) {
        echo "  - " . $user['email'] . " (" . $user['name'] . ") - " . $user['status'] . "\n";
    }
    
    echo "\n✅ Super Admin System Setup Complete!\n\n";
    echo "Super Admin Login Information:\n";
    echo "==============================\n";
    echo "Login URL: http://localhost/supercareSSibundle/secure_super_admin_login_simple.html\n";
    echo "Email: superadmin@supercare.com\n";
    echo "Password: SuP3rC@r3$up3rAdm1n!\n\n";
    echo "Dashboard URL: http://localhost/supercareSSibundle/super_admin_dashboard_simple.html\n";
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>
