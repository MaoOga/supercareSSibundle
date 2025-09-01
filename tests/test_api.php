<?php
// Test the new API endpoint
echo "<h2>Testing New API Endpoint</h2>";

// Test with patient ID 6 (the one from your error)
$test_url = "http://localhost/supercareSSibundle/get_patient_data_api.php?patient_id=6";

echo "<p>Testing URL: <code>$test_url</code></p>";

// Make the request
$response = file_get_contents($test_url);

echo "<h3>Response:</h3>";
echo "<pre>";
echo htmlspecialchars($response);
echo "</pre>";

// Try to decode JSON
$data = json_decode($response, true);
if ($data) {
    echo "<h3>Decoded JSON:</h3>";
    echo "<pre>";
    print_r($data);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>Failed to decode JSON!</p>";
}
?>
