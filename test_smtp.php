<?php
/**
 * SMTP Connection Test Script
 * This will test your Gmail SMTP connection and show detailed errors
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

echo "<h2>SMTP Connection Test</h2>";
echo "<pre>";

// Check environment variables
echo "=== Environment Variables ===\n";
echo "USE_SMTP: " . ($_ENV['USE_SMTP'] ?? 'NOT SET') . "\n";
echo "SMTP_HOST: " . ($_ENV['SMTP_HOST'] ?? 'NOT SET') . "\n";
echo "SMTP_PORT: " . ($_ENV['SMTP_PORT'] ?? 'NOT SET') . "\n";
echo "SMTP_SECURE: " . ($_ENV['SMTP_SECURE'] ?? 'NOT SET') . "\n";
echo "SMTP_USERNAME: " . ($_ENV['SMTP_USERNAME'] ?? 'NOT SET') . "\n";
echo "SMTP_PASSWORD: " . (empty($_ENV['SMTP_PASSWORD']) ? 'NOT SET' : 'SET (' . strlen($_ENV['SMTP_PASSWORD']) . ' chars)') . "\n";
echo "SMTP_FROM_EMAIL: " . ($_ENV['SMTP_FROM_EMAIL'] ?? 'NOT SET') . "\n\n";

// Check PHPMailer
if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "❌ ERROR: PHPMailer class not found!\n";
    echo "Run: composer install\n";
    exit;
}

echo "✅ PHPMailer loaded\n\n";

// Test SMTP connection
try {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    $mail->isSMTP();
    $mail->Host = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['SMTP_USERNAME'] ?? '';
    $mail->Password = $_ENV['SMTP_PASSWORD'] ?? '';
    
    $secure = $_ENV['SMTP_SECURE'] ?? 'tls';
    if ($secure === 'tls') {
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    } elseif ($secure === 'ssl') {
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
    }
    
    $mail->Port = intval($_ENV['SMTP_PORT'] ?? 587);
    $mail->CharSet = 'UTF-8';
    $mail->SMTPDebug = 2; // Enable verbose debugging
    $mail->Debugoutput = function($str, $level) {
        echo htmlspecialchars($str) . "\n";
    };
    
    echo "=== Testing SMTP Connection ===\n";
    echo "Connecting to: " . $mail->Host . ":" . $mail->Port . "\n";
    echo "Using: " . ($secure === 'tls' ? 'TLS' : 'SSL') . "\n\n";
    
    // Test connection (don't send email, just connect)
    $mail->smtpConnect();
    
    if ($mail->getSMTPInstance()->connected()) {
        echo "\n✅ SMTP Connection Successful!\n";
        $mail->smtpClose();
        
        // Now try to send a test email
        echo "\n=== Sending Test Email ===\n";
        $mail->setFrom($_ENV['SMTP_FROM_EMAIL'] ?? $_ENV['SMTP_USERNAME'], 'MK Uploader Test');
        $mail->addAddress('thoeungsereymongkol@gmail.com');
        $mail->Subject = 'Test Email from MK Uploader';
        $mail->Body = 'This is a test email to verify SMTP is working.';
        $mail->isHTML(false);
        
        if ($mail->send()) {
            echo "\n✅ Test email sent successfully!\n";
            echo "Check your inbox: thoeungsereymongkol@gmail.com\n";
        } else {
            echo "\n❌ Failed to send email: " . $mail->ErrorInfo . "\n";
        }
    } else {
        echo "\n❌ SMTP Connection Failed!\n";
        echo "Error: " . $mail->ErrorInfo . "\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ EXCEPTION: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "</pre>";
?>

