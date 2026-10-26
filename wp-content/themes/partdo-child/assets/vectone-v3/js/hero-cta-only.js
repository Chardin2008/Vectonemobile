(function () {
  'use strict';

  function makeHeroCtaOnly() {
    var hero = document.querySelector('.elementor-element-7e305b1 .klbth-banner');
    if (!hero) return;

    var mask = hero.querySelector(':scope > .link-mask');
    if (mask) mask.remove();

    var button = hero.querySelector('.banner-button button[href]');
    if (!button || button.dataset.heroCtaLinkReady) return;

    var link = document.createElement('a');
    link.href = button.getAttribute('href');
    link.target = button.getAttribute('target') || '_blank';
    link.rel = button.getAttribute('rel') || 'sponsored noopener noreferrer';
    link.className = button.className;
    link.innerHTML = button.innerHTML;
    button.replaceWith(link);
    link.dataset.heroCtaLinkReady = 'true';
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', makeHeroCtaOnly);
  } else {
    makeHeroCtaOnly();
  }
}());
