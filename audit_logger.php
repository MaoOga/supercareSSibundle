<?php
/**
 * Professional Audit Logger for SSI Bundle System
 * Tracks all admin activities with detailed information
 */

require_once 'config.php';

class AuditLogger {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Log an admin activity
     */
    public function log($adminUser, $actionType, $entityType, $entityId = null, $entityName = null, $description = '', $detailsBefore = null, $detailsAfter = null, $status = 'SUCCESS', $errorMessage = null) {
        try {
            $sql = "INSERT INTO admin_audit_logs (
                admin_user, action_type, entity_type, entity_id, entity_name, 
                description, details_before, details_after, ip_address, 
                user_agent, session_id, status, error_message
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $adminUser,
                $actionType,
                $entityType,
                $entityId,
                $entityName,
                $description,
                $detailsBefore ? json_encode($detailsBefore) : null,
                $detailsAfter ? json_encode($detailsAfter) : null,
                $this->getClientIP(),
                $_SERVER['HTTP_USER_AGENT'] ?? null,
                session_id(),
                $status,
                $errorMessage
            ]);
            
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            error_log("Audit logging failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log nurse account creation
     */
    public function logNurseCreate($adminUser, $nurseData) {
        return $this->log(
            $adminUser,
            'CREATE',
            'NURSE',
            $nurseData['id'] ?? null,
            $nurseData['name'] ?? 'Unknown Nurse',
            "Created new nurse account: {$nurseData['name']}",
            null,
            $nurseData
        );
    }
    
    /**
     * Log nurse account update
     */
    public function logNurseUpdate($adminUser, $nurseId, $nurseName, $detailsBefore, $detailsAfter) {
        return $this->log(
            $adminUser,
            'UPDATE',
            'NURSE',
            $nurseId,
            $nurseName,
            "Updated nurse account: {$nurseName}",
            $detailsBefore,
            $detailsAfter
        );
    }
    
    /**
     * Log nurse account deletion
     */
    public function logNurseDelete($adminUser, $nurseId, $nurseName, $detailsBefore) {
        return $this->log(
            $adminUser,
            'DELETE',
            'NURSE',
            $nurseId,
            $nurseName,
            "Deleted nurse account: {$nurseName}",
            $detailsBefore,
            null
        );
    }
    
    /**
     * Log nurse login attempts
     */
    public function logNurseLogin($adminUser, $nurseId, $nurseIdCode, $nurseName, $nurseData, $description, $status = 'SUCCESS') {
        return $this->log(
            $adminUser,
            'LOGIN',
            'NURSE',
            $nurseId,
            $nurseName ?? $nurseIdCode,
            $description,
            null,
            $nurseData,
            $status
        );
    }
    
    /**
     * Log surgeon account creation
     */
    public function logSurgeonCreate($adminUser, $surgeonData) {
        return $this->log(
            $adminUser,
            'CREATE',
            'SURGEON',
            $surgeonData['id'] ?? null,
            $surgeonData['name'] ?? 'Unknown Surgeon',
            "Created new surgeon account: {$surgeonData['name']}",
            null,
            $surgeonData
        );
    }
    
    /**
     * Log surgeon account update
     */
    public function logSurgeonUpdate($adminUser, $surgeonId, $surgeonName, $detailsBefore, $detailsAfter) {
        return $this->log(
            $adminUser,
            'UPDATE',
            'SURGEON',
            $surgeonId,
            $surgeonName,
            "Updated surgeon account: {$surgeonName}",
            $detailsBefore,
            $detailsAfter
        );
    }
    
    /**
     * Log surgeon account deletion
     */
    public function logSurgeonDelete($adminUser, $surgeonId, $surgeonName, $detailsBefore) {
        return $this->log(
            $adminUser,
            'DELETE',
            'SURGEON',
            $surgeonId,
            $surgeonName,
            "Deleted surgeon account: {$surgeonName}",
            $detailsBefore,
            null
        );
    }
    
    /**
     * Log backup creation
     */
    public function logBackupCreate($adminUser, $backupFile, $fileSize, $status = 'SUCCESS', $errorMessage = null) {
        return $this->log(
            $adminUser,
            'BACKUP',
            'BACKUP',
            basename($backupFile),
            'Database Backup',
            "Database backup created: " . basename($backupFile) . " ({$fileSize})",
            null,
            [
                'file' => basename($backupFile),
                'size' => $fileSize,
                'path' => $backupFile
            ],
            $status,
            $errorMessage
        );
    }
    
    /**
     * Log backup deletion
     */
    public function logBackupDelete($adminUser, $backupFile, $fileSize) {
        return $this->log(
            $adminUser,
            'DELETE',
            'BACKUP',
            basename($backupFile),
            'Database Backup',
            "Database backup deleted: " . basename($backupFile) . " ({$fileSize})",
            [
                'file' => basename($backupFile),
                'size' => $fileSize,
                'path' => $backupFile
            ],
            null
        );
    }
    
    /**
     * Log data export
     */
    public function logDataExport($adminUser, $exportType, $recordCount) {
        return $this->log(
            $adminUser,
            'EXPORT',
            'SYSTEM',
            null,
            'Data Export',
            "Exported {$exportType} data ({$recordCount} records)",
            null,
            [
                'type' => $exportType,
                'count' => $recordCount
            ]
        );
    }
    
    /**
     * Log data import
     */
    public function logDataImport($adminUser, $importType, $recordCount) {
        return $this->log(
            $adminUser,
            'IMPORT',
            'SYSTEM',
            null,
            'Data Import',
            "Imported {$importType} data ({$recordCount} records)",
            null,
            [
                'type' => $importType,
                'count' => $recordCount
            ]
        );
    }
    
    /**
     * Log admin login
     */
    public function logAdminLogin($adminUser, $status = 'SUCCESS', $errorMessage = null) {
        return $this->log(
            $adminUser,
            'LOGIN',
            'SYSTEM',
            null,
            'Admin Login',
            "Admin login attempt for user: {$adminUser}",
            null,
            ['user' => $adminUser],
            $status,
            $errorMessage
        );
    }
    
    /**
     * Log super admin login
     */
    public function logSuperAdminLogin($superAdminUser, $status = 'SUCCESS', $errorMessage = null) {
        return $this->log(
            $superAdminUser,
            'LOGIN',
            'SYSTEM',
            null,
            'Super Admin Login',
            "Super admin login attempt for user: {$superAdminUser}",
            null,
            ['user' => $superAdminUser, 'user_type' => 'super_admin'],
            $status,
            $errorMessage
        );
    }
    
    /**
     * Log admin logout
     */
    public function logAdminLogout($adminUser) {
        return $this->log(
            $adminUser,
            'LOGOUT',
            'SYSTEM',
            null,
            'Admin Logout',
            "Admin logout for user: {$adminUser}",
            null,
            ['user' => $adminUser]
        );
    }
    
    /**
     * Log password reset
     */
    public function logPasswordReset($adminUser, $targetUser, $status = 'SUCCESS', $errorMessage = null) {
        return $this->log(
            $adminUser,
            'PASSWORD_RESET',
            'SYSTEM',
            null,
            'Password Reset',
            "Password reset for user: {$targetUser}",
            null,
            ['target_user' => $targetUser],
            $status,
            $errorMessage
        );
    }
    
    /**
     * Log system maintenance
     */
    public function logSystemMaintenance($adminUser, $description, $details = null) {
        return $this->log(
            $adminUser,
            'SYSTEM_MAINTENANCE',
            'SYSTEM',
            null,
            'System Maintenance',
            $description,
            null,
            $details
        );
    }
    
    /**
     * Log data access
     */
    public function logDataAccess($adminUser, $entityType, $entityId, $entityName, $accessType) {
        return $this->log(
            $adminUser,
            'DATA_ACCESS',
            $entityType,
            $entityId,
            $entityName,
            "Data access: {$accessType} on {$entityType} - {$entityName}",
            null,
            [
                'access_type' => $accessType,
                'entity_type' => $entityType,
                'entity_id' => $entityId
            ]
        );
    }
    
    /**
     * Get client IP address
     */
    private function getClientIP() {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    }
    
    /**
     * Get audit logs with filtering and pagination
     */
    public function getAuditLogs($filters = [], $page = 1, $limit = 50) {
        $whereConditions = [];
        $params = [];
        
        // Apply filters
        if (!empty($filters['admin_user'])) {
            $whereConditions[] = "admin_user LIKE ?";
            $params[] = "%{$filters['admin_user']}%";
        }
        
        if (!empty($filters['action_type'])) {
            $whereConditions[] = "action_type = ?";
            $params[] = $filters['action_type'];
        }
        
        if (!empty($filters['entity_type'])) {
            $whereConditions[] = "entity_type = ?";
            $params[] = $filters['entity_type'];
        }
        
        if (!empty($filters['start_date'])) {
            $whereConditions[] = "timestamp >= ?";
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $whereConditions[] = "timestamp <= ?";
            $params[] = $filters['end_date'];
        }
        
        if (!empty($filters['status'])) {
            $whereConditions[] = "status = ?";
            $params[] = $filters['status'];
        }
        
        // Build query
        $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
        $offset = ($page - 1) * $limit;
        
        // Get total count
        $countSql = "SELECT COUNT(*) FROM admin_audit_logs {$whereClause}";
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalCount = $countStmt->fetchColumn();
        
        // Get logs
        $sql = "SELECT * FROM admin_audit_logs {$whereClause} ORDER BY timestamp DESC LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'logs' => $logs,
            'total_count' => $totalCount,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($totalCount / $limit)
        ];
    }
    
