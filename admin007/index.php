<?php
// ─── VYNARA FINANCE – Admin Panel ────────────────────────────────────────────
session_name('vf_sess');
session_start();

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$adminPass = '19990000';

// ─ Actions ───────────────────────────────────────────────────────────────────
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Login
if ($action === 'login') {
    $pass = $_POST['password'] ?? '';
    if ($pass === $adminPass) {
        $_SESSION['vf_admin'] = true;
        header('Location: /admin007');
    } else {
        $loginError = 'Mot de passe incorrect.';
    }
    if (!isset($loginError)) exit;
}

// Logout
if ($action === 'logout') {
    unset($_SESSION['vf_admin']);
    header('Location: /admin007');
    exit;
}

// Not logged in → show login
if (!isAdminLoggedIn()) {
    $loginError = $loginError ?? '';
    adminLoginPage($loginError);
    exit;
}

// Admin is logged in → handle actions
if ($action === 'save_settings') {
    $whatsapp     = sanitize($_POST['whatsapp_number']   ?? '');
    $contactEmail = sanitize($_POST['contact_email']     ?? '');
    $smtpHost     = sanitize($_POST['smtp_host']         ?? '');
    $smtpPort     = sanitize($_POST['smtp_port']         ?? '587');
    $smtpUser     = sanitize($_POST['smtp_user']         ?? '');
    $smtpPass     = trim($_POST['smtp_pass']             ?? '');

    setSetting('whatsapp_number', $whatsapp);
    setSetting('contact_email',   $contactEmail);
    setSetting('smtp_host',       $smtpHost);
    setSetting('smtp_port',       $smtpPort);
    setSetting('smtp_user',       $smtpUser);
    if (!empty($smtpPass)) setSetting('smtp_pass', $smtpPass);
    $settingsSaved = true;
}

if ($action === 'update_status') {
    $id     = (int)($_POST['id']     ?? 0);
    $type   = sanitize($_POST['type'] ?? '');
    $status = sanitize($_POST['status'] ?? '');
    if ($id > 0 && in_array($type, ['application', 'message'])) {
        $pdo = getDB();
        if ($type === 'application' && in_array($status, ['pending','reviewing','approved','rejected'])) {
            $pdo->prepare('UPDATE loan_applications SET status=? WHERE id=?')->execute([$status, $id]);
        } elseif ($type === 'message') {
            $pdo->prepare('UPDATE contact_messages SET is_read=TRUE WHERE id=?')->execute([$id]);
        }
    }
    header('Location: /admin007?' . ($type === 'application' ? 'tab=applications' : 'tab=messages'));
    exit;
}

// ─ Load data ─────────────────────────────────────────────────────────────────
$tab = $_GET['tab'] ?? 'dashboard';

try {
    $pdo = getDB();
    $statsApps = $pdo->query('SELECT COUNT(*) c FROM loan_applications')->fetchColumn();
    $statsMsgs = $pdo->query('SELECT COUNT(*) c FROM contact_messages WHERE is_read=FALSE')->fetchColumn();
    $statsPend = $pdo->query("SELECT COUNT(*) c FROM loan_applications WHERE status='pending'")->fetchColumn();
    $statsAppr = $pdo->query("SELECT COUNT(*) c FROM loan_applications WHERE status='approved'")->fetchColumn();

    if ($tab === 'applications' || $tab === 'dashboard') {
        $applications = $pdo->query('SELECT * FROM loan_applications ORDER BY created_at DESC LIMIT 100')->fetchAll();
    }
    if ($tab === 'messages' || $tab === 'dashboard') {
        $messages = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 100')->fetchAll();
    }
} catch (Throwable $e) {
    $dbError = $e->getMessage();
}

$settings = [
    'whatsapp_number' => getSetting('whatsapp_number', ''),
    'contact_email'   => getSetting('contact_email', SITE_EMAIL),
    'smtp_host'       => getSetting('smtp_host', ''),
    'smtp_port'       => getSetting('smtp_port', '587'),
    'smtp_user'       => getSetting('smtp_user', SITE_EMAIL),
];

