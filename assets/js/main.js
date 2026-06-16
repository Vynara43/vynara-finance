/* ═══════════════════════════════════════════════════════════════════════════
   VYNARA FINANCE — Main JavaScript
   ═══════════════════════════════════════════════════════════════════════════ */

'use strict';

/* ── Page Loader ────────────────────────────────────────────────────────────── */
window.addEventListener('load', () => {
  const loader = document.querySelector('.page-loader');
  if (loader) {
    setTimeout(() => loader.classList.add('hidden'), 800);
  }
});

/* ── Navbar scroll effect ───────────────────────────────────────────────────── */
const navbar = document.querySelector('.navbar');
if (navbar) {
  const onScroll = () => {
    navbar.classList.toggle('scrolled', window.scrollY > 30);
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
}

/* ── Mobile menu ────────────────────────────────────────────────────────────── */
const menuToggle = document.querySelector('.menu-toggle');
const mobileNav  = document.querySelector('.mobile-nav');
if (menuToggle && mobileNav) {
  menuToggle.addEventListener('click', () => {
    const open = mobileNav.classList.toggle('open');
    menuToggle.classList.toggle('active', open);
    document.body.style.overflow = open ? 'hidden' : '';
  });
  mobileNav.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => {
      mobileNav.classList.remove('open');
      menuToggle.classList.remove('active');
      document.body.style.overflow = '';
    });
  });
}

/* ── Language Switcher ──────────────────────────────────────────────────────── */
const langSwitcher = document.querySelector('.lang-switcher');
if (langSwitcher) {
  const trigger  = langSwitcher.querySelector('.lang-trigger');
  const options  = langSwitcher.querySelectorAll('.lang-option');

  trigger.addEventListener('click', (e) => {
    e.stopPropagation();
    langSwitcher.classList.toggle('open');
  });

  document.addEventListener('click', () => langSwitcher.classList.remove('open'));

  options.forEach(opt => {
    opt.addEventListener('click', () => {
      const lang = opt.dataset.lang;
      // Navigate with lang param
      const url = new URL(window.location.href);
      url.searchParams.set('lang', lang);
      window.location.href = url.toString();
    });
  });
}

/* ── Hero Carousel ──────────────────────────────────────────────────────────── */
(function initCarousel() {
  const slides = document.querySelectorAll('.carousel-slide');
  const dots   = document.querySelectorAll('.carousel-dot');
  if (!slides.length) return;

  let current = 0;
  let timer;

  const goTo = (index) => {
    slides[current].classList.remove('active');
    dots[current]?.classList.remove('active');
    current = (index + slides.length) % slides.length;
    slides[current].classList.add('active');
    dots[current]?.classList.add('active');
  };

  const next = () => goTo(current + 1);
  const startAuto = () => { timer = setInterval(next, 5000); };
  const stopAuto  = () => clearInterval(timer);

  dots.forEach((dot, i) => {
    dot.addEventListener('click', () => { stopAuto(); goTo(i); startAuto(); });
  });

  startAuto();
  // Pause on hover
  const carousel = document.querySelector('.hero-carousel');
  if (carousel) {
    carousel.addEventListener('mouseenter', stopAuto);
    carousel.addEventListener('mouseleave', startAuto);
  }
})();

/* ── Stats Counter ──────────────────────────────────────────────────────────── */
(function initCounters() {
  const counters = document.querySelectorAll('[data-count]');
  if (!counters.length) return;

  const easeOut = (t) => 1 - Math.pow(1 - t, 3);

  const animateCounter = (el) => {
    const target  = parseFloat(el.dataset.count);
    const prefix  = el.dataset.prefix  || '';
    const suffix  = el.dataset.suffix  || '';
    const decimals= parseInt(el.dataset.decimals || '0');
    const dur     = 2200;
    const start   = performance.now();

    const tick = (now) => {
      const elapsed  = now - start;
      const progress = Math.min(elapsed / dur, 1);
      const value    = target * easeOut(progress);
      el.textContent = prefix + value.toFixed(decimals) + suffix;
      if (progress < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting && !e.target.dataset.counted) {
        e.target.dataset.counted = '1';
        animateCounter(e.target);
      }
    });
  }, { threshold: 0.5 });

  counters.forEach(c => observer.observe(c));
})();

/* ── Scroll Animations (data-aos) ───────────────────────────────────────────── */
(function initScrollAnimations() {
  const els = document.querySelectorAll('[data-aos]');
  if (!els.length) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('aos-animate');
        observer.unobserve(e.target);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

  els.forEach(el => observer.observe(el));
})();

/* ── Smooth Scroll for anchors ──────────────────────────────────────────────── */
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const target = document.querySelector(a.getAttribute('href'));
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});

/* ── Contact Form (AJAX) ────────────────────────────────────────────────────── */
const contactForm = document.getElementById('contact-form');
if (contactForm) {
  contactForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn   = contactForm.querySelector('[type=submit]');
    const alert = contactForm.querySelector('.form-alert');
    const orig  = btn.textContent;

    btn.disabled = true;
    btn.textContent = '...';

    const formData = new FormData(contactForm);
    formData.append('lang', document.documentElement.lang || 'de');

    try {
      const resp = await fetch('/api/contact.php', { method: 'POST', body: formData });
      const data = await resp.json();

      alert.className = 'form-alert show ' + (data.success ? 'success' : 'error');
      alert.textContent = data.message;

      if (data.success) contactForm.reset();
    } catch {
      alert.className = 'form-alert show error';
      alert.textContent = 'Erreur réseau. Réessayez.';
    } finally {
      btn.disabled = false;
      btn.textContent = orig;
    }
  });
}

