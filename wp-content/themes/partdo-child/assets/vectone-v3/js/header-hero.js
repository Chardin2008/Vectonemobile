(function () {
  'use strict';

  function initVectoneV3HeaderHero() {
    // Sur l'ancienne homepage, le menu "Forfait mobile" pouvait arriver ouvert
    // et recouvrir le hero. On le ferme seulement au chargement ; le contrôle
    // existant reste ensuite libre de le rouvrir.
    if (document.body.classList.contains('home')) {
      document.querySelectorAll('.site-header .dropdown-menu.collapse.show').forEach(function (menu) {
        menu.classList.remove('show');
      });

      document.querySelectorAll('.site-header [aria-expanded="true"]').forEach(function (toggle) {
        if (toggle.closest('.header-nav')) {
          toggle.setAttribute('aria-expanded', 'false');
        }
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initVectoneV3HeaderHero);
  } else {
    initVectoneV3HeaderHero();
  }
}());
