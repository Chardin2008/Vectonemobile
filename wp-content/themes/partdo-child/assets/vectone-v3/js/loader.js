(function () {
  'use strict';
  var loader = document.querySelector('.vm-v3-loader');
  if (!loader) return;
  var remove = function () { if (loader && loader.parentNode) loader.parentNode.removeChild(loader); };
  var force = new URLSearchParams(window.location.search).get('show_loader') === '1';
  var seen = false;
  try { seen = sessionStorage.getItem('vectone_intro_seen') === '1'; } catch (e) {}
  if (seen && !force) { remove(); return; }
  try {
    var inner = loader.querySelector('.vm-v3-loader__inner');
    if (!inner) throw new Error('loader-inner-missing');
    var brand = loader.querySelector('.vm-v3-loader__brand'); var realLogo = document.querySelector('.site-brand img');
    if (brand && realLogo) { var logo = realLogo.cloneNode(true); logo.removeAttribute('loading'); logo.alt = ''; brand.textContent = ''; brand.appendChild(logo); brand.classList.add('has-real-logo'); }
  } catch (e) { remove(); return; }
  var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var duration = reduced ? 180 : 1120;
  var leave = function () { if (!loader || !document.body.contains(loader)) return; loader.classList.add('is-leaving'); window.setTimeout(function () { try { sessionStorage.setItem('vectone_intro_seen','1'); } catch (e) {} remove(); }, reduced ? 40 : 620); };
  window.setTimeout(leave, duration);
  window.setTimeout(remove, 1800);
}());
