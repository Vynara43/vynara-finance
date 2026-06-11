<?php
// ─── VYNARA FINANCE – Footer ─────────────────────────────────────────────────
if (!defined('SITE_NAME')) die();
$currentLang = $_SESSION['lang'] ?? 'de';
$contactEmail = getSetting('contact_email', SITE_EMAIL);
?>

<!-- ═══ FOOTER ═══════════════════════════════════════════════════════════════ -->
<footer class="footer">
  <div class="container">
    <div class="footer-grid">

      <!-- Brand -->
      <div class="footer-brand">
        <a href="/?lang=<?= h($currentLang) ?>" class="navbar-logo">
          <div class="logo-icon">🏦</div>
          <div class="logo-name">
            <span>VYNARA</span>
            <span>FINANCE</span>
          </div>
        </a>
        <p><?= t('footer.tagline') ?></p>
        <div class="footer-socials">
          <a href="#" class="social-btn" aria-label="LinkedIn">in</a>
          <a href="#" class="social-btn" aria-label="Facebook">f</a>
          <a href="#" class="social-btn" aria-label="Twitter">𝕏</a>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="footer-col">
        <h4><?= t('footer.links') ?></h4>
        <div class="footer-links">
          <a href="/?lang=<?= h($currentLang) ?>"><?= t('nav.home') ?></a>
          <a href="/services?lang=<?= h($currentLang) ?>"><?= t('nav.services') ?></a>
          <a href="/process?lang=<?= h($currentLang) ?>"><?= t('nav.process') ?></a>
          <a href="/about?lang=<?= h($currentLang) ?>"><?= t('nav.about') ?></a>
          <a href="/apply?lang=<?= h($currentLang) ?>"><?= t('nav.apply') ?></a>
        </div>
      </div>

      <!-- Services -->
      <div class="footer-col">
        <h4><?= t('nav.services') ?></h4>
        <div class="footer-links">
          <a href="/services?lang=<?= h($currentLang) ?>"><?= t('services.personal.title') ?></a>
          <a href="/services?lang=<?= h($currentLang) ?>"><?= t('services.business.title') ?></a>
          <a href="/services?lang=<?= h($currentLang) ?>"><?= t('services.mortgage.title') ?></a>
          <a href="/services?lang=<?= h($currentLang) ?>"><?= t('services.refi.title') ?></a>
        </div>
      </div>

      <!-- Contact -->
      <div class="footer-col">
        <h4><?= t('footer.contact') ?></h4>
        <div class="footer-contact-info">
          <p><span>Email</span><br><?= h($contactEmail) ?></p>
          <p><span>Web</span><br><?= h(SITE_DOMAIN) ?></p>
          <p><span>Lun–Sam</span><br>9:00 – 18:00</p>
        </div>
      </div>

    </div>

    <!-- Bottom bar -->
    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> VYNARA FINANCE. <?= t('footer.legal') ?></p>
      <div class="footer-legal">
        <a href="/privacy?lang=<?= h($currentLang) ?>"><?= t('footer.privacy') ?></a>
        <a href="/terms?lang=<?= h($currentLang) ?>"><?= t('footer.terms') ?></a>
      </div>
    </div>
  </div>
</footer>

<!-- Scripts -->
<script src="/assets/js/main.js" defer></script>
</body>
</html>