// ─ Render ─────────────────────────────────────────────────────────────────────
function adminLoginPage(string $error = ''): void {
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin — VYNARA FINANCE</title>
  <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="admin-body">
<div class="admin-login">
  <div class="login-box">
    <div class="logo-wrap">
      <div class="logo-icon">🏦</div>
      <h1>VYNARA FINANCE</h1>
      <p class="sub">Interface Administrateur</p>
    </div>
    <?php if ($error): ?>
    <div class="form-alert error show" style="margin-bottom:20px"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="/admin007">
      <input type="hidden" name="action" value="login">
      <div class="form-group">
        <label class="form-label">Mot de passe</label>
        <input type="password" name="password" class="form-input" placeholder="••••••••" autofocus required>
      </div>
      <button type="submit" class="btn btn-primary form-submit">Connexion →</button>
    </form>
  </div>
</div>
</body>
</html>
<?php
}

// ── Dashboard page ────────────────────────────────────────────────────────────
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin — VYNARA FINANCE</title>
  <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="admin-body">

<!-- Admin Header -->
<header class="admin-header">
  <div class="admin-logo">
    <span style="font-size:1.4rem">🏦</span>
    VYNARA <span>FINANCE</span>
    <span style="font-size:0.7rem;color:#8a9bb5;font-weight:400;margin-left:6px">— Admin</span>
  </div>
  <div class="admin-user">
    <div class="admin-avatar">VF</div>
    <span style="font-size:0.85rem;color:#8a9bb5">Administrateur</span>
    <a href="/admin007?action=logout" class="admin-btn admin-btn-danger" style="padding:7px 14px;font-size:0.8rem">Déconnexion</a>
  </div>
</header>

