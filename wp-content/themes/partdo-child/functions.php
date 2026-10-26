<?php

require_once get_stylesheet_directory() . '/includes/vectone-awin.php';
require_once get_stylesheet_directory() . '/includes/vectone-category-pages.php';
require_once get_stylesheet_directory() . '/includes/vectone-admin-content.php';

/* TranslateRocket is intentionally inactive until complete EN content exists. */
add_action( 'template_redirect', 'vectone_redirect_disabled_english_routes', 1 );
function vectone_redirect_disabled_english_routes() {
    $path = trim( (string) parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );
    if ( 'en' === $path || 0 === strpos( $path, 'en/' ) ) {
        wp_safe_redirect( home_url( '/' ), 302 );
        exit;
    }
}

function vectone_current_lang() {
    if ( function_exists( 'trrocket_current_language' ) ) {
        return strtolower( (string) trrocket_current_language() );
    }
    return strtolower( (string) determine_locale() );
}

function vectone_t( $fr, $en ) {
    return 0 === strpos( vectone_current_lang(), 'en' ) ? $en : $fr;
}

/* Keep the homepage semantically structured without changing its visual composition. */
add_action( 'wp_body_open', 'vectone_home_semantic_heading', 5 );
function vectone_home_semantic_heading() {
    if ( ! is_front_page() ) {
        return;
    }

    $front_page_id = (int) get_option( 'page_on_front' );
    $title = $front_page_id ? get_the_title( $front_page_id ) : '';
    $title = trim( wp_strip_all_tags( (string) $title ) );
    if ( '' === $title ) {
        $title = 'Vectone Mobile';
    }

    echo '<h1 class="screen-reader-text">' . esc_html( $title ) . '</h1>';
}

/* The parent theme emits this decorative service icon without an alt attribute. */
add_action( 'wp_footer', 'vectone_repair_decorative_service_icon_alt', 1 );
function vectone_repair_decorative_service_icon_alt() {
    if ( ! is_front_page() ) {
        return;
    }

    echo '<script>document.querySelectorAll(".serack-icon-kit:not([alt])").forEach(function(image){image.setAttribute("alt","");image.setAttribute("aria-hidden","true");});</script>';
}

add_filter( 'the_content', 'vectone_legal_page_toc', 20 );
function vectone_legal_page_toc( $content ) {
    if ( ! is_page( 4471 ) || ! in_the_loop() || ! is_main_query() ) return $content;
    $content = preg_replace( '/<h4([^>]*)>Nom de domaine et hébergement<\/h4>/u', '<h2 id="legal-hosting"$1>Nom de domaine et hébergement</h2>', $content );
    $content = preg_replace( '/<h4([^>]*)>Données personnelles ?:\s*<\/h4>/u', '<h2 id="legal-data"$1>Données personnelles :</h2>', $content );
    $content = preg_replace( '/<h2([^>]*)>Conditions Générales d’Utilisation<\/h2>/u', '<h2 id="legal-terms"$1>Conditions Générales d’Utilisation</h2>', $content );
    $toc = '<nav class="vm-legal-toc" aria-label="Sommaire de la page"><strong>Sur cette page</strong><a href="#legal-hosting">01 Éditeur et hébergement</a><a href="#legal-data">02 Données personnelles</a><a href="#legal-terms">03 Conditions générales d’utilisation</a></nav>';
    return $toc . $content;
}

add_filter( 'the_content', 'vectone_privacy_page_toc', 20 );
function vectone_privacy_page_toc( $content ) {
    if ( ! is_page( 4473 ) || ! in_the_loop() || ! is_main_query() ) return $content;
    $anchors = array(
        'Qui sommes-nous' => 'privacy-about', 'Commentaires' => 'privacy-comments', 'Médias' => 'privacy-media',
        'Cookies' => 'privacy-cookies', 'Contenu embarqué depuis d’autres sites' => 'privacy-embedded',
        'Utilisation et transmission de vos données personnelles' => 'privacy-use', 'Durées de stockage de vos données' => 'privacy-retention',
        'Les droits que vous avez sur vos données' => 'privacy-rights', 'Où vos données sont envoyées' => 'privacy-transfer'
    );
    $index = 0; $toc = '<nav class="vm-legal-toc" aria-label="Sommaire de la page"><strong>Sur cette page</strong>';
    foreach ( $anchors as $label => $id ) {
        $pattern = '/<h2([^>]*)>\s*' . preg_quote( $label, '/' ) . '\s*<\/h2>/u';
        if ( preg_match( $pattern, $content ) ) {
            $index++; $content = preg_replace( $pattern, '<h2 id="' . esc_attr( $id ) . '"$1>' . $label . '</h2>', $content, 1 );
            $toc .= '<a href="#' . esc_attr( $id ) . '">' . sprintf( '%02d', $index ) . ' — ' . esc_html( $label ) . '</a>';
        }
    }
    $toc .= '</nav>';
    return $toc . $content;
}

