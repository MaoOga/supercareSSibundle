-- Create comprehensive audit log table for SSI Bundle System
CREATE TABLE IF NOT EXISTS admin_audit_logs (
    audit_id INT AUTO_INCREMENT PRIMARY KEY,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    admin_user VARCHAR(100) NOT NULL,
    action_type ENUM(
        'CREATE', 'UPDATE', 'DELETE', 'LOGIN', 'LOGOUT', 'BACKUP', 'RESTORE', 
        'EXPORT', 'IMPORT', 'PASSWORD_RESET', 'ACCOUNT_CREATE', 'ACCOUNT_DELETE',
        'SETTINGS_CHANGE', 'DATA_ACCESS', 'SYSTEM_MAINTENANCE'
    ) NOT NULL,
    entity_type ENUM(
        'NURSE', 'SURGEON', 'PATIENT', 'BACKUP', 'SYSTEM', 'SETTINGS', 'AUDIT_LOG'
    ) NOT NULL,
    entity_id VARCHAR(50),
    entity_name VARCHAR(255),
    description TEXT NOT NULL,
    details_before JSON,
    details_after JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    session_id VARCHAR(255),
    status ENUM('SUCCESS', 'FAILED', 'PENDING') DEFAULT 'SUCCESS',
    error_message TEXT,
    INDEX idx_timestamp (timestamp),
    INDEX idx_admin_user (admin_user),
    INDEX idx_action_type (action_type),
    INDEX idx_entity_type (entity_type),
    INDEX idx_entity_id (entity_id)
);

-- Insert initial audit entry for table creation
INSERT INTO admin_audit_logs (
    admin_user, 
    action_type, 
    entity_type, 
    entity_name, 
    description, 
    details_after
) VALUES (
    'SYSTEM', 
    'SYSTEM_MAINTENANCE', 
    'SYSTEM', 
    'Audit System', 
    'Audit log table created and initialized', 
    '{"table": "admin_audit_logs", "columns": ["audit_id", "timestamp", "admin_user", "action_type", "entity_type", "entity_id", "entity_name", "description", "details_before", "details_after", "ip_address", "user_agent", "session_id", "status", "error_message"]}'
);
