<?php if (!defined('SITE_NAME')) die(); ?>

<!-- ═══ HERO ═══════════════════════════════════════════════════════════════════ -->
<section class="hero" id="home">
  <div class="container">
    <div class="hero-inner">

      <!-- Left: Text -->
      <div class="hero-content" data-aos="fade-right">
        <div class="hero-badge"><?= t('hero.badge') ?></div>
        <h1 class="hero-title">
          <span class="line1"><?= t('hero.title1') ?></span>
          <span class="line2"><?= t('hero.title2') ?></span>
        </h1>
        <p class="hero-desc"><?= t('hero.desc') ?></p>
        <div class="hero-actions">
          <a href="/simulate?lang=<?= h($lang) ?>" class="btn btn-primary btn-lg">
            <?= t('hero.cta1') ?> <span class="btn-icon">→</span>
          </a>
          <a href="/apply?lang=<?= h($lang) ?>" class="btn btn-outline btn-lg">
            <?= t('nav.apply') ?>
          </a>
        </div>
        <div class="hero-trust">
          <div class="trust-item"><div class="check">✓</div><?= t('hero.trust1') ?></div>
          <div class="trust-item"><div class="check">✓</div><?= t('hero.trust2') ?></div>
          <div class="trust-item"><div class="check">✓</div><?= t('hero.trust3') ?></div>
        </div>
      </div>

      <!-- Right: Visual -->
      <div class="hero-visual" data-aos="fade-left" style="--delay:0.2s">

        <!-- Image Carousel -->
        <div class="hero-carousel" id="heroCarousel">
          <div class="carousel-slide active">
            <img src="/assets/images/hero-1.png" alt="VYNARA FINANCE Building" loading="eager">
            <div class="carousel-overlay"></div>
            <div class="carousel-info">
              <div class="carousel-tag">Finance</div>
              <div class="carousel-caption">VYNARA FINANCE — <?= t('hero.badge') ?></div>
            </div>
          </div>
          <div class="carousel-slide">
            <img src="/assets/images/hero-2.png" alt="Premium Banking Interior" loading="lazy">
            <div class="carousel-overlay"></div>
          </div>
          <div class="carousel-slide">
            <img src="/assets/images/hero-4.png" alt="European Financial District" loading="lazy">
            <div class="carousel-overlay"></div>
          </div>

          <!-- Dots -->
          <div class="carousel-dots">
            <button class="carousel-dot active" aria-label="Slide 1"></button>
            <button class="carousel-dot" aria-label="Slide 2"></button>
            <button class="carousel-dot" aria-label="Slide 3"></button>
          </div>
        </div>

        <!-- Stats Card -->
        <div class="stats-card" data-aos="zoom-in" style="--delay:0.5s">
          <div class="stat-item">
            <div class="stat-value"
              data-count="5200" data-suffix="+"
            >5200+</div>
            <div class="stat-label"><?= t('stats.clients') ?></div>
          </div>
          <div class="stat-item">
            <div class="stat-value"
              data-count="420" data-prefix="€" data-suffix="M"
            >€420M</div>
            <div class="stat-label"><?= t('stats.financed') ?></div>
          </div>
          <div class="stat-item">
            <div class="stat-value"
              data-count="48" data-suffix="h"
            >48h</div>
            <div class="stat-label"><?= t('stats.response') ?></div>
          </div>
          <div class="stat-item">
            <div class="stat-value"
              data-count="98" data-suffix="%"
            >98%</div>
            <div class="stat-label"><?= t('stats.satisfaction') ?></div>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- ═══ TRUST SECTION ════════════════════════════════════════════════════════ -->
<section class="trust-section">
  <div class="container">
    <div class="trust-header" data-aos="fade-up">
      <h2><?= t('trust.title') ?></h2>
      <p><?= t('trust.subtitle') ?></p>
    </div>
    <div class="trust-grid">
      <div class="trust-card" data-aos="fade-up">
        <div class="trust-icon">🏦</div>
        <h3><?= t('trust.regulated.title') ?></h3>
        <p><?= t('trust.regulated.desc') ?></p>
      </div>
      <div class="trust-card" data-aos="fade-up" style="transition-delay:0.1s">
        <div class="trust-icon">🔒</div>
        <h3><?= t('trust.secure.title') ?></h3>
        <p><?= t('trust.secure.desc') ?></p>
      </div>
      <div class="trust-card" data-aos="fade-up" style="transition-delay:0.2s">
        <div class="trust-icon">⭐</div>
        <h3><?= t('trust.rated.title') ?></h3>
        <p><?= t('trust.rated.desc') ?></p>
      </div>
    </div>
  </div>
