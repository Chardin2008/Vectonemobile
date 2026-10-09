<?php

require_once get_stylesheet_directory() . '/includes/vectone-awin.php';
require_once get_stylesheet_directory() . '/includes/vectone-category-pages.php';

add_filter( 'wp_nav_menu_objects', 'vectone_compare_hub_menu_objects', 20, 2 );
function vectone_compare_hub_menu_objects( $items, $args ) {
    $parents = array();
    $children = array(
        'Forfaits sans engagement',
        'Forfaits avec engagement',
        'Forfaits data illimitée',
        'Forfaits low-cost',
        'Forfaits internationaux',
    );
    foreach ( $items as $item ) {
        if ( 'Comparatifs forfaits mobiles' === trim( wp_strip_all_tags( $item->title ) ) ) {
            $parents[] = (int) $item->ID;
            $item->title = 'Comparer';
            $item->classes[] = 'vectone-compare-parent';
            $item->url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
        }
    }
    if ( ! $parents ) return $items;
    $parent_id = reset( $parents );
    foreach ( $items as $item ) {
        if ( in_array( trim( wp_strip_all_tags( $item->title ) ), $children, true ) ) {
            $item->menu_item_parent = $parent_id;
            $item->classes[] = 'vectone-compare-child';
            if ( 'Forfaits data illimitée' === $item->title ) {
                $page = get_page_by_path( 'forfaits-data-illimitee' );
                if ( $page ) $item->url = get_permalink( $page );
            }
            if ( 'Forfaits low-cost' === $item->title ) {
                $page = get_page_by_path( 'forfaits-low-cost' );
                if ( $page ) $item->url = get_permalink( $page );
            }
            if ( 'Forfaits internationaux' === $item->title ) {
                $page = get_page_by_path( 'forfaits-internationaux' );
                if ( $page ) $item->url = get_permalink( $page );
            }
        }
    }
    return $items;
}

/**s
 * functions.php
 * @package WordPress
 * @subpackage Partdo
 * @since Partdo 1.0
 * 
 */

add_action( 'wp_enqueue_scripts', 'partdo_enqueue_styles', 99 );
function partdo_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_style_add_data( 'parent-style', 'rtl', 'replace' );

    $v3_base = get_stylesheet_directory() . '/assets/vectone-v3/';
    $v3_uri  = get_stylesheet_directory_uri() . '/assets/vectone-v3/';

    $v3_styles = array(
        'vectone-v3-tokens' => array( 'css/tokens.css', array( 'parent-style' ) ),
        'vectone-v3-header' => array( 'css/header.css', array( 'vectone-v3-tokens' ) ),
    );

    if ( is_front_page() ) {
        $v3_styles['vectone-v3-hero']   = array( 'css/hero.css', array( 'vectone-v3-header' ) );
        $v3_styles['vectone-v3-home']   = array( 'css/home.css', array( 'vectone-v3-hero' ) );
        $v3_styles['vectone-v3-loader'] = array( 'css/loader.css', array( 'vectone-v3-home' ) );
        $v3_styles['vectone-v3-compare'] = array( 'css/compare.css', array( 'vectone-v3-loader' ) );
        $v3_styles['vectone-v3-awin'] = array( 'css/awin.css', array( 'vectone-v3-compare' ) );
    }

    foreach ( $v3_styles as $handle => $data ) {
        $path = $v3_base . $data[0];
        wp_enqueue_style(
            $handle,
            $v3_uri . $data[0],
            $data[1],
            file_exists( $path ) ? filemtime( $path ) : null
        );
    }

    if ( is_front_page() ) {
        $app = $v3_base . 'js/app.js';
        wp_enqueue_script(
            'vectone-v3-app',
            $v3_uri . 'js/app.js',
            array(),
            file_exists( $app ) ? filemtime( $app ) : null,
            true
        );

        $compare = $v3_base . 'js/compare.js';
        wp_enqueue_script(
            'vectone-v3-compare',
            $v3_uri . 'js/compare.js',
            array( 'vectone-v3-app' ),
            file_exists( $compare ) ? filemtime( $compare ) : null,
            true
        );

        wp_localize_script(
            'vectone-v3-app',
            'VectoneV3',
            array(
                'heroFallback' => $v3_uri . 'images/hero-fallback.webp',
            )
        );

        $loader = $v3_base . 'js/loader.js';
        wp_enqueue_script(
            'vectone-v3-loader',
            $v3_uri . 'js/loader.js',
            array(),
            file_exists( $loader ) ? filemtime( $loader ) : null,
            true
        );
    }

    if ( is_page( array( 'forfaits-data-illimitee', 'forfaits-low-cost', 'forfaits-internationaux' ) ) ) {
        $category_css = $v3_base . 'css/category-pages.css';
        wp_enqueue_style( 'vectone-category-pages', $v3_uri . 'css/category-pages.css', array( 'vectone-v3-header' ), file_exists( $category_css ) ? filemtime( $category_css ) : null );
    }

    $hub_css = $v3_base . 'css/compare-hub.css';
    wp_enqueue_style( 'vectone-compare-hub', $v3_uri . 'css/compare-hub.css', array( 'vectone-v3-header' ), file_exists( $hub_css ) ? filemtime( $hub_css ) : null );

    $hub_js = $v3_base . 'js/compare-hub.js';
    wp_enqueue_script( 'vectone-compare-hub', $v3_uri . 'js/compare-hub.js', array(), file_exists( $hub_js ) ? filemtime( $hub_js ) : null, true );
    wp_localize_script( 'vectone-compare-hub', 'VectoneCompareHub', vectone_compare_hub_urls() );

    $offcanvas_css = $v3_base . 'css/offcanvas-close.css';
    wp_enqueue_style( 'vectone-offcanvas-close', $v3_uri . 'css/offcanvas-close.css', array( 'vectone-v3-header' ), file_exists( $offcanvas_css ) ? filemtime( $offcanvas_css ) : null );
    $offcanvas_js = $v3_base . 'js/offcanvas-close.js';
    wp_enqueue_script( 'vectone-offcanvas-close', $v3_uri . 'js/offcanvas-close.js', array(), file_exists( $offcanvas_js ) ? filemtime( $offcanvas_js ) : null, true );
}

