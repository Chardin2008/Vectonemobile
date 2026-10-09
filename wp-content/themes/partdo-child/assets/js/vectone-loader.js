(function () {
    'use strict';

    var loader = document.querySelector('.vectone-loader');
    if (!loader) {
        return;
    }

    var storageKey = 'vectone_intro_seen';
    var forceShow = new URLSearchParams(window.location.search).get('show_loader') === '1';
    var shouldShow = forceShow;

    try {
        shouldShow = forceShow || window.sessionStorage.getItem(storageKey) !== '1';
    } catch (error) {
        shouldShow = true;
    }

    function removeLoader() {
        if (loader.classList.contains('vectone-loader--hidden')) {
            return;
        }
        loader.classList.add('vectone-loader--hidden');
        window.setTimeout(function () {
            if (loader.parentNode) {
                loader.parentNode.removeChild(loader);
            }
        }, 240);

        try {
            window.sessionStorage.setItem(storageKey, '1');
        } catch (error) {
            // Le loader reste fonctionnel si le stockage est indisponible.
        }
    }

    if (!shouldShow) {
        removeLoader();
        return;
    }

    var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reducedMotion) {
        loader.classList.add('vectone-loader--reduced');
        window.setTimeout(removeLoader, 80);
        return;
    }

    // La séquence visuelle est autonome et ne bloque jamais plus de 1,8 seconde.
    window.setTimeout(removeLoader, 1550);
}());
