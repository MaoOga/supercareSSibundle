<?php
/**
 * Admin Session Manager
 * Robust session handling for admin system
 */

class AdminSessionManager {
    private $session_name = 'ADMIN_NEW_SESSION';
    private $session_timeout = 3600; // 60 minutes (increased for testing)
    private $db_connection;
    
    public function __construct() {
        $this->initSession();
        $this->connectDatabase();
    }
    
    /**
     * Initialize session with proper configuration
     */
    private function initSession() {
        // Only initialize if session hasn't been started yet
        if (session_status() === PHP_SESSION_NONE) {
            // Set session parameters before starting
            ini_set('session.gc_maxlifetime', $this->session_timeout);
            ini_set('session.cookie_lifetime', 0);
            ini_set('session.use_strict_mode', 1);
            ini_set('session.use_cookies', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_samesite', 'Lax');
            
            // Set session name
            session_name($this->session_name);
            
            // Set cookie parameters
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => false, // Set to true if using HTTPS
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            
            // Start session
            session_start();
            
            // Initialize regeneration time
            if (!isset($_SESSION['last_regeneration'])) {
                $_SESSION['last_regeneration'] = time();
            }
        } else {
            // Session already started, check if we need to regenerate ID
            if (isset($_SESSION['last_regeneration']) && (time() - $_SESSION['last_regeneration'] > 300)) { // 5 minutes
                session_regenerate_id(true);
                $_SESSION['last_regeneration'] = time();
            }
        }
    }
    
    /**
     * Connect to database
     */
    private function connectDatabase() {
        try {
            $this->db_connection = new PDO(
                "mysql:host=localhost;dbname=supercare_ssi;charset=utf8",
                "root",
                "",
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            $this->db_connection = null;
        }
    }
    
    /**
     * Create admin session after successful login
     */
    public function createSession($admin_data) {
        error_log("Creating admin session for user: " . $admin_data['admin_username']);
        
        // Clear any existing session data
        $_SESSION = array();
        
        // Set session data
        $_SESSION['admin_id'] = $admin_data['id'];
        $_SESSION['admin_username'] = $admin_data['admin_username'];
        $_SESSION['admin_name'] = $admin_data['name'];
        $_SESSION['admin_email'] = $admin_data['email'];
        $_SESSION['user_type'] = 'admin';
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['expires_at'] = time() + $this->session_timeout;
        $_SESSION['session_id'] = session_id();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        error_log("Session created successfully. Session data: " . print_r($_SESSION, true));
        
        // Log session creation in database
        $this->logSessionCreation($admin_data['id']);
        
        return true;
    }
    
    /**
     * Validate current session
     */
    public function validateSession() {
        error_log("Admin session validation started");
        
        // Check if session exists
        if (!isset($_SESSION['admin_id']) || !isset($_SESSION['user_type'])) {
            error_log("Admin session validation failed: Missing session data. Session contents: " . print_r($_SESSION, true));
            return false;
        }
        
        // Check if user type is admin
        if ($_SESSION['user_type'] !== 'admin') {
            error_log("Admin session validation failed: Invalid user type - " . $_SESSION['user_type']);
            return false;
        }
        
        // Check session timeout
        if (isset($_SESSION['expires_at']) && time() > $_SESSION['expires_at']) {
            error_log("Admin session validation failed: Session expired. Current time: " . time() . ", Expires at: " . $_SESSION['expires_at']);
            $this->destroySession();
            return false;
        }
        
        // Check if admin still exists and is active (only if database is available)
        if ($this->db_connection && !$this->validateAdminExists($_SESSION['admin_id'])) {
            error_log("Admin session validation failed: Admin not found in database");
            $this->destroySession();
            return false;
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        $_SESSION['expires_at'] = time() + $this->session_timeout;
        
        error_log("Admin session validation successful");
        return true;
    }
    
    /**
     * Get admin user info from session
     */
    public function getAdminInfo() {
        if (!$this->validateSession()) {
            return null;
        }
        
        return [
            'admin_id' => $_SESSION['admin_id'],
            'admin_username' => $_SESSION['admin_username'],
            'admin_name' => $_SESSION['admin_name'],
            'admin_email' => $_SESSION['admin_email'],
            'user_type' => $_SESSION['user_type'],
            'login_time' => $_SESSION['login_time'],
            'last_activity' => $_SESSION['last_activity']
        ];
    }
    
    /**
     * Destroy session
     */
    public function destroySession() {
        // Log session destruction
        if (isset($_SESSION['admin_id'])) {
            $this->logSessionDestruction($_SESSION['admin_id']);
        }
        
        // Clear session data
        $_SESSION = array();
        
        // Destroy session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        
        // Delete session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
    }
    
    /**
     * Validate admin exists in database
     */
    private function validateAdminExists($admin_id) {
        if (!$this->db_connection) {
            return false;
        }
        
        try {
            $stmt = $this->db_connection->prepare("
                SELECT id, status FROM admin_users 
                WHERE id = ? AND status = 'active'
            ");
            $stmt->execute([$admin_id]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("Error validating admin: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log session creation
     */
    private function logSessionCreation($admin_id) {
        if (!$this->db_connection) {
            return;
        }
        
        try {
            $stmt = $this->db_connection->prepare("
                INSERT INTO admin_login_logs (admin_id, email, status, message, ip_address, user_agent, created_at)
                VALUES (?, ?, 'session_created', 'Admin session created successfully', ?, ?, NOW())
            ");
            $stmt->execute([
                $admin_id,
                $_SESSION['admin_email'],
                $_SESSION['ip_address'],
                $_SESSION['user_agent']
            ]);
        } catch (PDOException $e) {
            error_log("Error logging session creation: " . $e->getMessage());
        }
    }
    
    /**
     * Log session destruction
     */
    private function logSessionDestruction($admin_id) {
        if (!$this->db_connection) {
            return;
        }
        
        try {
            $stmt = $this->db_connection->prepare("
                INSERT INTO admin_login_logs (admin_id, email, status, message, ip_address, user_agent, created_at)
                VALUES (?, ?, 'session_destroyed', 'Admin session destroyed', ?, ?, NOW())
            ");
            $stmt->execute([
                $admin_id,
                $_SESSION['admin_email'],
                $_SESSION['ip_address'],
                $_SESSION['user_agent']
            ]);
        } catch (PDOException $e) {
            error_log("Error logging session destruction: " . $e->getMessage());
        }
    }
    
    /**
     * Clean up old sessions
     */
    public function cleanupOldSessions() {
        if (!$this->db_connection) {
            return;
        }
        
        try {
            // Delete old session logs (older than 30 days)
            $stmt = $this->db_connection->prepare("
                DELETE FROM admin_login_logs 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
            ");
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error cleaning up old sessions: " . $e->getMessage());
        }
    }
}

// Global session manager instance
$adminSession = new AdminSessionManager();
?>
