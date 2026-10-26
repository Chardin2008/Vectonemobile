# Package de migration Vectone Mobile

## Contenu

Le package contient le thème enfant complet `wp-content/themes/partdo-child/`, notamment :

- `functions.php`, `header.php`, `style.css` et les fichiers PHP de `includes/` ;
- les CSS et JavaScript Vectone réellement présents dans `assets/` ;
- les images et WebP du thème enfant ;
- les modules d'administration Vectone et Awin ;
- les fichiers de traduction et de configuration du thème enfant.

Les tests locaux et captures de développement sont exclus du package de transfert. Les sauvegardes SQL, fichiers Docker, caches, logs et secrets sont également exclus.

## Dépendances à vérifier sur la destination

- WordPress et PHP compatibles avec le thème enfant ;
- thème parent Partdo et Partdo Core ;
- WooCommerce ;
- Elementor/Elementor Pro si utilisés par les pages ;
- Contact Form 7 ;
- extensions médias, consentement et SEO réellement utilisées ;
- menus, widgets/footer et contenus Elementor présents en production.

Le package ne contient pas la base WordPress ni les médias de la médiathèque. Ceux-ci doivent être sauvegardés et comparés côté production avant toute migration.

## URLs

Les URLs locales ne sont pas remplacées dans la base locale. Une adaptation WordPress sérialisée devra être exécutée sur une copie de sauvegarde de la base de destination avec un outil compatible, après comparaison des URLs.
