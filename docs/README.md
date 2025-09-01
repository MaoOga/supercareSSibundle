# SuperCare SSI Bundle System

A comprehensive Surgical Site Infection (SSI) monitoring and management system for SuperCare Hospital. This system provides complete patient tracking, form management, audit logging, and administrative oversight for SSI prevention and monitoring.

## 🏥 System Overview

The SuperCare SSI Bundle System is a web-based application designed to:

- **Monitor Surgical Site Infections** through comprehensive patient data collection
- **Track Patient Progress** from pre-operative to post-operative care
- **Manage Multiple User Types** (Nurses, Admins, Super Admins)
- **Provide Audit Trails** for all data modifications
- **Generate Reports** and statistics for quality improvement
- **Ensure Data Security** through proper authentication and authorization

## 🚀 Features

### Core Functionality

- **Patient Management**: Complete patient registration and tracking
- **SSI Bundle Forms**: Comprehensive data collection forms
- **Multi-User System**: Nurse, Admin, and Super Admin access levels
- **Audit Logging**: Complete audit trail for all system activities
- **Backup System**: Automated database backup with email notifications
- **Email Integration**: OTP system and password reset functionality
- **Responsive Design**: Works seamlessly on desktop and mobile devices

### User Roles & Permissions

- **Nurses**: Patient data entry, form management, patient viewing
- **Admins**: Patient records management, audit log viewing, user management
- **Super Admins**: System administration, backup management, user oversight

### Form Sections

1. **Patient Information**: Basic demographics and contact details
2. **Surgical Details**: Operation dates, surgeon information, duration
3. **Risk Factors**: Weight, height, SGA, steroids, tuberculosis, others
4. **Surgical Skin Preparation**: Pre-op bath, hair removal details
5. **Implanted Materials**: Metal, graft, patch, shunt/stent information
6. **Drains**: Drain usage and descriptions
7. **Antibiotic Usage**: Drug administration details with dates
8. **Post-Operative Monitoring**: Daily monitoring data
9. **Cultural Dressing**: Cultural swap and dressing findings
10. **Wound Complications**: SSI tracking and complications
11. **Review & Follow-up**: Suture removal and phone call reviews
12. **Infection Prevention Notes**: Nurse notes and recommendations

## 📋 Prerequisites

### System Requirements

- **Web Server**: Apache (XAMPP/WAMP/LAMP)
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **Browser**: Modern web browser with JavaScript enabled
- **Email**: SMTP access for notifications (Gmail recommended)

### Software Installation

1. **XAMPP** (Recommended for Windows)
   - Download from: https://www.apachefriends.org/
   - Install with Apache and MySQL services
2. **Alternative Options**
   - WAMP (Windows)
   - LAMP (Linux)
   - MAMP (macOS)

## 🛠️ Installation & Setup

### Step 1: Download and Extract

```bash
# Clone or download the project
git clone [repository-url]
# OR download and extract ZIP file

# Place in web server directory
# For XAMPP: C:\xampp\htdocs\supercareSSibundle\
```

### Step 2: Database Setup

1. **Start XAMPP Services**

   - Start Apache and MySQL services
   - Open phpMyAdmin: http://localhost/phpmyadmin

2. **Create Database**

   ```sql
   CREATE DATABASE supercare_ssi;
   USE supercare_ssi;
   ```

3. **Import Database Structure**

   - Open phpMyAdmin
   - Select `supercare_ssi` database
   - Go to "Import" tab
   - Choose `database_setup.sql`
   - Click "Go" to create all tables

4. **Verify Database Setup**
   - Visit: `http://localhost/supercareSSibundle/test_db.php`
   - Should show "Database connection successful!"

### Step 3: Configuration

1. **Database Configuration** (`config.php`)

   ```php
   $host = 'localhost';
   $dbname = 'supercare_ssi';
   $username = 'root';  // Change for production
   $password = '';      // Change for production
   ```

2. **Email Configuration** (`email_config.php`)

   ```php
   $emailConfig = [
       'host' => 'smtp.gmail.com',
       'port' => 587,
       'username' => 'your-email@gmail.com',
       'password' => 'your-app-password',  // Gmail App Password
       'from_email' => 'your-email@gmail.com',
       'from_name' => 'SuperCare System'
   ];
   ```

3. **Session Configuration** (`session_config.php`)
   - Session timeout: 30 minutes (configurable)
   - Secure session settings enabled
   - Admin context detection implemented

### Step 4: Initial Setup

