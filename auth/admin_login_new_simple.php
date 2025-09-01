<?php
/**
 * Admin Login Handler with Proper Session Management
 */

// Set content type to JSON
header('Content-Type: application/json');

// Include session manager
require_once 'admin_session_manager.php';

// Database configuration
function getDBConnection() {
    $configs = [
        ['host' => 'localhost', 'dbname' => 'supercare_ssi', 'username' => 'root', 'password' => ''],
        ['host' => '127.0.0.1', 'dbname' => 'supercare_ssi', 'username' => 'root', 'password' => ''],
        ['host' => 'localhost', 'dbname' => 'supercare_ssi', 'username' => 'root', 'password' => 'root'],
    ];
    
    foreach ($configs as $config) {
        try {
            $pdo = new PDO("mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8", 
                          $config['username'], $config['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        } catch (PDOException $e) {
            error_log("Database connection failed for {$config['host']}: " . $e->getMessage());
            continue;
        }
    }
    
    return false;
}

// Validate admin credentials
function validateAdmin($email, $password) {
    $pdo = getDBConnection();
    if (!$pdo) {
        error_log("Failed to get database connection in validateAdmin");
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT id, admin_username, name, email, password, status 
            FROM admin_users 
            WHERE email = ? AND status = 'active'
        ");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if (!$admin) {
            error_log("Admin not found or not active: $email");
            return false;
        }
        
        if (!password_verify($password, $admin['password'])) {
            error_log("Password verification failed for: $email");
            return false;
        }
        
        error_log("Admin validation successful for: $email");
        return $admin;
        
    } catch (PDOException $e) {
        error_log("Database error in validateAdmin: " . $e->getMessage());
        return false;
    }
}

// Log login attempts
function logLoginAttempt($email, $status, $message) {
    $pdo = getDBConnection();
    if (!$pdo) return;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO admin_login_logs (email, status, message, ip_address, user_agent, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $email,
            $status,
            $message,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    } catch (PDOException $e) {
        error_log("Failed to log login attempt: " . $e->getMessage());
    }
}

// Handle the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    error_log("Login attempt for email: $email");
    
    // Validate input
    if (empty($email) || empty($password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Email and password are required.'
        ]);
        exit;
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please enter a valid email address.'
        ]);
        exit;
    }
    
    // Attempt to validate admin credentials
    $admin = validateAdmin($email, $password);
    
    if ($admin) {
        // Log successful login
        logLoginAttempt($email, 'success', 'Login successful');
        
        // Create proper session
        if ($adminSession->createSession($admin)) {
            // Update last login time
            $pdo = getDBConnection();
            if ($pdo) {
                try {
                    $stmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                    $stmt->execute([$admin['id']]);
                } catch (PDOException $e) {
                    error_log("Failed to update last login: " . $e->getMessage());
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Login successful!',
                'admin_name' => $admin['name'],
                'session_id' => session_id()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create session. Please try again.'
            ]);
        }
        
    } else {
        // Log failed login
        logLoginAttempt($email, 'failed', 'Invalid credentials');
        
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email or password. Please check your credentials and try again.'
        ]);
    }
    
} else {
    // Invalid request method
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>
