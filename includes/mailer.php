<?php
// ─── VYNARA FINANCE – Mailer (SMTP + multilingual templates) ─────────────────
if (!defined('SITE_NAME')) die();

/**
 * Robust SMTP sender. Supports SSL (port 465) and STARTTLS (port 587).
 * Reads SMTP credentials from the `settings` table.
 * Returns true on success, false otherwise (errors are logged).
 */
function vfSendMail(string $to, string $toName, string $subject, string $htmlBody, ?string $replyTo = null): bool {
    $host = getSetting('smtp_host', '');
    $pass = getSetting('smtp_pass', '');
    $user = getSetting('smtp_user', SITE_EMAIL);
    $port = (int) getSetting('smtp_port', '587');

    if ($host === '' || $pass === '') {
        error_log('vfSendMail: SMTP not configured (smtp_host/smtp_pass empty)');
        return false;
    }

    $secure  = ($port === 465);
    $remote  = ($secure ? 'ssl://' : 'tcp://') . $host . ':' . $port;
    $ctx     = stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);

    $fp = @stream_socket_client($remote, $errno, $errstr, 20, STREAM_CLIENT_CONNECT, $ctx);
    if (!$fp) { error_log("vfSendMail: connect failed ($errno) $errstr"); return false; }
    stream_set_timeout($fp, 20);

    $read = function () use ($fp) {
        $data = '';
        while (($line = fgets($fp, 515)) !== false) {
            $data .= $line;
            if (strlen($line) < 4 || $line[3] === ' ') break;
        }
        return $data;
    };
    $code = fn($r) => (int) substr((string) $r, 0, 3);
    $cmd  = function (string $c) use ($fp, $read) { fputs($fp, $c . "\r\n"); return $read(); };

    try {
        if ($code($read()) !== 220) { fclose($fp); return false; }
        if ($code($cmd('EHLO ' . SITE_DOMAIN)) !== 250) { fclose($fp); return false; }

        if (!$secure) {
            if ($code($cmd('STARTTLS')) !== 220) { fclose($fp); return false; }
            $crypto = STREAM_CRYPTO_METHOD_TLS_CLIENT;
            if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
                $crypto |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
            }
            if (!@stream_socket_enable_crypto($fp, true, $crypto)) { fclose($fp); return false; }
            $cmd('EHLO ' . SITE_DOMAIN);
        }

        if ($code($cmd('AUTH LOGIN')) !== 334) { fclose($fp); return false; }
        if ($code($cmd(base64_encode($user))) !== 334) { fclose($fp); return false; }
        if ($code($cmd(base64_encode($pass))) !== 235) { error_log('vfSendMail: auth failed'); fclose($fp); return false; }

        if ($code($cmd("MAIL FROM:<{$user}>")) !== 250) { fclose($fp); return false; }
        if (!in_array($code($cmd("RCPT TO:<{$to}>")), [250, 251], true)) { fclose($fp); return false; }
        if ($code($cmd('DATA')) !== 354) { fclose($fp); return false; }

        $headers  = 'From: =?UTF-8?B?' . base64_encode(SITE_NAME) . "?= <{$user}>\r\n";
        $headers .= 'To: ' . ($toName !== '' ? '=?UTF-8?B?' . base64_encode($toName) . '?= ' : '') . "<{$to}>\r\n";
        if ($replyTo) $headers .= "Reply-To: <{$replyTo}>\r\n";
        $headers .= 'Subject: =?UTF-8?B?' . base64_encode($subject) . "?=\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "Content-Transfer-Encoding: base64\r\n";
        $body = chunk_split(base64_encode($htmlBody));

        fputs($fp, $headers . "\r\n" . $body . "\r\n.\r\n");
        $ok = $code($read()) === 250;
        $cmd('QUIT');
        fclose($fp);
        if (!$ok) error_log('vfSendMail: message not accepted');
        return $ok;
    } catch (Throwable $e) {
        error_log('vfSendMail exception: ' . $e->getMessage());
        if (is_resource($fp)) fclose($fp);
        return false;
    }
}