/* Homepage contact section: reuse the existing CF7 form immediately before the footer. */
add_action( 'get_footer', 'vectone_render_home_contact', 5 );
function vectone_render_home_contact() {
    if ( ! is_front_page() || ! shortcode_exists( 'contact-form-7' ) ) {
        return;
    }

    echo '<section id="contact" class="vm-home-contact" aria-labelledby="vm-home-contact-title">';
    echo '<div class="vm-home-contact__inner">';
    echo '<div class="vm-home-contact__intro">';
    $content = function_exists( 'vectone_site_content' ) ? vectone_site_content() : array();
    $home = $content['home'] ?? array();
    $home_value = static function( $key, $fallback ) use ( $home ) { return '' !== trim( (string) ( $home[ $key ] ?? '' ) ) ? $home[ $key ] : $fallback; };
    echo '<p class="vm-home-contact__eyebrow">' . esc_html( $home_value( 'contact_eyebrow', 'Contact' ) ) . '</p>';
    $contact_title = (string) $home_value( 'contact_title', 'Une question ?\nParlons-en.' );
    $contact_title = str_replace( array( "\\r\\n", "\\n", "\\r" ), "\n", $contact_title );
    $contact_title = str_replace( array( "\r\n", "\r" ), "\n", $contact_title );
    echo '<h2 id="vm-home-contact-title">' . nl2br( esc_html( $contact_title ), false ) . '</h2>';
    echo '<p class="vm-home-contact__lead">' . esc_html( $home_value( 'contact_text', 'Une question sur un forfait, une offre ou le site ? Écrivez-nous simplement.' ) ) . '</p>';
    echo '<p class="vm-home-contact__direct"><span>' . esc_html( $home_value( 'contact_direct_label', 'Contact direct' ) ) . '</span><a href="mailto:contact@vectonemobile.fr">contact@vectonemobile.fr</a></p>';
    echo '</div>';
    echo '<div class="vm-home-contact__form"><p class="vm-home-contact__form-title">' . esc_html( $home_value( 'contact_form_title', 'Écrivez-nous' ) ) . '</p><p class="vm-home-contact__form-note">' . esc_html( $home_value( 'contact_form_note', 'Nous vous répondrons dès que possible.' ) ) . '</p>';
    echo do_shortcode( '[contact-form-7 id="2299"]' );
    echo '</div></div></section>';
}

/* Contextual editorial transition: one shared hook, deliberately compact by page type. */
add_action( 'get_footer', 'vectone_render_signal_footer', 8 );
function vectone_render_signal_footer() {
    // Navigation already exists globally; do not inject a repeated CTA block.
    return;

    if ( is_front_page() ) return;
    $content = function_exists( 'vectone_site_content' ) ? vectone_site_content() : array();
    $footer = $content['footer'] ?? array();
    $compare_url = function_exists( 'vectone_compare_hub_urls' ) ? vectone_compare_hub_urls() : array();
    $compare_url = ! empty( $footer['cta_url'] ) ? $footer['cta_url'] : ( ! empty( $compare_url['all'] ) ? $compare_url['all'] : home_url( '/' ) );

    $context = 'minimal';
    if ( is_singular( 'post' ) ) $context = 'article';
    elseif ( function_exists( 'vectone_operator_archive_page' ) && vectone_operator_archive_page() ) $context = 'operator';
    elseif ( is_page( array( 'contact', 'mentions-legales', 'politique-de-confidentialite' ) ) ) $context = 'minimal';
    elseif ( is_page( array( 'comparer', 'comparateur' ) ) ) $context = 'compare';

    /* Articles intentionally end directly before the global footer. */
    if ( 'article' === $context ) return;
    /*
    if ( 'article' === $context ) {
        $posts = get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 2, 'post__not_in' => array( get_the_ID() ), 'orderby' => 'date', 'order' => 'DESC' ) );
        if ( ! $posts ) return;
        echo '<section class="vm-signal-footer vm-signal-footer--article" aria-labelledby="vm-signal-footer-title"><div class="vm-signal-footer__inner"><div class="vm-signal-footer__copy"><p class="vm-signal-footer__eyebrow">À LIRE ENSUITE</p><h2 id="vm-signal-footer-title">Poursuivez votre lecture</h2></div><div class="vm-signal-footer__recommendations">';
        foreach ( $posts as $post ) {
            $thumb = get_the_post_thumbnail_url( $post->ID, 'thumbnail' );
            echo '<a class="vm-signal-footer__recommendation" href="' . esc_url( get_permalink( $post ) ) . '">';
            if ( $thumb ) echo '<img src="' . esc_url( $thumb ) . '" alt="" loading="lazy">';
            echo '<span><small>' . esc_html( get_the_date( '', $post ) ) . '</small><strong>' . esc_html( get_the_title( $post ) ) . '</strong></span></a>';
        }
        echo '</div></div></section>';
        return;
    }
    */

    echo '<section class="vm-signal-footer vm-signal-footer--' . esc_attr( $context ) . '" aria-labelledby="vm-signal-footer-title"><div class="vm-signal-footer__inner"><div class="vm-signal-footer__copy">';
    if ( 'operator' === $context ) {
        echo '<p class="vm-signal-footer__eyebrow">POURSUIVRE LA COMPARAISON</p><h2 id="vm-signal-footer-title">Comparez aussi les autres forfaits disponibles.</h2>';
        echo '</div><div class="vm-signal-footer__action"><a class="vm-signal-footer__cta" href="' . esc_url( $compare_url ) . '">Comparer les forfaits <span aria-hidden="true">→</span></a></div></div></section>';
    } elseif ( 'compare' === $context ) {
        echo '<p class="vm-signal-footer__eyebrow">BESOIN D’AIDE ?</p><h2 id="vm-signal-footer-title">Besoin d’aide pour choisir ?</h2>';
        echo '</div><div class="vm-signal-footer__action"><a class="vm-signal-footer__cta" href="' . esc_url( home_url( '/#contact' ) ) . '">Nous contacter <span aria-hidden="true">→</span></a></div></div></section>';
    } else {
        echo '<p class="vm-signal-footer__eyebrow">VECTONE MOBILE</p><h2 id="vm-signal-footer-title">Des choix mobiles plus clairs.</h2>';
        echo '</div><div class="vm-signal-footer__action"><span class="vm-signal-footer__signal" aria-hidden="true"><i></i><i></i><i></i></span></div></div></section>';
    }
}

