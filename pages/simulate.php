<?php if (!defined('SITE_NAME')) die();
$pageTitle = t('sim.title');
// Annual interest rates (%) per loan type — used for the estimate
$rates = [
  'personal' => 3.9,
  'business' => 4.9,
  'mortgage' => 2.9,
  'refi'     => 4.4,
];
?>

<section class="page-hero">
  <div class="container">
    <div class="section-label" style="justify-content:center">🧮</div>
    <h1><?= t('sim.title') ?></h1>
    <p><?= t('sim.subtitle') ?></p>
  </div>
</section>

<section class="apply">
  <div class="container">
    <div class="apply-inner">
      <div class="apply-card" data-aos="fade-up">

        <form id="sim-form" novalidate>
          <div class="form-group">
            <label class="form-label"><?= t('sim.type') ?></label>
            <select id="sim-type" class="form-select">
              <option value="personal" data-rate="<?= $rates['personal'] ?>"><?= t('sim.type.personal') ?> — <?= number_format($rates['personal'],1,',','') ?>%</option>
              <option value="business" data-rate="<?= $rates['business'] ?>"><?= t('sim.type.business') ?> — <?= number_format($rates['business'],1,',','') ?>%</option>
              <option value="mortgage" data-rate="<?= $rates['mortgage'] ?>"><?= t('sim.type.mortgage') ?> — <?= number_format($rates['mortgage'],1,',','') ?>%</option>
              <option value="refi" data-rate="<?= $rates['refi'] ?>"><?= t('sim.type.refi') ?> — <?= number_format($rates['refi'],1,',','') ?>%</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label"><?= t('sim.amount') ?> : <strong id="sim-amount-label" style="color:var(--gold-500,#c9a84c)">€50.000</strong></label>
            <input type="range" id="sim-amount" class="sim-range" min="1000" max="2000000" step="1000" value="50000">
          </div>

          <div class="form-group">
            <label class="form-label"><?= t('sim.duration') ?> : <strong id="sim-duration-label" style="color:var(--gold-500,#c9a84c)">60 <?= t('sim.months') ?></strong></label>
            <input type="range" id="sim-duration" class="sim-range" min="6" max="360" step="6" value="60">
          </div>

          <!-- Results -->
          <div class="sim-results" id="sim-results" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin:26px 0 10px;">
            <div class="sim-result sim-result-main" style="grid-column:1 / -1;background:#0a1628;border-radius:14px;padding:22px;text-align:center;">
              <div style="color:#8a93a3;font-size:0.85rem;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:6px;"><?= t('sim.monthly') ?></div>
              <div id="sim-monthly" style="color:#c9a84c;font-size:2.4rem;font-weight:800;line-height:1;">€0</div>
            </div>
            <div class="sim-result" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:16px;">
              <div style="color:var(--text-muted,#8a93a3);font-size:0.8rem;margin-bottom:4px;"><?= t('sim.rate') ?></div>
              <div id="sim-rate" style="font-size:1.2rem;font-weight:700;">—</div>
            </div>
            <div class="sim-result" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:16px;">
              <div style="color:var(--text-muted,#8a93a3);font-size:0.8rem;margin-bottom:4px;"><?= t('sim.interest') ?></div>
              <div id="sim-interest" style="font-size:1.2rem;font-weight:700;">€0</div>
            </div>
            <div class="sim-result" style="grid-column:1 / -1;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:16px;display:flex;justify-content:space-between;align-items:center;">
              <div style="color:var(--text-muted,#8a93a3);font-size:0.8rem;"><?= t('sim.total') ?></div>
              <div id="sim-total" style="font-size:1.2rem;font-weight:700;">€0</div>
            </div>
          </div>

          <a href="/apply?lang=<?= h($lang) ?>" id="sim-cta" class="btn btn-primary form-submit btn-lg">
            <?= t('sim.cta') ?> →
          </a>
          <p class="form-note">ⓘ <?= t('sim.disclaimer') ?></p>
        </form>

      </div>
    </div>
  </div>
</section>