/** Branded HTML wrapper for all emails. */
function vfEmailWrap(string $heading, string $bodyHtml): string {
    $year = date('Y');
    return '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"></head>'
        . '<body style="margin:0;padding:0;background:#f4f5f7;">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f5f7;padding:24px 12px;font-family:Arial,Helvetica,sans-serif;">'
        . '<tr><td align="center">'
        . '<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #e6e8eb;">'
        . '<tr><td style="background:#0a1628;padding:26px 32px;text-align:center;">'
        . '<span style="font-size:22px;font-weight:800;color:#ffffff;letter-spacing:1px;">VYNARA <span style="color:#c9a84c;">FINANCE</span></span>'
        . '</td></tr>'
        . '<tr><td style="padding:36px 32px;color:#1a2433;font-size:15px;line-height:1.6;">'
        . '<h1 style="margin:0 0 20px;font-size:22px;color:#0a1628;">' . $heading . '</h1>'
        . $bodyHtml
        . '</td></tr>'
        . '<tr><td style="background:#0a1628;padding:18px 32px;text-align:center;color:#8a93a3;font-size:12px;">'
        . '&copy; ' . $year . ' VYNARA FINANCE &middot; <a href="' . SITE_URL . '" style="color:#c9a84c;text-decoration:none;">vynara-finance.cfd</a>'
        . '</td></tr>'
        . '</table></td></tr></table></body></html>';
}