/* Keep the parent footer copyright current without touching article dates. */
add_action( 'wp_footer', 'vectone_update_footer_copyright', 99 );
function vectone_update_footer_copyright() {
    $year = function_exists( 'wp_date' ) ? wp_date( 'Y' ) : date( 'Y' );
    echo '<script>(function(){var y=' . wp_json_encode( $year ) . ';document.querySelectorAll(".footer-copyright").forEach(function(el){var target=el.querySelector(".site-copyright p")||el.querySelector(".site-copyright");if(!target)return;target.textContent="© 2024–"+y+" Vectone Mobile. Tous droits réservés.";target.classList.add("vm-copyright-final");});})();</script>';
}

add_action( 'wp_enqueue_scripts', 'vectone_home_contact_style', 30 );
function vectone_home_contact_style() {
    if ( ! is_front_page() ) return;
    $file = get_stylesheet_directory() . '/assets/vectone-v3/css/home-contact.css';
    wp_enqueue_style( 'vectone-home-contact', get_stylesheet_directory_uri() . '/assets/vectone-v3/css/home-contact.css', array( 'parent-style' ), file_exists( $file ) ? filemtime( $file ) : null );
}

add_filter( 'wp_nav_menu_objects', 'vectone_home_contact_menu_link', 30 );
function vectone_home_contact_menu_link( $items ) {
    foreach ( $items as $item ) {
        if ( 'Contact' === trim( wp_strip_all_tags( $item->title ) ) ) {
            $item->url = home_url( '/#contact' );
        }
    }
    return $items;
}

add_action( 'wp_footer', 'vectone_repoint_contact_ctas', 40 );
function vectone_repoint_contact_ctas() {
    $contact_anchor = esc_js( home_url( '/#contact' ) );
    $content = function_exists( 'vectone_site_content' ) ? vectone_site_content() : array();
    $topbar = $content['topbar'] ?? array();
    echo '<script>document.querySelectorAll("a.vm-sans-contact").forEach(function(link){link.href="' . $contact_anchor . '";});(function(){var c=' . wp_json_encode( $topbar ) . ';if(!c)return;var n=document.querySelector(".header-topbar .header-notice p");if(n&&c.help&&c.email)n.innerHTML=""+c.help+" <a href=\"mailto:"+c.email+"\">"+c.email+"</a>";var l=document.querySelector(".header-topbar .column.left");if(l&&c.slogan)l.setAttribute("data-vectone-slogan",c.slogan);})();</script>';
}

add_action( 'wp_footer', 'vectone_render_admin_topbar_text', 41 );
function vectone_render_admin_topbar_text() {
    $topbar = function_exists( 'vectone_site_content' ) ? ( vectone_site_content()['topbar'] ?? array() ) : array();
    if ( empty( $topbar['slogan'] ) ) return;
    echo '<script>(function(){var l=document.querySelector(".header-topbar .column.left");if(l&&!l.querySelector(".vm-topbar-slogan")){var s=document.createElement("span");s.className="vm-topbar-slogan";s.textContent=' . wp_json_encode( $topbar['slogan'] ) . ';l.appendChild(s);}})();</script>';
}

