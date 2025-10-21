<?php
/**
 * Database Migration Script
 * Adds form_access column to nurses table
 * Run this once to enable form-based nurse access control
 */

require_once 'config.php';

try {
    echo "=== Adding Form Access Column to Nurses Table ===\n\n";
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if column already exists
    $checkColumn = $pdo->query("SHOW COLUMNS FROM nurses LIKE 'form_access'");
    if ($checkColumn->rowCount() > 0) {
        echo "❌ Column 'form_access' already exists in nurses table.\n";
        echo "✅ No migration needed.\n\n";
        exit;
    }
    
    // Add the column
    echo "📝 Adding form_access column...\n";
    $pdo->exec("ALTER TABLE nurses ADD COLUMN form_access VARCHAR(50) DEFAULT 'ssi' AFTER email");
    echo "✅ Column added successfully!\n\n";
    
    // Update existing nurses
    echo "📝 Setting default form access for existing nurses...\n";
    $stmt = $pdo->exec("UPDATE nurses SET form_access = 'ssi' WHERE form_access IS NULL OR form_access = ''");
    echo "✅ Updated existing nurse records.\n\n";
    
    // Display summary
    $countStmt = $pdo->query("SELECT COUNT(*) FROM nurses");
    $totalNurses = $countStmt->fetchColumn();
    
    echo "=== Migration Complete ===\n";
    echo "Total nurses in system: $totalNurses\n";
    echo "All nurses have been set to 'ssi' access by default.\n\n";
    echo "ℹ️  You can now:\n";
    echo "  1. Go to the admin panel\n";
    echo "  2. Edit nurse accounts to change their form access\n";
    echo "  3. Create new nurses with specific form access (SSI, CAUTI, Both, All)\n\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>

