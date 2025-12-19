# Quick Feedback Email Setup

## Problem
PHP `mail()` function doesn't work on XAMPP/local development without SMTP.

## Solution: Use Gmail SMTP (Easiest)

### Step 1: Get Gmail App Password

1. Go to: https://myaccount.google.com/apppasswords
2. Sign in with your Gmail account
3. Select "Mail" and "Other (Custom name)"
4. Enter: "MK Uploader Feedback"
5. Click "Generate"
6. Copy the 16-character password (looks like: `abcd efgh ijkl mnop`)

### Step 2: Add to .env File

Add these lines to your `.env` file:

```env
USE_SMTP=true
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-16-char-app-password
SMTP_FROM_EMAIL=your-email@gmail.com
```

**Example:**
```env
USE_SMTP=true
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_USERNAME=thoeungsereymongkol@gmail.com
SMTP_PASSWORD=abcd efgh ijkl mnop
SMTP_FROM_EMAIL=thoeungsereymongkol@gmail.com
```

**Important:** Remove spaces from the app password when pasting!

### Step 3: Test It!

1. Go to your website
2. Click the feedback button (bottom right)
3. Send a test message
4. Check your email: `thoeungsereymongkol@gmail.com`

## Alternative: Local Development

If you don't want to set up SMTP for local testing:

**Feedback is automatically saved to:** `feedback_backup.txt`

You can check feedback there while testing locally. The form will show "success" even if email fails (for local development).

## Troubleshooting

**"SMTP sending failed"**
- Check app password is correct (no spaces)
- Verify 2-factor authentication is enabled
- Check Gmail account is not locked

**"PHPMailer class not found"**
- Run: `composer install`
- Check `vendor/phpmailer` exists

**Still not working?**
- Check PHP error logs
- Check `feedback_backup.txt` file (feedback is saved there)
- Try the backup file method for local testing

## Production Deployment

For production, use the same Gmail SMTP setup, or configure with your hosting provider's SMTP settings.