add_action( 'loop_start', 'vectone_news_step3_intro', 10 );
function vectone_news_step3_intro( $query ) {
    if ( ! is_home() || ! $query->is_main_query() ) {
        return;
    }

    echo '<section class="vectone-news-intro" aria-labelledby="vectone-news-intro-title">';
    echo '<div class="vectone-news-intro__copy">';
    echo '<p class="vectone-news-intro__eyebrow"><span aria-hidden="true"></span>Actualités &amp; conseils</p>';
    echo '<h1 id="vectone-news-intro-title">Le magazine Vectone</h1>';
    echo '<p class="vectone-news-intro__lead">Guides, conseils et actualités pour mieux comprendre les forfaits mobiles, les réseaux et les usages du quotidien.</p>';
    echo '</div>';
    echo '<div class="vectone-news-intro__image-placeholder" aria-hidden="true"></div>';
    echo '</section>';
}

add_action( 'wp_footer', 'vectone_news_card_images', 30 );
function vectone_news_card_images() {
    if ( ! is_home() || ! is_main_query() ) {
        return;
    }
    global $wp_query;
    $images = array();
    foreach ( (array) $wp_query->posts as $post ) {
        $image = get_the_post_thumbnail_url( $post->ID, 'large' );
        if ( $image ) {
            $images[ 'post-' . (int) $post->ID ] = esc_url_raw( $image );
        }
    }
    if ( ! $images ) {
        return;
    }
    echo '<script>(function(){var images=' . wp_json_encode( $images ) . ';Object.keys(images).forEach(function(id){var card=document.getElementById(id);if(!card)return;var media=card.querySelector(".entry-media");if(media)media.style.backgroundImage="url(\\""+images[id]+"\\")";});}());</script>';
}

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

/**
 * Keep TranslateRocket's real language links in the compact topbar only.
 * The primary menu must retain only the site's navigation links.
 */
function vectone_v3_language_menu_cleanup( $items, $args ) {
	if ( ! is_array( $items ) || ! class_exists( '\TranslateRocket\Plugin' ) ) {
		return $items;
	}

	$router = \TranslateRocket\Plugin::instance()->router();
	if ( ! is_object( $router ) || ! method_exists( $router, 'url_for_language' ) ) {
		return $items;
	}

	$topbar_ids = array( 3655, 3662 );
	$has_topbar_items = false;
	foreach ( $items as $item ) {
		if ( in_array( (int) $item->ID, $topbar_ids, true ) ) {
			$has_topbar_items = true;
			break;
		}
	}

	if ( $has_topbar_items ) {
		foreach ( $items as $item ) {
			if ( ! in_array( (int) $item->ID, $topbar_ids, true ) ) {
				continue;
			}
			$code = 3655 === (int) $item->ID ? 'fr' : 'en';
			$item->menu_item_parent = 0;
			$item->url = $router->url_for_language( $code );
			$item->title = 'fr' === $code ? 'FR' : 'EN';
			$item->attr_title = 'fr' === $code ? 'Français' : 'English';
			$current = method_exists( $router, 'current_language' ) ? strtolower( (string) $router->current_language() ) : 'fr';
			$item->classes = array( 'menu-item', 'menu-item-type-custom', 'menu-item-object-custom', 'vectone-language-link', 'vectone-language-' . $code );
			if ( $code === $current ) {
				$item->classes[] = 'vectone-language-current';
			}
		}
	}

	// TranslateRocket's generated copies belong in the topbar, not the main menu.
	foreach ( $items as $key => $item ) {
		if ( in_array( 'trrocket-lang-item', (array) $item->classes, true ) ) {
			unset( $items[ $key ] );
		}
	}

	return array_values( $items );
}
add_filter( 'wp_nav_menu_objects', 'vectone_v3_language_menu_cleanup', 30, 2 );

/** TranslateRocket's switcher is already rendered in the topbar; disable only
 * its fallback floating instance, which otherwise appears as a large circle. */
function vectone_v3_disable_language_floating_fallback( $settings ) {
	if ( ! is_admin() && is_array( $settings ) ) {
		$settings['switcher']['floating'] = false;
		$settings['switcher']['in_spot']  = false;
	}
	return $settings;
}
add_filter( 'option_trrocket_settings', 'vectone_v3_disable_language_floating_fallback', 30 );

/** Shared presentation layer for the twelve operator product-category archives. */
function vectone_operator_archive_page() {
    if ( ! function_exists( 'is_product_category' ) || ! is_product_category() ) return false;
    $term = get_queried_object();
    if ( ! $term || empty( $term->taxonomy ) || 'product_cat' !== $term->taxonomy ) return false;
    $parent = ! empty( $term->parent ) ? get_term( (int) $term->parent, 'product_cat' ) : null;
    return $parent && ! is_wp_error( $parent ) && 'forfait-mobile' === $parent->slug;
}

function vectone_operator_archive_term() {
    $term = get_queried_object();
    return ( $term instanceof WP_Term && 'product_cat' === $term->taxonomy ) ? $term : null;
}

function vectone_operator_archive_body_class( $classes ) {
    if ( vectone_operator_archive_page() ) {
        $term = vectone_operator_archive_term();
        $classes[] = 'vectone-operator-archive';
        if ( $term ) $classes[] = 'vectone-operator-' . sanitize_html_class( $term->slug );
    }
    return $classes;
}
add_filter( 'body_class', 'vectone_operator_archive_body_class', 20 );

