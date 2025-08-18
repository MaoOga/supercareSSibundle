<?php
// Reset Super Admin Password
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Super Admin Password Reset</h2>";
echo "<pre>";

$host = 'localhost';
$dbname = 'supercare_ssi';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get current super admin user
    $stmt = $pdo->query("SELECT id, email, name FROM super_admin_users LIMIT 1");
    $user = $stmt->fetch();
    
    if ($user) {
        echo "Current super admin user:\n";
        echo "ID: " . $user['id'] . "\n";
        echo "Email: " . $user['email'] . "\n";
        echo "Name: " . $user['name'] . "\n\n";
        
        // Reset password to 'SuperAdmin@2025'
        $newPassword = password_hash('SuperAdmin@2025', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE super_admin_users SET password = ? WHERE id = ?");
        $stmt->execute([$newPassword, $user['id']]);
        
        echo "✅ Super admin password reset successfully!\n\n";
        echo "New login credentials:\n";
        echo "Email: " . $user['email'] . "\n";
        echo "Password: SuperAdmin@2025\n\n";
        echo "Super Admin Login URL: http://localhost/supercareSSibundle/secure_super_admin_login_simple.html\n";
        echo "Dashboard URL: http://localhost/supercareSSibundle/super_admin_dashboard_simple.html\n";
        
    } else {
        echo "No super admin users found.\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>
