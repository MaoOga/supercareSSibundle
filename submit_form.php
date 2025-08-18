<?php
// Start output buffering to capture any potential output
ob_start();

// Suppress error reporting to prevent HTML error messages from corrupting JSON output
error_reporting(0);
ini_set('display_errors', 0);

require_once 'config.php';
require_once 'session_config.php';
require_once 'audit_logger.php';

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

    // Handle drains table
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM drains WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    // Insert drains if used
    if (isset($formData['drain-used']) && $formData['drain-used'] === 'Yes') {
        for ($i = 1; $i <= 3; $i++) {
            if (!empty($formData["drain_$i"])) {
                $drainStmt = $pdo->prepare("INSERT INTO drains (patient_id, drain_used, drain_description, drain_number) VALUES (?, ?, ?, ?)");
                $drainStmt->execute([
                    $patientId,
                    'Yes',
                    $formData["drain_$i"],
                    $i
                ]);
            }
        }
    }

    // Handle antibiotic_usage table
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM antibiotic_usage WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    for ($i = 1; $i <= 3; $i++) {
        if (!empty($formData["drug-name_$i"])) {
            $startedOn = !empty($formData["antibiotic_usage"]["startedon_$i"]) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData["antibiotic_usage"]["startedon_$i"]))) : null;
            $stoppedOn = !empty($formData["antibiotic_usage"]["stoppeon_$i"]) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData["antibiotic_usage"]["stoppeon_$i"]))) : null;
            
            $antibioticStmt = $pdo->prepare("INSERT INTO antibiotic_usage (patient_id, serial_no, drug_name, dosage_route_frequency, started_on, stopped_on) VALUES (?, ?, ?, ?, ?, ?)");
            $antibioticStmt->execute([
                $patientId,
                $i,
                $formData["drug-name_$i"],
                $formData["dosage_$i"],
                $startedOn,
                $stoppedOn
            ]);
        }
    }

    // Handle post_operative_monitoring table
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM post_operative_monitoring WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    for ($i = 1; $i <= 3; $i++) {
        if (!empty($formData["post-operative"]["date_$i"])) {
            $monitoringDate = date('Y-m-d', strtotime(str_replace('/', '-', $formData["post-operative"]["date_$i"])));
            
            $postOpStmt = $pdo->prepare("INSERT INTO post_operative_monitoring (patient_id, day, monitoring_date, dosage, discharge_fluid, tenderness_pain, swelling, fever) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $postOpStmt->execute([
                $patientId,
                $i,
                $monitoringDate,
                $formData["post-dosage_$i"],
                $formData["type-ofdischarge_$i"],
                $formData["tenderness-pain_$i"],
                $formData["swelling_$i"],
                $formData["Fever_$i"]
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

    // Handle wound_complications table
    if ($isUpdate) {
        $deleteStmt = $pdo->prepare("DELETE FROM wound_complications WHERE patient_id = ?");
        $deleteStmt->execute([$patientId]);
    }

    $complicationDate = !empty($formData['Wound-Complication Date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $formData['Wound-Complication Date']))) : null;
    
    $woundStmt = $pdo->prepare("INSERT INTO wound_complications (patient_id, complication_date, wound_dehiscence, allergic_reaction, bleeding_haemorrhage, other_complication, other_specify, superficial_ssi, deep_si, organ_space_ssi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $woundStmt->execute([
        $patientId,
        $complicationDate,
        isset($formData['wounddehiscene']) ? 1 : 0,
        isset($formData['AllergicR']) ? 1 : 0,
        isset($formData['BleedingH']) ? 1 : 0,
        isset($formData['Other']) ? 1 : 0,
        $formData['WoundD-Specify'] ?? null,
        isset($formData['SuperficialSSI']) ? 1 : 0,
        isset($formData['DeepSI']) ? 1 : 0,
        isset($formData['OrganSI']) ? 1 : 0
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

    $phoneStmt = $pdo->prepare("INSERT INTO review_phone (patient_id, review_date, patient_identification, pain) VALUES (?, ?, ?, ?)");
    $phoneStmt->execute([
        $patientId,
        $phoneReviewDate,
        $formData['reviewp'] ?? null,
        $formData['reviewppain'] ?? null
    ]);

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

    $pdo->commit();
    
    $message = $isUpdate ? 'Patient data updated successfully!' : 'Patient data saved successfully!';
    echo json_encode(['success' => true, 'message' => $message]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
}

ob_end_flush();
?>
