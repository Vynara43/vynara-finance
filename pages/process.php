<?php if (!defined('SITE_NAME')) die();
$pageTitle = t('nav.process'); ?>

<section class="page-hero">
  <div class="container">
    <div class="section-label" style="justify-content:center"><?= t('nav.process') ?></div>
    <h1><?= t('process.title') ?></h1>
    <p><?= t('process.subtitle') ?></p>
  </div>
</section>

<!-- Process Steps Detail -->
<section class="process" style="padding-top:80px">
  <div class="container">
    <div style="display:flex;flex-direction:column;gap:60px">

      <?php
      $steps = [
        ['num'=>'01','icon'=>'📋','color'=>'rgba(201,168,76,0.15)'],
        ['num'=>'02','icon'=>'🔍','color'=>'rgba(13,202,240,0.1)'],
        ['num'=>'03','icon'=>'📄','color'=>'rgba(46,213,115,0.1)'],
        ['num'=>'04','icon'=>'💰','color'=>'rgba(201,168,76,0.15)'],
      ];
      for ($i = 1; $i <= 4; $i++): $s = $steps[$i-1];
      $isEven = $i % 2 === 0;
      ?>
      <div data-aos="fade-up" style="display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center">

        <div style="<?= $isEven ? 'order:2' : '' ?>">
          <div style="background:<?= $s['color'] ?>;border:1px solid rgba(201,168,76,0.15);border-radius:24px;padding:50px;text-align:center;font-size:5rem">
            <?= $s['icon'] ?>
          </div>
        </div>

        <div style="<?= $isEven ? 'order:1' : '' ?>">
          <div class="section-label"><?= $s['num'] ?></div>
          <h2 style="font-size:2.2rem;margin-bottom:16px"><?= t('process.step'.$i.'.title') ?></h2>
          <p style="font-size:1.05rem"><?= t('process.step'.$i.'.desc') ?></p>

          <?php if ($i === 1): ?>
          <div style="margin-top:28px;padding:20px 24px;background:rgba(255,255,255,0.04);border-radius:12px;border:1px solid rgba(255,255,255,0.07)">
            <p style="font-size:0.85rem;color:var(--text-muted)">
              ⏱ <strong style="color:var(--white)">5 min</strong> — <?= t('hero.trust1') ?>
            </p>
          </div>
          <?php elseif ($i === 2): ?>
          <div style="margin-top:28px;padding:20px 24px;background:rgba(255,255,255,0.04);border-radius:12px;border:1px solid rgba(255,255,255,0.07)">
            <p style="font-size:0.85rem;color:var(--text-muted)">
              ⚡ <strong style="color:var(--white)">24h</strong> — Analyse rapide
            </p>
          </div>
          <?php elseif ($i === 4): ?>
          <div style="margin-top:28px;padding:20px 24px;background:rgba(255,255,255,0.04);border-radius:12px;border:1px solid rgba(255,255,255,0.07)">
            <p style="font-size:0.85rem;color:var(--text-muted)">
              🏦 <strong style="color:var(--white)">48h</strong> — <?= t('hero.trust3') ?>
            </p>
          </div>
          <?php endif; ?>
        </div>

      </div>
      <?php endfor; ?>

    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <div data-aos="fade-up">
      <h2><?= t('cta.section.title') ?></h2>
      <p><?= t('cta.section.desc') ?></p>
      <div class="cta-actions">
        <a href="/apply?lang=<?= h($lang) ?>" class="btn btn-primary btn-lg"><?= t('cta.apply') ?> →</a>
      </div>
    </div>
  </div>
</section>

<style>
@media(max-width:700px){
  .process [style*="grid-template-columns:1fr 1fr"]{display:block!important}
  .process [style*="order:2"]{order:unset!important}
}
</style>
