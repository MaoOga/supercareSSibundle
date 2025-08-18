<?php
require_once 'config.php';
require_once 'session_config.php';
require_once 'audit_logger.php';

echo "<h1>Complete Flow Test</h1>";

try {
    // Step 1: Simulate nurse login
    echo "<h2>Step 1: Simulating Nurse Login</h2>";
    
    // Clear any existing session
    session_destroy();
    session_start();
    
    // Simulate nurse data (like from database)
    $nurse = [
        'id' => 'test_nurse_123',
        'nurse_id' => 'NURSE001',
        'name' => 'Test Nurse',
        'email' => 'test@hospital.com',
        'role' => 'nurse'
    ];
    
    // Store nurse info in session (like nurse_login.php does)
    $_SESSION['nurse_id'] = $nurse['id'];
    $_SESSION['nurse_info'] = $nurse;
    $_SESSION['logged_in'] = true;
    
    echo "<p>✅ Nurse session created:</p>";
    echo "<pre>" . print_r($_SESSION['nurse_info'], true) . "</pre>";
    
    // Step 2: Simulate form submission
    echo "<h2>Step 2: Simulating Form Submission</h2>";
    
    // Simulate form data
    $formData = [
        'name' => 'John Doe',
        'age' => '35',
        'Sex' => 'Male',
        'uhid' => 'UHID123456',
        'phone' => '1234567890',
        'bed' => 'A101',
        'address' => '123 Main St',
        'diagnosis' => 'Appendicitis',
        'surgical_procedure' => 'Appendectomy',
        'patient_info' => [
            'surgeon' => 'Dr. Smith',
            'doa' => '2024-01-15',
            'dos' => '2024-01-16',
            'dod' => '2024-01-18'
        ],
        'operation_duration' => '2 hours',
        'surgical_skin_preparation' => [
            'pre_op_bath' => 'Yes',
            'hair-removal' => 'Yes',
            'removal-done' => 'Abdomen'
        ],
        'implanted_used' => 'No',
        'drain-used' => 'No',
        'drug-name_1' => 'Amoxicillin',
        'dosage_1' => '500mg TDS',
        'antibiotic_usage' => [
            'startedon_1' => '2024-01-16',
            'stoppeon_1' => '2024-01-20'
        ],
        'post-operative' => [
            'date_1' => '2024-01-17'
        ],
        'post-dosage_1' => '500mg TDS',
        'type-ofdischarge_1' => 'Normal',
        'tenderness-pain_1' => 'Mild',
        'swelling_1' => 'None',
        'Fever_1' => 'No',
        'Cultural-Swap' => 'Negative',
        'Dressing-Finding' => 'Clean',
        'ReviewOn' => '2024-01-25',
        'SuturesROn' => '2024-01-25',
        'RevieworPhoneDate' => '2024-01-30',
        'reviewp' => 'Good',
        'reviewppain' => 'None'
    ];
    
    echo "<p>✅ Form data prepared</p>";
    
    // Step 3: Simulate database operations (simplified)
    echo "<h2>Step 3: Simulating Database Operations</h2>";
    
    // Simulate patient ID generation
    $patientId = 'TEST_PATIENT_' . time();
    
    echo "<p>✅ Patient ID generated: {$patientId}</p>";
    
    // Step 4: Test audit logging
    echo "<h2>Step 4: Testing Audit Logging</h2>";
    
    $auditLogger = new AuditLogger($pdo);
    
    // Get nurse info from session
    $nurseName = $_SESSION['nurse_info']['name'] ?? 'Unknown Nurse';
    $nurseId = $_SESSION['nurse_info']['nurse_id'] ?? 'Unknown';
    
    echo "<p>Nurse ID from session: {$nurseId}</p>";
    echo "<p>Nurse Name from session: {$nurseName}</p>";
    
    // Test patient create audit log
    $patientData = [
        'patient_id' => $patientId,
        'uhid' => $formData['uhid'],
        'name' => $formData['name'],
        'action_type' => 'CREATE'
    ];
    
    $result = $auditLogger->logPatientCreate($nurseId, $patientId, $formData['name'], $patientData);
    
    if ($result) {
        echo "<p>✅ Patient create audit log created successfully</p>";
    } else {
        echo "<p>❌ Failed to create patient audit log</p>";
    }
    
    // Step 5: Verify audit log was created
    echo "<h2>Step 5: Verifying Audit Log</h2>";
    
    $logs = $pdo->query("SELECT * FROM admin_audit_logs WHERE entity_type = 'PATIENT' AND entity_id = '{$patientId}' ORDER BY timestamp DESC LIMIT 1")->fetchAll();
    
    if (count($logs) > 0) {
        echo "<p>✅ Audit log found in database:</p>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Timestamp</th><th>Admin User</th><th>Action</th><th>Entity ID</th><th>Description</th></tr>";
        
        foreach ($logs as $log) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($log['audit_id']) . "</td>";
            echo "<td>" . htmlspecialchars($log['timestamp']) . "</td>";
            echo "<td>" . htmlspecialchars($log['admin_user']) . "</td>";
            echo "<td>" . htmlspecialchars($log['action_type']) . "</td>";
            echo "<td>" . htmlspecialchars($log['entity_id']) . "</td>";
            echo "<td>" . htmlspecialchars($log['description']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ No audit log found for patient ID: {$patientId}</p>";
    }
    
    // Step 6: Test API endpoint
    echo "<h2>Step 6: Testing API Endpoint</h2>";
    
    $apiUrl = "get_audit_logs.php?entity_type=PATIENT&action_type=CREATE,UPDATE";
    echo "<p>Testing API: {$apiUrl}</p>";
    
    // Simulate API call
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<p>HTTP Code: {$httpCode}</p>";
    
    if ($response) {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "<p>✅ API returned success</p>";
            echo "<p>Total logs: " . count($data['data']['logs']) . "</p>";
            
            // Check if our test log is in the response
            $found = false;
            foreach ($data['data']['logs'] as $log) {
                if ($log['entity_id'] === $patientId) {
                    $found = true;
                    echo "<p>✅ Our test log found in API response</p>";
                    break;
                }
            }
            
            if (!$found) {
                echo "<p>❌ Our test log not found in API response</p>";
            }
        } else {
            echo "<p>❌ API returned error: " . ($data['message'] ?? 'Unknown error') . "</p>";
        }
    } else {
        echo "<p>❌ No response from API</p>";
    }
    
    echo "<h2>✅ Test Complete</h2>";
    echo "<p>If all steps passed, the audit logging system should be working correctly.</p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "Error: " . htmlspecialchars($e->getMessage());
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { color: #2563eb; }
h2 { color: #1f2937; margin-top: 30px; }
table { margin-top: 10px; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f3f4f6; }
pre { background-color: #f3f4f6; padding: 10px; border-radius: 4px; }
</style>
