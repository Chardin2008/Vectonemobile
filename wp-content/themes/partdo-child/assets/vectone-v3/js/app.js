(function () {
  'use strict';

  var cfg = window.VectoneV3 || {};
  var qs = function (s, root) { return (root || document).querySelector(s); };
  var qsa = function (s, root) { return Array.prototype.slice.call((root || document).querySelectorAll(s)); };
  var clean = function (s) { return (s || '').replace(/\s+/g, ' ').trim(); };

  function closeCategoryMenu() {
    qsa('.site-header .dropdown-menu.collapse.show').forEach(function (el) { el.classList.remove('show'); });
    qsa('.site-header .header-nav [aria-expanded="true"]').forEach(function (el) { el.setAttribute('aria-expanded', 'false'); });
  }


  function relocateHero(heroWidget) {
    var shop = qs('.shop-container.shop-page-wrapper, .shop-container');
    if (!heroWidget || !shop || !shop.parentNode) return heroWidget;
    if (heroWidget.classList.contains('vm-v3-hero-relocated')) return heroWidget;
    if (heroWidget.parentElement !== shop.parentElement) {
      try { shop.parentNode.insertBefore(heroWidget, shop); } catch (e) {}
    }
    heroWidget.classList.add('vm-v3-hero-relocated');
    return heroWidget;
  }

  function ensureHeroMedia() {
    var hero = qs('.elementor-element-7e305b1 .klbth-banner');
    if (!hero) return;
    var wrapper = qs('.entry-wrapper', hero) || hero;
    var media = qs('.entry-media', wrapper);
    var existingImg = media && qs('img', media);
    if (media && existingImg) {
      existingImg.addEventListener('error', function () {
        if (cfg.heroFallback) existingImg.src = cfg.heroFallback;
      }, { once: true });
      return;
    }
    if (!cfg.heroFallback) return;
    media = document.createElement('div');
    media.className = 'vm-v3-fallback-media';
    var img = document.createElement('img');
    img.src = cfg.heroFallback;
    img.alt = '';
    img.loading = 'eager';
    img.decoding = 'async';
    media.appendChild(img);
    wrapper.appendChild(media);
  }

  function productRoots() {
    var roots = qsa('.products .product-wrapper, .products li.product, .shop-products .product-wrapper');
    var seen = new Set();
    return roots.filter(function (el) {
      if (!el || el.offsetParent === null) return false;
      var key = clean(el.textContent).slice(0, 100);
      if (!key || seen.has(key)) return false;
      seen.add(key); return true;
    });
  }

  var operatorsCatalog = [
    ['La Poste Mobile', /la\s+poste\s+mobile/i],
    ['Bouygues Télécom', /bouygues/i],
    ['Orange', /\borange\b/i],
    ['CDiscount Mobile', /cdiscount/i],
    ['Prixtel', /prixtel/i],
    ['Free', /\bfree\b/i],
    ['SFR', /\bsfr\b|\bred\b/i],
    ['Sosh', /sosh/i]
  ];

  function detectOperators() {
    var hay = productRoots().map(function (el) { return clean(el.textContent); }).join(' | ');
    var results = [];
    operatorsCatalog.forEach(function (item) { if (item[1].test(hay)) results.push(item[0]); });
    return results.slice(0, 8);
  }

  function section(tag, className) {
    var el = document.createElement(tag || 'section');
    el.className = 'vm-v3-section ' + className;
    return el;
  }

  function insertAfter(node, ref) {
    if (!ref || !ref.parentNode) return;
    ref.parentNode.insertBefore(node, ref.nextSibling);
  }

  function buildOperators(heroWidget) {
    if (qs('.vm-v3-operators')) return qs('.vm-v3-operators');
    var ops = detectOperators();
    if (!ops.length) return null;
    var s = section('section', 'vm-v3-operators');
    s.setAttribute('aria-label', 'Opérateurs disponibles');
    var intro = document.createElement('div'); intro.className = 'vm-v3-operators__intro';
    intro.innerHTML = '<div class="vm-v3-eyebrow">Des offres, pas un seul opérateur.</div><p>Comparez plusieurs réseaux depuis un seul endroit.</p>';
    var rail = document.createElement('div'); rail.className = 'vm-v3-operators__rail';
    ops.forEach(function (name) { var x = document.createElement('div'); x.className = 'vm-v3-operator'; x.textContent = name; rail.appendChild(x); });
    s.appendChild(intro); s.appendChild(rail); insertAfter(s, heroWidget); return s;
  }

  function filterTarget(label) {
    var candidates = qsa('.filtered-sidebar .widget, .sidebar-inner .widget, .filtered-sidebar, .sidebar-inner');
    var needle = label.toLowerCase();
    var hit = candidates.find(function (el) { return clean(el.textContent).toLowerCase().indexOf(needle) !== -1; });
    if (!hit) hit = qs('.filtered-sidebar, .sidebar-inner, .shop-container');
    if (hit) {
      hit.scrollIntoView({ behavior: 'smooth', block: 'start' });
      var toggle = qsa('button, .widget-title, .filter-title', hit).find(function (el) { return clean(el.textContent).toLowerCase().indexOf(needle) !== -1; });
      if (toggle && toggle.getAttribute('aria-expanded') === 'false') toggle.click();
    }
  }

  function buildNeeds(afterNode) {
    if (qs('.vm-v3-needs')) return qs('.vm-v3-needs');
    var s = section('section', 'vm-v3-needs');
    s.innerHTML = '<div class="vm-v3-needs__lead"><div class="vm-v3-eyebrow">01 &nbsp; Commencez ici</div><h2>Qu’est-ce que vous refusez de sacrifier ?</h2><p>Pas besoin de parcourir tous les forfaits. Commencez par ce qui compte vraiment pour vous.</p></div><div class="vm-v3-needs__grid"></div>';
    var items = [
      ['01','Payer moins','Mon budget','Aller droit aux forfaits les plus accessibles','Prix'],
      ['02','Plus de liberté','Mes gigas','Streamer, travailler et partager sans compter','Data'],
      ['03','Plus rapide','La 5G','Prioriser les forfaits compatibles 5G','5G'],
      ['04','Rester connecté','L’international','Appeler et voyager au-delà de la France','International']
    ];
    var grid = qs('.vm-v3-needs__grid', s);
    items.forEach(function (it) {
      var b = document.createElement('button'); b.type = 'button'; b.className = 'vm-v3-need';
      b.innerHTML = '<span class="vm-v3-need__index">'+it[0]+'</span><span class="vm-v3-need__tag">'+it[1]+'</span><strong>'+it[2]+'</strong><small>'+it[3]+'</small><span class="vm-v3-need__arrow">→</span>';
      b.addEventListener('click', function () { filterTarget(it[4]); }); grid.appendChild(b);
    });
    insertAfter(s, afterNode); return s;
  }

  function parseProduct(el) {
    var titleEl = qs('.product-title, .entry-title, h2.woocommerce-loop-product__title, h3, h2', el);
    var priceEl = qs('.price', el);
    var linkEl = qs('a.button[href], a[href].product-button, .product-footer a[href], a[href]', el);
    if (!titleEl || !priceEl || !linkEl || !linkEl.getAttribute('href')) return null;
    var title = clean(titleEl.textContent);
    var price = priceEl ? clean(priceEl.textContent) : '';
    var attributes = qsa('.product-attributes .attribute', el).map(function (attribute) { return clean(attribute.textContent); }).filter(Boolean);
    var text = [title].concat(attributes).join(' ');
    var dataMatch = text.match(/\b(\d{1,4})\s?(Go|GB)\b/i);
    var brand = '';
    operatorsCatalog.some(function (op) { if (op[1].test(text)) { brand = op[0]; return true; } return false; });
    if (!title || !price) return null;
    try { if (!/^https?:$/.test(new URL(linkEl.getAttribute('href'), document.baseURI).protocol)) return null; } catch (e) { return null; }
    var image = qs('.thumbnail-wrapper img', el);
    var meta = attributes.slice();
    if (/\b5G\b/i.test(title) && !meta.some(function (line) { return /\b5G\b/i.test(line); })) meta.push('5G');
    return { title:title, price:price, link:linkEl, brand:brand, image:image, data:dataMatch ? (dataMatch[1]+' '+dataMatch[2]) : '', meta:meta.join(' · ') };
  }

  function buildFeatured(afterNode) {
    if (qs('.vm-v3-featured')) return qs('.vm-v3-featured');
    // WooCommerce emits .featured on product containers for featured products.
    // Only use real visible cards; prioritize that native flag, then preserve catalogue order.
    var roots = qsa('.products .product-wrapper').filter(function (el) {
      return el.getClientRects().length && getComputedStyle(el).visibility !== 'hidden';
    });
    roots.sort(function (a, b) { return Number(Boolean(b.closest('.product.featured'))) - Number(Boolean(a.closest('.product.featured'))); });
    var seen = new Set();
    var products = roots.map(parseProduct).filter(function (p) {
      if (!p) return false;
      var key = p.link.getAttribute('data-product_id') || p.link.getAttribute('href');
      if (seen.has(key)) return false;
      seen.add(key); return true;
    }).slice(0,3);
    if (!products.length) return null;
    var s = section('section', 'vm-v3-featured');
    var copy = document.createElement('div'); copy.className = 'vm-v3-featured__copy';
    copy.innerHTML = '<div class="vm-v3-eyebrow">Sélection Vectone</div><h2>Les offres du moment.</h2><p>Quelques forfaits à regarder avant de comparer le reste.</p><a class="vm-v3-featured__link" href="#vm-v3-all-offers">Voir toutes les offres →</a>';
    var grid = document.createElement('div'); grid.className = 'vm-v3-featured__grid';
    products.forEach(function (p, i) {
      var card = document.createElement('article'); card.className = 'vm-v3-offer' + (i === 0 ? ' vm-v3-offer--primary' : '');
      var content = document.createElement('div');
      if (p.image) {
        var logo = document.createElement('img'); logo.className = 'vm-v3-offer__logo';
        logo.src = p.image.getAttribute('data-src') || p.image.currentSrc || p.image.getAttribute('src');
        logo.alt = p.image.getAttribute('alt') || ''; logo.width = 64; logo.height = 48; logo.loading = 'lazy';
        content.appendChild(logo);
      }
      if (p.brand) { var brand = document.createElement('span'); brand.className = 'vm-v3-offer__brand'; brand.textContent = p.brand; content.appendChild(brand); }
      var title = document.createElement('h3'); title.textContent = p.title; content.appendChild(title);
      if (p.data) { var data = document.createElement('span'); data.className = 'vm-v3-offer__data'; data.textContent = p.data; content.appendChild(data); }
      if (p.price) { var price = document.createElement('span'); price.className = 'vm-v3-offer__price'; price.textContent = p.price; content.appendChild(price); }
      if (p.meta) { var meta = document.createElement('div'); meta.className = 'vm-v3-offer__meta'; meta.textContent = p.meta; content.appendChild(meta); }
      var cta = p.link.cloneNode(true); cta.className = 'vm-v3-offer__cta'; cta.textContent = "Voir l'offre ";
      var arrow = document.createElement('span'); arrow.textContent = '→'; cta.appendChild(arrow);
      card.appendChild(content); card.appendChild(cta);
      grid.appendChild(card);
    });
    s.appendChild(copy); s.appendChild(grid); insertAfter(s, afterNode); return s;
  }

  function buildComparatorIntro(afterNode) {
    if (qs('.vm-v3-comparator-intro')) return;
    var shop = qs('.shop-container');
    if (!shop) return;
    shop.id = shop.id || 'vm-v3-all-offers';
    var s = section('section', 'vm-v3-comparator-intro');
    s.innerHTML = '<div><div class="vm-v3-eyebrow">Comparateur</div><h2>Comparez toutes les offres.</h2><p>Affinez selon votre budget, votre data et vos usages.</p></div><div class="vm-v3-comparator-intro__actions"></div>';
    [['Budget','Prix'],['Data','Data'],['Opérateur','Opérateur'],['5G','5G']].forEach(function (x) {
      var b = document.createElement('button'); b.type='button'; b.className='vm-v3-filter-jump'; b.textContent=x[0]; b.addEventListener('click',function(){filterTarget(x[1]);}); qs('.vm-v3-comparator-intro__actions',s).appendChild(b);
    });
    shop.parentNode.insertBefore(s, shop);
  }

  // Presentation only: keep source attributes, controls and links intact.
  function polishCatalogue() {
    qsa('.shop-container .products .product-wrapper').forEach(function (card) {
      var content = qs('.content-wrapper', card);
      var title = qs('.product-title', card);
      if (!content || !title) return;
      var name = clean(title.textContent);
      var operator = name.match(/\s+[–—-]\s+([^–—]+)$/);
      if (operator && !qs('.vm-card-operator', content)) {
        var label = document.createElement('span'); label.className = 'vm-card-operator';
        label.textContent = operator[1].replace(/\.$/, ''); content.insertBefore(label, title);
      }
      var attributes = qs('.product-attributes', card);
      var data = name.match(/\b\d+(?:[.,]\d+)?\s*(?:Go|GB)\b/i);
      if (attributes && data && !qs('.vm-card-data', attributes) && !/\b\d+\s*(?:Go|GB)\b/i.test(attributes.textContent)) {
        var chip = document.createElement('span'); chip.className = 'vm-card-data';
        chip.textContent = data[0]; attributes.prepend(chip);
      }
      var footer = qs('.product-footer', card);
      var price = footer && qs('.price', footer);
      if (price && !qs('.vm-card-period', footer) && !/mois/i.test(footer.textContent)) {
        var period = document.createElement('small'); period.className = 'vm-card-period'; period.textContent = '/mois';
        price.insertAdjacentElement('afterend', period);
      }
    });
    qsa('.filtered-sidebar .widget_search_filter .widget-title').forEach(function (title) { title.textContent = 'Affinez votre recherche'; });
    qsa('.filtered-sidebar .widget_price_filter button[type="submit"]').forEach(function (button) { button.textContent = 'Appliquer'; });
  }

  function init() {
    if (!document.body.classList.contains('home')) return;
    if (document.body.classList.contains('vm-v3-app-ready')) return;
    document.body.classList.add('vm-v3-app-ready');
    try { closeCategoryMenu(); } catch (e) {}
    try { ensureHeroMedia(); } catch (e) {}
    var heroWidget = qs('.elementor-element-7e305b1');
    if (!heroWidget) return;
    try { relocateHero(heroWidget); } catch (e) {}
    var ops = heroWidget;
    try { ops = buildOperators(heroWidget) || heroWidget; } catch (e) {}
    var needs = ops;
    try { needs = buildNeeds(ops) || ops; } catch (e) {}
    var featured = needs;
    try { featured = buildFeatured(needs) || needs; } catch (e) {}
    try { buildComparatorIntro(featured); } catch (e) {}
    try { polishCatalogue(); } catch (e) {}
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
}());
