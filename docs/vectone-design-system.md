# Vectone Mobile — spécification de design locale

Version de travail : 1.0  
Périmètre : copie Docker locale uniquement  
Nom de marque : **Vectone Mobile**, inchangé

## 1. Principes

- Renforcer la personnalité Vectone sans remplacer le logo ni les contenus utiles existants.
- Donner une impression éditoriale, humaine et directe, adaptée à un opérateur mobile et à un comparateur de forfaits.
- Préserver l’édition dans WordPress, Elementor et WooCommerce.
- Préférer une hiérarchie claire, des espaces généreux et des composants peu répétitifs.
- Utiliser les données réelles du catalogue et des pages existantes ; ne pas inventer de prix, d’avis ou de témoignages.

## 2. Couleurs

### Couleurs à conserver

| Rôle | Valeur | Usage |
|---|---|---|
| Rouge Vectone | `#e30613` | actions principales, accents, signal, états actifs |
| Rouge foncé | `#b9000b` | survols et états pressés |
| Ivoire | `#f5f0e7` | fonds éditoriaux et surfaces principales |
| Presque noir | `#151515` | texte, titres et navigation |
| Ligne | `rgba(21, 21, 21, .18)` | séparateurs discrets |

### Couleurs secondaires autorisées

- Blanc chaud : `#fffdf8`, pour les surfaces de contraste.
- Gris chaud clair : `#e8e1d7`, pour les fonds secondaires et états désactivés.
- Gris chaud foncé : `#5f5a54`, pour les textes secondaires.
- Rouge très pâle : `#fbe5e6`, pour les alertes ou sélections légères.
- Vert fonctionnel discret : `#237a52`, uniquement pour une confirmation réelle.

Les couleurs secondaires ne doivent jamais concurrencer le rouge Vectone ni créer une nouvelle palette de marque.

## 3. Typographies

Typographies disponibles sans dépendance externe :

- Titres éditoriaux : `Georgia`, `Times New Roman`, serif système.
- Texte courant : `Arial`, `Helvetica`, sans-serif système.
- Interface technique ou métrique : `Arial`, `Helvetica`, sans-serif système.

Règles :

- Titres courts, expressifs, avec une graisse normale plutôt que des blocs lourds.
- Corps de texte entre `1rem` et `1.125rem` selon le contexte.
- Interligne confortable : `1.4` à `1.6`.
- Éviter les polices distantes ou les imports Google sans validation de performance et de conformité.

## 4. Espacements

Échelle recommandée :

`4 / 8 / 12 / 16 / 24 / 32 / 48 / 64 / 80 / 104 px`

- Intérieur minimal d’un contrôle : `12–16 px`.
- Distance entre groupes liés : `16–24 px`.
- Distance entre sections : `64–104 px` sur desktop, `48–72 px` sur mobile.
- Largeur de lecture recommandée : `60–75 caractères`.
- Conteneur desktop : environ `1180 px` maximum.

## 5. Rayons et bordures

- Rayon par défaut : `0` à `4 px`.
- Boutons : `0` à `3 px`, jamais de pilules systématiques.
- Images produit : rayon maximum `4 px` si nécessaire.
- Bordures : `1 px`, couleur ligne ou gris chaud.
- Pas d’ombres lourdes ; préférer les séparateurs, le contraste de fond et la composition.

## 6. Motif graphique « signal mobile »

Le motif Vectone peut utiliser :

- trois points rouges de taille décroissante ;
- trois arcs fins centrés sur un point ;
- une ligne interrompue évoquant un signal ou une connexion ;
- une répétition asymétrique, jamais un motif décoratif envahissant.

Le motif doit rester lisible en monochrome et fonctionner sans image externe. Il peut être réalisé en CSS, SVG local ou élément Elementor contrôlé.

## 7. Loader

- Fond ivoire ou blanc chaud.
- Logo Vectone existant centré.
- Tracé rouge fin, animé brièvement entre `700` et `1000 ms`.
- Trois points ou arcs de signal très discrets.
- Texte optionnel : `Chargement…`, petit et peu contrasté.
- Disparition douce, sans bloquer inutilement la lecture.
- `prefers-reduced-motion: reduce` obligatoire.
- Aucun dégradé.

