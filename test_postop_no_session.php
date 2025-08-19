<?php
// Simplified test version without session requirements
header('Content-Type: application/json');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    $formData = $_POST;
    
    // Debug logging
    error_log("Test form submission - Form data keys: " . implode(', ', array_keys($formData)));
    
    // Check for post-operative related keys specifically
    $postOpKeys = [];
    foreach (array_keys($formData) as $key) {
        if (strpos($key, 'post-operative') !== false || strpos($key, 'post-dosage') !== false || strpos($key, 'type-ofdischarge') !== false || strpos($key, 'tenderness-pain') !== false || strpos($key, 'swelling') !== false || strpos($key, 'Fever') !== false) {
            $postOpKeys[] = $key;
        }
    }
    error_log("Post-operative related keys: " . implode(', ', $postOpKeys));
    
    // Create a test patient
    $patientStmt = $pdo->prepare("INSERT INTO patients (name, age, sex, uhid, phone, bed_ward, address, primary_diagnosis, surgical_procedure, date_completed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $patientStmt->execute([
        $formData['name'],
        $formData['age'],
        $formData['Sex'],
        $formData['uhid'],
        $formData['phone'],
        $formData['bed'],
        $formData['address'],
        $formData['diagnosis'],
        $formData['surgical_procedure'],
        date('Y-m-d')
    ]);
    $patientId = $pdo->lastInsertId();
    
    error_log("Created test patient with ID: $patientId");
    
    // Handle post_operative_monitoring table - TEST VERSION
    for ($i = 1; $i <= 10; $i++) { // Check up to 10 monitoring entries
        $monitoringDate = null;
        
        // Check if the post-operative array exists and has the date field
        if (isset($formData['post-operative']) && is_array($formData['post-operative'])) {
            // Check if it's the old format (without row numbers)
            if (isset($formData['post-operative']['date']) && !isset($formData['post-operative']["date_$i"])) {
                // Use the single values for all rows
                $monitoringDate = $formData['post-operative']['date'] ?? null;
            } else {
                // Use the row-specific values
                $monitoringDate = $formData['post-operative']["date_$i"] ?? null;
            }
        } else {
            // Try direct field access for the array notation
            $dateKey = "post-operative[date]_$i";
            $monitoringDate = $formData[$dateKey] ?? null;
            
            // If still not found, try direct field names without array notation
            if (empty($monitoringDate)) {
                $monitoringDate = $formData["date_$i"] ?? null;
            }
        }
        
        // Debug: Log what we found
        error_log("Row $i - Date parsing: monitoringDate='$monitoringDate', dateKey='$dateKey'");
        
        // Check if there's actual data in this row (not just a date)
        $hasData = !empty($formData["post-dosage_$i"]) || 
                   !empty($formData["type-ofdischarge_$i"]) || 
                   !empty($formData["tenderness-pain_$i"]) || 
                   !empty($formData["swelling_$i"]) || 
                   !empty($formData["Fever_$i"]);
        
        error_log("Post-operative row $i - Date: $monitoringDate, HasData: " . ($hasData ? 'Yes' : 'No') . ", Dosage: " . ($formData["post-dosage_$i"] ?? 'NOT SET'));
        
        if (!empty($monitoringDate) && $hasData) {
            $monitoringDate = date('Y-m-d', strtotime(str_replace('/', '-', $monitoringDate)));
            
            error_log("Inserting post-operative monitoring $i: Date=$monitoringDate, Fever=" . ($formData["Fever_$i"] ?? 'NOT SET'));
            $postOpStmt = $pdo->prepare("INSERT INTO post_operative_monitoring (patient_id, day, monitoring_date, dosage, discharge_fluid, tenderness_pain, swelling, fever) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $postOpStmt->execute([
                $patientId,
                $i,
                $monitoringDate,
                $formData["post-dosage_$i"] ?? null,
                $formData["type-ofdischarge_$i"] ?? null,
                $formData["tenderness-pain_$i"] ?? null,
                $formData["swelling_$i"] ?? null,
                $formData["Fever_$i"] ?? null
            ]);
            
            error_log("SUCCESS: Inserted post-operative monitoring row $i for patient $patientId");
        }
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Test patient data saved successfully!',
        'patient_id' => $patientId,
        'debug_info' => [
            'total_post_fields' => count($formData),
            'post_operative_array_exists' => isset($formData['post-operative']),
            'post_operative_array_content' => $formData['post-operative'] ?? 'NOT SET'
        ]
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false, 
        'message' => 'Database error occurred: ' . $e->getMessage(),
        'debug_info' => [
            'total_post_fields' => count($formData),
            'post_operative_array_exists' => isset($formData['post-operative']),
            'post_operative_array_content' => $formData['post-operative'] ?? 'NOT SET'
        ]
    ]);
}
?>
