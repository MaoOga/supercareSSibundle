# Forgot Password Functionality

## Overview
The SSI Bundle System now includes a complete forgot password functionality that allows nurses to reset their passwords securely via email.

## Features
- **Email-based password reset**: Users can request a password reset by entering their email address
- **Secure token system**: Unique, time-limited tokens are generated for each reset request
- **Password strength validation**: Ensures new passwords meet security requirements
- **Automatic token expiration**: Reset tokens expire after 1 hour for security
- **Professional email templates**: HTML-formatted emails with clear instructions

## Files Created/Modified

### New Files:
1. **`forgot_password.html`** - Form for entering email address
2. **`reset_password.html`** - Form for setting new password
3. **`forgot_password.php`** - Backend handler for email submission
4. **`reset_password.php`** - Backend handler for password reset
5. **`add_reset_token_columns.sql`** - SQL script to add reset token columns
6. **`FORGOT_PASSWORD_README.md`** - This documentation file

### Modified Files:
1. **`login.html`** - Added "Forgot Password" link
2. **`database_setup.sql`** - Added reset token columns to nurses table

## Database Changes
The `nurses` table has been updated with two new columns:
- `reset_token VARCHAR(64) NULL` - Stores the unique reset token
- `reset_expiry TIMESTAMP NULL` - Stores when the token expires

An index has been added on `reset_token` for better performance.

## Setup Instructions

### 1. Database Setup
If you're setting up a new database, the reset token columns are already included in `database_setup.sql`.

If you're updating an existing database, run the SQL commands from `add_reset_token_columns.sql`:
```sql
ALTER TABLE nurses 
ADD COLUMN reset_token VARCHAR(64) NULL,
ADD COLUMN reset_expiry TIMESTAMP NULL;

CREATE INDEX idx_reset_token ON nurses(reset_token);
```

### 2. Email Configuration
The system uses PHP's built-in `mail()` function. Ensure your server is configured to send emails.

For production environments, consider using a more robust email solution like:
- PHPMailer with SMTP
- SendGrid
- Amazon SES
- Mailgun

### 3. Email Template Customization
The email template is defined in `forgot_password.php`. You can customize:
- Email subject line
- HTML styling
- Email content
- From address
- Reply-to address

## How It Works

### 1. Password Reset Request
1. User clicks "Forgot Password" on login page
2. User enters their email address on `forgot_password.html`
3. System validates email exists in database
4. System generates unique reset token and expiry time
5. System sends email with reset link
6. User receives email with secure reset link

### 2. Password Reset
1. User clicks reset link in email
2. User is taken to `reset_password.html` with token in URL
3. User enters and confirms new password
4. System validates password strength requirements
5. System verifies token is valid and not expired
6. System updates password and clears reset token
7. User is redirected to login page

## Security Features

### Token Security
- **Cryptographically secure**: Uses `random_bytes(32)` for token generation
- **Time-limited**: Tokens expire after 1 hour
- **Single-use**: Tokens are cleared after password reset
- **Database-stored**: Tokens are stored securely in database

### Password Requirements
- Minimum 8 characters
- At least one uppercase letter
- At least one lowercase letter
- At least one number
- Real-time validation feedback

### Email Security
- No sensitive information in emails
- Clear instructions for users
- Professional formatting
- Secure reset links

## User Experience

### Password Reset Request
- Clean, intuitive interface
- Clear error messages
- Loading states during submission
- Success confirmation

### Password Reset
- Real-time password strength validation
- Visual feedback for requirements
- Password confirmation matching
- Automatic redirect after success

## Error Handling

### Common Error Scenarios
- **Email not found**: "Email address not found in our records"
- **Invalid token**: "Invalid or expired reset token"
- **Weak password**: Specific validation error messages
- **Network errors**: "Network error occurred. Please try again"

### User-Friendly Messages
- Clear, actionable error messages
- No technical jargon
- Helpful suggestions for resolution

## Testing

### Test Scenarios
1. **Valid email request**: Should send reset email
2. **Invalid email**: Should show "email not found" error
3. **Valid token reset**: Should allow password change
4. **Expired token**: Should show "token expired" error
5. **Weak password**: Should show validation errors
6. **Password mismatch**: Should show confirmation error

### Email Testing
- Test email delivery in your environment
- Verify email content and formatting
- Test reset link functionality
- Verify token expiration

## Troubleshooting

### Common Issues
1. **Emails not sending**: Check server mail configuration
2. **Token not working**: Verify database columns were added
3. **Password validation failing**: Check password requirements
4. **Reset link not working**: Verify URL generation logic

### Debug Steps
1. Check server error logs
2. Verify database connection
3. Test email functionality
4. Validate token generation
5. Check password hashing

## Future Enhancements

### Potential Improvements
- **Rate limiting**: Prevent abuse of reset requests
- **Audit logging**: Track password reset attempts
- **Multiple email providers**: Fallback email services
- **SMS verification**: Two-factor authentication
- **Security questions**: Additional verification methods

## Support

For issues or questions regarding the forgot password functionality:
1. Check this documentation
2. Review error logs
3. Test with known good data
4. Verify server configuration
5. Contact system administrator
