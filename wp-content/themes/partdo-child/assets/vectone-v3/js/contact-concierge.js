(function () {
  'use strict';
  function init() {
    if (!document.body.classList.contains('page-id-2144') || document.querySelector('.vm-contact-letter')) return;
    var form = document.querySelector('.elementor-element-5d759af');
    var section = form && form.closest('.elementor-section');
    if (!form || !section) return;
    var intro = document.createElement('section'); intro.className = 'vm-contact-letter'; intro.setAttribute('aria-labelledby', 'vm-contact-letter-title');
    intro.innerHTML = '<span class="vm-contact-letter__line" aria-hidden="true"></span><div class="vm-contact-letter__eyebrow">Contact / Vectone</div><h1 id="vm-contact-letter-title">Écrivez-nous.<br>On vous lit.</h1><p>Une question sur un forfait, une offre ou le site ? Envoyez-nous votre message.</p><div class="vm-contact-letter__direct"><small>Contact direct</small><a href="mailto:contact@vectonemobile.fr">contact@vectonemobile.fr</a></div>';
    section.parentNode.insertBefore(intro, section);
    intro.querySelector('h1').innerHTML = 'Comment pouvons-nous<br>vous aider ?';
    var left = document.querySelector('.elementor-element-31bd2c9');
    if (left) { left.classList.add('vm-contact-letter__context'); var side = document.createElement('aside'); side.className = 'vm-contact-letter__side'; side.innerHTML = '<div class="vm-contact-letter__side-label">Nous écrire</div><h2>Une demande ?<br>Parlez-nous-en.</h2><p>Utilisez le formulaire pour nous transmettre votre demande.</p><small>Adresse e-mail</small><a href="mailto:contact@vectonemobile.fr">contact@vectonemobile.fr</a>'; left.querySelector('.elementor-widget-wrap').prepend(side); }
    var spacer = document.querySelector('.elementor-element-2dd4f00'); if (spacer) spacer.classList.add('vm-contact-letter__spacer');
    var right = document.querySelector('.elementor-element-d4b1c9c');
    if (right) { right.classList.add('vm-contact-letter__form'); var heading = right.querySelector('.elementor-element-f824e4b h2'); if (heading) heading.textContent = 'Envoyez votre message'; }
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
}());
