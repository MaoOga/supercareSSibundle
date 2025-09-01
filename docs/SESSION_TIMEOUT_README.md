# Super Admin Session Timeout Feature

## Overview

The Super Admin Dashboard now includes a comprehensive 30-minute session timeout system with visual indicators and user-friendly warnings.

## Features

### 1. Session Timer Display
- **Location**: Top-right corner of the dashboard
- **Visibility**: Shows when session has less than 25 minutes remaining
- **Color Coding**:
  - **Default**: Black background (25-10 minutes remaining)
  - **Orange**: Warning color (10-5 minutes remaining)
  - **Red**: Critical color (5-0 minutes remaining)

### 2. Warning Dialog
- **Trigger**: Appears 5 minutes before session expiration
- **Features**:
  - Countdown timer showing remaining time
  - "Extend Session" button to prolong the session
  - Clear visual warning with yellow border

### 3. Session Expiration
- **Timeout**: 30 minutes of inactivity
- **Process**:
  - Shows expiration dialog
  - 5-second countdown to redirect
  - Automatic logout and redirect to login page

### 4. Activity Detection
The system monitors user activity through:
- Mouse movements
- Keyboard input
- Mouse clicks
- Page scrolling

Any activity resets the session timer.

## Technical Implementation

### Files Modified/Created

1. **`super_admin_dashboard_simple.html`**
   - Added session timeout JavaScript functions
   - Added visual timer and warning dialogs
   - Integrated activity monitoring

2. **`check_super_admin_session.php`**
   - Updated to check session timeout
   - Returns remaining session time
   - Handles session expiration

3. **`update_session_activity.php`** (New)
   - Handles session extension requests
   - Updates session activity timestamps
   - Logs session extensions

4. **`verify_otp.php`**
   - Updated session variables to match expected structure
   - Sets initial session activity timestamp

5. **`secure_super_admin_simple.php`**
   - Updated session variables to match expected structure

### Session Variables

The system uses these session variables:
- `$_SESSION['super_admin_logged_in']` - Boolean indicating login status
- `$_SESSION['super_admin_id']` - Super admin user ID
- `$_SESSION['super_admin_username']` - Super admin username
- `$_SESSION['last_activity']` - Timestamp of last activity
- `$_SESSION['login_time']` - Timestamp of login

### Configuration

Session timeout settings (in JavaScript):
```javascript
const SESSION_DURATION = 30 * 60 * 1000; // 30 minutes
const WARNING_TIME = 5 * 60 * 1000;      // 5 minutes warning
const REDIRECT_DELAY = 5;                // 5 seconds redirect
```

## User Experience

### Normal Operation
1. User logs into super admin dashboard
2. Session timer appears after 25 minutes of inactivity
3. Timer changes color as session approaches expiration
4. Warning dialog appears 5 minutes before expiration
5. User can extend session or let it expire

### Session Extension
1. Click "Extend Session" button in warning dialog
2. System calls `update_session_activity.php`
3. Session is extended for another 30 minutes
4. Success message is displayed
5. All timers are reset

### Session Expiration
1. After 30 minutes of inactivity, session expires
2. Expiration dialog appears with countdown
3. User is automatically logged out after 5 seconds
4. Redirected to super admin login page

## Security Features

1. **Server-side Validation**: Session timeout is enforced on both client and server side
2. **Activity Logging**: All session extensions are logged in the database
3. **Automatic Cleanup**: Expired sessions are automatically destroyed
4. **IP Tracking**: Session activity includes IP address logging

## Testing

Use `test_session_timeout.php` to verify session functionality:
- Check current session status
- View session timeout configuration
- Test session extension functionality

## Troubleshooting

### Common Issues

1. **Session timer not appearing**
   - Check if user is properly logged in
   - Verify session variables are set correctly
   - Check browser console for JavaScript errors

2. **Session extension not working**
   - Verify `update_session_activity.php` is accessible
   - Check database connection
   - Review server error logs

3. **Premature session expiration**
   - Check server time settings
   - Verify session timeout configuration
   - Review activity detection events

### Debug Information

The system provides detailed logging:
- Session creation and extension in database
- Access attempts in `super_admin_access.log`
- JavaScript console logs for client-side events

## Future Enhancements

Potential improvements:
1. Configurable timeout duration
2. Remember me functionality
3. Multiple session support
4. Advanced activity detection
5. Session analytics and reporting
