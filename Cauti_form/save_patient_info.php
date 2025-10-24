<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session for messages
session_start();

// Include database configuration
require_once '../database/config.php';

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request method. Only POST is allowed.';
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Only POST is allowed.'
    ]);
    exit;
}

try {
    // Check if PDO connection is available
    if (!$pdo) {
        throw new Exception('Database connection not available.');
    }

    // Check if this is an update (editing existing record)
    $patient_id = isset($_POST['patient_id']) && $_POST['patient_id'] !== '' ? intval($_POST['patient_id']) : 0;
    $is_update = ($patient_id > 0);
    
    // Get POST data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $age = isset($_POST['age']) && $_POST['age'] !== '' ? intval($_POST['age']) : null;
    $sex = isset($_POST['sex']) ? trim($_POST['sex']) : '';
    $uhid = isset($_POST['uhid']) ? trim($_POST['uhid']) : '';
    $bed_ward = isset($_POST['bed']) ? trim($_POST['bed']) : '';
    $date_of_admission = isset($_POST['date_of_admission']) ? trim($_POST['date_of_admission']) : '';
    $diagnosis = isset($_POST['diagnosis']) ? trim($_POST['diagnosis']) : '';
    $nurse_notes = isset($_POST['nurse_notes']) ? trim($_POST['nurse_notes']) : '';

    // Validate required fields
    if (empty($name)) {
        $_SESSION['error'] = 'Patient name is required.';
        echo json_encode([
            'success' => false,
            'message' => 'Patient name is required.'
        ]);
        exit;
    }
    
    if (empty($uhid)) {
        $_SESSION['error'] = 'Patient UHID is required.';
        echo json_encode([
            'success' => false,
            'message' => 'Patient UHID is required.'
        ]);
        exit;
    }

    // ===== SERVER-SIDE UHID DUPLICATE CHECK =====
    // Check if UHID already exists in patients table (SSI Bundle)
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE uhid = ?");
    $checkStmt->execute([$uhid]);
    $ssiCount = $checkStmt->fetchColumn();
    
    // Check if UHID already exists in cauti_patient_info table (CAUTI)
    // Exclude current patient if updating
    if ($is_update) {
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM cauti_patient_info WHERE uhid = ? AND id != ?");
        $checkStmt->execute([$uhid, $patient_id]);
    } else {
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM cauti_patient_info WHERE uhid = ?");
        $checkStmt->execute([$uhid]);
    }
    $cautiCount = $checkStmt->fetchColumn();
    
    // If UHID exists in either table, reject the submission
    if ($ssiCount > 0 || $cautiCount > 0) {
        $location = [];
        if ($ssiCount > 0) $location[] = 'SSI Bundle';
        if ($cautiCount > 0) $location[] = 'CAUTI';
        $locationText = implode(' and ', $location);
        
        $_SESSION['error'] = "UHID already exists in {$locationText} system.";
        echo json_encode([
            'success' => false,
            'message' => "⚠️ UHID already exists in {$locationText} system! Please use a unique UHID."
        ]);
        exit;
    }
    // ===== END UHID DUPLICATE CHECK =====

    // Convert date format if needed (dd/mm/yyyy to yyyy-mm-dd)
    if (!empty($date_of_admission)) {
        // Check if date is in dd/mm/yyyy format
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date_of_admission, $matches)) {
            $date_of_admission = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }
        
        // Validate date format
        $date_parts = explode('-', $date_of_admission);
        if (count($date_parts) === 3) {
            if (!checkdate($date_parts[1], $date_parts[2], $date_parts[0])) {
                $date_of_admission = null;
            }
        } else {
            $date_of_admission = null;
        }
    } else {
        $date_of_admission = null;
    }

    // Prepare SQL statement (INSERT or UPDATE)
    if ($is_update) {
        // UPDATE existing record
        $sql = "UPDATE cauti_patient_info 
                SET name = :name, age = :age, sex = :sex, uhid = :uhid, 
                    bed_ward = :bed_ward, date_of_admission = :date_of_admission, 
                    diagnosis = :diagnosis, nurse_notes = :nurse_notes 
                WHERE id = :patient_id";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':name' => $name,
            ':age' => $age,
            ':sex' => $sex,
            ':uhid' => $uhid,
            ':bed_ward' => $bed_ward,
            ':date_of_admission' => $date_of_admission,
            ':diagnosis' => $diagnosis,
            ':nurse_notes' => $nurse_notes,
            ':patient_id' => $patient_id
        ]);
        
        $inserted_id = $patient_id; // Use existing ID for update
    } else {
        // INSERT new record
    $sql = "INSERT INTO cauti_patient_info 
            (name, age, sex, uhid, bed_ward, date_of_admission, diagnosis, nurse_notes) 
            VALUES 
                (:name, :age, :sex, :uhid, :bed_ward, :date_of_admission, :diagnosis, :nurse_notes)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':name' => $name,
            ':age' => $age,
            ':sex' => $sex,
            ':uhid' => $uhid,
            ':bed_ward' => $bed_ward,
            ':date_of_admission' => $date_of_admission,
            ':diagnosis' => $diagnosis,
            ':nurse_notes' => $nurse_notes
        ]);
        
        $inserted_id = $pdo->lastInsertId();
    }

    if ($result) {
        
        // If updating, delete existing catheter records first
        if ($is_update) {
            $deleteStmt = $pdo->prepare("DELETE FROM cauti_catheter WHERE patient_id = ?");
            $deleteStmt->execute([$inserted_id]);
        }
        
        // Now insert catheter data (multiple rows)
        $catheterInsertCount = 0;
        $catheterStmt = $pdo->prepare("
            INSERT INTO cauti_catheter 
            (patient_id, catheter_date, catheter_time, catheter_changed_on, catheter_removed_on, catheter_out_date, catheter_out_time, total_catheter_days) 
            VALUES 
            (:patient_id, :catheter_date, :catheter_time, :catheter_changed_on, :catheter_removed_on, :catheter_out_date, :catheter_out_time, :total_catheter_days)
        ");
        
        // Loop through up to 10 catheter rows (adjust as needed)
        for ($i = 1; $i <= 10; $i++) {
            $catheter_date = isset($_POST["catheter_date_$i"]) ? trim($_POST["catheter_date_$i"]) : '';
            
            // Skip if this row is empty (no date means empty row)
            if (empty($catheter_date)) {
                continue;
            }
            
            // Get catheter time components
            $catheter_hour = isset($_POST["catheter_hour_$i"]) ? trim($_POST["catheter_hour_$i"]) : '';
            $catheter_minute = isset($_POST["catheter_minute_$i"]) ? trim($_POST["catheter_minute_$i"]) : '';
            $catheter_meridiem = isset($_POST["catheter_meridiem_$i"]) ? trim($_POST["catheter_meridiem_$i"]) : 'AM';
            
            // Convert 12-hour time to 24-hour format
            $catheter_time = null;
            if (!empty($catheter_hour) && !empty($catheter_minute)) {
                $hour_24 = intval($catheter_hour);
                if ($catheter_meridiem === 'PM' && $hour_24 != 12) {
                    $hour_24 += 12;
                } elseif ($catheter_meridiem === 'AM' && $hour_24 == 12) {
                    $hour_24 = 0;
                }
                $catheter_time = sprintf('%02d:%02d:00', $hour_24, intval($catheter_minute));
            }
            
            // Convert dates from dd/mm/yyyy to yyyy-mm-dd
            $catheter_date_formatted = null;
            if (!empty($catheter_date) && preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $catheter_date, $matches)) {
                $catheter_date_formatted = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            }
            
            $catheter_changed_on = isset($_POST["catheter_changed_on_$i"]) ? trim($_POST["catheter_changed_on_$i"]) : '';
            $catheter_changed_on_formatted = null;
            if (!empty($catheter_changed_on) && preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $catheter_changed_on, $matches)) {
                $catheter_changed_on_formatted = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            }
            
            $catheter_removed_on = isset($_POST["catheter_removed_on_$i"]) ? trim($_POST["catheter_removed_on_$i"]) : '';
            $catheter_removed_on_formatted = null;
            if (!empty($catheter_removed_on) && preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $catheter_removed_on, $matches)) {
                $catheter_removed_on_formatted = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            }
            
            // Get catheter out date and time
            $catheter_out_date = isset($_POST["catheter_out_date_$i"]) ? trim($_POST["catheter_out_date_$i"]) : '';
            $catheter_out_date_formatted = null;
            if (!empty($catheter_out_date) && preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $catheter_out_date, $matches)) {
                $catheter_out_date_formatted = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            }
            
            $catheter_out_hour = isset($_POST["catheter_out_hour_$i"]) ? trim($_POST["catheter_out_hour_$i"]) : '';
            $catheter_out_minute = isset($_POST["catheter_out_minute_$i"]) ? trim($_POST["catheter_out_minute_$i"]) : '';
            $catheter_out_meridiem = isset($_POST["catheter_out_meridiem_$i"]) ? trim($_POST["catheter_out_meridiem_$i"]) : 'AM';
            
            $catheter_out_time = null;
            if (!empty($catheter_out_hour) && !empty($catheter_out_minute)) {
                $hour_24 = intval($catheter_out_hour);
                if ($catheter_out_meridiem === 'PM' && $hour_24 != 12) {
                    $hour_24 += 12;
                } elseif ($catheter_out_meridiem === 'AM' && $hour_24 == 12) {
                    $hour_24 = 0;
                }
                $catheter_out_time = sprintf('%02d:%02d:00', $hour_24, intval($catheter_out_minute));
            }
            
            $total_catheter_days = isset($_POST["total_catheter_days_$i"]) ? trim($_POST["total_catheter_days_$i"]) : '';
            
            // Insert catheter record
            $catheterStmt->execute([
                ':patient_id' => $inserted_id,
                ':catheter_date' => $catheter_date_formatted,
                ':catheter_time' => $catheter_time,
                ':catheter_changed_on' => $catheter_changed_on_formatted,
                ':catheter_removed_on' => $catheter_removed_on_formatted,
                ':catheter_out_date' => $catheter_out_date_formatted,
                ':catheter_out_time' => $catheter_out_time,
                ':total_catheter_days' => $total_catheter_days
            ]);
            
            $catheterInsertCount++;
        }
        
        // If updating, delete existing problem records first
        if ($is_update) {
            $deleteStmt = $pdo->prepare("DELETE FROM cauti_problem WHERE patient_id = ?");
            $deleteStmt->execute([$inserted_id]);
        }
        
        // Now insert problem data (multiple rows)
        $problemInsertCount = 0;
        $problemStmt = $pdo->prepare("
            INSERT INTO cauti_problem 
            (patient_id, problem_date, problem_time, types_of_symptoms, pain_burning_sensation, fever_temperature) 
            VALUES 
            (:patient_id, :problem_date, :problem_time, :types_of_symptoms, :pain_burning_sensation, :fever_temperature)
        ");
        
        // Loop through up to 10 problem rows (adjust as needed)
        for ($i = 1; $i <= 10; $i++) {
            $problem_date = isset($_POST["problem_date_$i"]) ? trim($_POST["problem_date_$i"]) : '';
            
            // Skip if this row is empty (no date means empty row)
            if (empty($problem_date)) {
                continue;
            }
            
            // Get problem time components
            $problem_hour = isset($_POST["problem_hour_$i"]) ? trim($_POST["problem_hour_$i"]) : '';
            $problem_minute = isset($_POST["problem_minute_$i"]) ? trim($_POST["problem_minute_$i"]) : '';
            $problem_meridiem = isset($_POST["problem_meridiem_$i"]) ? trim($_POST["problem_meridiem_$i"]) : 'AM';
            
            // Convert 12-hour time to 24-hour format
            $problem_time = null;
            if (!empty($problem_hour) && !empty($problem_minute)) {
                $hour_24 = intval($problem_hour);
                if ($problem_meridiem === 'PM' && $hour_24 != 12) {
                    $hour_24 += 12;
                } elseif ($problem_meridiem === 'AM' && $hour_24 == 12) {
                    $hour_24 = 0;
                }
                $problem_time = sprintf('%02d:%02d:00', $hour_24, intval($problem_minute));
            }
            
            // Convert date from dd/mm/yyyy to yyyy-mm-dd
            $problem_date_formatted = null;
            if (!empty($problem_date) && preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $problem_date, $matches)) {
                $problem_date_formatted = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            }
            
            $types_of_symptoms = isset($_POST["types_of_symptoms_$i"]) ? trim($_POST["types_of_symptoms_$i"]) : '';
            $pain_burning_sensation = isset($_POST["pain_burning_sensation_$i"]) ? trim($_POST["pain_burning_sensation_$i"]) : '';
            $fever_temperature = isset($_POST["fever_temperature_$i"]) && $_POST["fever_temperature_$i"] !== '' ? floatval($_POST["fever_temperature_$i"]) : null;
            
            // Insert problem record
            $problemStmt->execute([
                ':patient_id' => $inserted_id,
                ':problem_date' => $problem_date_formatted,
                ':problem_time' => $problem_time,
                ':types_of_symptoms' => $types_of_symptoms,
                ':pain_burning_sensation' => $pain_burning_sensation,
                ':fever_temperature' => $fever_temperature
            ]);
            
            $problemInsertCount++;
        }
        
        // If updating, delete existing urine culture records first
        if ($is_update) {
            $deleteStmt = $pdo->prepare("DELETE FROM cauti_urine_culture WHERE patient_id = ?");
            $deleteStmt->execute([$inserted_id]);
        }
        
        // Now insert urine culture data (multiple rows)
        $urineCultureInsertCount = 0;
        $urineCultureStmt = $pdo->prepare("
            INSERT INTO cauti_urine_culture 
            (patient_id, sending_date, reporting_date, sample_type, result) 
            VALUES 
            (:patient_id, :sending_date, :reporting_date, :sample_type, :result)
        ");
        
        // Loop through up to 10 urine culture rows (adjust as needed)
        for ($i = 1; $i <= 10; $i++) {
            $sending_date = isset($_POST["urine_re_sending_date_$i"]) ? trim($_POST["urine_re_sending_date_$i"]) : '';
            
            // Skip if this row is empty (no sending date means empty row)
            if (empty($sending_date)) {
                continue;
            }
            
            // Convert date from dd/mm/yyyy to yyyy-mm-dd
            $sending_date_formatted = null;
            if (!empty($sending_date) && preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $sending_date, $matches)) {
                $sending_date_formatted = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            }
            
            $reporting_date = isset($_POST["urine_re_reporting_date_$i"]) ? trim($_POST["urine_re_reporting_date_$i"]) : '';
            $reporting_date_formatted = null;
            if (!empty($reporting_date) && preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $reporting_date, $matches)) {
                $reporting_date_formatted = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            }
            
            $sample_type = isset($_POST["urine_re_sample_type_$i"]) ? trim($_POST["urine_re_sample_type_$i"]) : '';
            $result = isset($_POST["urine_re_result_$i"]) ? trim($_POST["urine_re_result_$i"]) : '';
            
            // Insert urine culture record
            $urineCultureStmt->execute([
                ':patient_id' => $inserted_id,
                ':sending_date' => $sending_date_formatted,
                ':reporting_date' => $reporting_date_formatted,
                ':sample_type' => $sample_type,
                ':result' => $result
            ]);
            
            $urineCultureInsertCount++;
        }
        
        // If updating, delete existing urine re pus records first
        if ($is_update) {
            $deleteStmt = $pdo->prepare("DELETE FROM cauti_urine_re_pus WHERE patient_id = ?");
            $deleteStmt->execute([$inserted_id]);
        }
        
        // Now insert urine re pus data (multiple rows)
        $urineRePusInsertCount = 0;
        $urineRePusStmt = $pdo->prepare("
            INSERT INTO cauti_urine_re_pus 
            (patient_id, test_date, test_time, pus_cells) 
            VALUES 
            (:patient_id, :test_date, :test_time, :pus_cells)
        ");
        
        // Loop through up to 10 urine re pus rows (adjust as needed)
        for ($i = 1; $i <= 10; $i++) {
            $test_date = isset($_POST["urine_re_pus_date_$i"]) ? trim($_POST["urine_re_pus_date_$i"]) : '';
            
            // Skip if this row is empty (no date means empty row)
            if (empty($test_date)) {
                continue;
            }
            
            // Get test time components
            $test_hour = isset($_POST["urine_re_pus_hour_$i"]) ? trim($_POST["urine_re_pus_hour_$i"]) : '';
            $test_minute = isset($_POST["urine_re_pus_minute_$i"]) ? trim($_POST["urine_re_pus_minute_$i"]) : '';
            $test_meridiem = isset($_POST["urine_re_pus_meridiem_$i"]) ? trim($_POST["urine_re_pus_meridiem_$i"]) : 'AM';
            
            // Convert 12-hour time to 24-hour format
            $test_time = null;
            if (!empty($test_hour) && !empty($test_minute)) {
                $hour_24 = intval($test_hour);
                if ($test_meridiem === 'PM' && $hour_24 != 12) {
                    $hour_24 += 12;
                } elseif ($test_meridiem === 'AM' && $hour_24 == 12) {
                    $hour_24 = 0;
                }
                $test_time = sprintf('%02d:%02d:00', $hour_24, intval($test_minute));
            }
            
            // Convert date from dd/mm/yyyy to yyyy-mm-dd
            $test_date_formatted = null;
            if (!empty($test_date) && preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $test_date, $matches)) {
                $test_date_formatted = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            }
            
            $pus_cells = isset($_POST["urine_re_pus_cells_$i"]) ? trim($_POST["urine_re_pus_cells_$i"]) : '';
            
            // Insert urine re pus record
            $urineRePusStmt->execute([
                ':patient_id' => $inserted_id,
                ':test_date' => $test_date_formatted,
                ':test_time' => $test_time,
                ':pus_cells' => $pus_cells
            ]);
            
            $urineRePusInsertCount++;
        }
        
        // If updating, delete existing urine output records first
        if ($is_update) {
            $deleteStmt = $pdo->prepare("DELETE FROM cauti_urine_output WHERE patient_id = ?");
            $deleteStmt->execute([$inserted_id]);
        }
        
        // Now insert urine output data (multiple rows)
        $urineOutputInsertCount = 0;
        $urineOutputStmt = $pdo->prepare("
            INSERT INTO cauti_urine_output 
            (patient_id, output_date, output_time, amount) 
            VALUES 
            (:patient_id, :output_date, :output_time, :amount)
        ");
        
        // Loop through up to 10 urine output rows (adjust as needed)
        for ($i = 1; $i <= 10; $i++) {
            $output_date = isset($_POST["urine_output_date_$i"]) ? trim($_POST["urine_output_date_$i"]) : '';
            
            // Skip if this row is empty (no date means empty row)
            if (empty($output_date)) {
                continue;
            }
            
            // Get output time components
            $output_hour = isset($_POST["urine_output_hour_$i"]) ? trim($_POST["urine_output_hour_$i"]) : '';
            $output_minute = isset($_POST["urine_output_minute_$i"]) ? trim($_POST["urine_output_minute_$i"]) : '';
            $output_meridiem = isset($_POST["urine_output_meridiem_$i"]) ? trim($_POST["urine_output_meridiem_$i"]) : 'AM';
            
            // Convert 12-hour time to 24-hour format
            $output_time = null;
            if (!empty($output_hour) && !empty($output_minute)) {
                $hour_24 = intval($output_hour);
                if ($output_meridiem === 'PM' && $hour_24 != 12) {
                    $hour_24 += 12;
                } elseif ($output_meridiem === 'AM' && $hour_24 == 12) {
                    $hour_24 = 0;
                }
                $output_time = sprintf('%02d:%02d:00', $hour_24, intval($output_minute));
            }
            
            // Convert date from dd/mm/yyyy to yyyy-mm-dd
            $output_date_formatted = null;
            if (!empty($output_date) && preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $output_date, $matches)) {
                $output_date_formatted = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            }
            
            $amount = isset($_POST["urine_output_amount_$i"]) ? trim($_POST["urine_output_amount_$i"]) : '';
            
            // Insert urine output record
            $urineOutputStmt->execute([
                ':patient_id' => $inserted_id,
                ':output_date' => $output_date_formatted,
                ':output_time' => $output_time,
                ':amount' => $amount
            ]);
            
            $urineOutputInsertCount++;
        }
        
        // If updating, delete existing urine result records first
        if ($is_update) {
            $deleteStmt = $pdo->prepare("DELETE FROM cauti_urine_result WHERE patient_id = ?");
            $deleteStmt->execute([$inserted_id]);
        }
        
        // Now insert urine result data (multiple rows)
        $urineResultInsertCount = 0;
        $urineResultStmt = $pdo->prepare("
            INSERT INTO cauti_urine_result 
            (patient_id, result_date, color_of_urine, cloudy_urine, catheter_observation) 
            VALUES 
            (:patient_id, :result_date, :color_of_urine, :cloudy_urine, :catheter_observation)
        ");
        
        // Loop through up to 10 urine result rows (adjust as needed)
        for ($i = 1; $i <= 10; $i++) {
            $result_date = isset($_POST["urine_result_date_$i"]) ? trim($_POST["urine_result_date_$i"]) : '';
            
            // Skip if this row is empty (no date means empty row)
            if (empty($result_date)) {
                continue;
            }
            
            // Convert date from dd/mm/yyyy to yyyy-mm-dd
            $result_date_formatted = null;
            if (!empty($result_date) && preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $result_date, $matches)) {
                $result_date_formatted = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            }
            
            $color_of_urine = isset($_POST["urine_result_color_$i"]) ? trim($_POST["urine_result_color_$i"]) : '';
            $cloudy_urine = isset($_POST["urine_result_cloudy_$i"]) ? trim($_POST["urine_result_cloudy_$i"]) : '';
            $catheter_observation = isset($_POST["urine_result_catheter_obs_$i"]) ? trim($_POST["urine_result_catheter_obs_$i"]) : '';
            
            // Insert urine result record
            $urineResultStmt->execute([
                ':patient_id' => $inserted_id,
                ':result_date' => $result_date_formatted,
                ':color_of_urine' => $color_of_urine,
                ':cloudy_urine' => $cloudy_urine,
                ':catheter_observation' => $catheter_observation
            ]);
            
            $urineResultInsertCount++;
        }
        
        // If updating, delete existing creatinine level records first
        if ($is_update) {
            $deleteStmt = $pdo->prepare("DELETE FROM cauti_creatinine_level WHERE patient_id = ?");
            $deleteStmt->execute([$inserted_id]);
        }
        
        // Now insert creatinine level data (multiple rows)
        $creatinineLevelInsertCount = 0;
        $creatinineLevelStmt = $pdo->prepare("
            INSERT INTO cauti_creatinine_level 
            (patient_id, test_date, result) 
            VALUES 
            (:patient_id, :test_date, :result)
        ");
        
        // Loop through up to 10 creatinine level rows (adjust as needed)
        for ($i = 1; $i <= 10; $i++) {
            $test_date = isset($_POST["creatinine_level_date_$i"]) ? trim($_POST["creatinine_level_date_$i"]) : '';
            
            // Skip if this row is empty (no date means empty row)
            if (empty($test_date)) {
                continue;
            }
            
            // Convert date from dd/mm/yyyy to yyyy-mm-dd
            $test_date_formatted = null;
            if (!empty($test_date) && preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $test_date, $matches)) {
                $test_date_formatted = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            }
            
            $result = isset($_POST["creatinine_level_result_$i"]) ? trim($_POST["creatinine_level_result_$i"]) : '';
            
            // Insert creatinine level record
            $creatinineLevelStmt->execute([
                ':patient_id' => $inserted_id,
                ':test_date' => $test_date_formatted,
                ':result' => $result
            ]);
            
            $creatinineLevelInsertCount++;
        }
        
        // If updating, delete existing immuno suppressants records first
        if ($is_update) {
            $deleteStmt = $pdo->prepare("DELETE FROM cauti_immuno_suppressants WHERE patient_id = ?");
            $deleteStmt->execute([$inserted_id]);
        }
        
        // Now insert immuno suppressants data (multiple rows)
        $immunoSuppressantsInsertCount = 0;
        $immunoSuppressantsStmt = $pdo->prepare("
            INSERT INTO cauti_immuno_suppressants 
            (patient_id, record_date, injection_name, start_on, stop_on) 
            VALUES 
            (:patient_id, :record_date, :injection_name, :start_on, :stop_on)
        ");
        
        // Loop through up to 10 immuno suppressants rows (adjust as needed)
        for ($i = 1; $i <= 10; $i++) {
            $record_date = isset($_POST["immuno_suppressants_date_$i"]) ? trim($_POST["immuno_suppressants_date_$i"]) : '';
            
            // Skip if this row is empty (no date means empty row)
            if (empty($record_date)) {
                continue;
            }
            
            // Convert date from dd/mm/yyyy to yyyy-mm-dd
            $record_date_formatted = null;
            if (!empty($record_date) && preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $record_date, $matches)) {
                $record_date_formatted = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            }
            
            $injection_name = isset($_POST["immuno_suppressants_injection_name_$i"]) ? trim($_POST["immuno_suppressants_injection_name_$i"]) : '';
            $start_on = isset($_POST["immuno_suppressants_start_on_$i"]) ? trim($_POST["immuno_suppressants_start_on_$i"]) : '';
            $stop_on = isset($_POST["immuno_suppressants_stop_on_$i"]) ? trim($_POST["immuno_suppressants_stop_on_$i"]) : '';
            
            // Insert immuno suppressants record
            $immunoSuppressantsStmt->execute([
                ':patient_id' => $inserted_id,
                ':record_date' => $record_date_formatted,
                ':injection_name' => $injection_name,
                ':start_on' => $start_on,
                ':stop_on' => $stop_on
            ]);
            
            $immunoSuppressantsInsertCount++;
        }
        
        $success_message = $is_update ? 'Patient information updated successfully!' : 'Patient information saved successfully!';
        $_SESSION['success'] = $success_message . ' Patient ID: ' . $inserted_id;
        $_SESSION['patient_id'] = $inserted_id;
        
        // Return success response for AJAX
        echo json_encode([
            'success' => true,
            'message' => $success_message,
            'patient_id' => $inserted_id,
            'catheter_records_saved' => $catheterInsertCount,
            'problem_records_saved' => $problemInsertCount,
            'urine_culture_records_saved' => $urineCultureInsertCount,
            'urine_re_pus_records_saved' => $urineRePusInsertCount,
            'urine_output_records_saved' => $urineOutputInsertCount,
            'urine_result_records_saved' => $urineResultInsertCount,
            'creatinine_level_records_saved' => $creatinineLevelInsertCount,
            'immuno_suppressants_records_saved' => $immunoSuppressantsInsertCount,
            'is_update' => $is_update
        ]);
        exit;
    } else {
        throw new Exception('Failed to save patient information.');
    }

} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
?>
