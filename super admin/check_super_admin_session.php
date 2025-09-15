<?php
header('Content-Type: application/json');

// Session management removed - no authentication required
echo json_encode([
    'success' => true,
    'is_super_admin' => true,
    'user_id' => 'SYSTEM',
    'username' => 'System User',
    'remaining_time' => 3600,
    'session_timeout' => 3600
]);
?>
