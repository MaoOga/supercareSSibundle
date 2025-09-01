<?php
/**
 * API endpoint to retrieve audit logs with filtering and pagination
 */

require_once '../database/config.php';
require_once 'audit_logger.php';

header('Content-Type: application/json');

// Handle CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $auditLogger = new AuditLogger($pdo);
    
    // Get query parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $actionType = $_GET['action_type'] ?? '';
    $entityType = $_GET['entity_type'] ?? '';
    $adminUser = $_GET['admin_user'] ?? '';
    $startDate = $_GET['start_date'] ?? '';
    $endDate = $_GET['end_date'] ?? '';
    $status = $_GET['status'] ?? '';
    $days = isset($_GET['days']) ? (int)$_GET['days'] : 0;
    $isExport = isset($_GET['export']) && $_GET['export'] === 'true';
    $auditId = isset($_GET['audit_id']) ? (int)$_GET['audit_id'] : null;
    
    // Validate parameters
    if ($page < 1) $page = 1;
    if ($limit < 1 || $limit > 100) $limit = 50;
    
    // For export, get all data without pagination
    if ($isExport) {
        $page = 1;
        $limit = 999999; // Get all records
    }
    
    // Build filters
    $filters = [];
    if (!empty($actionType)) $filters['action_type'] = $actionType;
    if (!empty($entityType)) $filters['entity_type'] = $entityType;
    if (!empty($adminUser)) $filters['admin_user'] = $adminUser;
    if (!empty($startDate)) $filters['start_date'] = $startDate;
    if (!empty($endDate)) $filters['end_date'] = $endDate;
    if (!empty($status)) $filters['status'] = $status;
    
    // Handle days filter
    if ($days > 0) {
        $endDate = date('Y-m-d H:i:s');
        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $filters['start_date'] = $startDate;
        $filters['end_date'] = $endDate;
    }
    
    // If audit_id is provided, get specific log entry
    if ($auditId) {
        $result = $auditLogger->getAuditLogById($auditId);
        if (!$result) {
            echo json_encode([
                'success' => false,
                'message' => 'Audit log not found'
            ]);
            exit;
        }
        // Convert single log to array format for consistency
        $result = [
            'logs' => [$result],
            'page' => 1,
            'total_pages' => 1,
            'total_count' => 1,
            'limit' => 1
        ];
    } else {
        // Get audit logs with pagination
        $result = $auditLogger->getAuditLogs($filters, $page, $limit);
    }
    
    // Process logs for better display
    $processedLogs = [];
    foreach ($result['logs'] as $log) {
        $processedLogs[] = [
            'audit_id' => $log['audit_id'],
            'timestamp' => $log['timestamp'],
            'admin_user' => $log['admin_user'],
            'action_type' => $log['action_type'],
            'entity_type' => $log['entity_type'],
            'entity_id' => $log['entity_id'],
            'entity_name' => $log['entity_name'],
            'description' => $log['description'],
            'details_before' => $log['details_before'] ? json_decode($log['details_before'], true) : null,
            'details_after' => $log['details_after'] ? json_decode($log['details_after'], true) : null,
            'ip_address' => $log['ip_address'],
            'status' => $log['status'],
            'error_message' => $log['error_message'],
            'formatted_time' => date('M j, Y g:i A', strtotime($log['timestamp'])),
            'time_ago' => getTimeAgo($log['timestamp'])
        ];
    }
    
    // Prepare response data
    $responseData = [
        'success' => true,
        'data' => [
            'logs' => $processedLogs,
            'pagination' => [
                'current_page' => $result['page'],
                'total_pages' => $result['total_pages'],
                'total_count' => $result['total_count'],
                'limit' => $result['limit'],
                'has_next' => $result['page'] < $result['total_pages'],
                'has_prev' => $result['page'] > 1
            ],
            'filters' => $filters
        ]
    ];
    
    // Add export-specific information
    if ($isExport) {
        $responseData['export_info'] = [
            'export_date' => date('Y-m-d H:i:s'),
            'filters_applied' => $filters,
            'total_records_exported' => count($processedLogs),
            'export_type' => 'audit_logs'
        ];
    }
    
    echo json_encode($responseData);
    
} catch (Exception $e) {
    error_log("Error fetching audit logs: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching audit logs',
        'error' => $e->getMessage()
    ]);
}

/**
 * Get time ago string
 */
function getTimeAgo($timestamp) {
    $time = time() - strtotime($timestamp);
    
    if ($time < 60) {
        return 'Just now';
    } elseif ($time < 3600) {
        $minutes = floor($time / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($time < 86400) {
        $hours = floor($time / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($time < 2592000) {
        $days = floor($time / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        $months = floor($time / 2592000);
        return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
    }
}
?>
