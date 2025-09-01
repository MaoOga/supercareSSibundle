<?php
// Form submission with audit logging for nurse activities
header('Content-Type: application/json');

require_once '../database/config.php';
require_once '../auth/session_config.php';
require_once '../audit/audit_logger.php';

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
    
    // Get nurse information from session for audit logging
    $nurseId = $_SESSION['nurse_id'] ?? null;
    $nurseInfo = $_SESSION['nurse_info'] ?? null;
    $nurseName = $nurseInfo['name'] ?? 'Unknown Nurse';
    $nurseIdCode = $nurseInfo['nurse_id'] ?? 'UNKNOWN';
    
    // Debug logging
    error_log("Form submission with audit - Patient ID: $patientId, Is Update: " . ($isUpdate ? 'Yes' : 'No'));
    error_log("Nurse ID: $nurseId, Nurse Name: $nurseName");
    
    // Check if UHID already exists (only for new records)
    if (!$isUpdate && !empty($formData['uhid'])) {
        $uhidCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE uhid = ?");
        $uhidCheckStmt->execute([$formData['uhid']]);
        $uhidExists = $uhidCheckStmt->fetchColumn() > 0;
        
        if ($uhidExists) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'UHID already exists. Please use a unique UHID.']);
            exit;
        }
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

    // Handle drains table
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM drains WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    for ($i = 1; $i <= 10; $i++) {
        $drainType = $formData["drain-type_$i"] ?? null;
        $drainDescription = $formData["drain-description_$i"] ?? null;
        
        if ($drainType || $drainDescription) {
            $drainStmt = $pdo->prepare("INSERT INTO drains (patient_id, drain_type, description) VALUES (?, ?, ?)");
            $drainStmt->execute([$patientId, $drainType, $drainDescription]);
        }
    }

    // Handle antibiotic_usage table
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM antibiotic_usage WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    for ($i = 1; $i <= 10; $i++) {
        $antibioticName = $formData["antibiotic-name_$i"] ?? null;
        $startedOn = $formData["started-on_$i"] ?? null;
        $stoppedOn = $formData["stopped-on_$i"] ?? null;
        
        if ($antibioticName) {
            $startedDate = !empty($startedOn) ? date('Y-m-d', strtotime(str_replace('/', '-', $startedOn))) : null;
            $stoppedDate = !empty($stoppedOn) ? date('Y-m-d', strtotime(str_replace('/', '-', $stoppedOn))) : null;
            
            $antibioticStmt = $pdo->prepare("INSERT INTO antibiotic_usage (patient_id, antibiotic_name, started_on, stopped_on) VALUES (?, ?, ?, ?)");
            $antibioticStmt->execute([$patientId, $antibioticName, $startedDate, $stoppedDate]);
        }
    }

    // Handle post_operative_monitoring table
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM post_operative_monitoring WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    for ($i = 1; $i <= 10; $i++) {
        $monitoringDate = $formData["monitoring-date_$i"] ?? null;
        $postDosage = $formData["post-dosage_$i"] ?? null;
        $typeOfDischarge = $formData["type-ofdischarge_$i"] ?? null;
        $tendernessPain = $formData["tenderness-pain_$i"] ?? null;
        $swelling = $formData["swelling_$i"] ?? null;
        $fever = $formData["Fever_$i"] ?? null;
        
        // Check if we have any data for this row
        $hasData = $monitoringDate || $postDosage || $typeOfDischarge || $tendernessPain || $swelling || $fever;
        
        if ($hasData) {
            $monitoringDateParsed = !empty($monitoringDate) ? date('Y-m-d', strtotime(str_replace('/', '-', $monitoringDate))) : null;
            
            $postOpStmt = $pdo->prepare("INSERT INTO post_operative_monitoring (patient_id, monitoring_date, post_dosage, type_of_discharge, tenderness_pain, swelling, fever) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $postOpStmt->execute([
                $patientId,
                $monitoringDateParsed,
                $postDosage,
                $typeOfDischarge,
                $tendernessPain,
                $swelling,
                $fever
            ]);
        }
    }

    // Handle wound_complications table
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM wound_complications WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    $complicationDate = !empty($formData['Wound-Complication_Date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData['Wound-Complication_Date']))) : null;

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

    // Handle review_phone table
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM review_phone WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    $reviewDate = !empty($formData['review-date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData['review-date']))) : null;

    $reviewStmt = $pdo->prepare("INSERT INTO review_phone (patient_id, review_date, pus, bleeding, others) VALUES (?, ?, ?, ?, ?)");
    $reviewStmt->execute([
        $patientId,
        $reviewDate,
        isset($formData['pus']) ? 1 : 0,
        isset($formData['bleeding']) ? 1 : 0,
        isset($formData['others']) ? 1 : 0
    ]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => $isUpdate ? 'Patient record updated successfully!' : 'Patient record created successfully!',
        'patient_id' => $patientId
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Form submission error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while saving the data: ' . $e->getMessage()
    ]);
}
?>
