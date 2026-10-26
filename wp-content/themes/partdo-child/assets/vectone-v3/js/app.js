(function () {
  'use strict';

  var cfg = window.VectoneV3 || {};
  var siteContent = cfg.siteContent || {};
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
    var selectedHero = typeof cfg.heroImageUrl === 'string' && cfg.heroImageUrl.trim() ? cfg.heroImageUrl.trim() : '';
    if (media && existingImg) {
      if (selectedHero && existingImg.src !== selectedHero) existingImg.src = selectedHero;
      existingImg.addEventListener('error', function () {
        var fallback = cfg.heroFallback || cfg.heroSafetyFallback;
        if (fallback && existingImg.src !== fallback) existingImg.src = fallback;
      }, { once: true });
      return;
    }
    var heroSource = selectedHero || cfg.heroFallback || cfg.heroSafetyFallback;
    if (!heroSource) return;
    media = document.createElement('div');
    media.className = 'vm-v3-fallback-media';
    var img = document.createElement('img');
    img.src = heroSource;
    img.alt = '';
    img.loading = 'eager';
    img.decoding = 'async';
    media.appendChild(img);
    img.addEventListener('error', function () {
      var fallback = cfg.heroFallback || cfg.heroSafetyFallback;
      if (fallback && img.src !== fallback) img.src = fallback;
    }, { once: true });
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

  function buildNetworkSignal(afterNode) {
    if (qs('.vm-v3-network-signal') || !afterNode) return null;
    var s = section('section', 'vm-v3-network-signal');
    s.setAttribute('aria-labelledby', 'vm-v3-network-signal-title');
    s.innerHTML = '<div class="vm-v3-network-signal__visual" aria-hidden="true"><span class="vm-v3-network-signal__orbit vm-v3-network-signal__orbit--one"></span><span class="vm-v3-network-signal__orbit vm-v3-network-signal__orbit--two"></span><span class="vm-v3-network-signal__node vm-v3-network-signal__node--one"></span><span class="vm-v3-network-signal__node vm-v3-network-signal__node--two"></span><span class="vm-v3-network-signal__node vm-v3-network-signal__node--three"></span><span class="vm-v3-network-signal__core"></span></div><div class="vm-v3-network-signal__copy"><div class="vm-v3-eyebrow">Le réseau en clair</div><h2 id="vm-v3-network-signal-title">Plusieurs réseaux.<br><em>Un choix plus simple.</em></h2><p>Comparez les offres disponibles au même endroit et choisissez selon vos usages, votre budget et votre besoin de liberté.</p><div class="vm-v3-network-signal__steps"><span><b>01</b> Explorer</span><span><b>02</b> Comparer</span><span><b>03</b> Choisir</span></div></div>';
    insertAfter(s, afterNode); return s;
  }

  function buildNetworkImpact(heroWidget) {
    var media = qs('.klbth-banner .entry-media', heroWidget);
    if (!media) return null;
    media.classList.add('vm-v3-network-impact-media');
    if (qs('.vm-v3-network-impact-copy', media)) return media;
    var visual = siteContent.second_visual || {};
    var copy = document.createElement('div');
    copy.className = 'vm-v3-network-impact-copy';
    var eyebrow = document.createElement('span');
    eyebrow.className = 'vm-v3-network-impact-eyebrow';
    eyebrow.textContent = visual.eyebrow || 'PLUS DE CHOIX, PLUS DE LIBERTÉ';
    var title = document.createElement('h2');
    title.className = 'vm-v3-network-impact-title';
    title.textContent = visual.title || 'Comparez plusieurs réseaux. Choisissez celui qui vous ressemble.';
    var text = document.createElement('p');
    text.className = 'vm-v3-network-impact-text';
    text.textContent = visual.description || 'Explorez plusieurs opérateurs et trouvez l’offre adaptée à vos usages depuis un seul endroit.';
    if (visual.image) { var image = qs('img', media); if (image) image.src = visual.image; }
    if (visual.show_cta && visual.cta_text && visual.cta_url) { var cta = document.createElement('a'); cta.className = 'vm-v3-network-impact-cta'; cta.href = visual.cta_url; cta.textContent = visual.cta_text + ' →'; copy.appendChild(cta); }
    copy.appendChild(eyebrow); copy.appendChild(title); copy.appendChild(text);
    media.appendChild(copy);
    return media;
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
    if (Array.isArray(siteContent.guide && siteContent.guide.profiles) && siteContent.guide.profiles.length) items = siteContent.guide.profiles.filter(function (x) { return x && (x.title || x.description); }).slice(0, 5).map(function (x) { return ['GUIDE', x.title || '', (x.description || '') + (x.details ? ' ' + x.details : ''), []]; });
    else if (Array.isArray(siteContent.faq) && siteContent.faq.length) items = siteContent.faq.filter(function (x) { return x && Number(x.active) !== 0 && x.question; }).sort(function (a, b) { return Number(a.order || 0) - Number(b.order || 0); }).slice(0, 5).map(function (x) { return ['GUIDE', x.question, x.answer || '', []]; });
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

  function buildEditorialMagazine(afterNode) {
    if (qs('.vm-v3-editorial-magazine')) return qs('.vm-v3-editorial-magazine');
    var posts = qsa('.blog-posts .post').map(function (post) {
      var link = qs('.entry-title a, .entry-media > a', post);
      var image = qs('.entry-media img', post);
      var title = qs('.entry-title a', post);
      var excerpt = qs('.entry-excerpt p', post);
      var category = qs('.entry-categories a', post);
      var date = qs('.entry-published a', post);
      if (!link || !title || !image) return null;
      return { href: link.href, title: clean(title.textContent), excerpt: excerpt ? clean(excerpt.textContent) : '', category: category ? clean(category.textContent) : 'Actualités', date: date ? clean(date.textContent) : '', image: image.getAttribute('data-src') || image.currentSrc || image.src };
    }).filter(Boolean).slice(0, 3);
    if (posts.length < 3) return null;
    var newsImages = [
      { match: /forfaits free 2025/i, url: cfg.newsFree },
      { match: /forfaits sosh/i, url: cfg.newsSosh },
      { match: /fin de vectone/i, url: cfg.newsInternational }
    ];
    posts.forEach(function (post) {
      var mapped = newsImages.find(function (item) { return item.url && item.match.test(post.title); });
      if (mapped) post.image = mapped.url;
    });
    var s = section('section', 'vm-v3-editorial-magazine');
    s.setAttribute('aria-labelledby', 'vm-v3-editorial-title');
    s.innerHTML = '<div class="vm-v3-editorial-magazine__head"><div><div class="vm-v3-eyebrow">Actualités • Guides • Bons plans</div><div class="vm-v3-editorial-magazine__edition">Magazine <strong>01</strong></div><h2 id="vm-v3-editorial-title">Ce qu’il faut savoir<br>avant de choisir votre prochain forfait.</h2><p>Décryptages, comparatifs et conseils pour mieux comprendre les offres mobiles et faire les bons choix.</p></div><a href="/actualites/">Voir toutes les actualités <span aria-hidden="true">→</span></a></div><div class="vm-v3-editorial-magazine__grid"></div>';
    var grid = qs('.vm-v3-editorial-magazine__grid', s);
    posts.forEach(function (post, index) {
      var card = document.createElement('article'); card.className = 'vm-v3-editorial-card' + (index === 0 ? ' vm-v3-editorial-card--lead' : '');
      card.innerHTML = '<a class="vm-v3-editorial-card__image" href="' + post.href + '"><img loading="lazy" src="' + post.image + '" alt=""></a><div class="vm-v3-editorial-card__body"><span class="vm-v3-editorial-card__badge">' + (index === 0 ? 'À la une' : post.category) + '</span><div class="vm-v3-editorial-card__meta">' + post.category + (post.date ? ' · ' + post.date : '') + '</div><h3><a href="' + post.href + '">' + post.title + '</a></h3><p>' + post.excerpt + '</p><a class="vm-v3-editorial-card__link" href="' + post.href + '">Lire l’article <span aria-hidden="true">→</span></a></div>';
      grid.appendChild(card);
    });
    insertAfter(s, afterNode); return s;
  }

  function buildReviewsTrust() {
    if (qs('.vm-v3-reviews-trust')) return qs('.vm-v3-reviews-trust');
    var widget = qs('.elementor-element-371471e');
    if (!widget) return null;
    var source = qsa('.slider-item.testimonial', widget).map(function (item) {
      var name = qs('.customer-name', item), text = qs('.customer-description p', item), avatar = qs('.customer-avatar img', item), stars = qsa('.stars i', item);
      if (!name || !text) return null;
      return { name: clean(name.textContent), text: clean(text.textContent), stars: stars.length || 0, avatar: avatar ? (avatar.getAttribute('data-src') || avatar.src) : '' };
    }).filter(Boolean);
    if (!source.length) return null;
    var totalStars = source.reduce(function (sum, item) { return sum + item.stars; }, 0);
    var average = totalStars && source.length ? (totalStars / source.length).toFixed(1) : '';
    var s = section('section', 'vm-v3-reviews-trust');
    s.setAttribute('aria-labelledby', 'vm-v3-reviews-title');
    s.innerHTML = '<div class="vm-v3-reviews-trust__head"><div class="vm-v3-eyebrow">Ils nous font confiance</div><h2 id="vm-v3-reviews-title">Ce que pensent nos utilisateurs</h2><p>Des retours d’expérience actuellement publiés sur Vectone.</p></div><div class="vm-v3-reviews-trust__layout"><aside class="vm-v3-reviews-trust__summary"><span>Avis clients</span><strong>' + (average || '—') + '</strong><small>/ 5</small><div class="vm-v3-reviews-trust__stars" aria-label="Note moyenne ' + (average || 'indisponible') + ' sur 5">★★★★★</div><p>' + source.length + ' avis publiés</p></aside><div class="vm-v3-reviews-trust__grid"></div></div>';
    var grid = qs('.vm-v3-reviews-trust__grid', s);
    source.forEach(function (item, index) {
      var initials = item.name.split(/\s+/).map(function (part) { return part.charAt(0); }).join('').slice(0, 2).toUpperCase();
      var card = document.createElement('article'); card.className = 'vm-v3-review-card' + (index === 0 ? ' vm-v3-review-card--featured' : '');
      var avatarHtml = item.avatar ? '<img src="' + item.avatar + '" alt="Avatar de ' + item.name + '"><span aria-hidden="true">' + initials + '</span>' : '<span aria-hidden="true">' + initials + '</span>';
      card.innerHTML = '<div class="vm-v3-review-card__top"><div class="vm-v3-review-card__avatar">' + avatarHtml + '</div><div><h3>' + item.name + '</h3><div class="vm-v3-review-card__stars" aria-label="' + item.stars + ' étoiles">' + '★'.repeat(item.stars) + '</div><small>Avis client</small></div></div><p class="vm-v3-review-card__text">' + item.text + '</p>';
      grid.appendChild(card);
      var img = qs('img', card); if (img) img.addEventListener('error', function () { img.hidden = true; }, { once: true });
    });
    widget.setAttribute('aria-hidden', 'true'); widget.classList.add('vm-v3-reviews-source-hidden');
    insertAfter(s, widget); return s;
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

  function buildVectoneConcierge() {
    var source = qs('.elementor-element-1a54d13');
    if (!source || qs('.vm-v3-concierge')) return;
    source.setAttribute('aria-hidden', 'true'); source.classList.add('vm-v3-news-duplicate-hidden');
    var compareUrl = window.VectoneCompareHub && VectoneCompareHub.all ? VectoneCompareHub.all : '';
    var contactUrl = cfg.contactUrl || '';
    var items = [
      ['Combien de data me faut-il ?', 'Le bon volume dépend surtout de vos usages : navigation, réseaux sociaux, musique, vidéo et partage de connexion.', ['Navigation & messages', 'Streaming', 'Partage de connexion']],
      ['Avec ou sans engagement ?', 'Un forfait sans engagement offre davantage de souplesse pour changer d’offre. Un forfait avec engagement implique une durée définie à vérifier avant de souscrire.', ['Souplesse', 'Durée définie', 'Conditions du contrat']],
      ['Comment comparer les réseaux ?', 'La qualité dépend notamment de la couverture, de votre zone géographique et du réseau utilisé par l’opérateur.', ['Couverture', 'Zone géographique', 'Réseau utilisé']],
      ['Quel forfait pour l’international ?', 'Vérifiez les destinations incluses, les appels, les SMS, la data et les conditions de roaming avant de choisir.', ['Destinations', 'Appels & SMS', 'Roaming']],
      ['Puis-je garder mon numéro ?', 'En France, la portabilité permet généralement de conserver son numéro lors d’un changement d’opérateur.', ['Portabilité', 'Changement d’opérateur', 'Conserver son numéro']]
    ];
    if (Array.isArray(siteContent.guide && siteContent.guide.profiles) && siteContent.guide.profiles.length) items = siteContent.guide.profiles.filter(function (x) { return x && (x.title || x.description); }).slice(0, 5).map(function (x) { return ['GUIDE', x.title || '', (x.description || '') + (x.details ? ' ' + x.details : ''), []]; });
    else if (Array.isArray(siteContent.faq) && siteContent.faq.length) items = siteContent.faq.filter(function (x) { return x && Number(x.active) !== 0 && x.question; }).sort(function (a, b) { return Number(a.order || 0) - Number(b.order || 0); }).slice(0, 5).map(function (x) { return ['GUIDE', x.question, x.answer || '', []]; });
    var s = section('section', 'vm-v3-concierge'); s.setAttribute('aria-labelledby', 'vm-v3-concierge-title');
    s.innerHTML = '<div class="vm-v3-concierge__intro"><div class="vm-v3-eyebrow">Vectone / Conseil</div><h2 id="vm-v3-concierge-title">Bien choisir commence<br>par les bonnes questions.</h2><p>Data, engagement, réseau ou international : explorez les points essentiels avant de comparer les offres.</p></div><div class="vm-v3-concierge__module"><nav class="vm-v3-concierge__nav" aria-label="Questions de conseil"></nav><div class="vm-v3-concierge__answer" aria-live="polite"></div></div><div class="vm-v3-concierge__cta"><div><div class="vm-v3-eyebrow">Prêt à comparer ?</div><h3>Trouvez l’offre adaptée à vos usages.</h3></div><div class="vm-v3-concierge__actions"></div></div>';
    var nav = qs('.vm-v3-concierge__nav', s), answer = qs('.vm-v3-concierge__answer', s);
    function renderAnswer(index) { var item = items[index]; answer.innerHTML = '<span class="vm-v3-concierge__answer-count">Question ' + ('0' + (index + 1)).slice(-2) + ' / 05</span><span class="vm-v3-concierge__answer-ghost" aria-hidden="true">0' + (index + 1) + '</span><h3>' + item[0] + '</h3><p>' + item[1] + '</p><div class="vm-v3-concierge__markers">' + item[2].map(function (marker) { return '<span>' + marker + '</span>'; }).join('') + '</div>'; }
    items.forEach(function (item, index) { var button = document.createElement('button'); button.type = 'button'; button.className = 'vm-v3-concierge__nav-item' + (index === 0 ? ' is-active' : ''); button.setAttribute('aria-selected', index === 0 ? 'true' : 'false'); button.innerHTML = '<span>0' + (index + 1) + '</span><strong>' + item[0] + '</strong>'; button.addEventListener('click', function () { qsa('.vm-v3-concierge__nav-item', nav).forEach(function (other) { other.classList.remove('is-active'); other.setAttribute('aria-selected', 'false'); }); button.classList.add('is-active'); button.setAttribute('aria-selected', 'true'); renderAnswer(index); }); nav.appendChild(button); });
    renderAnswer(0);
    var actions = qs('.vm-v3-concierge__actions', s); if (compareUrl) actions.innerHTML += '<a class="vm-v3-concierge__primary" href="' + compareUrl + '">Comparer les forfaits <span aria-hidden="true">→</span></a>'; if (contactUrl) actions.innerHTML += '<a class="vm-v3-concierge__secondary" href="' + contactUrl + '">Nous contacter</a>';
    source.parentNode.insertBefore(s, source.nextSibling);
  }

  function buildEditorialGuide() {
    var source = qs('.elementor-element-1a54d13');
    if (!source || qs('.vm-v3-editorial-guide')) return;
    source.setAttribute('aria-hidden', 'true'); source.classList.add('vm-v3-news-duplicate-hidden');
    var compareUrl = window.VectoneCompareHub && VectoneCompareHub.all ? VectoneCompareHub.all : '';
    var contactUrl = cfg.contactUrl || '';
    var items = [
      ['DATA', 'Combien de data me faut-il vraiment ?', 'Le bon volume depend surtout de vos usages : navigation, reseaux sociaux, musique, video et partage de connexion.', ['NAVIGATION & MESSAGES', 'STREAMING', 'PARTAGE DE CONNEXION']],
      ['ENGAGEMENT', 'Avec ou sans engagement ?', 'Un forfait sans engagement offre davantage de souplesse pour changer d’offre. Un forfait avec engagement implique une duree definie a verifier avant de souscrire.', ['FLEXIBILITE', 'DUREE', 'RESILIATION']],
      ['RESEAU', 'Comment comparer les reseaux ?', 'La qualite depend notamment de la couverture, de votre zone geographique et du reseau utilise par l’operateur.', ['COUVERTURE', 'LOCALISATION', 'TECHNOLOGIE RESEAU']],
      ['INTERNATIONAL', 'Quel forfait pour l’international ?', 'Avant de choisir, verifiez les destinations incluses, les appels, les SMS, la data et les conditions de roaming.', ['APPELS', 'DATA', 'ROAMING']],
      ['PORTABILITE', 'Puis-je garder mon numero ?', 'En France, la portabilite permet generalement de conserver son numero lors d’un changement d’operateur.', ['RIO', 'NUMERO', 'PORTABILITE']]
    ];
    var guide = siteContent.guide || {};
    var s = section('section', 'vm-v3-editorial-guide'); s.setAttribute('aria-labelledby', 'vm-v3-editorial-guide-title');
    s.innerHTML = '<div class="vm-v3-editorial-guide__head"><div class="vm-v3-eyebrow">Le guide Vectone</div><h2 id="vm-v3-editorial-guide-title">5 questions a se poser<br>avant de choisir son forfait.</h2><p>Data, engagement, reseau, international et portabilite : les points essentiels a comprendre avant de comparer.</p></div><nav class="vm-v3-editorial-guide__tabs" aria-label="Questions du guide"></nav><div class="vm-v3-editorial-guide__panel" aria-live="polite"></div><div class="vm-v3-editorial-guide__footer"><span>Vous savez ce qu’il vous faut ?</span><div></div></div>';
    var tabs = qs('.vm-v3-editorial-guide__tabs', s), panel = qs('.vm-v3-editorial-guide__panel', s), footer = qs('.vm-v3-editorial-guide__footer div', s);
    if (guide.eyebrow) qs('.vm-v3-editorial-guide__head .vm-v3-eyebrow', s).textContent = guide.eyebrow;
    if (guide.title) qs('.vm-v3-editorial-guide__head h2', s).textContent = guide.title;
    if (guide.description) qs('.vm-v3-editorial-guide__head p', s).textContent = guide.description;
    function render(index) { var item = items[index]; panel.innerHTML = '<div class="vm-v3-editorial-guide__question"><span>0' + (index + 1) + ' / 05</span><h3>' + item[1] + '</h3></div><div class="vm-v3-editorial-guide__response"><span class="vm-v3-editorial-guide__ghost" aria-hidden="true">0' + (index + 1) + '</span><p>' + item[2] + '</p><div class="vm-v3-editorial-guide__markers">' + item[3].map(function (x) { return '<span>' + x + '</span>'; }).join('') + '</div></div>'; }
    items.forEach(function (item, index) { var b = document.createElement('button'); b.type = 'button'; b.className = 'vm-v3-editorial-guide__tab' + (index === 0 ? ' is-active' : ''); b.setAttribute('aria-selected', index === 0 ? 'true' : 'false'); b.setAttribute('aria-controls', 'vm-v3-editorial-guide-panel'); b.innerHTML = '<span>0' + (index + 1) + '</span><strong>' + item[0] + '</strong>'; b.addEventListener('click', function () { qsa('.vm-v3-editorial-guide__tab', tabs).forEach(function (x) { x.classList.remove('is-active'); x.setAttribute('aria-selected', 'false'); }); b.classList.add('is-active'); b.setAttribute('aria-selected', 'true'); render(index); }); tabs.appendChild(b); });
    panel.id = 'vm-v3-editorial-guide-panel'; render(0);
    if (compareUrl) footer.innerHTML += '<a href="' + compareUrl + '">Comparer les forfaits <span aria-hidden="true">→</span></a>'; if (contactUrl) footer.innerHTML += '<a class="vm-v3-editorial-guide__contact" href="' + contactUrl + '">Nous contacter</a>';
    source.parentNode.insertBefore(s, source.nextSibling);
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
    try { buildNetworkSignal(ops); } catch (e) {}
    // The homepage needs/guide block was intentionally removed from the frontend.
    // Keep the following pipeline attached to the existing network/hero node so
    // the next sections close up naturally without leaving a layout gap.
    var needs = qs('.vm-v3-network-signal') || ops;
    var featured = needs;
    try { featured = buildFeatured(needs) || needs; } catch (e) {}
    var editorial = featured;
    try { editorial = buildEditorialMagazine(featured) || featured; } catch (e) {}
    try { buildComparatorIntro(editorial); } catch (e) {}
    try { buildReviewsTrust(); } catch (e) {}
    try { buildEditorialGuide(); } catch (e) {}
    try { polishCatalogue(); } catch (e) {}
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
}());