/** Localized strings for client-facing emails. */
function vfEmailStrings(string $lang): array {
    $L = [
        'da' => [
            'greeting' => 'Hej', 'thanks' => 'Med venlig hilsen', 'signature' => 'VYNARA FINANCE-teamet',
            'apply_subject' => 'Din låneansøgning er modtaget — VYNARA FINANCE',
            'apply_heading' => 'Ansøgning modtaget ✓',
            'apply_intro'   => 'Vi har modtaget din låneansøgning. Tak for din tillid.',
            'apply_next'    => 'Vores team gennemgår din sag og vender tilbage til dig inden for 48 timer.',
            'l_amount' => 'Beløb', 'l_duration' => 'Løbetid', 'l_purpose' => 'Formål', 'l_months' => 'måneder',
            'contact_subject' => 'Vi har modtaget din besked — VYNARA FINANCE',
            'contact_heading' => 'Besked modtaget ✓',
            'contact_intro'   => 'Vi har modtaget din besked og vender tilbage til dig inden for 24–48 timer.',
        ],
        'de' => [
            'greeting' => 'Hallo', 'thanks' => 'Mit freundlichen Grüßen', 'signature' => 'Ihr VYNARA FINANCE Team',
            'apply_subject' => 'Ihr Kreditantrag ist eingegangen — VYNARA FINANCE',
            'apply_heading' => 'Antrag eingegangen ✓',
            'apply_intro'   => 'Wir haben Ihren Kreditantrag erhalten. Vielen Dank für Ihr Vertrauen.',
            'apply_next'    => 'Unser Team prüft Ihren Antrag und meldet sich innerhalb von 48 Stunden bei Ihnen.',
            'l_amount' => 'Betrag', 'l_duration' => 'Laufzeit', 'l_purpose' => 'Zweck', 'l_months' => 'Monate',
            'contact_subject' => 'Wir haben Ihre Nachricht erhalten — VYNARA FINANCE',
            'contact_heading' => 'Nachricht erhalten ✓',
            'contact_intro'   => 'Wir haben Ihre Nachricht erhalten und melden uns innerhalb von 24–48 Stunden bei Ihnen.',
        ],
        'it' => [
            'greeting' => 'Gentile', 'thanks' => 'Cordiali saluti', 'signature' => 'Il team VYNARA FINANCE',
            'apply_subject' => 'La tua richiesta di prestito è stata ricevuta — VYNARA FINANCE',
            'apply_heading' => 'Richiesta ricevuta ✓',
            'apply_intro'   => 'Abbiamo ricevuto la tua richiesta di prestito. Grazie per la fiducia.',
            'apply_next'    => 'Il nostro team esaminerà la tua richiesta e ti risponderà entro 48 ore.',
            'l_amount' => 'Importo', 'l_duration' => 'Durata', 'l_purpose' => 'Scopo', 'l_months' => 'mesi',
            'contact_subject' => 'Abbiamo ricevuto il tuo messaggio — VYNARA FINANCE',
            'contact_heading' => 'Messaggio ricevuto ✓',
            'contact_intro'   => 'Abbiamo ricevuto il tuo messaggio e ti risponderemo entro 24–48 ore.',
        ],
        'pt' => [
            'greeting' => 'Olá', 'thanks' => 'Com os melhores cumprimentos', 'signature' => 'A equipa VYNARA FINANCE',
            'apply_subject' => 'O seu pedido de empréstimo foi recebido — VYNARA FINANCE',
            'apply_heading' => 'Pedido recebido ✓',
            'apply_intro'   => 'Recebemos o seu pedido de empréstimo. Obrigado pela sua confiança.',
            'apply_next'    => 'A nossa equipa irá analisar o seu pedido e entrará em contacto consigo no prazo de 48 horas.',
            'l_amount' => 'Montante', 'l_duration' => 'Prazo', 'l_purpose' => 'Finalidade', 'l_months' => 'meses',
            'contact_subject' => 'Recebemos a sua mensagem — VYNARA FINANCE',
            'contact_heading' => 'Mensagem recebida ✓',
            'contact_intro'   => 'Recebemos a sua mensagem e entraremos em contacto consigo no prazo de 24–48 horas.',
        ],
        'el' => [
            'greeting' => 'Γεια σας', 'thanks' => 'Με εκτίμηση', 'signature' => 'Η ομάδα της VYNARA FINANCE',
            'apply_subject' => 'Η αίτηση δανείου σας ελήφθη — VYNARA FINANCE',
            'apply_heading' => 'Η αίτηση ελήφθη ✓',
            'apply_intro'   => 'Λάβαμε την αίτηση δανείου σας. Σας ευχαριστούμε για την εμπιστοσύνη σας.',
            'apply_next'    => 'Η ομάδα μας θα εξετάσει την αίτησή σας και θα επικοινωνήσει μαζί σας εντός 48 ωρών.',
            'l_amount' => 'Ποσό', 'l_duration' => 'Διάρκεια', 'l_purpose' => 'Σκοπός', 'l_months' => 'μήνες',
            'contact_subject' => 'Λάβαμε το μήνυμά σας — VYNARA FINANCE',
            'contact_heading' => 'Το μήνυμα ελήφθη ✓',
            'contact_intro'   => 'Λάβαμε το μήνυμά σας και θα επικοινωνήσουμε μαζί σας εντός 24–48 ωρών.',
        ],
        'sk' => [
            'greeting' => 'Dobrý deň', 'thanks' => 'S pozdravom', 'signature' => 'Tím VYNARA FINANCE',
            'apply_subject' => 'Vaša žiadosť o pôžičku bola prijatá — VYNARA FINANCE',
            'apply_heading' => 'Žiadosť prijatá ✓',
            'apply_intro'   => 'Prijali sme vašu žiadosť o pôžičku. Ďakujeme za vašu dôveru.',
            'apply_next'    => 'Náš tím posúdi vašu žiadosť a ozve sa vám do 48 hodín.',
            'l_amount' => 'Suma', 'l_duration' => 'Doba splácania', 'l_purpose' => 'Účel', 'l_months' => 'mesiacov',
            'contact_subject' => 'Prijali sme vašu správu — VYNARA FINANCE',
            'contact_heading' => 'Správa prijatá ✓',
            'contact_intro'   => 'Prijali sme vašu správu a ozveme sa vám do 24 – 48 hodín.',
        ],
        'sl' => [
            'greeting' => 'Pozdravljeni', 'thanks' => 'Lep pozdrav', 'signature' => 'Ekipa VYNARA FINANCE',
            'apply_subject' => 'Vaša vloga za posojilo je bila prejeta — VYNARA FINANCE',
            'apply_heading' => 'Vloga prejeta ✓',
            'apply_intro'   => 'Prejeli smo vašo vlogo za posojilo. Hvala za zaupanje.',
            'apply_next'    => 'Naša ekipa bo pregledala vašo vlogo in se vam oglasila v 48 urah.',
            'l_amount' => 'Znesek', 'l_duration' => 'Doba odplačevanja', 'l_purpose' => 'Namen', 'l_months' => 'mesecev',
            'contact_subject' => 'Prejeli smo vaše sporočilo — VYNARA FINANCE',
            'contact_heading' => 'Sporočilo prejeto ✓',
            'contact_intro'   => 'Prejeli smo vaše sporočilo in se vam bomo oglasili v 24–48 urah.',
        ],
    ];
    // at & ch use German
    $map = ['at' => 'de', 'ch' => 'de'];
    $lang = $map[$lang] ?? $lang;
    return $L[$lang] ?? $L['de'];
}

function vfRow(string $label, string $value): string {
    return '<tr>'
        . '<td style="padding:8px 0;color:#6b7480;font-size:13px;border-bottom:1px solid #eef0f2;width:42%;">' . $label . '</td>'
        . '<td style="padding:8px 0;color:#0a1628;font-size:14px;font-weight:700;border-bottom:1px solid #eef0f2;">' . $value . '</td>'
        . '</tr>';
}

