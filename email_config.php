<?php
// Email Configuration for SSI Bundle System
// Update these settings according to your email provider

// Email Configuration Array (used by OTP system)
$emailConfig = [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => 'ahensananingthemcha@gmail.com',
    'password' => 'irelgxhyraptvexn',
    'from_email' => 'ahensananingthemcha@gmail.com',
    'from_name' => 'SuperCare System'
];

// SMTP Configuration (legacy support)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');

// Email Account Settings
define('EMAIL_USERNAME', 'ahensananingthemcha@gmail.com');
define('EMAIL_PASSWORD', 'irelgxhyraptvexn');
define('EMAIL_FROM', 'ahensananingthemcha@gmail.com');
define('EMAIL_FROM_NAME', 'SuperCare System');

// Debug Settings
define('EMAIL_DEBUG', false);
?>
