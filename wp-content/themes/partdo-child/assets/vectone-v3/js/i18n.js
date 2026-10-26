(function (window, document) {
  'use strict';
  var lang = (document.documentElement.lang || 'fr').toLowerCase().indexOf('en') === 0 ? 'en' : 'fr';
  var strings = {
    fr: { compare: 'Comparer', clearAll: 'Tout effacer', remove: 'Retirer', addCompare: '+ Comparer', compareChoices: 'Comparer vos choix', viewOffer: "Voir l'offre", news: 'Actualités', latestNews: 'Nos dernières actualités', viewAll: 'Tout afficher', readArticle: "Lire l'article", reviews: 'Avis clients', review: 'Avis client', faq: 'Questions fréquentes', contactUs: 'Nous contacter', needHelp: "Besoin d’aide ?", send: 'Envoyer', close: 'Fermer', offers: 'LES OFFRES', chooseNeeds: 'Choisissez selon vos besoins', guide: 'Le guide Vectone', guideTitle: '5 questions à se poser avant de choisir son forfait', mobilePlans: 'Forfait mobile' },
    en: { compare: 'Compare', clearAll: 'Clear all', remove: 'Remove', addCompare: '+ Compare', compareChoices: 'Compare your choices', viewOffer: 'View offer', news: 'News', latestNews: 'Latest news', viewAll: 'View all', readArticle: 'Read article', reviews: 'Customer reviews', review: 'Customer review', faq: 'Frequently asked questions', contactUs: 'Contact us', needHelp: 'Need help?', send: 'Send', close: 'Close', offers: 'OFFERS', chooseNeeds: 'Choose according to your needs', guide: 'Vectone guide', guideTitle: '5 questions to ask before choosing your mobile plan', mobilePlans: 'Mobile plans' }
  };
  window.VM_I18N = strings;
  window.vmLang = lang;
  window.vmT = function (key) { return (strings[lang] && strings[lang][key]) || (strings.fr[key] || key); };
  function translateDynamicUi() {
    if (lang !== 'en') return;
    var map = {
      'Forfait mobile': 'Mobile plans', 'Comparer': 'Compare', 'Tout effacer': 'Clear all', 'Retirer': 'Remove',
      '+ Comparer': '+ Compare', 'Comparer vos choix': 'Compare your choices',
      "Voir l'offre": 'View offer', 'Actualités': 'News', 'Nos dernières actualités': 'Latest news', 'Tout afficher': 'View all', "Lire l'article": 'Read article', 'Avis clients': 'Customer reviews',
      'Avis client': 'Customer review', 'Questions fréquentes': 'Frequently asked questions',
      'Nous contacter': 'Contact us', "Besoin d’aide ?": 'Need help?', "Besoin d'aide ?": 'Need help?', 'Envoyer': 'Send', 'Fermer': 'Close',
      'LES OFFRES': 'OFFERS', 'Choisissez selon vos besoins': 'Choose according to your needs',
      'Le guide Vectone': 'Vectone guide', 'Comparateur': 'Comparator',
      'Comparez vos choix': 'Compare your choices', 'Comparer les forfaits': 'Compare plans',
      'Comparer les offres': 'Compare offers', 'Comparer les prix': 'Compare prices',
      'Nous vous répondrons dès que possible.': 'We will get back to you as soon as possible.',
      'Ce que pensent nos utilisateurs': 'What our users think', 'Avis publiés': 'published reviews',
      'Des retours d’expérience actuellement publiés sur Vectone.': 'Experiences currently shared by Vectone users.',
      'Voir toutes les offres': 'View all offers', 'Voir toutes les actualités': 'View all news',
      'Prêt à comparer ?': 'Ready to compare?', 'Trouver l’offre adaptée à vos usages.': 'Find the plan suited to your needs.'
    };
    var nodes = document.querySelectorAll('body *');
    for (var i = 0; i < nodes.length; i++) {
      var el = nodes[i];
      if (el.children.length === 0) {
        var value = (el.textContent || '').trim();
        if (map[value]) el.textContent = map[value];
        else Object.keys(map).forEach(function (from) { if (value.indexOf(from) !== -1) el.textContent = value.split(from).join(map[from]); });
      }
      Array.prototype.slice.call(el.attributes || []).forEach(function (attr) {
        Object.keys(map).forEach(function (from) {
          if (attr.value.indexOf(from) !== -1) attr.value = attr.value.split(from).join(map[from]);
        });
      });
    }
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', function () { window.setTimeout(translateDynamicUi, 80); });
  else window.setTimeout(translateDynamicUi, 80);
}(window, document));
