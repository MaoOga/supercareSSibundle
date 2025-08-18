# Email Setup Guide for SSI Bundle System

## Overview

This guide will help you configure email functionality for the forgot password feature in your SSI Bundle System.

## Prerequisites

- PHP with Composer installed
- PHPMailer library (already installed via Composer)
- An email account (Gmail, Outlook, Yahoo, or custom SMTP)

## Step 1: Configure Email Settings

### 1.1 Edit `email_config.php`

Open the `email_config.php` file and update the following settings:

```php
// SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com'); // Your SMTP server
define('SMTP_PORT', 587);              // SMTP port
define('SMTP_SECURE', 'tls');          // 'tls' or 'ssl'

// Email Account Settings
define('EMAIL_USERNAME', 'your-email@gmail.com'); // Your email
define('EMAIL_PASSWORD', 'your-app-password');    // Your password/app password
define('EMAIL_FROM', 'noreply@supercare.com');    // From email address
define('EMAIL_FROM_NAME', 'SSI Bundle System');   // From name
```

### 1.2 Popular Email Provider Settings

#### Gmail Configuration

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('EMAIL_USERNAME', 'your-gmail@gmail.com');
define('EMAIL_PASSWORD', 'your-app-password'); // Use App Password, not regular password
```

**Gmail Setup Steps:**

1. Enable 2-Factor Authentication on your Gmail account
2. Go to Google Account Settings → Security → App Passwords
3. Generate an App Password for "Mail"
4. Use this App Password in your configuration

#### Outlook/Hotmail Configuration

```php
define('SMTP_HOST', 'smtp-mail.outlook.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('EMAIL_USERNAME', 'your-email@outlook.com');
define('EMAIL_PASSWORD', 'your-password');
```

#### Yahoo Configuration

```php
define('SMTP_HOST', 'smtp.mail.yahoo.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('EMAIL_USERNAME', 'your-email@yahoo.com');
define('EMAIL_PASSWORD', 'your-app-password'); // Use App Password
```

## Step 2: Test Email Configuration

### 2.1 Update Test Email Address

Edit `test_email.php` and change the test email address:

```php
$testEmail = 'your-test-email@gmail.com'; // Change to your email
```

### 2.2 Run Email Test

1. Open your web browser
2. Navigate to: `http://your-domain/test_email.php`
3. Check the output for success or error messages
4. Check your email inbox for the test message

## Step 3: Troubleshooting Common Issues

### 3.1 "Authentication Failed" Error

**Symptoms:** SMTP authentication error
**Solutions:**

- Verify your email username and password
- For Gmail: Use App Password instead of regular password
- Check if 2-factor authentication is enabled (for Gmail/Yahoo)

### 3.2 "Connection Refused" Error

**Symptoms:** Cannot connect to SMTP server
**Solutions:**

- Verify SMTP host and port are correct
- Check if your server allows outbound SMTP connections
- Try different ports (587, 465, 25)

### 3.3 "SSL/TLS Error" Error

**Symptoms:** SSL/TLS connection issues
**Solutions:**

- Verify SMTP_SECURE setting ('tls' or 'ssl')
- Try changing from 'tls' to 'ssl' or vice versa
- Check if your server supports the required SSL/TLS version

### 3.4 "Email Not Received" Issue

**Symptoms:** Test shows success but no email received
**Solutions:**

- Check spam/junk folder
- Verify recipient email address is correct
- Check email provider's sending limits
- Verify "From" email address is valid

## Step 4: Production Considerations

### 4.1 Security Best Practices

- Use App Passwords instead of regular passwords
- Enable 2-factor authentication on email accounts
- Use dedicated email accounts for system emails
- Regularly rotate email passwords

### 4.2 Email Provider Limits

- **Gmail:** 500 emails/day for regular accounts, 2000/day for Google Workspace
- **Outlook:** 300 emails/day
- **Yahoo:** 500 emails/day
- **Custom SMTP:** Varies by provider

### 4.3 Alternative Solutions

For high-volume or production environments, consider:

- **SendGrid:** 100 emails/day free, then paid plans
- **Mailgun:** 5,000 emails/month free
- **Amazon SES:** Very cost-effective for high volume
- **SMTP.com:** Professional email delivery service

## Step 5: Testing the Forgot Password Feature

### 5.1 Database Setup

Ensure you've run the database updates:

```sql
ALTER TABLE nurses
ADD COLUMN reset_token VARCHAR(64) NULL,
ADD COLUMN reset_expiry TIMESTAMP NULL;

CREATE INDEX idx_reset_token ON nurses(reset_token);
```

### 5.2 Test the Complete Flow

1. Go to login page
2. Click "Forgot Password"
3. Enter a valid email address from your nurses table
4. Check if reset email is received
5. Click the reset link in the email
6. Set a new password
7. Try logging in with the new password

## Step 6: Monitoring and Maintenance

### 6.1 Enable Debug Mode (Temporary)

For troubleshooting, enable debug mode in `email_config.php`:

```php
define('EMAIL_DEBUG', true);
```

### 6.2 Check Email Logs

Monitor your email provider's sending logs for:

- Delivery failures
- Bounce rates
- Spam complaints

### 6.3 Regular Testing

- Test email functionality monthly
- Monitor for any configuration changes
- Update email passwords regularly

## Support

If you continue to experience issues:

1. **Check Error Messages:** Look at the detailed error output from `test_email.php`
2. **Verify Configuration:** Double-check all settings in `email_config.php`
3. **Test with Different Provider:** Try a different email provider
4. **Check Server Logs:** Review PHP and web server error logs
5. **Contact Support:** Reach out with specific error messages

## Quick Reference

### Common SMTP Settings

| Provider | SMTP Host             | Port | Security | Notes                 |
| -------- | --------------------- | ---- | -------- | --------------------- |
| Gmail    | smtp.gmail.com        | 587  | TLS      | Requires App Password |
| Outlook  | smtp-mail.outlook.com | 587  | TLS      | Regular password OK   |
| Yahoo    | smtp.mail.yahoo.com   | 587  | TLS      | Requires App Password |
| Custom   | your-smtp.com         | 587  | TLS      | Varies by provider    |

### File Locations

- **Email Config:** `email_config.php`
- **Test Script:** `test_email.php`
- **Forgot Password:** `forgot_password.php`
- **Reset Password:** `reset_password.php`

### Key Functions

- **Test Email:** Visit `test_email.php` in browser
- **Forgot Password:** Use the "Forgot Password" link on login page
- **Reset Password:** Click link in reset email
