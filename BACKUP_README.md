# SSI Bundle Backup System

This backup system provides automated database backups for the SSI Bundle application with email notifications and file management.

## Features

- ✅ **Automated Database Backups** - Complete MySQL database dumps
- ✅ **Email Notifications** - Get notified when backups succeed or fail
- ✅ **File Management** - View, download, and delete backup files
- ✅ **Automatic Cleanup** - Remove old backups to save disk space
- ✅ **Web Interface** - Easy-to-use backup management dashboard
- ✅ **Logging** - Detailed logs of all backup operations
- ✅ **Cross-Platform** - Works on Windows, Linux, and macOS

## Files

### Core Backup Files

- `backup_system.php` - Main backup script
- `backup_manager.php` - Web interface for managing backups
- `get_backup_status.php` - API for backup status information

### Generated Files

- `backups/` - Directory containing backup files
- `backup_log.log` - Log file with backup history

## Setup Instructions

### 1. Database Configuration

The backup system uses your existing `config.php` file for database connection details.

### 2. Email Configuration (Optional)

To enable email notifications, edit `backup_system.php`:

```php
// Email configuration
$emailFrom     = 'your-email@gmail.com';      // Your Gmail address
$emailTo       = 'admin@hospital.com';        // Admin email address
$emailPassword = 'your-app-password';         // Gmail App Password
$enableEmail   = true; // Set to true to enable
```

**Note:** For Gmail, you need to:

1. Enable 2-factor authentication
2. Generate an App Password
3. Use the App Password (not your regular password)

### 3. PHPMailer Setup (Optional)

For email notifications, download PHPMailer:

1. Download from: https://github.com/PHPMailer/PHPMailer
2. Extract to a `phpmailer/` folder in your project
3. The system will work without PHPMailer (email notifications will be skipped)

### 4. Directory Permissions

Ensure the web server has write permissions to:

- `backups/` directory (will be created automatically)
- `backup_log.log` file (will be created automatically)

## Usage

### Manual Backup

1. **Via Web Interface:**

   - Go to Admin Panel → Backup
   - Click "Create Backup" button
   - Wait for completion

2. **Via Direct URL:**

   - Visit: `http://your-domain/backup_system.php`
   - Backup will be created automatically

3. **Via Command Line:**
   ```bash
   php backup_system.php
   ```

### Automated Backups

Set up a cron job (Linux/macOS) or Task Scheduler (Windows):

**Linux/macOS (cron):**

```bash
# Daily backup at 2 AM
0 2 * * * /usr/bin/php /path/to/your/project/backup_system.php

# Weekly backup on Sundays at 3 AM
0 3 * * 0 /usr/bin/php /path/to/your/project/backup_system.php
```

**Windows (Task Scheduler):**

1. Open Task Scheduler
2. Create Basic Task
3. Set trigger (daily/weekly)
4. Action: Start a program
5. Program: `C:\xampp\php\php.exe`
6. Arguments: `C:\path\to\your\project\backup_system.php`

### Managing Backups

#### Web Interface

1. Go to Admin Panel → Backup
2. View all backup files with details
3. Download backup files
4. Delete old backups
5. View backup logs

#### File System

- Backup files are stored in: `backups/`
- File naming: `ssi_bundle_YYYY-MM-DD_HH-MM-SS.sql`
- Log file: `backup_log.log`

## Configuration Options

Edit `backup_system.php` to customize:

```php
$backupDir  = __DIR__ . '/backups/';  // Backup directory
$deleteOld  = true;                   // Enable automatic cleanup
$retainDays = 7;                      // Keep backups for 7 days
$compress   = false;                  // Enable compression (future feature)
```

## Backup Contents

The backup includes all database tables:

- `patients` - Patient records and SSI data
- `nurses` - Nurse accounts and authentication
- `surgeons` - Surgeon directory
- `audit_logs` - System audit trail
- All other application tables

## Security Considerations

1. **File Access:** Backup files contain sensitive data. Ensure proper file permissions
2. **Email Security:** Use App Passwords for Gmail, not regular passwords
3. **Network Security:** Consider encrypting backup files for transmission
4. **Storage:** Store backups in a secure location separate from the main server

## Troubleshooting

### Common Issues

1. **"mysqldump not found"**

   - Install MySQL client tools
   - Verify mysqldump path in the script

2. **"Permission denied"**

   - Check directory permissions
   - Ensure web server can write to backup directory

3. **"Email failed to send"**

   - Verify Gmail credentials
   - Check if PHPMailer is installed
   - Ensure 2FA is enabled for Gmail

4. **"Backup file is empty"**
   - Check database connection
   - Verify database name in config.php
   - Check MySQL user permissions

### Log Analysis

Check `backup_log.log` for detailed error messages and operation history.

## API Endpoints

### Get Backup Status

```
GET /get_backup_status.php
```

Returns JSON with backup statistics and recent files.

### Create Backup (AJAX)

```
GET /backup_system.php?ajax=1
```

Returns JSON response with backup result.

## Support

For issues or questions:

1. Check the backup log file
2. Verify all configuration settings
3. Test database connectivity
4. Ensure proper file permissions

## Version History

- **v1.0** - Initial release with basic backup functionality
- **v1.1** - Added web interface and file management
- **v1.2** - Added email notifications and logging
- **v1.3** - Added automatic cleanup and status API
