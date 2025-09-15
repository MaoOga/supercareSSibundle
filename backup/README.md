# SSI Bundle Database Backup System

## 📋 Overview

The SSI Bundle Database Backup System is a comprehensive backup solution that automatically creates and manages MySQL database backups for the SuperCare Hospital SSI (Surgical Site Infection) monitoring system. It ensures complete data preservation of all patient records, user accounts, audit logs, and system configurations.

## 🔧 How It Works

### Backup Process

1. **Database Connection**: Connects to the `supercare_ssi` MySQL database
2. **mysqldump Execution**: Uses MySQL's native `mysqldump` tool to create complete database dumps
3. **File Generation**: Creates timestamped SQL files in the `backups/` directory
4. **Email Notifications**: Sends backup completion alerts (if configured)
5. **Audit Logging**: Records backup activities in the audit system
6. **Retention Management**: Automatically removes old backups based on retention policy

### Technical Details

- **Backup Method**: Full MySQL database dump using `mysqldump`
- **File Format**: SQL dump files with complete structure and data
- **Compression**: Not enabled by default (configurable)
- **Database**: `supercare_ssi` (complete database backup)
- **File Naming**: `ssi_bundle_YYYY-MM-DD_HH-MM-SS.sql`

## 🚀 Usage

### Manual Backup Creation

#### From Super Admin Dashboard

1. **Access**: Login to Super Admin Dashboard
2. **Navigate**: Go to "Database Backup Management" section
3. **Create**: Click "Create Backup" button
4. **Monitor**: Watch for success/error messages
5. **Download**: Use download button to save backup files

#### Direct API Call

```bash
# Create backup via direct URL
GET /backup/backup_system.php?ajax=1
```

#### Command Line (if needed)

```bash
# Navigate to backup directory
cd backup/

# Run backup script
php backup_system.php
```

### Automatic Backup (Configuration Required)

The system supports automatic backups but requires configuration:

#### Cron Job Setup (Linux/Mac)

```bash
# Add to crontab for daily backups at 2 AM
0 2 * * * cd /path/to/supercareSSibundle/backup && php backup_system.php
```

#### Windows Task Scheduler

1. Open Task Scheduler
2. Create Basic Task
3. Set trigger (daily, weekly, etc.)
4. Set action: `php backup_system.php`
5. Set start in: `C:\New Xampp\htdocs\supercareSSibundle\backup`

## ⚙️ Configuration

### Backup Settings (in `backup_system.php`)

```php
// Database Configuration
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'supercare_ssi';

// Backup Configuration
$backupDir = __DIR__ . '/backups/';
$deleteOld = true;           // Enable automatic cleanup
$retainDays = 90;           // Keep backups for 90 days
$compress = false;          // Compression disabled by default
$enableEmail = true;        // Email notifications enabled
```

### Email Configuration

- **From**: Uses `EMAIL_USERNAME` from email config
- **To**: Sends notifications to same email address
- **Subject**: "✅ SSI Bundle Database Backup Completed Successfully"
- **Content**: Includes backup details, file size, and timestamp

## 📁 File Management

### Backup Files Location

```
backup/
├── backups/                    # Backup files directory
│   ├── ssi_bundle_2025-09-14_00-28-26.sql
│   ├── ssi_bundle_2025-09-14_00-32-05.sql
│   └── ssi_bundle_2025-09-14_22-29-35.sql
├── backup_system.php          # Main backup script
├── backup_manager.php         # Backup management utilities
├── download_backup_simple.php # Download handler
├── get_backup_files_simple.php # File listing API
└── delete_backup_simple.php   # File deletion handler
```

### File Operations

#### Download Backups

- **Method**: Click download button in dashboard
- **URL**: `/backup/download_backup_simple.php?file=filename.sql`
- **Security**: Filename validation and path sanitization

#### Delete Backups

- **Method**: Click delete button in dashboard
- **Confirmation**: Custom popup confirmation dialog
- **API**: `/backup/delete_backup_simple.php`

#### List Backups

- **API**: `/backup/get_backup_files_simple.php`
- **Response**: JSON with file details (name, size, date)

## 📅 Retention Policy

### Current Settings

- **Retention Period**: **90 days** (3 months)
- **Automatic Cleanup**: Enabled
- **Manual Override**: Available through dashboard

### Retention Logic

