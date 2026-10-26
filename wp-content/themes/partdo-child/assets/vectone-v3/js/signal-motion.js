(function () {
  'use strict';

  function initSignalHero() {
    if (!document.body.classList.contains('home')) return;
    var hero = document.querySelector('.elementor-element-7e305b1 .klbth-banner');
    if (!hero || hero.classList.contains('vm-signal-hero')) return;
    hero.classList.add('vm-signal-hero');

    window.requestAnimationFrame(function () {
      window.setTimeout(function () { hero.classList.add('vm-signal-ready'); }, 40);
    });

    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    if (window.matchMedia && window.matchMedia('(max-width: 767px)').matches) return;

    var media = hero.querySelector('.vm-v3-fallback-media, .entry-media');
    if (!media) return;
    var frame = 0;
    media.addEventListener('mousemove', function (event) {
      var rect = media.getBoundingClientRect();
      var x = ((event.clientX - rect.left) / rect.width - .5) * 8;
      var y = ((event.clientY - rect.top) / rect.height - .5) * 6;
      window.cancelAnimationFrame(frame);
      frame = window.requestAnimationFrame(function () {
        media.style.setProperty('--vm-signal-x', x.toFixed(2) + 'px');
        media.style.setProperty('--vm-signal-y', y.toFixed(2) + 'px');
      });
    });
    media.addEventListener('mouseleave', function () {
      window.cancelAnimationFrame(frame);
      media.style.setProperty('--vm-signal-x', '0px');
      media.style.setProperty('--vm-signal-y', '0px');
    });
  }

  function initSectionReveals() {
    if (!document.body.classList.contains('home')) return;
    var selector = [
      '.vm-v3-network-signal',
      '.vm-v3-operators',
      '.vm-v3-featured',
      '.vm-v3-editorial-magazine',
      '.vm-v3-comparator-intro',
      '.vm-v3-reviews-trust',
      '.vm-v3-editorial-guide'
    ].join(',');
    var sections = Array.prototype.slice.call(document.querySelectorAll(selector));
    if (!sections.length) return;
    sections.forEach(function (section) {
      section.classList.add('vm-signal-reveal');
      var title = section.querySelector('.vm-v3-eyebrow');
      if (title) title.classList.add('vm-signal-title');
      if (section.classList.contains('vm-v3-reviews-trust')) {
        Array.prototype.slice.call(section.querySelectorAll('.vm-v3-review-card')).forEach(function (card, index) {
          card.classList.add(index % 2 ? 'vm-signal-review-even' : 'vm-signal-review-odd');
        });
      }
    });
    if (!('IntersectionObserver' in window) || (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches)) {
      sections.forEach(function (section) { section.classList.add('is-signal-visible'); });
      return;
    }
    var observer = new IntersectionObserver(function (entries, instance) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-signal-visible');
        instance.unobserve(entry.target);
      });
    }, { threshold: 0.14 });
    sections.forEach(function (section) { observer.observe(section); });
  }

  function initChapterDividers() {
    if (!document.body.classList.contains('home')) return;
    var selector = '.vm-v3-featured, .vm-v3-comparator-intro, .vm-v3-reviews-trust, .vm-v3-editorial-magazine, .vm-v3-editorial-guide';
    Array.prototype.slice.call(document.querySelectorAll(selector)).forEach(function (section) {
      if (section.previousElementSibling && section.previousElementSibling.classList.contains('vm-signal-divider')) return;
      var divider = document.createElement('span');
      divider.className = 'vm-signal-divider';
      divider.setAttribute('aria-hidden', 'true');
      section.parentNode.insertBefore(divider, section);
    });
    var dividers = Array.prototype.slice.call(document.querySelectorAll('.vm-signal-divider'));
    if (!('IntersectionObserver' in window) || (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches)) {
      dividers.forEach(function (divider) { divider.classList.add('is-signal-visible'); });
      return;
    }
    var observer = new IntersectionObserver(function (entries, instance) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-signal-visible');
        instance.unobserve(entry.target);
      });
    }, { threshold: .2 });
    dividers.forEach(function (divider) { observer.observe(divider); });
  }

  function initCompareFeedback() {
    document.addEventListener('click', function (event) {
      var button = event.target.closest && event.target.closest('.vectone-compare-button');
      if (!button) return;
      var card = button.closest('.product-wrapper');
      if (!card) return;
      card.classList.remove('vm-signal-connected');
      window.requestAnimationFrame(function () {
        card.classList.add('vm-signal-connected');
        window.setTimeout(function () { card.classList.remove('vm-signal-connected'); }, 460);
      });
    });
  }

  function initSignalRail() {
    if (!document.body.classList.contains('home') || window.matchMedia && window.matchMedia('(max-width: 1024px)').matches) return;
    if (document.querySelector('.vm-signal-rail')) return;
    var sections = Array.prototype.slice.call(document.querySelectorAll('.vm-v3-featured, .vm-v3-comparator-intro, .vm-v3-reviews-trust, .vm-v3-editorial-magazine, .vm-v3-editorial-guide'));
    if (!sections.length) return;
    var rail = document.createElement('aside');
    rail.className = 'vm-signal-rail';
    rail.setAttribute('aria-hidden', 'true');
    sections.forEach(function () { var dot = document.createElement('i'); dot.className = 'vm-signal-rail__dot'; rail.appendChild(dot); });
    document.body.appendChild(rail);
    var dots = Array.prototype.slice.call(rail.querySelectorAll('.vm-signal-rail__dot'));
    if (!('IntersectionObserver' in window)) return;
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var index = sections.indexOf(entry.target);
        dots.forEach(function (dot, dotIndex) { dot.classList.toggle('is-active', dotIndex === index); });
      });
    }, { threshold: .3 });
    sections.forEach(function (section) { observer.observe(section); });
  }

  function initFooterSequence() {
    var footer = document.querySelector('.vm-signal-footer');
    if (!footer || footer.classList.contains('vm-footer-sequence-ready')) return;
    footer.classList.add('vm-footer-sequence-ready');
    if (!('IntersectionObserver' in window) || (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches)) {
      footer.classList.add('is-signal-visible');
      return;
    }
    var observer = new IntersectionObserver(function (entries, instance) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        footer.classList.add('is-signal-visible');
        instance.disconnect();
      });
    }, { threshold: .18 });
    observer.observe(footer);
  }

  function initActualitesMotion() {
    if (!document.body.classList.contains('blog')) return;
    var cards = Array.prototype.slice.call(document.querySelectorAll('.klb-blog .blog-posts > article.klb-article, .elementor-element-1a54d13 .blog-posts .post'));
    if (!cards.length) return;
    document.body.classList.add('vm-news-motion-ready');
    cards.forEach(function (card) { card.classList.add('vm-news-motion-card'); });
    var intro = document.querySelector('.vectone-news-intro');
    var blog = document.querySelector('.klb-blog');
    if (intro && blog && (!intro.nextElementSibling || !intro.nextElementSibling.classList.contains('vm-signal-divider'))) {
      var divider = document.createElement('span');
      divider.className = 'vm-signal-divider vm-news-divider';
      divider.setAttribute('aria-hidden', 'true');
      intro.parentNode.insertBefore(divider, blog);
    }
    if (!('IntersectionObserver' in window) || (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches)) {
      cards.forEach(function (card) { card.classList.add('is-signal-visible'); });
      return;
    }
    var observer = new IntersectionObserver(function (entries, instance) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-signal-visible');
        instance.unobserve(entry.target);
      });
    }, { threshold: .12 });
    cards.forEach(function (card) { observer.observe(card); });
  }

  function applyTestimonialAvatarFallback(card) {
    if (!card || !card.matches('.slider-item.testimonial')) return;
    var avatar = card.querySelector('.customer-avatar');
    if (!avatar) return;
    var image = avatar.querySelector('img');
    var nameNode = card.querySelector('.customer-name');
    var name = nameNode ? nameNode.textContent.trim() : '';
    var words = name.replace(/[^\p{L}\p{N}\s'-]/gu, ' ').split(/\s+/).filter(Boolean);
    var initials = words.length > 1
      ? (words[0].charAt(0) + words[words.length - 1].charAt(0)).toUpperCase()
      : (words[0] ? words[0].charAt(0).toUpperCase() : '?');
    var fallback = avatar.querySelector('.vm-testimonial-avatar-fallback');
    if (!fallback) {
      fallback = document.createElement('span');
      fallback.className = 'vm-testimonial-avatar-fallback';
      fallback.setAttribute('aria-hidden', 'true');
      avatar.appendChild(fallback);
    }
    fallback.textContent = initials;
    fallback.hidden = true;
    var showFallback = function () {
      if (image) { image.hidden = true; image.removeAttribute('alt'); }
      fallback.hidden = false;
    };
    if (!image) { showFallback(); return; }
    image.addEventListener('error', showFallback, { once: true });
    if (image.complete && image.naturalWidth === 0) window.setTimeout(showFallback, 0);
  }

  function initInternalTestimonialAvatars() {
    var body = document.body;
    if (body.classList.contains('home')) return;
    if (!body.classList.contains('tax-product_cat') &&
        !body.classList.contains('search') &&
        !body.classList.contains('search-results') &&
        !body.classList.contains('page-id-6672') &&
        !body.classList.contains('page-id-6673') &&
        !body.classList.contains('page-id-6674')) return;
    var widget = document.querySelector('.elementor-element-371471e');
    if (!widget) return;
    var scan = function (root) {
      var cards = [];
      if (root && root.matches && root.matches('.slider-item.testimonial')) cards.push(root);
      if (root && root.querySelectorAll) cards = cards.concat(Array.prototype.slice.call(root.querySelectorAll('.slider-item.testimonial')));
      cards.forEach(applyTestimonialAvatarFallback);
    };
    scan(widget);
    if (!('MutationObserver' in window)) return;
    var observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        Array.prototype.forEach.call(mutation.addedNodes, scan);
      });
    });
    observer.observe(widget, { childList: true, subtree: true });
  }

  function init() {
    initHeaderMotion();
    initChapterDividers();
    initCompareFeedback();
    initSignalRail();
    initFooterSequence();
    initActualitesMotion();
    initInternalTestimonialAvatars();
    initSignalHero();
    initSectionReveals();
  }

  function initHeaderMotion() {
    var body = document.body;
    var header = document.querySelector('.site-header');
    if (!header || body.classList.contains('vm-signal-motion-ready')) return;
    body.classList.add('vm-signal-motion-ready');
    window.requestAnimationFrame(function () { body.classList.add('vm-signal-header-ready'); });
    var ticking = false;
    window.addEventListener('scroll', function () {
      if (ticking) return;
      ticking = true;
      window.requestAnimationFrame(function () {
        body.classList.toggle('vm-signal-header-scrolled', window.scrollY > 12);
        ticking = false;
      });
    }, { passive: true });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
}());
