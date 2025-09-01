<?php
// Test file to verify super admin system fixes
echo "<h2>Super Admin System Fix Test</h2>";

// Test 1: Database connection
echo "<h3>1. Database Connection Test:</h3>";
try {
    require_once '../database/config.php';
    $pdo->query('SELECT 1');
    echo "✅ Database connection successful<br>";
} catch(Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    exit;
}

// Test 2: Check admin_users table structure
echo "<h3>2. Admin Users Table Structure:</h3>";
try {
    $stmt = $pdo->query("DESCRIBE admin_users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasStatus = false;
    $hasIsActive = false;
    
    foreach ($columns as $column) {
        echo "Column: {$column['Field']} - Type: {$column['Type']}<br>";
        if ($column['Field'] === 'status') $hasStatus = true;
        if ($column['Field'] === 'is_active') $hasIsActive = true;
    }
    
    if ($hasStatus && !$hasIsActive) {
        echo "✅ Table structure is correct (has 'status', no 'is_active')<br>";
    } elseif (!$hasStatus && $hasIsActive) {
        echo "❌ Table has old structure (has 'is_active', no 'status')<br>";
        echo "Run fix_admin_users_table.sql to fix this<br>";
    } elseif (!$hasStatus && !$hasIsActive) {
        echo "❌ Table missing both columns<br>";
    } else {
        echo "⚠️ Table has both columns (transitional state)<br>";
    }
    
} catch(Exception $e) {
    echo "❌ Error checking table structure: " . $e->getMessage() . "<br>";
}

// Test 3: Check if check_super_admin_session.php exists
echo "<h3>3. File Existence Test:</h3>";
$files = [
    'check_super_admin_session.php',
    'get_admins_simple.php',
    'secure_super_admin_login_simple.html',
    'super_admin_dashboard_simple.html'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ {$file} exists<br>";
    } else {
        echo "❌ {$file} missing<br>";
    }
}

// Test 4: Test admin users query
echo "<h3>4. Admin Users Query Test:</h3>";
try {
    $stmt = $pdo->prepare("SELECT id, admin_username, name, email, created_at, last_login, status FROM admin_users ORDER BY created_at DESC");
    $stmt->execute();
    $admins = $stmt->fetchAll();
    
    echo "✅ Query executed successfully<br>";
    echo "Found " . count($admins) . " admin users<br>";
    
    if (count($admins) > 0) {
        echo "<h4>Admin Users:</h4>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Username</th><th>Name</th><th>Email</th><th>Status</th></tr>";
        foreach ($admins as $admin) {
            echo "<tr>";
            echo "<td>{$admin['id']}</td>";
            echo "<td>{$admin['admin_username']}</td>";
            echo "<td>{$admin['name']}</td>";
            echo "<td>{$admin['email']}</td>";
            echo "<td>{$admin['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch(Exception $e) {
    echo "❌ Query failed: " . $e->getMessage() . "<br>";
}

echo "<h3>5. Access Links:</h3>";
echo "<a href='../auth/super_admin_login.html' target='_blank'>Super Admin Login</a><br>";
echo "<a href='../super admin/super_admin_dashboard_simple.html' target='_blank'>Super Admin Dashboard</a><br>";
echo "<a href='../database/fix_admin_users_table.sql' target='_blank'>Database Fix Script</a><br>";

echo "<h3>6. Next Steps:</h3>";
echo "1. If database structure is incorrect, run fix_admin_users_table.sql<br>";
echo "2. Try accessing the super admin login page<br>";
echo "3. Check browser console for any remaining errors<br>";
?>
