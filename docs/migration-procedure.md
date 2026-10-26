# Procédure de migration Vectone Mobile

Cette procédure prépare une mise en ligne ; elle n'a pas été exécutée.

## Avant transfert

1. Obtenir un accès SSH/SFTP, accès au panneau d'hébergement et accès MySQL de production.
2. Relever les versions PHP, WordPress, WooCommerce, Elementor et Partdo Core en production.
3. Sauvegarder séparément les fichiers, la base, la médiathèque et les réglages de production.
4. Vérifier que la sauvegarde est lisible et restaurable sur une copie.
5. Exporter l'inventaire des plugins, thèmes, menus, formulaires, produits, commandes et utilisateurs.
6. Comparer la production et le local. Ne jamais remplacer la base de production par la base locale sans migration sélective validée.

## Transfert

1. Placer le site en maintenance uniquement après validation et sauvegarde.
2. Transférer le contenu de `partdo-child-package/` dans `wp-content/themes/partdo-child/`.
3. Ne pas écraser le thème parent ni `wp-config.php`.
4. Conserver les commandes, utilisateurs, produits, prix et réglages commerciaux de production.
5. Migrer uniquement les options/contenus explicitement validés, notamment les options Vectone et les campagnes Awin.
6. Si les URLs changent, utiliser un outil WordPress compatible avec les données sérialisées sur une copie, puis valider avant production.
7. Vérifier les permaliens, HTTPS, canonicals et sitemap.

## Vérification après installation

- accueil, actualités, article, opérateur, catégorie, produit, contact, légal, recherche ;
- header, sticky, menus, menu Comparer et responsive 1440/1024/768/390/375 ;
- hero administrable et fallback ;
- médias et WebP ;
- formulaires sans envoyer de message de test réel ;
- campagnes Awin et paramètres de tracking ;
- WooCommerce, sessions et tables requises sur la destination ;
- commandes, utilisateurs, produits, prix et catégories conservés ;
- logs PHP/WordPress et erreurs JavaScript.

La table de sessions WooCommerce manquante en local doit être contrôlée sur la destination avant toute ouverture commerciale.

## Retour arrière

1. Activer le mode maintenance.
2. Restaurer les fichiers de production sauvegardés.
3. Restaurer la base de production sauvegardée uniquement si des données ont été modifiées et après décision documentée.
4. Purger uniquement les caches autorisés par l'hébergement.
5. Vérifier accueil, connexion, commandes, formulaires et paiements.
6. Retirer la maintenance seulement après validation.
