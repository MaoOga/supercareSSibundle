<?php
// Cleanup old session files
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Session Cleanup</h2>";

$session_path = ini_get('session.save_path');
if (empty($session_path)) {
    $session_path = sys_get_temp_dir();
}

echo "Session path: $session_path<br>";

// Get all session files
$session_files = glob($session_path . '/sess_*');
echo "Total session files found: " . count($session_files) . "<br>";

if (count($session_files) > 0) {
    $deleted = 0;
    foreach ($session_files as $file) {
        if (unlink($file)) {
            $deleted++;
        }
    }
    echo "Deleted $deleted session files<br>";
} else {
    echo "No session files to delete<br>";
}

echo "<br><a href='admin_login_new.html'>Try Admin Login Now</a><br>";
echo "<a href='test_simple_session.php'>Test Session Again</a><br>";
?>
