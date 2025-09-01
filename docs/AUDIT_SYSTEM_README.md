# Audit System Setup Guide

## Overview

The SSI Bundle system includes a comprehensive audit logging system that tracks all admin activities for security and compliance purposes.

## Features

- **Real-time Activity Tracking**: Logs all admin actions (CREATE, UPDATE, DELETE, LOGIN, etc.)
- **Detailed Information**: Records user, action type, entity, timestamp, IP address, and more
- **Visual Dashboard**: Beautiful charts and statistics showing system activity
- **Filtering & Search**: Advanced filtering by action type, entity, date range, and status
- **Export Functionality**: Export audit logs for compliance reporting
- **Mobile Responsive**: Works perfectly on desktop and mobile devices

## Setup Instructions

### 1. Database Setup

First, ensure your database is properly configured in `config.php`:

```php
$host = 'localhost';
$dbname = 'supercare_ssi';
$username = 'root';
$password = '';
```

### 2. Create Audit Table

Run the setup script to create the audit table:

```
http://your-domain/setup_audit_system.php
```

This will:

- Create the `admin_audit_logs` table if it doesn't exist
- Verify the table structure
- Show current status

### 3. Test the System

Run the test script to populate with sample data:

```
http://your-domain/test_audit_system.php
```

This will:

- Test database connectivity
- Create sample audit entries
- Test API endpoints
- Verify everything is working

### 4. Access the Audit Dashboard

Once setup is complete, access the audit log page:

```
http://your-domain/audit_log.html
```

## Action Types Tracked

### Core Actions

- **CREATE**: Creating new records (nurses, surgeons, patients)
- **UPDATE**: Modifying existing records
- **DELETE**: Removing records from the system
- **LOGIN**: User authentication activities
- **LOGOUT**: User logout activities

### System Actions

- **BACKUP**: Database backup operations
- **EXPORT**: Data export operations
- **IMPORT**: Data import operations
- **PASSWORD_RESET**: Password reset activities
- **SYSTEM_MAINTENANCE**: System maintenance tasks

### Entity Types

- **NURSE**: Nurse-related activities
- **SURGEON**: Surgeon-related activities
- **PATIENT**: Patient-related activities
- **BACKUP**: Backup system activities
- **SYSTEM**: System-level activities

### Status Types

- **SUCCESS**: Successfully completed actions
- **FAILED**: Failed or error actions
- **PENDING**: Actions in progress

## API Endpoints

### Get Audit Statistics

```
GET /get_audit_stats.php?days=30
```

Returns:

- Total activities count
- Success rate
- Action type distribution
- Daily activity trends
- Top users

### Get Audit Logs

```
GET /get_audit_logs.php?page=1&limit=50&action_type=CREATE&entity_type=NURSE
```

Parameters:

- `page`: Page number (default: 1)
- `limit`: Records per page (default: 50, max: 100)
- `action_type`: Filter by action type
- `entity_type`: Filter by entity type
- `status`: Filter by status
- `start_date`: Filter by start date
- `end_date`: Filter by end date

## Integration with Existing Code

The audit system is automatically integrated with existing admin functions:

### Nurse Management

- `create_nurse.php` - Logs nurse creation
- `delete_nurse.php` - Logs nurse deletion
- `update_nurse.php` - Logs nurse updates

### Surgeon Management

- `create_surgeon.php` - Logs surgeon creation
- `delete_surgeon.php` - Logs surgeon deletion
- `update_surgeon.php` - Logs surgeon updates

### Backup System

- `backup_manager.php` - Logs backup operations
- `backup_system.php` - Logs automated backups

## Manual Audit Logging

You can manually log audit events using the AuditLogger class:

```php
require_once '../audit/audit_logger.php';

$auditLogger = new AuditLogger($pdo);

// Log a custom event
$auditLogger->log(
    'admin',                    // Admin user
    'CREATE',                   // Action type
    'NURSE',                    // Entity type
    $nurseId,                   // Entity ID
    $nurseName,                 // Entity name
    'Created new nurse account', // Description
    null,                       // Details before
    $nurseData,                 // Details after
    'SUCCESS'                   // Status
);
```

## Dashboard Features

### Statistics Cards

- **Total Activities**: Count of all logged activities
- **Success Rate**: Percentage of successful operations
- **Active Users**: Number of unique admin users
- **Today's Activities**: Activities performed today

### Charts

- **Activity by Action Type**: Doughnut chart showing action distribution
- **Daily Activity Trend**: Line chart showing activity over time

### Filters

- **Action Type**: Filter by CREATE, UPDATE, DELETE, etc.
- **Entity Type**: Filter by NURSE, SURGEON, PATIENT, etc.
- **Status**: Filter by SUCCESS, FAILED, PENDING
- **Date Range**: Filter by time period (7, 30, 90, 365 days)

### Table Features

- **Pagination**: Navigate through large datasets
- **Sorting**: Sort by timestamp (newest first)
- **Search**: Filter by specific criteria
- **Export**: Download audit logs as CSV
- **Details**: Click any row to view detailed information

## Mobile Support

The audit dashboard is fully responsive and includes:

- Mobile-optimized layout
- Touch-friendly interface
- Bottom navigation for mobile devices
- Optimized charts for small screens
- Card-based layout for mobile tables

## Security Features

- **IP Address Tracking**: Records client IP addresses
- **User Agent Logging**: Tracks browser/client information
- **Session Tracking**: Links activities to user sessions
- **Error Logging**: Records failed operations with error messages
- **Data Integrity**: Before/after data for update operations

## Maintenance

### Cleanup Old Logs

The system includes a cleanup function to remove old audit logs:

```php
$auditLogger->cleanOldLogs(90); // Keep last 90 days
```

### Performance Optimization

- Indexed database columns for fast queries
- Pagination to handle large datasets
- Efficient JSON storage for complex data
- Optimized queries with proper filtering

## Troubleshooting

### Common Issues

1. **Database Connection Error**

   - Check `config.php` database settings
   - Ensure MySQL service is running
   - Verify database exists

2. **Audit Table Not Found**

   - Run `setup_audit_system.php`
   - Check file permissions
   - Verify SQL execution

3. **API Endpoints Not Working**

   - Check file permissions
   - Verify PHP configuration
   - Check browser console for errors

4. **No Data Showing**
   - Run `test_audit_system.php` to populate sample data
   - Check if admin functions are being called
   - Verify audit logging is integrated

### Debug Mode

Enable error reporting in `config.php` for debugging:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Support

For issues or questions:

1. Check the troubleshooting section above
2. Run the test scripts to verify functionality
3. Check browser console for JavaScript errors
4. Review server error logs

The audit system is designed to be robust and self-contained, providing comprehensive activity tracking for the SSI Bundle system.