/* ── Apply Form (AJAX) ──────────────────────────────────────────────────────── */
const applyForm = document.getElementById('apply-form');
if (applyForm) {
  applyForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn   = applyForm.querySelector('[type=submit]');
    const alert = applyForm.querySelector('.form-alert');
    const orig  = btn.textContent;

    btn.disabled = true;
    btn.textContent = '...';

    const formData = new FormData(applyForm);
    formData.append('lang', document.documentElement.lang || 'de');

    try {
      const resp = await fetch('/api/apply.php', { method: 'POST', body: formData });
      const data = await resp.json();

      alert.className = 'form-alert show ' + (data.success ? 'success' : 'error');
      alert.textContent = data.message;

      if (data.success) applyForm.reset();
    } catch {
      alert.className = 'form-alert show error';
      alert.textContent = 'Erreur réseau. Réessayez.';
    } finally {
      btn.disabled = false;
      btn.textContent = orig;
    }
  });
}

/* ── WhatsApp button tooltip label ─────────────────────────────────────────── */
const waBtn = document.querySelector('.whatsapp-btn');
if (waBtn) {
  // Pulse animation after 3s
  setTimeout(() => {
    waBtn.style.animation = 'wa-pulse 1s ease 2';
  }, 3000);
}

const waStyle = document.createElement('style');
waStyle.textContent = `
  @keyframes wa-pulse {
    0%  { transform: scale(1); }
    50% { transform: scale(1.15); }
    100%{ transform: scale(1); }
  }
`;
document.head.appendChild(waStyle);

/* ── Active nav link ────────────────────────────────────────────────────────── */
(function markActiveNav() {
  const path = window.location.pathname;
  document.querySelectorAll('.navbar-nav a, .mobile-nav a').forEach(a => {
    const href = a.getAttribute('href') || '';
    const segment = href.split('?')[0];
    if (
      (segment === '/' && (path === '/' || path === '/index.php')) ||
      (segment !== '/' && path.startsWith(segment))
    ) {
      a.classList.add('active');
    }
  });
})();

/* ── Number input formatting ────────────────────────────────────────────────── */
const amountInput = document.getElementById('amount');
if (amountInput) {
  amountInput.addEventListener('input', () => {
    let v = amountInput.value.replace(/\D/g, '');
    if (v > 2000000) v = 2000000;
    amountInput.value = v;
  });
}

/* ── Loan Simulator ─────────────────────────────────────────────────────────── */
(function initSimulator() {
  const form = document.getElementById('sim-form');
  if (!form) return;

  const typeEl     = document.getElementById('sim-type');
  const amountEl   = document.getElementById('sim-amount');
  const durationEl = document.getElementById('sim-duration');
  const amountLbl  = document.getElementById('sim-amount-label');
  const durLbl     = document.getElementById('sim-duration-label');
  const monthsTxt  = (durLbl.textContent.match(/[^0-9\s]+.*$/) || ['mois'])[0].trim() ||
                     durLbl.textContent.replace(/[0-9]/g, '').trim();
  const monthsWord = durLbl.textContent.replace(/[0-9]/g, '').trim() || 'mois';

  const out = {
    monthly:  document.getElementById('sim-monthly'),
    rate:     document.getElementById('sim-rate'),
    interest: document.getElementById('sim-interest'),
    total:    document.getElementById('sim-total'),
  };
  const cta = document.getElementById('sim-cta');
  const fmt = new Intl.NumberFormat('de-DE', { maximumFractionDigits: 0 });

  function compute() {
    const rate     = parseFloat(typeEl.selectedOptions[0].dataset.rate) || 3.9;
    const amount   = parseFloat(amountEl.value) || 0;
    const months   = parseInt(durationEl.value) || 1;
    const r        = rate / 100 / 12;
    const monthly  = r > 0 ? (amount * r) / (1 - Math.pow(1 + r, -months)) : amount / months;
    const total    = monthly * months;
    const interest = total - amount;

    amountLbl.textContent = '€' + fmt.format(amount);
    durLbl.textContent    = months + ' ' + monthsWord;
    out.monthly.textContent  = '€' + fmt.format(Math.round(monthly));
    out.rate.textContent     = rate.toFixed(1).replace('.', ',') + '%';
    out.interest.textContent = '€' + fmt.format(Math.round(interest));
    out.total.textContent    = '€' + fmt.format(Math.round(total));

    const url = new URL(cta.href, window.location.origin);
    url.searchParams.set('amount', Math.round(amount));
    cta.href = url.pathname + '?' + url.searchParams.toString();
  }

  [typeEl, amountEl, durationEl].forEach(el => {
    el.addEventListener('input', compute);
    el.addEventListener('change', compute);
  });
  compute();
})();
