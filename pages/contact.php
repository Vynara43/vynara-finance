<?php if (!defined('SITE_NAME')) die();
$pageTitle = t('nav.contact');
$contactEmail = getSetting('contact_email', SITE_EMAIL);
$whatsappNum  = getSetting('whatsapp_number', '');
?>

<section class="page-hero">
  <div class="container">
    <div class="section-label" style="justify-content:center"><?= t('nav.contact') ?></div>
    <h1><?= t('contact.title') ?></h1>
    <p><?= t('contact.subtitle') ?></p>
  </div>
</section>

<section class="contact">
  <div class="container">
    <div class="contact-inner">

      <!-- Info -->
      <div class="contact-info" data-aos="fade-right">
        <div class="section-label"><?= t('nav.contact') ?></div>
        <h2><?= t('contact.title') ?></h2>
        <p><?= t('contact.subtitle') ?></p>

        <div class="contact-items">
          <div class="contact-item">
            <div class="contact-item-icon">📧</div>
            <div>
              <h4>Email</h4>
              <p><?= h($contactEmail) ?></p>
            </div>
          </div>
          <?php if ($whatsappNum): ?>
          <div class="contact-item">
            <div class="contact-item-icon">💬</div>
            <div>
              <h4>WhatsApp</h4>
              <p><?= h($whatsappNum) ?></p>
            </div>
          </div>
          <?php endif; ?>
          <div class="contact-item">
            <div class="contact-item-icon">🌐</div>
            <div>
              <h4>Web</h4>
              <p><?= SITE_DOMAIN ?></p>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-item-icon">🕐</div>
            <div>
              <h4>Horaires</h4>
              <p>Lun–Sam : 9h00 – 18h00</p>
            </div>
          </div>
        </div>

        <!-- Countries -->
        <div style="margin-top:36px">
          <p style="font-size:0.8rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:14px;font-weight:700">Pays desservis</p>
          <div style="display:flex;gap:10px;flex-wrap:wrap">
            <?php foreach (LANGUAGES as $code => $info): ?>
            <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);border-radius:50px;font-size:0.8rem">
              <?= $info['flag'] ?> <?= h($info['iso']) ?>
            </span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Form -->
      <div class="contact-form-wrap" data-aos="fade-left">
        <div class="form-alert" id="contactAlert"></div>
        <form id="contact-form" novalidate>
          <div class="form-group">
            <label class="form-label"><?= t('contact.name') ?> *</label>
            <input type="text" name="name" class="form-input" placeholder="Jean Dupont" required>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label"><?= t('contact.email') ?> *</label>
              <input type="email" name="email" class="form-input" placeholder="email@exemple.com" required>
            </div>
            <div class="form-group">
              <label class="form-label"><?= t('contact.subject') ?></label>
              <input type="text" name="subject" class="form-input" placeholder="...">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label"><?= t('contact.message') ?> *</label>
            <textarea name="message" class="form-textarea" rows="5" required></textarea>
          </div>
          <input type="hidden" name="lang" value="<?= h($lang) ?>">
          <button type="submit" class="btn btn-primary form-submit">
            <?= t('contact.send') ?>
          </button>
          <p class="form-note">🔒 <?= t('feature.secure.desc') ?></p>
        </form>
      </div>

    </div>
  </div>
</section>