function vectone_operator_archive_hero() {
    if ( ! vectone_operator_archive_page() ) return;
    $term = vectone_operator_archive_term();
    if ( ! $term ) return;
    $name = single_term_title( '', false );
    $description = trim( wp_strip_all_tags( term_description( $term->term_id, 'product_cat' ) ) );
    $description = $description ? wp_trim_words( $description, 42, '…' ) : 'Comparez les forfaits mobiles disponibles et leurs caractéristiques réelles.';
    ?>
    <section class="vm-operator-hero" aria-labelledby="vm-operator-title">
        <div class="vm-operator-hero__signal" aria-hidden="true"><i></i><i></i><i></i></div>
        <span class="vm-category-eyebrow">FORFAITS MOBILE</span>
        <h1 id="vm-operator-title">Forfaits <?php echo esc_html( $name ); ?></h1>
        <p><?php echo esc_html( $description ); ?></p>
    </section>
    <?php
}
add_action( 'woocommerce_before_main_content', 'vectone_operator_archive_hero', 18 );

function vectone_operator_archive_results_heading() {
    if ( ! vectone_operator_archive_page() ) return;
    $name = single_term_title( '', false );
    echo '<section class="vm-operator-results-heading" aria-labelledby="vm-operator-results-title"><span class="vm-category-eyebrow">SÉLECTION VECTONE</span><h2 id="vm-operator-results-title">Les offres ' . esc_html( $name ) . '</h2><p>Comparez les offres actuellement disponibles chez cet opérateur.</p></section>';
}
add_action( 'woocommerce_before_shop_loop', 'vectone_operator_archive_results_heading', 4 );

