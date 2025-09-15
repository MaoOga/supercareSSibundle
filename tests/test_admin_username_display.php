<?php
require_once '../database/config.php';
require_once '../auth/admin_session_manager.php';

echo "<h1>Admin Username Display Test</h1>";

// Check if admin session is valid
if (!$adminSession->validateSession()) {
    echo "<h2>❌ Admin session is not valid</h2>";
    echo "<p>Please log in as admin first.</p>";
    echo "<p><a href='../admin/admin_login_new.html'>Go to admin login</a></p>";
    exit();
}

// Get admin user info
$adminUser = $adminSession->getAdminInfo();

if (!$adminUser) {
    echo "<h2>❌ Could not retrieve admin user info</h2>";
    exit();
}

echo "<h2>✅ Admin session is valid</h2>";
echo "<p><strong>Admin Username:</strong> " . htmlspecialchars($adminUser['admin_username']) . "</p>";
echo "<p><strong>Admin Username:</strong> " . htmlspecialchars($adminUser['admin_username']) . "</p>";
echo "<p><strong>Admin Email:</strong> " . htmlspecialchars($adminUser['admin_email']) . "</p>";

echo "<h2>Test Admin Pages with Username Display</h2>";
echo "<p>The following pages should now show the admin username in both desktop and mobile navigation:</p>";

echo "<h3>1. Admin Panel (admin.php)</h3>";
echo "<ul>";
echo "<li><strong>Desktop:</strong> Username shown below the title in red color</li>";
echo "<li><strong>Mobile:</strong> Username shown below 'Admin' in the bottom navigation</li>";
echo "<li><a href='../admin/admin.php' target='_blank'>Test admin.php</a></li>";
echo "</ul>";

echo "<h3>2. Audit Log (audit_log.php)</h3>";
echo "<ul>";
echo "<li><strong>Desktop:</strong> Username shown below the title in blue color</li>";
echo "<li><strong>Mobile:</strong> Username shown below 'Audit' in the bottom navigation</li>";
echo "<li><a href='../admin/audit_log.php' target='_blank'>Test audit_log.php</a></li>";
echo "</ul>";

echo "<h3>3. Patient Records (admin_patient_records.php)</h3>";
echo "<ul>";
echo "<li><strong>Desktop:</strong> Username shown below the title in red color</li>";
echo "<li><strong>Mobile:</strong> Username shown below 'Patients' in the bottom navigation</li>";
echo "<li><a href='../admin/admin_patient_records.php' target='_blank'>Test admin_patient_records.php</a></li>";
echo "</ul>";

echo "<h2>Expected Display Format</h2>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<p><strong>Desktop Header:</strong></p>";
echo "<p style='margin: 5px 0;'><i class='fas fa-user-shield' style='color: #dc2626;'></i> Logged in as: <strong>" . htmlspecialchars($adminUser['admin_username']) . "</strong></p>";
echo "</div>";

echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<p><strong>Mobile Navigation:</strong></p>";
echo "<p style='margin: 5px 0; font-size: 12px;'><i class='fas fa-user-shield' style='color: #dc2626;'></i> " . htmlspecialchars($adminUser['admin_username']) . "</p>";
echo "</div>";

echo "<h2>Features Added</h2>";
echo "<ul>";
echo "<li>✅ <strong>Desktop Display:</strong> Admin username shown in header below title</li>";
echo "<li>✅ <strong>Mobile Display:</strong> Admin username shown in bottom navigation</li>";
echo "<li>✅ <strong>Security:</strong> Username is HTML-escaped to prevent XSS</li>";
echo "<li>✅ <strong>Consistent Styling:</strong> Uses existing color schemes</li>";
echo "<li>✅ <strong>Icon:</strong> Shield icon to indicate admin status</li>";
echo "</ul>";

echo "<h2>Testing Instructions</h2>";
echo "<ol>";
echo "<li>Click on each admin page link above</li>";
echo "<li>Check that the admin username appears in the header (desktop)</li>";
echo "<li>Resize browser to mobile view or use mobile device</li>";
echo "<li>Check that the admin username appears in bottom navigation (mobile)</li>";
echo "<li>Verify the username matches: <strong>" . htmlspecialchars($adminUser['admin_username']) . "</strong></li>";
echo "</ol>";

echo "<h2>Color Schemes Used</h2>";
echo "<ul>";
echo "<li><strong>admin.php:</strong> Red color (var(--text-icon))</li>";
echo "<li><strong>audit_log.php:</strong> Blue color (var(--accent-color))</li>";
echo "<li><strong>admin_patient_records.php:</strong> Red color (var(--text-icon))</li>";
echo "</ul>";

echo "<hr>";
echo "<p><small>Test completed at: " . date('Y-m-d H:i:s') . "</small></p>";
?>
