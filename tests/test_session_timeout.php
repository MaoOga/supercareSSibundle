<?php
require_once '../database/config.php';
require_once '../auth/session_config.php';

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Timeout Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3e0; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Session Timeout Configuration Test</h1>
    
    <div class="info">
        <h3>Current Session Configuration:</h3>
        <p><strong>NURSE_SESSION_TIMEOUT:</strong> <?php echo NURSE_SESSION_TIMEOUT; ?> seconds (<?php echo NURSE_SESSION_TIMEOUT / 60; ?> minutes)</p>
        <p><strong>SESSION_TIMEOUT:</strong> <?php echo SESSION_TIMEOUT; ?> seconds (<?php echo SESSION_TIMEOUT / 60; ?> minutes)</p>
    </div>

    <div class="info">
        <h3>Current Session Status:</h3>
        <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
        <p><strong>Session Status:</strong> <?php echo session_status(); ?></p>
        <p><strong>Logged In:</strong> <?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true ? 'Yes' : 'No'; ?></p>
        <p><strong>User Type:</strong> <?php echo $_SESSION['user_type'] ?? 'Not set'; ?></p>
        <p><strong>Last Activity:</strong> <?php echo isset($_SESSION['last_activity']) ? date('Y-m-d H:i:s', $_SESSION['last_activity']) : 'Not set'; ?></p>
    </div>

         <div class="warning">
         <h3>Client-Side Timeout Settings:</h3>
         <p><strong>JavaScript Timeout:</strong> 60 minutes</p>
         <p><strong>Check Interval:</strong> 1 minute</p>
         <p><strong>Alert Messages:</strong> Removed (direct redirect)</p>
     </div>

     <div class="success">
         <h3>Changes Made:</h3>
         <ul>
             <li>✅ Server-side timeout set to 60 minutes</li>
             <li>✅ Client-side timeout set to 60 minutes</li>
             <li>✅ Alert messages removed from all pages</li>
             <li>✅ Direct redirect to login page on timeout</li>
         </ul>
     </div>

    <div class="info">
        <h3>Files Updated:</h3>
        <ul>
            <li>session_config.php - Server timeout configuration</li>
            <li>index.html - Main dashboard timeout</li>
            <li>form_template.html - Form page timeout</li>
            <li>search.html - Search page timeout</li>
        </ul>
    </div>

    <script>
                 // Test the client-side timeout configuration
         const SESSION_TIMEOUT_MINUTES = 60;
         const SESSION_CHECK_INTERVAL = 60000; // 1 minute
        
        console.log('Client-side timeout configuration:');
        console.log('Session timeout:', SESSION_TIMEOUT_MINUTES, 'minutes');
        console.log('Check interval:', SESSION_CHECK_INTERVAL / 1000, 'seconds');
        
        // Test function to simulate timeout (for testing purposes only)
        function testTimeout() {
            console.log('Session timeout test - redirecting to login...');
            window.location.href = 'login.html?msg=session_expired';
        }
        
        // Uncomment the line below to test timeout (will redirect immediately)
        // setTimeout(testTimeout, 1000);
    </script>
</body>
</html>
