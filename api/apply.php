<?php
// ─── VYNARA FINANCE – Loan Application API ───────────────────────────────────
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

$firstName = sanitize($_POST['first_name'] ?? '');
$lastName  = sanitize($_POST['last_name']  ?? '');
$email     = sanitize($_POST['email']      ?? '');
$phone     = sanitize($_POST['phone']      ?? '');
$amount    = (float)($_POST['amount']      ?? 0);
$purpose   = sanitize($_POST['purpose']    ?? '');
$duration  = (int)($_POST['duration']      ?? 0);
$message   = sanitize($_POST['message']    ?? '');
$lang      = sanitize($_POST['lang']       ?? 'de');
if (!array_key_exists($lang, LANGUAGES)) $lang = 'de';
$country   = sanitize($_POST['country']    ?? (LANGUAGES[$lang]['name'] ?? ''));

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

// Translate purpose label in the user's language
$langFile = LANGUAGES[$lang]['file'] ?? 'de';
$translations = [];
$langPath = dirname(__DIR__) . '/lang/' . $langFile . '.php';
if (is_file($langPath)) require $langPath; // sets global $translations
$purposeLabel = $purpose !== '' ? t('purpose.' . $purpose) : '';
$amountFmt    = number_format($amount, 0, ',', '.');

$data = [
    'firstName' => $firstName, 'lastName' => $lastName, 'email' => $email, 'phone' => $phone,
    'amountFmt' => $amountFmt, 'duration' => $duration, 'purposeLabel' => $purposeLabel,
    'country' => $country, 'message' => $message,
];

$ownerEmail = getSetting('contact_email', SITE_EMAIL);

// 1) Confirmation to the applicant (in their language)
$userMail = vfApplyUserEmail($lang, $data);
vfSendMail($email, $firstName . ' ' . $lastName, $userMail['subject'], $userMail['html']);

// 2) Notification to the site owner (French)
$ownerMail = vfApplyOwnerEmail($data);
vfSendMail($ownerEmail, SITE_NAME, $ownerMail['subject'], $ownerMail['html'], $email);

// Success messages (shown in the UI)
$messages = [
    'da' => 'Din ansøgning er modtaget. Du modtager en bekræftelse på e-mail.',
    'de' => 'Ihr Antrag wurde empfangen. Sie erhalten eine Bestätigung per E-Mail.',
    'at' => 'Ihr Antrag wurde empfangen. Sie erhalten eine Bestätigung per E-Mail.',
    'it' => 'La tua domanda è stata ricevuta. Riceverai una conferma via email.',
    'pt' => 'O seu pedido foi recebido. Receberá uma confirmação por email.',
    'el' => 'Η αίτησή σας ελήφθη. Θα λάβετε επιβεβαίωση μέσω email.',
    'sk' => 'Vaša žiadosť bola prijatá. Potvrdenie dostanete e-mailom.',
    'sl' => 'Vaša vloga je bila prejeta. Potrdilo boste prejeli po e-pošti.',
    'ch' => 'Ihr Antrag wurde empfangen. Sie erhalten eine Bestätigung per E-Mail.',
];
jsonResponse(['success' => true, 'message' => $messages[$lang] ?? $messages['de']]);
