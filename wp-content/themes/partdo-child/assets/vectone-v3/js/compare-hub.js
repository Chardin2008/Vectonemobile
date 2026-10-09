(function () {
  'use strict';
  function setup(anchor, index) {
    if (anchor.dataset.compareHubReady) return;
    var item = anchor.closest('li');
    var submenu = item && item.querySelector(':scope > .sub-menu');
    if (!item || !submenu) return;
    anchor.dataset.compareHubReady = 'true';
    anchor.textContent = 'Comparer';
    anchor.classList.add('vectone-compare-toggle');
    anchor.setAttribute('role', 'button');
    anchor.setAttribute('aria-expanded', 'false');
    submenu.id = submenu.id || 'vectone-compare-menu-' + index;
    anchor.setAttribute('aria-controls', submenu.id);
    anchor.href = (window.VectoneCompareHub && window.VectoneCompareHub.all) || '/';
    var chevron = document.createElement('span');
    chevron.className = 'vectone-compare-toggle__chevron';
    chevron.setAttribute('aria-hidden', 'true');
    chevron.textContent = '▾';
    anchor.appendChild(chevron);
    function close() {
      item.classList.remove('is-open');
      anchor.setAttribute('aria-expanded', 'false');
    }
    function toggle(event) {
      event.preventDefault();
      var open = item.classList.toggle('is-open');
      anchor.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    anchor.addEventListener('click', toggle);
    anchor.addEventListener('keydown', function (event) {
      if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); toggle(event); }
      if (event.key === 'Escape') close();
    });
    document.addEventListener('click', function (event) { if (!item.contains(event.target)) close(); });
    document.addEventListener('keydown', function (event) { if (event.key === 'Escape') close(); });
  }
  function init() {
    Array.prototype.slice.call(document.querySelectorAll('.site-header a')).filter(function (el) {
      return ['Comparatifs forfaits mobiles', 'Comparer'].indexOf(el.textContent.trim()) !== -1;
    }).forEach(setup);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
}());
