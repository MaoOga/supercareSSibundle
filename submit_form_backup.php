<?php
// Start output buffering to capture any potential output
ob_start();

// Suppress error reporting to prevent HTML error messages from corrupting JSON output
error_reporting(0);
ini_set('display_errors', 0);

require_once 'config.php';
// Temporarily comment out session requirements for debugging
// require_once 'session_config.php';
// require_once 'audit_logger.php';

// Clear any output that might have been generated
ob_clean();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    $formData = $_POST;
    $patientId = $formData['patient_id'] ?? null;
    $isUpdate = !empty($patientId);
    
    // Debug logging
    error_log("Form submission - Patient ID: $patientId, Is Update: " . ($isUpdate ? 'Yes' : 'No'));
    error_log("Form data keys: " . implode(', ', array_keys($formData)));
    
    // Check for post-operative related keys specifically
    $postOpKeys = [];
    foreach (array_keys($formData) as $key) {
        if (strpos($key, 'post-operative') !== false || strpos($key, 'post-dosage') !== false || strpos($key, 'type-ofdischarge') !== false || strpos($key, 'tenderness-pain') !== false || strpos($key, 'swelling') !== false || strpos($key, 'Fever') !== false) {
            $postOpKeys[] = $key;
        }
    }
    error_log("Post-operative related keys: " . implode(', ', $postOpKeys));
    
    if ($isUpdate) {
        // For updates, check if UHID exists for a different patient
        $uhidCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE uhid = ? AND patient_id != ?");
        $uhidCheckStmt->execute([$formData['uhid'], $patientId]);
        $uhidExists = $uhidCheckStmt->fetchColumn() > 0;
    } else {
        // For new records, check if UHID already exists
        $uhidCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE uhid = ?");
        $uhidCheckStmt->execute([$formData['uhid']]);
        $uhidExists = $uhidCheckStmt->fetchColumn() > 0;
    }
    
    if ($uhidExists) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'UHID already exists. Please use a unique UHID.']);
        exit;
    }

    // Handle patients table
    if ($isUpdate) {
        $patientStmt = $pdo->prepare("UPDATE patients SET name = ?, age = ?, sex = ?, uhid = ?, phone = ?, bed_ward = ?, address = ?, primary_diagnosis = ?, surgical_procedure = ?, date_completed = ? WHERE patient_id = ?");
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
            date('Y-m-d'),
            $patientId
        ]);
    } else {
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
    }

    // Handle surgical_details table
    $doa = !empty($formData['patient_info']['doa']) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData['patient_info']['doa']))) : null;
    $dos = !empty($formData['patient_info']['dos']) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData['patient_info']['dos']))) : null;
    $dod = !empty($formData['patient_info']['dod']) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData['patient_info']['dod']))) : null;

    if ($isUpdate) {
        // Delete existing surgical details
        $deleteStmt = $pdo->prepare("DELETE FROM surgical_details WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    $surgicalStmt = $pdo->prepare("INSERT INTO surgical_details (patient_id, doa, dos, dod, surgeon, operation_duration) VALUES (?, ?, ?, ?, ?, ?)");
    $surgicalStmt->execute([
        $patientId,
        $doa,
        $dos,
        $dod,
        $formData['patient_info']['surgeon'],
        $formData['operation_duration']
    ]);

    // Handle surgical_skin_preparation table
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM surgical_skin_preparation WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    $skinPrepStmt = $pdo->prepare("INSERT INTO surgical_skin_preparation (patient_id, pre_op_bath, pre_op_bath_reason, hair_removal, hair_removal_reason, hair_removal_location) VALUES (?, ?, ?, ?, ?, ?)");
    $skinPrepStmt->execute([
        $patientId,
        $formData['surgical_skin_preparation']['pre_op_bath'] ?? null,
        $formData['pre-notdone'] ?? null,
        $formData['surgical_skin_preparation']['hair-removal'] ?? null,
        $formData['hair-notdone'] ?? null,
        $formData['surgical_skin_preparation']['removal-done'] ?? null
    ]);

    // Handle implanted_materials table
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM implanted_materials WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    $implantStmt = $pdo->prepare("INSERT INTO implanted_materials (patient_id, implanted_used, metal, graft, patch, shunt_stent) VALUES (?, ?, ?, ?, ?, ?)");
    $implantStmt->execute([
        $patientId,
        $formData['implanted_used'] ?? null,
        $formData['metal'] ?? null,
        $formData['graft'] ?? null,
        $formData['Patch'] ?? null,
        $formData['Shunt'] ?? null
    ]);

    // Handle drains table - FIXED: Use correct field names
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM drains WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    // Insert drains - Look for drain descriptions in the form data
    $drainUsed = isset($formData['drain-used']) && $formData['drain-used'] === 'Yes' ? 'Yes' : 'No';
    error_log("Drain used: $drainUsed");
    
    // Check for drain descriptions in the form data
    for ($i = 1; $i <= 10; $i++) { // Check up to 10 drains
        $drainDescription = $formData["drain_$i"] ?? null; // Fixed: Use correct field name
        if (!empty($drainDescription)) {
            error_log("Inserting drain $i: $drainDescription");
            $drainStmt = $pdo->prepare("INSERT INTO drains (patient_id, drain_used, drain_description, drain_number) VALUES (?, ?, ?, ?)");
            $drainStmt->execute([
                $patientId,
                $drainUsed,
                $drainDescription,
                $i
            ]);
        }
    }

    // Handle antibiotic_usage table - FIXED: Use correct field names
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM antibiotic_usage WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    for ($i = 1; $i <= 10; $i++) { // Check up to 10 antibiotics
        $drugName = $formData["drug-name_$i"] ?? null;
        if (!empty($drugName)) {
            // Fix the date field names - these are array fields in the form
            $startedOn = null;
            $stoppedOn = null;
            
            // Check if the antibiotic_usage array exists and has the date fields
            if (isset($formData['antibiotic_usage']) && is_array($formData['antibiotic_usage'])) {
                // Check if it's the old format (without row numbers)
                if (isset($formData['antibiotic_usage']['startedon']) && !isset($formData['antibiotic_usage']["startedon_$i"])) {
                    // Use the single values for all rows
                    $startedOn = !empty($formData['antibiotic_usage']['startedon']) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData['antibiotic_usage']['startedon']))) : null;
                    $stoppedOn = !empty($formData['antibiotic_usage']['stoppeon']) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData['antibiotic_usage']['stoppeon']))) : null;
                } else {
                    // Use the row-specific values
                    $startedOn = !empty($formData['antibiotic_usage']["startedon_$i"]) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData['antibiotic_usage']["startedon_$i"]))) : null;
                    $stoppedOn = !empty($formData['antibiotic_usage']["stoppeon_$i"]) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData['antibiotic_usage']["stoppeon_$i"]))) : null;
                }
            } else {
                // Try direct field access for the array notation
                $startedOnKey = "antibiotic_usage[startedon]_$i";
                $stoppedOnKey = "antibiotic_usage[stoppeon]_$i";
                
                $startedOn = !empty($formData[$startedOnKey]) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData[$startedOnKey]))) : null;
                $stoppedOn = !empty($formData[$stoppedOnKey]) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData[$stoppedOnKey]))) : null;
                
                // If still not found, try direct field names without array notation
                if (empty($startedOn)) {
                    $startedOn = !empty($formData["startedon_$i"]) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData["startedon_$i"]))) : null;
                }
                if (empty($stoppedOn)) {
                    $stoppedOn = !empty($formData["stoppeon_$i"]) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData["stoppeon_$i"]))) : null;
                }
            }
            
            error_log("Inserting antibiotic $i: $drugName, Started: $startedOn, Stopped: $stoppedOn");
            $antibioticStmt = $pdo->prepare("INSERT INTO antibiotic_usage (patient_id, serial_no, drug_name, dosage_route_frequency, started_on, stopped_on) VALUES (?, ?, ?, ?, ?, ?)");
            $antibioticStmt->execute([
                $patientId,
                $i,
                $drugName,
                $formData["dosage_$i"] ?? null,
                $startedOn,
                $stoppedOn
            ]);
        }
    }

    // Handle post_operative_monitoring table - FIXED: Use correct field names
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM post_operative_monitoring WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

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
        
        error_log("Post-operative row $i - Date: '$monitoringDate', HasData: " . ($hasData ? 'Yes' : 'No') . ", Dosage: '" . ($formData["post-dosage_$i"] ?? 'NOT SET') . "', DateKey: '$dateKey'");
        
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
                $formData["Fever_$i"] ?? null // Fixed: Use capital F for Fever field
            ]);
        }
    }

    // Handle cultural_dressing table
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM cultural_dressing WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    $culturalStmt = $pdo->prepare("INSERT INTO cultural_dressing (patient_id, cultural_swap, dressing_finding) VALUES (?, ?, ?)");
    $culturalStmt->execute([
        $patientId,
        $formData['Cultural-Swap'] ?? null,
        $formData['Dressing-Finding'] ?? null
    ]);

    // Handle wound_complications table - FIXED: Add missing fields
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM wound_complications WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    $complicationDate = !empty($formData['Wound-Complication Date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData['Wound-Complication Date']))) : null;
    
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
        $formData['WoundD-Notes'] ?? null, // Add notes field
        isset($formData['SuperficialSSI']) ? 1 : 0,
        isset($formData['DeepSI']) ? 1 : 0,
        isset($formData['OrganSI']) ? 1 : 0,
        isset($formData['wtd1-1']) ? 1 : 0, // purulent_discharge_superficial
        isset($formData['wtd1-2']) ? 1 : 0, // purulent_discharge_deep
        isset($formData['wtd1-3']) ? 1 : 0, // purulent_discharge_organ
        isset($formData['wtd2-1']) ? 1 : 0, // organism_identified_superficial
        isset($formData['wtd2-2']) ? 1 : 0, // organism_identified_organ
        isset($formData['wtd3-1']) ? 1 : 0, // clinical_diagnosis_ssi
        isset($formData['wtd3-2']) ? 1 : 0, // deep_incision_reopening
        isset($formData['wtd3-3']) ? 1 : 0, // abscess_evidence_organ
        isset($formData['wtd4-1']) ? 1 : 0, // deliberate_opening_symptoms
        isset($formData['wtd4-2']) ? 1 : 0, // abscess_evidence_deep
        isset($formData['wtd4-3']) ? 1 : 0, // not_infected_conditions
        $formData['SurgeonOpinion1'] ?? null, // surgeon_opinion_superficial
        $formData['SurgeonOpinion2'] ?? null, // surgeon_opinion_deep
        $formData['SurgeonOpinion3'] ?? null // surgeon_opinion_organ
    ]);

    // Handle review_sutures table
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM review_sutures WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    $reviewOn = !empty($formData['ReviewOn']) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData['ReviewOn']))) : null;
    $suturesRemovedOn = !empty($formData['SuturesROn']) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData['SuturesROn']))) : null;

    $reviewStmt = $pdo->prepare("INSERT INTO review_sutures (patient_id, review_on, sutures_removed_on) VALUES (?, ?, ?)");
    $reviewStmt->execute([
        $patientId,
        $reviewOn,
        $suturesRemovedOn
    ]);

    // Handle review_phone table - FIXED: Add missing fields
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM review_phone WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    $phoneReviewDate = !empty($formData['RevieworPhoneDate']) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData['RevieworPhoneDate']))) : null;

    $phoneStmt = $pdo->prepare("INSERT INTO review_phone (patient_id, review_date, patient_identification, pain, pus, bleeding, other) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $phoneStmt->execute([
        $patientId,
        $phoneReviewDate,
        $formData['reviewp'] ?? null,
        $formData['reviewppain'] ?? null,
        $formData['reviewpus'] ?? null, // Fixed field name
        $formData['reviewbleed'] ?? null, // Fixed field name
        $formData['reviewother'] ?? null // Fixed field name
    ]);

    // Temporarily comment out session and audit logging for debugging
    /*
    // Get nurse information from session
    $nurseName = $_SESSION['nurse_info']['name'] ?? 'Unknown Nurse';
    $nurseId = $_SESSION['nurse_info']['nurse_id'] ?? 'Unknown';
    
    // Debug: Log session information
    error_log("Session data: " . print_r($_SESSION, true));
    error_log("Nurse ID: " . $nurseId . ", Nurse Name: " . $nurseName);
    
    // Log the audit event
    $auditLogger = new AuditLogger($pdo);
    $patientData = [
        'patient_id' => $patientId,
        'uhid' => $formData['uhid'],
        'name' => $formData['name'],
        'action_type' => $isUpdate ? 'UPDATE' : 'CREATE'
    ];
    
    // Debug: Log audit attempt
    error_log("Attempting to log audit - Nurse ID: {$nurseId}, Patient ID: {$patientId}, Action: " . ($isUpdate ? 'UPDATE' : 'CREATE'));
    
    if ($isUpdate) {
        $result = $auditLogger->logPatientUpdate($nurseId, $patientId, $formData['name'], $patientData);
        error_log("Patient update audit log result: " . ($result ? 'SUCCESS' : 'FAILED'));
    } else {
        $result = $auditLogger->logPatientCreate($nurseId, $patientId, $formData['name'], $patientData);
        error_log("Patient create audit log result: " . ($result ? 'SUCCESS' : 'FAILED'));
    }
    */

    $pdo->commit();
    
    $message = $isUpdate ? 'Patient data updated successfully!' : 'Patient data saved successfully!';
    echo json_encode(['success' => true, 'message' => $message]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
}

ob_end_flush();
?>
