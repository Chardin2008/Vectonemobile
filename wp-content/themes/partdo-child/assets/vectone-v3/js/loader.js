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
    var ns = 'http://www.w3.org/2000/svg';
    var svg = document.createElementNS(ns, 'svg'); svg.classList.add('vm-v3-loader__signal'); svg.setAttribute('viewBox', '0 0 420 420');
    var points = [[52,82],[350,70],[56,330],[364,326],[104,210]];
    var routes = ['M52 82 Q120 120 210 210','M350 70 Q285 132 210 210','M56 330 Q145 300 210 210','M364 326 Q278 278 210 210','M104 210 Q156 178 210 210'];
    routes.forEach(function (route) { var path = document.createElementNS(ns, 'path'); path.setAttribute('d', route); svg.appendChild(path); });
    points.forEach(function (point, index) { var circle = document.createElementNS(ns, 'circle'); circle.setAttribute('cx',point[0]); circle.setAttribute('cy',point[1]); circle.style.setProperty('--x',(210-point[0])+'px'); circle.style.setProperty('--y',(210-point[1])+'px'); circle.style.animationDelay=(index*25)+'ms'; svg.appendChild(circle); });
    inner.appendChild(svg);
    var portal = document.createElement('span'); portal.className = 'vm-v3-loader__portal'; inner.appendChild(portal);
    var brand = loader.querySelector('.vm-v3-loader__brand'); var realLogo = document.querySelector('.site-brand img');
    if (brand && realLogo) { var logo = realLogo.cloneNode(true); logo.removeAttribute('loading'); logo.alt = ''; brand.textContent = ''; brand.appendChild(logo); brand.classList.add('has-real-logo'); }
  } catch (e) { remove(); return; }
  var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var duration = reduced ? 350 : 2600;
  window.setTimeout(function () { if (!loader || !document.body.contains(loader)) return; loader.classList.add('is-leaving'); window.setTimeout(function () { try { sessionStorage.setItem('vectone_intro_seen','1'); } catch (e) {} remove(); }, reduced ? 40 : 220); }, duration);
  window.setTimeout(remove, 3200);
}());
