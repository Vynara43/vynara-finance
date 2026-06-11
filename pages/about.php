<?php if (!defined('SITE_NAME')) die();
$pageTitle = t('nav.about'); ?>

<section class="page-hero">
  <div class="container">
    <div class="section-label" style="justify-content:center"><?= t('nav.about') ?></div>
    <h1><?= t('about.title') ?></h1>
    <p><?= t('about.subtitle') ?></p>
  </div>
</section>

<!-- About Main -->
<section class="about">
  <div class="container">
    <div class="about-inner">
      <div class="about-image" data-aos="fade-right">
        <img src="/assets/images/hero-1.png" alt="VYNARA FINANCE">
        <div class="about-image-badge">
          <div class="badge-value">2013</div>
          <div class="badge-label">Fondée</div>
        </div>
      </div>
      <div class="about-text" data-aos="fade-left">
        <div class="section-label"><?= t('nav.about') ?></div>
        <h2 class="section-title"><?= t('about.title') ?></h2>
        <p><?= t('about.text1') ?></p>
        <p><?= t('about.text2') ?></p>
        <div class="about-mission">
          <h4><?= t('about.mission') ?></h4>
          <p><?= t('about.mission.desc') ?></p>
        </div>
        <div class="about-stats">
          <div class="about-stat">
            <div class="value" data-count="5200" data-suffix="+">5200+</div>
            <div class="label"><?= t('stats.clients') ?></div>
          </div>
          <div class="about-stat">
            <div class="value" data-count="150" data-suffix="+">150+</div>
            <div class="label"><?= $lang === 'de' ? 'Partnerbanken' : 'Partner Banks' ?></div>
          </div>
          <div class="about-stat">
            <div class="value" data-count="420" data-prefix="€" data-suffix="M">€420M</div>
            <div class="label"><?= t('stats.financed') ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Values -->
<section class="features" style="padding-top:0">
  <div class="container">
    <div style="text-align:center;margin-bottom:50px" data-aos="fade-up">
      <div class="section-label" style="justify-content:center">Valeurs</div>
      <h2 class="section-title"><?= $lang === 'de' ? 'Unsere Werte' : ($lang === 'it' ? 'I nostri valori' : ($lang === 'pt' ? 'Os nossos valores' : 'Our Values')) ?></h2>
    </div>
    <div class="features-grid">
      <div class="feature-card" data-aos="fade-up">
        <div class="feature-icon">🔍</div>
        <h3><?= $lang === 'de' ? 'Transparenz' : ($lang === 'it' ? 'Trasparenza' : ($lang === 'pt' ? 'Transparência' : 'Transparency')) ?></h3>
        <p><?= $lang === 'de' ? 'Klare und transparente Bedingungen ohne versteckte Kosten.' : ($lang === 'it' ? 'Condizioni chiare e trasparenti senza costi nascosti.' : 'Clear and transparent conditions with no hidden fees.') ?></p>
      </div>
      <div class="feature-card" data-aos="fade-up" style="transition-delay:0.15s">
        <div class="feature-icon">⚡</div>
        <h3><?= $lang === 'de' ? 'Effizienz' : ($lang === 'it' ? 'Efficienza' : ($lang === 'pt' ? 'Eficiência' : 'Efficiency')) ?></h3>
        <p><?= $lang === 'de' ? 'Schnelle Bearbeitung und transparente Kommunikation in jeder Phase.' : ($lang === 'it' ? 'Elaborazione rapida e comunicazione trasparente in ogni fase.' : 'Fast processing and transparent communication at every stage.') ?></p>
      </div>
      <div class="feature-card" data-aos="fade-up" style="transition-delay:0.3s">
        <div class="feature-icon">💡</div>
        <h3><?= $lang === 'de' ? 'Innovation' : ($lang === 'it' ? 'Innovazione' : ($lang === 'pt' ? 'Inovação' : 'Innovation')) ?></h3>
        <p><?= $lang === 'de' ? 'Modernste Technologien für schnelle und sichere Kreditlösungen.' : ($lang === 'it' ? 'Tecnologie all\'avanguardia per soluzioni di credito veloci e sicure.' : 'Cutting-edge technologies for fast and secure lending solutions.') ?></p>
      </div>
    </div>
  </div>
</section>

<!-- Countries -->
<section style="background:var(--navy-900);padding:80px 0">
  <div class="container">
    <div style="text-align:center;margin-bottom:50px" data-aos="fade-up">
      <div class="section-label" style="justify-content:center">Europe</div>
      <h2 class="section-title"><?= $lang === 'de' ? 'Unsere Märkte' : ($lang === 'it' ? 'I nostri mercati' : 'Our Markets') ?></h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:20px" data-aos="fade-up">
      <?php foreach (LANGUAGES as $code => $info): ?>
      <div style="text-align:center;padding:24px 16px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:14px;transition:var(--transition)"
           onmouseover="this.style.borderColor='rgba(201,168,76,0.3)'"
           onmouseout="this.style.borderColor='rgba(255,255,255,0.08)'">
        <div style="font-size:2.2rem;margin-bottom:10px"><?= $info['flag'] ?></div>
        <div style="font-size:0.85rem;font-weight:600;color:var(--text-light)"><?= h($info['name']) ?></div>
      </div>
      <?php endforeach; ?>
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
