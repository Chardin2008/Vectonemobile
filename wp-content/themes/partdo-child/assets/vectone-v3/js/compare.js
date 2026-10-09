(function () {
  'use strict';

  var maxSelected = 3;
  var selected = [];
  var previousBodyOverflow = '';

  function textOf(element) {
    return element ? element.textContent.replace(/\s+/g, ' ').trim() : '';
  }

  function products() {
    return Array.prototype.slice.call(document.querySelectorAll('body .products .product-wrapper')).filter(function (product) {
      return product.offsetParent !== null;
    });
  }

  function realLink(product) {
    return product.querySelector('.product-footer a[href], .product-title a[href], .product-thumbnail[href]');
  }

  function productData(product) {
    var title = product.querySelector('.product-title');
    var image = product.querySelector('.thumbnail-wrapper img');
    var dataMatch = textOf(product).match(/(\d+(?:[.,]\d+)?)\s*(Go|GB)\b/i);
    return {
      title: textOf(title),
      price: textOf(product.querySelector('.price')),
      image: image ? (image.getAttribute('data-src') || image.currentSrc || image.getAttribute('src') || '') : '',
      attributes: Array.prototype.slice.call(product.querySelectorAll('.product-attributes .attribute')).map(textOf).filter(Boolean),
      data: dataMatch ? dataMatch[1].replace(',', '.') + ' ' + dataMatch[2].toUpperCase() : '',
      link: realLink(product)
    };
  }

  function updateBar() {
    var bar = document.querySelector('.vectone-compare-bar');
    if (!bar) return;
    var count = bar.querySelector('[data-compare-count]');
    var open = bar.querySelector('[data-compare-open]');
    if (count) count.textContent = selected.length + ' / ' + maxSelected;
    if (open) open.disabled = selected.length < 2;
    bar.hidden = selected.length === 0;
  }

  function closeModal() {
    var modal = document.querySelector('.vectone-compare-modal');
    if (!modal) return;
    modal.hidden = true;
    document.body.style.overflow = previousBodyOverflow;
  }

  function addText(parent, tag, className, value) {
    if (!value) return;
    var element = document.createElement(tag);
    element.className = className;
    element.textContent = value;
    parent.appendChild(element);
  }

  function renderModal() {
    var modal = document.querySelector('.vectone-compare-modal');
    var grid = modal && modal.querySelector('.vectone-compare-modal__grid');
    if (!modal || !grid) return;
    grid.textContent = '';
    selected.forEach(function (product) {
      var data = productData(product);
      var item = document.createElement('article');
      item.className = 'vectone-compare-modal__item';
      if (data.image) {
        var image = document.createElement('img');
        image.className = 'vectone-compare-modal__logo';
        image.src = data.image;
        image.alt = data.title;
        item.appendChild(image);
      }
      addText(item, 'h3', 'vectone-compare-modal__title', data.title);
      addText(item, 'strong', 'vectone-compare-modal__data', data.data);
      addText(item, 'p', 'vectone-compare-modal__price', data.price);
      data.attributes.forEach(function (attribute) { addText(item, 'p', 'vectone-compare-modal__attribute', attribute); });
      if (/\b5G\b/i.test(data.attributes.join(' ') + ' ' + data.title)) addText(item, 'p', 'vectone-compare-modal__attribute', '5G');
      if (data.link && data.link.getAttribute('href')) {
        var link = data.link.cloneNode(true);
        link.className = 'vectone-compare-modal__cta';
        link.textContent = "Voir l'offre →";
        item.appendChild(link);
      }
      grid.appendChild(item);
    });
    modal.hidden = false;
    previousBodyOverflow = document.body.style.overflow;
    document.body.style.overflow = 'hidden';
  }

  function createModal() {
    if (document.querySelector('.vectone-compare-modal')) return;
    var modal = document.createElement('div');
    modal.className = 'vectone-compare-modal';
    modal.hidden = true;
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-modal', 'true');
    modal.setAttribute('aria-labelledby', 'vectone-compare-title');
    var backdrop = document.createElement('div');
    backdrop.className = 'vectone-compare-modal__backdrop';
    var panel = document.createElement('section');
    panel.className = 'vectone-compare-modal__panel';
    var close = document.createElement('button');
    close.type = 'button';
    close.className = 'vectone-compare-modal__close';
    close.setAttribute('aria-label', 'Fermer');
    close.textContent = '×';
    close.addEventListener('click', closeModal);
    var eyebrow = document.createElement('p');
    eyebrow.className = 'vectone-compare-modal__eyebrow';
    eyebrow.textContent = 'Comparateur';
    var title = document.createElement('h2');
    title.id = 'vectone-compare-title';
    title.textContent = 'Comparez vos choix';
    var subtitle = document.createElement('p');
    subtitle.className = 'vectone-compare-modal__subtitle';
    subtitle.textContent = 'Les différences essentielles, côte à côte.';
    var grid = document.createElement('div');
    grid.className = 'vectone-compare-modal__grid';
    panel.appendChild(close); panel.appendChild(eyebrow); panel.appendChild(title); panel.appendChild(subtitle); panel.appendChild(grid);
    modal.appendChild(backdrop); modal.appendChild(panel); document.body.appendChild(modal);
    backdrop.addEventListener('click', closeModal);
  }

  function toggle(product, button) {
    var index = selected.indexOf(product);
    if (index !== -1) {
      selected.splice(index, 1); product.classList.remove('is-compare-selected'); button.classList.remove('is-added'); button.textContent = '+ Comparer'; updateBar(); return;
    }
    if (selected.length >= maxSelected) return;
    selected.push(product); product.classList.add('is-compare-selected'); button.classList.add('is-added'); button.textContent = '✓ Ajouté'; updateBar();
  }

  function setup() {
    var list = products();
    if (!list.length || document.querySelector('.vectone-compare-bar')) return;
    list.forEach(function (product) {
      var footer = product.querySelector('.product-footer-inner, .product-footer');
      if (!footer || !product.querySelector('.product-title') || !product.querySelector('.price') || !realLink(product)) return;
      if (footer.querySelector('.vectone-compare-button')) return;
      var button = document.createElement('button');
      button.type = 'button'; button.className = 'vectone-compare-button'; button.textContent = '+ Comparer';
      button.addEventListener('click', function () { toggle(product, button); });
      footer.appendChild(button);
    });
    var bar = document.createElement('div');
    bar.className = 'vectone-compare-bar'; bar.hidden = true;
    bar.innerHTML = '<span>Comparer vos choix</span><strong data-compare-count>0 / 3</strong><button type="button" data-compare-open disabled>Comparer <span aria-hidden="true">→</span></button>';
    document.body.appendChild(bar);
    createModal();
    var open = bar.querySelector('[data-compare-open]');
    if (open) open.addEventListener('click', function () { if (selected.length >= 2) renderModal(); });
    updateBar();
  }

  document.addEventListener('keydown', function (event) { if (event.key === 'Escape') closeModal(); });
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', setup); else setup();
}());
