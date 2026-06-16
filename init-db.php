<?php
// ─── VYNARA FINANCE – Direct Database Setup Script ──────────────────────────
// This script directly initializes your Neon PostgreSQL database
// Database URL is read from environment variable DATABASE_URL

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');

try {
    $pdo = getDB();

    echo '<pre style="background:#0a1628;color:#c9a84c;padding:30px;font-family:monospace;font-size:13px;border-radius:8px;margin:20px">';
    echo "<strong>VYNARA FINANCE — Initialisation Base de Données Neon</strong>\n";
    echo "═" . str_repeat("═", 70) . "\n\n";

    // 1. CREATE SETTINGS TABLE
    echo "1️⃣  Création de la table 'settings'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            key        VARCHAR(100) PRIMARY KEY,
            value      TEXT NOT NULL DEFAULT '',
            updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
        )
    ");
    echo "   ✅ Table 'settings' créée\n\n";

    // 2. CREATE LOAN APPLICATIONS TABLE
    echo "2️⃣  Création de la table 'loan_applications'...\n";
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
    echo "   ✅ Table 'loan_applications' créée\n\n";

    // 3. CREATE CONTACT MESSAGES TABLE
    echo "3️⃣  Création de la table 'contact_messages'...\n";
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
    echo "   ✅ Table 'contact_messages' créée\n\n";

    // 4. INSERT DEFAULT SETTINGS
    echo "4️⃣  Insertion des paramètres par défaut...\n";
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
    echo "   ✅ Paramètres par défaut insérés\n\n";

    // 5. VERIFY TABLES
    echo "5️⃣  Vérification des tables...\n";
    $tables = $pdo->query("
        SELECT table_name FROM information_schema.tables
        WHERE table_schema = 'public'
    ")->fetchAll();

    foreach ($tables as $table) {
        echo "   ✓ " . $table['table_name'] . "\n";
    }
    echo "\n";

    // 6. VERIFY SETTINGS
    echo "6️⃣  Vérification des paramètres...\n";
    $settings = $pdo->query("SELECT key, value FROM settings")->fetchAll();
    echo "   Trouvé " . count($settings) . " paramètre(s)\n\n";

    echo "═" . str_repeat("═", 70) . "\n";
    echo "<strong style='color:#2ed573'>✅ BASE DE DONNÉES INITIALISÉE AVEC SUCCÈS !</strong>\n";
    echo "═" . str_repeat("═", 70) . "\n\n";

    echo "📊 <strong>Résumé :</strong>\n";
    echo "   • 3 tables créées ✅\n";
    echo "   • Paramètres insérés ✅\n\n";

    echo "🔐 <strong>Prochaines étapes :</strong>\n";
    echo "   1. Accédez à l'admin : /admin007\n";
    echo "   2. Configurez SMTP dans Paramètres pour activer les emails\n";
    echo "   3. Supprimez ce fichier init-db.php\n\n";

    echo "<strong style='color:#ff4757'>⚠️  IMPORTANT :</strong>\n";
    echo "   Supprimez ce fichier init-db.php après utilisation !\n\n";

    echo "</pre>";

} catch (PDOException $e) {
    echo '<pre style="background:#0a1628;color:#ff4757;padding:30px;font-family:monospace;font-size:13px;border-radius:8px;margin:20px">';
    echo "❌ ERREUR DE CONNEXION\n";
    echo "═" . str_repeat("═", 70) . "\n\n";
    echo htmlspecialchars($e->getMessage()) . "\n\n";
    echo "Vérifiez:\n";
    echo "  • La variable d'environnement DATABASE_URL\n";
    echo "  • La connectivité réseau\n";
    echo '</pre>';
} catch (Throwable $e) {
    echo '<pre style="background:#0a1628;color:#ff4757;padding:30px;font-family:monospace;font-size:13px;border-radius:8px;margin:20px">';
    echo "❌ ERREUR\n";
    echo "═" . str_repeat("═", 70) . "\n\n";
    echo htmlspecialchars($e->getMessage()) . "\n";
    echo '</pre>';
}
?>
