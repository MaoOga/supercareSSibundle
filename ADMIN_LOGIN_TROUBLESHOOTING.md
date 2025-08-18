# Admin Login Troubleshooting Guide

## Issue Description

Admin login redirects back to login page after successful authentication for a few milliseconds.

## Potential Causes and Solutions

### 1. Session Configuration Conflicts

**Problem**: Multiple session configurations are conflicting with each other.

**Files involved**:

- `.htaccess` (global session settings)
- `session_config.php` (uses different session name)
- `admin_login_new.php` (uses ADMIN_NEW_SESSION)
- `admin.php` (uses ADMIN_NEW_SESSION)

**Solution Applied**:

- Commented out global session configuration in `.htaccess`
- Enhanced session handling in `admin_login_new.php`
- Added better debugging and logging

### 2. Session Name Conflicts

**Problem**: Different parts of the system use different session names.

**Solution**: Ensure consistent use of `ADMIN_NEW_SESSION` for admin functionality.

### 3. Session Data Not Persisting

**Problem**: Session data is not being saved properly between requests.

**Solution Applied**:

- Added `session_write_close()` and restart in login handler
- Enhanced session validation with detailed logging
- Clear existing session data before setting new data

## Testing Steps

### Step 1: Run Debug Script

1. Open `debug_admin_session.php` in your browser
2. Check all test results
3. Note any failed tests

### Step 2: Test Session Functionality

1. Open `test_admin_session.php` in your browser
2. Verify session data can be set and retrieved
3. Check if admin validation logic works

### Step 3: Test Login Flow

1. Try logging in with admin credentials
2. Check if you get redirected back to login
3. Look at the URL parameters for error messages:
   - `?msg=no_session` - Session not active
   - `?msg=unauthorized` - User type not valid
   - `?msg=invalid_session` - User ID not valid
   - `?msg=session_expired` - Session expired

### Step 4: Check Error Logs

1. Check Apache error logs: `C:\New Xampp\apache\logs\error.log`
2. Check PHP error logs: `C:\New Xampp\php\logs\php_error_log`
3. Look for session-related error messages

## Debug Information

### Session Configuration

- Session name: `ADMIN_NEW_SESSION`
- Session timeout: 30 minutes
- Session validation: Multiple checks in `admin.php`

### Database Requirements

- Table: `admin_users`
- Required fields: `id`, `admin_username`, `name`, `email`, `password`, `status`
- Status must be 'active'

### Files Modified

1. `admin_login_new.php` - Enhanced session handling
2. `admin.php` - Added detailed logging
3. `.htaccess` - Commented out conflicting session settings
4. `debug_admin_session.php` - Created for debugging
5. `test_admin_session.php` - Created for testing

## Common Error Messages

### "Session not active"

- Session failed to start properly
- Check PHP session configuration
- Verify session directory permissions

### "User type not valid"

- Session data not set properly during login
- Check if login handler is setting `$_SESSION['user_type'] = 'admin'`
- Verify session persistence

### "User ID not valid"

- `$_SESSION['user_id']` not set or empty
- Check database connection in login handler
- Verify admin user exists in database

### "Session expired"

- Session timeout reached
- Check `$_SESSION['expires_at']` value
- Verify timezone settings

## Quick Fixes to Try

### 1. Clear Browser Cookies

- Clear all cookies for your domain
- Try logging in again

### 2. Restart Apache

- Stop and start Apache server
- Clear any cached session data

### 3. Check Database Connection

- Verify database is running
- Check admin_users table exists
- Confirm admin user credentials

### 4. Test with Different Browser

- Try logging in with incognito/private mode
- Test with different browser

## If Problem Persists

1. Run the debug scripts and share the output
2. Check error logs and share relevant messages
3. Try the login process and note the exact error message in URL
4. Verify admin user exists in database with correct credentials

## Files to Check

### Core Files

- `admin_login_new.html` - Login form
- `admin_login_new.php` - Login handler
- `admin.php` - Admin panel
- `admin_logout_new.php` - Logout handler

### Configuration Files

- `.htaccess` - Server configuration
- `session_config.php` - Session settings
- `config.php` - Database configuration

### Debug Files

- `debug_admin_session.php` - Comprehensive debugging
- `test_admin_session.php` - Session testing
- `test_session_persistence.php` - Session persistence test
