(function () {
    'use strict';

    function revealOnScroll() {
        var elements = document.querySelectorAll('.vectone-reveal');
        if (!('IntersectionObserver' in window)) {
            elements.forEach(function (element) { element.classList.add('is-visible'); });
            return;
        }

        var observer = new IntersectionObserver(function (entries, instance) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    instance.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        elements.forEach(function (element) { observer.observe(element); });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', revealOnScroll);
    } else {
        revealOnScroll();
    }
}());
