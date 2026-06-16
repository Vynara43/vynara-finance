<?php
// ─── VYNARA FINANCE – Database Connection (Neon PostgreSQL) ─────────────────
function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    // Support both DB_URL (Vercel) and DATABASE_URL (standard)
    $url = getenv('DB_URL') ?: getenv('DATABASE_URL') ?: '';
    if (empty($url)) {
        throw new RuntimeException('No database URL set (DB_URL or DATABASE_URL)');
    }

    $parsed = parse_url($url);
    $host   = $parsed['host'] ?? '';
    $port   = $parsed['port'] ?? 5432;
    $dbname = ltrim($parsed['path'] ?? '', '/');
    $user   = rawurldecode($parsed['user'] ?? '');
    $pass   = rawurldecode($parsed['pass'] ?? '');

    // Extract sslmode, ignore unsupported params like channel_binding
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
    try {
        $pdo = getDB();
        $pdo->prepare(
            'INSERT INTO settings (key, value, updated_at) VALUES (?, ?, NOW())
             ON CONFLICT (key) DO UPDATE SET value = EXCLUDED.value, updated_at = NOW()'
        )->execute([$key, $value]);
    } catch (Throwable $e) {
        error_log('setSetting error: ' . $e->getMessage());
        throw $e;
    }
}
