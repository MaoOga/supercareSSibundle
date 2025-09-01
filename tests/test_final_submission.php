<?php
// Final test script to verify form submission is working
header('Content-Type: application/json');

require_once '../database/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    $formData = $_POST;
    
    // Debug logging
    error_log("FINAL TEST - Form data keys: " . implode(', ', array_keys($formData)));
    
    // Check for post-operative related keys specifically
    $postOpKeys = [];
    foreach (array_keys($formData) as $key) {
        if (strpos($key, 'post-operative') !== false || strpos($key, 'post-dosage') !== false || strpos($key, 'type-ofdischarge') !== false || strpos($key, 'tenderness-pain') !== false || strpos($key, 'swelling') !== false || strpos($key, 'Fever') !== false) {
            $postOpKeys[] = $key;
        }
    }
    error_log("FINAL TEST - Post-operative related keys: " . implode(', ', $postOpKeys));
    
    // Create a test patient
    $patientStmt = $pdo->prepare("INSERT INTO patients (name, age, sex, uhid, phone, bed_ward, address, primary_diagnosis, surgical_procedure, date_completed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $patientStmt->execute([
        $formData['name'] ?? 'Test Patient',
        $formData['age'] ?? 30,
        $formData['Sex'] ?? 'Male',
        $formData['uhid'] ?? 'FINALTEST',
        $formData['phone'] ?? '1234567890',
        $formData['bed'] ?? 'A1',
        $formData['address'] ?? 'Test Address',
        $formData['diagnosis'] ?? 'Test Diagnosis',
        $formData['surgical_procedure'] ?? 'Test Procedure',
        date('Y-m-d')
    ]);
    $patientId = $pdo->lastInsertId();
    
    error_log("FINAL TEST - Created test patient with ID: $patientId");
    
    // Handle post_operative_monitoring table
    for ($i = 1; $i <= 10; $i++) {
        $monitoringDate = null;
        
        // Method 1: Try the array notation directly
        $dateKey = "post-operative[date]_$i";
        $monitoringDate = $formData[$dateKey] ?? null;
        
        // Method 2: If not found, try the array access
        if (empty($monitoringDate) && isset($formData['post-operative']) && is_array($formData['post-operative'])) {
            if (isset($formData['post-operative']["date_$i"])) {
                $monitoringDate = $formData['post-operative']["date_$i"];
            } elseif (isset($formData['post-operative']['date'])) {
                $monitoringDate = $formData['post-operative']['date'];
            }
        }
        
        // Method 3: Try simple field name
        if (empty($monitoringDate)) {
            $monitoringDate = $formData["date_$i"] ?? null;
        }
        
        // Check if there's actual data in this row
        $hasData = !empty($formData["post-dosage_$i"]) || 
                   !empty($formData["type-ofdischarge_$i"]) || 
                   !empty($formData["tenderness-pain_$i"]) || 
                   !empty($formData["swelling_$i"]) || 
                   !empty($formData["Fever_$i"]);
        
        error_log("FINAL TEST - Row $i - Date: '$monitoringDate', HasData: " . ($hasData ? 'Yes' : 'No') . ", Dosage: '" . ($formData["post-dosage_$i"] ?? 'NOT SET') . "', DateKey: '$dateKey'");
        
        if (!empty($monitoringDate) && $hasData) {
            $monitoringDate = date('Y-m-d', strtotime(str_replace('/', '-', $monitoringDate)));
            
            error_log("FINAL TEST - Inserting post-operative monitoring $i: Date=$monitoringDate, Fever=" . ($formData["Fever_$i"] ?? 'NOT SET'));
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
            
            error_log("FINAL TEST - SUCCESS: Inserted post-operative monitoring row $i for patient $patientId");
        }
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Final test successful! Post-Operative Monitoring data saved.',
        'patient_id' => $patientId,
        'debug_info' => [
            'total_post_fields' => count($formData),
            'post_operative_keys' => $postOpKeys
        ]
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false, 
        'message' => 'Database error occurred: ' . $e->getMessage(),
        'debug_info' => [
            'total_post_fields' => count($formData),
            'post_operative_keys' => $postOpKeys ?? []
        ]
    ]);
}
?>
