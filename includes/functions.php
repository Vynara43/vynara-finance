<?php
// ─── VYNARA FINANCE – Utility Functions ─────────────────────────────────────

function detectLanguage(): string {
    // 1. URL param
    if (!empty($_GET['lang']) && array_key_exists($_GET['lang'], LANGUAGES)) {
        return $_GET['lang'];
    }
    // 2. Session
    if (!empty($_SESSION['lang']) && array_key_exists($_SESSION['lang'], LANGUAGES)) {
        return $_SESSION['lang'];
    }
    // 3. Cookie
    if (!empty($_COOKIE['vf_lang']) && array_key_exists($_COOKIE['vf_lang'], LANGUAGES)) {
        return $_COOKIE['vf_lang'];
    }
    // 4. Accept-Language header
    $accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    if ($accept) {
        $parts = explode(',', $accept);
        foreach ($parts as $part) {
            $tag = strtolower(trim(explode(';', $part)[0]));
            $code2 = substr($tag, 0, 2);
            $full = str_replace('-', '_', $tag);
            // Check region-specific first
            foreach (LANGUAGES as $code => $info) {
                if ($code === $tag || $code === $full) return $code;
            }
            // Fallback to 2-letter
            if (array_key_exists($code2, LANGUAGES)) return $code2;
        }
    }
    return 'de'; // Default: German
}

function setLanguage(string $lang): void {
    if (!array_key_exists($lang, LANGUAGES)) $lang = 'de';
    $_SESSION['lang'] = $lang;
    setcookie('vf_lang', $lang, time() + (86400 * 365), '/', '', true, false);
}

function t(string $key, array $replace = []): string {
    global $translations;
    $str = $translations[$key] ?? $key;
    foreach ($replace as $k => $v) {
        $str = str_replace('{' . $k . '}', htmlspecialchars((string)$v, ENT_QUOTES), $str);
    }
    return $str;
}

function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function currentPage(): string {
    $uri   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $parts = explode('/', trim($uri, '/'));
    return $parts[0] ?: 'home';
}

function getLangUrl(string $lang): string {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $qs  = $_GET;
    $qs['lang'] = $lang;
    return $uri . '?' . http_build_query($qs);
}

function redirect(string $url): void {
    header('Location: ' . $url, true, 302);
    exit;
}

function whatsappMessage(string $lang): string {
    $messages = [
        'da' => 'Hej! Jeg er interesseret i et lån fra VYNARA FINANCE. Kan I hjælpe mig?',
        'de' => 'Hallo! Ich interessiere mich für einen Kredit bei VYNARA FINANCE. Können Sie mir helfen?',
        'at' => 'Hallo! Ich interessiere mich für einen Kredit bei VYNARA FINANCE. Können Sie mir helfen?',
        'it' => 'Salve! Sono interessato/a a un prestito presso VYNARA FINANCE. Potete aiutarmi?',
        'pt' => 'Olá! Tenho interesse num empréstimo da VYNARA FINANCE. Podem ajudar-me?',
        'el' => 'Γεια σας! Ενδιαφέρομαι για δάνειο από την VYNARA FINANCE. Μπορείτε να με βοηθήσετε;',
        'sk' => 'Dobrý deň! Mám záujem o pôžičku od VYNARA FINANCE. Môžete mi pomôcť?',
        'sl' => 'Pozdravljeni! Zanima me posojilo pri VYNARA FINANCE. Mi lahko pomagate?',
        'ch' => 'Hallo! Ich interessiere mich für einen Kredit bei VYNARA FINANCE. Können Sie mir helfen?',
    ];
    return $messages[$lang] ?? $messages['de'];
}

function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function isAdminLoggedIn(): bool {
    return !empty($_SESSION['vf_admin']) && $_SESSION['vf_admin'] === true;
}

function formatDate(string $date): string {
    return date('d/m/Y H:i', strtotime($date));
}

function sanitize(string $str): string {
    return trim(strip_tags($str));
}
