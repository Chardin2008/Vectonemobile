(function () {
  'use strict';
  function init() {
    var drawer = document.querySelector('.site-drawer');
    var triggers = Array.prototype.slice.call(document.querySelectorAll('.toggle-button'));
    var closeLink = drawer && drawer.querySelector('.site-drawer-header .site-close a');
    if (!drawer || !triggers.length || !closeLink) return;
    var backdrop = document.createElement('div');
    backdrop.className = 'vectone-offcanvas-backdrop';
    backdrop.setAttribute('aria-hidden', 'true');
    document.body.appendChild(backdrop);
    drawer.id = drawer.id || 'vectone-offcanvas';
    closeLink.setAttribute('aria-label', 'Fermer le menu');
    closeLink.setAttribute('role', 'button');
    closeLink.removeAttribute('aria-hidden');
    triggers.forEach(function (trigger) {
      trigger.setAttribute('role', 'button');
      trigger.setAttribute('tabindex', '0');
      trigger.setAttribute('aria-expanded', 'false');
      trigger.setAttribute('aria-controls', drawer.id);
    });
    var lastTrigger = triggers[0];
    function close() {
      drawer.classList.remove('vectone-offcanvas-is-open');
      document.body.classList.remove('vectone-offcanvas-open');
      backdrop.classList.remove('is-visible');
      triggers.forEach(function (trigger) { trigger.setAttribute('aria-expanded', 'false'); });
      if (lastTrigger) lastTrigger.focus();
    }
    function open(trigger) {
      lastTrigger = trigger || lastTrigger;
      drawer.classList.add('vectone-offcanvas-is-open');
      document.body.classList.add('vectone-offcanvas-open');
      backdrop.classList.add('is-visible');
      triggers.forEach(function (item) { item.setAttribute('aria-expanded', item === lastTrigger ? 'true' : 'false'); });
      closeLink.focus();
    }
    function toggle(trigger) {
      if (drawer.classList.contains('vectone-offcanvas-is-open')) close(); else open(trigger);
    }
    document.addEventListener('click', function (event) {
      var trigger = event.target.closest && event.target.closest('.toggle-button');
      if (trigger && triggers.indexOf(trigger) !== -1) { event.preventDefault(); event.stopPropagation(); toggle(trigger); return; }
      if (event.target === backdrop || event.target.closest && event.target.closest('.site-drawer-header .site-close a')) { event.preventDefault(); close(); return; }
      if (drawer.classList.contains('vectone-offcanvas-is-open') && event.target.closest && event.target.closest('.site-drawer a[href]') && !event.target.closest('.site-drawer-header .site-close a')) close();
    }, true);
    document.addEventListener('keydown', function (event) {
      var trigger = event.target.closest && event.target.closest('.toggle-button');
      if (trigger && (event.key === 'Enter' || event.key === ' ')) { event.preventDefault(); toggle(trigger); }
      if (event.key === 'Escape' && drawer.classList.contains('vectone-offcanvas-is-open')) { event.preventDefault(); close(); }
    }, true);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
}());
