(function () {
  'use strict';

  function init() {
    document.querySelectorAll('.vm-prixtel-mobile-hero').forEach(function (node) { node.remove(); });
    var source = document.querySelector('.elementor-element-7e305b1');
    if (!source) return;
    var image = source.querySelector('.klbth-banner .entry-media img');
    if (!image) return;
    var realSource = image.getAttribute('data-src') || image.getAttribute('data-lazy-src');
    var current = image.currentSrc || image.getAttribute('src') || '';
    if (realSource && (!current || current.indexOf('data:image') === 0)) image.src = realSource;
    image.loading = 'eager';
    image.decoding = 'async';
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
}());
