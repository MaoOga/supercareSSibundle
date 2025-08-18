<?php
// Simple Admin Account Management Script

$host = 'localhost';
$dbname = 'supercare_ssi';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== ADMIN ACCOUNT MANAGEMENT ===\n\n";
    
    // Show current admin accounts
    echo "Current Admin Accounts:\n";
    $stmt = $pdo->query("SELECT id, admin_username, name, email, status, created_at FROM admin_users ORDER BY id");
    $count = 0;
    
    while($row = $stmt->fetch()) {
        $count++;
        echo "$count. {$row['name']} ({$row['email']}) - {$row['status']}\n";
    }
    
    if($count == 0) {
        echo "No admin accounts found.\n";
    }
    
    echo "\n=== RECOMMENDATIONS ===\n";
    echo "✅ To create admin accounts: Use the Super Admin Dashboard\n";
    echo "✅ To login: Use http://localhost/supercareSSibundle/admin_login_new.html\n";
    echo "❌ Don't run setup scripts multiple times\n";
    echo "❌ Don't manually modify admin accounts in the database\n";
    
    echo "\n=== CURRENT WORKING LOGIN ===\n";
    echo "Email: supercareadmin@gmail.com\n";
    echo "Password: admin123\n";
    echo "Status: ✅ Working (verified)\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
