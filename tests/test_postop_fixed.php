<?php
// Fixed version that manually parses the array field names
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
    error_log("Test form submission - Form data keys: " . implode(', ', array_keys($formData)));
    
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
    
    // Handle post_operative_monitoring table - FIXED VERSION
    for ($i = 1; $i <= 10; $i++) { // Check up to 10 monitoring entries
        $monitoringDate = null;
        
        // Method 1: Try the array notation directly
        $dateKey = "post-operative[date]_$i";
        $monitoringDate = $formData[$dateKey] ?? null;
        
        // Method 2: If not found, try the array access
        if (empty($monitoringDate) && isset($formData['post-operative']) && is_array($formData['post-operative'])) {
            if (isset($formData['post-operative']["date_$i"])) {
                $monitoringDate = $formData['post-operative']["date_$i"];
            } elseif (isset($formData['post-operative']['date'])) {
                // Use single date for all rows
                $monitoringDate = $formData['post-operative']['date'];
            }
        }
        
        // Method 3: Try simple field name
        if (empty($monitoringDate)) {
            $monitoringDate = $formData["date_$i"] ?? null;
        }
        
        // Check if there's actual data in this row (not just a date)
        $hasData = !empty($formData["post-dosage_$i"]) || 
                   !empty($formData["type-ofdischarge_$i"]) || 
                   !empty($formData["tenderness-pain_$i"]) || 
                   !empty($formData["swelling_$i"]) || 
                   !empty($formData["Fever_$i"]);
        
        error_log("Row $i - Date: '$monitoringDate', HasData: " . ($hasData ? 'Yes' : 'No') . ", Dosage: '" . ($formData["post-dosage_$i"] ?? 'NOT SET') . "'");
        
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
            'post_operative_array_content' => $formData['post-operative'] ?? 'NOT SET',
            'post_operative_keys' => array_filter(array_keys($formData), function($key) {
                return strpos($key, 'post-operative') !== false;
            })
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
