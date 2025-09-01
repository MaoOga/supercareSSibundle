# Troubleshooting Guide for JSON Parsing Errors

## Problem Description

You're experiencing `SyntaxError: Unexpected token '<', "<?php..." is not valid JSON` errors when trying to access the super admin dashboard.

## Root Cause

This error occurs when PHP files are being served as plain text instead of being processed by the PHP interpreter. The browser receives raw PHP code (starting with `<?php`) instead of the expected JSON response.

## Step-by-Step Troubleshooting

### 1. Test Basic PHP Functionality

First, test if PHP is working at all:

- Open your browser and go to: `http://localhost/supercareSSibundle/test_php.php`
- You should see a JSON response like: `{"success":true,"message":"PHP is working correctly"}`
- If you see raw PHP code instead, PHP is not being processed

### 2. Run Comprehensive Diagnostics

- Go to: `http://localhost/supercareSSibundle/troubleshoot.php`
- This will show detailed diagnostics about your setup

### 3. Check XAMPP Configuration

#### A. Verify Apache is Running

1. Open XAMPP Control Panel
2. Make sure Apache is started (green light)
3. If not, click "Start" next to Apache

#### B. Verify PHP is Enabled

1. In XAMPP Control Panel, click "Config" next to Apache
2. Select "httpd.conf"
3. Search for "LoadModule php" - it should be uncommented
4. Look for a line like: `LoadModule php_module "C:/xampp/php/php8apache2_4.dll"`

#### C. Check File Extensions

1. In the same httpd.conf file, search for "AddType"
2. Make sure you have: `AddType application/x-httpd-php .php`

### 4. Check File Permissions

- Make sure all PHP files have read permissions
- On Windows, right-click the folder → Properties → Security → Make sure "Users" can read

### 5. Test Database Connection

- Go to: `http://localhost/supercareSSibundle/test_db_connection.php`
- This will test if the database is accessible

### 6. Check .htaccess File

- Make sure the `.htaccess` file exists in your project root
- If it doesn't exist, the one we created should help

## Common Solutions

### Solution 1: Restart XAMPP

1. Stop Apache and MySQL in XAMPP Control Panel
2. Close XAMPP Control Panel
3. Reopen XAMPP Control Panel
4. Start Apache and MySQL again

### Solution 2: Check PHP Version

1. Go to: `http://localhost/supercareSSibundle/troubleshoot.php`
2. Check the PHP version in the diagnostics
3. Make sure it's compatible with your code

### Solution 3: Verify Database Tables

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select the `supercare_ssi` database
3. Verify these tables exist:
   - `nurses`
   - `surgeons`
   - `admin_users`
   - `audit_logs`

### Solution 4: Check File Paths

Make sure your project is in the correct location:

- Should be in: `C:\New Xampp\htdocs\supercareSSibundle\`
- Access via: `http://localhost/supercareSSibundle/`

## Error Messages and Meanings

### "Unexpected token '<'"

- **Meaning**: PHP code is being served as text
- **Solution**: Check Apache/PHP configuration

### "Database connection failed"

- **Meaning**: MySQL is not running or credentials are wrong
- **Solution**: Start MySQL in XAMPP, check database credentials

### "Table doesn't exist"

- **Meaning**: Database tables haven't been created
- **Solution**: Run the database setup scripts

## Quick Fix Commands

If you have command line access:

```bash
# Test PHP syntax
"C:\New Xampp\php\php.exe" -l get_nurses.php

# Test database connection
"C:\New Xampp\php\php.exe" test_db_connection.php
```

## Still Having Issues?

1. **Check XAMPP Error Logs**:

   - Go to: `C:\New Xampp\apache\logs\error.log`
   - Look for recent errors

2. **Check PHP Error Logs**:

   - Go to: `C:\New Xampp\php\logs\php_error_log`
   - Look for recent errors

3. **Enable Detailed Error Reporting**:
   - The PHP files now have error reporting enabled
   - Check the browser's Network tab in Developer Tools
   - Look at the actual response from the failing PHP files

## Expected Behavior After Fix

Once everything is working correctly:

1. `http://localhost/supercareSSibundle/super_admin_login.html` should show the login form
2. Login with: `superadmin` / `SuperCare2024!`
3. The dashboard should load without JSON errors
4. All stats should display correctly

## Contact Information

If you're still experiencing issues after trying these steps, please:

1. Run the troubleshoot.php script
2. Copy the complete JSON output
3. Check the browser's Network tab for the actual responses
4. Provide this information for further assistance
