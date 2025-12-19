# Add SMTP Configuration to .env file
$envFile = ".env"
$appPassword = "200419"

Write-Host "`nAdding SMTP configuration to .env file...`n" -ForegroundColor Cyan

# Check if .env exists
if (-not (Test-Path $envFile)) {
    Write-Host "Creating .env file..." -ForegroundColor Yellow
    New-Item -Path $envFile -ItemType File | Out-Null
}

# Read current .env content
$content = Get-Content $envFile -ErrorAction SilentlyContinue

# Remove existing SMTP lines
$content = $content | Where-Object { 
    $_ -notmatch "^USE_SMTP=" -and 
    $_ -notmatch "^SMTP_HOST=" -and 
    $_ -notmatch "^SMTP_PORT=" -and 
    $_ -notmatch "^SMTP_SECURE=" -and 
    $_ -notmatch "^SMTP_USERNAME=" -and 
    $_ -notmatch "^SMTP_PASSWORD=" -and 
    $_ -notmatch "^SMTP_FROM_EMAIL=" 
}

# Add SMTP configuration
$smtpConfig = @"
# SMTP Configuration for Feedback Form
USE_SMTP=true
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_USERNAME=thoeungsereymongkol@gmail.com
SMTP_PASSWORD=$appPassword
SMTP_FROM_EMAIL=thoeungsereymongkol@gmail.com
"@

# Add newline if content exists
if ($content) {
    $content += ""
}

# Add SMTP config
$content += $smtpConfig

# Write back to file
$content | Set-Content $envFile

Write-Host "✅ SMTP configuration added to .env file!`n" -ForegroundColor Green
Write-Host "Configuration:" -ForegroundColor Yellow
Write-Host "  SMTP_HOST: smtp.gmail.com" -ForegroundColor White
Write-Host "  SMTP_PORT: 587" -ForegroundColor White
Write-Host "  SMTP_USERNAME: thoeungsereymongkol@gmail.com" -ForegroundColor White
Write-Host "  SMTP_PASSWORD: $appPassword`n" -ForegroundColor White

Write-Host "⚠️  Note: If '200419' is only 6 characters, you may need the full 16-character App Password." -ForegroundColor Yellow
Write-Host "   Gmail App Passwords are typically 16 characters (like: abcd efgh ijkl mnop)`n" -ForegroundColor Yellow

Write-Host "Test it:" -ForegroundColor Cyan
Write-Host "  1. Go to your website" -ForegroundColor White
Write-Host "  2. Click feedback button" -ForegroundColor White
Write-Host "  3. Send a test message" -ForegroundColor White
Write-Host "  4. Check your email`n" -ForegroundColor White

