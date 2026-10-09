(function () {
    'use strict';

    var selected = [];
    var maxSelected = 3;
    var previousBodyOverflow = '';

    function findProducts() {
        // Sur la homepage, Partdo rend les offres comme des blocs .product
        // contenant directement .product-wrapper, sans li.product.
        return document.querySelectorAll('body .products .product-wrapper');
    }

    function updateBar() {
        var bar = document.querySelector('.vectone-compare-bar');
        if (!bar) {
            return;
        }

        bar.querySelector('[data-compare-count]').textContent = selected.length + ' / ' + maxSelected;
        bar.querySelector('[data-compare-open]').disabled = selected.length < 2;
        bar.hidden = selected.length === 0;
    }

    function textOf(element) {
        return element ? element.textContent.replace(/\s+/g, ' ').trim() : '';
    }

    function getProductData(product) {
        var titleElement = product.querySelector('.product-title');
        var title = textOf(titleElement);
        var price = textOf(product.querySelector('.price'));
        var image = product.querySelector('.thumbnail-wrapper img');
        var attributes = Array.prototype.slice.call(product.querySelectorAll('.product-attributes .attribute')).map(textOf).filter(Boolean);
        var dataElement = product.querySelector('.vectone-plan-data');
        var dataMatch = title.match(/(\d+(?:[.,]\d+)?)\s*(Go|GB)\b/i);
        var offerLink = product.querySelector('.product-footer a.button, .product-footer a.product_type_external, .product-footer a[href]');
        var fiveG = attributes.concat([title]).join(' ').match(/\b5G\b/i);

        return {
            image: image ? (image.getAttribute('data-src') || image.currentSrc || image.getAttribute('src') || '') : '',
            title: title,
            data: dataElement ? textOf(dataElement) : (dataMatch ? dataMatch[1].replace(',', '.') + ' ' + dataMatch[2].toUpperCase() : ''),
            price: price,
            attributes: attributes,
            fiveG: Boolean(fiveG),
            link: offerLink ? offerLink.href : ''
        };
    }

    function makeText(tag, className, value) {
        var element = document.createElement(tag);
        element.className = className;
        element.textContent = value;
        return element;
    }

    function renderCompareModal() {
        var modal = document.querySelector('.vectone-compare-modal');
        var grid = modal.querySelector('.vectone-compare-modal__grid');
        grid.textContent = '';

        selected.forEach(function (product) {
            var data = getProductData(product);
            var item = document.createElement('article');
            item.className = 'vectone-compare-modal__item';

            if (data.image) {
                var image = document.createElement('img');
                image.className = 'vectone-compare-modal__logo';
                image.src = data.image;
                image.alt = data.title;
                item.appendChild(image);
            }

            item.appendChild(makeText('h3', 'vectone-compare-modal__title', data.title));
            if (data.data) {
                item.appendChild(makeText('strong', 'vectone-compare-modal__data', data.data));
            }
            if (data.price) {
                item.appendChild(makeText('p', 'vectone-compare-modal__price', data.price + ' / mois'));
            }
            data.attributes.forEach(function (attribute) {
                item.appendChild(makeText('p', 'vectone-compare-modal__attribute', attribute));
            });
            if (data.fiveG) {
                item.appendChild(makeText('p', 'vectone-compare-modal__attribute', '5G'));
            }

            if (data.link) {
                var link = document.createElement('a');
                link.className = 'vectone-compare-modal__cta';
                link.href = data.link;
                link.target = '_blank';
                link.rel = 'sponsored noopener';
                link.textContent = 'Voir l’offre →';
                item.appendChild(link);
            }
            grid.appendChild(item);
        });

        modal.hidden = false;
        previousBodyOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
    }

    function closeCompareModal() {
        var modal = document.querySelector('.vectone-compare-modal');
        if (!modal) {
            return;
        }
        modal.hidden = true;
        document.body.style.overflow = previousBodyOverflow;
    }

    function createCompareModal() {
        var modal = document.createElement('div');
        modal.className = 'vectone-compare-modal';
        modal.hidden = true;
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('aria-labelledby', 'vectone-compare-title');
        modal.innerHTML = '<div class="vectone-compare-modal__backdrop"></div>' +
            '<section class="vectone-compare-modal__panel">' +
            '<button type="button" class="vectone-compare-modal__close" aria-label="Fermer">×</button>' +
            '<p class="vectone-compare-modal__eyebrow">Comparateur</p>' +
            '<h2 id="vectone-compare-title">Comparez vos choix</h2>' +
            '<p class="vectone-compare-modal__subtitle">Les différences essentielles, côte à côte.</p>' +
            '<div class="vectone-compare-modal__grid"></div>' +
            '</section>';
        document.body.appendChild(modal);
        modal.querySelector('.vectone-compare-modal__close').addEventListener('click', closeCompareModal);
        modal.querySelector('.vectone-compare-modal__backdrop').addEventListener('click', closeCompareModal);
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !modal.hidden) {
                closeCompareModal();
            }
        });
    }

    function toggleProduct(product, button) {
        var index = selected.indexOf(product);

        if (index !== -1) {
            selected.splice(index, 1);
            product.classList.remove('is-compare-selected');
            button.classList.remove('is-added');
            button.textContent = '+ Comparer';
            updateBar();
            return;
        }

        if (selected.length >= maxSelected) {
            return;
        }

        selected.push(product);
        product.classList.add('is-compare-selected');
        button.classList.add('is-added');
        button.textContent = '✓ Ajouté';
        updateBar();
    }

    function setup() {
        var products = findProducts();
        if (!products.length) {
            return;
        }

        products.forEach(function (product) {
            var footer = product.querySelector('.product-footer-inner, .product-footer');
            var title = product.querySelector('.product-title');
            if (!footer) {
                return;
            }

            if (title && !product.querySelector('.vectone-plan-data')) {
                var match = title.textContent.match(/(\d+(?:[.,]\d+)?)\s*(Go|GB)\b/i);
                if (match) {
                    var data = document.createElement('span');
                    data.className = 'vectone-plan-data';
                    data.textContent = match[1].replace(',', '.') + ' ' + match[2].toUpperCase();
                    title.parentNode.insertBefore(data, title);
                }
            }

            if (footer.querySelector('.vectone-compare-button')) {
                return;
            }

            var compare = document.createElement('button');
            compare.type = 'button';
            compare.className = 'vectone-compare-button';
            compare.textContent = '+ Comparer';
            compare.addEventListener('click', function () {
                toggleProduct(product, compare);
            });
            footer.appendChild(compare);
        });

        var bar = document.createElement('div');
        bar.className = 'vectone-compare-bar';
        bar.hidden = true;
        bar.innerHTML = '<span>Comparer vos choix</span><strong data-compare-count>0 / 3</strong><button type="button" data-compare-open disabled>Comparer <span aria-hidden="true">→</span></button>';
        document.body.appendChild(bar);
        createCompareModal();
        bar.querySelector('[data-compare-open]').addEventListener('click', function () {
            if (selected.length >= 2) {
                renderCompareModal();
            }
        });
        updateBar();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setup);
    } else {
        setup();
    }
}());
