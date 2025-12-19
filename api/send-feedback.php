<?php
header('Content-Type: application/json');

// Load environment variables (required for SMTP configuration)
require_once __DIR__ . '/../config.php';

// Get feedback data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$message = $_POST['message'] ?? '';

if (empty($message)) {
    echo json_encode([
        'success' => false,
        'error' => 'Message is required'
    ]);
    exit;
}

// Recipient email
$to = 'thoeungsereymongkol@gmail.com';
$subject = 'Feedback from MK Uploader Website';

// Email body
$body = "New feedback received from MK Uploader Website\n\n";
$body .= "Name: " . htmlspecialchars($name ?: 'Not provided') . "\n";
$body .= "Email: " . htmlspecialchars($email ?: 'Not provided') . "\n";
$body .= "Message:\n" . htmlspecialchars($message) . "\n";
$body .= "\n---\n";
$body .= "Sent from: " . ($_SERVER['HTTP_REFERER'] ?? 'Unknown') . "\n";
$body .= "IP Address: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "\n";
$body .= "Date: " . date('Y-m-d H:i:s') . "\n";

// Try multiple methods to send email
$success = false;
$errorMessage = '';
$isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', '::1']);

// Method 1: Try SMTP if configured
$useSMTP = filter_var($_ENV['USE_SMTP'] ?? false, FILTER_VALIDATE_BOOLEAN);
if ($useSMTP && !empty($_ENV['SMTP_USERNAME']) && !empty($_ENV['SMTP_PASSWORD'])) {
    $smtpResult = sendViaSMTP($to, $subject, $body, $email);
    if (is_array($smtpResult)) {
        $success = $smtpResult['success'];
        if (!$success) {
            $errorMessage = 'SMTP sending failed: ' . ($smtpResult['error'] ?? 'Unknown error');
            error_log("SMTP sending failed: " . $errorMessage);
        }
    } else {
        // Legacy return (boolean)
        $success = $smtpResult;
        if (!$success) {
            $errorMessage = 'SMTP sending failed. Check SMTP credentials.';
            error_log("SMTP sending failed");
        }
    }
}

// Method 2: Try PHP mail() function (if SMTP not configured or failed)
if (!$success) {
    // Configure mail for better compatibility
    $headers = "From: MK Uploader <noreply@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ">\r\n";
    $headers .= "Reply-To: " . (!empty($email) ? htmlspecialchars($email) : 'noreply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost')) . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    $headers .= "MIME-Version: 1.0\r\n";
    
    $success = @mail($to, $subject, $body, $headers);
    
    if ($success) {
        error_log("Feedback sent successfully via PHP mail()");
    } else {
        $lastError = error_get_last();
        $errorMessage = 'PHP mail() function failed. ' . ($lastError ? $lastError['message'] : 'Check PHP mail configuration');
        error_log("PHP mail() failed: " . $errorMessage);
    }
}

// Method 3: Save to file as backup (always save, especially for local development)
$backupFile = __DIR__ . '/../feedback_backup.txt';
$backupEntry = "\n" . str_repeat('=', 50) . "\n";
$backupEntry .= "Date: " . date('Y-m-d H:i:s') . "\n";
$backupEntry .= "Name: " . htmlspecialchars($name ?: 'Not provided') . "\n";
$backupEntry .= "Email: " . htmlspecialchars($email ?: 'Not provided') . "\n";
$backupEntry .= "Message: " . htmlspecialchars($message) . "\n";
$backupEntry .= "Status: " . ($success ? 'Sent' : 'Failed - saved to backup') . "\n";
$backupEntry .= str_repeat('=', 50) . "\n";

@file_put_contents($backupFile, $backupEntry, FILE_APPEND);
error_log("Feedback saved to backup file: " . $backupFile);

// For local development, still show error but save to file
// Don't automatically mark as success on local - we want to know if SMTP fails

if ($success) {
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your feedback! We will get back to you soon.'
    ]);
} else {
    error_log("Feedback sending failed: " . $errorMessage);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to send feedback. ' . ($errorMessage ?: 'Please check server configuration or try again later.')
    ]);
}

/**
 * Send email via SMTP using PHPMailer (if available)
 */
function sendViaSMTP($to, $subject, $body, $replyTo = '') {
    $errorDetails = '';
    
    try {
        // Load PHPMailer via autoload
        require_once __DIR__ . '/../vendor/autoload.php';
        
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            $errorDetails = "PHPMailer class not found";
            error_log("SMTP Error: " . $errorDetails);
            return ['success' => false, 'error' => $errorDetails];
        }
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // SMTP configuration from environment
        $smtpHost = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
        $smtpUsername = $_ENV['SMTP_USERNAME'] ?? '';
        $smtpPassword = $_ENV['SMTP_PASSWORD'] ?? '';
        $smtpPort = intval($_ENV['SMTP_PORT'] ?? 587);
        $smtpSecure = $_ENV['SMTP_SECURE'] ?? 'tls';
        
        // Log configuration (without password)
        error_log("SMTP Config: Host=$smtpHost, Port=$smtpPort, Secure=$smtpSecure, Username=$smtpUsername");
        
        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUsername;
        $mail->Password = $smtpPassword;
        
        // Set encryption type
        if ($smtpSecure === 'tls') {
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        } elseif ($smtpSecure === 'ssl') {
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        }
        
        $mail->Port = $smtpPort;
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = 0; // Set to 2 for detailed debugging
        $mail->Debugoutput = function($str, $level) use (&$errorDetails) {
            $errorDetails .= $str . "\n";
            error_log("SMTP Debug: " . trim($str));
        };
        
        // Email content
        $fromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? $smtpUsername;
        $mail->setFrom($fromEmail, 'MK Uploader');
        $mail->addAddress($to);
        if (!empty($replyTo)) {
            $mail->addReplyTo($replyTo);
        }
        
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->isHTML(false);
        
        $result = $mail->send();
        
        if ($result) {
            error_log("Email sent successfully via SMTP to: $to");
            return ['success' => true];
        } else {
            $errorDetails = "Send failed: " . $mail->ErrorInfo;
            error_log("SMTP Send Error: " . $errorDetails);
            return ['success' => false, 'error' => $errorDetails];
        }
    } catch (Exception $e) {
        $errorDetails = "Exception: " . $e->getMessage();
        error_log("SMTP Exception: " . $errorDetails);
        return ['success' => false, 'error' => $errorDetails];
    }
}

