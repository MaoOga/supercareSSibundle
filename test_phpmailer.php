<?php
// Test PHPMailer Installation
echo "<h2>PHPMailer Installation Test</h2>";

// Test 1: Check if PHPMailer files exist
echo "<h3>1. Checking PHPMailer Files</h3>";
$files = [
    'phpmailer/PHPMailer.php',
    'phpmailer/SMTP.php',
    'phpmailer/Exception.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ {$file} - Found</p>";
    } else {
        echo "<p style='color: red;'>✗ {$file} - Missing</p>";
    }
}

// Test 2: Try to include PHPMailer files
echo "<h3>2. Testing PHPMailer Inclusion</h3>";
try {
    require_once 'phpmailer/PHPMailer.php';
    require_once 'phpmailer/SMTP.php';
    require_once 'phpmailer/Exception.php';
    
    echo "<p style='color: green;'>✓ PHPMailer files included successfully</p>";
    
    // Test if classes are available
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        echo "<p style='color: green;'>✓ PHPMailer class loaded successfully</p>";
    } else {
        echo "<p style='color: red;'>✗ PHPMailer class not found</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error including PHPMailer: " . $e->getMessage() . "</p>";
}

// Test 3: Try to create a PHPMailer instance
echo "<h3>3. Testing PHPMailer Instance Creation</h3>";
try {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    echo "<p style='color: green;'>✓ PHPMailer instance created successfully</p>";
    
    // Test basic configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    
    echo "<p style='color: green;'>✓ PHPMailer basic configuration successful</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error creating PHPMailer instance: " . $e->getMessage() . "</p>";
}

// Test 4: Check PHP version and extensions
echo "<h3>4. System Requirements Check</h3>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

$extensions = ['openssl', 'mbstring', 'json'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'>✓ {$ext} extension - Loaded</p>";
    } else {
        echo "<p style='color: red;'>✗ {$ext} extension - Missing</p>";
    }
}

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>If all tests pass, your PHPMailer installation is working correctly</li>";
echo "<li>Update your email settings in <code>email_config.php</code></li>";
echo "<li>Test the forgot password functionality</li>";
echo "</ol>";

echo "<p><a href='test_email.php' style='background: #1aaf51; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test Email Configuration</a></p>";
?>
