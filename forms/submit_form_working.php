<?php
// Working version with session and audit logging
header('Content-Type: application/json');

require_once '../database/config.php';
// Session management removed - no authentication required
require_once '../audit/audit_logger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Session management removed - no authentication required

    $pdo->beginTransaction();
    
    $formData = $_POST;
    $patientId = $formData['patient_id'] ?? null;
    $isUpdate = !empty($patientId);
    
    // Session management removed - using default nurse info for audit logging
    $nurseInfo = ['nurse_id' => 'SYSTEM', 'name' => 'System User'];
    $nurseIdCode = $nurseInfo['nurse_id'] ?? 'UNKNOWN';
    $nurseName = $nurseInfo['name'] ?? 'Unknown Nurse';
    
    // Debug logging
    error_log("Working form submission - Patient ID: $patientId, Is Update: " . ($isUpdate ? 'Yes' : 'No'));
    error_log("Nurse ID: $nurseIdCode, Nurse Name: $nurseName");
    error_log("Form data keys: " . implode(', ', array_keys($formData)));
    
    // Debug antibiotic form data specifically (can be removed in production)
    // $antibioticKeys = [];
    // foreach (array_keys($formData) as $key) {
    //     if (strpos($key, 'started-on') !== false || strpos($key, 'stopped-on') !== false || strpos($key, 'drug-name') !== false) {
    //         $antibioticKeys[] = $key . '=' . $formData[$key];
    //     }
    // }
    // error_log("Antibiotic form data: " . implode(', ', $antibioticKeys));
    
    // Check for post-operative related keys specifically
    $postOpKeys = [];
    foreach (array_keys($formData) as $key) {
        if (strpos($key, 'post-operative') !== false || strpos($key, 'post-dosage') !== false || strpos($key, 'type-ofdischarge') !== false || strpos($key, 'tenderness-pain') !== false || strpos($key, 'swelling') !== false || strpos($key, 'Fever') !== false) {
            $postOpKeys[] = $key;
        }
    }
    error_log("Post-operative related keys: " . implode(', ', $postOpKeys));
    
    // ===== SERVER-SIDE UHID DUPLICATE CHECK (checks both SSI and CAUTI tables) =====
    if ($isUpdate) {
        // For updates, check if UHID exists for a different patient in patients table
        $uhidCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE uhid = ? AND patient_id != ?");
        $uhidCheckStmt->execute([$formData['uhid'], $patientId]);
        $ssiCount = $uhidCheckStmt->fetchColumn();
    } else {
        // For new records, check if UHID already exists in patients table
        $uhidCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE uhid = ?");
        $uhidCheckStmt->execute([$formData['uhid']]);
        $ssiCount = $uhidCheckStmt->fetchColumn();
    }
    
    // Also check if UHID exists in cauti_patient_info table (CAUTI system)
    $cautiCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM cauti_patient_info WHERE uhid = ?");
    $cautiCheckStmt->execute([$formData['uhid']]);
    $cautiCount = $cautiCheckStmt->fetchColumn();
    
    // If UHID exists in either table, reject the submission
    if ($ssiCount > 0 || $cautiCount > 0) {
        $location = [];
        if ($ssiCount > 0) $location[] = 'SSI Bundle';
        if ($cautiCount > 0) $location[] = 'CAUTI';
        $locationText = implode(' and ', $location);
        
        $pdo->rollBack();
        echo json_encode([
            'success' => false, 
            'message' => "⚠️ UHID already exists in {$locationText} system! Please use a unique UHID."
        ]);
        exit;
    }
    // ===== END UHID DUPLICATE CHECK =====

    // Handle patients table
    if ($isUpdate) {
        // Convert date_completed from DD/MM/YYYY to YYYY-MM-DD format
        $dateCompleted = null;
        if (!empty($formData['date_completed'])) {
            $dateCompleted = date('Y-m-d', strtotime(str_replace('/', '-', $formData['date_completed'])));
        }
        
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
            $dateCompleted,
            $patientId
        ]);
        
        // Log the update
        $auditLogger = new AuditLogger($pdo);
        $auditLogger->log(
            $nurseIdCode, // Use nurse ID as admin_user
            'UPDATE',
            'PATIENT',
            $patientId,
            $formData['name'],
            "Updated patient record: {$formData['name']} (UHID: {$formData['uhid']})",
            null,
            $formData
        );
    } else {
        // Convert date_completed from DD/MM/YYYY to YYYY-MM-DD format
        $dateCompleted = null;
        if (!empty($formData['date_completed'])) {
            $dateCompleted = date('Y-m-d', strtotime(str_replace('/', '-', $formData['date_completed'])));
        }
        
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
            $dateCompleted
        ]);
        $patientId = $pdo->lastInsertId();
        
        // Log the creation
        $auditLogger = new AuditLogger($pdo);
        $auditLogger->log(
            $nurseIdCode, // Use nurse ID as admin_user
            'CREATE',
            'PATIENT',
            $patientId,
            $formData['name'],
            "Created new patient record: {$formData['name']} (UHID: {$formData['uhid']})",
            null,
            $formData
        );
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

    // Handle risk_factors table
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM risk_factors WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    $riskStmt = $pdo->prepare("INSERT INTO risk_factors (patient_id, weight, height, sga, steroids, tuberculosis, others) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $riskStmt->execute([
        $patientId,
        $formData['risk_factor_weight'] ?? null,
        $formData['risk_factor_height'] ?? null,
        $formData['risk_factor_sga'] ?? null,
        $formData['risk_factor_steroids'] ?? null,
        $formData['risk_factor_tuberculosis'] ?? null,
        $formData['risk_factor_others'] ?? null
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

    // Handle drains table
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM drains WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    // Insert drains - handle both 'Yes' and 'No' cases properly
    $drainUsed = isset($formData['drain-used']) ? $formData['drain-used'] : null;
    error_log("Drain used: " . ($drainUsed ?? 'NOT SELECTED'));
    
    if ($drainUsed === 'Yes') {
        // Insert drain records only if 'Yes' is selected
        for ($i = 1; $i <= 40; $i++) {
            $drainDescription = $formData["drain_$i"] ?? null;
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
    } elseif ($drainUsed === 'No') {
        // If 'No' is explicitly selected, insert a record indicating 'No'
        error_log("Drain used is 'No', inserting record to indicate no drains used");
        $drainStmt = $pdo->prepare("INSERT INTO drains (patient_id, drain_used, drain_description, drain_number) VALUES (?, ?, ?, ?)");
        $drainStmt->execute([
            $patientId,
            'No',
            'No drains used',
            1
        ]);
    } else {
        // If no radio button was selected, don't insert any drain records
        error_log("No drain-used radio button selected, not inserting any drain records");
    }

    // Handle antibiotic_usage table
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM antibiotic_usage WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    for ($i = 1; $i <= 40; $i++) {
        $drugName = $formData["drug-name_$i"] ?? null;
        // Debug: error_log("Checking antibiotic row $i: drug-name='$drugName'");
        if (!empty($drugName)) {
            // Get started_on and stopped_on exactly like drug_name and dosage (simple field access)
            $startedOn = $formData["started-on_$i"] ?? null;
            $stoppedOn = $formData["stopped-on_$i"] ?? null;
            
            // Debug the exact field names being accessed (can be removed in production)
            // error_log("Antibiotic $i - Field access debug:");
            // error_log("  Looking for: started-on_$i");
            // error_log("  Found value: " . ($startedOn ?? 'NULL'));
            // error_log("  Looking for: stopped-on_$i");
            // error_log("  Found value: " . ($stoppedOn ?? 'NULL'));
            
            // No date conversion needed - columns are now TEXT and accept any value
            
            error_log("Inserting antibiotic $i: $drugName, Started: '$startedOn', Stopped: '$stoppedOn'");
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

    // Handle post_operative_monitoring table - WORKING VERSION
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM post_operative_monitoring WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    for ($i = 1; $i <= 40; $i++) {
        // Get monitoring date using simple field name
        $monitoringDate = $formData["postop-date_$i"] ?? null;
        
        // Check if there's actual data in this row
        $hasData = !empty($monitoringDate) ||
                   !empty($formData["post-dosage_$i"]) || 
                   !empty($formData["type-ofdischarge_$i"]) || 
                   !empty($formData["tenderness-pain_$i"]) || 
                   !empty($formData["swelling_$i"]) || 
                   !empty($formData["Fever_$i"]);
        
        error_log("Post-operative row $i - Date: '$monitoringDate', HasData: " . ($hasData ? 'Yes' : 'No') . ", Dosage: '" . ($formData["post-dosage_$i"] ?? 'NOT SET') . "'");
        
        if ($hasData) {
            // No date conversion needed - column is now TEXT and accepts any value
            
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

    // Handle wound_complications table
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM wound_complications WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    // Debug logging for wound complication date
    error_log("WOUND COMPLICATION DATE DEBUG - Raw value: '" . ($formData['Wound-Complication_Date'] ?? 'NOT SET') . "'");
    error_log("WOUND COMPLICATION DATE DEBUG - Empty check: " . (empty($formData['Wound-Complication_Date']) ? 'YES' : 'NO'));
    
    $complicationDate = !empty($formData['Wound-Complication_Date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData['Wound-Complication_Date']))) : null;
    
    error_log("WOUND COMPLICATION DATE DEBUG - Final parsed date: '" . ($complicationDate ?? 'NULL') . "'");
    
    $woundStmt = $pdo->prepare("INSERT INTO wound_complications (
        patient_id, complication_date, wound_dehiscence, allergic_reaction, bleeding_haemorrhage, 
        other_complication, other_specify, notes, superficial_ssi, deep_si, organ_space_ssi,
        purulent_discharge_superficial, purulent_discharge_deep, purulent_discharge_organ,
        organism_identified_superficial, organism_identified_deep, organism_identified_organ, clinical_diagnosis_ssi,
        deep_incision_reopening, abscess_evidence_organ, deliberate_opening_symptoms,
        abscess_evidence_deep, not_infected_conditions, surgeon_opinion_superficial,
        surgeon_opinion_deep, surgeon_opinion_organ
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
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
        isset($formData['wtd2-1b']) ? 1 : 0,
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

    // Handle review_phone table
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
        $formData['reviewpus'] ?? null,
        $formData['reviewbleed'] ?? null,
        $formData['reviewother'] ?? null
    ]);

    // Handle infection prevention notes and signature
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM infection_prevention_notes WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    $infectionStmt = $pdo->prepare("INSERT INTO infection_prevention_notes (patient_id, infection_prevention_notes, signature) VALUES (?, ?, ?)");
    $infectionStmt->execute([
        $patientId,
        $formData['infection_prevention_notes'] ?? null,
        $formData['signature'] ?? null
    ]);

    // Update signature in patients table
    $updateSignatureStmt = $pdo->prepare("UPDATE patients SET signature = ? WHERE patient_id = ?");
    $updateSignatureStmt->execute([
        $formData['signature'] ?? null,
        $patientId
    ]);

    $pdo->commit();
    
    $message = $isUpdate ? 'Patient data updated successfully!' : 'Patient data saved successfully!';
    echo json_encode(['success' => true, 'message' => $message]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
?>