## 8. Header

- Logo inchangé, proportions conservées.
- Navigation courte et hiérarchisée.
- Une action principale clairement identifiable en rouge.
- Header mobile compact, sans chevauchement du logo, du menu ou du contenu.
- Zones tactiles d’au moins `44 px`.
- Ne pas dupliquer la navigation Partdo et Elementor.

## 9. Filtres

- Filtres visibles et compréhensibles avant l’interaction.
- Labels explicites, sans dépendre uniquement de la couleur.
- États actif, sélectionné, désactivé et réinitialisé clairement distincts.
- Sur mobile : panneau repliable ou barre d’accès fixe légère.
- Conserver les filtres WooCommerce existants et leurs URLs tant que leur comportement n’est pas audité.

## 10. Cartes d’offres

- Éviter les grilles uniformes de cartes identiques.
- Utiliser une offre principale plus grande, puis des offres secondaires plus sobres.
- Hiérarchie : nom réel, bénéfice réel, caractéristiques réelles, prix réel, action.
- Aucun faux badge de popularité ou de recommandation.
- Les cartes peuvent être remplacées par des lignes éditoriales ou des blocs asymétriques.

## 11. Fiches produits

- Image produit réelle, proportions conservées.
- Nom, disponibilité, caractéristiques, prix et action séparés visuellement.
- Variations et options lisibles au clavier et au toucher.
- Stock et états d’achat affichés uniquement lorsqu’ils existent réellement.
- Ne pas supprimer les fonctions WooCommerce : panier, variations, galerie, filtres ou quick view sans validation.

## 12. Articles

- Titres éditoriaux avec largeur de lecture maîtrisée.
- Image mise en avant si elle existe réellement.
- Métadonnées discrètes : date, catégorie, auteur si disponibles.
- Corps de texte confortable sur mobile.
- Archives lisibles sans transformer les articles en catalogue.

## 13. Responsive

Largeurs de référence :

- Mobile étroit : `375 px`.
- Mobile standard : `390 px`.
- Grand mobile : `430 px`.
- Desktop : `1280 px` et plus.

Règles :

- Aucun débordement horizontal.
- Le logo ne doit jamais recouvrir le menu ou le contenu.
- Les colonnes deviennent des séquences verticales lisibles.
- Les actions principales restent visibles sans zoom.
- Les filtres et sélecteurs restent utilisables au toucher.
- Les tailles de texte ne doivent pas dépendre uniquement d’un écran large.

## 14. Animations autorisées

- Transitions courtes : `150–250 ms`.
- Apparitions ou déplacements courts : `300–500 ms`.
- Loader : `700–1000 ms`.
- Animations discrètes de signal ou de soulignement.
- Respect obligatoire de `prefers-reduced-motion`.

Interdire les animations permanentes qui attirent l’attention loin de l’offre ou du contenu.

## 15. Éléments interdits

- Dégradés génériques ou décoratifs.
- Empilements de cartes répétitives sans hiérarchie.
- Faux avis, faux témoignages, faux chiffres ou fausses preuves sociales.
- Prix, promotions ou caractéristiques inventés.
- Texte de remplissage présenté comme contenu Vectone.
- Modification du nom « Vectone Mobile » ou du logo existant.
- Modification des pages légales et de Contact sans validation spécifique.
- Dépendance à la production pour afficher le prototype local.
- Modification directe du thème parent quand une surcharge enfant suffit.

## 16. Compatibilité avec l’existant

- Partdo reste le socle structurel.
- Elementor reste le système d’édition visuelle lorsque les pages ou modèles existants l’utilisent.
- WooCommerce reste la source des produits, prix, variations et filtres.
- Les nouveaux styles doivent être ajoutés en priorité dans `partdo-child/style.css`.
- Toute modification PHP doit être précédée d’une sauvegarde et d’un test sur une page représentative.
- Aucun passage vers Gutenberg ne doit être envisagé sans inventaire des pages Elementor existantes et de leurs métadonnées.
