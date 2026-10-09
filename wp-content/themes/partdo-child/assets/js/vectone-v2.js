(function () {
  'use strict';

  function ready(fn) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn, { once: true });
    } else {
      fn();
    }
  }

  function makeChoiceStrip() {
    if (!document.body.classList.contains('home')) return;
    if (document.querySelector('.vm-choice-strip')) return;

    var hero = document.querySelector('.elementor-element-7e305b1');
    var shop = document.querySelector('.shop-container');
    if (!hero && !shop) return;

    var section = document.createElement('section');
    section.className = 'vm-choice-strip vm-reveal';
    section.setAttribute('aria-label', 'Choisir une priorité de comparaison');
    section.innerHTML =
      '<div class="container vm-choice-strip__inner">' +
        '<div class="vm-choice-strip__intro">' +
          '<p class="vm-kicker">Votre usage avant le catalogue</p>' +
          '<h2>Qu’est-ce que vous refusez de sacrifier ?</h2>' +
        '</div>' +
        '<div class="vm-choice-strip__choices" role="group" aria-label="Priorités">' +
          '<button class="vm-choice" type="button" data-vm-target="price"><span class="vm-choice__index">01</span><strong>Budget</strong><small>Garder le prix sous contrôle</small></button>' +
          '<button class="vm-choice" type="button" data-vm-target="data"><span class="vm-choice__index">02</span><strong>Gigas</strong><small>Ne jamais manquer de data</small></button>' +
          '<button class="vm-choice" type="button" data-vm-target="5g"><span class="vm-choice__index">03</span><strong>5G</strong><small>Priorité au réseau rapide</small></button>' +
          '<button class="vm-choice" type="button" data-vm-target="international"><span class="vm-choice__index">04</span><strong>International</strong><small>Rester joignable au-delà de la France</small></button>' +
        '</div>' +
      '</div>';

    if (hero && hero.parentNode) {
      hero.parentNode.insertBefore(section, hero.nextSibling);
    } else if (shop && shop.parentNode) {
      shop.parentNode.insertBefore(section, shop);
    }
  }

  function findFilter(kind) {
    var selectors = [
      '.filtered-sidebar',
      '.filter-holder',
      '#klb-attribute-filter',
      '.widget_search_filter'
    ];
    var root = document.querySelector(selectors.join(','));
    if (!root) return null;

    var needles = {
      price: ['prix', 'price'],
      data: ['data', 'giga', 'go'],
      '5g': ['5g'],
      international: ['international', 'internationaux']
    }[kind] || [];

    var nodes = root.querySelectorAll('select, input, label, .widget, .filter, .attribute, div');
    for (var i = 0; i < nodes.length; i++) {
      var text = (nodes[i].textContent || '').toLowerCase();
      var name = ((nodes[i].getAttribute && (nodes[i].getAttribute('name') || nodes[i].getAttribute('id') || '')) || '').toLowerCase();
      for (var j = 0; j < needles.length; j++) {
        if (text.indexOf(needles[j]) !== -1 || name.indexOf(needles[j]) !== -1) return nodes[i];
      }
    }
    return root;
  }

  function wireChoices() {
    document.querySelectorAll('.vm-choice').forEach(function (button) {
      button.addEventListener('click', function () {
        document.querySelectorAll('.vm-choice').forEach(function (item) {
          item.classList.remove('is-active');
          item.setAttribute('aria-pressed', 'false');
        });
        button.classList.add('is-active');
        button.setAttribute('aria-pressed', 'true');

        var target = findFilter(button.getAttribute('data-vm-target'));
        if (!target) return;
        var focusable = target.matches && target.matches('select,input,button,a') ? target : target.querySelector && target.querySelector('select,input,button,a');
        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        target.style.transition = 'box-shadow 180ms ease';
        target.style.boxShadow = '0 0 0 4px rgba(217,255,67,.9)';
        window.setTimeout(function () { target.style.boxShadow = ''; }, 1100);
        if (focusable && focusable.focus) window.setTimeout(function () { focusable.focus({ preventScroll: true }); }, 450);
      });
    });
  }

  function enhanceProductCards() {
    document.querySelectorAll('ul.products li.product').forEach(function (card) {
      if (card.classList.contains('vm-enhanced')) return;
      var title = card.querySelector('.product-title');
      var content = card.querySelector('.content-wrapper');
      if (!title || !content) return;

      var text = (title.textContent || '').replace(/\s+/g, ' ').trim();
      var match = text.match(/(\d+(?:[.,]\d+)?)\s*(Go|GB|Mo|MB)/i);
      if (match) {
        var data = document.createElement('div');
        data.className = 'vm-data-display';
        data.textContent = match[1] + ' ' + match[2].toUpperCase().replace('GB', 'GO');
        content.insertBefore(data, title);
      }
      card.classList.add('vm-enhanced');
    });
  }

  function repairBrokenImages() {
    document.querySelectorAll('img').forEach(function (img) {
      function mark() {
        if (img.complete && img.naturalWidth === 0) {
          img.classList.add('vm-broken-image');
          img.style.visibility = 'hidden';
        }
      }
      img.addEventListener('error', mark, { once: true });
      mark();
    });
  }

  function reveal() {
    var items = document.querySelectorAll('.vm-reveal');
    if (!('IntersectionObserver' in window)) {
      items.forEach(function (el) { el.classList.add('is-visible'); });
      return;
    }
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    items.forEach(function (el) { observer.observe(el); });
  }

  ready(function () {
    makeChoiceStrip();
    wireChoices();
    enhanceProductCards();
    repairBrokenImages();
    reveal();
  });
}());
