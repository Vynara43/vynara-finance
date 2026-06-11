<?php if (!defined('SITE_NAME')) die();
$pageTitle = t('footer.privacy'); ?>
<section class="page-hero">
  <div class="container">
    <h1><?= t('footer.privacy') ?></h1>
    <p>VYNARA FINANCE — <?= SITE_DOMAIN ?></p>
  </div>
</section>
<section style="background:var(--navy-900);padding:80px 0">
  <div class="container">
    <div style="max-width:800px;margin:0 auto;display:flex;flex-direction:column;gap:32px">
      <?php
      $sections = [
        ['Données collectées', 'Nous collectons les données que vous soumettez via nos formulaires : nom, email, téléphone, montant du prêt souhaité. Ces données sont utilisées exclusivement pour traiter votre demande.'],
        ['Utilisation des données', 'Vos données personnelles sont utilisées pour analyser votre demande de financement, vous contacter concernant votre dossier, et améliorer nos services.'],
        ['Conservation', 'Vos données sont conservées pendant la durée nécessaire au traitement de votre demande, conformément aux réglementations en vigueur dans l\'Union Européenne.'],
        ['Vos droits (RGPD)', 'Vous disposez d\'un droit d\'accès, de rectification, de suppression et de portabilité de vos données. Pour exercer ces droits, contactez-nous à : ' . SITE_EMAIL],
        ['Sécurité', 'Toutes les communications sont chiffrées (SSL/TLS). Vos données ne sont jamais vendues ni partagées avec des tiers sans votre consentement explicite.'],
        ['Cookies', 'Nous utilisons uniquement des cookies fonctionnels essentiels (session de langue, formulaires). Aucun cookie publicitaire ni de tracking tiers.'],
        ['Contact', 'Pour toute question relative à la protection de vos données : ' . SITE_EMAIL],
      ];
      foreach ($sections as [$title, $text]):
      ?>
      <div style="border-left:3px solid var(--gold-500);padding:20px 28px;background:rgba(255,255,255,0.03);border-radius:0 12px 12px 0">
        <h3 style="margin-bottom:10px;font-size:1.1rem"><?= h($title) ?></h3>
        <p style="font-size:0.95rem"><?= h($text) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
