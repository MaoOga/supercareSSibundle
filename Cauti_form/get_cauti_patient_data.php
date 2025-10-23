<?php
// Prevent any output before JSON
ob_start();

// Disable error reporting to prevent HTML errors
error_reporting(0);
ini_set('display_errors', 0);

// Clear any existing output
ob_clean();

// Set JSON header
header('Content-Type: application/json');

try {
    // Include config
    require_once '../database/config.php';
    
    // Check if PDO connection is available
    if (!$pdo) {
        echo json_encode(['success' => false, 'message' => 'Database connection not available']);
        exit;
    }
    
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    // Get patient ID
    $patient_id = $_GET['patient_id'] ?? '';
    if (empty($patient_id)) {
        echo json_encode(['success' => false, 'message' => 'Patient ID is required']);
        exit;
    }
    
    $patient_id = intval($patient_id);
    if ($patient_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid patient ID']);
        exit;
    }

    // Fetch patient information
    $stmt = $pdo->prepare("SELECT * FROM cauti_patient_info WHERE id = ?");
    $stmt->execute([$patient_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        throw new Exception('Patient not found.');
    }

    // Format date for display (yyyy-mm-dd to dd/mm/yyyy)
    if (!empty($patient['date_of_admission'])) {
        $date_obj = new DateTime($patient['date_of_admission']);
        $patient['date_of_admission'] = $date_obj->format('d/m/Y');
    }

    // Fetch catheter records for this patient
    $stmt = $pdo->prepare("SELECT * FROM cauti_catheter WHERE patient_id = ? ORDER BY id ASC");
    $stmt->execute([$patient_id]);
    $catheter_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format catheter data for display
    foreach ($catheter_records as &$record) {
        // Format dates (yyyy-mm-dd to dd/mm/yyyy)
        if (!empty($record['catheter_date'])) {
            $date_obj = new DateTime($record['catheter_date']);
            $record['catheter_date'] = $date_obj->format('d/m/Y');
        }
        if (!empty($record['catheter_changed_on'])) {
            $date_obj = new DateTime($record['catheter_changed_on']);
            $record['catheter_changed_on'] = $date_obj->format('d/m/Y');
        }
        if (!empty($record['catheter_removed_on'])) {
            $date_obj = new DateTime($record['catheter_removed_on']);
            $record['catheter_removed_on'] = $date_obj->format('d/m/Y');
        }
        if (!empty($record['catheter_out_date'])) {
            $date_obj = new DateTime($record['catheter_out_date']);
            $record['catheter_out_date'] = $date_obj->format('d/m/Y');
        }

        // Convert time from 24-hour to 12-hour format with AM/PM
        if (!empty($record['catheter_time'])) {
            $time_obj = new DateTime($record['catheter_time']);
            $hour = intval($time_obj->format('H'));
            $minute = $time_obj->format('i');
            
            $meridiem = ($hour >= 12) ? 'PM' : 'AM';
            $hour_12 = ($hour > 12) ? ($hour - 12) : (($hour == 0) ? 12 : $hour);
            
            $record['catheter_hour'] = $hour_12;
            $record['catheter_minute'] = $minute;
            $record['catheter_meridiem'] = $meridiem;
        }

        if (!empty($record['catheter_out_time'])) {
            $time_obj = new DateTime($record['catheter_out_time']);
            $hour = intval($time_obj->format('H'));
            $minute = $time_obj->format('i');
            
            $meridiem = ($hour >= 12) ? 'PM' : 'AM';
            $hour_12 = ($hour > 12) ? ($hour - 12) : (($hour == 0) ? 12 : $hour);
            
            $record['catheter_out_hour'] = $hour_12;
            $record['catheter_out_minute'] = $minute;
            $record['catheter_out_meridiem'] = $meridiem;
        }
    }
    unset($record); // Break the reference

    // Fetch problem records for this patient
    $stmt = $pdo->prepare("SELECT * FROM cauti_problem WHERE patient_id = ? ORDER BY id ASC");
    $stmt->execute([$patient_id]);
    $problem_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format problem data for display
    foreach ($problem_records as &$record) {
        // Format dates (yyyy-mm-dd to dd/mm/yyyy)
        if (!empty($record['problem_date'])) {
            $date_obj = new DateTime($record['problem_date']);
            $record['problem_date'] = $date_obj->format('d/m/Y');
        }

        // Convert time from 24-hour to 12-hour format with AM/PM
        if (!empty($record['problem_time'])) {
            $time_obj = new DateTime($record['problem_time']);
            $hour = intval($time_obj->format('H'));
            $minute = $time_obj->format('i');
            
            $meridiem = ($hour >= 12) ? 'PM' : 'AM';
            $hour_12 = ($hour > 12) ? ($hour - 12) : (($hour == 0) ? 12 : $hour);
            
            $record['problem_hour'] = $hour_12;
            $record['problem_minute'] = $minute;
            $record['problem_meridiem'] = $meridiem;
        }
    }
    unset($record); // Break the reference

    // Fetch urine culture records for this patient
    $stmt = $pdo->prepare("SELECT * FROM cauti_urine_culture WHERE patient_id = ? ORDER BY id ASC");
    $stmt->execute([$patient_id]);
    $urine_culture_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format urine culture data for display
    foreach ($urine_culture_records as &$record) {
        // Format dates (yyyy-mm-dd to dd/mm/yyyy)
        if (!empty($record['sending_date'])) {
            $date_obj = new DateTime($record['sending_date']);
            $record['sending_date'] = $date_obj->format('d/m/Y');
        }
        if (!empty($record['reporting_date'])) {
            $date_obj = new DateTime($record['reporting_date']);
            $record['reporting_date'] = $date_obj->format('d/m/Y');
        }
    }
    unset($record); // Break the reference

    // Fetch urine re pus records for this patient
    $stmt = $pdo->prepare("SELECT * FROM cauti_urine_re_pus WHERE patient_id = ? ORDER BY id ASC");
    $stmt->execute([$patient_id]);
    $urine_re_pus_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format urine re pus data for display
    foreach ($urine_re_pus_records as &$record) {
        // Format dates (yyyy-mm-dd to dd/mm/yyyy)
        if (!empty($record['test_date'])) {
            $date_obj = new DateTime($record['test_date']);
            $record['test_date'] = $date_obj->format('d/m/Y');
        }
        
        // Format time (24-hour to 12-hour with AM/PM)
        if (!empty($record['test_time'])) {
            $time_obj = new DateTime($record['test_time']);
            $hour_24 = (int)$time_obj->format('H');
            $minute = $time_obj->format('i');
            
            // Convert 24-hour to 12-hour
            $meridiem = ($hour_24 >= 12) ? 'PM' : 'AM';
            $hour_12 = ($hour_24 % 12);
            if ($hour_12 == 0) $hour_12 = 12;
            
            $record['test_hour'] = $hour_12;
            $record['test_minute'] = $minute;
            $record['test_meridiem'] = $meridiem;
        }
    }
    unset($record); // Break the reference

    // Prepare response data
    $responseData = [
        'patient' => $patient,
        'catheter_records' => $catheter_records,
        'problem_records' => $problem_records,
        'urine_culture_records' => $urine_culture_records,
        'urine_re_pus_records' => $urine_re_pus_records
    ];

    // Return success response
    echo json_encode([
        'success' => true,
        'data' => $responseData
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// End output buffering and flush
ob_end_flush();
?>

