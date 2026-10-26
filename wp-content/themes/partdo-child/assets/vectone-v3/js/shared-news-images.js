(function () {
  'use strict';
  if (document.body.classList.contains('home')) return;
  var widget = document.querySelector('.elementor-element-1a54d13 .blog-posts');
  var urls = window.VectoneSharedNews || {};
  if (!widget) return;
  Array.prototype.slice.call(widget.querySelectorAll('.post')).slice(0, 3).forEach(function (post, index) {
    var key = ['free', 'sosh', 'international'][index];
    var url = urls[key];
    var image = post.querySelector('.entry-media img');
    if (!url || !image) return;
    image.src = url;
    image.removeAttribute('data-src');
    image.removeAttribute('srcset');
    image.setAttribute('loading', 'lazy');
  });
}());
