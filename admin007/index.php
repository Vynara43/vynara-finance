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
    try {
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
    } catch (Throwable $e) {
        $settingsError = 'Erreur lors de l\'enregistrement : ' . $e->getMessage();
    }
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

if ($action === 'delete') {
    $id   = (int)($_POST['id']   ?? 0);
    $type = sanitize($_POST['type'] ?? '');
    if ($id > 0 && in_array($type, ['application', 'message'])) {
        $pdo = getDB();
        if ($type === 'application') {
            $pdo->prepare('DELETE FROM loan_applications WHERE id=?')->execute([$id]);
        } elseif ($type === 'message') {
            $pdo->prepare('DELETE FROM contact_messages WHERE id=?')->execute([$id]);
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
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
  <title>Admin — VYNARA FINANCE</title>
  <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="admin-body" style="overflow-x:hidden;max-width:100vw">
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
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
  <title>Admin — VYNARA FINANCE</title>
  <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="admin-body" style="overflow-x:hidden;max-width:100vw">

<!-- Admin Header -->
<header class="admin-header">
  <div class="admin-logo">
    <span style="font-size:1.4rem">🏦</span>
    <span class="admin-logo-text">VYNARA <span>FINANCE</span></span>
    <span class="admin-logo-badge">— Admin</span>
  </div>
  <div class="admin-user">
    <div class="admin-avatar">VF</div>
    <span class="admin-user-label">Administrateur</span>
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
          <tr class="row-clickable" data-type="application" data-record="<?= htmlspecialchars(json_encode($app, JSON_UNESCAPED_UNICODE)) ?>">
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
            <td class="no-row-click">
              <div style="display:flex;flex-direction:column;gap:5px">
                <form method="POST" action="/admin007?tab=applications" style="display:flex;gap:5px;align-items:center">
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
                <div style="display:flex;gap:5px">
                  <button type="button" onclick="openDetail(this.closest('tr'))" style="padding:5px 10px;font-size:0.78rem;flex:1;background:rgba(201,168,76,0.15);border:1px solid rgba(201,168,76,0.3);color:var(--gold-500);border-radius:6px;cursor:pointer">👁 Voir</button>
                  <form method="POST" action="/admin007?tab=applications" onsubmit="return confirm('Supprimer cette demande ? Action irréversible.')" style="flex:1">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="type" value="application">
                    <input type="hidden" name="id" value="<?= (int)$app['id'] ?>">
                    <button type="submit" class="admin-btn admin-btn-danger" style="padding:5px 10px;font-size:0.78rem;width:100%">🗑 Suppr.</button>
                  </form>
                </div>
              </div>
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
          <tr class="row-clickable" data-type="message" data-record="<?= htmlspecialchars(json_encode($msg, JSON_UNESCAPED_UNICODE)) ?>">
            <td><?= (int)$msg['id'] ?></td>
            <td><?= htmlspecialchars($msg['name']) ?></td>
            <td><?= htmlspecialchars($msg['email']) ?></td>
            <td><?= htmlspecialchars($msg['subject'] ?? '—') ?></td>
            <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text-muted)"><?= htmlspecialchars(substr($msg['message'], 0, 60)) ?>…</td>
            <td><?= strtoupper(htmlspecialchars($msg['lang'] ?? '—')) ?></td>
            <td><span class="badge-status <?= $msg['is_read'] ? '' : 'badge-unread' ?>"><?= $msg['is_read'] ? 'Lu' : 'Non lu' ?></span></td>
            <td style="color:var(--text-muted);font-size:0.78rem"><?= formatDate($msg['created_at']) ?></td>
            <td class="no-row-click">
              <div style="display:flex;flex-direction:column;gap:5px">
                <?php if (!$msg['is_read']): ?>
                <form method="POST" action="/admin007?tab=messages">
                  <input type="hidden" name="action" value="update_status">
                  <input type="hidden" name="type" value="message">
                  <input type="hidden" name="id" value="<?= (int)$msg['id'] ?>">
                  <button type="submit" class="admin-btn admin-btn-primary" style="padding:5px 10px;font-size:0.78rem;width:100%">✓ Lu</button>
                </form>
                <?php endif; ?>
                <button type="button" onclick="openDetail(this.closest('tr'))" style="padding:5px 10px;font-size:0.78rem;background:rgba(201,168,76,0.15);border:1px solid rgba(201,168,76,0.3);color:var(--gold-500);border-radius:6px;cursor:pointer">👁 Voir</button>
                <form method="POST" action="/admin007?tab=messages" onsubmit="return confirm('Supprimer ce message ? Action irréversible.')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="type" value="message">
                  <input type="hidden" name="id" value="<?= (int)$msg['id'] ?>">
                  <button type="submit" class="admin-btn admin-btn-danger" style="padding:5px 10px;font-size:0.78rem;width:100%">🗑 Suppr.</button>
                </form>
              </div>
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
    <div class="form-alert success show" style="margin-bottom:24px">✓ Paramètres enregistrés avec succès. Les emails seront envoyés automatiquement.</div>
    <?php endif; ?>

    <?php if (!empty($settingsError)): ?>
    <div class="form-alert error show" style="margin-bottom:24px">⚠️ <?= htmlspecialchars($settingsError) ?></div>
    <?php endif; ?>

    <?php
    // Check current SMTP configuration
    $smtpConfigured = !empty(getSetting('smtp_host', '')) && !empty(getSetting('smtp_pass', ''));
    if (!$smtpConfigured):
    ?>
    <div class="form-alert error show" style="margin-bottom:24px">
      ⚠️ Configuration SMTP incomplete ! Les emails ne seront pas envoyés.
      Veuillez remplir : Serveur SMTP, Port, Email SMTP et Mot de passe SMTP.
    </div>
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

<!-- Detail Modal -->
<div id="detailModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.75);backdrop-filter:blur(4px);overflow-y:auto;padding:20px" onclick="if(event.target===this)closeModal()">
  <div style="max-width:680px;margin:40px auto;background:#0d1f38;border:1px solid rgba(201,168,76,0.25);border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.5)">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid rgba(255,255,255,0.08);background:rgba(201,168,76,0.06)">
      <div id="modalTitle" style="font-size:1.1rem;font-weight:700;color:var(--gold-500)"></div>
      <button onclick="closeModal()" style="background:rgba(255,255,255,0.08);border:none;color:#fff;width:32px;height:32px;border-radius:50%;font-size:1.2rem;cursor:pointer">&times;</button>
    </div>
    <div id="modalBody" style="padding:24px"></div>
    <div id="modalFooter" style="padding:16px 24px;border-top:1px solid rgba(255,255,255,0.08);display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap"></div>
  </div>
</div>

<style>
.row-clickable{cursor:pointer;transition:background .15s}
.row-clickable:hover{background:rgba(201,168,76,.06)!important}
.d-row{display:flex;gap:12px;margin-bottom:14px;flex-wrap:wrap}
.d-block{flex:1;min-width:180px}
.d-label{font-size:.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px}
.d-value{font-size:.9rem;color:#e0e6f0;word-break:break-word}
.d-msg{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:8px;padding:14px;line-height:1.65;color:#d0d8e8;font-size:.9rem;white-space:pre-wrap;word-break:break-word;margin-top:6px}
</style>

<script>
function openDetail(row){
  const type=row.dataset.type,r=JSON.parse(row.dataset.record);
  const modal=document.getElementById('detailModal'),
        title=document.getElementById('modalTitle'),
        body=document.getElementById('modalBody'),
        footer=document.getElementById('modalFooter');
  if(type==='application'){
    title.textContent='Demande #'+r.id+' — '+r.first_name+' '+r.last_name;
    body.innerHTML=`
      <div class="d-row">
        <div class="d-block"><div class="d-label">Prénom</div><div class="d-value">${e(r.first_name)}</div></div>
        <div class="d-block"><div class="d-label">Nom</div><div class="d-value">${e(r.last_name)}</div></div>
      </div>
      <div class="d-row">
        <div class="d-block"><div class="d-label">Email</div><div class="d-value">${e(r.email)}</div></div>
        <div class="d-block"><div class="d-label">Téléphone</div><div class="d-value">${e(r.phone||'—')}</div></div>
      </div>
      <div class="d-row">
        <div class="d-block"><div class="d-label">Montant</div><div class="d-value" style="color:var(--gold-500);font-weight:700;font-size:1.1rem">€${Number(r.amount).toLocaleString('fr-FR')}</div></div>
        <div class="d-block"><div class="d-label">Durée</div><div class="d-value">${e(r.duration)} mois</div></div>
        <div class="d-block"><div class="d-label">Objet</div><div class="d-value">${e(r.purpose||'—')}</div></div>
      </div>
      <div class="d-row">
        <div class="d-block"><div class="d-label">Pays</div><div class="d-value">${e(r.country||'—')}</div></div>
        <div class="d-block"><div class="d-label">Langue</div><div class="d-value">${e((r.lang||'—').toUpperCase())}</div></div>
        <div class="d-block"><div class="d-label">Statut</div><div class="d-value"><span class="badge-status badge-${e(r.status)}">${e(r.status)}</span></div></div>
      </div>
      <div class="d-row"><div class="d-block" style="flex:100%"><div class="d-label">Date</div><div class="d-value">${e(r.created_at)}</div></div></div>
      ${r.message?`<div><div class="d-label" style="margin-bottom:6px">Message du client</div><div class="d-msg">${e(r.message)}</div></div>`:''}
    `;
    footer.innerHTML=`
      <form method="POST" action="/admin007?tab=applications" onsubmit="return confirm('Supprimer cette demande ? Action irréversible.')">
        <input type="hidden" name="action" value="delete"><input type="hidden" name="type" value="application"><input type="hidden" name="id" value="${r.id}">
        <button type="submit" class="admin-btn admin-btn-danger" style="padding:9px 18px">🗑 Supprimer</button>
      </form>
      <button onclick="closeModal()" class="admin-btn" style="padding:9px 18px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);color:#fff">Fermer</button>`;
  } else {
    title.textContent='Message #'+r.id+' — '+r.name;
    body.innerHTML=`
      <div class="d-row">
        <div class="d-block"><div class="d-label">Nom</div><div class="d-value">${e(r.name)}</div></div>
        <div class="d-block"><div class="d-label">Email</div><div class="d-value">${e(r.email)}</div></div>
      </div>
      <div class="d-row">
        <div class="d-block"><div class="d-label">Sujet</div><div class="d-value">${e(r.subject||'—')}</div></div>
        <div class="d-block"><div class="d-label">Langue</div><div class="d-value">${e((r.lang||'—').toUpperCase())}</div></div>
        <div class="d-block"><div class="d-label">Statut</div><div class="d-value"><span class="badge-status ${r.is_read?'':'badge-unread'}">${r.is_read?'Lu':'Non lu'}</span></div></div>
      </div>
      <div class="d-row"><div class="d-block" style="flex:100%"><div class="d-label">Date</div><div class="d-value">${e(r.created_at)}</div></div></div>
      <div><div class="d-label" style="margin-bottom:6px">Message complet</div><div class="d-msg">${e(r.message)}</div></div>
    `;
    const readBtn=!r.is_read?`<form method="POST" action="/admin007?tab=messages"><input type="hidden" name="action" value="update_status"><input type="hidden" name="type" value="message"><input type="hidden" name="id" value="${r.id}"><button type="submit" class="admin-btn admin-btn-primary" style="padding:9px 18px">✓ Marquer lu</button></form>`:'';
    footer.innerHTML=`${readBtn}
      <form method="POST" action="/admin007?tab=messages" onsubmit="return confirm('Supprimer ce message ? Action irréversible.')">
        <input type="hidden" name="action" value="delete"><input type="hidden" name="type" value="message"><input type="hidden" name="id" value="${r.id}">
        <button type="submit" class="admin-btn admin-btn-danger" style="padding:9px 18px">🗑 Supprimer</button>
      </form>
      <button onclick="closeModal()" class="admin-btn" style="padding:9px 18px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);color:#fff">Fermer</button>`;
  }
  modal.style.display='block';
  document.body.style.overflow='hidden';
}
function closeModal(){
  document.getElementById('detailModal').style.display='none';
  document.body.style.overflow='';
}
function e(s){return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
document.addEventListener('keydown',ev=>{if(ev.key==='Escape')closeModal();});
document.querySelectorAll('.row-clickable').forEach(row=>{
  row.addEventListener('click',ev=>{if(!ev.target.closest('.no-row-click'))openDetail(row);});
});
</script>
</body>
</html>