function vectone_compare_hub_urls() {
    $url = function( $slug ) {
        $page = get_page_by_path( $slug );
        return $page ? get_permalink( $page ) : '';
    };
    $term_url = function( $slug ) {
        $term = get_term_by( 'slug', $slug, 'product_cat' );
        return $term && ! is_wp_error( $term ) ? get_term_link( $term ) : '';
    };
    return array(
        'data' => $url( 'forfaits-data-illimitee' ),
        'low' => $url( 'forfaits-low-cost' ),
        'international' => $url( 'forfaits-internationaux' ),
        'all' => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ),
        'sans' => $term_url( 'forfaits-sans-engagement' ),
        'engagement' => $term_url( 'forfaits-avec-engagement' ),
    );
}

add_filter( 'body_class', 'vectone_v3_body_class' );
function vectone_v3_body_class( $classes ) {
    $classes[] = 'vectone-v3';
    return $classes;
}

/**
 * Intro Vectone : pure présentation, une seule fois par session.
 * Aucune donnée WordPress / WooCommerce n'est dupliquée.
 */
add_action( 'wp_body_open', 'vectone_v3_loader_markup', 1 );
function vectone_v3_loader_markup() {
    if ( ! is_front_page() ) {
        return;
    }
    ?>
    <div class="vm-v3-loader" aria-hidden="true">
        <div class="vm-v3-loader__inner">
            <div class="vm-v3-loader__track"></div>
            <div class="vm-v3-loader__count">01 / 03</div>
            <span class="vm-v3-loader__word">COMPARER</span>
            <span class="vm-v3-loader__word">CHOISIR</span>
            <span class="vm-v3-loader__word">CONNECTER</span>
            <div class="vm-v3-loader__brand">VECTONE MOBILE</div>
        </div>
    </div>
    <?php
}

// Changer l'affichage par défaut des produits en liste
// Changer l'affichage par défaut des produits WooCommerce en liste
//add_action('woocommerce_before_shop_loop', 'set_default_view_to_list');


function set_default_view_to_list() {
    if (isset($_GET['shop_view'])) {
        return;
    }
    // Redirige vers l'affichage en liste
    wp_redirect(add_query_arg('shop_view', 'list_view', $_SERVER['REQUEST_URI']));
    exit;
}




// Redirection des produits externes directement à l'URL d'achat
add_action('template_redirect', 'redirect_external_product');

function redirect_external_product() {
    if ( is_product() ) {
        global $post;
        $product = wc_get_product( $post->ID );

        if ( $product->is_type( 'external' ) ) {
            wp_redirect( $product->get_product_url() );
            exit;
        }
    }
}



