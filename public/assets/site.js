(function () {
    'use strict';

    const root = document.documentElement;
    const body = document.body;

    // Theme toggle ----------------------------------------------------
    const themeToggle = document.getElementById('theme-toggle');
    const stored = localStorage.getItem('pr6-theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const initial = stored || (prefersDark ? 'dark' : 'light');
    root.setAttribute('data-theme', initial);
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            localStorage.setItem('pr6-theme', next);
        });
    }

    // Vertical hover tints page background ----------------------------
    const verticals = document.querySelectorAll('.vertical');
    verticals.forEach(card => {
        card.addEventListener('mouseenter', () => {
            const accent = getComputedStyle(card).getPropertyValue('--accent').trim();
            body.style.setProperty('--page-tint',
                `radial-gradient(1000px 600px at 50% 0%, ${accent}22, transparent 60%)`);
            body.setAttribute('data-tint-active', 'true');
        });
        card.addEventListener('mouseleave', () => {
            body.removeAttribute('data-tint-active');
        });
    });

    // Count up stats --------------------------------------------------
    const counters = document.querySelectorAll('[data-count]');
    const counterIO = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el = entry.target;
            const target = parseInt(el.dataset.count, 10);
            const duration = 1400;
            const startTime = performance.now();
            const startValue = 0;
            const tick = (now) => {
                const progress = Math.min((now - startTime) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.round(startValue + (target - startValue) * eased);
                if (progress < 1) requestAnimationFrame(tick);
                else el.textContent = target;
            };
            requestAnimationFrame(tick);
            counterIO.unobserve(el);
        });
    }, { threshold: 0.4 });
    counters.forEach(c => counterIO.observe(c));

    // Scroll reveal ---------------------------------------------------
    const revealTargets = document.querySelectorAll(
        '.section-head, .vertical, .stat, .news, .agenda-item, .indicator, .ouvidoria__inner'
    );
    revealTargets.forEach(el => el.classList.add('reveal'));
    const revealIO = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                revealIO.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });
    revealTargets.forEach(el => revealIO.observe(el));

    // News filter -----------------------------------------------------
    const chips = document.querySelectorAll('.chip[data-filter]');
    const newsCards = document.querySelectorAll('.news[data-tenant]');
    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            chips.forEach(c => c.classList.remove('is-active'));
            chip.classList.add('is-active');
            const filter = chip.dataset.filter;
            newsCards.forEach(card => {
                const matches = filter === 'all' || card.dataset.tenant === filter;
                card.style.transition = 'opacity .3s ease, transform .3s ease';
                if (matches) {
                    card.style.display = '';
                    requestAnimationFrame(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    });
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(8px)';
                    setTimeout(() => { card.style.display = 'none'; }, 300);
                }
            });
        });
    });

    // Nav dropdown toggle (mobile-friendly) --------------------------
    document.querySelectorAll('.nav__has-children').forEach(item => {
        const trigger = item.querySelector('.nav__trigger');
        if (!trigger) return;
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            const isOpen = item.dataset.open === 'true';
            document.querySelectorAll('.nav__has-children[data-open="true"]').forEach(o => o.removeAttribute('data-open'));
            if (!isOpen) item.dataset.open = 'true';
            trigger.setAttribute('aria-expanded', !isOpen);
        });
    });
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.nav__has-children')) {
            document.querySelectorAll('.nav__has-children[data-open="true"]').forEach(o => {
                o.removeAttribute('data-open');
                o.querySelector('.nav__trigger')?.setAttribute('aria-expanded', 'false');
            });
        }
    });

    // LGPD banner — 3 níveis -----------------------------------------
    const banner = document.getElementById('lgpd-banner');
    if (banner) {
        const stored = localStorage.getItem('pr6-lgpd-v2');
        if (!stored) setTimeout(() => banner.removeAttribute('hidden'), 800);

        const choices = banner.querySelector('[data-lgpd-choices]');
        const btnCustomize = banner.querySelector('[data-lgpd-customize]');
        const btnEssential = banner.querySelector('[data-lgpd-essential-only]');
        const btnAcceptAll = banner.querySelector('[data-lgpd-accept-all]');
        const btnSaveCustom = banner.querySelector('[data-lgpd-save-custom]');

        function getCsrf() {
            return document.querySelector('meta[name="csrf-token"]')?.content || '';
        }

        async function send(consents) {
            try {
                await fetch('/lgpd/consent', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrf(),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ consents }),
                });
            } catch (e) {}
            localStorage.setItem('pr6-lgpd-v2', JSON.stringify({ consents, at: Date.now() }));
            banner.setAttribute('hidden', '');
        }

        btnCustomize?.addEventListener('click', () => {
            choices.removeAttribute('hidden');
            btnCustomize.setAttribute('hidden', '');
            btnSaveCustom.removeAttribute('hidden');
        });
        btnEssential?.addEventListener('click', () => send({ essential: true, analytics: false, marketing: false }));
        btnAcceptAll?.addEventListener('click', () => send({ essential: true, analytics: true, marketing: true }));
        btnSaveCustom?.addEventListener('click', () => {
            const consents = {
                essential: true,
                analytics: choices.querySelector('input[name="analytics"]').checked,
                marketing: choices.querySelector('input[name="marketing"]').checked,
            };
            send(consents);
        });
    }

})();
