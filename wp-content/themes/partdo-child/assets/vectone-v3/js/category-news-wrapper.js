(function () {
    'use strict';

    function buildCategoryNewsWrapper() {
        if (!document.body.classList.contains('term-forfaits-sans-engagement') && !document.body.classList.contains('term-forfaits-avec-engagement')) return;

        var source = document.querySelector('.elementor-element-1a54d13 .blog-posts');
        if (!source || source.querySelector('.vm-category-news-side')) return;

        var posts = Array.prototype.filter.call(source.children, function (child) {
            return child.classList && child.classList.contains('post');
        }).slice(0, 3);

        if (posts.length !== 3) return;

        var layout = document.createElement('div');
        layout.className = 'vm-category-news-layout blog-posts';

        var main = document.createElement('div');
        main.className = 'vm-category-news-main';
        posts[0].classList.add('vm-category-news-main-card');
        main.appendChild(posts[0]);

        var side = document.createElement('div');
        side.className = 'vm-category-news-side';

        posts[1].classList.add('vm-category-news-side-card');
        posts[2].classList.add('vm-category-news-side-card');
        side.appendChild(posts[1]);
        side.appendChild(posts[2]);

        layout.appendChild(main);
        layout.appendChild(side);
        source.parentNode.replaceChild(layout, source);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', buildCategoryNewsWrapper);
    } else {
        buildCategoryNewsWrapper();
    }
}());
