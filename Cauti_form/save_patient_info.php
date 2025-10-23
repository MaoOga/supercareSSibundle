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

    // Validate required fields
    if (empty($name)) {
        $_SESSION['error'] = 'Patient name is required.';
        echo json_encode([
            'success' => false,
            'message' => 'Patient name is required.'
        ]);
        exit;
    }

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
                    diagnosis = :diagnosis 
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
            ':patient_id' => $patient_id
        ]);
        
        $inserted_id = $patient_id; // Use existing ID for update
    } else {
        // INSERT new record
        $sql = "INSERT INTO cauti_patient_info 
                (name, age, sex, uhid, bed_ward, date_of_admission, diagnosis) 
                VALUES 
                (:name, :age, :sex, :uhid, :bed_ward, :date_of_admission, :diagnosis)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':name' => $name,
            ':age' => $age,
            ':sex' => $sex,
            ':uhid' => $uhid,
            ':bed_ward' => $bed_ward,
            ':date_of_admission' => $date_of_admission,
            ':diagnosis' => $diagnosis
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
        
        $success_message = $is_update ? 'Patient information updated successfully!' : 'Patient information saved successfully!';
        $_SESSION['success'] = $success_message . ' Patient ID: ' . $inserted_id;
        $_SESSION['patient_id'] = $inserted_id;
        
        // Return success response for AJAX
        echo json_encode([
            'success' => true,
            'message' => $success_message,
            'patient_id' => $inserted_id,
            'catheter_records_saved' => $catheterInsertCount,
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