1. **Create Super Admin Account**

   - Visit: `http://localhost/supercareSSibundle/setup_super_admin_system.php`
   - Follow the setup wizard
   - Note down the generated credentials

2. **Create Admin Users**

   - Login as Super Admin
   - Navigate to Admin Management
   - Create admin accounts

3. **Create Nurse Accounts**
   - Login as Admin
   - Navigate to Nurse Management
   - Create nurse accounts

## 🔐 Security Features

### Authentication & Authorization

- **Multi-level User System**: Nurse → Admin → Super Admin
- **Session Management**: Secure session handling with timeout
- **Password Security**: Bcrypt hashing for all passwords
- **OTP System**: Two-factor authentication for Super Admin
- **Session Protection**: CSRF protection and secure cookies

### Data Protection

- **SQL Injection Prevention**: Prepared statements throughout
- **Input Validation**: Comprehensive form validation
- **XSS Protection**: Output sanitization
- **Audit Logging**: Complete audit trail for all actions
- **Data Encryption**: Sensitive data encryption in transit

### Security Best Practices

- **Error Handling**: Secure error reporting
- **File Permissions**: Proper file access controls
- **Database Security**: Dedicated database users
- **HTTPS Ready**: Configured for secure connections

## 📁 File Structure

```
supercareSSibundle/
├── 📄 Core Files
│   ├── index.php                 # Main entry point
│   ├── form.php                  # Form entry point
│   ├── config.php                # Database configuration
│   ├── session_config.php        # Session management
│   └── admin_session_manager.php # Admin session handling
│
├── 📄 Templates & UI
│   ├── form_template.html        # Main form template
│   ├── index.html               # Nurse dashboard
│   ├── admin.php                # Admin panel
│   ├── login.html               # Nurse login
│   ├── admin_login_new.html     # Admin login
│   └── super_admin_login.html   # Super admin login
│
├── 📄 Processing & API
│   ├── submit_form_working.php  # Form submission handler
│   ├── nurse_login.php          # Nurse authentication
│   ├── admin_login_new_simple.php # Admin authentication
│   ├── get_patient_data_api.php # Patient data API
│   └── update_session_activity.php # Session activity
│
├── 📄 Management
│   ├── create_nurse.php         # Nurse account creation
│   ├── create_admin_simple.php  # Admin account creation
│   ├── manage_admin_accounts.php # Admin management
│   └── update_nurse.php         # Nurse account updates
│
├── 📄 Audit & Logging
│   ├── audit_logger.php         # Audit logging system
│   ├── audit_log.php            # Audit log viewer
│   ├── get_audit_logs.php       # Audit log API
│   └── get_audit_stats.php      # Audit statistics
│
├── 📄 Backup & Maintenance
│   ├── backup_system.php        # Automated backup system
│   ├── backup_manager.php       # Backup management
│   └── export_system.php        # Data export functionality
│
├── 📄 Email & Notifications
│   ├── email_config.php         # Email configuration
│   ├── send_otp.php             # OTP system
│   ├── forgot_password.php      # Password reset
│   └── phpmailer/               # Email library
│
├── 📄 Assets
│   ├── style.css                # Main stylesheet
│   ├── script.js                # JavaScript functionality
│   └── supercare-hospital_logo.png
│
├── 📄 Database
│   ├── database_setup.sql       # Database structure
│   ├── update_database_new_fields.sql # Database updates
│   └── run_database_updates.php # Update runner
│
└── 📄 Documentation
    ├── README.md                # This file
    ├── ADMIN_README.md          # Admin documentation
    ├── TROUBLESHOOTING_GUIDE.md # Troubleshooting guide
    └── SECURITY_README.md       # Security documentation
```

## 🗄️ Database Schema

### Core Tables

1. **patients** - Patient basic information
2. **surgical_details** - Surgery information
3. **surgical_skin_preparation** - Pre-operative preparation
4. **risk_factors** - Patient risk factors
5. **implanted_materials** - Implant information
6. **drains** - Drain usage details
7. **antibiotic_usage** - Antibiotic administration
8. **post_operative_monitoring** - Daily monitoring
9. **cultural_dressing** - Cultural findings
10. **wound_complications** - SSI tracking
11. **review_sutures** - Suture review
12. **review_phone** - Follow-up calls
13. **infection_prevention_notes** - Nurse notes

### User Management Tables

14. **nurses** - Nurse accounts
15. **admin_users** - Admin accounts
16. **super_admin_users** - Super admin accounts
17. **admin_audit_logs** - System audit trail
18. **admin_login_logs** - Login attempts
19. **super_admin_otp_logs** - OTP usage logs

