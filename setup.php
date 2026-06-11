<?php
// ─── VYNARA FINANCE – Database Setup (run once) ──────────────────────────────
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Optional: simple token protection
$token = $_GET['token'] ?? '';
if ($token !== 'setup-vynara-2026') {
    http_response_code(403);
    die('Accès refusé. Ajoutez ?token=setup-vynara-2026 à l\'URL.');
}

header('Content-Type: text/html; charset=utf-8');
echo '<pre style="background:#0a1628;color:#c9a84c;padding:30px;font-family:monospace;font-size:13px">';
echo "<strong>VYNARA FINANCE — Database Setup</strong>\n\n";

try {
    $pdo = getDB();
    echo "✓ Connexion DB réussie\n\n";

    // Settings table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            key        VARCHAR(100) PRIMARY KEY,
            value      TEXT NOT NULL DEFAULT '',
            updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
        )
    ");
    echo "✓ Table 'settings' créée\n";

    // Loan applications table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS loan_applications (
            id         SERIAL PRIMARY KEY,
            first_name VARCHAR(100) NOT NULL,
            last_name  VARCHAR(100) NOT NULL,
            email      VARCHAR(255) NOT NULL,
            phone      VARCHAR(50),
            amount     NUMERIC(12,2) NOT NULL,
            purpose    VARCHAR(100),
            duration   INTEGER,
            message    TEXT,
            lang       VARCHAR(10),
            country    VARCHAR(100),
            status     VARCHAR(20) NOT NULL DEFAULT 'pending',
            created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
        )
    ");
    echo "✓ Table 'loan_applications' créée\n";

    // Contact messages table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS contact_messages (
            id         SERIAL PRIMARY KEY,
            name       VARCHAR(200) NOT NULL,
            email      VARCHAR(255) NOT NULL,
            subject    VARCHAR(300),
            message    TEXT NOT NULL,
            lang       VARCHAR(10),
            is_read    BOOLEAN NOT NULL DEFAULT FALSE,
            created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
        )
    ");
    echo "✓ Table 'contact_messages' créée\n\n";

    // Default settings
    $defaults = [
        ['whatsapp_number', ''],
        ['contact_email',   'contact@vynara-finance.cfd'],
        ['smtp_host',       ''],
        ['smtp_port',       '587'],
        ['smtp_user',       'contact@vynara-finance.cfd'],
        ['smtp_pass',       ''],
    ];
    foreach ($defaults as [$k, $v]) {
        $pdo->prepare(
            'INSERT INTO settings (key, value) VALUES (?, ?) ON CONFLICT (key) DO NOTHING'
        )->execute([$k, $v]);
    }
    echo "✓ Paramètres par défaut insérés\n\n";
    echo "<strong style='color:#2ed573'>✅ Installation terminée avec succès !</strong>\n\n";
    echo "Vous pouvez maintenant accéder à l'admin : <a href='/admin007' style='color:#c9a84c'>/admin007</a>\n";
    echo "<strong style='color:#ff4757'>⚠️ Supprimez ce fichier setup.php après installation !</strong>\n";

} catch (Throwable $e) {
    echo "<strong style='color:#ff4757'>❌ Erreur : " . htmlspecialchars($e->getMessage()) . "</strong>\n";
}
echo '</pre>';
