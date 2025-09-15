<?php
require_once '../database/config.php';

header('Content-Type: application/json');

// Session management removed - no authentication required
echo json_encode(['success' => true, 'message' => 'Session activity updated']);
?>
