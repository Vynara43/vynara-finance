<?php
// ─── VYNARA FINANCE – Loan Application API ───────────────────────────────────
session_name('vf_sess');
session_start();

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$firstName = sanitize($_POST['first_name'] ?? '');
$lastName  = sanitize($_POST['last_name']  ?? '');
$email     = sanitize($_POST['email']      ?? '');
$phone     = sanitize($_POST['phone']      ?? '');
$amount    = (float)($_POST['amount']      ?? 0);
$purpose   = sanitize($_POST['purpose']    ?? '');
$duration  = (int)($_POST['duration']      ?? 0);
$message   = sanitize($_POST['message']    ?? '');
$lang      = sanitize($_POST['lang']       ?? 'de');
$country   = sanitize($_POST['country']    ?? '');

// Validate
if (strlen($firstName) < 2) jsonResponse(['success' => false, 'message' => 'Prénom invalide'], 422);
if (strlen($lastName)  < 2) jsonResponse(['success' => false, 'message' => 'Nom invalide'],    422);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) jsonResponse(['success' => false, 'message' => 'Email invalide'], 422);
if ($amount < 1000 || $amount > 2000000) jsonResponse(['success' => false, 'message' => 'Montant invalide (1 000 – 2 000 000 €)'], 422);
if ($duration <= 0) jsonResponse(['success' => false, 'message' => 'Durée invalide'], 422);

// Store in DB
try {
    $pdo = getDB();
    $pdo->prepare(
        'INSERT INTO loan_applications
         (first_name, last_name, email, phone, amount, purpose, duration, message, lang, country, status, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, \'pending\', NOW())'
    )->execute([$firstName, $lastName, $email, $phone, $amount, $purpose, $duration, $message, $lang, $country]);
} catch (Throwable $e) {
    error_log('Apply DB error: ' . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Erreur interne. Veuillez réessayer.'], 500);
}

// Send notification email to admin
$smtpHost = getSetting('smtp_host', '');
$smtpPass = getSetting('smtp_pass', '');
$smtpUser = getSetting('smtp_user', SITE_EMAIL);
$smtpPort = (int)getSetting('smtp_port', '587');
$toEmail  = getSetting('contact_email', SITE_EMAIL);

if (!empty($smtpHost) && !empty($smtpPass)) {
    $subject = "[VYNARA] Nouvelle demande de prêt — {$firstName} {$lastName} — €{$amount}";
    $body    = "Nouvelle demande reçue:\n\n";
    $body   .= "Nom: $firstName $lastName\n";
    $body   .= "Email: $email\n";
    $body   .= "Téléphone: $phone\n";
    $body   .= "Montant: €$amount\n";
    $body   .= "Durée: {$duration} mois\n";
    $body   .= "Objet: $purpose\n";
    $body   .= "Pays: $country\n\n";
    $body   .= "Message: $message\n\n";
    $body   .= "Voir dans l'admin: " . SITE_URL . "/admin007?tab=applications";

    try {
        $fp = stream_socket_client("tcp://$smtpHost:$smtpPort", $errno, $errstr, 15);
        if ($fp) {
            fgets($fp);
            fputs($fp, "EHLO vynara-finance.cfd\r\n"); fgets($fp);
            fputs($fp, "AUTH LOGIN\r\n"); fgets($fp);
            fputs($fp, base64_encode($smtpUser)."\r\n"); fgets($fp);
            fputs($fp, base64_encode($smtpPass)."\r\n"); fgets($fp);
            fputs($fp, "MAIL FROM:<$smtpUser>\r\n"); fgets($fp);
            fputs($fp, "RCPT TO:<$toEmail>\r\n"); fgets($fp);
            fputs($fp, "DATA\r\n"); fgets($fp);
            $raw  = "From: VYNARA FINANCE <$smtpUser>\r\n";
            $raw .= "To: $toEmail\r\n";
            $raw .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
            $raw .= "Content-Type: text/plain; charset=utf-8\r\n\r\n";
            $raw .= $body . "\r\n.\r\n";
            fputs($fp, $raw); fgets($fp);
            fputs($fp, "QUIT\r\n");
            fclose($fp);
        }
    } catch (Throwable $e) {
        error_log('Apply SMTP error: ' . $e->getMessage());
    }
}

// Success messages
$messages = [
    'da' => 'Din ansøgning er modtaget. Vi kontakter dig snarest.',
    'de' => 'Ihr Antrag wurde empfangen. Wir melden uns so schnell wie möglich.',
    'at' => 'Ihr Antrag wurde empfangen. Wir melden uns so schnell wie möglich.',
    'it' => 'La tua domanda è stata ricevuta. Ti contatteremo al più presto.',
    'pt' => 'O seu pedido foi recebido. Entraremos em contacto brevemente.',
    'el' => 'Η αίτησή σας ελήφθη. Θα επικοινωνήσουμε μαζί σας σύντομα.',
    'sk' => 'Vaša žiadosť bola prijatá. Kontaktujeme vás čo najskôr.',
    'sl' => 'Vaša vloga je bila prejeta. Kontaktirali vas bomo kmalu.',
    'ch' => 'Ihr Antrag wurde empfangen. Wir melden uns so schnell wie möglich.',
];
jsonResponse(['success' => true, 'message' => $messages[$lang] ?? $messages['de']]);
