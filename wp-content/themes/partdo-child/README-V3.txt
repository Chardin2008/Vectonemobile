VECTONE MOBILE — V3
===================

Objectif
--------
Refonte visuelle de la homepage sans changer les données ni les redirections.

Ce qui reste la source de vérité WordPress
------------------------------------------
- Slider / textes / image existante : Elementor.
- Produits, prix, attributs, images et URLs d'achat : WooCommerce.
- Menus : WordPress / Apparence > Menus.
- Articles : WordPress / Articles.
- Footer / widgets : configuration WordPress existante.

La V3 ne remplace pas ces données : elle lit le DOM existant et construit autour une nouvelle présentation.
Les cartes "Offres du moment" réutilisent les vrais titres, prix et href des produits déjà présents dans la page.

Fichiers V3
-----------
assets/vectone-v3/css/tokens.css
assets/vectone-v3/css/header.css
assets/vectone-v3/css/hero.css
assets/vectone-v3/css/home.css
assets/vectone-v3/css/loader.css
assets/vectone-v3/js/app.js
assets/vectone-v3/js/loader.js
assets/vectone-v3/images/hero-fallback.webp

Image hero
----------
L'image Elementor existante reste prioritaire.
hero-fallback.webp n'est utilisée que si aucune image de hero exploitable n'est présente.
Ainsi, changer l'image dans Elementor continue de fonctionner.

Loader
------
Une fois par session.
Pour le rejouer : ajouter ?show_loader=1 à l'URL de la homepage.

Sécurité fonctionnelle
----------------------
La logique de redirection des produits externes, le SEO/canonical, la recherche WooCommerce et les fonctions historiques du thème enfant ont été conservés.
Aucune base de données n'est modifiée par ces fichiers.
