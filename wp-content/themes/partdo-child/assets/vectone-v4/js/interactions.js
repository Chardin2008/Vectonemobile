(function () {
  'use strict';
  function text(selector, root) {
    var el = (root || document).querySelector(selector);
    return el ? el.textContent.trim() : '';
  }
  function init() {
    var hero = document.querySelector('.vectone-v4 .elementor-element-7e305b1 .klbth-banner');
    if (!hero || hero.querySelector('.vectone-v4-price-card')) return;
    var title = text('.entry-title', hero);
    var subtitle = text('.entry-subtitle', hero);
    var price = text('.price', hero);
    var data = (title.match(/\b\d+\s*(?:Go|GB)\b/i) || [])[0] || '';
    var operator = (title.split(/\s+[–—-]\s+/)[1] || '').replace(/\.$/, '').trim();
    if (!data && !operator && !price) return;
    var card = document.createElement('aside');
    card.className = 'vectone-v4-price-card';
    card.setAttribute('aria-label', 'Détails de l’offre affichée');
    card.innerHTML = '<span class="vectone-v4-price-card__operator"></span><strong class="vectone-v4-price-card__data"></strong><span class="vectone-v4-price-card__price"></span>';
    card.querySelector('.vectone-v4-price-card__operator').textContent = operator || subtitle;
    card.querySelector('.vectone-v4-price-card__data').textContent = data;
    card.querySelector('.vectone-v4-price-card__price').textContent = price;
    hero.appendChild(card);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
  window.addEventListener('load', init);
}());
