<?php
/**
 * Comprehensive Export System for SSI Bundle
 * Handles exports of different data types in various formats
 */

require_once 'config.php';
require_once 'audit_logger.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $exportType = $_GET['type'] ?? 'audit_logs';
    $format = $_GET['format'] ?? 'json';
    $filters = $_GET['filters'] ?? [];
    
    // Parse filters if passed as JSON
    if (is_string($filters)) {
        $filters = json_decode($filters, true) ?: [];
    }
    
    $exportData = [];
    $filename = '';
    
    switch ($exportType) {
        case 'audit_logs':
            $auditLogger = new AuditLogger($pdo);
            $result = $auditLogger->getAuditLogs($filters, 1, 999999);
            
            $exportData = [
                'export_type' => 'audit_logs',
                'export_date' => date('Y-m-d H:i:s'),
                'filters_applied' => $filters,
                'total_records' => $result['total_count'],
                'data' => $result['logs']
            ];
            $filename = "audit-logs-" . date('Y-m-d-H-i-s');
            break;
            
        case 'nurses':
            $stmt = $pdo->prepare("SELECT id, nurse_id, name, email, role, created_at, updated_at FROM nurses ORDER BY created_at DESC");
            $stmt->execute();
            $nurses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $exportData = [
                'export_type' => 'nurses',
                'export_date' => date('Y-m-d H:i:s'),
                'total_records' => count($nurses),
                'data' => $nurses
            ];
            $filename = "nurses-" . date('Y-m-d-H-i-s');
            break;
            
        case 'surgeons':
            $stmt = $pdo->prepare("SELECT id, name, created_at FROM surgeons ORDER BY created_at DESC");
            $stmt->execute();
            $surgeons = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $exportData = [
                'export_type' => 'surgeons',
                'export_date' => date('Y-m-d H:i:s'),
                'total_records' => count($surgeons),
                'data' => $surgeons
            ];
            $filename = "surgeons-" . date('Y-m-d-H-i-s');
            break;
            
        case 'patients':
            $stmt = $pdo->prepare("SELECT * FROM patients ORDER BY patient_id DESC");
            $stmt->execute();
            $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $exportData = [
                'export_type' => 'patients',
                'export_date' => date('Y-m-d H:i:s'),
                'total_records' => count($patients),
                'data' => $patients
            ];
            $filename = "patients-" . date('Y-m-d-H-i-s');
            break;
            
        case 'complete_system':
            // Export all system data
            $auditLogger = new AuditLogger($pdo);
            $auditResult = $auditLogger->getAuditLogs([], 1, 999999);
            
            $stmt = $pdo->prepare("SELECT id, nurse_id, name, email, role, created_at, updated_at FROM nurses ORDER BY created_at DESC");
            $stmt->execute();
            $nurses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt = $pdo->prepare("SELECT id, name, created_at FROM surgeons ORDER BY created_at DESC");
            $stmt->execute();
            $surgeons = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt = $pdo->prepare("SELECT * FROM patients ORDER BY patient_id DESC");
            $stmt->execute();
            $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $exportData = [
                'export_type' => 'complete_system',
                'export_date' => date('Y-m-d H:i:s'),
                'system_info' => [
                    'database_name' => $dbname,
                    'export_version' => '1.0',
                    'total_nurses' => count($nurses),
                    'total_surgeons' => count($surgeons),
                    'total_patients' => count($patients),
                    'total_audit_logs' => $auditResult['total_count']
                ],
                'data' => [
                    'nurses' => $nurses,
                    'surgeons' => $surgeons,
                    'patients' => $patients,
                    'audit_logs' => $auditResult['logs']
                ]
            ];
            $filename = "ssi-complete-system-" . date('Y-m-d-H-i-s');
            break;
            
        default:
            throw new Exception("Invalid export type: {$exportType}");
    }
    
    // Handle different formats
    switch ($format) {
        case 'json':
            $content = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $mimeType = 'application/json';
            $extension = 'json';
            break;
            
        case 'csv':
            $content = convertToCSV($exportData);
            $mimeType = 'text/csv';
            $extension = 'csv';
            break;
            
        case 'xml':
            $content = convertToXML($exportData);
            $mimeType = 'application/xml';
            $extension = 'xml';
            break;
            
        default:
            throw new Exception("Invalid format: {$format}");
    }
    
    // Set headers for file download
    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: attachment; filename="' . $filename . '.' . $extension . '"');
    header('Content-Length: ' . strlen($content));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    
    echo $content;
    
    // Log the export activity
    $auditLogger = new AuditLogger($pdo);
    $auditLogger->logDataExport('SYSTEM', $exportType, $exportData['total_records'] ?? 0);
    
} catch (Exception $e) {
    error_log("Export error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Export failed: ' . $e->getMessage()
    ]);
}

/**
 * Convert data to CSV format
 */
function convertToCSV($data) {
    if (empty($data['data'])) {
        return '';
    }
    
    $csv = '';
    
    // Add headers
    if (is_array($data['data']) && !empty($data['data'])) {
        $firstRow = is_array($data['data'][0]) ? $data['data'][0] : $data['data'];
        $csv .= implode(',', array_keys($firstRow)) . "\n";
        
        // Add data rows
        foreach ($data['data'] as $row) {
            $csvRow = [];
            foreach ($row as $value) {
                // Escape commas and quotes
                $value = str_replace('"', '""', $value);
                $csvRow[] = '"' . $value . '"';
            }
            $csv .= implode(',', $csvRow) . "\n";
        }
    }
    
    return $csv;
}

/**
 * Convert data to XML format
 */
function convertToXML($data) {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<export>' . "\n";
    $xml .= '  <export_type>' . htmlspecialchars($data['export_type']) . '</export_type>' . "\n";
    $xml .= '  <export_date>' . htmlspecialchars($data['export_date']) . '</export_date>' . "\n";
    $xml .= '  <total_records>' . ($data['total_records'] ?? 0) . '</total_records>' . "\n";
    
    if (isset($data['data'])) {
        $xml .= '  <data>' . "\n";
        foreach ($data['data'] as $item) {
            $xml .= '    <record>' . "\n";
            foreach ($item as $key => $value) {
                $xml .= '      <' . $key . '>' . htmlspecialchars($value) . '</' . $key . '>' . "\n";
            }
            $xml .= '    </record>' . "\n";
        }
        $xml .= '  </data>' . "\n";
    }
    
    $xml .= '</export>';
    return $xml;
}
?>
