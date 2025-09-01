# Super Admin System - SSI Bundle

## Overview

This system implements a secure three-tier access control system for the SSI Bundle application:

1. **Super Admin** - Can create admin accounts (restricted access)
2. **Admin** - Can manage nurses and surgeons (created by Super Admin)
3. **Nurses** - Can access patient forms (created by Admin)

## Security Features

- **Hardcoded Super Admin Credentials** - Only you and your customer know these
- **Session-based Authentication** - 30-minute timeout
- **Audit Logging** - All actions are logged
- **Password Hashing** - Secure password storage
- **No Public Registration** - Only Super Admin can create accounts

## Quick Setup

### 1. Run Setup Script

Visit: `http://localhost/supercareSSibundle/setup_super_admin_system.php`

This will:

- Create the `admin_users` table
- Create the `admin_audit_logs` table
- Show you the access information

### 2. Default Super Admin Credentials

- **Username:** `superadmin`
- **Password:** `SuperCare2024!`

**⚠️ IMPORTANT:** Change these credentials in `super_admin_login.php` after first login!

## Access Hierarchy

### Super Admin Access

- **Login:** `super_admin_login.html`
- **Dashboard:** `super_admin_dashboard.html`
- **Permissions:** Create/delete admin accounts

### Admin Access

- **Login:** `admin_login.html`
- **Dashboard:** `admin_protected.php` (protected version of admin.html)
- **Permissions:** Manage nurses and surgeons

### Nurse Access

- **Login:** `login.html` (existing)
- **Permissions:** Access patient forms

## File Structure

### New Files Created

```
super_admin_login.html          # Super admin login page
super_admin_login.php           # Super admin authentication
super_admin_dashboard.html      # Super admin dashboard
super_admin_logout.php          # Super admin logout
admin_login.html               # Admin login page
admin_login.php                # Admin authentication
admin_logout.php               # Admin logout
admin_protected.php            # Protected admin panel
check_admin_session.php        # Session protection
create_admin.php               # Create admin accounts
get_admins.php                 # Get admin list
delete_admin.php               # Delete admin accounts
create_admin_table.sql         # Database table creation
setup_super_admin_system.php   # Initial setup script
```

### Modified Files

```
audit_logger.php               # Added super admin logging
admin.html                     # Updated logout functionality
```

## Usage Workflow

### For Your Customer (Super Admin)

1. Login at `super_admin_login.html`
2. Use credentials: `superadmin` / `SuperCare2024!`
3. Create admin accounts for hospital staff
4. Monitor system through audit logs

### For Hospital Staff (Admin)

1. Get admin credentials from Super Admin
2. Login at `admin_login.html`
3. Manage nurses and surgeons
4. Access backup and audit systems

### For Nurses

1. Get nurse credentials from Admin
2. Login at `login.html`
3. Access patient forms and data

## Security Recommendations

### 1. Change Super Admin Password

After first login, edit `super_admin_login.php`:

```php
$SUPER_ADMIN_USERNAME = 'your_new_username';
$SUPER_ADMIN_PASSWORD = 'your_new_secure_password';
```

### 2. Use Strong Passwords

- Minimum 8 characters
- Mix of uppercase, lowercase, numbers, symbols
- Different passwords for each admin account

### 3. Regular Monitoring

- Check audit logs regularly
- Monitor for failed login attempts
- Review admin account activity

## Database Tables

### admin_users

```sql
CREATE TABLE admin_users (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(50) NOT NULL UNIQUE,
  name varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  password varchar(255) NOT NULL,
  created_at timestamp DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  last_login timestamp NULL DEFAULT NULL,
  is_active tinyint(1) DEFAULT 1,
  PRIMARY KEY (id)
);
```

### admin_audit_logs

Already exists in your system - used for logging all admin actions.

## Troubleshooting

### Common Issues

1. **"No admin accounts exist"**

   - Run `setup_super_admin_system.php`
   - Create admin accounts via Super Admin

2. **Session timeout errors**

   - Sessions expire after 30 minutes
   - Simply login again

3. **Database connection errors**

   - Check `config.php` database settings
   - Ensure XAMPP is running

4. **Permission denied errors**
   - Check file permissions
   - Ensure PHP has write access

### Testing the System

1. **Test Super Admin:**

   - Visit `super_admin_login.html`
   - Login with default credentials
   - Create a test admin account

2. **Test Admin:**

   - Login with the created admin account
   - Try creating a nurse account
   - Test logout functionality

3. **Test Session Protection:**
   - Try accessing `admin_protected.php` without login
   - Should redirect to login page

## Support

For technical issues:

1. Check browser console for errors
2. Review PHP error logs
3. Verify database connectivity
4. Test with `test_db.php`

## Security Notes

- This system is designed for internal hospital use
- Super admin credentials should be shared only with authorized personnel
- Regular password changes are recommended
- All actions are logged for audit purposes
- Session timeout prevents unauthorized access