</section>

<!-- ═══ FEATURES ═════════════════════════════════════════════════════════════ -->
<section class="features">
  <div class="container">
    <div class="features-grid">
      <div class="feature-card" data-aos="fade-up">
        <div class="feature-icon">🤝</div>
        <h3><?= t('feature.trust.title') ?></h3>
        <p><?= t('feature.trust.desc') ?></p>
      </div>
      <div class="feature-card" data-aos="fade-up" style="transition-delay:0.15s">
        <div class="feature-icon">⚡</div>
        <h3><?= t('feature.fast.title') ?></h3>
        <p><?= t('feature.fast.desc') ?></p>
      </div>
      <div class="feature-card" data-aos="fade-up" style="transition-delay:0.3s">
        <div class="feature-icon">🛡️</div>
        <h3><?= t('feature.secure.title') ?></h3>
        <p><?= t('feature.secure.desc') ?></p>
      </div>
    </div>
  </div>
</section>

<!-- ═══ SERVICES PREVIEW ═════════════════════════════════════════════════════ -->
<section class="services">
  <div class="container">
    <div class="services-header" data-aos="fade-up">
      <div class="section-label"><?= t('nav.services') ?></div>
      <h2 class="section-title"><?= t('services.title') ?></h2>
      <p class="section-subtitle"><?= t('services.subtitle') ?></p>
    </div>
    <div class="services-grid">
      <div class="service-card" data-aos="fade-up">
        <div class="service-icon-wrap">💳</div>
        <div class="service-content">
          <h3><?= t('services.personal.title') ?></h3>
          <p><?= t('services.personal.desc') ?></p>
          <a href="/services?lang=<?= h($lang) ?>" class="service-link"><?= t('cta.learn') ?> →</a>
        </div>
      </div>
      <div class="service-card" data-aos="fade-up" style="transition-delay:0.1s">
        <div class="service-icon-wrap">🏢</div>
        <div class="service-content">
          <h3><?= t('services.business.title') ?></h3>
          <p><?= t('services.business.desc') ?></p>
          <a href="/services?lang=<?= h($lang) ?>" class="service-link"><?= t('cta.learn') ?> →</a>
        </div>
      </div>
      <div class="service-card" data-aos="fade-up" style="transition-delay:0.2s">
        <div class="service-icon-wrap">🏡</div>
        <div class="service-content">
          <h3><?= t('services.mortgage.title') ?></h3>
          <p><?= t('services.mortgage.desc') ?></p>
          <a href="/services?lang=<?= h($lang) ?>" class="service-link"><?= t('cta.learn') ?> →</a>
        </div>
      </div>
      <div class="service-card" data-aos="fade-up" style="transition-delay:0.3s">
        <div class="service-icon-wrap">🔄</div>
        <div class="service-content">
          <h3><?= t('services.refi.title') ?></h3>
          <p><?= t('services.refi.desc') ?></p>
          <a href="/services?lang=<?= h($lang) ?>" class="service-link"><?= t('cta.learn') ?> →</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ PROCESS PREVIEW ══════════════════════════════════════════════════════ -->
<section class="process">
  <div class="container">
    <div class="process-header" data-aos="fade-up">
      <div class="section-label"><?= t('nav.process') ?></div>
      <h2 class="section-title"><?= t('process.title') ?></h2>
      <p class="process-subtitle"><?= t('process.subtitle') ?></p>
    </div>
    <div class="process-grid">
      <?php
      $steps = [
        ['icon'=>'📋','num'=>'01'],
        ['icon'=>'🔍','num'=>'02'],
        ['icon'=>'📄','num'=>'03'],
        ['icon'=>'💰','num'=>'04'],
      ];
      for ($i = 1; $i <= 4; $i++): $s = $steps[$i-1]; ?>
      <div class="process-step" data-aos="fade-up" style="transition-delay:<?= ($i-1)*0.15 ?>s">
        <div class="step-num"><span class="step-icon"><?= $s['icon'] ?></span></div>
        <h3><?= t('process.step'.$i.'.title') ?></h3>
        <p><?= t('process.step'.$i.'.desc') ?></p>
      </div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<!-- ═══ ABOUT PREVIEW ══════════════════���═════════════════════════════════════ -->