function scroll_to_primary_div() {
    // Ne jamais forcer le scroll sur la homepage : le hero V3 doit rester le premier écran.
    if ( is_front_page() ) {
        return;
    }
    ?>
    <script>
        jQuery(document).ready(function($) {
            // Vérifiez si ce n'est pas la page d'accueil
            if (window.location.href !== 'https://vectonemobile.fr/?shop_view=list_view') {
                // Vérifiez si l'élément avec id="primary" existe
                if ($('#primary').length) {
                    // Ajustez le décalage ici (par exemple, 100 pixels)
                    var offset = 800; // Changez cette valeur selon vos besoins

                    // Faites défiler vers le div avec le décalage
                    $('html, body').animate({
                        scrollTop: $('#primary').offset().top - offset
                    }, 1000); // 1000 millisecondes pour un défilement fluide
                } 
            }
        });
    </script>
    <?php
}
add_action('wp_footer', 'scroll_to_primary_div');





add_filter('wpseo_robots', 'replace_robots_for_filters', 10, 1);
function replace_robots_for_filters($robots) {
    // Vérifier si des paramètres GET sont présents dans l'URL
    if (!empty($_GET)) {
        // Si l'URL contient des paramètres, définir 'noindex, follow'
        return 'noindex, follow';
    }

    // Retourner la valeur par défaut si aucun paramètre n'est trouvé
    return $robots;
}

function custom_filter_canonical_tag() {
    // Récupère l'URL complète de la page actuelle
    $canonical = $_SERVER['REQUEST_URI'];

    // Supprimer tous les paramètres GET de l'URL
    $url_parts = parse_url($canonical);

    // Si l'URL contient une chaîne de requête (paramètres GET)
    if (isset($url_parts['query'])) {
        // On retire la chaîne de requête pour ne garder que le chemin
        $canonical = $url_parts['path'];
    }

    // Afficher la balise canonical
    echo '<link rel="canonical" href="' . esc_url(home_url($canonical)) . '" />' . "\n";
}
add_action('wp_head', 'custom_filter_canonical_tag', 1);


// Optimisation PageSpeed : différer le chargement de klbicon.css
add_filter('style_loader_tag', 'defer_klbicon_css_style', 10, 2);
function defer_klbicon_css_style($html, $handle) {
    if (strpos($html, 'klbicon.css') !== false) {
        return str_replace(
            "rel='stylesheet'",
            "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"",
            $html
        ) . "<noscript>$html</noscript>";
    }
    return $html;
}

/**
 * Élargit la recherche WooCommerce sans modifier les données produits.
 * Chaque terme saisi peut correspondre au titre, au contenu, à une métadonnée
 * ou au nom d'un terme d'attribut/catégorie existant.
 */
add_filter('posts_clauses', 'vectone_product_search_clauses', 20, 2);
function vectone_product_search_clauses($clauses, $query) {
    global $wpdb;

    if (is_admin() || !$query->is_main_query() || !$query->is_search()) {
        return $clauses;
    }

    if ('product' !== $query->get('post_type')) {
        return $clauses;
    }

    $search = trim((string) $query->get('s'));
    if ('' === $search) {
        return $clauses;
    }

    $tokens = preg_split('/\s+/u', $search, -1, PREG_SPLIT_NO_EMPTY);
    $tokens = array_values(array_filter(array_map('sanitize_text_field', $tokens), static function ($token) {
        return function_exists('mb_strlen') ? mb_strlen($token) > 1 : strlen($token) > 1;
    }));

    if (!$tokens) {
        return $clauses;
    }

    if (false === strpos($clauses['join'], 'vectone_search_meta')) {
        $clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS vectone_search_meta ON {$wpdb->posts}.ID = vectone_search_meta.post_id";
        $clauses['join'] .= " LEFT JOIN {$wpdb->term_relationships} AS vectone_search_rel ON {$wpdb->posts}.ID = vectone_search_rel.object_id";
        $clauses['join'] .= " LEFT JOIN {$wpdb->term_taxonomy} AS vectone_search_tax ON vectone_search_rel.term_taxonomy_id = vectone_search_tax.term_taxonomy_id";
        $clauses['join'] .= " LEFT JOIN {$wpdb->terms} AS vectone_search_terms ON vectone_search_tax.term_id = vectone_search_terms.term_id";
    }

    $conditions = [];
    $params = [];
    foreach ($tokens as $token) {
        $like = '%' . $wpdb->esc_like($token) . '%';
        $conditions[] = '(' .
            "{$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_content LIKE %s OR {$wpdb->posts}.post_excerpt LIKE %s OR vectone_search_meta.meta_value LIKE %s OR vectone_search_terms.name LIKE %s" .
            ')';
        array_push($params, $like, $like, $like, $like, $like);
    }

    $clauses['where'] .= ' AND ' . $wpdb->prepare(implode(' AND ', $conditions), $params);
    $clauses['distinct'] = 'DISTINCT';

    return $clauses;
}



?>