/** Confirmation email for a loan applicant (in their language). */
function vfApplyUserEmail(string $lang, array $d): array {
    $s = vfEmailStrings($lang);
    $name = h($d['firstName']);
    $summary = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:18px 0;">'
        . vfRow($s['l_amount'], '€' . h($d['amountFmt']))
        . vfRow($s['l_duration'], h((string) $d['duration']) . ' ' . $s['l_months'])
        . ($d['purposeLabel'] !== '' ? vfRow($s['l_purpose'], h($d['purposeLabel'])) : '')
        . '</table>';
    $body = '<p style="margin:0 0 14px;">' . $s['greeting'] . ' ' . $name . ',</p>'
        . '<p style="margin:0 0 14px;">' . $s['apply_intro'] . '</p>'
        . $summary
        . '<p style="margin:14px 0;">' . $s['apply_next'] . '</p>'
        . '<p style="margin:24px 0 0;color:#6b7480;">' . $s['thanks'] . ',<br><strong style="color:#0a1628;">' . $s['signature'] . '</strong></p>';
    return ['subject' => $s['apply_subject'], 'html' => vfEmailWrap($s['apply_heading'], $body)];
}

/** Confirmation email for a contact message (in their language). */
function vfContactUserEmail(string $lang, array $d): array {
    $s = vfEmailStrings($lang);
    $name = h($d['name']);
    $body = '<p style="margin:0 0 14px;">' . $s['greeting'] . ' ' . $name . ',</p>'
        . '<p style="margin:0 0 14px;">' . $s['contact_intro'] . '</p>'
        . '<p style="margin:24px 0 0;color:#6b7480;">' . $s['thanks'] . ',<br><strong style="color:#0a1628;">' . $s['signature'] . '</strong></p>';
    return ['subject' => $s['contact_subject'], 'html' => vfEmailWrap($s['contact_heading'], $body)];
}

/** Internal notification (French) for the site owner — new loan application. */
function vfApplyOwnerEmail(array $d): array {
    $summary = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:18px 0;">'
        . vfRow('Nom', h($d['firstName'] . ' ' . $d['lastName']))
        . vfRow('Email', h($d['email']))
        . vfRow('Téléphone', h($d['phone']))
        . vfRow('Montant', '€' . h($d['amountFmt']))
        . vfRow('Durée', h((string) $d['duration']) . ' mois')
        . vfRow('Objet', h($d['purposeLabel']))
        . vfRow('Pays', h($d['country']))
        . '</table>';
    $msg = $d['message'] !== '' ? '<p style="margin:0 0 6px;color:#6b7480;font-size:13px;">Message :</p><p style="margin:0 0 14px;background:#f7f8fa;border-radius:8px;padding:12px;">' . nl2br(h($d['message'])) . '</p>' : '';
    $body = '<p style="margin:0 0 14px;">Une nouvelle demande de prêt a été soumise.</p>'
        . $summary . $msg
        . '<p style="margin:18px 0 0;"><a href="' . SITE_URL . '/admin007" style="display:inline-block;background:#c9a84c;color:#0a1628;font-weight:700;text-decoration:none;padding:11px 22px;border-radius:8px;">Voir dans l\'admin →</a></p>';
    return ['subject' => '[VYNARA] Nouvelle demande de prêt — ' . $d['firstName'] . ' ' . $d['lastName'] . ' — €' . $d['amountFmt'], 'html' => vfEmailWrap('Nouvelle demande de prêt', $body)];
}

/** Internal notification (French) for the site owner — new contact message. */
function vfContactOwnerEmail(array $d): array {
    $summary = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:18px 0;">'
        . vfRow('Nom', h($d['name']))
        . vfRow('Email', h($d['email']))
        . vfRow('Sujet', h($d['subject']))
        . '</table>'
        . '<p style="margin:0 0 6px;color:#6b7480;font-size:13px;">Message :</p><p style="margin:0 0 14px;background:#f7f8fa;border-radius:8px;padding:12px;">' . nl2br(h($d['message'])) . '</p>';
    $body = '<p style="margin:0 0 14px;">Un nouveau message a été reçu via le formulaire de contact.</p>'
        . $summary
        . '<p style="margin:18px 0 0;"><a href="' . SITE_URL . '/admin007" style="display:inline-block;background:#c9a84c;color:#0a1628;font-weight:700;text-decoration:none;padding:11px 22px;border-radius:8px;">Voir dans l\'admin →</a></p>';
    return ['subject' => '[VYNARA] Nouveau message de ' . $d['name'], 'html' => vfEmailWrap('Nouveau message de contact', $body)];
}