<section class="about">
  <div class="container">
    <div class="about-inner">
      <div class="about-image" data-aos="fade-right">
        <img src="/assets/images/hero-1.png" alt="VYNARA FINANCE">
        <div class="about-image-badge">
          <div class="badge-value">10+</div>
          <div class="badge-label"><?= $lang === 'de' ? 'Jahre Erfahrung' : ($lang === 'it' ? 'Anni di Esperienza' : ($lang === 'pt' ? 'Anos de Experiência' : ($lang === 'da' ? 'Års Erfaring' : 'Years Experience') )) ?></div>
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
            <div class="value" data-count="150" data-suffix="+">150+</div>
            <div class="label"><?= $lang === 'de' ? 'Partnerbanken' : ($lang === 'it' ? 'Banche Partner' : 'Partner Banks') ?></div>
          </div>
          <div class="about-stat">
            <div class="value" data-count="9" data-suffix="">9</div>
            <div class="label"><?= $lang === 'de' ? 'Länder' : ($lang === 'it' ? 'Paesi' : 'Countries') ?></div>
          </div>
          <div class="about-stat">
            <div class="value" data-count="10" data-suffix="+">10+</div>
            <div class="label"><?= $lang === 'de' ? 'Jahre' : ($lang === 'it' ? 'Anni' : 'Years') ?></div>
          </div>
        </div>
        <div style="margin-top:32px">
          <a href="/about?lang=<?= h($lang) ?>" class="btn btn-primary"><?= t('cta.learn') ?> →</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ TESTIMONIALS ═════════════════════════════════════════════════════════ -->
<section class="testimonials">
  <div class="container">
    <div class="testimonials-header" data-aos="fade-up">
      <h2><?= t('testimonials.title') ?></h2>
      <p><?= t('testimonials.subtitle') ?></p>
    </div>
    <div class="testimonials-grid">
      <div class="testimonial-card" data-aos="fade-up">
        <div class="testimonial-stars">⭐⭐⭐⭐⭐</div>
        <p class="testimonial-text"><?= t('testimonials.client1.text') ?></p>
        <div class="testimonial-author">
          <div class="author-name"><?= t('testimonials.client1.name') ?></div>
          <div class="author-role"><?= t('testimonials.client1.role') ?></div>
        </div>
      </div>
      <div class="testimonial-card" data-aos="fade-up" style="transition-delay:0.1s">
        <div class="testimonial-stars">⭐⭐⭐⭐⭐</div>
        <p class="testimonial-text"><?= t('testimonials.client2.text') ?></p>
        <div class="testimonial-author">
          <div class="author-name"><?= t('testimonials.client2.name') ?></div>
          <div class="author-role"><?= t('testimonials.client2.role') ?></div>
        </div>
      </div>
      <div class="testimonial-card" data-aos="fade-up" style="transition-delay:0.2s">
        <div class="testimonial-stars">⭐⭐⭐⭐⭐</div>
        <p class="testimonial-text"><?= t('testimonials.client3.text') ?></p>
        <div class="testimonial-author">
          <div class="author-name"><?= t('testimonials.client3.name') ?></div>
          <div class="author-role"><?= t('testimonials.client3.role') ?></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ CTA SECTION ══════════════════════════════════════════════════════════ -->
<section class="cta-section">
  <div class="container">
    <div data-aos="fade-up">
      <div class="section-label" style="justify-content:center"><?= t('nav.apply') ?></div>
      <h2><?= t('cta.section.title') ?></h2>
      <p><?= t('cta.section.desc') ?></p>
      <div class="cta-actions">
        <a href="/apply?lang=<?= h($lang) ?>" class="btn btn-primary btn-lg"><?= t('cta.apply') ?> →</a>
        <a href="/contact?lang=<?= h($lang) ?>" class="btn btn-outline btn-lg"><?= t('cta.contact') ?></a>
      </div>
    </div>
  </div>
</section>
