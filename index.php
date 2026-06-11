<?php
// ─── VYNARA FINANCE – Main Router ────────────────────────────────────────────
session_name('vf_sess');
session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// ─ Determine language ─────────────────────────────────────────────────────────
if (!empty($_GET['lang']) && array_key_exists($_GET['lang'], LANGUAGES)) {
    setLanguage($_GET['lang']);
} elseif (empty($_SESSION['lang'])) {
    setLanguage(detectLanguage());
}
$lang = $_SESSION['lang'] ?? 'de';
$langFile = LANGUAGES[$lang]['file'] ?? 'de';
require_once __DIR__ . '/lang/' . $langFile . '.php';

// ─ Route ──────────────────────────────────────────────────────────────────────
$uri   = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri   = rtrim($uri, '/') ?: '/';
$parts = array_values(array_filter(explode('/', $uri)));
$seg   = $parts[0] ?? '';

// API routes ─────────────────────────────────────────────────────────────────
if ($seg === 'api') {
    $endpoint = $parts[1] ?? '';
    $file     = __DIR__ . '/api/' . basename($endpoint) . '.php';
    if (file_exists($file)) { require $file; } else { http_response_code(404); echo '{"error":"Not found"}'; }
    exit;
}

// Admin route ─────────────────────────────────────────────────────────────────
if ($seg === ADMIN_PATH) {
    require __DIR__ . '/admin007/index.php';
    exit;
}

// Page map ────────────────────────────────────────────────────────────────────
$pageMap = [
    ''          => 'home',
    'home'      => 'home',
    'services'  => 'services',
    'process'   => 'process',
    'about'     => 'about',
    'contact'   => 'contact',
    'apply'     => 'apply',
    'privacy'   => 'privacy',
    'terms'     => 'terms',
];

$page = $pageMap[$seg] ?? '404';

// Output ──────────────────────────────────────────────────────────────────────
require __DIR__ . '/includes/header.php';
$pageFile = __DIR__ . '/pages/' . $page . '.php';
if (file_exists($pageFile)) {
    require $pageFile;
} else {
    http_response_code(404);
    echo '<section style="padding:160px 0;text-align:center"><div class="container"><h1>404</h1><p style="margin:20px 0">Page non trouvée.</p><a href="/" class="btn btn-primary">Retour</a></div></section>';
}
require __DIR__ . '/includes/footer.php';
