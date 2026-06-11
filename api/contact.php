<?php
// ─── VYNARA FINANCE – Contact Form API ───────────────────────────────────────
session_name('vf_sess');
session_start();

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$name    = sanitize($_POST['name']    ?? '');
$email   = sanitize($_POST['email']   ?? '');
$subject = sanitize($_POST['subject'] ?? '');
$message = sanitize($_POST['message'] ?? '');
$lang    = sanitize($_POST['lang']    ?? 'de');

// Validate
if (strlen($name) < 2) jsonResponse(['success' => false, 'message' => 'Nom invalide'], 422);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) jsonResponse(['success' => false, 'message' => 'Email invalide'], 422);
if (strlen($message) < 10) jsonResponse(['success' => false, 'message' => 'Message trop court'], 422);

// Store in DB
try {
    $pdo = getDB();
    $pdo->prepare(
        'INSERT INTO contact_messages (name, email, subject, message, lang, is_read, created_at)
         VALUES (?, ?, ?, ?, ?, FALSE, NOW())'
    )->execute([$name, $email, $subject, $message, $lang]);
} catch (Throwable $e) {
    error_log('Contact DB error: ' . $e->getMessage());
}

// Send email notification
$smtpHost = getSetting('smtp_host', '');
$smtpPass = getSetting('smtp_pass', '');
$smtpUser = getSetting('smtp_user', SITE_EMAIL);
$smtpPort = (int)getSetting('smtp_port', '587');
$toEmail  = getSetting('contact_email', SITE_EMAIL);

if (!empty($smtpHost) && !empty($smtpPass)) {
    sendEmail(
        $toEmail,
        '[VYNARA] Nouveau message de ' . $name,
        "Nom: $name\nEmail: $email\nSujet: $subject\n\nMessage:\n$message",
        $smtpHost, $smtpPort, $smtpUser, $smtpPass
    );
}

// Success message per language
$messages = [
    'da' => 'Tak! Din besked er modtaget.',
    'de' => 'Danke! Ihre Nachricht wurde empfangen.',
    'at' => 'Danke! Ihre Nachricht wurde empfangen.',
    'it' => 'Grazie! Il tuo messaggio è stato ricevuto.',
    'pt' => 'Obrigado! A sua mensagem foi recebida.',
    'el' => 'Ευχαριστούμε! Το μήνυμά σας ελήφθη.',
    'sk' => 'Ďakujeme! Vaša správa bola prijatá.',
    'sl' => 'Hvala! Vaše sporočilo je bilo prejeto.',
    'ch' => 'Danke! Ihre Nachricht wurde empfangen.',
];
jsonResponse(['success' => true, 'message' => $messages[$lang] ?? $messages['de']]);

function sendEmail(string $to, string $subject, string $body, string $host, int $port, string $user, string $pass): void {
    // Simple SMTP via stream (no dependency)
    try {
        $fp = stream_socket_client("tcp://$host:$port", $errno, $errstr, 15);
        if (!$fp) return;
        $r = fgets($fp);
        fputs($fp, "EHLO vynara-finance.cfd\r\n"); $r = fgets($fp);
        fputs($fp, "AUTH LOGIN\r\n"); $r = fgets($fp);
        fputs($fp, base64_encode($user)."\r\n"); $r = fgets($fp);
        fputs($fp, base64_encode($pass)."\r\n"); $r = fgets($fp);
        fputs($fp, "MAIL FROM:<$user>\r\n"); $r = fgets($fp);
        fputs($fp, "RCPT TO:<$to>\r\n"); $r = fgets($fp);
        fputs($fp, "DATA\r\n"); $r = fgets($fp);
        $msg  = "From: VYNARA FINANCE <$user>\r\n";
        $msg .= "To: $to\r\n";
        $msg .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
        $msg .= "Content-Type: text/plain; charset=utf-8\r\n\r\n";
        $msg .= $body . "\r\n.\r\n";
        fputs($fp, $msg); $r = fgets($fp);
        fputs($fp, "QUIT\r\n");
        fclose($fp);
    } catch (Throwable $e) {
        error_log('SMTP error: ' . $e->getMessage());
    }
}