function partdo_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_style_add_data( 'parent-style', 'rtl', 'replace' );
    $v4_mode = vectone_v4_enabled() && is_front_page();

    $v3_base = get_stylesheet_directory() . '/assets/vectone-v3/';
    $v3_uri  = get_stylesheet_directory_uri() . '/assets/vectone-v3/';

    $v3_styles = array(
        'vectone-v3-tokens' => array( 'css/tokens.css', array( 'parent-style' ) ),
        'vectone-v3-foundation' => array( 'css/foundation.css', array( 'vectone-v3-tokens' ) ),
    );
    if ( ! $v4_mode ) {
        $v3_styles['vectone-v3-header'] = array( 'css/header.css', array( 'vectone-v3-tokens' ) );
    }

    if ( is_front_page() ) {
        if ( ! $v4_mode ) {
            $v3_styles['vectone-v3-hero'] = array( 'css/hero.css', array( 'vectone-v3-header' ) );
        }
        $v3_styles['vectone-v3-home']   = array( 'css/home.css', array( $v4_mode ? 'vectone-v3-tokens' : 'vectone-v3-hero' ) );
        $v3_styles['vectone-v3-loader'] = array( 'css/loader.css', array( 'vectone-v3-home' ) );
        $v3_styles['vectone-v3-mobile-hero'] = array( 'css/mobile-hero.css', array( 'vectone-v3-loader' ) );
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


    $shared_components = $v3_base . 'css/shared-home-components.css';
    wp_enqueue_style( 'vectone-shared-home-components', $v3_uri . 'css/shared-home-components.css', array( 'vectone-v3-foundation' ), file_exists( $shared_components ) ? filemtime( $shared_components ) : null );

    $signal_motion_css = $v3_base . 'css/signal-motion.css';
    wp_enqueue_style( 'vectone-signal-motion', $v3_uri . 'css/signal-motion.css', array( 'vectone-v3-foundation' ), file_exists( $signal_motion_css ) ? filemtime( $signal_motion_css ) : null );
    $signal_motion_js = $v3_base . 'js/signal-motion.js';
    wp_enqueue_script( 'vectone-signal-motion', $v3_uri . 'js/signal-motion.js', is_front_page() ? array( 'vectone-v3-app' ) : array(), file_exists( $signal_motion_js ) ? filemtime( $signal_motion_js ) : null, true );

    // This script only repairs editorial post thumbnails. Do not load it on
    // pages that cannot contain the target news widget.
    $news_image_context = is_home() || is_singular( 'post' ) || vectone_operator_archive_page();
    if ( $news_image_context ) {
        $shared_news_images = $v3_base . 'js/shared-news-images.js';
        wp_enqueue_script( 'vectone-shared-news-images', $v3_uri . 'js/shared-news-images.js', array(), file_exists( $shared_news_images ) ? filemtime( $shared_news_images ) : null, true );
        wp_localize_script( 'vectone-shared-news-images', 'VectoneSharedNews', array(
            'free' => $v3_uri . 'images/news-free-hero.webp',
            'sosh' => $v3_uri . 'images/news-sosh-hero.webp',
            'international' => $v3_uri . 'images/news-international-hero.webp',
        ) );
    }

    $topbar_css = $v3_base . 'css/topbar.css';
    wp_enqueue_style( 'vectone-topbar', $v3_uri . 'css/topbar.css', array( 'parent-style' ), file_exists( $topbar_css ) ? filemtime( $topbar_css ) : null );
    $topbar_js = $v3_base . 'js/topbar.js';
    wp_enqueue_script( 'vectone-topbar', $v3_uri . 'js/topbar.js', array(), file_exists( $topbar_js ) ? filemtime( $topbar_js ) : null, true );
    if ( is_front_page() ) {
        $hero_cta = $v3_base . 'js/hero-cta-only.js';
        wp_enqueue_script( 'vectone-hero-cta-only', $v3_uri . 'js/hero-cta-only.js', array(), file_exists( $hero_cta ) ? filemtime( $hero_cta ) : null, true );
    }
    $i18n = $v3_base . 'js/i18n.js';
    wp_enqueue_script( 'vectone-dynamic-i18n', $v3_uri . 'js/i18n.js', array(), file_exists( $i18n ) ? filemtime( $i18n ) : null, true );

    if ( is_front_page() ) {
        $app = $v3_base . 'js/app.js';
        wp_enqueue_script(
            'vectone-v3-app',
            $v3_uri . 'js/app.js',
            array( 'vectone-dynamic-i18n' ),
            file_exists( $app ) ? filemtime( $app ) : null,
            true
        );

        $compare = $v3_base . 'js/compare.js';
        wp_enqueue_script(
            'vectone-v3-compare',
            $v3_uri . 'js/compare.js',
            array( 'vectone-v3-app', 'vectone-dynamic-i18n' ),
            file_exists( $compare ) ? filemtime( $compare ) : null,
            true
        );

        $site_content = function_exists( 'vectone_site_content' ) ? vectone_site_content() : array();
        $hero_image_id = absint( $site_content['home']['hero_image_id'] ?? 0 );
        $hero_image_url = $hero_image_id ? wp_get_attachment_image_url( $hero_image_id, 'full' ) : '';
        wp_localize_script(
            'vectone-v3-app',
            'VectoneV3',
            array(
                'heroFallback' => $v3_uri . 'images/hero-prixtel-60go-premium.webp',
                'heroSafetyFallback' => $v3_uri . 'images/hero-fallback.webp',
            'heroImageUrl' => $hero_image_url,
                'newsFree' => $v3_uri . 'images/news-free-hero.webp',
                'newsSosh' => $v3_uri . 'images/news-sosh-hero.webp',
                'newsInternational' => $v3_uri . 'images/news-international-hero.webp',
                'contactUrl' => home_url( '/#contact' ),
                'siteContent' => $site_content,
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
        $mobile_hero = $v3_base . 'js/mobile-hero.js';
        wp_enqueue_script( 'vectone-v3-mobile-hero', $v3_uri . 'js/mobile-hero.js', array( 'vectone-v3-loader' ), file_exists( $mobile_hero ) ? filemtime( $mobile_hero ) : null, true );
    }

    if ( is_page( array( 'forfaits-data-illimitee', 'forfaits-low-cost', 'forfaits-internationaux' ) ) ) {
        $category_css = $v3_base . 'css/category-pages.css';
        wp_enqueue_style( 'vectone-category-pages', $v3_uri . 'css/category-pages.css', array( 'vectone-v3-header' ), file_exists( $category_css ) ? filemtime( $category_css ) : null );
    }

    if ( is_page( 'forfaits-data-illimitee' ) ) {
        $data_css = $v3_base . 'css/data-page.css';
        wp_enqueue_style( 'vectone-data-page', $v3_uri . 'css/data-page.css', array( 'vectone-v3-header' ), file_exists( $data_css ) ? filemtime( $data_css ) : null );
    }

    if ( is_page( 'forfaits-low-cost' ) ) {
        $low_css = $v3_base . 'css/low-cost-page.css';
        wp_enqueue_style( 'vectone-low-cost-page', $v3_uri . 'css/low-cost-page.css', array( 'vectone-v3-header' ), file_exists( $low_css ) ? filemtime( $low_css ) : null );
    }

    if ( is_page( 'forfaits-internationaux' ) ) {
        $international_css = $v3_base . 'css/international-page.css';
        wp_enqueue_style( 'vectone-international-page', $v3_uri . 'css/international-page.css', array( 'vectone-v3-header' ), file_exists( $international_css ) ? filemtime( $international_css ) : null );
    }

    if ( function_exists( 'is_product_category' ) && is_product_category( 201 ) ) {
        $sans_css = $v3_base . 'css/sans-engagement.css';
        wp_enqueue_style( 'vectone-sans-engagement', $v3_uri . 'css/sans-engagement.css', array( 'vectone-v3-header' ), file_exists( $sans_css ) ? filemtime( $sans_css ) : null );
        $sans_offers_css = $v3_base . 'css/sans-engagement-offers.css';
        wp_enqueue_style( 'vectone-sans-engagement-offers', $v3_uri . 'css/sans-engagement-offers.css', array( 'vectone-sans-engagement' ), file_exists( $sans_offers_css ) ? filemtime( $sans_offers_css ) : null );
        $sans_guide_css = $v3_base . 'css/sans-engagement-guide.css';
        wp_enqueue_style( 'vectone-sans-engagement-guide', $v3_uri . 'css/sans-engagement-guide.css', array( 'vectone-sans-engagement-offers' ), file_exists( $sans_guide_css ) ? filemtime( $sans_guide_css ) : null );
    }

    if ( function_exists( 'is_product_category' ) && is_product_category( 202 ) ) {
        $avec_css = $v3_base . 'css/avec-engagement.css';
        wp_enqueue_style( 'vectone-avec-engagement', $v3_uri . 'css/avec-engagement.css', array( 'vectone-v3-header' ), file_exists( $avec_css ) ? filemtime( $avec_css ) : null );
    }

    if ( vectone_operator_archive_page() ) {
        $operator_css = $v3_base . 'css/operator-pages.css';
        wp_enqueue_style( 'vectone-operator-pages', $v3_uri . 'css/operator-pages.css', array( 'vectone-v3-header' ), file_exists( $operator_css ) ? filemtime( $operator_css ) : null );
    }

    if ( is_singular( 'post' ) ) {
        $article_css = $v3_base . 'css/article-pages.css';
        wp_enqueue_style( 'vectone-article-pages', $v3_uri . 'css/article-pages.css', array( 'vectone-v3-header' ), file_exists( $article_css ) ? filemtime( $article_css ) : null );
    }

    $category_pages = ( function_exists( 'is_product_category' ) && is_product_category( array( 201, 202 ) ) ) || is_page( array( 'forfaits-data-illimitee', 'forfaits-low-cost', 'forfaits-internationaux' ) );
    if ( $category_pages ) {
        $coherence_css = $v3_base . 'css/category-home-coherence.css';
        wp_enqueue_style( 'vectone-category-home-coherence', $v3_uri . 'css/category-home-coherence.css', array( 'vectone-v3-header' ), file_exists( $coherence_css ) ? filemtime( $coherence_css ) : null );
    }

    if ( $category_pages ) {
        $reviews_news_css = $v3_base . 'css/engagement-reviews-news.css';
        wp_enqueue_style( 'vectone-engagement-reviews-news', $v3_uri . 'css/engagement-reviews-news.css', array( 'vectone-category-home-coherence' ), file_exists( $reviews_news_css ) ? filemtime( $reviews_news_css ) : null );
    }

    if ( function_exists( 'is_product_category' ) && ( is_product_category( 201 ) || is_product_category( 202 ) ) ) {
        $category_news_wrapper = $v3_base . 'js/category-news-wrapper.js';
        wp_enqueue_script( 'vectone-category-news-wrapper', $v3_uri . 'js/category-news-wrapper.js', array(), file_exists( $category_news_wrapper ) ? filemtime( $category_news_wrapper ) : null, true );
    }

    if ( is_page( 'contact' ) ) {
        $contact_css = $v3_base . 'css/contact-concierge.css';
        wp_enqueue_style( 'vectone-contact-editorial-letter', $v3_uri . 'css/contact-concierge.css', array( 'parent-style' ), file_exists( $contact_css ) ? filemtime( $contact_css ) : null );
        $contact_js = $v3_base . 'js/contact-concierge.js';
        wp_enqueue_script( 'vectone-contact-editorial-letter', $v3_uri . 'js/contact-concierge.js', array(), file_exists( $contact_js ) ? filemtime( $contact_js ) : null, true );
    }

    if ( is_page( 4471 ) ) {
        $legal_css = $v3_base . 'css/legal-page.css';
        wp_enqueue_style( 'vectone-legal-page', $v3_uri . 'css/legal-page.css', array( 'vectone-v3-header' ), file_exists( $legal_css ) ? filemtime( $legal_css ) : null );
    }
    if ( is_page( 4473 ) ) {
        $privacy_css = $v3_base . 'css/privacy-page.css';
        wp_enqueue_style( 'vectone-privacy-page', $v3_uri . 'css/privacy-page.css', array( 'vectone-v3-header' ), file_exists( $privacy_css ) ? filemtime( $privacy_css ) : null );
    }

    $hub_css = $v3_base . 'css/compare-hub.css';
    wp_enqueue_style( 'vectone-compare-hub', $v3_uri . 'css/compare-hub.css', array( $v4_mode ? 'vectone-v3-tokens' : 'vectone-v3-header' ), file_exists( $hub_css ) ? filemtime( $hub_css ) : null );

    $hub_js = $v3_base . 'js/compare-hub.js';
    wp_enqueue_script( 'vectone-compare-hub', $v3_uri . 'js/compare-hub.js', array( 'vectone-dynamic-i18n' ), file_exists( $hub_js ) ? filemtime( $hub_js ) : null, true );
    wp_localize_script( 'vectone-compare-hub', 'VectoneCompareHub', vectone_compare_hub_urls() );

    $offcanvas_css = $v3_base . 'css/offcanvas-close.css';
    wp_enqueue_style( 'vectone-offcanvas-close', $v3_uri . 'css/offcanvas-close.css', array( $v4_mode ? 'vectone-v3-tokens' : 'vectone-v3-header' ), file_exists( $offcanvas_css ) ? filemtime( $offcanvas_css ) : null );
    $offcanvas_js = $v3_base . 'js/offcanvas-close.js';
    wp_enqueue_script( 'vectone-offcanvas-close', $v3_uri . 'js/offcanvas-close.js', array(), file_exists( $offcanvas_js ) ? filemtime( $offcanvas_js ) : null, true );

    if ( vectone_v4_enabled() && is_front_page() ) {
        $v4_base = get_stylesheet_directory() . '/assets/vectone-v4/';
        $v4_uri  = get_stylesheet_directory_uri() . '/assets/vectone-v4/';
        wp_enqueue_style( 'vectone-v4-fonts', 'https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700;800&family=Manrope:wght@400;500;600;700&display=swap', array(), null );
        $v4_styles = array(
            'vectone-v4-tokens' => array( 'css/tokens.css', array( 'parent-style', 'vectone-v4-fonts' ) ),
            'vectone-v4-header' => array( 'css/header.css', array( 'vectone-v4-tokens' ) ),
            'vectone-v4-hero' => array( 'css/hero.css', array( 'vectone-v4-header' ) ),
            'vectone-v4-responsive' => array( 'css/responsive.css', array( 'vectone-v4-hero' ) ),
        );
        foreach ( $v4_styles as $handle => $data ) {
            $path = $v4_base . $data[0];
            wp_enqueue_style( $handle, $v4_uri . $data[0], $data[1], file_exists( $path ) ? filemtime( $path ) : null );
        }
        $v4_interactions = $v4_base . 'js/interactions.js';
        wp_enqueue_script( 'vectone-v4-interactions', $v4_uri . 'js/interactions.js', array( 'vectone-v3-app' ), file_exists( $v4_interactions ) ? filemtime( $v4_interactions ) : null, true );
    }
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
    if ( vectone_v4_enabled() ) {
        $classes[] = 'vectone-v4';
    }
    return $classes;
}

function vectone_v4_enabled() {
    return isset( $_GET['vectone_v4'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['vectone_v4'] ) );
}

add_action( 'partdo_after_main_header', 'vectone_v4_render_category_rail', 20 );
function vectone_v4_render_category_rail() {
    if ( ! vectone_v4_enabled() || ! is_front_page() ) return;
    $page_url = function( $slug ) {
        $page = get_page_by_path( $slug );
        return $page ? get_permalink( $page ) : '';
    };
    $term_url = function( $slug ) {
        $term = get_term_by( 'slug', $slug, 'product_cat' );
        return $term && ! is_wp_error( $term ) ? get_term_link( $term ) : '';
    };
    $items = array(
        array( 'icon' => '↗', 'label' => 'Sans engagement', 'url' => $term_url( 'forfaits-sans-engagement' ) ),
        array( 'icon' => '▣', 'label' => 'Avec engagement', 'url' => $term_url( 'forfaits-avec-engagement' ) ),
        array( 'icon' => '∞', 'label' => 'Data illimitée', 'url' => $page_url( 'forfaits-data-illimitee' ) ),
        array( 'icon' => '◉', 'label' => 'Low-cost', 'url' => $page_url( 'forfaits-low-cost' ) ),
        array( 'icon' => '◎', 'label' => 'International', 'url' => $page_url( 'forfaits-internationaux' ) ),
    );
    echo '<nav class="vectone-v4-category-rail" aria-label="Catégories de forfaits"><div class="vectone-v4-category-rail__inner">';
    foreach ( $items as $item ) {
        if ( ! $item['url'] ) continue;
        echo '<a class="vectone-v4-category-rail__item" href="' . esc_url( $item['url'] ) . '"><span class="vectone-v4-category-rail__icon" aria-hidden="true">' . esc_html( $item['icon'] ) . '</span><span>' . esc_html( $item['label'] ) . '</span></a>';
    }
    echo '</div></nav>';
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
        <div class="vm-v3-loader__panel vm-v3-loader__panel--left"></div>
        <div class="vm-v3-loader__panel vm-v3-loader__panel--right"></div>
        <div class="vm-v3-loader__inner">
            <div class="vm-v3-loader__signal" aria-hidden="true"><i></i><i></i><i></i></div>
            <div class="vm-v3-loader__connection" aria-hidden="true"></div>
            <div class="vm-v3-loader__brand">VECTONE MOBILE</div>
            <div class="vm-v3-loader__tagline">COMPAREZ. CHOISISSEZ. CONNECTEZ.</div>
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