## 👥 User Guide

### For Nurses

#### Login Process

1. Visit: `http://localhost/supercareSSibundle/login.html`
2. Enter Nurse ID and Password
3. Access patient dashboard

#### Adding New Patients

1. Click "New Patient" button
2. Fill out comprehensive SSI bundle form
3. All sections are auto-saved
4. Click "Submit" to finalize

#### Managing Existing Patients

1. Search for patients using UHID or name
2. Click "View/Edit" to access patient data
3. Update information as needed
4. Changes are automatically logged

### For Admins

#### Login Process

1. Visit: `http://localhost/supercareSSibundle/admin_login_new.html`
2. Enter email and password
3. Access admin dashboard

#### Patient Management

1. View all patient records
2. Search and filter patients
3. Export patient data
4. View audit logs

#### User Management

1. Create and manage nurse accounts
2. Monitor user activity
3. Reset passwords
4. View login logs

### For Super Admins

#### Login Process

1. Visit: `http://localhost/supercareSSibundle/super_admin_login.html`
2. Enter email and password
3. Complete OTP verification
4. Access super admin dashboard

#### System Administration

1. Manage admin accounts
2. Monitor system backups
3. View comprehensive audit logs
4. System configuration

## 🔧 Configuration Options

### Session Timeout

```php
// session_config.php
define('NURSE_SESSION_TIMEOUT', 1800); // 30 minutes
define('SESSION_TIMEOUT', 1800);        // 30 minutes
```

### Email Settings

```php
// email_config.php
define('EMAIL_DEBUG', false);           // Set to true for debugging
define('SMTP_SECURE', 'tls');           // Use 'ssl' for port 465
```

### Backup Settings

```php
// backup_system.php
define('BACKUP_RETENTION_DAYS', 30);    // Keep backups for 30 days
define('BACKUP_TIME', '02:00');         // Daily backup at 2 AM
```

## 🚨 Troubleshooting

### Common Issues

#### Database Connection Errors

```bash
# Check XAMPP services
# Verify database exists
# Test connection: test_db.php
```

#### Session Expiration Issues

```bash
# Check session configuration
# Verify cookie settings
# Test session: test_session_fix.php
```

#### Form Submission Errors

```bash
# Check browser console
# Verify file permissions
# Check PHP error logs
```

#### Email Not Working

```bash
# Verify SMTP settings
# Check Gmail app password
# Test email: test_phpmailer.php
```

### Debug Tools

- `test_db.php` - Database connection test
- `test_session_fix.php` - Session debugging
- `test_api.php` - API endpoint testing
- `check_audit_table.php` - Audit system verification

## 🔒 Security Recommendations

### Production Deployment

1. **Change Default Credentials**

   - Database username/password
   - Admin account passwords
   - Email credentials

2. **Enable HTTPS**

   - Configure SSL certificate
   - Update session settings
   - Set secure cookies

3. **Implement Rate Limiting**

   - Login attempt limits
   - API request throttling
   - Form submission limits

4. **Regular Maintenance**
   - Database backups
   - Log rotation
   - Security updates

### Security Checklist

- [ ] Change default database credentials
- [ ] Enable HTTPS
- [ ] Set secure session cookies
- [ ] Implement rate limiting
- [ ] Configure proper file permissions
- [ ] Set up automated backups
- [ ] Monitor audit logs
- [ ] Regular security updates

## 📊 Monitoring & Maintenance

### Daily Tasks

- Monitor audit logs for suspicious activity
- Check backup completion status
- Review error logs

### Weekly Tasks

- Review user activity reports
- Check database performance
- Verify email functionality

### Monthly Tasks

- Update system documentation
- Review security settings
- Performance optimization

## 🤝 Support & Documentation

### Additional Documentation

- `ADMIN_README.md` - Detailed admin guide
- `TROUBLESHOOTING_GUIDE.md` - Common issues and solutions
- `SECURITY_README.md` - Security best practices
- `AUDIT_SYSTEM_README.md` - Audit system documentation

### Getting Help

1. Check troubleshooting guides
2. Review audit logs for errors
3. Test with debug tools
4. Contact system administrator

## 📝 License & Credits

This system was developed for SuperCare Hospital for SSI monitoring and management.

### Version Information

- **Current Version**: 2.0
- **Last Updated**: August 2025
- **PHP Version**: 7.4+
- **Database**: MySQL 5.7+

### System Requirements

- **Web Server**: Apache/Nginx
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Browser**: Modern browsers with JavaScript enabled

---

**For technical support or questions, please contact the development team.**
