<?php if (!defined('SITE_NAME')) die();
$pageTitle = t('nav.apply'); ?>

<section class="page-hero">
  <div class="container">
    <div class="section-label" style="justify-content:center">📋</div>
    <h1><?= t('apply.title') ?></h1>
    <p><?= t('apply.subtitle') ?></p>
  </div>
</section>

<section class="apply">
  <div class="container">
    <div class="apply-inner">

      <!-- Info Banner -->
      <div class="loan-range" data-aos="fade-up">
        <span class="range-label">💳 <?= t('services.personal.title') ?></span>
        <span>→</span>
        <span class="range-value">€1.000 – €150.000</span>
        <span>|</span>
        <span class="range-label">🏢 <?= t('services.business.title') ?></span>
        <span>→</span>
        <span class="range-value">€150.000 – €2.000.000</span>
      </div>

      <div class="apply-card" data-aos="fade-up">
        <div class="apply-header">
          <div class="section-label" style="justify-content:center">
            <?= t('hero.trust1') ?> &nbsp;·&nbsp; <?= t('hero.trust2') ?> &nbsp;·&nbsp; <?= t('hero.trust3') ?>
          </div>
          <h2 style="font-size:1.8rem;margin-bottom:8px"><?= t('apply.title') ?></h2>
          <p style="font-size:0.95rem"><?= t('apply.subtitle') ?></p>
        </div>

        <div class="form-alert" id="applyAlert"></div>
        <form id="apply-form" novalidate>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label"><?= t('apply.firstname') ?> *</label>
              <input type="text" name="first_name" class="form-input" required>
            </div>
            <div class="form-group">
              <label class="form-label"><?= t('apply.lastname') ?> *</label>
              <input type="text" name="last_name" class="form-input" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label"><?= t('apply.email') ?> *</label>
              <input type="email" name="email" class="form-input" required>
            </div>
            <div class="form-group">
              <label class="form-label"><?= t('apply.phone') ?> *</label>
              <input type="tel" name="phone" class="form-input" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label"><?= t('apply.amount') ?> *</label>
              <input type="number" name="amount" id="amount" class="form-input" min="1000" max="2000000" required placeholder="50000">
            </div>
            <div class="form-group">
              <label class="form-label"><?= t('apply.duration') ?> *</label>
              <select name="duration" class="form-select" required>
                <option value="">—</option>
                <?php foreach ([12,24,36,48,60,84,120,180,240,360] as $m): ?>
                <option value="<?= $m ?>"><?= $m ?> <?= $m <= 24 ? '' : '' ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label"><?= t('apply.purpose') ?> *</label>
            <select name="purpose" class="form-select" required>
              <option value="">—</option>
              <?php
              $purposes = ['house','car','business','renovation','studies','debt','other'];
              foreach ($purposes as $p): ?>
              <option value="<?= $p ?>"><?= t('purpose.'.$p) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label"><?= t('apply.message') ?></label>
            <textarea name="message" class="form-textarea" rows="3"></textarea>
          </div>

          <input type="hidden" name="lang" value="<?= h($lang) ?>">
          <input type="hidden" name="country" value="<?= h(LANGUAGES[$lang]['name'] ?? '') ?>">

          <button type="submit" class="btn btn-primary form-submit btn-lg">
            <?= t('apply.submit') ?> →
          </button>
          <p class="form-note">🔒 <?= t('feature.secure.desc') ?></p>
        </form>

      </div>
    </div>
  </div>
</section>
