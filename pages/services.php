<?php if (!defined('SITE_NAME')) die();
$pageTitle = t('nav.services'); ?>

<section class="page-hero">
  <div class="container">
    <div class="section-label" style="justify-content:center"><?= t('nav.services') ?></div>
    <h1><?= t('services.title') ?></h1>
    <p><?= t('services.subtitle') ?></p>
  </div>
</section>

<section class="services" style="padding-top:80px">
  <div class="container">

    <!-- Personal Loan -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;margin-bottom:80px">
      <div data-aos="fade-right">
        <div style="background:rgba(201,168,76,0.08);border:1px solid rgba(201,168,76,0.2);border-radius:20px;overflow:hidden;aspect-ratio:4/3">
          <img src="/assets/images/hero-2.png" alt="Personal Loan" style="width:100%;height:100%;object-fit:cover">
        </div>
      </div>
      <div data-aos="fade-left">
        <div class="section-label">01</div>
        <h2 style="font-size:2rem;margin-bottom:14px">💳 <?= t('services.personal.title') ?></h2>
        <p><?= t('services.personal.desc') ?></p>
        <ul style="list-style:none;margin:24px 0;display:flex;flex-direction:column;gap:10px">
          <?php $items = ['€1.000 – €150.000', t('hero.trust2'), t('hero.trust3'), t('hero.trust1')];
          foreach($items as $item): ?>
          <li style="display:flex;align-items:center;gap:10px;font-size:0.9rem;color:var(--text-light)">
            <span style="color:var(--gold-500)">✓</span> <?= h($item) ?>
          </li>
          <?php endforeach; ?>
        </ul>
        <a href="/apply?lang=<?= h($lang) ?>" class="btn btn-primary"><?= t('cta.apply') ?> →</a>
      </div>
    </div>

    <!-- Business Loan -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;margin-bottom:80px">
      <div data-aos="fade-right" style="order:2">
        <div style="background:rgba(201,168,76,0.08);border:1px solid rgba(201,168,76,0.2);border-radius:20px;overflow:hidden;aspect-ratio:4/3">
          <img src="/assets/images/hero-3.png" alt="Business Loan" style="width:100%;height:100%;object-fit:cover">
        </div>
      </div>
      <div data-aos="fade-left" style="order:1">
        <div class="section-label">02</div>
        <h2 style="font-size:2rem;margin-bottom:14px">🏢 <?= t('services.business.title') ?></h2>
        <p><?= t('services.business.desc') ?></p>
        <ul style="list-style:none;margin:24px 0;display:flex;flex-direction:column;gap:10px">
          <?php foreach(['€10.000 – €2.000.000', t('hero.trust2'), t('hero.trust3'), t('hero.trust1')] as $item): ?>
          <li style="display:flex;align-items:center;gap:10px;font-size:0.9rem;color:var(--text-light)">
            <span style="color:var(--gold-500)">✓</span> <?= h($item) ?>
          </li>
          <?php endforeach; ?>
        </ul>
        <a href="/apply?lang=<?= h($lang) ?>" class="btn btn-primary"><?= t('cta.apply') ?> →</a>
      </div>
    </div>

    <!-- Mortgage -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;margin-bottom:80px">
      <div data-aos="fade-right">
        <div style="background:rgba(201,168,76,0.08);border:1px solid rgba(201,168,76,0.2);border-radius:20px;overflow:hidden;aspect-ratio:4/3">
          <img src="/assets/images/hero-4.png" alt="Mortgage" style="width:100%;height:100%;object-fit:cover">
        </div>
      </div>
      <div data-aos="fade-left">
        <div class="section-label">03</div>
        <h2 style="font-size:2rem;margin-bottom:14px">🏡 <?= t('services.mortgage.title') ?></h2>
        <p><?= t('services.mortgage.desc') ?></p>
        <ul style="list-style:none;margin:24px 0;display:flex;flex-direction:column;gap:10px">
          <?php foreach(['Jusqu\'à 30 ans', t('hero.trust2'), t('hero.trust3'), t('hero.trust1')] as $item): ?>
          <li style="display:flex;align-items:center;gap:10px;font-size:0.9rem;color:var(--text-light)">
            <span style="color:var(--gold-500)">✓</span> <?= h($item) ?>
          </li>
          <?php endforeach; ?>
        </ul>
        <a href="/apply?lang=<?= h($lang) ?>" class="btn btn-primary"><?= t('cta.apply') ?> →</a>
      </div>
    </div>

    <!-- Refinancing -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;margin-bottom:40px">
      <div data-aos="fade-right" style="order:2">
        <div style="background:rgba(201,168,76,0.08);border:1px solid rgba(201,168,76,0.2);border-radius:20px;overflow:hidden;aspect-ratio:4/3">
          <img src="/assets/images/hero-1.png" alt="Refinancing" style="width:100%;height:100%;object-fit:cover">
        </div>
      </div>
      <div data-aos="fade-left" style="order:1">
        <div class="section-label">04</div>
        <h2 style="font-size:2rem;margin-bottom:14px">🔄 <?= t('services.refi.title') ?></h2>
        <p><?= t('services.refi.desc') ?></p>
        <a href="/apply?lang=<?= h($lang) ?>" class="btn btn-primary"><?= t('cta.apply') ?> →</a>
      </div>
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
        <a href="/contact?lang=<?= h($lang) ?>" class="btn btn-outline btn-lg"><?= t('cta.contact') ?></a>
      </div>
    </div>
  </div>
</section>

<style>
@media(max-width:700px){
  .services section [style*="grid-template-columns:1fr 1fr"]{
    display:block!important;
  }
  .services section [style*="order:2"]{order:unset!important}
}
</style>
