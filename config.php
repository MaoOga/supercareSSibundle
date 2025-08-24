<?php
// Database configuration
$host = 'localhost';
$dbname = 'supercare_ssi';
$username = 'root';  // Default XAMPP username
$password = '';      // Default XAMPP password (empty)

// Set timezone to IST (India Standard Time)
date_default_timezone_set('Asia/Kolkata');

// Global PDO connection variable
$pdo = null;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Set MySQL timezone to IST
    $pdo->exec("SET time_zone = '+05:30'");
} catch(PDOException $e) {
    // Log the error but don't throw exception immediately
    error_log("Database connection failed: " . $e->getMessage());
    $pdo = null;
}
?>
