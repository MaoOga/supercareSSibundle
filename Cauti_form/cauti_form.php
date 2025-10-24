<?php
session_start();
require_once '../database/config.php';

// Store nurse info for JavaScript if available from PHP session
$nurseInfoJson = '';
if (isset($_SESSION['username']) || isset($_SESSION['nurse_id'])) {
    $nurseInfo = [
        'nurse_id' => $_SESSION['username'] ?? $_SESSION['nurse_id'] ?? 'Unknown',
        'username' => $_SESSION['username'] ?? $_SESSION['nurse_id'] ?? 'Unknown',
        'name' => $_SESSION['name'] ?? ''
    ];
    $nurseInfoJson = json_encode($nurseInfo);
}

// Check if patient_id is in URL for edit mode
$patient_id = isset($_GET['patient_id']) ? intval($_GET['patient_id']) : 0;
$patientData = null;
$catheterRecords = [];
$problemRecords = [];
$urineCultureRecords = [];

// If editing, fetch patient data directly in PHP
if ($patient_id > 0 && $pdo) {
    try {
        // Fetch patient information
        $stmt = $pdo->prepare("SELECT * FROM cauti_patient_info WHERE id = ?");
        $stmt->execute([$patient_id]);
        $patientData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($patientData) {
            // Format date for display (yyyy-mm-dd to dd/mm/yyyy)
            if (!empty($patientData['date_of_admission'])) {
                $date_obj = new DateTime($patientData['date_of_admission']);
                $patientData['date_of_admission'] = $date_obj->format('d/m/Y');
            }
            
            // Fetch catheter records
            $stmt = $pdo->prepare("SELECT * FROM cauti_catheter WHERE patient_id = ? ORDER BY id ASC");
            $stmt->execute([$patient_id]);
            $catheterRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format catheter data
            foreach ($catheterRecords as &$record) {
                // Format dates
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
                
                // Convert time from 24-hour to 12-hour format
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
            unset($record);
            
            // Fetch problem records
            $stmt = $pdo->prepare("SELECT * FROM cauti_problem WHERE patient_id = ? ORDER BY id ASC");
            $stmt->execute([$patient_id]);
            $problemRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format problem data
            foreach ($problemRecords as &$record) {
                // Format dates
                if (!empty($record['problem_date'])) {
                    $date_obj = new DateTime($record['problem_date']);
                    $record['problem_date'] = $date_obj->format('d/m/Y');
                }
                
                // Convert time from 24-hour to 12-hour format
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
            unset($record);
            
            // Fetch urine culture records
            $stmt = $pdo->prepare("SELECT * FROM cauti_urine_culture WHERE patient_id = ? ORDER BY id ASC");
            $stmt->execute([$patient_id]);
            $urineCultureRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format urine culture data
            foreach ($urineCultureRecords as &$record) {
                // Format dates
                if (!empty($record['sending_date'])) {
                    $date_obj = new DateTime($record['sending_date']);
                    $record['sending_date'] = $date_obj->format('d/m/Y');
                }
                if (!empty($record['reporting_date'])) {
                    $date_obj = new DateTime($record['reporting_date']);
                    $record['reporting_date'] = $date_obj->format('d/m/Y');
                }
            }
            unset($record);
        }
    } catch (Exception $e) {
        error_log("Error loading patient data: " . $e->getMessage());
    }
}

// Initialize urine re pus records array
$urineRePusRecords = [];

// If editing, fetch urine re pus data
if ($patient_id > 0 && $pdo && $patientData) {
    try {
        // Fetch urine re pus records
        $stmt = $pdo->prepare("SELECT * FROM cauti_urine_re_pus WHERE patient_id = ? ORDER BY id ASC");
        $stmt->execute([$patient_id]);
        $urineRePusRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format urine re pus data
        foreach ($urineRePusRecords as &$record) {
            // Format dates
            if (!empty($record['test_date'])) {
                $date_obj = new DateTime($record['test_date']);
                $record['test_date'] = $date_obj->format('d/m/Y');
            }
            
            // Convert time from 24-hour to 12-hour format
            if (!empty($record['test_time'])) {
                $time_obj = new DateTime($record['test_time']);
                $hour = intval($time_obj->format('H'));
                $minute = $time_obj->format('i');
                $meridiem = ($hour >= 12) ? 'PM' : 'AM';
                $hour_12 = ($hour > 12) ? ($hour - 12) : (($hour == 0) ? 12 : $hour);
                $record['test_hour'] = $hour_12;
                $record['test_minute'] = $minute;
                $record['test_meridiem'] = $meridiem;
            }
        }
        unset($record);
    } catch (Exception $e) {
        error_log("Error loading urine re pus data: " . $e->getMessage());
    }
}

// Initialize urine output records array
$urineOutputRecords = [];

// If editing, fetch urine output data
if ($patient_id > 0 && $pdo && $patientData) {
    try {
        // Fetch urine output records
        $stmt = $pdo->prepare("SELECT * FROM cauti_urine_output WHERE patient_id = ? ORDER BY id ASC");
        $stmt->execute([$patient_id]);
        $urineOutputRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format urine output data
        foreach ($urineOutputRecords as &$record) {
            // Format dates
            if (!empty($record['output_date'])) {
                $date_obj = new DateTime($record['output_date']);
                $record['output_date'] = $date_obj->format('d/m/Y');
            }
            
            // Convert time from 24-hour to 12-hour format
            if (!empty($record['output_time'])) {
                $time_obj = new DateTime($record['output_time']);
                $hour = intval($time_obj->format('H'));
                $minute = $time_obj->format('i');
                $meridiem = ($hour >= 12) ? 'PM' : 'AM';
                $hour_12 = ($hour > 12) ? ($hour - 12) : (($hour == 0) ? 12 : $hour);
                $record['output_hour'] = $hour_12;
                $record['output_minute'] = $minute;
                $record['output_meridiem'] = $meridiem;
            }
        }
        unset($record);
    } catch (Exception $e) {
        error_log("Error loading urine output data: " . $e->getMessage());
    }
}

// Initialize urine result records array
$urineResultRecords = [];

// If editing, fetch urine result data
if ($patient_id > 0 && $pdo && $patientData) {
    try {
        // Fetch urine result records
        $stmt = $pdo->prepare("SELECT * FROM cauti_urine_result WHERE patient_id = ? ORDER BY id ASC");
        $stmt->execute([$patient_id]);
        $urineResultRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format urine result data
        foreach ($urineResultRecords as &$record) {
            // Format dates
            if (!empty($record['result_date'])) {
                $date_obj = new DateTime($record['result_date']);
                $record['result_date'] = $date_obj->format('d/m/Y');
            }
        }
        unset($record);
    } catch (Exception $e) {
        error_log("Error loading urine result data: " . $e->getMessage());
    }
}

// Initialize creatinine level records array
$creatinineLevelRecords = [];

// If editing, fetch creatinine level data
if ($patient_id > 0 && $pdo && $patientData) {
    try {
        // Fetch creatinine level records
        $stmt = $pdo->prepare("SELECT * FROM cauti_creatinine_level WHERE patient_id = ? ORDER BY id ASC");
        $stmt->execute([$patient_id]);
        $creatinineLevelRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format creatinine level data
        foreach ($creatinineLevelRecords as &$record) {
            // Format dates
            if (!empty($record['test_date'])) {
                $date_obj = new DateTime($record['test_date']);
                $record['test_date'] = $date_obj->format('d/m/Y');
            }
        }
        unset($record);
    } catch (Exception $e) {
        error_log("Error loading creatinine level data: " . $e->getMessage());
    }
}

// Initialize immuno suppressants records array
$immunoSuppressantsRecords = [];

// If editing, fetch immuno suppressants data
if ($patient_id > 0 && $pdo && $patientData) {
    try {
        // Fetch immuno suppressants records
        $stmt = $pdo->prepare("SELECT * FROM cauti_immuno_suppressants WHERE patient_id = ? ORDER BY id ASC");
        $stmt->execute([$patient_id]);
        $immunoSuppressantsRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format immuno suppressants data
        foreach ($immunoSuppressantsRecords as &$record) {
            // Format dates
            if (!empty($record['record_date'])) {
                $date_obj = new DateTime($record['record_date']);
                $record['record_date'] = $date_obj->format('d/m/Y');
            }
        }
        unset($record);
    } catch (Exception $e) {
        error_log("Error loading immuno suppressants data: " . $e->getMessage());
    }
}

// Helper function to safely output value
function getValue($data, $field, $default = '') {
    return isset($data[$field]) ? htmlspecialchars($data[$field]) : $default;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CAUTI Form</title>
    <link
      href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css"
    />
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <link rel="stylesheet" type="text/css" href="../assets/new_style.css" />
    <style>
      /* Mobile Responsive Styles - EXACT COPY from Example/style.css */
      @media screen and (max-width: 640px) {
        body {
          padding: 1rem;
          overflow-y: auto;
          overflow-x: hidden; /* Prevent horizontal scrolling site-wide */
          min-height: 100vh;
        }

        .container {
          max-width: 100%;
          padding-bottom: 4rem;
          overflow-y: visible;
        }

        .header {
          flex-direction: column;
          align-items: center;
          gap: 10px;
        }

        .header img {
          height: 60px;
        }

        .header .title-main {
          font-size: 1.5em;
        }

        .header .title-sub {
          font-size: 1.2em;
          text-align: center;
        }

        .tg {
          font-size: 12px;
          margin-bottom: 1.5rem;
          min-width: unset; /* Prevent horizontal scrolling for these tables */
          max-width: 100%; /* Fit within viewport */
          width: 100%;
          box-sizing: border-box;
        }

        .tg td,
        .tg th {
          padding: 8px;
          white-space: normal;
          word-break: normal;
        }

        .tg tr {
          display: flex;
          flex-direction: column;
        }

        .tg td,
        .tg th {
          width: 100% !important;
          display: block;
        }

        /* Complex data tables - Enable horizontal scrolling */
        #catheter-table,
        #problem-table,
        #urine-re-table,
        #urine-re-pus-table,
        #urine-output-table,
        #urine-result-table,
        #creatinine-level-table,
        #immuno-suppressants-table {
          min-width: 800px;
          font-size: 11px;
        }

        /* Override stacking for complex tables - keep them as table */
        #catheter-table tr,
        #problem-table tr,
        #urine-re-table tr,
        #urine-re-pus-table tr,
        #urine-output-table tr,
        #urine-result-table tr,
        #creatinine-level-table tr,
        #immuno-suppressants-table tr {
          display: table-row !important;
        }

        #catheter-table td,
        #catheter-table th,
        #problem-table td,
        #problem-table th,
        #urine-re-table td,
        #urine-re-table th,
        #urine-re-pus-table td,
        #urine-re-pus-table th,
        #urine-output-table td,
        #urine-output-table th,
        #urine-result-table td,
        #urine-result-table th,
        #creatinine-level-table td,
        #creatinine-level-table th,
        #immuno-suppressants-table td,
        #immuno-suppressants-table th {
          display: table-cell !important;
          width: auto !important;
          min-width: 100px;
        }

        /* Make table wrappers scrollable */
        .catheter-table-wrapper,
        .problem-table-wrapper,
        .urine-re-table-wrapper,
        .urine-re-pus-table-wrapper,
        .urine-output-table-wrapper,
        .urine-result-table-wrapper,
        .creatinine-level-table-wrapper,
        .immuno-suppressants-table-wrapper {
          overflow-x: auto;
          -webkit-overflow-scrolling: touch;
        }

        input[type="text"].datepicker {
          width: 100% !important;
          max-width: 100px !important;
          min-width: 90px;
          padding: 2px 20px 2px 4px;
          font-size: 11px;
          box-sizing: border-box;
          border: 1px solid #d1d5db;
          border-radius: 4px;
          vertical-align: middle;
        }

        input[type="text"].datepicker::-webkit-calendar-picker-indicator {
          right: 2px;
          padding: 1px;
          width: 16px;
          height: 16px;
        }

        .input-overlay,
        .input-full,
        select {
          font-size: 12px;
          padding: 4px;
          vertical-align: middle;
          max-width: 100%; /* Prevent inputs from causing overflow */
        }

        /* Force labels to expand and inputs to fill space */
        label.flex {
          display: flex !important;
          width: 100% !important;
          align-items: flex-start;
        }

        label.flex .input-overlay,
        label.flex .input-full,
        label.flex input,
        label.flex textarea,
        label.flex select {
          flex: 1 !important;
          min-width: 0 !important;
        }

        label.flex span,
        label.flex .label-bold,
        label.flex .whitespace-nowrap {
          flex-shrink: 0 !important;
          padding-top: 2px;
        }

        .action-button {
          width: 24px;
          height: 24px;
          font-size: 16px;
        }
      }

      /* Global utility classes (from example) */
      .flex {
        display: flex;
      }

      .gap-2 {
        gap: 0.5rem;
      }

      .items-center {
        align-items: center;
      }

      .w-full {
        width: 100%;
      }

      .whitespace-nowrap {
        white-space: nowrap;
      }

      .label-bold {
        font-weight: bold;
        white-space: nowrap;
        display: inline-block;
        vertical-align: middle;
      }

      .input-overlay {
        width: 100%;
        border: none;
        outline: none;
        background: transparent;
        font: inherit;
        padding: 0;
        margin: 0;
        resize: none;
        box-sizing: border-box;
        line-height: 1.4;
        vertical-align: baseline;
      }

      .input-full {
        width: 100%;
        border: none;
        outline: none;
        background: transparent;
        font: inherit;
        resize: none;
        box-sizing: border-box;
        line-height: 1.4;
        vertical-align: baseline;
      }

      label.flex {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        width: 100%;
      }

      /* Make inputs expand to fill available space */
      label.flex .input-overlay,
      label.flex .input-full,
      label.flex input,
      label.flex textarea,
      label.flex select {
        flex: 1;
        min-width: 0;
        padding-top: 1px;
      }

      /* Table cell styles */
      .tg .tg-1wig {
        font-weight: bold;
        text-align: left;
        vertical-align: top;
      }

      .tg .tg-0lax {
        text-align: left;
        vertical-align: top;
      }

      /* Time picker and datetime picker styles from VAP */
      .time-picker {
        position: absolute;
        display: inline-block;
        padding: 10px;
        background: #eeeeee;
        border-radius: 6px;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      }

      .time-picker__select {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        outline: none;
        text-align: center;
        border: 1px solid #dddddd;
        border-radius: 6px;
        padding: 6px 10px;
        background: #ffffff;
        cursor: pointer;
        font-family: Arial, sans-serif;
        margin: 0 2px;
      }

      .time-pickable {
        border: none;
        border-radius: 0;
        padding: 8px;
        background: transparent;
        cursor: pointer;
        font-family: Arial, sans-serif;
        font-size: 14px;
        width: 100%;
        box-sizing: border-box;
      }

      .time-pickable:focus {
        outline: none;
        border: none;
        box-shadow: none;
        background: transparent;
      }

      .datetime-pickable {
        border: none;
        outline: none;
        background: transparent;
        font-family: Arial, sans-serif;
        font-size: 14px;
        resize: none;
        overflow: hidden;
        min-height: 20px;
        height: auto;
        line-height: 1.4;
        padding: 2px 0;
        margin: 0;
        width: 100%;
        box-sizing: border-box;
        color: #000;
        cursor: pointer;
        text-align: left;
      }

      .datetime-pickable:focus {
        outline: none;
        border: none;
        box-shadow: none;
        background: transparent;
      }

      .datetime-picker {
        position: absolute;
        display: inline-block;
        padding: 15px;
        background: #ffffff;
        border: 1px solid #ddd;
        border-radius: 8px;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        min-width: 300px;
      }

      .datetime-picker__section {
        margin-bottom: 10px;
      }

      .datetime-picker__label {
        font-weight: 700;
        margin-bottom: 5px;
        display: block;
        font-size: 12px;
        color: #666;
      }

      .datetime-picker__date {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        margin-bottom: 10px;
        cursor: pointer;
        background-color: #fff;
        transition: border-color 0.2s ease;
      }

      .datetime-picker__date:hover {
        border-color: #007bff;
      }

      .datetime-picker__date:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
      }

      .datetime-picker__time {
        display: flex;
        align-items: center;
        gap: 5px;
      }

      .datetime-picker__select {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        outline: none;
        text-align: center;
        border: 1px solid #dddddd;
        border-radius: 4px;
        padding: 6px 8px;
        background: #ffffff;
        cursor: pointer;
        font-family: Arial, sans-serif;
        font-size: 14px;
      }

      .datetime-picker__separator {
        font-weight: 700;
        color: #666;
      }

      /* Symptomatic table styling */
      .tg .tg-5yq0 {
        background-color: #c8e0c8;
        border-color: black;
        font-weight: 700;
        text-align: center;
        vertical-align: top;
      }

      .tg .tg-c3ow {
        border-color: black;
        text-align: center;
        vertical-align: top;
      }

      /* Hide autocomplete suggestions for all inputs */
      input::-webkit-autofill,
      input::-webkit-autofill:hover,
      input::-webkit-autofill:focus,
      input::-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px transparent inset !important;
        -webkit-text-fill-color: #000 !important;
        background-color: transparent !important;
        background-clip: content-box !important;
      }

      /* Disable autocomplete suggestions */
      input:-webkit-autofill {
        -webkit-box-shadow: 0 0 0 30px transparent inset !important;
        -webkit-text-fill-color: #000 !important;
        background-color: transparent !important;
      }

      /* Hide browser autocomplete dropdown */
      input::-webkit-calendar-picker-indicator {
        display: none !important;
      }

      /* Disable autocomplete for all form inputs */
      input,
      textarea,
      select {
        -autocomplete: off;
        -webkit-autocomplete: off;
        -moz-autocomplete: off;
        -ms-autocomplete: off;
      }

      /* Patient Info table - Flexible layout */
      #patient-info-table {
        width: 100%;
        max-width: 1200px;
        table-layout: auto; /* Flexible table layout */
      }

      /* Patient info table label and input styling */
      #patient-info-table .tg-1wig {
        font-weight: bold;
        text-align: left;
        vertical-align: top;
      }

      #patient-info-table label.flex {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        width: 100%;
        flex-wrap: nowrap;
      }

      /* Keep label text aligned at the top */
      #patient-info-table label.flex > span,
      #patient-info-table label.flex > .label-bold,
      #patient-info-table label.flex > .whitespace-nowrap {
        padding-top: 2px;
      }

      #patient-info-table .input-overlay,
      #patient-info-table .input-full {
        flex: 1;
        width: 100%;
        border: none;
        outline: none;
        background: transparent;
        font: inherit;
        padding: 0;
        margin: 0;
        resize: none;
        box-sizing: border-box;
      }

      #patient-info-table .label-bold {
        font-weight: bold;
        white-space: nowrap !important;
        display: inline-block;
        vertical-align: middle;
        flex-shrink: 0;
      }

      #patient-info-table .whitespace-nowrap {
        white-space: nowrap !important;
        flex-shrink: 0;
      }

      /* Date picker styling for patient info table */
      #patient-info-table input.datepicker {
        border: none;
        outline: none;
        background: transparent;
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: normal;
        padding: 2px 0;
        margin: 0;
        min-height: 20px;
        line-height: 1.4;
        width: 120px;
        flex: 0 0 auto;
      }

      #patient-info-table input.datepicker:focus {
        border: none;
        outline: none;
        background: transparent;
        box-shadow: none;
      }

      /* CATETHER Table - Column width adjustments */
      #catheter-table {
        width: 100% !important;
        max-width: 100% !important;
        table-layout: fixed !important;
      }

      /* Force width changes with higher specificity for catheter table */
      .catheter-table-wrapper #catheter-table td:first-child {
        width: 12% !important;
        min-width: 90px !important;
        max-width: 120px !important;
      }

      .catheter-table-wrapper #catheter-table td:nth-child(2) {
        width: 12% !important;
        min-width: 90px !important;
        max-width: 120px !important;
      }

      .catheter-table-wrapper #catheter-table td:nth-child(3) {
        width: 18% !important;
        min-width: 120px !important;
      }

      .catheter-table-wrapper #catheter-table td:nth-child(4) {
        width: 18% !important;
        min-width: 120px !important;
      }

      .catheter-table-wrapper #catheter-table td:nth-child(5) {
        width: 25% !important;
        min-width: 160px !important;
      }

      .catheter-table-wrapper #catheter-table td:nth-child(6) {
        width: 15% !important;
        min-width: 100px !important;
      }

      #catheter-table td:first-child {
        width: 12% !important;
        min-width: 90px;
      }

      #catheter-table td:nth-child(2) {
        width: 12% !important;
        min-width: 90px;
      }

      #catheter-table td:nth-child(3) {
        width: 18% !important;
        min-width: 120px;
      }

      #catheter-table td:nth-child(4) {
        width: 18% !important;
        min-width: 120px;
      }

      #catheter-table td:nth-child(5) {
        width: 25% !important;
        min-width: 160px;
      }

      #catheter-table td:nth-child(6) {
        width: 15% !important;
        min-width: 100px;
      }

      /* Make catheter table cells more compact */
      #catheter-table td {
        padding: 5px 2px !important;
        font-size: 14px;
        overflow: visible;
        word-break: break-word;
        vertical-align: top;
        height: 40px !important;
        min-height: 40px !important;
      }

      #catheter-table th {
        padding: 8px 2px !important;
        font-size: 14px;
        overflow: visible;
        word-break: break-word;
      }

      /* Compact form inputs for catheter table */
      #catheter-table .form-input {
        font-size: 14px;
        padding: 1px 2px;
        overflow: visible;
        word-break: break-word;
        width: 100%;
        box-sizing: border-box;
        max-width: 100%;
        resize: none;
        height: auto;
        min-height: 20px;
        min-width: 0 !important;
      }

      /* Additional text containment for catheter table */
      #catheter-table td,
      #catheter-table th {
        white-space: normal;
        hyphens: auto;
      }

      /* Ensure textareas don't overflow their containers */
      #catheter-table textarea {
        max-width: 100%;
        overflow-wrap: break-word;
        word-wrap: break-word;
      }

      /* Ensure inputs fit within their cells */
      #catheter-table input[type="text"] {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box;
        padding: 2px 4px;
        font-size: 12px;
      }

      /* Make date and time inputs more compact */
      #catheter-table .datepicker,
      #catheter-table .time-pickable {
        font-size: 11px;
        padding: 1px 2px;
        min-width: 0 !important;
        max-width: 100% !important;
      }

      /* Fix date input styling in catheter table to match form-input */
      #catheter-table input.datepicker {
        border: none;
        outline: none;
        background: transparent;
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: normal;
        padding: 2px 0;
        margin: 0;
        min-height: 20px;
        line-height: 1.4;
        text-align: center;
        width: 100px;
      }

      #catheter-table input.datepicker:focus {
        border: none;
        outline: none;
        background: transparent;
        box-shadow: none;
      }

      /* Center the table cells containing catheter inputs */
      #catheter-table td:first-child,
      #catheter-table td:nth-child(2) {
        text-align: center;
      }

      /* Center the Total Catheter Days column */
      #catheter-table td:nth-child(6) {
        text-align: center;
      }

      /* Center the text inside the Total Catheter Days textarea input */
      #catheter-table td:nth-child(6) textarea {
        text-align: center;
      }

      /* PROBLEM Table - Column width adjustments */
      #problem-table {
        width: 100% !important;
        max-width: 100% !important;
        table-layout: fixed !important;
      }

      /* Force width changes with higher specificity for problem table */
      .problem-table-wrapper #problem-table td:first-child {
        width: 12% !important;
        min-width: 90px !important;
        max-width: 120px !important;
      }

      .problem-table-wrapper #problem-table td:nth-child(2) {
        width: 12% !important;
        min-width: 90px !important;
        max-width: 120px !important;
      }

      .problem-table-wrapper #problem-table td:nth-child(3) {
        width: 40% !important;
        min-width: 200px !important;
      }

      .problem-table-wrapper #problem-table td:nth-child(4) {
        width: 18% !important;
        min-width: 120px !important;
      }

      .problem-table-wrapper #problem-table td:nth-child(5) {
        width: 18% !important;
        min-width: 120px !important;
      }

      #problem-table td:first-child {
        width: 12% !important;
        min-width: 90px;
      }

      #problem-table td:nth-child(2) {
        width: 12% !important;
        min-width: 90px;
      }

      #problem-table td:nth-child(3) {
        width: 40% !important;
        min-width: 200px;
      }

      #problem-table td:nth-child(4) {
        width: 18% !important;
        min-width: 120px;
      }

      #problem-table td:nth-child(5) {
        width: 18% !important;
        min-width: 120px;
      }

      /* Make problem table cells more compact */
      #problem-table td {
        padding: 5px 2px !important;
        font-size: 14px;
        overflow: visible;
        word-break: break-word;
        vertical-align: top;
        height: 40px !important;
        min-height: 40px !important;
      }

      #problem-table th {
        padding: 8px 2px !important;
        font-size: 14px;
        overflow: visible;
        word-break: break-word;
      }

      /* Compact form inputs for problem table */
      #problem-table .form-input {
        font-size: 14px;
        padding: 1px 2px;
        overflow: visible;
        word-break: break-word;
        width: 100%;
        box-sizing: border-box;
        max-width: 100%;
        resize: none;
        height: auto;
        min-height: 20px;
        min-width: 0 !important;
      }

      /* Ensure problem table cells can expand */
      #problem-table {
        table-layout: fixed;
      }

      /* Additional text containment for problem table */
      #problem-table td,
      #problem-table th {
        white-space: normal;
        hyphens: auto;
      }

      /* Ensure textareas don't overflow their containers */
      #problem-table textarea {
        max-width: 100%;
        overflow-wrap: break-word;
        word-wrap: break-word;
      }

      /* Ensure inputs fit within their cells */
      #problem-table input[type="text"] {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box;
        padding: 2px 4px;
        font-size: 12px;
      }

      /* Fix date input styling in problem table to match form-input */
      #problem-table input.datepicker {
        border: none;
        outline: none;
        background: transparent;
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: normal;
        padding: 2px 0;
        margin: 0;
        min-height: 20px;
        line-height: 1.4;
        text-align: center;
        width: 100px;
      }

      #problem-table input.datepicker:focus {
        border: none;
        outline: none;
        background: transparent;
        box-shadow: none;
      }

      /* Center the table cells containing problem inputs */
      #problem-table td:first-child,
      #problem-table td:nth-child(2) {
        text-align: center;
      }

      /* Structured time input styling for problem table */
      #problem-table .structured-time-input {
        display: flex;
        align-items: center;
        gap: -2px;
        justify-content: center;
        font-size: 14px;
        font-family: Arial, sans-serif;
        width: 100%;
        min-width: 120px;
      }

      /* Make time inputs much closer to colon for problem table */
      #problem-table .structured-time-input input[type="number"] {
        border: none;
        outline: none;
        background: transparent;
        padding: 2px 0;
        font-size: 14px;
        font-family: Arial, sans-serif;
        font-weight: normal;
        text-align: center;
        width: 25px;
        min-height: 20px;
        line-height: 1.4;
        margin: 0;
        /* Hide number input spinners */
        appearance: textfield;
        -moz-appearance: textfield;
      }

      #problem-table
        .structured-time-input
        input[type="number"]::-webkit-outer-spin-button,
      #problem-table
        .structured-time-input
        input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
      }

      #problem-table .structured-time-input select {
        border: none;
        outline: none;
        background: transparent;
        padding: 2px 0;
        font-size: 14px;
        font-family: Arial, sans-serif;
        font-weight: normal;
        width: 50px;
        min-height: 20px;
        line-height: 1.4;
        margin: 0;
      }

      #problem-table .structured-time-input span {
        font-weight: normal;
        color: #000;
        margin: 0;
        font-size: 14px;
        font-family: Arial, sans-serif;
        padding: 0;
        width: 8px;
        text-align: center;
      }

      /* Simple Fever Input Styling */
      #problem-table .fever-input-container {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 20px;
      }

      /* Fever Temperature Input Styling */
      #problem-table .fever-temperature-input {
        border: none;
        outline: none;
        background: transparent;
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: normal;
        padding: 2px 4px;
        width: 50px;
        min-height: 18px;
        line-height: 1.2;
        margin: 0;
        text-align: center;
        /* Hide number input spinners */
        appearance: textfield;
        -moz-appearance: textfield;
      }

      #problem-table .fever-temperature-input::-webkit-outer-spin-button,
      #problem-table .fever-temperature-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
      }

      #problem-table .fever-temperature-input:focus {
        background: rgba(0, 123, 255, 0.1);
        border-radius: 2px;
      }

      /* Degree Symbol Styling */
      #problem-table .degree-symbol {
        font-size: 14px;
        font-family: Arial, sans-serif;
        margin: 0 1px;
        color: #000;
      }

      /* General time meridiem select styling */
      .time-meridiem {
        border: none;
        outline: none;
        background: transparent;
        padding: 2px 0;
        font-size: 14px;
        font-family: Arial, sans-serif;
        font-weight: normal;
        width: 50px !important;
        min-height: 20px;
        line-height: 1.4;
        margin: 0;
      }

      /* URINE RE Table - Column width adjustments */
      #urine-re-table {
        width: 100% !important;
        max-width: 100% !important;
        table-layout: auto !important;
      }

      /* Force width changes with higher specificity for urine re table - 4 columns */
      .urine-re-table-wrapper #urine-re-table th:first-child,
      .urine-re-table-wrapper #urine-re-table td:first-child {
        width: 20% !important;
        min-width: 120px !important;
      }

      .urine-re-table-wrapper #urine-re-table th:nth-child(2),
      .urine-re-table-wrapper #urine-re-table td:nth-child(2) {
        width: 20% !important;
        min-width: 120px !important;
      }

      .urine-re-table-wrapper #urine-re-table th:nth-child(3),
      .urine-re-table-wrapper #urine-re-table td:nth-child(3) {
        width: 35% !important;
        min-width: 180px !important;
      }

      .urine-re-table-wrapper #urine-re-table th:nth-child(4),
      .urine-re-table-wrapper #urine-re-table td:nth-child(4) {
        width: 25% !important;
        min-width: 150px !important;
      }

      #urine-re-table th:first-child,
      #urine-re-table td:first-child {
        width: 20% !important;
        min-width: 120px;
      }

      #urine-re-table th:nth-child(2),
      #urine-re-table td:nth-child(2) {
        width: 20% !important;
        min-width: 120px;
      }

      #urine-re-table th:nth-child(3),
      #urine-re-table td:nth-child(3) {
        width: 35% !important;
        min-width: 180px;
      }

      #urine-re-table th:nth-child(4),
      #urine-re-table td:nth-child(4) {
        width: 25% !important;
        min-width: 150px;
      }

      /* Make urine re table cells more compact */
      #urine-re-table td {
        padding: 5px 2px !important;
        font-size: 14px;
        overflow: visible;
        word-break: break-word;
        vertical-align: top;
        height: 40px !important;
        min-height: 40px !important;
      }

      #urine-re-table th {
        padding: 8px 2px !important;
        font-size: 14px;
        overflow: visible;
        word-break: break-word;
      }

      /* Compact form inputs for urine re table */
      #urine-re-table .form-input {
        font-size: 14px;
        padding: 1px 2px;
        overflow: visible;
        word-break: break-word;
        width: 100%;
        box-sizing: border-box;
        max-width: 100%;
        resize: none;
        height: auto;
        min-height: 20px;
        min-width: 0 !important;
      }

      /* Ensure urine re table cells can expand */

      /* Additional text containment for urine re table */
      #urine-re-table td,
      #urine-re-table th {
        white-space: normal;
        hyphens: auto;
      }

      /* Ensure textareas don't overflow their containers */
      #urine-re-table textarea {
        max-width: 100%;
        overflow-wrap: break-word;
        word-wrap: break-word;
      }

      /* Ensure inputs fit within their cells */
      #urine-re-table input[type="text"] {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box;
        padding: 2px 4px;
        font-size: 12px;
      }

      /* Fix date input styling in urine re table to match form-input */
      #urine-re-table input.datepicker {
        border: none;
        outline: none;
        background: transparent;
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: normal;
        padding: 2px 0;
        margin: 0;
        min-height: 20px;
        line-height: 1.4;
        text-align: center;
        width: 100px;
      }

      #urine-re-table input.datepicker:focus {
        border: none;
        outline: none;
        background: transparent;
        box-shadow: none;
      }

      /* Center the table cells containing urine re inputs */
      #urine-re-table td:first-child,
      #urine-re-table td:nth-child(2) {
        text-align: center;
      }

      /* URINE RE PUS CELLS Table - Column width adjustments */
      #urine-re-pus-table {
        width: 100% !important;
        max-width: 100% !important;
        table-layout: auto !important;
      }

      /* Force width changes with higher specificity for urine re pus table - 3 columns */
      .urine-re-pus-table-wrapper #urine-re-pus-table th:first-child,
      .urine-re-pus-table-wrapper #urine-re-pus-table td:first-child {
        width: 20% !important;
        min-width: 120px !important;
      }

      .urine-re-pus-table-wrapper #urine-re-pus-table th:nth-child(2),
      .urine-re-pus-table-wrapper #urine-re-pus-table td:nth-child(2) {
        width: 20% !important;
        min-width: 120px !important;
      }

      .urine-re-pus-table-wrapper #urine-re-pus-table th:nth-child(3),
      .urine-re-pus-table-wrapper #urine-re-pus-table td:nth-child(3) {
        width: 60% !important;
        min-width: 300px !important;
      }

      #urine-re-pus-table th:first-child,
      #urine-re-pus-table td:first-child {
        width: 20% !important;
        min-width: 120px;
      }

      #urine-re-pus-table th:nth-child(2),
      #urine-re-pus-table td:nth-child(2) {
        width: 20% !important;
        min-width: 120px;
      }

      #urine-re-pus-table th:nth-child(3),
      #urine-re-pus-table td:nth-child(3) {
        width: 60% !important;
        min-width: 300px;
      }

      /* Make urine re pus table cells more compact */
      #urine-re-pus-table td {
        padding: 5px 2px !important;
        font-size: 14px;
        overflow: visible;
        word-break: break-word;
        vertical-align: top;
        height: 40px !important;
        min-height: 40px !important;
      }

      #urine-re-pus-table th {
        padding: 8px 2px !important;
        font-size: 14px;
        overflow: visible;
        word-break: break-word;
      }

      /* Compact form inputs for urine re pus table */
      #urine-re-pus-table .form-input {
        font-size: 14px;
        padding: 1px 2px;
        overflow: visible;
        word-break: break-word;
        width: 100%;
        box-sizing: border-box;
        max-width: 100%;
        resize: none;
        height: auto;
        min-height: 20px;
        min-width: 0 !important;
      }

      /* Ensure urine re pus table cells can expand */

      /* Additional text containment for urine re pus table */
      #urine-re-pus-table td,
      #urine-re-pus-table th {
        white-space: normal;
        hyphens: auto;
      }

      /* Ensure textareas don't overflow their containers */
      #urine-re-pus-table textarea {
        max-width: 100%;
        overflow-wrap: break-word;
        word-wrap: break-word;
      }

      /* Ensure inputs fit within their cells */
      #urine-re-pus-table input[type="text"] {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box;
        padding: 2px 4px;
        font-size: 12px;
      }

      /* Fix date input styling in urine re pus table to match form-input */
      #urine-re-pus-table input.datepicker {
        border: none;
        outline: none;
        background: transparent;
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: normal;
        padding: 2px 0;
        margin: 0;
        min-height: 20px;
        line-height: 1.4;
        text-align: center;
        width: 100px;
      }

      #urine-re-pus-table input.datepicker:focus {
        border: none;
        outline: none;
        background: transparent;
        box-shadow: none;
      }

      /* Center the table cells containing urine re pus inputs */
      #urine-re-pus-table td:first-child,
      #urine-re-pus-table td:nth-child(2) {
        text-align: center;
      }

      /* Structured time input styling for urine re pus table */
      #urine-re-pus-table .structured-time-input {
        display: flex;
        align-items: center;
        gap: -2px;
        justify-content: center;
        font-size: 14px;
        font-family: Arial, sans-serif;
        width: 100%;
        min-width: 120px;
      }

      /* Make time inputs much closer to colon for urine re pus table */
      #urine-re-pus-table .structured-time-input input[type="number"] {
        border: none;
        outline: none;
        background: transparent;
        padding: 2px 0;
        font-size: 14px;
        font-family: Arial, sans-serif;
        font-weight: normal;
        text-align: center;
        width: 25px;
        min-height: 20px;
        line-height: 1.4;
        margin: 0;
        /* Hide number input spinners */
        appearance: textfield;
        -moz-appearance: textfield;
      }

      #urine-re-pus-table
        .structured-time-input
        input[type="number"]::-webkit-outer-spin-button,
      #urine-re-pus-table
        .structured-time-input
        input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
      }

      #urine-re-pus-table .structured-time-input select {
        border: none;
        outline: none;
        background: transparent;
        padding: 2px 0;
        font-size: 14px;
        font-family: Arial, sans-serif;
        font-weight: normal;
        width: 50px;
        min-height: 20px;
        line-height: 1.4;
        margin: 0;
      }

      #urine-re-pus-table .structured-time-input span {
        font-weight: normal;
        color: #000;
        margin: 0;
        font-size: 14px;
        font-family: Arial, sans-serif;
        padding: 0;
        width: 8px;
        text-align: center;
      }

      /* URINE OUTPUT Table - Column width adjustments */
      #urine-output-table {
        width: 100% !important;
        max-width: 100% !important;
        table-layout: auto !important;
      }

      /* Force width changes with higher specificity for urine output table - 3 columns */
      .urine-output-table-wrapper #urine-output-table th:first-child,
      .urine-output-table-wrapper #urine-output-table td:first-child {
        width: 20% !important;
        min-width: 120px !important;
      }

      .urine-output-table-wrapper #urine-output-table th:nth-child(2),
      .urine-output-table-wrapper #urine-output-table td:nth-child(2) {
        width: 20% !important;
        min-width: 120px !important;
      }

      .urine-output-table-wrapper #urine-output-table th:nth-child(3),
      .urine-output-table-wrapper #urine-output-table td:nth-child(3) {
        width: 60% !important;
        min-width: 300px !important;
      }

      #urine-output-table th:first-child,
      #urine-output-table td:first-child {
        width: 20% !important;
        min-width: 120px;
      }

      #urine-output-table th:nth-child(2),
      #urine-output-table td:nth-child(2) {
        width: 20% !important;
        min-width: 120px;
      }

      #urine-output-table th:nth-child(3),
      #urine-output-table td:nth-child(3) {
        width: 60% !important;
        min-width: 300px;
      }

      /* Make urine output table cells more compact */
      #urine-output-table td {
        padding: 5px 2px !important;
        font-size: 14px;
        overflow: visible;
        word-break: break-word;
        vertical-align: top;
        height: 40px !important;
        min-height: 40px !important;
      }

      #urine-output-table th {
        padding: 8px 2px !important;
        font-size: 14px;
        overflow: visible;
        word-break: break-word;
      }

      /* Compact form inputs for urine output table */
      #urine-output-table .form-input {
        font-size: 14px;
        padding: 1px 2px;
        overflow: visible;
        word-break: break-word;
        width: 100%;
        box-sizing: border-box;
        max-width: 100%;
        resize: none;
        height: auto;
        min-height: 20px;
        min-width: 0 !important;
      }

      /* Ensure urine output table cells can expand */

      /* Additional text containment for urine output table */
      #urine-output-table td,
      #urine-output-table th {
        white-space: normal;
        hyphens: auto;
      }

      /* Ensure textareas don't overflow their containers */
      #urine-output-table textarea {
        max-width: 100%;
        overflow-wrap: break-word;
        word-wrap: break-word;
      }

      /* Ensure inputs fit within their cells */
      #urine-output-table input[type="text"] {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box;
        padding: 2px 4px;
        font-size: 12px;
      }

      /* Fix date input styling in urine output table to match form-input */
      #urine-output-table input.datepicker {
        border: none;
        outline: none;
        background: transparent;
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: normal;
        padding: 2px 0;
        margin: 0;
        min-height: 20px;
        line-height: 1.4;
        text-align: center;
        width: 100px;
      }

      #urine-output-table input.datepicker:focus {
        border: none;
        outline: none;
        background: transparent;
        box-shadow: none;
      }

      /* Center the table cells containing urine output inputs */
      #urine-output-table td:first-child,
      #urine-output-table td:nth-child(2) {
        text-align: center;
      }

      /* Structured time input styling for urine output table */
      #urine-output-table .structured-time-input {
        display: flex;
        align-items: center;
        gap: -2px;
        justify-content: center;
        font-size: 14px;
        font-family: Arial, sans-serif;
        width: 100%;
        min-width: 120px;
      }

      /* Make time inputs much closer to colon for urine output table */
      #urine-output-table .structured-time-input input[type="number"] {
        border: none;
        outline: none;
        background: transparent;
        padding: 2px 0;
        font-size: 14px;
        font-family: Arial, sans-serif;
        font-weight: normal;
        text-align: center;
        width: 25px;
        min-height: 20px;
        line-height: 1.4;
        margin: 0;
        /* Hide number input spinners */
        appearance: textfield;
        -moz-appearance: textfield;
      }

      #urine-output-table
        .structured-time-input
        input[type="number"]::-webkit-outer-spin-button,
      #urine-output-table
        .structured-time-input
        input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
      }

      #urine-output-table .structured-time-input select {
        border: none;
        outline: none;
        background: transparent;
        padding: 2px 0;
        font-size: 14px;
        font-family: Arial, sans-serif;
        font-weight: normal;
        width: 50px;
        min-height: 20px;
        line-height: 1.4;
        margin: 0;
      }

      #urine-output-table .structured-time-input span {
        font-weight: normal;
        color: #000;
        margin: 0;
        font-size: 14px;
        font-family: Arial, sans-serif;
        padding: 0;
        width: 8px;
        text-align: center;
      }

      /* URINE RESULT Table - Column width adjustments */
      #urine-result-table {
        width: 100% !important;
        max-width: 100% !important;
        table-layout: auto !important;
      }

      /* Force width changes with higher specificity for urine result table - 4 columns */
      .urine-result-table-wrapper #urine-result-table th:first-child,
      .urine-result-table-wrapper #urine-result-table td:first-child {
        width: 15% !important;
        min-width: 100px !important;
      }

      .urine-result-table-wrapper #urine-result-table th:nth-child(2),
      .urine-result-table-wrapper #urine-result-table td:nth-child(2) {
        width: 25% !important;
        min-width: 150px !important;
      }

      .urine-result-table-wrapper #urine-result-table th:nth-child(3),
      .urine-result-table-wrapper #urine-result-table td:nth-child(3) {
        width: 25% !important;
        min-width: 150px !important;
      }

      .urine-result-table-wrapper #urine-result-table th:nth-child(4),
      .urine-result-table-wrapper #urine-result-table td:nth-child(4) {
        width: 35% !important;
        min-width: 200px !important;
      }

      #urine-result-table th:first-child,
      #urine-result-table td:first-child {
        width: 15% !important;
        min-width: 100px;
      }

      #urine-result-table th:nth-child(2),
      #urine-result-table td:nth-child(2) {
        width: 25% !important;
        min-width: 150px;
      }

      #urine-result-table th:nth-child(3),
      #urine-result-table td:nth-child(3) {
        width: 25% !important;
        min-width: 150px;
      }

      #urine-result-table th:nth-child(4),
      #urine-result-table td:nth-child(4) {
        width: 35% !important;
        min-width: 200px;
      }

      /* Make urine result table cells more compact */
      #urine-result-table td {
        padding: 5px 2px !important;
        font-size: 14px;
        overflow: visible;
        word-break: break-word;
        vertical-align: top;
        height: 40px !important;
        min-height: 40px !important;
      }

      #urine-result-table th {
        padding: 8px 2px !important;
        font-size: 14px;
        overflow: visible;
        word-break: break-word;
      }

      /* Compact form inputs for urine result table */
      #urine-result-table .form-input {
        font-size: 14px;
        padding: 1px 2px;
        overflow: visible;
        word-break: break-word;
        width: 100%;
        box-sizing: border-box;
        max-width: 100%;
        resize: none;
        height: auto;
        min-height: 20px;
        min-width: 0 !important;
      }

      /* Ensure urine result table cells can expand */

      /* Additional text containment for urine result table */
      #urine-result-table td,
      #urine-result-table th {
        white-space: normal;
        hyphens: auto;
      }

      /* Ensure textareas don't overflow their containers */
      #urine-result-table textarea {
        max-width: 100%;
        overflow-wrap: break-word;
        word-wrap: break-word;
      }

      /* Ensure inputs fit within their cells */
      #urine-result-table input[type="text"] {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box;
        padding: 2px 4px;
        font-size: 12px;
      }

      /* Fix date input styling in urine result table to match form-input */
      #urine-result-table input.datepicker {
        border: none;
        outline: none;
        background: transparent;
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: normal;
        padding: 2px 0;
        margin: 0;
        min-height: 20px;
        line-height: 1.4;
        text-align: center;
        width: 100px;
      }

      #urine-result-table input.datepicker:focus {
        border: none;
        outline: none;
        background: transparent;
        box-shadow: none;
      }

      /* Center the table cells containing urine result inputs */
      #urine-result-table td:first-child {
        text-align: center;
      }

      /* IMMUNO SUPPRESSANTS Table - Column width adjustments */
      #immuno-suppressants-table {
        width: 100% !important;
        max-width: 100% !important;
        table-layout: auto !important;
      }

      /* Force width changes with higher specificity for immuno suppressants table - 4 columns */
      .immuno-suppressants-table-wrapper
        #immuno-suppressants-table
        th:first-child,
      .immuno-suppressants-table-wrapper
        #immuno-suppressants-table
        td:first-child {
        width: 20% !important;
        min-width: 120px !important;
      }

      .immuno-suppressants-table-wrapper
        #immuno-suppressants-table
        th:nth-child(2),
      .immuno-suppressants-table-wrapper
        #immuno-suppressants-table
        td:nth-child(2) {
        width: 40% !important;
        min-width: 200px !important;
      }

      .immuno-suppressants-table-wrapper
        #immuno-suppressants-table
        th:nth-child(3),
      .immuno-suppressants-table-wrapper
        #immuno-suppressants-table
        td:nth-child(3) {
        width: 20% !important;
        min-width: 120px !important;
      }

      .immuno-suppressants-table-wrapper
        #immuno-suppressants-table
        th:nth-child(4),
      .immuno-suppressants-table-wrapper
        #immuno-suppressants-table
        td:nth-child(4) {
        width: 20% !important;
        min-width: 120px !important;
      }

      /* IMMUNO SUPPRESSANTS Table styling */
      .immuno-suppressants-table-wrapper #immuno-suppressants-table td {
        height: 40px !important;
        padding: 4px !important;
        text-align: left !important;
        vertical-align: top !important;
        font-family: Arial, sans-serif !important;
        font-size: 14px !important;
      }

      .immuno-suppressants-table-wrapper #immuno-suppressants-table input,
      .immuno-suppressants-table-wrapper #immuno-suppressants-table textarea {
        width: 100% !important;
        height: 32px !important;
        border: none !important;
        outline: none !important;
        font-family: Arial, sans-serif !important;
        font-size: 14px !important;
        padding: 4px !important;
        box-sizing: border-box !important;
        background: transparent !important;
      }

      .immuno-suppressants-table-wrapper #immuno-suppressants-table textarea {
        min-height: 32px !important;
        resize: none !important;
        overflow: hidden !important;
      }

      .immuno-suppressants-table-wrapper
        #immuno-suppressants-table
        input.datepicker {
        text-align: center !important;
      }

      .immuno-suppressants-table-wrapper
        #immuno-suppressants-table
        td:first-child {
        text-align: center !important;
      }

      /* Compact form inputs for immuno suppressants table */
      #immuno-suppressants-table .form-input {
        font-size: 14px;
        padding: 1px 2px;
        overflow: visible;
        word-break: break-word;
        width: 100%;
        box-sizing: border-box;
        max-width: 100%;
        resize: none;
        height: auto;
        min-height: 20px;
        min-width: 0 !important;
      }

      /* Ensure immuno suppressants table cells can expand */
      #immuno-suppressants-table td,
      #immuno-suppressants-table th {
        white-space: normal;
        hyphens: auto;
      }

      /* Ensure textareas don't overflow their containers */
      #immuno-suppressants-table textarea {
        max-width: 100%;
        overflow-wrap: break-word;
        word-wrap: break-word;
      }

      /* Ensure inputs fit within their cells */
      #immuno-suppressants-table input[type="text"] {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box;
        padding: 2px 4px;
        font-size: 12px;
      }

      /* Fix date input styling in immuno suppressants table to match form-input */
      #immuno-suppressants-table input.datepicker {
        border: none;
        outline: none;
        background: transparent;
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: normal;
        padding: 2px 0;
        margin: 0;
        min-height: 20px;
        line-height: 1.4;
        text-align: center;
        width: 100px;
      }

      #immuno-suppressants-table input.datepicker:focus {
        border: none;
        outline: none;
        background: transparent;
        box-shadow: none;
      }

      /* Center the table cells containing immuno suppressants inputs */
      #immuno-suppressants-table td:first-child {
        text-align: center;
      }

      /* Center the Immuno Suppressants headers */
      #immuno-suppressants-table td:nth-child(2),
      #immuno-suppressants-table td:nth-child(3),
      #immuno-suppressants-table td:nth-child(4) {
        text-align: center !important;
      }

      /* CREATININE LEVEL Table - Column width adjustments */
      #creatinine-level-table {
        width: 100% !important;
        max-width: 100% !important;
        table-layout: auto !important;
      }

      /* Force width changes with higher specificity for creatinine level table - 2 columns */
      .creatinine-level-table-wrapper #creatinine-level-table th:first-child,
      .creatinine-level-table-wrapper #creatinine-level-table td:first-child {
        width: 30% !important;
        min-width: 150px !important;
      }

      .creatinine-level-table-wrapper #creatinine-level-table th:nth-child(2),
      .creatinine-level-table-wrapper #creatinine-level-table td:nth-child(2) {
        width: 70% !important;
        min-width: 300px !important;
      }

      /* CREATININE LEVEL Table styling */
      .creatinine-level-table-wrapper #creatinine-level-table td {
        height: 40px !important;
        padding: 4px !important;
        text-align: left !important;
        vertical-align: top !important;
        font-family: Arial, sans-serif !important;
        font-size: 14px !important;
      }

      .creatinine-level-table-wrapper #creatinine-level-table input,
      .creatinine-level-table-wrapper #creatinine-level-table textarea {
        width: 100% !important;
        height: 32px !important;
        border: none !important;
        outline: none !important;
        font-family: Arial, sans-serif !important;
        font-size: 14px !important;
        padding: 4px !important;
        box-sizing: border-box !important;
        background: transparent !important;
      }

      .creatinine-level-table-wrapper #creatinine-level-table textarea {
        min-height: 32px !important;
        resize: none !important;
        overflow: hidden !important;
      }

      .creatinine-level-table-wrapper #creatinine-level-table input.datepicker {
        text-align: center !important;
      }

      .creatinine-level-table-wrapper #creatinine-level-table td:first-child {
        text-align: center !important;
      }

      /* Compact form inputs for creatinine level table */
      #creatinine-level-table .form-input {
        font-size: 14px;
        padding: 1px 2px;
        overflow: visible;
        word-break: break-word;
        width: 100%;
        box-sizing: border-box;
        max-width: 100%;
        resize: none;
        height: auto;
        min-height: 20px;
        min-width: 0 !important;
      }

      /* Ensure creatinine level table cells can expand */
      #creatinine-level-table td,
      #creatinine-level-table th {
        white-space: normal;
        hyphens: auto;
      }

      /* Ensure textareas don't overflow their containers */
      #creatinine-level-table textarea {
        max-width: 100%;
        overflow-wrap: break-word;
        word-wrap: break-word;
      }

      /* Ensure inputs fit within their cells */
      #creatinine-level-table input[type="text"] {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box;
        padding: 2px 4px;
        font-size: 12px;
      }

      /* Fix date input styling in creatinine level table to match form-input */
      #creatinine-level-table input.datepicker {
        border: none;
        outline: none;
        background: transparent;
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: normal;
        padding: 2px 0;
        margin: 0;
        min-height: 20px;
        line-height: 1.4;
        text-align: center;
        width: 100px;
      }

      #creatinine-level-table input.datepicker:focus {
        border: none;
        outline: none;
        background: transparent;
        box-shadow: none;
      }

      /* Center the table cells containing creatinine level inputs */
      #creatinine-level-table td:first-child {
        text-align: center;
      }

      /* Center the Result header in creatinine level table */
      #creatinine-level-table td:nth-child(2) {
        text-align: center !important;
      }

      /* Structured time input styling for catheter table */
      #catheter-table .structured-time-input {
        display: flex;
        align-items: center;
        gap: -2px;
        justify-content: center;
        font-size: 14px;
        font-family: Arial, sans-serif;
        width: 100%;
        min-width: 120px;
      }

      /* Make time inputs much closer to colon */
      #catheter-table .structured-time-input input[type="number"] {
        border: none;
        outline: none;
        background: transparent;
        padding: 2px 0;
        font-size: 14px;
        font-family: Arial, sans-serif;
        font-weight: normal;
        text-align: center;
        width: 25px;
        min-height: 20px;
        line-height: 1.4;
        margin: 0;
        /* Hide number input spinners */
        appearance: textfield;
        -moz-appearance: textfield;
      }

      #catheter-table
        .structured-time-input
        input[type="number"]::-webkit-outer-spin-button,
      #catheter-table
        .structured-time-input
        input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
      }

      #catheter-table .structured-time-input select {
        border: none;
        outline: none;
        background: transparent;
        padding: 2px 0;
        font-size: 14px;
        font-family: Arial, sans-serif;
        font-weight: normal;
        width: 50px;
        min-height: 20px;
        line-height: 1.4;
        margin: 0;
      }

      #catheter-table .structured-time-input span {
        font-weight: normal;
        color: #000;
        margin: 0;
        font-size: 14px;
        font-family: Arial, sans-serif;
        padding: 0;
        width: 8px;
        text-align: center;
      }
    </style>
  </head>

  <body>
    <div class="container">
      <div class="header">
        <a
          href="../Cauti_form/cauti_panel.php"
          style="display: inline-block; cursor: pointer"
        >
          <img
            src="/supercareSSibundle/assets/supercare-hospital_logo.png"
            alt="Supercare Logo"
            style="cursor: pointer; height: 80px"
          />
        </a>
        <div class="title-main">
          Catheter Associated Urinary Tract Infection FORM
        </div>
        <div class="title-sub">Supercare</div>
      </div>

      <!-- Form Start -->
      <form method="POST" action="save_patient_info.php">
      
      <!-- Hidden field for patient_id (for edit mode) -->
      <input type="hidden" name="patient_id" id="patient_id_hidden" value="<?php echo $patient_id; ?>">


      <!-- Patient Info Table -->
      <table class="tg" id="patient-info-table">
        <thead>
          <tr>
            <th class="tg-1wig">
              <label class="flex gap-2 w-full" style="align-items: baseline;"
                ><span style="padding-top: 4px;">Name:</span>
                <textarea
                  name="name"
                  class="input-overlay input-full"
                  rows="1"
                  oninput="autoGrow(this)"
                  style="min-height: 28px; line-height: 1.2; padding: 2px;"
                ><?php echo getValue($patientData, 'name'); ?></textarea>
              </label>
            </th>
            <th class="tg-1wig">
              <label class="flex gap-2 items-center w-full"
                >Age:
                <input type="number" class="input-overlay" name="age" value="<?php echo getValue($patientData, 'age'); ?>" />
              </label>
            </th>
            <th class="tg-1wig" style="vertical-align: middle;">
              <label class="flex gap-2 w-full" style="align-items: baseline;"
                ><span style="padding-top: 4px;">Sex:</span>
                <textarea
                  class="input-overlay input-full"
                  name="sex"
                  rows="1"
                  oninput="autoGrow(this)"
                  style="min-height: 28px; line-height: 1.2; padding: 2px; flex: 1;"
                ><?php echo getValue($patientData, 'sex'); ?></textarea>
              </label>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="tg-1wig">
              <label class="flex gap-2 items-center w-full"
                >UHID:
                <input type="text" class="input-overlay" name="uhid" id="uhidInput" value="<?php echo getValue($patientData, 'uhid'); ?>" />
              </label>
              <!-- UHID Validation Alert -->
              <div id="uhidAlert" style="display: none; color: #dc3545; font-size: 12px; margin-top: 4px; font-weight: bold;">
                <i class="fas fa-exclamation-triangle"></i> 
                <span id="uhidAlertText"></span>
              </div>
            </td>
            <td class="tg-1wig">
              <label class="flex gap-2 w-full" style="align-items: baseline;">
                <span class="whitespace-nowrap label-bold" style="padding-top: 4px;">Bed no./Ward:</span>
                <input type="text" class="input-overlay" name="bed" style="padding: 2px;" value="<?php echo getValue($patientData, 'bed_ward'); ?>" />
              </label>
            </td>
            <td class="tg-1wig">
              <label class="flex gap-2 items-center w-full">
                <span class="whitespace-nowrap label-bold"
                  >Date Of Admission:</span
                >
                <input
                  type="text"
                  class="datepicker"
                  name="date_of_admission"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo getValue($patientData, 'date_of_admission'); ?>"
                />
              </label>
            </td>
          </tr>
          <tr>
            <td class="tg-1wig" colspan="3">
              <label class="flex gap-2 w-full" style="align-items: baseline;">
                <span class="whitespace-nowrap label-bold" style="padding-top: 4px;">Diagnosis:</span>
                <textarea
                  class="input-overlay input-full"
                  name="diagnosis"
                  rows="1"
                  oninput="autoGrow(this)"
                  style="min-height: 28px; line-height: 1.2; padding: 2px;"
                ><?php echo getValue($patientData, 'diagnosis'); ?></textarea>
              </label>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Catheter Table -->
      <div
        class="catheter-table-wrapper"
        style="margin-top: 20px; overflow-x: auto; width: 100%"
      >
        <table class="tg" id="catheter-table">
          <thead>
            <tr>
              <th
                class="tg-5yq0"
                colspan="6"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #c8e0c8;
                "
              >
                CATHETER
              </th>
            </tr>
            <tr>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Date
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Time
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Catheter<br />Changed On
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Catheter<br />Removed On
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Catheter <br />out date & time
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Total Catheter<br />Days
              </td>
            </tr>
          </thead>
          <tbody>
            <tr class="catheter-row">
              <td class="tg-0pky" data-label="Date">
                <input
                  type="text"
                  name="catheter_date_1"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($catheterRecords[0]['catheter_date']) ? htmlspecialchars($catheterRecords[0]['catheter_date']) : ''; ?>"
                />
              </td>
              <td class="tg-0pky" data-label="Time">
                <div class="structured-time-input">
                  <input
                    type="number"
                    name="catheter_hour_1"
                    class="time-hour"
                    placeholder="HH"
                    min="1"
                    max="12"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo isset($catheterRecords[0]['catheter_hour']) ? htmlspecialchars($catheterRecords[0]['catheter_hour']) : ''; ?>"
                  />
                  <span>:</span>
                  <input
                    type="number"
                    name="catheter_minute_1"
                    class="time-minute"
                    placeholder="MM"
                    min="0"
                    max="59"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo isset($catheterRecords[0]['catheter_minute']) ? htmlspecialchars($catheterRecords[0]['catheter_minute']) : ''; ?>"
                  />
                  <select
                    name="catheter_meridiem_1"
                    class="time-meridiem"
                    autocomplete="off"
                  >
                    <option value="AM" <?php echo (isset($catheterRecords[0]['catheter_meridiem']) && $catheterRecords[0]['catheter_meridiem'] === 'AM') ? 'selected' : ''; ?>>AM</option>
                    <option value="PM" <?php echo (isset($catheterRecords[0]['catheter_meridiem']) && $catheterRecords[0]['catheter_meridiem'] === 'PM') ? 'selected' : ''; ?>>PM</option>
                  </select>
                </div>
              </td>
              <td class="tg-0pky" data-label="Catheter Changed On">
                <input
                  type="text"
                  name="catheter_changed_on_1"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($catheterRecords[0]['catheter_changed_on']) ? htmlspecialchars($catheterRecords[0]['catheter_changed_on']) : ''; ?>"
                />
              </td>
              <td class="tg-0pky" data-label="Catheter Removed On">
                <input
                  type="text"
                  name="catheter_removed_on_1"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($catheterRecords[0]['catheter_removed_on']) ? htmlspecialchars($catheterRecords[0]['catheter_removed_on']) : ''; ?>"
                />
              </td>
              <td class="tg-0pky" data-label="Catheter Out Date & Time">
                <input
                  type="text"
                  name="catheter_out_date_1"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($catheterRecords[0]['catheter_out_date']) ? htmlspecialchars($catheterRecords[0]['catheter_out_date']) : ''; ?>"
                />
                <br />
                <div class="structured-time-input">
                  <input
                    type="number"
                    name="catheter_out_hour_1"
                    class="time-hour"
                    placeholder="HH"
                    min="1"
                    max="12"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo isset($catheterRecords[0]['catheter_out_hour']) ? htmlspecialchars($catheterRecords[0]['catheter_out_hour']) : ''; ?>"
                  />
                  <span>:</span>
                  <input
                    type="number"
                    name="catheter_out_minute_1"
                    class="time-minute"
                    placeholder="MM"
                    min="0"
                    max="59"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo isset($catheterRecords[0]['catheter_out_minute']) ? htmlspecialchars($catheterRecords[0]['catheter_out_minute']) : ''; ?>"
                  />
                  <select
                    name="catheter_out_meridiem_1"
                    class="time-meridiem"
                    autocomplete="off"
                  >
                    <option value="AM" <?php echo (isset($catheterRecords[0]['catheter_out_meridiem']) && $catheterRecords[0]['catheter_out_meridiem'] === 'AM') ? 'selected' : ''; ?>>AM</option>
                    <option value="PM" <?php echo (isset($catheterRecords[0]['catheter_out_meridiem']) && $catheterRecords[0]['catheter_out_meridiem'] === 'PM') ? 'selected' : ''; ?>>PM</option>
                  </select>
                </div>
              </td>
              <td
                class="tg-0pky"
                style="text-align: center"
                data-label="Total Catheter Days"
              >
                <textarea
                  name="total_catheter_days_1"
                  class="form-input medium"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo isset($catheterRecords[0]['total_catheter_days']) ? htmlspecialchars($catheterRecords[0]['total_catheter_days']) : ''; ?></textarea>
              </td>
            </tr>
            <tr class="catheter-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="catheter_date_2"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($catheterRecords[1]['catheter_date']) ? htmlspecialchars($catheterRecords[1]['catheter_date']) : ''; ?>"
                />
              </td>
              <td class="tg-0pky">
                <div class="structured-time-input">
                  <input
                    type="number"
                    name="catheter_hour_2"
                    class="time-hour"
                    placeholder="HH"
                    min="1"
                    max="12"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo isset($catheterRecords[1]['catheter_hour']) ? htmlspecialchars($catheterRecords[1]['catheter_hour']) : ''; ?>"
                  />
                  <span>:</span>
                  <input
                    type="number"
                    name="catheter_minute_2"
                    class="time-minute"
                    placeholder="MM"
                    min="0"
                    max="59"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo isset($catheterRecords[1]['catheter_minute']) ? htmlspecialchars($catheterRecords[1]['catheter_minute']) : ''; ?>"
                  />
                  <select
                    name="catheter_meridiem_2"
                    class="time-meridiem"
                    autocomplete="off"
                  >
                    <option value="AM" <?php echo (isset($catheterRecords[1]['catheter_meridiem']) && $catheterRecords[1]['catheter_meridiem'] === 'AM') ? 'selected' : ''; ?>>AM</option>
                    <option value="PM" <?php echo (isset($catheterRecords[1]['catheter_meridiem']) && $catheterRecords[1]['catheter_meridiem'] === 'PM') ? 'selected' : ''; ?>>PM</option>
                  </select>
                </div>
              </td>
              <td class="tg-0pky">
                <input
                  type="text"
                  name="catheter_changed_on_2"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($catheterRecords[1]['catheter_changed_on']) ? htmlspecialchars($catheterRecords[1]['catheter_changed_on']) : ''; ?>"
                />
              </td>
              <td class="tg-0pky">
                <input
                  type="text"
                  name="catheter_removed_on_2"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($catheterRecords[1]['catheter_removed_on']) ? htmlspecialchars($catheterRecords[1]['catheter_removed_on']) : ''; ?>"
                />
              </td>
              <td class="tg-0pky">
                <input
                  type="text"
                  name="catheter_out_date_2"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($catheterRecords[1]['catheter_out_date']) ? htmlspecialchars($catheterRecords[1]['catheter_out_date']) : ''; ?>"
                />
                <br />
                <div class="structured-time-input">
                  <input
                    type="number"
                    name="catheter_out_hour_2"
                    class="time-hour"
                    placeholder="HH"
                    min="1"
                    max="12"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo isset($catheterRecords[1]['catheter_out_hour']) ? htmlspecialchars($catheterRecords[1]['catheter_out_hour']) : ''; ?>"
                  />
                  <span>:</span>
                  <input
                    type="number"
                    name="catheter_out_minute_2"
                    class="time-minute"
                    placeholder="MM"
                    min="0"
                    max="59"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo isset($catheterRecords[1]['catheter_out_minute']) ? htmlspecialchars($catheterRecords[1]['catheter_out_minute']) : ''; ?>"
                  />
                  <select
                    name="catheter_out_meridiem_2"
                    class="time-meridiem"
                    autocomplete="off"
                  >
                    <option value="AM" <?php echo (isset($catheterRecords[1]['catheter_out_meridiem']) && $catheterRecords[1]['catheter_out_meridiem'] === 'AM') ? 'selected' : ''; ?>>AM</option>
                    <option value="PM" <?php echo (isset($catheterRecords[1]['catheter_out_meridiem']) && $catheterRecords[1]['catheter_out_meridiem'] === 'PM') ? 'selected' : ''; ?>>PM</option>
                  </select>
                </div>
              </td>
              <td class="tg-0pky" style="text-align: center">
                <textarea
                  name="total_catheter_days_2"
                  class="form-input medium"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo isset($catheterRecords[1]['total_catheter_days']) ? htmlspecialchars($catheterRecords[1]['total_catheter_days']) : ''; ?></textarea>
              </td>
            </tr>
            <tr class="catheter-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="catheter_date_3"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($catheterRecords[2]['catheter_date']) ? htmlspecialchars($catheterRecords[2]['catheter_date']) : ''; ?>"
                />
              </td>
              <td class="tg-0pky">
                <div class="structured-time-input">
                  <input
                    type="number"
                    name="catheter_hour_3"
                    class="time-hour"
                    placeholder="HH"
                    min="1"
                    max="12"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo isset($catheterRecords[2]['catheter_hour']) ? htmlspecialchars($catheterRecords[2]['catheter_hour']) : ''; ?>"
                  />
                  <span>:</span>
                  <input
                    type="number"
                    name="catheter_minute_3"
                    class="time-minute"
                    placeholder="MM"
                    min="0"
                    max="59"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo isset($catheterRecords[2]['catheter_minute']) ? htmlspecialchars($catheterRecords[2]['catheter_minute']) : ''; ?>"
                  />
                  <select
                    name="catheter_meridiem_3"
                    class="time-meridiem"
                    autocomplete="off"
                  >
                    <option value="AM" <?php echo (isset($catheterRecords[2]['catheter_meridiem']) && $catheterRecords[2]['catheter_meridiem'] === 'AM') ? 'selected' : ''; ?>>AM</option>
                    <option value="PM" <?php echo (isset($catheterRecords[2]['catheter_meridiem']) && $catheterRecords[2]['catheter_meridiem'] === 'PM') ? 'selected' : ''; ?>>PM</option>
                  </select>
                </div>
              </td>
              <td class="tg-0pky">
                <input
                  type="text"
                  name="catheter_changed_on_3"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($catheterRecords[2]['catheter_changed_on']) ? htmlspecialchars($catheterRecords[2]['catheter_changed_on']) : ''; ?>"
                />
              </td>
              <td class="tg-0pky">
                <input
                  type="text"
                  name="catheter_removed_on_3"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($catheterRecords[2]['catheter_removed_on']) ? htmlspecialchars($catheterRecords[2]['catheter_removed_on']) : ''; ?>"
                />
              </td>
              <td class="tg-0pky">
                <input
                  type="text"
                  name="catheter_out_date_3"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($catheterRecords[2]['catheter_out_date']) ? htmlspecialchars($catheterRecords[2]['catheter_out_date']) : ''; ?>"
                />
                <br />
                <div class="structured-time-input">
                  <input
                    type="number"
                    name="catheter_out_hour_3"
                    class="time-hour"
                    placeholder="HH"
                    min="1"
                    max="12"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo isset($catheterRecords[2]['catheter_out_hour']) ? htmlspecialchars($catheterRecords[2]['catheter_out_hour']) : ''; ?>"
                  />
                  <span>:</span>
                  <input
                    type="number"
                    name="catheter_out_minute_3"
                    class="time-minute"
                    placeholder="MM"
                    min="0"
                    max="59"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo isset($catheterRecords[2]['catheter_out_minute']) ? htmlspecialchars($catheterRecords[2]['catheter_out_minute']) : ''; ?>"
                  />
                  <select
                    name="catheter_out_meridiem_3"
                    class="time-meridiem"
                    autocomplete="off"
                  >
                    <option value="AM" <?php echo (isset($catheterRecords[2]['catheter_out_meridiem']) && $catheterRecords[2]['catheter_out_meridiem'] === 'AM') ? 'selected' : ''; ?>>AM</option>
                    <option value="PM" <?php echo (isset($catheterRecords[2]['catheter_out_meridiem']) && $catheterRecords[2]['catheter_out_meridiem'] === 'PM') ? 'selected' : ''; ?>>PM</option>
                  </select>
                </div>
              </td>
              <td class="tg-0pky" style="text-align: center">
                <textarea
                  name="total_catheter_days_3"
                  class="form-input medium"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo isset($catheterRecords[2]['total_catheter_days']) ? htmlspecialchars($catheterRecords[2]['total_catheter_days']) : ''; ?></textarea>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-4" style="text-align: right">
        <button type="button" onclick="addCatheterRow()" class="action-button">+</button>
        <button type="button" onclick="removeCatheterRow()" class="action-button">−</button>
      </div>

      <!-- PROBLEM Table -->
      <div
        class="problem-table-wrapper"
        style="margin-top: 20px; overflow-x: auto; width: 100%"
      >
        <table class="tg" id="problem-table">
          <thead>
            <tr>
              <th
                class="tg-9hef"
                colspan="5"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #c8e0c8;
                "
              >
                PROBLEM
              </th>
            </tr>
            <tr>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Date
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Time
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Types Of Symptoms
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Pain/ Burning<br />Sensation
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Fever
              </td>
            </tr>
          </thead>
          <tbody>
            <tr class="problem-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="problem_date_1"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($problemRecords[0]['problem_date']) ? htmlspecialchars($problemRecords[0]['problem_date']) : ''; ?>"
                />
              </td>
              <td class="tg-0pky">
                <div class="structured-time-input">
                  <input
                    type="number"
                    name="problem_hour_1"
                    class="time-hour"
                    placeholder="HH"
                    min="1"
                    max="12"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo isset($problemRecords[0]['problem_hour']) ? htmlspecialchars($problemRecords[0]['problem_hour']) : ''; ?>"
                  />
                  <span>:</span>
                  <input
                    type="number"
                    name="problem_minute_1"
                    class="time-minute"
                    placeholder="MM"
                    min="0"
                    max="59"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo isset($problemRecords[0]['problem_minute']) ? htmlspecialchars($problemRecords[0]['problem_minute']) : ''; ?>"
                  />
                  <select
                    name="problem_meridiem_1"
                    class="time-meridiem"
                    autocomplete="off"
                  >
                    <option value="AM" <?php echo (isset($problemRecords[0]['problem_meridiem']) && $problemRecords[0]['problem_meridiem'] === 'AM') ? 'selected' : ''; ?>>AM</option>
                    <option value="PM" <?php echo (isset($problemRecords[0]['problem_meridiem']) && $problemRecords[0]['problem_meridiem'] === 'PM') ? 'selected' : ''; ?>>PM</option>
                  </select>
                </div>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="types_of_symptoms_1"
                  class="form-input wide"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo isset($problemRecords[0]['types_of_symptoms']) ? htmlspecialchars($problemRecords[0]['types_of_symptoms']) : ''; ?></textarea>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="pain_burning_sensation_1"
                  class="form-input medium"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo isset($problemRecords[0]['pain_burning_sensation']) ? htmlspecialchars($problemRecords[0]['pain_burning_sensation']) : ''; ?></textarea>
              </td>
              <td class="tg-0pky">
                <div class="fever-input-container">
                  <input
                    type="number"
                    name="fever_temperature_1"
                    class="fever-temperature-input"
                    placeholder="36.5"
                    step="0.1"
                    min="30"
                    autocomplete="off"
                    value="<?php echo isset($problemRecords[0]['fever_temperature']) ? htmlspecialchars($problemRecords[0]['fever_temperature']) : ''; ?>"
                  />
                  <span class="degree-symbol">°</span>
                  <span
                    style="
                      font-size: 14px;
                      font-family: Arial, sans-serif;
                      color: #000;
                    "
                    >C</span
                  >
                </div>
              </td>
            </tr>
            <tr class="problem-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="problem_date_2"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($problemRecords[1]['problem_date']) ? htmlspecialchars($problemRecords[1]['problem_date']) : ''; ?>"
                />
              </td>
              <td class="tg-0pky">
                <div class="structured-time-input">
                  <input
                    type="number"
                    name="problem_hour_2"
                    class="time-hour"
                    placeholder="HH"
                    min="1"
                    max="12"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo isset($problemRecords[1]['problem_hour']) ? htmlspecialchars($problemRecords[1]['problem_hour']) : ''; ?>"
                  />
                  <span>:</span>
                  <input
                    type="number"
                    name="problem_minute_2"
                    class="time-minute"
                    placeholder="MM"
                    min="0"
                    max="59"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo isset($problemRecords[1]['problem_minute']) ? htmlspecialchars($problemRecords[1]['problem_minute']) : ''; ?>"
                  />
                  <select
                    name="problem_meridiem_2"
                    class="time-meridiem"
                    autocomplete="off"
                  >
                    <option value="AM" <?php echo (isset($problemRecords[1]['problem_meridiem']) && $problemRecords[1]['problem_meridiem'] === 'AM') ? 'selected' : ''; ?>>AM</option>
                    <option value="PM" <?php echo (isset($problemRecords[1]['problem_meridiem']) && $problemRecords[1]['problem_meridiem'] === 'PM') ? 'selected' : ''; ?>>PM</option>
                  </select>
                </div>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="types_of_symptoms_2"
                  class="form-input wide"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo isset($problemRecords[1]['types_of_symptoms']) ? htmlspecialchars($problemRecords[1]['types_of_symptoms']) : ''; ?></textarea>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="pain_burning_sensation_2"
                  class="form-input medium"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo isset($problemRecords[1]['pain_burning_sensation']) ? htmlspecialchars($problemRecords[1]['pain_burning_sensation']) : ''; ?></textarea>
              </td>
              <td class="tg-0pky">
                <div class="fever-input-container">
                  <input
                    type="number"
                    name="fever_temperature_2"
                    class="fever-temperature-input"
                    placeholder="36.5"
                    step="0.1"
                    min="30"
                    autocomplete="off"
                    value="<?php echo isset($problemRecords[1]['fever_temperature']) ? htmlspecialchars($problemRecords[1]['fever_temperature']) : ''; ?>"
                  />
                  <span class="degree-symbol">°</span>
                  <span
                    style="
                      font-size: 14px;
                      font-family: Arial, sans-serif;
                      color: #000;
                    "
                    >C</span
                  >
                </div>
              </td>
            </tr>
            <tr class="problem-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="problem_date_3"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($problemRecords[2]['problem_date']) ? htmlspecialchars($problemRecords[2]['problem_date']) : ''; ?>"
                />
              </td>
              <td class="tg-0pky">
                <div class="structured-time-input">
                  <input
                    type="number"
                    name="problem_hour_3"
                    class="time-hour"
                    placeholder="HH"
                    min="1"
                    max="12"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo isset($problemRecords[2]['problem_hour']) ? htmlspecialchars($problemRecords[2]['problem_hour']) : ''; ?>"
                  />
                  <span>:</span>
                  <input
                    type="number"
                    name="problem_minute_3"
                    class="time-minute"
                    placeholder="MM"
                    min="0"
                    max="59"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo isset($problemRecords[2]['problem_minute']) ? htmlspecialchars($problemRecords[2]['problem_minute']) : ''; ?>"
                  />
                  <select
                    name="problem_meridiem_3"
                    class="time-meridiem"
                    autocomplete="off"
                  >
                    <option value="AM" <?php echo (isset($problemRecords[2]['problem_meridiem']) && $problemRecords[2]['problem_meridiem'] === 'AM') ? 'selected' : ''; ?>>AM</option>
                    <option value="PM" <?php echo (isset($problemRecords[2]['problem_meridiem']) && $problemRecords[2]['problem_meridiem'] === 'PM') ? 'selected' : ''; ?>>PM</option>
                  </select>
                </div>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="types_of_symptoms_3"
                  class="form-input wide"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo isset($problemRecords[2]['types_of_symptoms']) ? htmlspecialchars($problemRecords[2]['types_of_symptoms']) : ''; ?></textarea>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="pain_burning_sensation_3"
                  class="form-input medium"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo isset($problemRecords[2]['pain_burning_sensation']) ? htmlspecialchars($problemRecords[2]['pain_burning_sensation']) : ''; ?></textarea>
              </td>
              <td class="tg-0pky">
                <div class="fever-input-container">
                  <input
                    type="number"
                    name="fever_temperature_3"
                    class="fever-temperature-input"
                    placeholder="36.5"
                    step="0.1"
                    min="30"
                    autocomplete="off"
                    value="<?php echo isset($problemRecords[2]['fever_temperature']) ? htmlspecialchars($problemRecords[2]['fever_temperature']) : ''; ?>"
                  />
                  <span class="degree-symbol">°</span>
                  <span
                    style="
                      font-size: 14px;
                      font-family: Arial, sans-serif;
                      color: #000;
                    "
                    >C</span
                  >
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-4" style="text-align: right">
        <button type="button" onclick="addProblemRow()" class="action-button">+</button>
        <button type="button" onclick="removeProblemRow()" class="action-button">−</button>
      </div>

      <!-- URINE RE PUS CELLS Table -->
      <div
        class="urine-re-pus-table-wrapper"
        style="margin-top: 20px; overflow-x: auto; width: 100%"
      >
        <table class="tg" id="urine-re-pus-table">
          <thead>
            <tr>
              <th
                class="tg-9hef"
                colspan="3"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #c8e0c8;
                "
              >
                URINE RE
              </th>
            </tr>
            <tr>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Date
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Time
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Pus Cells
              </td>
            </tr>
          </thead>
          <tbody>
            <tr class="urine-re-pus-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="urine_re_pus_date_1"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo getValue($urineRePusRecords[0] ?? null, 'test_date'); ?>"
                />
              </td>
              <td class="tg-0pky">
                <div class="structured-time-input">
                  <input
                    type="number"
                    name="urine_re_pus_hour_1"
                    class="time-hour"
                    placeholder="HH"
                    min="1"
                    max="12"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo getValue($urineRePusRecords[0] ?? null, 'test_hour'); ?>"
                  />
                  <span>:</span>
                  <input
                    type="number"
                    name="urine_re_pus_minute_1"
                    class="time-minute"
                    placeholder="MM"
                    min="0"
                    max="59"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo getValue($urineRePusRecords[0] ?? null, 'test_minute'); ?>"
                  />
                  <select
                    name="urine_re_pus_meridiem_1"
                    class="time-meridiem"
                    autocomplete="off"
                  >
                    <option value="AM" <?php echo (getValue($urineRePusRecords[0] ?? null, 'test_meridiem') === 'AM') ? 'selected' : ''; ?>>AM</option>
                    <option value="PM" <?php echo (getValue($urineRePusRecords[0] ?? null, 'test_meridiem') === 'PM') ? 'selected' : ''; ?>>PM</option>
                  </select>
                </div>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_re_pus_cells_1"
                  class="form-input wide"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($urineRePusRecords[0] ?? null, 'pus_cells'); ?></textarea>
              </td>
            </tr>
            <tr class="urine-re-pus-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="urine_re_pus_date_2"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo getValue($urineRePusRecords[1] ?? null, 'test_date'); ?>"
                />
              </td>
              <td class="tg-0pky">
                <div class="structured-time-input">
                  <input
                    type="number"
                    name="urine_re_pus_hour_2"
                    class="time-hour"
                    placeholder="HH"
                    min="1"
                    max="12"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo getValue($urineRePusRecords[1] ?? null, 'test_hour'); ?>"
                  />
                  <span>:</span>
                  <input
                    type="number"
                    name="urine_re_pus_minute_2"
                    class="time-minute"
                    placeholder="MM"
                    min="0"
                    max="59"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo getValue($urineRePusRecords[1] ?? null, 'test_minute'); ?>"
                  />
                  <select
                    name="urine_re_pus_meridiem_2"
                    class="time-meridiem"
                    autocomplete="off"
                  >
                    <option value="AM" <?php echo (getValue($urineRePusRecords[1] ?? null, 'test_meridiem') === 'AM') ? 'selected' : ''; ?>>AM</option>
                    <option value="PM" <?php echo (getValue($urineRePusRecords[1] ?? null, 'test_meridiem') === 'PM') ? 'selected' : ''; ?>>PM</option>
                  </select>
                </div>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_re_pus_cells_2"
                  class="form-input wide"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($urineRePusRecords[1] ?? null, 'pus_cells'); ?></textarea>
              </td>
            </tr>
            <tr class="urine-re-pus-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="urine_re_pus_date_3"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo getValue($urineRePusRecords[2] ?? null, 'test_date'); ?>"
                />
              </td>
              <td class="tg-0pky">
                <div class="structured-time-input">
                  <input
                    type="number"
                    name="urine_re_pus_hour_3"
                    class="time-hour"
                    placeholder="HH"
                    min="1"
                    max="12"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo getValue($urineRePusRecords[2] ?? null, 'test_hour'); ?>"
                  />
                  <span>:</span>
                  <input
                    type="number"
                    name="urine_re_pus_minute_3"
                    class="time-minute"
                    placeholder="MM"
                    min="0"
                    max="59"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo getValue($urineRePusRecords[2] ?? null, 'test_minute'); ?>"
                  />
                  <select
                    name="urine_re_pus_meridiem_3"
                    class="time-meridiem"
                    autocomplete="off"
                  >
                    <option value="AM" <?php echo (getValue($urineRePusRecords[2] ?? null, 'test_meridiem') === 'AM') ? 'selected' : ''; ?>>AM</option>
                    <option value="PM" <?php echo (getValue($urineRePusRecords[2] ?? null, 'test_meridiem') === 'PM') ? 'selected' : ''; ?>>PM</option>
                  </select>
                </div>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_re_pus_cells_3"
                  class="form-input wide"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($urineRePusRecords[2] ?? null, 'pus_cells'); ?></textarea>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-4" style="text-align: right">
        <button type="button" onclick="addUrineRePusRow()" class="action-button">+</button>
        <button type="button" onclick="removeUrineRePusRow()" class="action-button">−</button>
      </div>

      <!-- URINE CULTURE/ FOLEY'S TIP Table -->
      <div
        class="urine-re-table-wrapper"
        style="margin-top: 20px; overflow-x: auto; width: 100%"
      >
        <table class="tg" id="urine-re-table">
          <thead>
            <tr>
              <th
                class="tg-9hef"
                colspan="4"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #c8e0c8;
                "
              >
                URINE CULTURE/ FOLEY'S TIP
              </th>
            </tr>
            <tr>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Date Of Sending
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Date of Reporting
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Type of Sample
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Result
              </td>
            </tr>
          </thead>
          <tbody>
            <tr class="urine-re-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="urine_re_sending_date_1"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($urineCultureRecords[0]['sending_date']) ? htmlspecialchars($urineCultureRecords[0]['sending_date']) : ''; ?>"
                />
              </td>
              <td class="tg-0pky">
                <input
                  type="text"
                  name="urine_re_reporting_date_1"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($urineCultureRecords[0]['reporting_date']) ? htmlspecialchars($urineCultureRecords[0]['reporting_date']) : ''; ?>"
                />
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_re_sample_type_1"
                  class="form-input medium"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo isset($urineCultureRecords[0]['sample_type']) ? htmlspecialchars($urineCultureRecords[0]['sample_type']) : ''; ?></textarea>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_re_result_1"
                  class="form-input medium"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo isset($urineCultureRecords[0]['result']) ? htmlspecialchars($urineCultureRecords[0]['result']) : ''; ?></textarea>
              </td>
            </tr>
            <tr class="urine-re-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="urine_re_sending_date_2"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($urineCultureRecords[1]['sending_date']) ? htmlspecialchars($urineCultureRecords[1]['sending_date']) : ''; ?>"
                />
              </td>
              <td class="tg-0pky">
                <input
                  type="text"
                  name="urine_re_reporting_date_2"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($urineCultureRecords[1]['reporting_date']) ? htmlspecialchars($urineCultureRecords[1]['reporting_date']) : ''; ?>"
                />
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_re_sample_type_2"
                  class="form-input medium"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo isset($urineCultureRecords[1]['sample_type']) ? htmlspecialchars($urineCultureRecords[1]['sample_type']) : ''; ?></textarea>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_re_result_2"
                  class="form-input medium"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo isset($urineCultureRecords[1]['result']) ? htmlspecialchars($urineCultureRecords[1]['result']) : ''; ?></textarea>
              </td>
            </tr>
            <tr class="urine-re-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="urine_re_sending_date_3"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($urineCultureRecords[2]['sending_date']) ? htmlspecialchars($urineCultureRecords[2]['sending_date']) : ''; ?>"
                />
              </td>
              <td class="tg-0pky">
                <input
                  type="text"
                  name="urine_re_reporting_date_3"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo isset($urineCultureRecords[2]['reporting_date']) ? htmlspecialchars($urineCultureRecords[2]['reporting_date']) : ''; ?>"
                />
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_re_sample_type_3"
                  class="form-input medium"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo isset($urineCultureRecords[2]['sample_type']) ? htmlspecialchars($urineCultureRecords[2]['sample_type']) : ''; ?></textarea>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_re_result_3"
                  class="form-input medium"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo isset($urineCultureRecords[2]['result']) ? htmlspecialchars($urineCultureRecords[2]['result']) : ''; ?></textarea>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-4" style="text-align: right">
        <button type="button" onclick="addUrineReRow()" class="action-button">+</button>
        <button type="button" onclick="removeUrineReRow()" class="action-button">−</button>
      </div>

      <!-- URINE OUTPUT Table -->
      <div
        class="urine-output-table-wrapper"
        style="margin-top: 20px; overflow-x: auto; width: 100%"
      >
        <table class="tg" id="urine-output-table">
          <thead>
            <tr>
              <th
                class="tg-9hef"
                colspan="3"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #c8e0c8;
                "
              >
                URINE OUTPUT 24
              </th>
            </tr>
            <tr>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Date
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Time
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Amount
              </td>
            </tr>
          </thead>
          <tbody>
            <tr class="urine-output-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="urine_output_date_1"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo getValue($urineOutputRecords[0] ?? null, 'output_date'); ?>"
                />
              </td>
              <td class="tg-0pky">
                <div class="structured-time-input">
                  <input
                    type="number"
                    name="urine_output_hour_1"
                    class="time-hour"
                    placeholder="HH"
                    min="1"
                    max="12"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo getValue($urineOutputRecords[0] ?? null, 'output_hour'); ?>"
                  />
                  <span>:</span>
                  <input
                    type="number"
                    name="urine_output_minute_1"
                    class="time-minute"
                    placeholder="MM"
                    min="0"
                    max="59"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo getValue($urineOutputRecords[0] ?? null, 'output_minute'); ?>"
                  />
                  <select
                    name="urine_output_meridiem_1"
                    class="time-meridiem"
                    autocomplete="off"
                  >
                    <option value="AM" <?php echo (getValue($urineOutputRecords[0] ?? null, 'output_meridiem') === 'AM') ? 'selected' : ''; ?>>AM</option>
                    <option value="PM" <?php echo (getValue($urineOutputRecords[0] ?? null, 'output_meridiem') === 'PM') ? 'selected' : ''; ?>>PM</option>
                  </select>
                </div>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_output_amount_1"
                  class="form-input wide"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($urineOutputRecords[0] ?? null, 'amount'); ?></textarea>
              </td>
            </tr>
            <tr class="urine-output-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="urine_output_date_2"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo getValue($urineOutputRecords[1] ?? null, 'output_date'); ?>"
                />
              </td>
              <td class="tg-0pky">
                <div class="structured-time-input">
                  <input
                    type="number"
                    name="urine_output_hour_2"
                    class="time-hour"
                    placeholder="HH"
                    min="1"
                    max="12"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo getValue($urineOutputRecords[1] ?? null, 'output_hour'); ?>"
                  />
                  <span>:</span>
                  <input
                    type="number"
                    name="urine_output_minute_2"
                    class="time-minute"
                    placeholder="MM"
                    min="0"
                    max="59"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo getValue($urineOutputRecords[1] ?? null, 'output_minute'); ?>"
                  />
                  <select
                    name="urine_output_meridiem_2"
                    class="time-meridiem"
                    autocomplete="off"
                  >
                    <option value="AM" <?php echo (getValue($urineOutputRecords[1] ?? null, 'output_meridiem') === 'AM') ? 'selected' : ''; ?>>AM</option>
                    <option value="PM" <?php echo (getValue($urineOutputRecords[1] ?? null, 'output_meridiem') === 'PM') ? 'selected' : ''; ?>>PM</option>
                  </select>
                </div>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_output_amount_2"
                  class="form-input wide"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($urineOutputRecords[1] ?? null, 'amount'); ?></textarea>
              </td>
            </tr>
            <tr class="urine-output-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="urine_output_date_3"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo getValue($urineOutputRecords[2] ?? null, 'output_date'); ?>"
                />
              </td>
              <td class="tg-0pky">
                <div class="structured-time-input">
                  <input
                    type="number"
                    name="urine_output_hour_3"
                    class="time-hour"
                    placeholder="HH"
                    min="1"
                    max="12"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo getValue($urineOutputRecords[2] ?? null, 'output_hour'); ?>"
                  />
                  <span>:</span>
                  <input
                    type="number"
                    name="urine_output_minute_3"
                    class="time-minute"
                    placeholder="MM"
                    min="0"
                    max="59"
                    maxlength="2"
                    autocomplete="off"
                    value="<?php echo getValue($urineOutputRecords[2] ?? null, 'output_minute'); ?>"
                  />
                  <select
                    name="urine_output_meridiem_3"
                    class="time-meridiem"
                    autocomplete="off"
                  >
                    <option value="AM" <?php echo (getValue($urineOutputRecords[2] ?? null, 'output_meridiem') === 'AM') ? 'selected' : ''; ?>>AM</option>
                    <option value="PM" <?php echo (getValue($urineOutputRecords[2] ?? null, 'output_meridiem') === 'PM') ? 'selected' : ''; ?>>PM</option>
                  </select>
                </div>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_output_amount_3"
                  class="form-input wide"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($urineOutputRecords[2] ?? null, 'amount'); ?></textarea>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-4" style="text-align: right">
        <button type="button" onclick="addUrineOutputRow()" class="action-button">+</button>
        <button type="button" onclick="removeUrineOutputRow()" class="action-button">
          −
        </button>
      </div>

      <!-- URINE RESULT Table -->
      <div
        class="urine-result-table-wrapper"
        style="margin-top: 20px; overflow-x: auto; width: 100%"
      >
        <table class="tg" id="urine-result-table">
          <thead>
            <tr>
              <th
                class="tg-9hef"
                colspan="4"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #c8e0c8;
                "
              >
                URINE RESULT
              </th>
            </tr>
            <tr>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Date
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Color of Urine
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Cloudy Urine
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Catheter Observation
              </td>
            </tr>
          </thead>
          <tbody>
            <tr class="urine-result-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="urine_result_date_1"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo getValue($urineResultRecords[0] ?? null, 'result_date'); ?>"
                />
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_result_color_1"
                  class="form-input medium"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($urineResultRecords[0] ?? null, 'color_of_urine'); ?></textarea>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_result_cloudy_1"
                  class="form-input medium"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($urineResultRecords[0] ?? null, 'cloudy_urine'); ?></textarea>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_result_catheter_obs_1"
                  class="form-input wide"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($urineResultRecords[0] ?? null, 'catheter_observation'); ?></textarea>
              </td>
            </tr>
            <tr class="urine-result-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="urine_result_date_2"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo getValue($urineResultRecords[1] ?? null, 'result_date'); ?>"
                />
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_result_color_2"
                  class="form-input medium"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($urineResultRecords[1] ?? null, 'color_of_urine'); ?></textarea>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_result_cloudy_2"
                  class="form-input medium"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($urineResultRecords[1] ?? null, 'cloudy_urine'); ?></textarea>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_result_catheter_obs_2"
                  class="form-input wide"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($urineResultRecords[1] ?? null, 'catheter_observation'); ?></textarea>
              </td>
            </tr>
            <tr class="urine-result-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="urine_result_date_3"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo getValue($urineResultRecords[2] ?? null, 'result_date'); ?>"
                />
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_result_color_3"
                  class="form-input medium"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($urineResultRecords[2] ?? null, 'color_of_urine'); ?></textarea>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_result_cloudy_3"
                  class="form-input medium"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($urineResultRecords[2] ?? null, 'cloudy_urine'); ?></textarea>
              </td>
              <td class="tg-0pky">
                <textarea
                  name="urine_result_catheter_obs_3"
                  class="form-input wide"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($urineResultRecords[2] ?? null, 'catheter_observation'); ?></textarea>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-4" style="text-align: right">
        <button type="button" onclick="addUrineResultRow()" class="action-button">+</button>
        <button type="button" onclick="removeUrineResultRow()" class="action-button">
          −
        </button>
      </div>

      <!-- CREATININE LEVEL Table -->
      <div
        class="creatinine-level-table-wrapper"
        style="margin-top: 20px; overflow-x: auto; width: 100%"
      >
        <table class="tg" id="creatinine-level-table">
          <thead>
            <tr>
              <th
                class="tg-9hef"
                colspan="2"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #c8e0c8;
                "
              >
                Creatinine Level
              </th>
            </tr>
            <tr>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Date
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Result
              </td>
            </tr>
          </thead>
          <tbody>
            <tr class="creatinine-level-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="creatinine_level_date_1"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo getValue($creatinineLevelRecords[0] ?? null, 'test_date'); ?>"
                />
              </td>
              <td class="tg-0pky">
                <textarea
                  name="creatinine_level_result_1"
                  class="form-input wide"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($creatinineLevelRecords[0] ?? null, 'result'); ?></textarea>
              </td>
            </tr>
            <tr class="creatinine-level-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="creatinine_level_date_2"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo getValue($creatinineLevelRecords[1] ?? null, 'test_date'); ?>"
                />
              </td>
              <td class="tg-0pky">
                <textarea
                  name="creatinine_level_result_2"
                  class="form-input wide"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($creatinineLevelRecords[1] ?? null, 'result'); ?></textarea>
              </td>
            </tr>
            <tr class="creatinine-level-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="creatinine_level_date_3"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo getValue($creatinineLevelRecords[2] ?? null, 'test_date'); ?>"
                />
              </td>
              <td class="tg-0pky">
                <textarea
                  name="creatinine_level_result_3"
                  class="form-input wide"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($creatinineLevelRecords[2] ?? null, 'result'); ?></textarea>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-4" style="text-align: right">
        <button type="button" onclick="addCreatinineLevelRow()" class="action-button">
          +
        </button>
        <button type="button" onclick="removeCreatinineLevelRow()" class="action-button">
          −
        </button>
      </div>

      <!-- IMMUNO SUPPRESSANTS Table -->
      <div
        class="immuno-suppressants-table-wrapper"
        style="margin-top: 20px; overflow-x: auto; width: 100%"
      >
        <table class="tg" id="immuno-suppressants-table">
          <thead>
            <tr>
              <th
                class="tg-9hef"
                colspan="4"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #c8e0c8;
                "
              >
                Immuno Suppressants
              </th>
            </tr>
            <tr>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Date
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Injection Name
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Start On
              </td>
              <td
                class="tg-9vaf"
                style="
                  font-weight: 700;
                  text-align: center;
                  vertical-align: top;
                  background-color: #e6e6e6;
                "
              >
                Stop On
              </td>
            </tr>
          </thead>
          <tbody>
            <tr class="immuno-suppressants-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="immuno_suppressants_date_1"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo getValue($immunoSuppressantsRecords[0] ?? null, 'record_date'); ?>"
                />
              </td>
              <td class="tg-0pky">
                <textarea
                  name="immuno_suppressants_injection_name_1"
                  class="form-input wide"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($immunoSuppressantsRecords[0] ?? null, 'injection_name'); ?></textarea>
              </td>
              <td class="tg-0pky">
                <input
                  type="text"
                  name="immuno_suppressants_start_on_1"
                  class="form-input"
                  autocomplete="off"
                  value="<?php echo getValue($immunoSuppressantsRecords[0] ?? null, 'start_on'); ?>"
                />
              </td>
              <td class="tg-0pky">
                <input
                  type="text"
                  name="immuno_suppressants_stop_on_1"
                  class="form-input"
                  autocomplete="off"
                  value="<?php echo getValue($immunoSuppressantsRecords[0] ?? null, 'stop_on'); ?>"
                />
              </td>
            </tr>
            <tr class="immuno-suppressants-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="immuno_suppressants_date_2"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo getValue($immunoSuppressantsRecords[1] ?? null, 'record_date'); ?>"
                />
              </td>
              <td class="tg-0pky">
                <textarea
                  name="immuno_suppressants_injection_name_2"
                  class="form-input wide"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($immunoSuppressantsRecords[1] ?? null, 'injection_name'); ?></textarea>
              </td>
              <td class="tg-0pky">
                <input
                  type="text"
                  name="immuno_suppressants_start_on_2"
                  class="form-input"
                  autocomplete="off"
                  value="<?php echo getValue($immunoSuppressantsRecords[1] ?? null, 'start_on'); ?>"
                />
              </td>
              <td class="tg-0pky">
                <input
                  type="text"
                  name="immuno_suppressants_stop_on_2"
                  class="form-input"
                  autocomplete="off"
                  value="<?php echo getValue($immunoSuppressantsRecords[1] ?? null, 'stop_on'); ?>"
                />
              </td>
            </tr>
            <tr class="immuno-suppressants-row">
              <td class="tg-0pky">
                <input
                  type="text"
                  name="immuno_suppressants_date_3"
                  class="datepicker"
                  placeholder="dd/mm/yyyy"
                  autocomplete="off"
                  value="<?php echo getValue($immunoSuppressantsRecords[2] ?? null, 'record_date'); ?>"
                />
              </td>
              <td class="tg-0pky">
                <textarea
                  name="immuno_suppressants_injection_name_3"
                  class="form-input wide"
                  rows="1"
                  oninput="autoGrow(this)"
                ><?php echo getValue($immunoSuppressantsRecords[2] ?? null, 'injection_name'); ?></textarea>
              </td>
              <td class="tg-0pky">
                <input
                  type="text"
                  name="immuno_suppressants_start_on_3"
                  class="form-input"
                  autocomplete="off"
                  value="<?php echo getValue($immunoSuppressantsRecords[2] ?? null, 'start_on'); ?>"
                />
              </td>
              <td class="tg-0pky">
                <input
                  type="text"
                  name="immuno_suppressants_stop_on_3"
                  class="form-input"
                  autocomplete="off"
                  value="<?php echo getValue($immunoSuppressantsRecords[2] ?? null, 'stop_on'); ?>"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-4" style="text-align: right">
        <button type="button" onclick="addImmunoSuppressantsRow()" class="action-button">
          +
        </button>
        <button type="button" onclick="removeImmunoSuppressantsRow()" class="action-button">
          −
        </button>
      </div>

      <!-- Notes Section -->
      <div class="notes-section mt-4" style="margin-top: 30px">
        <div
          class="notes-title"
          style="
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 10px;
            color: #000;
            text-align: left;
          "
        >
          Notes
        </div>
        <div
          class="notes-container"
          style="
            width: 100%;
            border: 2px solid #000;
            border-radius: 6px;
            padding: 10px;
            background-color: #f9f9f9;
            box-sizing: border-box;
          "
        >
          <textarea
            name="nurse_notes"
            class="notes-textarea"
            rows="6"
            placeholder="Enter any additional notes, observations, or comments here..."
            style="
              width: 100%;
              min-height: 120px;
              padding: 15px;
              border: 1px solid #ccc;
              border-radius: 4px;
              font-family: 'Courier New', 'Monaco', monospace;
              font-size: 14px;
              line-height: 1.4;
              resize: vertical;
              box-sizing: border-box;
              background-color: #fff;
              color: #000;
              outline: none;
              font-weight: bold;
              letter-spacing: 0.5px;
            "
          ><?php echo getValue($patientData, 'nurse_notes'); ?></textarea>
        </div>
      </div>

      <!-- Success/Error Message (Initially Hidden) -->
      <div id="alertMessage" style="display: none; text-align: center; margin: 20px auto; font-size: 16px; font-weight: bold; color: #28a745;">
        <span id="alertText">Patient information saved successfully!</span>
      </div>

      <!-- Submit Button -->
      <div
        class="submit-section"
        style="margin-top: 30px; text-align: center; margin-bottom: 40px"
      >
        <button
          type="submit"
          class="submit-button"
          style="
            background-color: #4caf50;
            color: white;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
          "
          onmouseover="this.style.backgroundColor='#45a049'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 8px rgba(0, 0, 0, 0.15)';"
          onmouseout="this.style.backgroundColor='#4CAF50'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0, 0, 0, 0.1)';"
        >
          Submit Form
        </button>
      </div>

      </form>
      <!-- Form End -->

    </div>

    <script src="../assets/new_script.js"></script>
    <script>
      // Store nurse info in sessionStorage if available from PHP session
      <?php if (!empty($nurseInfoJson)): ?>
      sessionStorage.setItem('nurseInfo', <?php echo $nurseInfoJson; ?>);
      console.log('Nurse info stored in sessionStorage');
      <?php endif; ?>

      // Handle form submission with immediate feedback
      document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action="save_patient_info.php"]');
        const alertMessage = document.getElementById('alertMessage');
        const alertText = document.getElementById('alertText');
        const submitButton = document.querySelector('.submit-button');

        if (form) {
          form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            // ===== CLIENT-SIDE VALIDATION =====
            // Check required fields: Name and UHID
            const nameInput = document.querySelector('textarea[name="name"]');
            const uhidInput = document.querySelector('input[name="uhid"]');
            
            const patientName = nameInput ? nameInput.value.trim() : '';
            const patientUHID = uhidInput ? uhidInput.value.trim() : '';
            
            // Validate Name
            if (!patientName) {
              alertMessage.style.display = 'block';
              alertMessage.style.color = '#dc3545';
              alertText.textContent = '⚠️ Patient Name is required!';
              alertMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
              
              // Highlight the Name field
              if (nameInput) {
                nameInput.style.border = '2px solid #dc3545';
                nameInput.focus();
                setTimeout(() => {
                  nameInput.style.border = '';
                }, 3000);
              }
              return false;
            }
            
            // Validate UHID
            if (!patientUHID) {
              alertMessage.style.display = 'block';
              alertMessage.style.color = '#dc3545';
              alertText.textContent = '⚠️ Patient UHID is required!';
              alertMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
              
              // Highlight the UHID field
              if (uhidInput) {
                uhidInput.style.border = '2px solid #dc3545';
                uhidInput.focus();
                setTimeout(() => {
                  uhidInput.style.border = '';
                }, 3000);
              }
              return false;
            }
            
            // ===== SYNCHRONOUS UHID CHECK ON SUBMIT =====
            // Make a synchronous check to ensure UHID doesn't exist before submitting
            const patientIdField = document.getElementById('patient_id_hidden');
            const patientId = patientIdField ? patientIdField.value : 0;
            
            // Use XMLHttpRequest for synchronous request (fetch doesn't support sync)
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `../forms/check_uhid.php?uhid=${encodeURIComponent(patientUHID)}&patient_id=${patientId}`, false); // false = synchronous
            xhr.send();
            
            if (xhr.status === 200) {
              const checkResult = JSON.parse(xhr.responseText);
              if (checkResult.success && checkResult.exists) {
                // UHID already exists - block submission
                alertMessage.style.display = 'block';
                alertMessage.style.color = '#dc3545';
                alertText.textContent = checkResult.message || '⚠️ Cannot submit: UHID already exists in the system!';
                alertMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Show alert below UHID field
                uhidExists = true;
                uhidAlertText.textContent = checkResult.message;
                uhidAlert.style.display = 'block';
                
                // Highlight the UHID field
                if (uhidInput) {
                  uhidInput.style.border = '2px solid #dc3545';
                  uhidInput.focus();
                  setTimeout(() => {
                    uhidInput.style.border = '';
                  }, 3000);
                }
                return false;
              }
            }
            // ===== END SYNCHRONOUS UHID CHECK =====
            
            // Disable submit button to prevent double submission
            submitButton.disabled = true;
            submitButton.style.opacity = '0.6';
            submitButton.style.cursor = 'not-allowed';
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            
            // Show loading message immediately
            alertMessage.style.display = 'block';
            alertMessage.style.color = '#007bff';
            alertText.textContent = 'Saving patient information...';
            
            // Scroll to the alert message
            alertMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Submit form data via AJAX
            const formData = new FormData(form);
            
            fetch('save_patient_info.php', {
              method: 'POST',
              body: formData
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                // Update message to success (without patient ID)
                alertMessage.style.color = '#28a745';
                alertText.textContent = 'Patient information saved successfully!';
                
                // Redirect to panel after 2 seconds
                setTimeout(function() {
                  window.location.href = 'cauti_panel.php';
                }, 2000);
              } else {
                // Show error message from server
                alertMessage.style.color = '#dc3545';
                alertText.textContent = data.message || 'Error saving patient information.';
                
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.style.opacity = '1';
                submitButton.style.cursor = 'pointer';
                submitButton.innerHTML = 'Submit Form';
              }
            })
            .catch(error => {
              // Show error message
              alertMessage.style.color = '#dc3545';
              alertText.textContent = 'Network error. Please check your connection and try again.';
              
              // Re-enable submit button
              submitButton.disabled = false;
              submitButton.style.opacity = '1';
              submitButton.style.cursor = 'pointer';
              submitButton.innerHTML = 'Submit Form';
            });
          });
        }
        
        // ===== UHID DUPLICATE CHECKING =====
        const uhidInput = document.getElementById('uhidInput');
        const uhidAlert = document.getElementById('uhidAlert');
        const uhidAlertText = document.getElementById('uhidAlertText');
        let uhidExists = false;
        
        if (uhidInput) {
          // Prevent form submission when Enter is pressed in UHID field
          uhidInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
              e.preventDefault();
              console.log('Enter key blocked in UHID field');
              return false;
            }
          });
          
          // Check UHID on input (immediately - no debounce)
          uhidInput.addEventListener('input', function() {
            const uhid = this.value.trim();
            
            // If empty, hide alert and reset
            if (!uhid) {
              uhidAlert.style.display = 'none';
              uhidExists = false;
              uhidInput.style.border = '';
              return;
            }
            
            // Show "checking..." indicator immediately
            uhidAlertText.textContent = 'Checking UHID...';
            uhidAlert.style.display = 'block';
            uhidAlert.style.color = '#007bff'; // Blue for checking
            uhidInput.style.border = '2px solid #007bff';
            
            // Check immediately - no delay
            checkUHID(uhid);
          });
          
          // Also check on blur (when field loses focus)
          uhidInput.addEventListener('blur', function() {
            const uhid = this.value.trim();
            if (uhid) {
              checkUHID(uhid);
            }
          });
        }
        
        function checkUHID(uhid) {
          // Get current patient_id if editing
          const patientIdField = document.getElementById('patient_id_hidden');
          const patientId = patientIdField ? patientIdField.value : 0;
          
          // Make AJAX request to check UHID
          fetch(`../forms/check_uhid.php?uhid=${encodeURIComponent(uhid)}&patient_id=${patientId}`)
            .then(response => response.json())
            .then(data => {
              if (data.success && data.exists) {
                // UHID already exists - show RED alert
                uhidExists = true;
                
                if (uhidAlertText) {
                  uhidAlertText.textContent = data.message;
                }
                if (uhidAlert) {
                  uhidAlert.style.display = 'block';
                  uhidAlert.style.color = '#dc3545'; // RED for error
                }
                
                // Add red border to input
                uhidInput.style.border = '2px solid #dc3545';
              } else if (data.success && !data.exists) {
                // UHID is available - show GREEN confirmation
                uhidExists = false;
                
                if (uhidAlertText) {
                  uhidAlertText.textContent = '✓ UHID is available';
                }
                if (uhidAlert) {
                  uhidAlert.style.display = 'block';
                  uhidAlert.style.color = '#28a745'; // GREEN for success
                }
                
                // Add green border to input
                if (uhidInput) {
                  uhidInput.style.border = '2px solid #28a745';
                }
                
                // Hide success message after 2 seconds
                setTimeout(function() {
                  if (uhidAlert) uhidAlert.style.display = 'none';
                  if (uhidInput) uhidInput.style.border = '';
                }, 2000);
              } else {
                // Hide alert on error
                if (uhidAlert) uhidAlert.style.display = 'none';
                if (uhidInput) uhidInput.style.border = '';
              }
            })
            .catch(error => {
              console.error('Error checking UHID:', error);
              if (uhidAlert) uhidAlert.style.display = 'none';
              if (uhidInput) uhidInput.style.border = '';
            });
        }
        // ===== END UHID CHECKING =====
      });

      // Helper function to set form values (same as SSI form)
      function setFormValue(name, value) {
        if (value === null || value === undefined) return;

        const element = document.querySelector(`[name="${name}"]`);
        if (element) {
          element.value = value;
          // Trigger autoGrow for textareas
          if (element.tagName === 'TEXTAREA') {
            // Auto-grow if function exists
            if (typeof autoGrow === 'function') {
              autoGrow(element);
            }
          }
        } else {
          console.log(`Element not found for name: ${name}`);
        }
      }

      // Function to fill form with patient data
      function fillFormWithCautiData(patientData) {
        console.log('Filling form with patient data:', patientData);
        
        const patient = patientData.patient;
        const catheterRecords = patientData.catheter_records || [];

        // Set basic patient info
        setFormValue('name', patient.name);
        setFormValue('age', patient.age);
        setFormValue('sex', patient.sex);
        setFormValue('uhid', patient.uhid);
        setFormValue('bed', patient.bed_ward);
        setFormValue('date_of_admission', patient.date_of_admission);
        setFormValue('diagnosis', patient.diagnosis);
        
        // Pre-fill catheter records
        catheterRecords.forEach((record, index) => {
                  const rowNum = index + 1; // Row numbers start from 1
                  
                  // Catheter date
                  if (record.catheter_date) {
                    const dateInput = document.querySelector(`input[name="catheter_date_${rowNum}"]`);
                    if (dateInput) dateInput.value = record.catheter_date;
                  }
                  
                  // Catheter time
                  if (record.catheter_hour) {
                    const hourInput = document.querySelector(`input[name="catheter_hour_${rowNum}"]`);
                    if (hourInput) hourInput.value = record.catheter_hour;
                  }
                  if (record.catheter_minute) {
                    const minuteInput = document.querySelector(`input[name="catheter_minute_${rowNum}"]`);
                    if (minuteInput) minuteInput.value = record.catheter_minute;
                  }
                  if (record.catheter_meridiem) {
                    const meridiemSelect = document.querySelector(`select[name="catheter_meridiem_${rowNum}"]`);
                    if (meridiemSelect) meridiemSelect.value = record.catheter_meridiem;
                  }
                  
                  // Catheter changed on
                  if (record.catheter_changed_on) {
                    const changedInput = document.querySelector(`input[name="catheter_changed_on_${rowNum}"]`);
                    if (changedInput) changedInput.value = record.catheter_changed_on;
                  }
                  
                  // Catheter removed on
                  if (record.catheter_removed_on) {
                    const removedInput = document.querySelector(`input[name="catheter_removed_on_${rowNum}"]`);
                    if (removedInput) removedInput.value = record.catheter_removed_on;
                  }
                  
                  // Catheter out date
                  if (record.catheter_out_date) {
                    const outDateInput = document.querySelector(`input[name="catheter_out_date_${rowNum}"]`);
                    if (outDateInput) outDateInput.value = record.catheter_out_date;
                  }
                  
                  // Catheter out time
                  if (record.catheter_out_hour) {
                    const outHourInput = document.querySelector(`input[name="catheter_out_hour_${rowNum}"]`);
                    if (outHourInput) outHourInput.value = record.catheter_out_hour;
                  }
                  if (record.catheter_out_minute) {
                    const outMinuteInput = document.querySelector(`input[name="catheter_out_minute_${rowNum}"]`);
                    if (outMinuteInput) outMinuteInput.value = record.catheter_out_minute;
                  }
                  if (record.catheter_out_meridiem) {
                    const outMeridiemSelect = document.querySelector(`select[name="catheter_out_meridiem_${rowNum}"]`);
                    if (outMeridiemSelect) outMeridiemSelect.value = record.catheter_out_meridiem;
                  }
                  
                  // Total catheter days
                  if (record.total_catheter_days) {
                    const daysTextarea = document.querySelector(`textarea[name="total_catheter_days_${rowNum}"]`);
                    if (daysTextarea) daysTextarea.value = record.total_catheter_days;
                  }
        });
        
        console.log(`Pre-filled ${catheterRecords.length} catheter record(s)`);
      }

      // Load patient data for editing (same pattern as SSI form)
      function loadCautiPatientData(patientId) {
        console.log('Loading patient data for ID:', patientId);
        
        // Show loading indicator
        const loadingIndicator = document.getElementById('loadingPatientData');
        if (loadingIndicator) {
          loadingIndicator.style.display = 'block';
          // Scroll to loading indicator
          loadingIndicator.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        // Test the URL being called
        const url = `get_cauti_patient_data.php?patient_id=${patientId}`;
        console.log('Calling URL:', url);
        
        fetch(url)
          .then(response => {
            console.log('Response status:', response.status);
            return response.text(); // Get raw text first
          })
          .then(text => {
            console.log('Raw response text:', text);
            try {
              const data = JSON.parse(text);
              console.log('Parsed JSON data:', data);
              
              // Hide loading indicator
              if (loadingIndicator) {
                loadingIndicator.style.display = 'none';
              }
              
              if (data.success) {
                fillFormWithCautiData(data.data);
                // Scroll to top of form after loading
                window.scrollTo({ top: 0, behavior: 'smooth' });
              } else {
                console.error('Error loading patient data:', data.message);
                alert('Error loading patient data: ' + data.message);
              }
            } catch (e) {
              // Hide loading indicator on error
              if (loadingIndicator) {
                loadingIndicator.style.display = 'none';
              }
              console.error('Failed to parse JSON:', e);
              console.error('Response was:', text);
              alert('Error parsing patient data. Please try again.');
            }
          })
          .catch(error => {
            // Hide loading indicator on error
            if (loadingIndicator) {
              loadingIndicator.style.display = 'none';
            }
            console.error('Fetch error:', error);
            alert('Network error. Please check your connection and try again.');
          });
      }

      // No need for JavaScript loading - data is pre-filled via PHP!
      // Patient data loads instantly with the page
      
      // BUT: Handle dynamically added catheter rows (beyond the 3 static rows)
      document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($catheterRecords) && count($catheterRecords) > 3): ?>
          // We have more than 3 catheter records - need to create additional rows
          const additionalRecords = <?php echo json_encode(array_slice($catheterRecords, 3)); ?>;
          const catheterTableBody = document.querySelector('#catheter-table tbody');
          
          if (additionalRecords && additionalRecords.length > 0) {
            console.log('Creating', additionalRecords.length, 'additional catheter rows...');
            
            additionalRecords.forEach((record, index) => {
              const rowNum = index + 4; // Start from row 4 (since we have 3 static rows)
              
              // Create new row
              const newRow = document.createElement('tr');
              newRow.className = 'catheter-row';
              newRow.innerHTML = `
                <td class="tg-0pky">
                  <input type="text" name="catheter_date_${rowNum}" class="datepicker" placeholder="dd/mm/yyyy" autocomplete="off" value="${record.catheter_date || ''}">
                </td>
                <td class="tg-0pky">
                  <div class="structured-time-input">
                    <input type="number" name="catheter_hour_${rowNum}" class="time-hour" placeholder="HH" min="1" max="12" maxlength="2" autocomplete="off" value="${record.catheter_hour || ''}">
                    <span>:</span>
                    <input type="number" name="catheter_minute_${rowNum}" class="time-minute" placeholder="MM" min="0" max="59" maxlength="2" autocomplete="off" value="${record.catheter_minute || ''}">
                    <select name="catheter_meridiem_${rowNum}" class="time-meridiem" autocomplete="off">
                      <option value="AM" ${record.catheter_meridiem === 'AM' ? 'selected' : ''}>AM</option>
                      <option value="PM" ${record.catheter_meridiem === 'PM' ? 'selected' : ''}>PM</option>
                    </select>
                  </div>
                </td>
                <td class="tg-0pky">
                  <input type="text" name="catheter_changed_on_${rowNum}" class="datepicker" placeholder="dd/mm/yyyy" autocomplete="off" value="${record.catheter_changed_on || ''}">
                </td>
                <td class="tg-0pky">
                  <input type="text" name="catheter_removed_on_${rowNum}" class="datepicker" placeholder="dd/mm/yyyy" autocomplete="off" value="${record.catheter_removed_on || ''}">
                </td>
                <td class="tg-0pky">
                  <input type="text" name="catheter_out_date_${rowNum}" class="datepicker" placeholder="dd/mm/yyyy" autocomplete="off" value="${record.catheter_out_date || ''}">
                  <br>
                  <div class="structured-time-input">
                    <input type="number" name="catheter_out_hour_${rowNum}" class="time-hour" placeholder="HH" min="1" max="12" maxlength="2" autocomplete="off" value="${record.catheter_out_hour || ''}">
                    <span>:</span>
                    <input type="number" name="catheter_out_minute_${rowNum}" class="time-minute" placeholder="MM" min="0" max="59" maxlength="2" autocomplete="off" value="${record.catheter_out_minute || ''}">
                    <select name="catheter_out_meridiem_${rowNum}" class="time-meridiem" autocomplete="off">
                      <option value="AM" ${record.catheter_out_meridiem === 'AM' ? 'selected' : ''}>AM</option>
                      <option value="PM" ${record.catheter_out_meridiem === 'PM' ? 'selected' : ''}>PM</option>
                    </select>
                  </div>
                </td>
                <td class="tg-0pky" style="text-align: center">
                  <textarea name="total_catheter_days_${rowNum}" class="form-input medium" rows="1" oninput="autoGrow(this)">${record.total_catheter_days || ''}</textarea>
                </td>
              `;
              
              catheterTableBody.appendChild(newRow);
            });
            
            // Re-initialize datepickers for new rows
            if (typeof jQuery !== 'undefined' && jQuery.fn.datepicker) {
              jQuery('.datepicker').datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+0'
              });
            }
            
            console.log('Additional catheter rows created and populated!');
          }
        <?php endif; ?>
        
        <?php if (!empty($problemRecords) && count($problemRecords) > 3): ?>
          // We have more than 3 problem records - need to create additional rows
          const additionalProblemRecords = <?php echo json_encode(array_slice($problemRecords, 3)); ?>;
          const problemTableBody = document.querySelector('#problem-table tbody');
          
          if (additionalProblemRecords && additionalProblemRecords.length > 0) {
            console.log('Creating', additionalProblemRecords.length, 'additional problem rows...');
            
            additionalProblemRecords.forEach((record, index) => {
              const rowNum = index + 4; // Start from row 4 (since we have 3 static rows)
              
              // Create new row
              const newRow = document.createElement('tr');
              newRow.className = 'problem-row';
              newRow.innerHTML = `
                <td class="tg-0pky">
                  <input type="text" name="problem_date_${rowNum}" class="datepicker" placeholder="dd/mm/yyyy" autocomplete="off" value="${record.problem_date || ''}">
                </td>
                <td class="tg-0pky">
                  <div class="structured-time-input">
                    <input type="number" name="problem_hour_${rowNum}" class="time-hour" placeholder="HH" min="1" max="12" maxlength="2" autocomplete="off" value="${record.problem_hour || ''}">
                    <span>:</span>
                    <input type="number" name="problem_minute_${rowNum}" class="time-minute" placeholder="MM" min="0" max="59" maxlength="2" autocomplete="off" value="${record.problem_minute || ''}">
                    <select name="problem_meridiem_${rowNum}" class="time-meridiem" autocomplete="off">
                      <option value="AM" ${record.problem_meridiem === 'AM' ? 'selected' : ''}>AM</option>
                      <option value="PM" ${record.problem_meridiem === 'PM' ? 'selected' : ''}>PM</option>
                    </select>
                  </div>
                </td>
                <td class="tg-0pky">
                  <textarea name="types_of_symptoms_${rowNum}" class="form-input wide" rows="1" oninput="autoGrow(this)">${record.types_of_symptoms || ''}</textarea>
                </td>
                <td class="tg-0pky">
                  <textarea name="pain_burning_sensation_${rowNum}" class="form-input medium" rows="1" oninput="autoGrow(this)">${record.pain_burning_sensation || ''}</textarea>
                </td>
                <td class="tg-0pky">
                  <div class="fever-input-container">
                    <input type="number" name="fever_temperature_${rowNum}" class="fever-temperature-input" placeholder="36.5" step="0.1" min="30" autocomplete="off" value="${record.fever_temperature || ''}">
                    <span class="degree-symbol">°</span>
                    <span style="font-size: 14px; font-family: Arial, sans-serif; color: #000;">C</span>
                  </div>
                </td>
              `;
              
              problemTableBody.appendChild(newRow);
            });
            
            // Re-initialize datepickers for new rows
            if (typeof jQuery !== 'undefined' && jQuery.fn.datepicker) {
              jQuery('.datepicker').datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+0'
              });
            }
            
            console.log('Additional problem rows created and populated!');
          }
        <?php endif; ?>
        
        <?php if (!empty($urineCultureRecords) && count($urineCultureRecords) > 3): ?>
          // We have more than 3 urine culture records - need to create additional rows
          const additionalUrineCultureRecords = <?php echo json_encode(array_slice($urineCultureRecords, 3)); ?>;
          const urineCultureTableBody = document.querySelector('#urine-re-table tbody');
          
          if (additionalUrineCultureRecords && additionalUrineCultureRecords.length > 0) {
            console.log('Creating', additionalUrineCultureRecords.length, 'additional urine culture rows...');
            
            additionalUrineCultureRecords.forEach((record, index) => {
              const rowNum = index + 4; // Start from row 4 (since we have 3 static rows)
              
              // Create new row
              const newRow = document.createElement('tr');
              newRow.className = 'urine-re-row';
              newRow.innerHTML = `
                <td class="tg-0pky">
                  <input type="text" name="urine_re_sending_date_${rowNum}" class="datepicker" placeholder="dd/mm/yyyy" autocomplete="off" value="${record.sending_date || ''}">
                </td>
                <td class="tg-0pky">
                  <input type="text" name="urine_re_reporting_date_${rowNum}" class="datepicker" placeholder="dd/mm/yyyy" autocomplete="off" value="${record.reporting_date || ''}">
                </td>
                <td class="tg-0pky">
                  <textarea name="urine_re_sample_type_${rowNum}" class="form-input medium" rows="1" oninput="autoGrow(this)">${record.sample_type || ''}</textarea>
                </td>
                <td class="tg-0pky">
                  <textarea name="urine_re_result_${rowNum}" class="form-input medium" rows="1" oninput="autoGrow(this)">${record.result || ''}</textarea>
                </td>
              `;
              
              urineCultureTableBody.appendChild(newRow);
            });
            
            // Re-initialize datepickers for new rows
            if (typeof jQuery !== 'undefined' && jQuery.fn.datepicker) {
              jQuery('.datepicker').datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+0'
              });
            }
            
            console.log('Additional urine culture rows created and populated!');
          }
        <?php endif; ?>
        
        <?php if (!empty($urineRePusRecords) && count($urineRePusRecords) > 3): ?>
          // We have more than 3 urine re pus records - need to create additional rows
          const additionalUrineRePusRecords = <?php echo json_encode(array_slice($urineRePusRecords, 3)); ?>;
          const urineRePusTableBody = document.querySelector('#urine-re-pus-table tbody');
          
          if (additionalUrineRePusRecords && additionalUrineRePusRecords.length > 0) {
            console.log('Creating', additionalUrineRePusRecords.length, 'additional urine re pus rows...');
            
            additionalUrineRePusRecords.forEach((record, index) => {
              const rowNum = index + 4; // Start from row 4 (since we have 3 static rows)
              
              // Create new row
              const newRow = document.createElement('tr');
              newRow.className = 'urine-re-pus-row';
              newRow.innerHTML = `
                <td class="tg-0pky">
                  <input type="text" name="urine_re_pus_date_${rowNum}" class="datepicker" placeholder="dd/mm/yyyy" autocomplete="off" value="${record.test_date || ''}">
                </td>
                <td class="tg-0pky">
                  <div class="structured-time-input">
                    <input type="number" name="urine_re_pus_hour_${rowNum}" class="time-hour" placeholder="HH" min="1" max="12" maxlength="2" autocomplete="off" value="${record.test_hour || ''}">
                    <span>:</span>
                    <input type="number" name="urine_re_pus_minute_${rowNum}" class="time-minute" placeholder="MM" min="0" max="59" maxlength="2" autocomplete="off" value="${record.test_minute || ''}">
                    <select name="urine_re_pus_meridiem_${rowNum}" class="time-meridiem" autocomplete="off">
                      <option value="AM" ${record.test_meridiem === 'AM' ? 'selected' : ''}>AM</option>
                      <option value="PM" ${record.test_meridiem === 'PM' ? 'selected' : ''}>PM</option>
                    </select>
                  </div>
                </td>
                <td class="tg-0pky">
                  <textarea name="urine_re_pus_cells_${rowNum}" class="form-input wide" rows="1" oninput="autoGrow(this)">${record.pus_cells || ''}</textarea>
                </td>
              `;
              
              urineRePusTableBody.appendChild(newRow);
            });
            
            // Re-initialize datepickers for new rows
            if (typeof jQuery !== 'undefined' && jQuery.fn.datepicker) {
              jQuery('.datepicker').datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+0'
              });
            }
            
            console.log('Additional urine re pus rows created and populated!');
          }
        <?php endif; ?>
        
        <?php if (!empty($urineOutputRecords) && count($urineOutputRecords) > 3): ?>
          // We have more than 3 urine output records - need to create additional rows
          const additionalUrineOutputRecords = <?php echo json_encode(array_slice($urineOutputRecords, 3)); ?>;
          const urineOutputTableBody = document.querySelector('#urine-output-table tbody');
          
          if (additionalUrineOutputRecords && additionalUrineOutputRecords.length > 0) {
            console.log('Creating', additionalUrineOutputRecords.length, 'additional urine output rows...');
            
            additionalUrineOutputRecords.forEach((record, index) => {
              const rowNum = index + 4; // Start from row 4 (since we have 3 static rows)
              
              // Create new row
              const newRow = document.createElement('tr');
              newRow.className = 'urine-output-row';
              newRow.innerHTML = `
                <td class="tg-0pky">
                  <input type="text" name="urine_output_date_${rowNum}" class="datepicker" placeholder="dd/mm/yyyy" autocomplete="off" value="${record.output_date || ''}">
                </td>
                <td class="tg-0pky">
                  <div class="structured-time-input">
                    <input type="number" name="urine_output_hour_${rowNum}" class="time-hour" placeholder="HH" min="1" max="12" maxlength="2" autocomplete="off" value="${record.output_hour || ''}">
                    <span>:</span>
                    <input type="number" name="urine_output_minute_${rowNum}" class="time-minute" placeholder="MM" min="0" max="59" maxlength="2" autocomplete="off" value="${record.output_minute || ''}">
                    <select name="urine_output_meridiem_${rowNum}" class="time-meridiem" autocomplete="off">
                      <option value="AM" ${record.output_meridiem === 'AM' ? 'selected' : ''}>AM</option>
                      <option value="PM" ${record.output_meridiem === 'PM' ? 'selected' : ''}>PM</option>
                    </select>
                  </div>
                </td>
                <td class="tg-0pky">
                  <textarea name="urine_output_amount_${rowNum}" class="form-input wide" rows="1" oninput="autoGrow(this)">${record.amount || ''}</textarea>
                </td>
              `;
              
              urineOutputTableBody.appendChild(newRow);
            });
            
            // Re-initialize datepickers for new rows
            if (typeof jQuery !== 'undefined' && jQuery.fn.datepicker) {
              jQuery('.datepicker').datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+0'
              });
            }
            
            console.log('Additional urine output rows created and populated!');
          }
        <?php endif; ?>
        
        <?php if (!empty($urineResultRecords) && count($urineResultRecords) > 3): ?>
          // We have more than 3 urine result records - need to create additional rows
          const additionalUrineResultRecords = <?php echo json_encode(array_slice($urineResultRecords, 3)); ?>;
          const urineResultTableBody = document.querySelector('#urine-result-table tbody');
          
          if (additionalUrineResultRecords && additionalUrineResultRecords.length > 0) {
            console.log('Creating', additionalUrineResultRecords.length, 'additional urine result rows...');
            
            additionalUrineResultRecords.forEach((record, index) => {
              const rowNum = index + 4; // Start from row 4 (since we have 3 static rows)
              
              // Create new row
              const newRow = document.createElement('tr');
              newRow.className = 'urine-result-row';
              newRow.innerHTML = `
                <td class="tg-0pky">
                  <input type="text" name="urine_result_date_${rowNum}" class="datepicker" placeholder="dd/mm/yyyy" autocomplete="off" value="${record.result_date || ''}">
                </td>
                <td class="tg-0pky">
                  <textarea name="urine_result_color_${rowNum}" class="form-input medium" rows="1" oninput="autoGrow(this)">${record.color_of_urine || ''}</textarea>
                </td>
                <td class="tg-0pky">
                  <textarea name="urine_result_cloudy_${rowNum}" class="form-input medium" rows="1" oninput="autoGrow(this)">${record.cloudy_urine || ''}</textarea>
                </td>
                <td class="tg-0pky">
                  <textarea name="urine_result_catheter_obs_${rowNum}" class="form-input wide" rows="1" oninput="autoGrow(this)">${record.catheter_observation || ''}</textarea>
                </td>
              `;
              
              urineResultTableBody.appendChild(newRow);
            });
            
            // Re-initialize datepickers for new rows
            if (typeof jQuery !== 'undefined' && jQuery.fn.datepicker) {
              jQuery('.datepicker').datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+0'
              });
            }
            
            console.log('Additional urine result rows created and populated!');
          }
        <?php endif; ?>
        
        <?php if (!empty($creatinineLevelRecords) && count($creatinineLevelRecords) > 3): ?>
          // We have more than 3 creatinine level records - need to create additional rows
          const additionalCreatinineLevelRecords = <?php echo json_encode(array_slice($creatinineLevelRecords, 3)); ?>;
          const creatinineLevelTableBody = document.querySelector('#creatinine-level-table tbody');
          
          if (additionalCreatinineLevelRecords && additionalCreatinineLevelRecords.length > 0) {
            console.log('Creating', additionalCreatinineLevelRecords.length, 'additional creatinine level rows...');
            
            additionalCreatinineLevelRecords.forEach((record, index) => {
              const rowNum = index + 4; // Start from row 4 (since we have 3 static rows)
              
              // Create new row
              const newRow = document.createElement('tr');
              newRow.className = 'creatinine-level-row';
              newRow.innerHTML = `
                <td class="tg-0pky">
                  <input type="text" name="creatinine_level_date_${rowNum}" class="datepicker" placeholder="dd/mm/yyyy" autocomplete="off" value="${record.test_date || ''}">
                </td>
                <td class="tg-0pky">
                  <textarea name="creatinine_level_result_${rowNum}" class="form-input wide" rows="1" oninput="autoGrow(this)">${record.result || ''}</textarea>
                </td>
              `;
              
              creatinineLevelTableBody.appendChild(newRow);
            });
            
            // Re-initialize datepickers for new rows
            if (typeof jQuery !== 'undefined' && jQuery.fn.datepicker) {
              jQuery('.datepicker').datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+0'
              });
            }
            
            console.log('Additional creatinine level rows created and populated!');
          }
        <?php endif; ?>
        
        <?php if (!empty($immunoSuppressantsRecords) && count($immunoSuppressantsRecords) > 3): ?>
          // We have more than 3 immuno suppressants records - need to create additional rows
          const additionalImmunoSuppressantsRecords = <?php echo json_encode(array_slice($immunoSuppressantsRecords, 3)); ?>;
          const immunoSuppressantsTableBody = document.querySelector('#immuno-suppressants-table tbody');
          
          if (additionalImmunoSuppressantsRecords && additionalImmunoSuppressantsRecords.length > 0) {
            console.log('Creating', additionalImmunoSuppressantsRecords.length, 'additional immuno suppressants rows...');
            
            additionalImmunoSuppressantsRecords.forEach((record, index) => {
              const rowNum = index + 4; // Start from row 4 (since we have 3 static rows)
              
              // Create new row
              const newRow = document.createElement('tr');
              newRow.className = 'immuno-suppressants-row';
              newRow.innerHTML = `
                <td class="tg-0pky">
                  <input type="text" name="immuno_suppressants_date_${rowNum}" class="datepicker" placeholder="dd/mm/yyyy" autocomplete="off" value="${record.record_date || ''}">
                </td>
                <td class="tg-0pky">
                  <textarea name="immuno_suppressants_injection_name_${rowNum}" class="form-input wide" rows="1" oninput="autoGrow(this)">${record.injection_name || ''}</textarea>
                </td>
                <td class="tg-0pky">
                  <input type="text" name="immuno_suppressants_start_on_${rowNum}" class="form-input" autocomplete="off" value="${record.start_on || ''}">
                </td>
                <td class="tg-0pky">
                  <input type="text" name="immuno_suppressants_stop_on_${rowNum}" class="form-input" autocomplete="off" value="${record.stop_on || ''}">
                </td>
              `;
              
              immunoSuppressantsTableBody.appendChild(newRow);
            });
            
            // Re-initialize datepickers for new rows
            if (typeof jQuery !== 'undefined' && jQuery.fn.datepicker) {
              jQuery('.datepicker').datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+0'
              });
            }
            
            console.log('Additional immuno suppressants rows created and populated!');
          }
        <?php endif; ?>
      });
    </script>
  </body>
</html>