```php
$retainDays = 90;  // Keep backups for 90 days
$deleteOld = true; // Enable automatic deletion
```

### File Age Calculation

- Backups older than 90 days are automatically deleted
- Manual deletion available through dashboard
- Audit logs track all deletion activities

## 🔒 Security Features

### File Security

- **Path Validation**: Prevents directory traversal attacks
- **Filename Sanitization**: Uses `basename()` for security
- **Access Control**: Super admin authentication required
- **File Type Validation**: Only `.sql` files allowed

### Data Security

- **Complete Encryption**: Database passwords are hashed
- **Audit Trail**: All backup activities logged
- **Session Management**: Secure session handling
- **Access Logging**: IP addresses and user agents tracked

## 📊 What Gets Backed Up

### Complete Database Backup Includes:

#### 🏥 Patient Medical Data

- Patient information (name, age, sex, UHID, contact details)
- Medical records (diagnosis, surgical procedures, dates)
- Surgical details (DOA, DOS, DOD, surgeon, duration)
- Surgical preparation (pre-op bath, hair removal)
- Implanted materials (metal, grafts, patches, shunts)
- Drains and post-operative monitoring
- Antibiotic usage records
- Wound complications and management
- Risk factors and assessment data
- Infection prevention notes and signatures

#### 👥 User Management

- Nurse accounts and credentials
- Admin accounts and permissions
- Super admin accounts
- Surgeon profiles
- Session data and activity logs

#### 🔐 System Data

- Complete audit logs
- Login attempts and security logs
- OTP verification records
- System configuration
- Database structure and relationships

## 🚨 Troubleshooting

### Common Issues

#### Backup Creation Fails

```bash
# Check mysqldump path
which mysqldump

# Verify database connection
mysql -u root -p supercare_ssi

# Check file permissions
ls -la backup/backups/
```

#### Download Issues

- **Check file exists**: Verify file in `backups/` directory
- **Check permissions**: Ensure web server can read files
- **Check path**: Verify correct relative path in download URL

#### Email Notifications Not Working

- **Check email config**: Verify `email_config.php` settings
- **Check PHPMailer**: Ensure PHPMailer is properly installed
- **Check SMTP**: Verify SMTP server settings

### Error Messages

#### "mysqldump not found"

- **Solution**: Install MySQL client tools or update path in script
- **Windows**: Ensure MySQL bin directory is in PATH
- **Linux**: Install `mysql-client` package

#### "Backup directory could not be created"

- **Solution**: Create directory manually with proper permissions

```bash
mkdir -p backup/backups/
chmod 755 backup/backups/
```

#### "Database connection failed"

- **Solution**: Check database credentials in `config.php`
- **Verify**: Database server is running
- **Check**: Network connectivity

## 📈 Monitoring and Logs

### Backup Logs

- **Location**: `backup_log.log`
- **Content**: Backup creation, success/failure, file sizes
- **Format**: Timestamped entries with detailed information

### Audit Logs

- **Table**: `admin_audit_logs`
- **Actions**: CREATE, UPDATE, DELETE, BACKUP, RESTORE
- **Details**: User, timestamp, file information, status

### System Logs

- **PHP Error Log**: Check web server error logs
- **MySQL Log**: Check MySQL error logs
- **Application Log**: Check application-specific logs

## 🔄 Backup Restoration

### Manual Restoration

```bash
# Stop web application
# Restore from backup file
mysql -u root -p supercare_ssi < backup_file.sql

# Verify restoration
mysql -u root -p -e "USE supercare_ssi; SHOW TABLES;"
```

### Automated Restoration (Future Feature)

- Restoration scripts planned for future releases
- Point-in-time recovery capabilities
- Automated verification and testing

## 📞 Support

### System Requirements

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Web Server**: Apache/Nginx
- **Disk Space**: Minimum 1GB for backups
- **Memory**: 256MB PHP memory limit

### Contact Information

- **Technical Support**: System Administrator
- **Documentation**: This README file
- **Issues**: Check audit logs and error logs first

---

## 📝 Version Information

- **Backup System Version**: 1.0
- **Last Updated**: 2025-01-14
- **Compatible With**: SSI Bundle System v1.0
- **Database Version**: supercare_ssi v1.0

---

**⚠️ Important**: Always test backup restoration procedures in a development environment before implementing in production. Regular backup verification is recommended to ensure data integrity.
