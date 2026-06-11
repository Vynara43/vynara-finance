<?php
// ─── VYNARA FINANCE – Database Connection (Neon PostgreSQL) ─────────────────
function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $url = getenv('DATABASE_URL') ?: '';
    if (empty($url)) {
        throw new RuntimeException('DATABASE_URL environment variable not set');
    }

    $parsed = parse_url($url);
    $host   = $parsed['host'] ?? '';
    $port   = $parsed['port'] ?? 5432;
    $dbname = ltrim($parsed['path'] ?? '', '/');
    $user   = $parsed['user'] ?? '';
    $pass   = $parsed['pass'] ?? '';

    // Extract extra params from query string
    $query  = $parsed['query'] ?? '';
    parse_str($query, $params);
    $sslmode = $params['sslmode'] ?? 'require';

    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode={$sslmode}";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}

function getSetting(string $key, string $default = ''): string {
    try {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT value FROM settings WHERE key = ?');
        $stmt->execute([$key]);
        $row  = $stmt->fetch();
        return $row ? $row['value'] : $default;
    } catch (Throwable $e) {
        return $default;
    }
}

function setSetting(string $key, string $value): void {
    $pdo = getDB();
    $pdo->prepare(
        'INSERT INTO settings (key, value, updated_at) VALUES (?, ?, NOW())
         ON CONFLICT (key) DO UPDATE SET value = EXCLUDED.value, updated_at = NOW()'
    )->execute([$key, $value]);
}
