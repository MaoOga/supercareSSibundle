<?php
/**
 * API endpoint to retrieve audit statistics and analytics
 */

require_once 'audit_logger.php';

header('Content-Type: application/json');

// Handle CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $auditLogger = new AuditLogger($pdo);
    
    // Get query parameters
    $days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
    if ($days < 1 || $days > 365) $days = 30;
    
    // Get audit statistics
    $stats = $auditLogger->getAuditStats($days);
    
    // Process statistics for better display
    $processedStats = [];
    $actionTypeCounts = [];
    $entityTypeCounts = [];
    $statusCounts = [];
    $dailyActivity = [];
    
    foreach ($stats as $stat) {
        $date = $stat['date'];
        $actionType = $stat['action_type'];
        $entityType = $stat['entity_type'];
        $status = $stat['status'];
        $count = $stat['count'];
        
        // Count by action type
        if (!isset($actionTypeCounts[$actionType])) {
            $actionTypeCounts[$actionType] = 0;
        }
        $actionTypeCounts[$actionType] += $count;
        
        // Count by entity type
        if (!isset($entityTypeCounts[$entityType])) {
            $entityTypeCounts[$entityType] = 0;
        }
        $entityTypeCounts[$entityType] += $count;
        
        // Count by status
        if (!isset($statusCounts[$status])) {
            $statusCounts[$status] = 0;
        }
        $statusCounts[$status] += $count;
        
        // Daily activity
        if (!isset($dailyActivity[$date])) {
            $dailyActivity[$date] = 0;
        }
        $dailyActivity[$date] += $count;
    }
    
    // Convert to arrays for easier frontend consumption
    $actionTypeData = [];
    foreach ($actionTypeCounts as $actionType => $count) {
        $actionTypeData[] = [
            'action_type' => $actionType,
            'count' => $count,
            'percentage' => array_sum($actionTypeCounts) > 0 ? round(($count / array_sum($actionTypeCounts)) * 100, 1) : 0
        ];
    }
    
    $entityTypeData = [];
    foreach ($entityTypeCounts as $entityType => $count) {
        $entityTypeData[] = [
            'entity_type' => $entityType,
            'count' => $count,
            'percentage' => array_sum($entityTypeCounts) > 0 ? round(($count / array_sum($entityTypeCounts)) * 100, 1) : 0
        ];
    }
    
    $statusData = [];
    foreach ($statusCounts as $status => $count) {
        $statusData[] = [
            'status' => $status,
            'count' => $count,
            'percentage' => array_sum($statusCounts) > 0 ? round(($count / array_sum($statusCounts)) * 100, 1) : 0
        ];
    }
    
    // Sort daily activity by date
    ksort($dailyActivity);
    $dailyActivityData = [];
    foreach ($dailyActivity as $date => $count) {
        $dailyActivityData[] = [
            'date' => $date,
            'count' => $count,
            'formatted_date' => date('M j', strtotime($date))
        ];
    }
    
    // Calculate summary statistics
    $totalActivities = array_sum($actionTypeCounts);
    $successfulActivities = $statusCounts['SUCCESS'] ?? 0;
    $failedActivities = $statusCounts['FAILED'] ?? 0;
    $successRate = $totalActivities > 0 ? round(($successfulActivities / $totalActivities) * 100, 1) : 0;
    
    // Get most active admin users
    $sql = "SELECT admin_user, COUNT(*) as count 
            FROM admin_audit_logs 
            WHERE timestamp >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY admin_user 
            ORDER BY count DESC 
            LIMIT 10";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$days]);
    $topUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get recent activity (last 10 entries)
    $sql = "SELECT admin_user, action_type, entity_type, entity_name, timestamp, status
            FROM admin_audit_logs 
            ORDER BY timestamp DESC 
            LIMIT 10";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process recent activity
    $processedRecentActivity = [];
    foreach ($recentActivity as $activity) {
        $processedRecentActivity[] = [
            'admin_user' => $activity['admin_user'],
            'action_type' => $activity['action_type'],
            'entity_type' => $activity['entity_type'],
            'entity_name' => $activity['entity_name'],
            'timestamp' => $activity['timestamp'],
            'status' => $activity['status'],
            'formatted_time' => date('M j, Y g:i A', strtotime($activity['timestamp'])),
            'time_ago' => getTimeAgo($activity['timestamp'])
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'summary' => [
                'total_activities' => $totalActivities,
                'successful_activities' => $successfulActivities,
                'failed_activities' => $failedActivities,
                'success_rate' => $successRate,
                'period_days' => $days
            ],
            'action_types' => $actionTypeData,
            'entity_types' => $entityTypeData,
            'status_distribution' => $statusData,
            'daily_activity' => $dailyActivityData,
            'top_users' => $topUsers,
            'recent_activity' => $processedRecentActivity
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Error fetching audit statistics: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching audit statistics',
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
