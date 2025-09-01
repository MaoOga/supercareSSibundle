<?php
// Reset Token Debug Script for SSI Bundle System
// This script helps diagnose password reset token issues

require_once '../database/config.php';

echo "<h2>Password Reset Token Debug</h2>";

// Get token from URL if provided
$token = $_GET['token'] ?? '';

if ($token) {
    echo "<h3>Analyzing Token: " . substr($token, 0, 16) . "...</h3>";
    
    try {
        // Check if token exists and is not expired
        $stmt = $pdo->prepare("SELECT id, nurse_id, name, email, reset_token, reset_expiry FROM nurses WHERE reset_token = ?");
        $stmt->execute([$token]);
        $nurse = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($nurse) {
            echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
            echo "<h4>✓ Token Found in Database</h4>";
            echo "<p><strong>Nurse ID:</strong> " . htmlspecialchars($nurse['nurse_id']) . "</p>";
            echo "<p><strong>Name:</strong> " . htmlspecialchars($nurse['name']) . "</p>";
            echo "<p><strong>Email:</strong> " . htmlspecialchars($nurse['email']) . "</p>";
            echo "<p><strong>Reset Expiry:</strong> " . $nurse['reset_expiry'] . "</p>";
            
            // Check if token is expired
            $now = new DateTime();
            $expiry = new DateTime($nurse['reset_expiry']);
            
            if ($now > $expiry) {
                echo "<p style='color: red;'><strong>✗ Token is EXPIRED</strong></p>";
                echo "<p>Token expired at: " . $nurse['reset_expiry'] . "</p>";
                echo "<p>Current time: " . $now->format('Y-m-d H:i:s') . "</p>";
            } else {
                echo "<p style='color: green;'><strong>✓ Token is VALID</strong></p>";
                $timeLeft = $expiry->diff($now);
                echo "<p>Token expires in: " . $timeLeft->format('%h hours %i minutes') . "</p>";
            }
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
            echo "<h4>✗ Token Not Found</h4>";
            echo "<p>The provided token does not exist in the database.</p>";
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
        echo "<h4>✗ Database Error</h4>";
        echo "<p>Error: " . $e->getMessage() . "</p>";
        echo "</div>";
    }
}

// Show all reset tokens in the database
echo "<h3>All Reset Tokens in Database</h3>";
try {
    $stmt = $pdo->prepare("SELECT id, nurse_id, name, email, reset_token, reset_expiry FROM nurses WHERE reset_token IS NOT NULL");
    $stmt->execute();
    $tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($tokens) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Nurse ID</th><th>Name</th><th>Email</th><th>Token (First 16 chars)</th><th>Expiry</th><th>Status</th></tr>";
        
        $now = new DateTime();
        
        foreach ($tokens as $tokenData) {
            $expiry = new DateTime($tokenData['reset_expiry']);
            $isExpired = $now > $expiry;
            $status = $isExpired ? "EXPIRED" : "VALID";
            $statusColor = $isExpired ? "red" : "green";
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($tokenData['nurse_id']) . "</td>";
            echo "<td>" . htmlspecialchars($tokenData['name']) . "</td>";
            echo "<td>" . htmlspecialchars($tokenData['email']) . "</td>";
            echo "<td>" . substr($tokenData['reset_token'], 0, 16) . "...</td>";
            echo "<td>" . $tokenData['reset_expiry'] . "</td>";
            echo "<td style='color: $statusColor; font-weight: bold;'>$status</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No reset tokens found in the database.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error fetching tokens: " . $e->getMessage() . "</p>";
}

// Test token generation
echo "<h3>Test Token Generation</h3>";
echo "<form method='GET' style='margin: 20px 0;'>";
echo "<label for='test_email'>Test with email:</label> ";
echo "<input type='email' name='test_email' id='test_email' placeholder='Enter email to test' style='padding: 5px; margin: 0 10px;'>";
echo "<button type='submit' style='padding: 5px 15px; background: #1aaf51; color: white; border: none; border-radius: 3px;'>Generate Test Token</button>";
echo "</form>";

if (isset($_GET['test_email'])) {
    $testEmail = $_GET['test_email'];
    
    try {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id, nurse_id, name FROM nurses WHERE email = ?");
        $stmt->execute([$testEmail]);
        $nurse = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($nurse) {
            // Generate a test token
            $resetToken = bin2hex(random_bytes(32));
            $resetExpiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store the test token
            $stmt = $pdo->prepare("UPDATE nurses SET reset_token = ?, reset_expiry = ? WHERE id = ?");
            $stmt->execute([$resetToken, $resetExpiry, $nurse['id']]);
            
            echo "<div style='background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
            echo "<h4>✓ Test Token Generated</h4>";
            echo "<p><strong>For:</strong> " . htmlspecialchars($nurse['name']) . " (" . htmlspecialchars($nurse['nurse_id']) . ")</p>";
            echo "<p><strong>Token:</strong> " . $resetToken . "</p>";
            echo "<p><strong>Expires:</strong> " . $resetExpiry . "</p>";
            echo "<p><strong>Reset Link:</strong> <a href='../auth/reset_password.html?token=" . $resetToken . "' target='_blank'>Click here to test</a></p>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
            echo "<h4>✗ Email Not Found</h4>";
            echo "<p>The email '$testEmail' is not registered in the system.</p>";
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
        echo "<h4>✗ Error</h4>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "</div>";
    }
}

// Clean up expired tokens
echo "<h3>Clean Up Expired Tokens</h3>";
echo "<form method='GET' style='margin: 20px 0;'>";
echo "<input type='hidden' name='cleanup' value='1'>";
echo "<button type='submit' style='padding: 5px 15px; background: #dc3545; color: white; border: none; border-radius: 3px;'>Clean Up Expired Tokens</button>";
echo "</form>";

if (isset($_GET['cleanup'])) {
    try {
        $stmt = $pdo->prepare("UPDATE nurses SET reset_token = NULL, reset_expiry = NULL WHERE reset_expiry < NOW()");
        $stmt->execute();
        $cleanedCount = $stmt->rowCount();
        
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
        echo "<h4>✓ Cleanup Complete</h4>";
        echo "<p>Cleaned up $cleanedCount expired tokens.</p>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
        echo "<h4>✗ Cleanup Error</h4>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "</div>";
    }
}

echo "<hr>";
echo "<h3>Common Issues and Solutions:</h3>";
echo "<ul>";
echo "<li><strong>Token not found:</strong> The token may have been used already or never existed</li>";
echo "<li><strong>Token expired:</strong> Tokens expire after 1 hour for security</li>";
echo "<li><strong>Database issues:</strong> Check if the nurses table has reset_token and reset_expiry columns</li>";
echo "<li><strong>URL encoding:</strong> Make sure the token in the URL is properly encoded</li>";
echo "</ul>";

echo "<p><a href='../auth/forgot_password.html' style='background: #1aaf51; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Request New Reset Link</a></p>";
?>
