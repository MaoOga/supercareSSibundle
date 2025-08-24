<?php
require_once 'config.php';

echo "<h2>Drain Issue Debug</h2>";

// Check the drains table structure
echo "<h3>1. Drains Table Structure</h3>";
try {
    $stmt = $pdo->query("DESCRIBE drains");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "<td>" . $column['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Check existing drain records
echo "<h3>2. Existing Drain Records</h3>";
try {
    $stmt = $pdo->query("SELECT * FROM drains ORDER BY patient_id, drain_number");
    $drains = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($drains)) {
        echo "<p>No drain records found in database.</p>";
    } else {
        echo "<table border='1'>";
        echo "<tr><th>drain_id</th><th>patient_id</th><th>drain_used</th><th>drain_description</th><th>drain_number</th></tr>";
        foreach ($drains as $drain) {
            echo "<tr>";
            echo "<td>" . $drain['drain_id'] . "</td>";
            echo "<td>" . $drain['patient_id'] . "</td>";
            echo "<td>" . $drain['drain_used'] . "</td>";
            echo "<td>" . htmlspecialchars($drain['drain_description']) . "</td>";
            echo "<td>" . $drain['drain_number'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Check patients table for recent entries
echo "<h3>3. Recent Patient Records</h3>";
try {
    $stmt = $pdo->query("SELECT patient_id, name, uhid, date_completed FROM patients ORDER BY date_completed DESC LIMIT 10");
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($patients)) {
        echo "<p>No patient records found.</p>";
    } else {
        echo "<table border='1'>";
        echo "<tr><th>patient_id</th><th>name</th><th>uhid</th><th>date_completed</th></tr>";
        foreach ($patients as $patient) {
            echo "<tr>";
            echo "<td>" . $patient['patient_id'] . "</td>";
            echo "<td>" . htmlspecialchars($patient['name']) . "</td>";
            echo "<td>" . htmlspecialchars($patient['uhid']) . "</td>";
            echo "<td>" . $patient['date_completed'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Test the form submission logic
echo "<h3>4. Testing Form Submission Logic</h3>";

// Test case 1: No radio button selected
$formData1 = [
    'drain_1' => 'Test drain description',
    'drain_2' => 'Another test description'
];

echo "<h4>Test Case 1: No radio button selected</h4>";
echo "Form data: " . json_encode($formData1) . "<br>";
$drainUsed1 = isset($formData1['drain-used']) && $formData1['drain-used'] === 'Yes' ? 'Yes' : 'No';
echo "Result: drain_used = '$drainUsed1'<br>";
echo "Should insert drain records: " . ($drainUsed1 === 'Yes' ? 'Yes' : 'No') . "<br><br>";

// Test case 2: Yes radio button selected
$formData2 = [
    'drain-used' => 'Yes',
    'drain_1' => 'Test drain description',
    'drain_2' => 'Another test description'
];

echo "<h4>Test Case 2: Yes radio button selected</h4>";
echo "Form data: " . json_encode($formData2) . "<br>";
$drainUsed2 = isset($formData2['drain-used']) && $formData2['drain-used'] === 'Yes' ? 'Yes' : 'No';
echo "Result: drain_used = '$drainUsed2'<br>";
echo "Should insert drain records: " . ($drainUsed2 === 'Yes' ? 'Yes' : 'No') . "<br><br>";

// Test case 3: No radio button selected
$formData3 = [
    'drain-used' => 'No',
    'drain_1' => 'Test drain description',
    'drain_2' => 'Another test description'
];

echo "<h4>Test Case 3: No radio button selected</h4>";
echo "Form data: " . json_encode($formData3) . "<br>";
$drainUsed3 = isset($formData3['drain-used']) && $formData3['drain-used'] === 'Yes' ? 'Yes' : 'No';
echo "Result: drain_used = '$drainUsed3'<br>";
echo "Should insert drain records: " . ($drainUsed3 === 'Yes' ? 'Yes' : 'No') . "<br><br>";

echo "<h3>5. Instructions for Testing</h3>";
echo "<ol>";
echo "<li>Open the form in your browser</li>";
echo "<li>Fill in some drain descriptions but DO NOT select any radio button</li>";
echo "<li>Submit the form</li>";
echo "<li>Check if drain records were inserted (they should NOT be)</li>";
echo "<li>Edit the form and check if the radio button shows 'No'</li>";
echo "<li>Select 'Yes' radio button, fill drain descriptions, and submit</li>";
echo "<li>Check if drain records were inserted (they should be)</li>";
echo "<li>Edit the form and check if the radio button shows 'Yes'</li>";
echo "</ol>";

echo "<h3>6. Current Files Status</h3>";
$files = [
    'submit_form.php',
    'submit_form_working.php', 
    'submit_form_backup.php',
    'form_template.html'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p>✓ $file exists</p>";
    } else {
        echo "<p>✗ $file missing</p>";
    }
}
?>
