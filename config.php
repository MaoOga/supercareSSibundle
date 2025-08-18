<?php
// Database configuration
$host = 'localhost';
$dbname = 'supercare_ssi';
$username = 'root';  // Default XAMPP username
$password = '';      // Default XAMPP password (empty)

// Global PDO connection variable
$pdo = null;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Log the error but don't throw exception immediately
    error_log("Database connection failed: " . $e->getMessage());
    $pdo = null;
}
?>
