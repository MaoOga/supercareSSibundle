<?php
header('Content-Type: application/json');
require_once '../database/config.php';

try {
    // Check if it's a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get and validate token
    $token = trim($_POST['token'] ?? '');
    
    if (empty($token)) {
        throw new Exception('Reset token is required');
    }

    // Get and validate new password
    $newPassword = trim($_POST['newPassword'] ?? '');
    
    if (empty($newPassword)) {
        throw new Exception('New password is required');
    }

    // Validate password strength
    if (strlen($newPassword) < 8) {
        throw new Exception('Password must be at least 8 characters long');
    }

    if (!preg_match('/[A-Z]/', $newPassword)) {
        throw new Exception('Password must contain at least one uppercase letter');
    }

    if (!preg_match('/[a-z]/', $newPassword)) {
        throw new Exception('Password must contain at least one lowercase letter');
    }

    if (!preg_match('/\d/', $newPassword)) {
        throw new Exception('Password must contain at least one number');
    }

    // Check if token exists and is not expired
    $stmt = $pdo->prepare("SELECT id, nurse_id, name, form_access FROM nurses WHERE reset_token = ? AND reset_expiry > NOW()");
    $stmt->execute([$token]);
    $nurse = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$nurse) {
        throw new Exception('Invalid or expired reset token. Please request a new password reset link.');
    }

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update the password and clear the reset token
    $stmt = $pdo->prepare("UPDATE nurses SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
    $stmt->execute([$hashedPassword, $nurse['id']]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Failed to update password. Please try again.');
    }

    // Determine redirect URL based on form access
    $formAccess = $nurse['form_access'] ?? 'ssi';
    $redirectUrl = ($formAccess === 'cauti') ? '../Cauti_form/cauti_login.html' : 'login.html';
    
    echo json_encode([
        'success' => true,
        'message' => 'Password has been reset successfully!',
        'redirect_url' => $redirectUrl
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
