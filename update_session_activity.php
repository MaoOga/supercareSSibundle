<?php
require_once 'config.php';
require_once 'session_config.php';

header('Content-Type: application/json');

// Update session activity
$result = updateSessionActivity();
echo json_encode($result);
?>
