<?php
// ─── VYNARA FINANCE – Load .env variables safely ──────────────────────────────
// Charge les variables d'environnement depuis .env ou depuis les variables système

function loadEnv(string $filePath = '.env'): void {
    if (!file_exists($filePath)) {
        return; // Pas de fichier .env, utilise les variables d'environnement du système
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignore les commentaires
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Retire les guillemets si présents
            if ((strlen($value) > 1 && $value[0] === '"' && substr($value, -1) === '"') ||
                (strlen($value) > 1 && $value[0] === "'" && substr($value, -1) === "'")) {
                $value = substr($value, 1, -1);
            }

            // Définit la variable d'environnement
            if (!getenv($key)) {
                putenv("$key=$value");
            }
        }
    }
}

// Charge le fichier .env si présent (pour développement local)
loadEnv(__DIR__ . '/.env');

// Alternativement, si vous utilisez Vercel ou un autre service, 
// assurez-vous que DATABASE_URL est défini dans les variables d'environnement
