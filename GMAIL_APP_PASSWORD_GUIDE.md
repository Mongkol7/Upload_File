# How to Get Gmail App Password

## ⚠️ Important
**DO NOT use your regular Gmail password!** Gmail requires a special "App Password" for SMTP.

## Step-by-Step Guide

### Step 1: Enable 2-Factor Authentication
1. Go to: https://myaccount.google.com/security
2. Under "Signing in to Google", find "2-Step Verification"
3. If not enabled, click it and follow the setup
4. **You MUST have 2FA enabled to create App Passwords**

### Step 2: Generate App Password
1. Go to: https://myaccount.google.com/apppasswords
2. Sign in with: `thoeungsereymongkol@gmail.com`
3. You'll see a page titled "App passwords"
4. Under "Select app", choose: **"Mail"**
5. Under "Select device", choose: **"Other (Custom name)"**
6. Type: **"MK Uploader Feedback"**
7. Click **"Generate"**
8. You'll see a 16-character password like: `abcd efgh ijkl mnop`
9. **Copy this password** (remove spaces when using it)

### Step 3: Add to .env File
Add these lines to your `.env` file:

```env
USE_SMTP=true
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_USERNAME=thoeungsereymongkol@gmail.com
SMTP_PASSWORD=abcdefghijklmnop
SMTP_FROM_EMAIL=thoeungsereymongkol@gmail.com
```

**Important:** 
- Use the 16-character App Password (not your regular password)
- Remove spaces from the app password
- Example: If you got `abcd efgh ijkl mnop`, use `abcdefghijklmnop`

### Step 4: Test It
1. Save the `.env` file
2. Go to your website
3. Click the feedback button
4. Send a test message
5. Check your email: `thoeungsereymongkol@gmail.com`

## Troubleshooting

**"2-Step Verification is not enabled"**
- You MUST enable 2FA first
- Go to: https://myaccount.google.com/security

**"App passwords option not showing"**
- Make sure 2FA is enabled
- Try refreshing the page
- Make sure you're signed in to the correct account

**"SMTP sending failed"**
- Double-check the app password (no spaces)
- Make sure 2FA is enabled
- Check that the username is correct

## Security Note
- App Passwords are safer than your regular password
- You can revoke them anytime
- Each app gets its own password
- If compromised, you can delete just that app password

