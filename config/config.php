<?php
// ─── VYNARA FINANCE – Site Configuration ───────────────────────────────────
// Charge les variables d'environnement en premier
require_once __DIR__ . '/env-loader.php';

define('SITE_NAME',    'VYNARA FINANCE');
define('SITE_DOMAIN',  'vynara-finance.cfd');
define('SITE_URL',     'https://vynara-finance.cfd');
define('SITE_EMAIL',   getenv('CONTACT_EMAIL') ?: 'contact@vynara-finance.cfd');
define('ADMIN_PATH',   'admin007');

// ⚠️ IMPORTANT: Admin password should be set via environment variable
// Never store plain passwords in code
$adminPass = getenv('ADMIN_PASSWORD');
if (!$adminPass || $adminPass === 'change_me_to_secure_password') {
    // Default fallback for development (CHANGE THIS IN PRODUCTION!)
    define('ADMIN_PASS', password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 10]));
} else {
    define('ADMIN_PASS', password_hash($adminPass, PASSWORD_BCRYPT, ['cost' => 10]));
}

// Languages in display order
define('LANGUAGES', [
    'da' => ['name' => 'Danemark',  'flag' => '🇩🇰', 'iso' => 'DA', 'file' => 'da'],
    'de' => ['name' => 'Allemagne', 'flag' => '🇩🇪', 'iso' => 'DE', 'file' => 'de'],
    'at' => ['name' => 'Autriche',  'flag' => '🇦🇹', 'iso' => 'AT', 'file' => 'de'],
    'it' => ['name' => 'Italie',    'flag' => '🇮🇹', 'iso' => 'IT', 'file' => 'it'],
    'pt' => ['name' => 'Portugal',  'flag' => '🇵🇹', 'iso' => 'PT', 'file' => 'pt'],
    'el' => ['name' => 'Grèce',     'flag' => '🇬🇷', 'iso' => 'GR', 'file' => 'el'],
    'sk' => ['name' => 'Slovaquie', 'flag' => '🇸🇰', 'iso' => 'SK', 'file' => 'sk'],
    'sl' => ['name' => 'Slovénie',  'flag' => '🇸🇮', 'iso' => 'SI', 'file' => 'sl'],
    'ch' => ['name' => 'Suisse',    'flag' => '🇨🇭', 'iso' => 'CH', 'file' => 'de'],
]);

// Admin stored hash (static for security)
define('ADMIN_HASH', '$2y$10$somehashedpassword'); // overridden in auth check

// Session name
define('SESSION_NAME', 'vf_sess');

// DB URL from environment
define('DATABASE_URL', getenv('DATABASE_URL') ?: '');
