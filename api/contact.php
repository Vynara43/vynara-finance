<?php
// ─── VYNARA FINANCE – Contact Form API ───────────────────────────────────────
session_name('vf_sess');
session_start();

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/mailer.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$name    = sanitize($_POST['name']    ?? '');
$email   = sanitize($_POST['email']   ?? '');
$subject = sanitize($_POST['subject'] ?? '');
$message = sanitize($_POST['message'] ?? '');
$lang    = sanitize($_POST['lang']    ?? 'de');
if (!array_key_exists($lang, LANGUAGES)) $lang = 'de';

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

$ownerEmail = getSetting('contact_email', SITE_EMAIL);
$data = ['name' => $name, 'email' => $email, 'subject' => $subject, 'message' => $message];

// 1) Confirmation to the visitor (in their language)
$userMail = vfContactUserEmail($lang, $data);
vfSendMail($email, $name, $userMail['subject'], $userMail['html']);

// 2) Notification to the site owner (French)
$ownerMail = vfContactOwnerEmail($data);
vfSendMail($ownerEmail, SITE_NAME, $ownerMail['subject'], $ownerMail['html'], $email);

// Success message per language
$messages = [
    'da' => 'Tak! Din besked er modtaget. Du modtager snart en bekræftelse på e-mail.',
    'de' => 'Danke! Ihre Nachricht wurde empfangen. Sie erhalten in Kürze eine Bestätigung per E-Mail.',
    'at' => 'Danke! Ihre Nachricht wurde empfangen. Sie erhalten in Kürze eine Bestätigung per E-Mail.',
    'it' => 'Grazie! Il tuo messaggio è stato ricevuto. Riceverai a breve una conferma via email.',
    'pt' => 'Obrigado! A sua mensagem foi recebida. Receberá em breve uma confirmação por email.',
    'el' => 'Ευχαριστούμε! Το μήνυμά σας ελήφθη. Θα λάβετε σύντομα επιβεβαίωση μέσω email.',
    'sk' => 'Ďakujeme! Vaša správa bola prijatá. Čoskoro dostanete potvrdenie e-mailom.',
    'sl' => 'Hvala! Vaše sporočilo je bilo prejeto. Kmalu boste prejeli potrdilo po e-pošti.',
    'ch' => 'Danke! Ihre Nachricht wurde empfangen. Sie erhalten in Kürze eine Bestätigung per E-Mail.',
];
jsonResponse(['success' => true, 'message' => $messages[$lang] ?? $messages['de']]);
