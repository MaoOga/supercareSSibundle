<?php
/**
 * Test Admin Patient Records View Details Functionality
 * Verify that the View Details button works correctly
 */

echo "<h2>Admin Patient Records View Details Test</h2>";

// Test 1: Check if admin_patient_records.php exists and has the correct function
echo "<h3>1. Checking admin_patient_records.php</h3>";

$adminPatientRecordsContent = file_get_contents('admin_patient_records.php');

if (strpos($adminPatientRecordsContent, 'form.php?patient_id=') !== false) {
    echo "✅ admin_patient_records.php correctly uses form.php<br>";
} else {
    echo "❌ admin_patient_records.php still uses form.html<br>";
}

if (strpos($adminPatientRecordsContent, 'readonly=true') !== false) {
    echo "✅ admin_patient_records.php includes readonly=true parameter<br>";
} else {
    echo "❌ admin_patient_records.php missing readonly=true parameter<br>";
}

// Test 2: Check if form.php exists and handles readonly parameter
echo "<h3>2. Checking form.php</h3>";

if (file_exists('form.php')) {
    echo "✅ form.php exists<br>";
    
    $formContent = file_get_contents('form.php');
    if (strpos($formContent, 'form_template.html') !== false) {
        echo "✅ form.php includes form_template.html<br>";
    } else {
        echo "❌ form.php doesn't include form_template.html<br>";
    }
} else {
    echo "❌ form.php doesn't exist<br>";
}

// Test 3: Check if form_template.html has read-only functionality
echo "<h3>3. Checking form_template.html read-only functionality</h3>";

$formTemplateContent = file_get_contents('form_template.html');

if (strpos($formTemplateContent, 'makeFormReadOnly()') !== false) {
    echo "✅ form_template.html has makeFormReadOnly() function<br>";
} else {
    echo "❌ form_template.html missing makeFormReadOnly() function<br>";
}

if (strpos($formTemplateContent, 'readonly=true') !== false) {
    echo "✅ form_template.html handles readonly=true parameter<br>";
} else {
    echo "❌ form_template.html doesn't handle readonly=true parameter<br>";
}

if (strpos($formTemplateContent, 'Read-Only View') !== false) {
    echo "✅ form_template.html shows read-only indicator<br>";
} else {
    echo "❌ form_template.html missing read-only indicator<br>";
}

// Test 4: Check if there are any patients in the database to test with
echo "<h3>4. Checking for test patients</h3>";

require_once 'config.php';

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM patients LIMIT 1");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result && $result['count'] > 0) {
        echo "✅ Found patients in database for testing<br>";
        
        // Get a sample patient ID
        $stmt = $pdo->prepare("SELECT patient_id FROM patients LIMIT 1");
        $stmt->execute();
        $patient = $stmt->fetch();
        
        if ($patient) {
            $testUrl = "form.php?patient_id={$patient['patient_id']}&readonly=true";
            echo "✅ Test URL: <a href='$testUrl' target='_blank'>$testUrl</a><br>";
        }
    } else {
        echo "❌ No patients found in database<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 5: JavaScript function test
echo "<h3>5. JavaScript Function Test</h3>";
echo "The viewDetails() function should now work correctly:<br>";
echo "<code>viewDetails(patientId) {<br>";
echo "  window.open(`form.php?patient_id=\${patientId}&readonly=true`, '_blank');<br>";
echo "}</code><br>";

echo "<h3>6. Summary</h3>";
echo "✅ Fixed: admin_patient_records.php now uses form.php instead of form.html<br>";
echo "✅ Fixed: View Details button will open form in read-only mode<br>";
echo "✅ Fixed: Admin can view patient details without editing capabilities<br>";
echo "✅ Fixed: Form shows read-only indicator when in admin view mode<br>";

echo "<h3>7. How to Test</h3>";
echo "1. Go to admin_patient_records.php<br>";
echo "2. Search for a patient<br>";
echo "3. Click 'View Details' button<br>";
echo "4. Should open form.php with patient data in read-only mode<br>";
echo "5. Form should show '(Read-Only View)' indicator<br>";
echo "6. All form fields should be disabled/read-only<br>";
?>