<div class="admin-layout">

  <!-- Sidebar -->
  <aside class="admin-sidebar">
    <a href="/admin007?tab=dashboard"     class="admin-nav-item <?= $tab === 'dashboard' ? 'active' : '' ?>">
      <span class="admin-nav-icon">📊</span> Tableau de bord
    </a>
    <a href="/admin007?tab=applications"  class="admin-nav-item <?= $tab === 'applications' ? 'active' : '' ?>">
      <span class="admin-nav-icon">📋</span> Demandes
      <?php if ($statsPend > 0): ?>
      <span style="margin-left:auto;background:rgba(201,168,76,0.2);color:var(--gold-500);font-size:0.72rem;font-weight:700;padding:2px 8px;border-radius:10px"><?= $statsPend ?></span>
      <?php endif; ?>
    </a>
    <a href="/admin007?tab=messages"      class="admin-nav-item <?= $tab === 'messages' ? 'active' : '' ?>">
      <span class="admin-nav-icon">💬</span> Messages
      <?php if ($statsMsgs > 0): ?>
      <span style="margin-left:auto;background:rgba(201,168,76,0.2);color:var(--gold-500);font-size:0.72rem;font-weight:700;padding:2px 8px;border-radius:10px"><?= $statsMsgs ?></span>
      <?php endif; ?>
    </a>
    <a href="/admin007?tab=settings"      class="admin-nav-item <?= $tab === 'settings' ? 'active' : '' ?>">
      <span class="admin-nav-icon">⚙️</span> Paramètres
    </a>
    <a href="/?lang=de" target="_blank"   class="admin-nav-item" style="margin-top:auto">
      <span class="admin-nav-icon">🌐</span> Voir le site
    </a>
  </aside>

  <!-- Main Content -->
  <main class="admin-content">

    <?php if (!empty($dbError)): ?>
    <div class="form-alert error show" style="margin-bottom:24px">
      ⚠️ Base de données : <?= htmlspecialchars($dbError) ?>
      — <a href="/setup.php" style="color:var(--gold-500)">Initialiser la base →</a>
    </div>
    <?php endif; ?>

    <?php if ($tab === 'dashboard'): ?>
    <!-- ── DASHBOARD ──────────────────────────────────────────────────────── -->
    <div class="admin-page-title">Tableau de bord</div>
    <div class="admin-page-sub">Vue d'ensemble de VYNARA FINANCE</div>

    <div class="admin-cards">
      <div class="admin-card">
        <div class="card-label">Demandes totales</div>
        <div class="card-value"><?= (int)($statsApps ?? 0) ?></div>
      </div>
      <div class="admin-card">
        <div class="card-label">En attente</div>
        <div class="card-value" style="color:#ffc107"><?= (int)($statsPend ?? 0) ?></div>
      </div>
      <div class="admin-card">
        <div class="card-label">Approuvées</div>
        <div class="card-value" style="color:#2ed573"><?= (int)($statsAppr ?? 0) ?></div>
      </div>
      <div class="admin-card">
        <div class="card-label">Messages non lus</div>
        <div class="card-value" style="color:var(--gold-500)"><?= (int)($statsMsgs ?? 0) ?></div>
      </div>
    </div>

    <!-- Recent Applications -->
    <div class="admin-section-title">📋 Demandes récentes</div>
    <div class="admin-table-wrap" style="margin-bottom:32px">
      <table class="admin-table">
        <thead>
          <tr>
            <th>#</th><th>Nom</th><th>Email</th><th>Montant</th><th>Pays</th><th>Statut</th><th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($applications)): foreach (array_slice($applications, 0, 10) as $app): ?>
          <tr>
            <td><?= (int)$app['id'] ?></td>
            <td><?= htmlspecialchars($app['first_name'] . ' ' . $app['last_name']) ?></td>
            <td><?= htmlspecialchars($app['email']) ?></td>
            <td style="color:var(--gold-500);font-weight:700">€<?= number_format((float)$app['amount'], 0, ',', ' ') ?></td>
            <td><?= htmlspecialchars($app['country'] ?? '—') ?></td>
            <td><span class="badge-status badge-<?= $app['status'] ?>"><?= ucfirst($app['status']) ?></span></td>
            <td style="color:var(--text-muted);font-size:0.82rem"><?= formatDate($app['created_at']) ?></td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:32px">Aucune demande pour le moment.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Recent Messages -->
    <div class="admin-section-title">💬 Messages récents</div>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr><th>#</th><th>Nom</th><th>Email</th><th>Sujet</th><th>Statut</th><th>Date</th></tr>
        </thead>
        <tbody>
          <?php if (!empty($messages)): foreach (array_slice($messages, 0, 10) as $msg): ?>
          <tr>
            <td><?= (int)$msg['id'] ?></td>
            <td><?= htmlspecialchars($msg['name']) ?></td>
            <td><?= htmlspecialchars($msg['email']) ?></td>
            <td><?= htmlspecialchars($msg['subject'] ?? '—') ?></td>
            <td><span class="badge-status <?= $msg['is_read'] ? '' : 'badge-unread' ?>"><?= $msg['is_read'] ? 'Lu' : 'Non lu' ?></span></td>
            <td style="color:var(--text-muted);font-size:0.82rem"><?= formatDate($msg['created_at']) ?></td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:32px">Aucun message pour le moment.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif ($tab === 'applications'): ?>
    <!-- ── APPLICATIONS ────────────────────────────────────────────────────── -->
    <div class="admin-page-title">Demandes de prêts</div>
    <div class="admin-page-sub">Gérez toutes les demandes reçues</div>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr><th>#</th><th>Nom</th><th>Email</th><th>Téléphone</th><th>Montant</th><th>Durée</th><th>Objet</th><th>Pays</th><th>Statut</th><th>Date</th><th>Action</th></tr>
        </thead>
        <tbody>
          <?php if (!empty($applications)): foreach ($applications as $app): ?>
          <tr>
            <td><?= (int)$app['id'] ?></td>
            <td><?= htmlspecialchars($app['first_name'] . ' ' . $app['last_name']) ?></td>
            <td><?= htmlspecialchars($app['email']) ?></td>
            <td><?= htmlspecialchars($app['phone'] ?? '—') ?></td>
            <td style="color:var(--gold-500);font-weight:700">€<?= number_format((float)$app['amount'], 0, ',', ' ') ?></td>
            <td><?= (int)$app['duration'] ?>m</td>
            <td><?= htmlspecialchars($app['purpose'] ?? '—') ?></td>
            <td><?= htmlspecialchars($app['country'] ?? '—') ?></td>
            <td><span class="badge-status badge-<?= $app['status'] ?>"><?= ucfirst($app['status']) ?></span></td>
            <td style="color:var(--text-muted);font-size:0.78rem"><?= formatDate($app['created_at']) ?></td>
            <td>
              <form method="POST" action="/admin007?tab=applications" style="display:flex;gap:6px;align-items:center">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="type" value="application">
                <input type="hidden" name="id" value="<?= (int)$app['id'] ?>">
                <select name="status" class="form-select" style="padding:5px 8px;font-size:0.78rem;background:rgba(255,255,255,0.06);border-radius:6px;min-width:110px">
                  <?php foreach (['pending','reviewing','approved','rejected'] as $st): ?>
                  <option value="<?= $st ?>" <?= $app['status'] === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" class="admin-btn admin-btn-primary" style="padding:5px 10px;font-size:0.78rem">✓</button>
              </form>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="11" style="text-align:center;color:var(--text-muted);padding:40px">Aucune demande.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif ($tab === 'messages'): ?>
    <!-- ── MESSAGES ────────────────────────────────────────────────────────── -->
    <div class="admin-page-title">Messages de contact</div>
    <div class="admin-page-sub">Tous les messages reçus via le formulaire de contact</div>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr><th>#</th><th>Nom</th><th>Email</th><th>Sujet</th><th>Message</th><th>Langue</th><th>Statut</th><th>Date</th><th>Action</th></tr>
        </thead>
        <tbody>
          <?php if (!empty($messages)): foreach ($messages as $msg): ?>
          <tr>
            <td><?= (int)$msg['id'] ?></td>
            <td><?= htmlspecialchars($msg['name']) ?></td>
            <td><?= htmlspecialchars($msg['email']) ?></td>
            <td><?= htmlspecialchars($msg['subject'] ?? '—') ?></td>
            <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= htmlspecialchars($msg['message']) ?>"><?= htmlspecialchars(substr($msg['message'], 0, 60)) ?>...</td>
            <td><?= strtoupper(htmlspecialchars($msg['lang'] ?? '—')) ?></td>
            <td><span class="badge-status <?= $msg['is_read'] ? '' : 'badge-unread' ?>"><?= $msg['is_read'] ? 'Lu' : 'Non lu' ?></span></td>
            <td style="color:var(--text-muted);font-size:0.78rem"><?= formatDate($msg['created_at']) ?></td>
            <td>
              <?php if (!$msg['is_read']): ?>
              <form method="POST" action="/admin007?tab=messages">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="type" value="message">
                <input type="hidden" name="id" value="<?= (int)$msg['id'] ?>">
                <button type="submit" class="admin-btn admin-btn-primary" style="padding:5px 12px;font-size:0.78rem">Marquer lu</button>
              </form>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="9" style="text-align:center;color:var(--text-muted);padding:40px">Aucun message.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif ($tab === 'settings'): ?>
    <!-- ── SETTINGS ────────────────────────────────────────────────────────── -->
    <div class="admin-page-title">Paramètres du site</div>
    <div class="admin-page-sub">Configurez les paramètres de VYNARA FINANCE</div>

    <?php if (!empty($settingsSaved)): ?>
    <div class="form-alert success show" style="margin-bottom:24px">✓ Paramètres enregistrés avec succès.</div>
    <?php endif; ?>

    <form method="POST" action="/admin007?tab=settings">
      <input type="hidden" name="action" value="save_settings">

      <!-- Contact Settings -->
      <div class="admin-settings-form" style="margin-bottom:28px">
        <h3>📞 Coordonnées</h3>
        <div class="form-group">
          <label class="form-label">Numéro WhatsApp (avec indicatif pays, ex: +33612345678)</label>
          <input type="text" name="whatsapp_number" class="form-input" value="<?= htmlspecialchars($settings['whatsapp_number']) ?>" placeholder="+33612345678">
          <small style="color:var(--text-muted);font-size:0.8rem;margin-top:6px;display:block">Ce numéro sera affiché sur le bouton WhatsApp du site.</small>
        </div>
        <div class="form-group">
          <label class="form-label">Email de contact affiché sur le site</label>
          <input type="email" name="contact_email" class="form-input" value="<?= htmlspecialchars($settings['contact_email']) ?>">
        </div>
      </div>

      <!-- SMTP Settings -->
      <div class="admin-settings-form" style="margin-bottom:28px">
        <h3>📧 Configuration SMTP (envoi d'emails)</h3>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Serveur SMTP</label>
            <input type="text" name="smtp_host" class="form-input" value="<?= htmlspecialchars($settings['smtp_host']) ?>" placeholder="mail.vynara-finance.cfd">
          </div>
          <div class="form-group">
            <label class="form-label">Port SMTP</label>
            <input type="number" name="smtp_port" class="form-input" value="<?= htmlspecialchars($settings['smtp_port']) ?>" placeholder="587">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Email SMTP (expéditeur)</label>
          <input type="email" name="smtp_user" class="form-input" value="<?= htmlspecialchars($settings['smtp_user']) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Mot de passe SMTP (laisser vide pour ne pas changer)</label>
          <input type="password" name="smtp_pass" class="form-input" placeholder="••••••••">
        </div>
      </div>

      <button type="submit" class="admin-btn admin-btn-primary" style="font-size:0.95rem;padding:12px 28px">
        💾 Enregistrer les paramètres
      </button>
    </form>

    <?php endif; ?>

  </main>
</div>

<script src="/assets/js/main.js" defer></script>
</body>
</html>
