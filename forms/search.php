<?php
require_once '../database/config.php';
require_once '../auth/session_config.php';

// Check if user is logged in
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// Check session activity
$session_valid = checkSessionActivity();

// If not logged in or session invalid, redirect to login
if (!$logged_in || !$session_valid) {
    header('Location: ../auth/login.html?msg=session_expired');
    exit();
}

// Get nurse information from session
$nurse_info = getNurseInfo();
$nurse_id = $nurse_info['nurse_id'] ?? '';
$nurse_name = $nurse_info['name'] ?? '';
$nurse_email = $nurse_info['email'] ?? '';

// If logged in, serve the search.html content
$search_html = file_get_contents('search.html');

// Inject nurse information directly into the HTML elements
$search_html = str_replace(
    '<span class="font-bold" id="nurseIdValue">Loading...</span>',
    '<span class="font-bold" id="nurseIdValue">' . htmlspecialchars($nurse_id) . '</span>',
    $search_html
);

$search_html = str_replace(
    '<span class="font-bold text-xs" id="mobileNurseIdValue">Loading...</span>',
    '<span class="font-bold text-xs" id="mobileNurseIdValue">' . htmlspecialchars($nurse_id) . '</span>',
    $search_html
);

// Show the nurse ID displays by default since we have the data
$search_html = str_replace(
    'id="nurseIdDisplay"',
    'id="nurseIdDisplay" style="display: flex;"',
    $search_html
);

$search_html = str_replace(
    'id="mobileNurseIdDisplay"',
    'id="mobileNurseIdDisplay" style="display: flex;"',
    $search_html
);

// Inject nurse data into JavaScript for compatibility
$nurse_data_json = json_encode([
    'nurse_id' => $nurse_id,
    'name' => $nurse_name,
    'email' => $nurse_email
]);

// Add nurse data to the page for JavaScript to use
$search_html = str_replace(
    '<script>',
    "<script>\n// Nurse data injected by PHP\nwindow.nurseData = $nurse_data_json;\n// Pre-populate sessionStorage for compatibility\nif (typeof sessionStorage !== 'undefined') {\n  sessionStorage.setItem('nurseInfo', JSON.stringify(window.nurseData));\n}\n",
    $search_html
);

// Set content type to HTML
header('Content-Type: text/html; charset=UTF-8');

// Output the modified search.html content
echo $search_html;
?>

