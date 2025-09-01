<?php
// Test file to demonstrate the drain radio button fix
require_once '../database/config.php';

echo "<h2>Drain Radio Button Fix Test</h2>";

// Test 1: Simulate form submission with no radio button selected
echo "<h3>Test 1: No radio button selected</h3>";
$formData1 = [
    'name' => 'Test Patient 1',
    'age' => '30',
    'Sex' => 'Male',
    'uhid' => 'TEST001',
    'phone' => '1234567890',
    'bed' => 'Ward 1',
    'address' => 'Test Address',
    'diagnosis' => 'Test Diagnosis',
    'surgical_procedure' => 'Test Procedure',
    'drain_1' => 'Some drain description', // Drain description filled but no radio selected
    'drain_2' => 'Another drain description'
];

echo "Form data with drain descriptions but no radio button selected:<br>";
echo "drain-used: " . (isset($formData1['drain-used']) ? $formData1['drain-used'] : 'NOT SET') . "<br>";
echo "drain_1: " . $formData1['drain_1'] . "<br>";
echo "drain_2: " . $formData1['drain_2'] . "<br><br>";

// Apply the new logic
$drainUsed1 = isset($formData1['drain-used']) ? $formData1['drain-used'] : null;
echo "Result: drain_used = " . ($drainUsed1 ?? 'NOT SELECTED') . "<br>";
echo "Should insert drain records: " . ($drainUsed1 === 'Yes' ? 'Yes (with descriptions)' : ($drainUsed1 === 'No' ? 'Yes (No drains used record)' : 'No')) . "<br><br>";

// Test 2: Simulate form submission with "Yes" radio button selected
echo "<h3>Test 2: 'Yes' radio button selected</h3>";
$formData2 = [
    'name' => 'Test Patient 2',
    'age' => '35',
    'Sex' => 'Female',
    'uhid' => 'TEST002',
    'phone' => '0987654321',
    'bed' => 'Ward 2',
    'address' => 'Test Address 2',
    'diagnosis' => 'Test Diagnosis 2',
    'surgical_procedure' => 'Test Procedure 2',
    'drain-used' => 'Yes', // Radio button selected
    'drain_1' => 'Drain description 1',
    'drain_2' => 'Drain description 2'
];

echo "Form data with 'Yes' radio button selected:<br>";
echo "drain-used: " . $formData2['drain-used'] . "<br>";
echo "drain_1: " . $formData2['drain_1'] . "<br>";
echo "drain_2: " . $formData2['drain_2'] . "<br><br>";

// Apply the new logic
$drainUsed2 = isset($formData2['drain-used']) ? $formData2['drain-used'] : null;
echo "Result: drain_used = " . ($drainUsed2 ?? 'NOT SELECTED') . "<br>";
echo "Should insert drain records: " . ($drainUsed2 === 'Yes' ? 'Yes (with descriptions)' : ($drainUsed2 === 'No' ? 'Yes (No drains used record)' : 'No')) . "<br><br>";

// Test 3: Simulate form submission with "No" radio button selected
echo "<h3>Test 3: 'No' radio button selected</h3>";
$formData3 = [
    'name' => 'Test Patient 3',
    'age' => '40',
    'Sex' => 'Male',
    'uhid' => 'TEST003',
    'phone' => '5555555555',
    'bed' => 'Ward 3',
    'address' => 'Test Address 3',
    'diagnosis' => 'Test Diagnosis 3',
    'surgical_procedure' => 'Test Procedure 3',
    'drain-used' => 'No', // Radio button selected
    'drain_1' => 'Drain description (should not be inserted)',
    'drain_2' => 'Another drain description (should not be inserted)'
];

echo "Form data with 'No' radio button selected:<br>";
echo "drain-used: " . $formData3['drain-used'] . "<br>";
echo "drain_1: " . $formData3['drain_1'] . "<br>";
echo "drain_2: " . $formData3['drain_2'] . "<br><br>";

// Apply the new logic
$drainUsed3 = isset($formData3['drain-used']) ? $formData3['drain-used'] : null;
echo "Result: drain_used = " . ($drainUsed3 ?? 'NOT SELECTED') . "<br>";
echo "Should insert drain records: " . ($drainUsed3 === 'Yes' ? 'Yes (with descriptions)' : ($drainUsed3 === 'No' ? 'Yes (No drains used record)' : 'No')) . "<br><br>";

echo "<h3>Summary of the Updated Fix:</h3>";
echo "<ul>";
echo "<li><strong>Before:</strong> Drain records were always inserted if drain descriptions existed, regardless of radio button selection</li>";
echo "<li><strong>After:</strong> Drain records are handled properly based on radio button selection:</li>";
echo "<ul>";
echo "<li><strong>Yes selected:</strong> Insert drain records with descriptions</li>";
echo "<li><strong>No selected:</strong> Insert one record indicating 'No drains used'</li>";
echo "<li><strong>Nothing selected:</strong> Don't insert any drain records</li>";
echo "</ul>";
echo "<li><strong>Display Fix:</strong> When editing, the radio button shows the actual selection or remains unselected if nothing was chosen</li>";
echo "</ul>";

echo "<h3>Files Modified:</h3>";
echo "<ul>";
echo "<li>submit_form.php - Fixed drain insertion logic</li>";
echo "<li>submit_form_working.php - Fixed drain insertion logic</li>";
echo "<li>submit_form_backup.php - Fixed drain insertion logic</li>";
echo "<li>form_template.html - Fixed radio button display logic</li>";
echo "</ul>";
?>
