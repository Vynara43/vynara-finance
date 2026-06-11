<?php if (!defined('SITE_NAME')) die();
$pageTitle = t('footer.terms'); ?>
<section class="page-hero">
  <div class="container">
    <h1><?= t('footer.terms') ?></h1>
    <p>VYNARA FINANCE — <?= SITE_DOMAIN ?></p>
  </div>
</section>
<section style="background:var(--navy-900);padding:80px 0">
  <div class="container">
    <div style="max-width:800px;margin:0 auto;display:flex;flex-direction:column;gap:32px">
      <?php
      $sections = [
        ['Objet', 'Les présentes conditions générales d\'utilisation régissent l\'accès et l\'utilisation du site web de VYNARA FINANCE (vynara-finance.cfd).'],
        ['Caractère informatif', 'Les informations présentées sur ce site ont un caractère informatif et ne constituent pas une offre contractuelle de crédit. Tout financement est soumis à acceptation de votre dossier.'],
        ['Intermédiaire financier', 'VYNARA FINANCE agit en tant qu\'intermédiaire financier. Les décisions de crédit sont prises par les établissements partenaires conformément à leurs procédures d\'évaluation.'],
        ['Responsabilité', 'VYNARA FINANCE ne peut être tenu responsable des décisions d\'octroi ou de refus de crédit prises par ses partenaires bancaires.'],
        ['Propriété intellectuelle', 'L\'ensemble du contenu de ce site (textes, images, logos) est la propriété exclusive de VYNARA FINANCE et est protégé par le droit d\'auteur.'],
        ['Droit applicable', 'Ces conditions générales sont soumises au droit de l\'Union Européenne. En cas de litige, les tribunaux compétents de la juridiction du siège social seront saisis.'],
        ['Modification', 'VYNARA FINANCE se réserve le droit de modifier ces conditions à tout moment. La version applicable est celle publiée en ligne à la date de votre visite.'],
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