    /**
     * Get audit statistics
     */
    public function getAuditStats($days = 30) {
        $sql = "SELECT 
                    action_type,
                    entity_type,
                    status,
                    COUNT(*) as count,
                    DATE(timestamp) as date
                FROM admin_audit_logs 
                WHERE timestamp >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY action_type, entity_type, status, DATE(timestamp)
                ORDER BY date DESC, count DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Clean old audit logs (keep last 90 days by default)
     */
    public function cleanOldLogs($daysToKeep = 90) {
        $sql = "DELETE FROM admin_audit_logs WHERE timestamp < DATE_SUB(CURDATE(), INTERVAL ? DAY)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$daysToKeep]);
        return $stmt->rowCount();
    }
    
    /**
     * Log patient form creation by nurse
     */
    public function logPatientCreate($nurseId, $patientId, $patientName, $detailsAfter) {
        return $this->log(
            $nurseId,
            'CREATE',
            'PATIENT',
            $patientId,
            $patientName,
            "Patient form created: {$patientName}",
            null,
            $detailsAfter
        );
    }
    
    /**
     * Log patient form update by nurse
     */
    public function logPatientUpdate($nurseId, $patientId, $patientName, $detailsAfter) {
        return $this->log(
            $nurseId,
            'UPDATE',
            'PATIENT',
            $patientId,
            $patientName,
            "Patient form updated: {$patientName}",
            null,
            $detailsAfter
        );
    }
    
    /**
     * Log row deletion from patient forms
     */
    public function logRowDelete($nurseId, $patientId, $tableType, $rowNumber, $detailsAfter) {
        return $this->log(
            $nurseId,
            'DELETE_ROW',
            'PATIENT_FORM',
            $patientId,
            "Patient ID: {$patientId}",
            "Deleted row {$rowNumber} from {$tableType} table",
            null,
            $detailsAfter
        );
    }
    
    /**
     * Get a specific audit log by ID
     */
    public function getAuditLogById($auditId) {
        $sql = "SELECT * FROM admin_audit_logs WHERE audit_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$auditId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Create global audit logger instance
$auditLogger = new AuditLogger($pdo);
?>
