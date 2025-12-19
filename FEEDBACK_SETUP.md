# Feedback Form Email Setup

## Current Issue

PHP's `mail()` function often doesn't work on local development (XAMPP) without SMTP configuration.

## Solutions

### Option 1: Use SMTP (Recommended for Production)

Add to your `.env` file:

```env
USE_SMTP=true
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_FROM_EMAIL=noreply@yourdomain.com
```

**For Gmail:**

1. Enable 2-factor authentication
2. Generate an App Password: https://myaccount.google.com/apppasswords
3. Use the app password (not your regular password)

### Option 2: Local Development (XAMPP)

For local testing, feedback is automatically saved to `feedback_backup.txt` file.

**Location:** `feedback_backup.txt` in your website root directory

You can check feedback there while testing locally.

### Option 3: Install PHPMailer (Best Solution)

```bash
composer require phpmailer/phpmailer
```

Then configure SMTP in `.env` (see Option 1).

### Option 4: Use a Webhook/API Service

You can also use services like:

- Formspree
- EmailJS
- SendGrid API
- Mailgun API

## Quick Test

1. Try sending feedback
2. Check browser console (F12) for errors
3. Check PHP error log for details
4. For local: Check `feedback_backup.txt` file

## Troubleshooting

**"Failed to send feedback"**

- Check PHP error logs
- For local: Check `feedback_backup.txt`
- For production: Configure SMTP

**Email not received**

- Check spam folder
- Verify SMTP credentials
- Check server logs
