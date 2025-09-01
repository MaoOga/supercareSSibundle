<?php
// Test script for wound complications
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
    error_log("WOUND COMPLICATIONS TEST - Form data keys: " . implode(', ', array_keys($formData)));
    
    // Check for wound complication related keys specifically
    $woundKeys = [];
    foreach (array_keys($formData) as $key) {
        if (strpos($key, 'Wound') !== false || strpos($key, 'wound') !== false || strpos($key, 'SSI') !== false || strpos($key, 'wtd') !== false || strpos($key, 'SurgeonOpinion') !== false) {
            $woundKeys[] = $key;
        }
    }
    error_log("WOUND COMPLICATIONS TEST - Wound related keys: " . implode(', ', $woundKeys));
    
    // Create a test patient
    $patientStmt = $pdo->prepare("INSERT INTO patients (name, age, sex, uhid, phone, bed_ward, address, primary_diagnosis, surgical_procedure, date_completed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $patientStmt->execute([
        $formData['name'] ?? 'Test Patient',
        $formData['age'] ?? 30,
        $formData['Sex'] ?? 'Male',
        $formData['uhid'] ?? 'WOUNDTEST',
        $formData['phone'] ?? '1234567890',
        $formData['bed'] ?? 'A1',
        $formData['address'] ?? 'Test Address',
        $formData['diagnosis'] ?? 'Test Diagnosis',
        $formData['surgical_procedure'] ?? 'Test Procedure',
        date('Y-m-d')
    ]);
    $patientId = $pdo->lastInsertId();
    
    error_log("WOUND COMPLICATIONS TEST - Created test patient with ID: $patientId");
    
    // Handle wound_complications table
    $complicationDate = !empty($formData['Wound-Complication Date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData['Wound-Complication Date']))) : null;
    
    error_log("WOUND COMPLICATIONS TEST - Complication Date: '" . ($formData['Wound-Complication Date'] ?? 'NOT SET') . "' -> '$complicationDate'");
    
    $woundStmt = $pdo->prepare("INSERT INTO wound_complications (
        patient_id, complication_date, wound_dehiscence, allergic_reaction, bleeding_haemorrhage, 
        other_complication, other_specify, notes, superficial_ssi, deep_si, organ_space_ssi,
        purulent_discharge_superficial, purulent_discharge_deep, purulent_discharge_organ,
        organism_identified_superficial, organism_identified_organ, clinical_diagnosis_ssi,
        deep_incision_reopening, abscess_evidence_organ, deliberate_opening_symptoms,
        abscess_evidence_deep, not_infected_conditions, surgeon_opinion_superficial,
        surgeon_opinion_deep, surgeon_opinion_organ
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $woundStmt->execute([
        $patientId,
        $complicationDate,
        isset($formData['wounddehiscene']) ? 1 : 0,
        isset($formData['AllergicR']) ? 1 : 0,
        isset($formData['BleedingH']) ? 1 : 0,
        isset($formData['Other']) ? 1 : 0,
        $formData['WoundD-Specify'] ?? null,
        $formData['WoundD-Notes'] ?? null,
        isset($formData['SuperficialSSI']) ? 1 : 0,
        isset($formData['DeepSI']) ? 1 : 0,
        isset($formData['OrganSI']) ? 1 : 0,
        isset($formData['wtd1-1']) ? 1 : 0,
        isset($formData['wtd1-2']) ? 1 : 0,
        isset($formData['wtd1-3']) ? 1 : 0,
        isset($formData['wtd2-1']) ? 1 : 0,
        isset($formData['wtd2-2']) ? 1 : 0,
        isset($formData['wtd3-1']) ? 1 : 0,
        isset($formData['wtd3-2']) ? 1 : 0,
        isset($formData['wtd3-3']) ? 1 : 0,
        isset($formData['wtd4-1']) ? 1 : 0,
        isset($formData['wtd4-2']) ? 1 : 0,
        isset($formData['wtd4-3']) ? 1 : 0,
        $formData['SurgeonOpinion1'] ?? null,
        $formData['SurgeonOpinion2'] ?? null,
        $formData['SurgeonOpinion3'] ?? null
    ]);
    
    error_log("WOUND COMPLICATIONS TEST - SUCCESS: Inserted wound complications for patient $patientId");
    
    // Now retrieve the data to verify it was saved correctly
    $retrieveStmt = $pdo->prepare("SELECT * FROM wound_complications WHERE patient_id = ?");
    $retrieveStmt->execute([$patientId]);
    $retrievedData = $retrieveStmt->fetch(PDO::FETCH_ASSOC);
    
    error_log("WOUND COMPLICATIONS TEST - Retrieved data: " . print_r($retrievedData, true));
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Wound complications test successful!',
        'patient_id' => $patientId,
        'complication_date_submitted' => $formData['Wound-Complication Date'] ?? 'NOT SET',
        'complication_date_saved' => $complicationDate,
        'retrieved_data' => $retrievedData,
        'debug_info' => [
            'total_post_fields' => count($formData),
            'wound_related_keys' => $woundKeys
        ]
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false, 
        'message' => 'Database error occurred: ' . $e->getMessage(),
        'debug_info' => [
            'total_post_fields' => count($formData),
            'wound_related_keys' => $woundKeys ?? []
        ]
    ]);
}
?>
