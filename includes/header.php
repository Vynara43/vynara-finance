<?php
// ─── VYNARA FINANCE – Header ─────────────────────────────────────────────────
if (!defined('SITE_NAME')) die();

$currentLang = $_SESSION['lang'] ?? 'de';
$langInfo    = LANGUAGES[$currentLang] ?? LANGUAGES['de'];
$langFile    = $langInfo['file'];
global $translations;

$whatsappNum = getSetting('whatsapp_number', '');
$waMsg       = whatsappMessage($currentLang);
$waLink      = $whatsappNum
    ? 'https://wa.me/' . preg_replace('/\D/', '', $whatsappNum) . '?text=' . rawurlencode($waMsg)
    : '#';
$currentPage = currentPage();
?>
<!DOCTYPE html>
<html lang="<?= h($langFile) ?>" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <meta name="description" content="<?= h(t('hero.desc')) ?>">
  <meta name="robots" content="index, follow">

  <!-- Open Graph -->
  <meta property="og:type"        content="website">
  <meta property="og:title"       content="VYNARA FINANCE">
  <meta property="og:description" content="<?= h(t('hero.desc')) ?>">
  <meta property="og:image"       content="<?= SITE_URL ?>/assets/images/og-image.png">
  <meta property="og:url"         content="<?= SITE_URL ?>">
  <meta name="twitter:card"       content="summary_large_image">

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
  <link rel="shortcut icon" href="/assets/images/favicon.svg">

  <title><?= isset($pageTitle) ? h($pageTitle) . ' — ' : '' ?>VYNARA FINANCE</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Styles -->
  <link rel="stylesheet" href="/assets/css/style.css?v=20260611b">
</head>
<body>

<!-- Page Loader -->
<div class="page-loader" id="pageLoader">
  <div class="loader-logo">
    <div class="logo-icon">🏦</div>
    <div class="loader-bar"><div class="loader-bar-fill"></div></div>
  </div>
</div>

<!-- ═══ NAVBAR ═══════════════════════════════════════════════════════════════ -->
<nav class="navbar" id="navbar">
  <div class="navbar-inner">

    <!-- Logo -->
    <a href="/?lang=<?= h($currentLang) ?>" class="navbar-logo">
      <div class="logo-icon">🏦</div>
      <div class="logo-name">
        <span>VYNARA</span>
        <span>FINANCE</span>
      </div>
    </a>

    <!-- Desktop Nav -->
    <div class="navbar-nav">
      <a href="/?lang=<?= h($currentLang) ?>" class="<?= $currentPage === 'home' || $currentPage === '' ? 'active' : '' ?>"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg><?= t('nav.home') ?></a>
      <a href="/services?lang=<?= h($currentLang) ?>" class="<?= $currentPage === 'services' ? 'active' : '' ?>"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg><?= t('nav.services') ?></a>
      <a href="/process?lang=<?= h($currentLang) ?>" class="<?= $currentPage === 'process' ? 'active' : '' ?>"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg><?= t('nav.process') ?></a>
      <a href="/about?lang=<?= h($currentLang) ?>" class="<?= $currentPage === 'about' ? 'active' : '' ?>"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><?= t('nav.about') ?></a>
      <a href="/contact?lang=<?= h($currentLang) ?>" class="<?= $currentPage === 'contact' ? 'active' : '' ?>"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg><?= t('nav.contact') ?></a>
    </div>

    <!-- Right Side -->
    <div class="navbar-right">

      <!-- Language Switcher -->
      <div class="lang-switcher" id="langSwitcher">
        <button class="lang-trigger" aria-label="Changer de langue">
          <span class="flag"><?= $langInfo['flag'] ?></span>
          <span><?= h($langInfo['iso']) ?></span>
          <span class="arrow">▼</span>
        </button>
        <div class="lang-dropdown">
          <?php foreach (LANGUAGES as $code => $info): ?>
          <div class="lang-option <?= $code === $currentLang ? 'active' : '' ?>" data-lang="<?= h($code) ?>">
            <span class="flag"><?= $info['flag'] ?></span>
            <span><?= h($info['name']) ?></span>
            <span class="iso"><?= h($info['iso']) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Mobile toggle -->
      <button class="menu-toggle" id="menuToggle" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>

  </div>
</nav>

<!-- Mobile Nav -->
<div class="mobile-nav" id="mobileNav">
  <a href="/?lang=<?= h($currentLang) ?>"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg><?= t('nav.home') ?></a>
  <a href="/services?lang=<?= h($currentLang) ?>"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg><?= t('nav.services') ?></a>
  <a href="/process?lang=<?= h($currentLang) ?>"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg><?= t('nav.process') ?></a>
  <a href="/about?lang=<?= h($currentLang) ?>"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg><?= t('nav.about') ?></a>
  <a href="/contact?lang=<?= h($currentLang) ?>"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg><?= t('nav.contact') ?></a>
  <a href="/apply?lang=<?= h($currentLang) ?>" class="mobile-nav-apply"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg><?= t('nav.apply') ?></a>
</div>

<!-- WhatsApp Button -->
<?php if ($whatsappNum): ?>
<a href="<?= h($waLink) ?>" target="_blank" rel="noopener" class="whatsapp-btn" aria-label="WhatsApp">
  <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
  </svg>
  <div class="whatsapp-tooltip">WhatsApp</div>
</a>
<?php endif; ?>
