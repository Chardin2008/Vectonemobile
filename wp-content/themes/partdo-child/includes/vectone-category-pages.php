<?php
if ( ! function_exists( 'wc_get_products' ) ) { return; }

function vectone_cat_text( $p ) {
    $parts = array( $p->get_name() );
    foreach ( $p->get_attributes() as $a ) {
        $v = $a->is_taxonomy() ? wc_get_product_terms( $p->get_id(), $a->get_name(), array( 'fields' => 'names' ) ) : $a->get_options();
        $parts[] = implode( ' ', $v );
    }
    return strtolower( wp_strip_all_tags( implode( ' ', $parts ) ) );
}
function vectone_cat_gb( $p ) {
    return preg_match( '/(\d+(?:[,.]\d+)?)\s*(?:go|gb)\b/i', vectone_cat_text( $p ), $m ) ? (float) str_replace( ',', '.', $m[1] ) : 0;
}
function vectone_cat_operator( $p ) {
    foreach ( $p->get_attributes() as $a ) {
        if ( false !== stripos( $a->get_name(), 'operateur' ) ) {
            $v = $a->is_taxonomy() ? wc_get_product_terms( $p->get_id(), $a->get_name(), array( 'fields' => 'names' ) ) : $a->get_options();
            return implode( ', ', $v );
        }
    }
    return preg_match( '/(?:–|-|—)\s*([^|]+)/u', $p->get_name(), $m ) ? trim( $m[1] ) : '';
}
function vectone_cat_card( $p ) {
    $url = $p->is_type( 'external' ) ? $p->get_product_url() : $p->get_permalink();
    $attrs = array();
    foreach ( $p->get_attributes() as $a ) {
        $v = $a->is_taxonomy() ? wc_get_product_terms( $p->get_id(), $a->get_name(), array( 'fields' => 'names' ) ) : $a->get_options();
        if ( $v && count( $attrs ) < 4 ) $attrs[] = wc_attribute_label( $a->get_name() ) . ': ' . implode( ', ', $v );
    }
    ob_start(); ?>
<article class="vm-category-offer"><div class="vm-category-offer__media"><?php echo wp_kses_post( $p->get_image( 'woocommerce_thumbnail' ) ); ?></div><div class="vm-category-offer__body"><?php if ( vectone_cat_operator( $p ) ) : ?><span class="vm-category-offer__operator"><?php echo esc_html( vectone_cat_operator( $p ) ); ?></span><?php endif; ?><h2><?php echo esc_html( $p->get_name() ); ?></h2><div class="vm-category-offer__attributes"><?php foreach ( $attrs as $a ) : ?><span><?php echo esc_html( $a ); ?></span><?php endforeach; ?></div></div><div class="vm-category-offer__action"><div class="vm-category-offer__price"><?php echo wp_kses_post( $p->get_price_html() ); ?></div><a class="vm-category-offer__cta" href="<?php echo esc_url( $url ); ?>"<?php echo $p->is_type( 'external' ) ? ' target="_blank" rel="sponsored noopener noreferrer"' : ''; ?>><?php echo esc_html( vectone_t( 'Voir l’offre', 'View offer' ) ); ?> <span aria-hidden="true">→</span></a></div></article>
    <?php return ob_get_clean();
}
function vectone_category_page( $type ) {
    $c = array(
        'data' => array( 'eyebrow' => 'DATA', 'title' => 'Forfaits avec un maximum de data', 'sub' => 'Comparez les forfaits offrant le plus de data parmi les offres disponibles.', 'intro' => 'Quand le volume compte, comparez les données réellement incluses dans les offres disponibles.', 'advice' => 'Vérifiez le volume inclus et les usages associés dans chaque fiche WooCommerce.' ),
        'low' => array( 'eyebrow' => 'BUDGET', 'title' => 'Forfaits mobiles à petit prix', 'sub' => 'Comparez les offres les plus accessibles actuellement disponibles.', 'intro' => 'Retrouvez les offres classées par prix réellement affiché, avec leurs informations utiles.', 'advice' => 'Comparez le prix avec la data, les appels et les conditions visibles dans chaque fiche.' ),
        'international' => array( 'eyebrow' => 'INTERNATIONAL', 'title' => 'Forfaits pour rester connecté à l’international', 'sub' => 'Comparez les offres proposant des usages adaptés aux déplacements et communications internationales.', 'intro' => 'Les offres sont sélectionnées à partir des informations réellement disponibles dans le catalogue.', 'advice' => 'Vérifiez toujours les destinations et usages internationaux explicitement indiqués sur la fiche.' ),
    );
    $products = wc_get_products( array( 'status' => 'publish', 'limit' => -1, 'return' => 'objects' ) );
    if ( 'data' === $type ) usort( $products, function( $a, $b ) { return vectone_cat_gb( $b ) <=> vectone_cat_gb( $a ); } );
    if ( 'low' === $type ) usort( $products, function( $a, $b ) { return (float) $a->get_price() <=> (float) $b->get_price(); } );
    if ( 'international' === $type ) { $m = array_filter( $products, function( $p ) { return (bool) preg_match( '/international|étranger|monde|hors france/u', vectone_cat_text( $p ) ); } ); if ( $m ) $products = array_values( $m ); }
    $products = array_slice( array_filter( $products, function( $p ) { return $p->is_visible(); } ), 0, 6 );
    ob_start(); ?>
    <main class="vm-category-page vm-category-page--<?php echo esc_attr( $type ); ?>"><section class="vm-category-hero"><span class="vm-category-eyebrow"><?php echo esc_html( $c[$type]['eyebrow'] ); ?></span><h2><?php echo esc_html( $c[$type]['title'] ); ?></h2><p><?php echo esc_html( $c[$type]['sub'] ); ?></p></section><section class="vm-category-intro"><p><?php echo esc_html( $c[$type]['intro'] ); ?></p></section><section class="vm-category-results"><span class="vm-category-eyebrow">LES OFFRES</span><h2>Choisissez selon vos besoins.</h2><div class="vm-category-offers"><?php foreach ( $products as $p ) echo vectone_cat_card( $p ); ?></div></section><section class="vm-category-advice"><span class="vm-category-eyebrow">À REGARDER</span><h2>Les bons critères avant de choisir.</h2><p><?php echo esc_html( $c[$type]['advice'] ); ?></p></section><section class="vm-category-cta"><h2>Envie de comparer toutes les offres ?</h2><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Comparer tous les forfaits <span aria-hidden="true">→</span></a></section></main>
    <?php return ob_get_clean();
}
function vectone_data_offers_shortcode() { return vectone_category_page( 'data' ); }
function vectone_budget_offers_shortcode() { return vectone_category_page( 'low' ); }
function vectone_international_offers_shortcode() { return vectone_category_page( 'international' ); }
add_shortcode( 'vectone_data_offers', 'vectone_data_offers_shortcode' );
add_shortcode( 'vectone_budget_offers', 'vectone_budget_offers_shortcode' );
add_shortcode( 'vectone_international_offers', 'vectone_international_offers_shortcode' );

/**
 * Editorial framing for the real WooCommerce archive only.
 * The product loop, sorting, filters and pagination remain theme/WooCommerce-owned.
 */
function vectone_sans_engagement_page() {
    return function_exists( 'is_product_category' ) && is_product_category( 201 );
}

function vectone_sans_admin_value( $key, $fallback = '' ) {
    $content = function_exists( 'vectone_site_content' ) ? vectone_site_content() : array();
    $value = $content['categories']['sans'][ $key ] ?? '';
    return '' !== trim( (string) $value ) ? $value : $fallback;
}

function vectone_sans_admin_faq() {
    $content = function_exists( 'vectone_site_content' ) ? vectone_site_content() : array();
    $items = array_filter( (array) ( $content['categories']['sans']['faq'] ?? array() ), function( $row ) { return ! empty( $row['question'] ) && ! empty( $row['active'] ); } );
    if ( ! $items ) return array();
    usort( $items, function( $a, $b ) { return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 ); } );
    return array_slice( $items, 0, 10 );
}

function vectone_sans_engagement_hero() {
    if ( ! vectone_sans_engagement_page() ) return;
    ob_start();
    ?>
    <section class="vm-sans-hero" aria-labelledby="vm-sans-title">
        <div class="vm-sans-hero__content">
            <span class="vm-category-eyebrow"><?php echo esc_html( vectone_sans_admin_value( 'eyebrow', 'LIBRE DE CHANGER' ) ); ?></span>
            <h1 id="vm-sans-title"><?php echo esc_html( vectone_sans_admin_value( 'title', 'Les forfaits sans engagement' ) ); ?></h1>
            <p>Comparez les offres mobiles sans durée minimale d’engagement et trouvez le forfait qui correspond réellement à vos usages et à votre budget.</p>
            <div class="vm-sans-hero__actions">
                <a class="vm-sans-button vm-sans-button--primary" href="#vm-sans-offers">Voir les offres <span aria-hidden="true">→</span></a>
                <a class="vm-sans-button vm-sans-button--secondary" href="#vectone-guide-sans-engagement">Comment choisir ?</a>
            </div>
        </div>
        <div class="vm-sans-hero__visual"><img class="vm-sans-hero__image" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/vectone-v3/images/forfaits-sans-engagement-hero.png' ); ?>" width="1448" height="1086" loading="eager" alt="" aria-hidden="true"></div>
    </section>
    <section class="vm-sans-benefits" aria-labelledby="vm-sans-benefits-title">
        <div class="vm-sans-section-heading"><span class="vm-category-eyebrow">EN TOUTE SIMPLICITÉ</span><h2 id="vm-sans-benefits-title">Pourquoi choisir un forfait sans engagement ?</h2></div>
        <div class="vm-sans-benefits__grid">
            <article><span class="vm-sans-number">01</span><h3>Liberté</h3><p>Vous gardez la possibilité de changer d’offre lorsque vos besoins évoluent.</p></article>
            <article><span class="vm-sans-number">02</span><h3>Budget</h3><p>Comparez facilement les prix et choisissez une formule adaptée à votre consommation.</p></article>
            <article><span class="vm-sans-number">03</span><h3>Flexibilité</h3><p>Plus ou moins de data, 5G ou services supplémentaires : recherchez l’offre adaptée au moment présent.</p></article>
        </div>
    </section>
    <?php
    $html = ob_get_clean();
    $html = str_replace( 'Comparez les offres mobiles sans durÃ©e minimale dâ€™engagement et trouvez le forfait qui correspond rÃ©ellement Ã  vos usages et Ã  votre budget.', esc_html( vectone_sans_admin_value( 'intro', 'Comparez les offres mobiles sans durée minimale d’engagement et trouvez le forfait qui correspond réellement à vos usages et à votre budget.' ) ), $html );
    echo $html;
}
add_action( 'woocommerce_before_main_content', 'vectone_sans_engagement_hero', 20 );

function vectone_sans_engagement_loop_intro() {
    if ( ! vectone_sans_engagement_page() ) return;
    echo '<div id="vm-sans-offers" class="vm-sans-loop-intro"><span class="vm-category-eyebrow">LES OFFRES</span><h2>Comparez les forfaits disponibles</h2><p>Les prix, volumes de data et services proviennent directement des offres actuellement référencées sur Vectone.</p></div>';
}
add_action( 'woocommerce_before_shop_loop', 'vectone_sans_engagement_loop_intro', 5 );

function vectone_sans_admin_loop_buffer_start() { if ( vectone_sans_engagement_page() ) ob_start(); }
function vectone_sans_admin_loop_buffer_end() {
    if ( ! vectone_sans_engagement_page() || ! ob_get_level() ) return;
    $html = ob_get_clean();
    $html = str_replace( 'Comparez les forfaits disponibles', esc_html( vectone_sans_admin_value( 'offers_title', 'Comparez les forfaits disponibles' ) ), $html );
    $html = str_replace( 'Les prix, volumes de data et services proviennent directement des offres actuellement rÃ©fÃ©rencÃ©es sur Vectone.', esc_html( vectone_sans_admin_value( 'offers_sub', 'Les prix, volumes de data et services proviennent directement des offres actuellement référencées sur Vectone.' ) ), $html );
    echo $html;
}
add_action( 'woocommerce_before_shop_loop', 'vectone_sans_admin_loop_buffer_start', 1 );
add_action( 'woocommerce_before_shop_loop', 'vectone_sans_admin_loop_buffer_end', 99 );

function vectone_sans_engagement_after_loop() {
    if ( ! vectone_sans_engagement_page() ) return;
    ob_start();
    ?>
    <section id="vectone-guide-sans-engagement" class="vm-sans-guide" aria-labelledby="vm-sans-guide-title">
        <span class="vm-category-eyebrow">GUIDE DE CHOIX</span><h2 id="vm-sans-guide-title">Quel forfait sans engagement choisir ?</h2>
        <p class="vm-sans-guide__intro">Le bon forfait dépend surtout de votre consommation de data, de votre budget et des services dont vous avez réellement besoin.</p>
        <div class="vm-sans-guide__profiles">
            <article><h3>Petit usage</h3><p>Wi-Fi fréquent, appels et messagerie : privilégiez d’abord le prix et un volume de data adapté.</p></article>
            <article><h3>Usage quotidien</h3><p>Réseaux sociaux, musique, navigation et vidéo occasionnelle : cherchez un équilibre entre data et prix.</p></article>
            <article><h3>Gros consommateur</h3><p>Streaming, partage de connexion et déplacements fréquents : comparez en priorité les volumes de data élevés et les conditions d’usage.</p></article>
        </div>
        <aside class="vm-sans-advice"><h3>Avant de choisir</h3><p>Ne regardez pas uniquement le nombre de gigas. Vérifiez également le réseau proposé, les services inclus et les conditions détaillées de l’opérateur avant de souscrire.</p></aside>
    </section>
    <section class="vm-sans-faq" aria-labelledby="vm-sans-faq-title"><span class="vm-category-eyebrow">À SAVOIR</span><h2 id="vm-sans-faq-title">Questions fréquentes</h2>
        <details><summary>Qu’est-ce qu’un forfait sans engagement ?</summary><p>Il s’agit d’une offre mobile qui n’impose pas une durée minimale d’engagement contractuel. Les conditions exactes restent celles indiquées par chaque opérateur.</p></details>
        <details><summary>Puis-je garder mon numéro de téléphone ?</summary><p>Dans de nombreux cas, la portabilité permet de conserver son numéro lors d’un changement d’opérateur. Vérifiez les modalités de l’offre choisie avant la souscription.</p></details>
        <details><summary>Comment choisir le bon volume de data ?</summary><p>Basez-vous sur votre consommation habituelle : navigation, réseaux sociaux, streaming, partage de connexion et usage du Wi-Fi.</p></details>
        <details><summary>Le sans engagement est-il toujours moins cher ?</summary><p>Pas nécessairement. Le prix dépend de l’opérateur, de la data et des services inclus. L’intérêt principal du sans engagement est surtout la flexibilité.</p></details>
        <details><summary>Les offres affichées sont-elles identiques chez tous les opérateurs ?</summary><p>Non. Les volumes de data, réseaux, tarifs et conditions peuvent varier. Consultez toujours les détails de l’offre avant de souscrire.</p></details>
    </section>
    <section class="vm-sans-final-cta" aria-labelledby="vm-sans-final-title"><div><h2 id="vm-sans-final-title">Prêt à trouver votre prochain forfait ?</h2><p>Comparez les offres disponibles et choisissez celle qui correspond le mieux à vos usages.</p></div><div><a class="vm-sans-button vm-sans-button--primary" href="#vm-sans-offers">Comparer les offres <span aria-hidden="true">→</span></a><a class="vm-sans-contact" href="mailto:contact@vectonemobile.fr">Besoin d’aide ?</a></div></section>
    <?php
    $html = ob_get_clean();
    $replace = array(
        'Questions frÃ©quentes' => vectone_sans_admin_value( 'faq_title', 'Questions fréquentes' ),
        'Ce quâ€™il faut savoir avant de choisir.' => vectone_sans_admin_value( 'news_title', 'Ce qu’il faut savoir avant de choisir.' ),
        'PrÃªt Ã  trouver votre prochain forfait ?' => vectone_sans_admin_value( 'final_title', 'Prêt à trouver votre prochain forfait ?' ),
        'Comparez les offres disponibles et choisissez celle qui correspond le mieux Ã  vos usages.' => vectone_sans_admin_value( 'final_text', 'Comparez les offres disponibles et choisissez celle qui correspond le mieux à vos usages.' ),
        'Comparer les offres' => vectone_sans_admin_value( 'cta_text', 'Comparer les offres' ),
    );
    foreach ( $replace as $from => $to ) $html = str_replace( $from, esc_html( $to ), $html );
    $final_url = vectone_sans_admin_value( 'final_url', '#vm-sans-offers' );
    $html = preg_replace( '/(<section class="vm-sans-final-cta".*?<a class="vm-sans-button vm-sans-button--primary" href=")[^"]*(".*?>)/si', '$1' . esc_url( $final_url ) . '$2', $html, 1 );
    $items = vectone_sans_admin_faq();
    if ( $items ) {
        $faq = '<section class="vm-sans-faq" aria-labelledby="vm-sans-faq-title"><span class="vm-category-eyebrow">À SAVOIR</span><h2 id="vm-sans-faq-title">' . esc_html( vectone_sans_admin_value( 'faq_title', 'Questions fréquentes' ) ) . '</h2>';
        foreach ( $items as $item ) $faq .= '<details><summary>' . esc_html( $item['question'] ) . '</summary><p>' . esc_html( $item['answer'] ?? '' ) . '</p></details>';
        $faq .= '</section>';
        $html = preg_replace( '/<section class="vm-sans-faq".*?<\/section>/si', $faq, $html, 1 );
    }
    echo $html;
}
add_action( 'woocommerce_after_shop_loop', 'vectone_sans_engagement_after_loop', 20 );

function vectone_shared_news_markup() {
    $posts = get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 3, 'orderby' => 'date', 'order' => 'DESC' ) );
    if ( ! $posts ) return '';
    ob_start(); ?>
    <section class="vm-category-news" aria-labelledby="vm-category-news-title">
        <div class="vm-category-heading"><span class="vm-category-eyebrow">ACTUALITÉS • GUIDES • BONS PLANS</span><h2 id="vm-category-news-title">Ce qu’il faut savoir avant de choisir.</h2><p>Décryptages et conseils pour mieux comprendre les offres mobiles.</p></div>
        <div class="vm-category-news__grid">
            <?php foreach ( $posts as $post ) : setup_postdata( $post ); ?>
                <article class="vm-category-news__card">
                    <a class="vm-category-news__image" href="<?php the_permalink(); ?>"><?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large', array( 'loading' => 'lazy', 'alt' => '' ) ); } ?></a>
                    <div class="vm-category-news__body"><span class="vm-category-news__meta"><?php echo esc_html( get_the_date() ); ?></span><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><a class="vm-category-news__link" href="<?php the_permalink(); ?>">Lire l’article <span aria-hidden="true">→</span></a></div>
                </article>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
    </section>
    <?php
    $html = ob_get_clean();
    if ( vectone_sans_engagement_page() ) {
        $html = str_replace( 'Ce quâ€™il faut savoir avant de choisir.', esc_html( vectone_sans_admin_value( 'news_title', 'Ce qu’il faut savoir avant de choisir.' ) ), $html );
        $html = str_replace( 'DÃ©cryptages et conseils pour mieux comprendre les offres mobiles.', esc_html( vectone_sans_admin_value( 'news_text', 'Décryptages et conseils pour mieux comprendre les offres mobiles.' ) ), $html );
    }
    return $html;
}

function vectone_shared_reviews_markup() {
    $home_ids = array_filter( array_unique( array( (int) get_option( 'page_on_front' ), 4394 ) ) );
    $tree = array();
    foreach ( $home_ids as $home_id ) {
        $data = get_post_meta( $home_id, '_elementor_data', true );
        $candidate = $data ? json_decode( $data, true ) : array();
        if ( is_array( $candidate ) && $candidate ) { $tree = $candidate; break; }
    }
    if ( ! $tree ) return '';
    $items = array();
    $walk = function( $nodes ) use ( &$walk, &$items ) {
        foreach ( (array) $nodes as $node ) {
            if ( isset( $node['widgetType'] ) && false !== strpos( $node['widgetType'], 'testimonial' ) && ! empty( $node['settings'] ) ) {
                $scan = function( $values ) use ( &$scan, &$items ) {
                    foreach ( (array) $values as $key => $value ) {
                        if ( is_array( $value ) ) {
                            $text = isset( $value['content'] ) ? wp_strip_all_tags( $value['content'] ) : ( isset( $value['text'] ) ? wp_strip_all_tags( $value['text'] ) : '' );
                            $name = isset( $value['name'] ) ? wp_strip_all_tags( $value['name'] ) : ( isset( $value['customer_name'] ) ? wp_strip_all_tags( $value['customer_name'] ) : '' );
                            if ( $text && $name ) $items[] = array( 'text' => $text, 'name' => $name );
                            $scan( $value );
                        }
                    }
                };
                $scan( $node['settings'] );
            }
            if ( ! empty( $node['elements'] ) ) $walk( $node['elements'] );
        }
    };
    $walk( $tree );
    if ( ! $items && class_exists( '\\Elementor\\Plugin' ) ) {
        $html = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( 4394 );
        if ( $html ) {
            preg_match_all( '/<div[^>]*class="[^"]*customer-name[^"]*"[^>]*>(.*?)<\\/div>.*?<div[^>]*class="[^"]*customer-description[^"]*"[^>]*>.*?<p>(.*?)<\\/p>/si', $html, $matches, PREG_SET_ORDER );
            foreach ( $matches as $match ) {
                $name = wp_strip_all_tags( html_entity_decode( $match[1] ) );
                $text = wp_strip_all_tags( html_entity_decode( $match[2] ) );
                if ( $name && $text ) $items[] = array( 'text' => $text, 'name' => $name );
            }
        }
    }
    if ( ! $items ) return '';
    $items = array_slice( $items, 0, 4 );
    ob_start(); ?>
    <section class="vm-category-reviews" aria-labelledby="vm-category-reviews-title"><div class="vm-category-heading"><span class="vm-category-eyebrow">ILS NOUS FONT CONFIANCE</span><h2 id="vm-category-reviews-title">Ce que disent nos utilisateurs.</h2></div><div class="vm-category-reviews__grid">
        <?php foreach ( $items as $item ) : ?><article class="vm-category-review"><div class="vm-category-review__stars" aria-label="Avis utilisateur">★★★★★</div><p><?php echo esc_html( $item['text'] ); ?></p><strong><?php echo esc_html( $item['name'] ); ?></strong></article><?php endforeach; ?>
    </div></section>
    <?php
    $html = ob_get_clean();
    if ( vectone_sans_engagement_page() ) $html = str_replace( 'Ce que disent nos utilisateurs.', esc_html( vectone_sans_admin_value( 'reviews_title', 'Ce que disent nos utilisateurs.' ) ), $html );
    return $html;
}

/* Rolled-back reviews/news pilot: retained only as inert source history. */
function vectone_sans_engagement_shared_content() {
    if ( ! vectone_sans_engagement_page() ) return;
    echo vectone_shared_reviews_markup();
    echo vectone_shared_news_markup();
}

function vectone_avec_engagement_page() { return function_exists( 'is_product_category' ) && is_product_category( 202 ); }

function vectone_avec_admin_value( $key, $fallback = '' ) {
    $content = function_exists( 'vectone_site_content' ) ? vectone_site_content() : array();
    $value = $content['categories']['avec'][ $key ] ?? '';
    return '' !== trim( (string) $value ) ? $value : $fallback;
}

function vectone_avec_admin_faq() {
    $content = function_exists( 'vectone_site_content' ) ? vectone_site_content() : array();
    $items = array_filter( (array) ( $content['categories']['avec']['faq'] ?? $content['faq'] ?? array() ), function( $row ) { return ! empty( $row['question'] ) && ! empty( $row['active'] ); } );
    if ( ! $items ) return array();
    usort( $items, function( $a, $b ) { return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 ); } );
    return array_slice( $items, 0, 10 );
}

function vectone_avec_engagement_hero() {
    if ( ! vectone_avec_engagement_page() ) return;
    ob_start();
    ?>
    <section class="vm-sans-hero" aria-labelledby="vm-avec-title"><div class="vm-sans-hero__content"><span class="vm-category-eyebrow">PENSÉ POUR DURER</span><h1 id="vm-avec-title">Les forfaits avec engagement</h1><p>Comparez les offres mobiles avec durée d’engagement et trouvez une formule adaptée à vos usages, à votre budget et aux conditions proposées par chaque opérateur.</p><div class="vm-sans-hero__actions"><a class="vm-sans-button vm-sans-button--primary" href="#vm-avec-offers">Voir les offres <span aria-hidden="true">→</span></a><a class="vm-sans-button vm-sans-button--secondary" href="#vectone-guide-avec-engagement">Comment choisir ?</a></div></div><div class="vm-sans-hero__visual"><img class="vm-sans-hero__image" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/vectone-v3/images/forfaits-avec-engagement-hero.png' ); ?>" width="1448" height="1086" loading="eager" alt="" aria-hidden="true"></div></section>
    <section class="vm-sans-benefits" aria-labelledby="vm-avec-benefits-title"><div class="vm-sans-section-heading"><span class="vm-category-eyebrow">EN TOUTE SIMPLICITÉ</span><h2 id="vm-avec-benefits-title">Pourquoi choisir un forfait avec engagement ?</h2></div><div class="vm-sans-benefits__grid"><article><span class="vm-sans-number">01</span><h3>STABILITÉ</h3><p>Une formule avec engagement peut convenir si vous souhaitez conserver votre offre sur une durée définie.</p></article><article><span class="vm-sans-number">02</span><h3>SERVICES</h3><p>Comparez la data, le réseau et les services inclus afin de trouver une offre adaptée à vos usages.</p></article><article><span class="vm-sans-number">03</span><h3>CONDITIONS</h3><p>Vérifiez toujours la durée d’engagement ainsi que les modalités prévues par l’opérateur avant de souscrire.</p></article></div></section>
    <?php
    $html = ob_get_clean();
    $html = str_replace( 'PENSÃ‰ POUR DURER', esc_html( vectone_avec_admin_value( 'eyebrow', 'PENSÉ POUR DURER' ) ), $html );
    $html = str_replace( 'Les forfaits avec engagement', esc_html( vectone_avec_admin_value( 'title', 'Les forfaits avec engagement' ) ), $html );
    $html = str_replace( 'Comparez les offres mobiles avec durÃ©e dâ€™engagement et trouvez une formule adaptÃ©e Ã  vos usages, Ã  votre budget et aux conditions proposÃ©es par chaque opÃ©rateur.', esc_html( vectone_avec_admin_value( 'intro', 'Comparez les offres mobiles avec durée d’engagement et trouvez une formule adaptée à vos usages, à votre budget et aux conditions proposées par chaque opérateur.' ) ), $html );
    echo $html;
}
add_action( 'woocommerce_before_main_content', 'vectone_avec_engagement_hero', 20 );

function vectone_avec_engagement_empty_state() { if ( ! vectone_avec_engagement_page() ) return; $term = get_term( 202, 'product_cat' ); if ( ! $term || is_wp_error( $term ) || (int) $term->count !== 0 ) return; $title = vectone_avec_admin_value( 'empty_title', 'Aucune offre avec engagement référencée pour le moment' ); $text = vectone_avec_admin_value( 'empty_text', 'Vectone ne référence actuellement aucune offre disposant d’une durée d’engagement fiable dans son catalogue. Cette page sera mise à jour lorsque de nouvelles offres correspondantes seront disponibles.' ); $cta = vectone_avec_admin_value( 'empty_cta_text', 'Voir les forfaits sans engagement' ); $url = vectone_avec_admin_value( 'empty_url', home_url( '/categorie-produit/forfaits-sans-engagement/' ) ); echo '<div id="vm-avec-offers" class="vm-sans-loop-intro vm-avec-empty-intro"><span class="vm-category-eyebrow">LES OFFRES</span><h2>Comparez les forfaits avec engagement</h2><div class="vm-avec-empty-state"><span class="vm-avec-empty-state__signal" aria-hidden="true"></span><h3>' . esc_html( $title ) . '</h3><p>' . esc_html( $text ) . '</p><div class="vm-avec-empty-state__advice"><strong>En attendant, vous pouvez consulter les forfaits sans engagement disponibles actuellement.</strong><a class="vm-sans-button vm-sans-button--primary" href="' . esc_url( $url ) . '">' . esc_html( $cta ) . ' <span aria-hidden="true">→</span></a></div></div></div>'; }
add_action( 'woocommerce_before_main_content', 'vectone_avec_engagement_empty_state', 21 );

function vectone_avec_admin_before_buffer_start() { if ( vectone_avec_engagement_page() ) ob_start(); }
function vectone_avec_admin_before_buffer_end() {
    if ( ! vectone_avec_engagement_page() || ! ob_get_level() ) return;
    $html = ob_get_clean();
    $html = str_replace( 'Comparez les forfaits avec engagement', esc_html( vectone_avec_admin_value( 'offers_title', 'Comparez les forfaits avec engagement' ) ), $html );
    $html = str_replace( 'Les prix, volumes de data affichÃ©s proviennent directement des offres actuellement rÃ©fÃ©rencÃ©es sur Vectone.', esc_html( vectone_avec_admin_value( 'offers_sub', 'Les prix, volumes de data affichés proviennent directement des offres actuellement référencées sur Vectone.' ) ), $html );
    echo $html;
}
add_action( 'woocommerce_before_main_content', 'vectone_avec_admin_before_buffer_start', 1 );
add_action( 'woocommerce_before_main_content', 'vectone_avec_admin_before_buffer_end', 99 );

function vectone_avec_engagement_loop_intro() { if ( ! vectone_avec_engagement_page() ) return; $term = get_term( 202, 'product_cat' ); if ( $term && ! is_wp_error( $term ) && (int) $term->count === 0 ) return; echo '<div id="vm-avec-offers" class="vm-sans-loop-intro"><span class="vm-category-eyebrow">LES OFFRES</span><h2>Comparez les forfaits avec engagement</h2><p>Les prix, volumes de data et services affichés proviennent directement des offres actuellement référencées sur Vectone.</p></div>'; }
add_action( 'woocommerce_before_shop_loop', 'vectone_avec_engagement_loop_intro', 5 );

function vectone_avec_engagement_after_loop() {
    if ( ! vectone_avec_engagement_page() ) return;
    ob_start();
    ?>
    <section id="vectone-guide-avec-engagement" class="vm-sans-guide" aria-labelledby="vm-avec-guide-title"><span class="vm-category-eyebrow">GUIDE DE CHOIX</span><h2 id="vm-avec-guide-title">Comment choisir un forfait avec engagement ?</h2><p class="vm-sans-guide__intro">Le bon choix dépend de vos usages, des services inclus et surtout des conditions d’engagement prévues par l’opérateur.</p><div class="vm-sans-guide__profiles"><article><h3>BESOIN STABLE</h3><p>Si vos usages évoluent peu, comparez les offres capables de répondre durablement à votre consommation habituelle.</p></article><article><h3>SERVICES</h3><p>Ne regardez pas uniquement le prix : data, réseau, appels, options et services inclus peuvent faire la différence.</p></article><article><h3>DURÉE</h3><p>Vérifiez toujours la durée d’engagement et les modalités de changement ou de résiliation avant de souscrire.</p></article></div><aside class="vm-sans-advice"><h3>Avant de vous engager</h3><p>Prenez le temps de vérifier la durée du contrat, les services inclus et les conditions détaillées de l’opérateur. Une offre intéressante doit correspondre à vos usages pendant toute la durée prévue.</p></aside></section>
    <section class="vm-sans-faq" aria-labelledby="vm-avec-faq-title"><span class="vm-category-eyebrow">À SAVOIR</span><h2 id="vm-avec-faq-title">Questions fréquentes</h2><details><summary>Qu’est-ce qu’un forfait avec engagement ?</summary><p>Il s’agit d’une offre mobile qui prévoit une durée minimale d’engagement. La durée et les conditions exactes dépendent de l’opérateur et de l’offre choisie.</p></details><details><summary>Combien de temps dure l’engagement ?</summary><p>La durée peut varier selon l’offre. Consultez toujours les conditions indiquées par l’opérateur avant de souscrire.</p></details><details><summary>Puis-je garder mon numéro de téléphone ?</summary><p>Dans de nombreux cas, la portabilité permet de conserver son numéro lors d’un changement d’opérateur. Vérifiez les modalités prévues pour l’offre choisie.</p></details><details><summary>Que faut-il vérifier avant de souscrire ?</summary><p>Regardez notamment la durée d’engagement, le prix, le volume de data, le réseau, les services inclus et les conditions de résiliation ou de changement.</p></details><details><summary>Un forfait avec engagement est-il toujours moins cher ?</summary><p>Non. Le prix dépend de l’offre, de l’opérateur et des services inclus. Comparez l’ensemble des conditions plutôt que le tarif seul.</p></details></section>
    <section class="vm-sans-final-cta" aria-labelledby="vm-avec-final-title"><div><h2 id="vm-avec-final-title">Prêt à comparer les offres avec engagement ?</h2><p>Parcourez les forfaits disponibles et choisissez une offre adaptée à vos usages et à vos attentes.</p></div><div><a class="vm-sans-button vm-sans-button--primary" href="#vm-avec-offers">Comparer les offres <span aria-hidden="true">→</span></a><a class="vm-sans-contact" href="mailto:contact@vectonemobile.fr">Besoin d’aide ?</a></div></section>
    <?php
    $html = ob_get_clean();
    $html = str_replace( 'Questions frÃ©quentes', esc_html( vectone_avec_admin_value( 'faq_title', 'Questions fréquentes' ) ), $html );
    $html = str_replace( 'PrÃªt Ã  comparer les offres avec engagement ?', esc_html( vectone_avec_admin_value( 'final_title', 'Prêt à comparer les offres avec engagement ?' ) ), $html );
    $html = str_replace( 'Parcourez les forfaits disponibles et choisissez une offre adaptÃ©e Ã  vos usages et Ã  vos attentes.', esc_html( vectone_avec_admin_value( 'final_text', 'Parcourez les forfaits disponibles et choisissez une offre adaptée à vos usages et à vos attentes.' ) ), $html );
    $html = str_replace( 'Comparer les offres', esc_html( vectone_avec_admin_value( 'cta_text', 'Comparer les offres' ) ), $html );
    $final_url = vectone_avec_admin_value( 'final_url', '#vm-avec-offers' );
    $html = preg_replace( '/(<section class="vm-sans-final-cta".*?<a class="vm-sans-button vm-sans-button--primary" href=")[^"]*(".*?>)/si', '$1' . esc_url( $final_url ) . '$2', $html, 1 );
    $items = vectone_avec_admin_faq();
    if ( $items ) {
        $faq = '<section class="vm-sans-faq" aria-labelledby="vm-avec-faq-title"><span class="vm-category-eyebrow">À SAVOIR</span><h2 id="vm-avec-faq-title">' . esc_html( vectone_avec_admin_value( 'faq_title', 'Questions fréquentes' ) ) . '</h2>';
        foreach ( $items as $item ) $faq .= '<details><summary>' . esc_html( $item['question'] ) . '</summary><p>' . esc_html( $item['answer'] ?? '' ) . '</p></details>';
        $faq .= '</section>';
        $html = preg_replace( '/<section class="vm-sans-faq".*?<\/section>/si', $faq, $html, 1 );
    }
    $avec_content = function_exists( 'vectone_site_content' ) ? vectone_site_content() : array();
    $avec_row = $avec_content['categories']['avec'] ?? array();
    $avec_items = array_values( array_filter( (array) ( $avec_row['benefits'] ?? array() ), function( $item ) { return ! empty( $item['title'] ) && ! empty( $item['active'] ); } ) );
    usort( $avec_items, function( $a, $b ) { return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 ); } );
    if ( $avec_items ) $html = preg_replace_callback( '/(<div class="vm-sans-benefits__grid">)(.*?)(<\/div>)/si', function( $block ) use ( $avec_items ) { $i = 0; $inner = preg_replace_callback( '/(<article[^>]*>.*?<h3>).*?(<\/h3>).*?(<p>).*?(<\/p>).*?(<\/article>)/si', function( $m ) use ( &$i, $avec_items ) { if ( ! isset( $avec_items[ $i ] ) ) return $m[0]; $item = $avec_items[ $i++ ]; return $m[1] . esc_html( $item['title'] ) . $m[2] . $m[3] . esc_html( $item['description'] ?? '' ) . $m[4] . $m[5]; }, $block[2] ); return $block[1] . $inner . $block[3]; }, $html, 1 );
    $avec_guide = array_values( array_filter( (array) ( $avec_row['guide'] ?? array() ), function( $item ) { return ! empty( $item['title'] ) && ! empty( $item['active'] ); } ) );
    usort( $avec_guide, function( $a, $b ) { return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 ); } );
    if ( $avec_guide ) $html = preg_replace_callback( '/(<div class="vm-sans-guide__profiles">)(.*?)(<\/div>)/si', function( $block ) use ( $avec_guide ) { $i = 0; $inner = preg_replace_callback( '/(<article[^>]*>.*?<h3>).*?(<\/h3>).*?(<p>).*?(<\/p>).*?(<\/article>)/si', function( $m ) use ( &$i, $avec_guide ) { if ( ! isset( $avec_guide[ $i ] ) ) return $m[0]; $item = $avec_guide[ $i++ ]; return $m[1] . esc_html( $item['title'] ) . $m[2] . $m[3] . esc_html( $item['description'] ?? ( $item['text'] ?? '' ) ) . $m[4] . $m[5]; }, $block[2] ); return $block[1] . $inner . $block[3]; }, $html, 1 );
    echo $html;
}
add_action( 'woocommerce_after_main_content', 'vectone_avec_engagement_after_loop', 20 );

function vectone_data_admin_value( $key, $fallback = '' ) {
    $content = function_exists( 'vectone_site_content' ) ? vectone_site_content() : array();
    $value = $content['categories']['data'][ $key ] ?? '';
    return '' !== trim( (string) $value ) ? $value : $fallback;
}

function vectone_data_admin_faq() {
    $content = function_exists( 'vectone_site_content' ) ? vectone_site_content() : array();
    $items = array_filter( (array) ( $content['categories']['data']['faq'] ?? $content['faq'] ?? array() ), function( $row ) { return ! empty( $row['question'] ) && ! empty( $row['active'] ); } );
    if ( ! $items ) return array();
    usort( $items, function( $a, $b ) { return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 ); } );
    return array_slice( $items, 0, 10 );
}

function vectone_data_admin_items( $group ) {
    $content = function_exists( 'vectone_site_content' ) ? vectone_site_content() : array();
    $items = array_filter( (array) ( $content['categories']['data'][ $group ] ?? array() ), function( $row ) { return ! empty( $row['title'] ) && ! empty( $row['active'] ); } );
    usort( $items, function( $a, $b ) { return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 ); } );
    return array_values( $items );
}

function vectone_data_page_v2() {
    $products = wc_get_products( array( 'status' => 'publish', 'limit' => 20, 'return' => 'objects' ) );
    $products = array_filter( $products, function( $p ) { return $p->is_visible() && vectone_cat_gb( $p ) > 0; } );
    usort( $products, function( $a, $b ) { return vectone_cat_gb( $b ) <=> vectone_cat_gb( $a ); } );
    $products = array_slice( $products, 0, 6 );
    ob_start(); ?>
    <main class="vm-data-page">
        <section class="vm-sans-hero" aria-labelledby="vm-data-title"><div class="vm-sans-hero__content"><span class="vm-category-eyebrow">POUR LES GROS USAGES</span><h1 id="vm-data-title">Les forfaits avec beaucoup de data</h1><p>Streaming, réseaux sociaux, navigation ou partage de connexion : comparez les offres disposant des volumes de data les plus élevés actuellement référencés sur Vectone.</p><div class="vm-sans-hero__actions"><a class="vm-sans-button vm-sans-button--primary" href="#vm-data-offers">Voir les offres <span aria-hidden="true">→</span></a><a class="vm-sans-button vm-sans-button--secondary" href="#vectone-guide-data">Comment choisir ?</a></div></div><div class="vm-sans-hero__visual"><img class="vm-sans-hero__image" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/vectone-v3/images/forfaits-data-hero.png' ); ?>" width="1448" height="1086" loading="eager" alt="" aria-hidden="true"></div></section>
        <section class="vm-sans-benefits" aria-labelledby="vm-data-benefits-title"><div class="vm-sans-section-heading"><span class="vm-category-eyebrow">EN TOUTE SIMPLICITÉ</span><h2 id="vm-data-benefits-title">Pourquoi regarder les forfaits avec beaucoup de data ?</h2></div><div class="vm-sans-benefits__grid"><article><span class="vm-sans-number">01</span><h3>STREAMING</h3><p>Un volume de data élevé est particulièrement utile si vous regardez régulièrement des vidéos ou écoutez de la musique en mobilité.</p></article><article><span class="vm-sans-number">02</span><h3>PARTAGE</h3><p>Le partage de connexion peut consommer rapidement votre enveloppe data lorsque plusieurs appareils utilisent votre téléphone comme point d’accès.</p></article><article><span class="vm-sans-number">03</span><h3>MOBILITÉ</h3><p>Si vous utilisez peu le Wi-Fi, comparez attentivement les volumes de data proposés par les différentes offres.</p></article></div></section>
        <section id="vm-data-offers" class="vm-sans-loop-intro"><span class="vm-category-eyebrow">LES OFFRES</span><h2>Comparez les forfaits les plus généreux en data</h2><p>Les volumes de data, prix et services affichés proviennent directement des offres actuellement référencées sur Vectone.</p><div class="vm-category-offers"><?php foreach ( $products as $p ) echo vectone_cat_card( $p ); ?></div></section>
        <section id="vectone-guide-data" class="vm-sans-guide" aria-labelledby="vm-data-guide-title"><span class="vm-category-eyebrow">GUIDE DE CHOIX</span><h2 id="vm-data-guide-title">Quel forfait data choisir ?</h2><p class="vm-sans-guide__intro">Le bon volume dépend surtout de votre consommation quotidienne, du temps passé en Wi-Fi et de vos usages multimédias.</p><div class="vm-sans-guide__profiles"><article><h3>USAGE MODÉRÉ</h3><p>Navigation, messagerie et réseaux sociaux légers ne nécessitent pas forcément l’enveloppe de data la plus élevée.</p></article><article><h3>MULTIMÉDIA</h3><p>Vidéo, musique et réseaux sociaux intensifs peuvent consommer rapidement plusieurs gigaoctets.</p></article><article><h3>PARTAGE DE CONNEXION</h3><p>Si votre téléphone sert régulièrement de point d’accès, comparez attentivement les volumes disponibles et les conditions de l’offre.</p></article></div><aside class="vm-sans-advice"><h3>Avant de choisir votre volume de data</h3><p>Plus de data n’est pas toujours synonyme de meilleur forfait. Comparez également le prix, le réseau, les services inclus et vos habitudes réelles de consommation.</p></aside></section>
        <section class="vm-sans-faq" aria-labelledby="vm-data-faq-title"><span class="vm-category-eyebrow">À SAVOIR</span><h2 id="vm-data-faq-title">Questions fréquentes</h2><details><summary>De combien de data ai-je besoin ?</summary><p>Cela dépend de vos usages. Navigation, réseaux sociaux, vidéo, musique et partage de connexion n’ont pas la même consommation.</p></details><details><summary>Le streaming consomme-t-il beaucoup de data ?</summary><p>Oui, la vidéo et certains usages multimédias peuvent consommer rapidement une enveloppe mobile, surtout lorsque la qualité est élevée.</p></details><details><summary>Le Wi-Fi réduit-il ma consommation mobile ?</summary><p>Lorsque votre téléphone utilise un réseau Wi-Fi, les données mobiles ne sont généralement pas utilisées pour cette connexion.</p></details><details><summary>Qu’est-ce que le partage de connexion ?</summary><p>Il permet d’utiliser la connexion mobile de votre téléphone pour connecter un autre appareil, comme un ordinateur ou une tablette.</p></details><details><summary>Les offres de cette page sont-elles réellement illimitées ?</summary><p>La page présente les offres disposant des volumes de data les plus élevés actuellement référencés. Vérifiez toujours les conditions exactes de chaque opérateur avant de souscrire.</p></details></section>
        <section class="vm-sans-final-cta" aria-labelledby="vm-data-final-title"><div><h2 id="vm-data-final-title">Besoin de plus de data au quotidien ?</h2><p>Comparez les offres disponibles et choisissez le volume adapté à vos usages.</p></div><div><a class="vm-sans-button vm-sans-button--primary" href="#vm-data-offers">Comparer les offres <span aria-hidden="true">→</span></a><a class="vm-sans-contact" href="mailto:contact@vectonemobile.fr">Besoin d’aide ?</a></div></section>
    </main>
    <?php
    $html = ob_get_clean();
    $replacements = array(
        'POUR LES GROS USAGES' => vectone_data_admin_value( 'eyebrow', 'POUR LES GROS USAGES' ),
        'Les forfaits avec beaucoup de data' => vectone_data_admin_value( 'title', 'Les forfaits avec beaucoup de data' ),
        'Streaming, rÃ©seaux sociaux, navigation ou partage de connexion : comparez les offres disposant des volumes de data les plus Ã©levÃ©s actuellement rÃ©fÃ©rencÃ©s sur Vectone.' => vectone_data_admin_value( 'intro', 'Streaming, réseaux sociaux, navigation ou partage de connexion : comparez les offres disposant des volumes de data les plus élevés actuellement référencés sur Vectone.' ),
        'Comparez les forfaits les plus gÃ©nÃ©reux en data' => vectone_data_admin_value( 'offers_title', 'Comparez les forfaits les plus généreux en data' ),
        'Les volumes de data, prix et services affichÃ©s proviennent directement des offres actuellement rÃ©fÃ©rencÃ©es sur Vectone.' => vectone_data_admin_value( 'offers_sub', 'Les volumes de data, prix et services affichés proviennent directement des offres actuellement référencées sur Vectone.' ),
        'Questions frÃ©quentes' => vectone_data_admin_value( 'faq_title', 'Questions fréquentes' ),
        'Besoin de plus de data au quotidien ?' => vectone_data_admin_value( 'final_title', 'Besoin de plus de data au quotidien ?' ),
        'Comparez les offres disponibles et choisissez le volume adaptÃ© Ã  vos usages.' => vectone_data_admin_value( 'final_text', 'Comparez les offres disponibles et choisissez le volume adapté à vos usages.' ),
        'Comparer les offres' => vectone_data_admin_value( 'cta_text', 'Comparer les offres' ),
    );
    foreach ( $replacements as $from => $to ) $html = str_replace( $from, esc_html( $to ), $html );
    foreach ( array( 'benefits', 'guide' ) as $group ) {
        $items = vectone_data_admin_items( $group );
        if ( ! $items ) continue;
        $wrapper = 'benefits' === $group ? 'vm-sans-benefits__grid' : 'vm-sans-guide__profiles';
        $html = preg_replace_callback( '/(<div class="' . preg_quote( $wrapper, '/' ) . '">)(.*?)(<\/div>)/si', function( $block ) use ( $items ) {
            $index = 0;
            $inner = preg_replace_callback( '/(<article[^>]*>.*?<h3>).*?(<\/h3>).*?(<p>).*?(<\/p>).*?(<\/article>)/si', function( $match ) use ( &$index, $items ) {
                if ( ! isset( $items[ $index ] ) ) return $match[0];
                $item = $items[ $index++ ];
                $description = $item['description'] ?? ( $item['text'] ?? '' );
                return $match[1] . esc_html( $item['title'] ) . $match[2] . $match[3] . esc_html( $description ) . $match[4] . $match[5];
            }, $block[2] );
            return $block[1] . $inner . $block[3];
        }, $html, 1 );
    }
    $final_url = vectone_data_admin_value( 'final_url', '#vm-data-offers' );
    $html = preg_replace( '/(<section class="vm-sans-final-cta".*?<a class="vm-sans-button vm-sans-button--primary" href=")[^"]*(".*?>)/si', '$1' . esc_url( $final_url ) . '$2', $html, 1 );
    $items = vectone_data_admin_faq();
    if ( $items ) {
        $faq = '<section class="vm-sans-faq" aria-labelledby="vm-data-faq-title"><span class="vm-category-eyebrow">À SAVOIR</span><h2 id="vm-data-faq-title">' . esc_html( vectone_data_admin_value( 'faq_title', 'Questions fréquentes' ) ) . '</h2>';
        foreach ( $items as $item ) $faq .= '<details><summary>' . esc_html( $item['question'] ) . '</summary><p>' . esc_html( $item['answer'] ?? '' ) . '</p></details>';
        $faq .= '</section>';
        $html = preg_replace( '/<section class="vm-sans-faq".*?<\/section>/si', $faq, $html, 1 );
    }
    echo $html;
}
add_shortcode( 'vectone_data_offers', 'vectone_data_page_v2' );

function vectone_low_cost_page_v2() {
    $products = wc_get_products( array( 'status' => 'publish', 'limit' => 20, 'return' => 'objects' ) );
    $products = array_filter( $products, function( $p ) { return $p->is_visible() && '' !== $p->get_price(); } );
    usort( $products, function( $a, $b ) { return (float) $a->get_price() <=> (float) $b->get_price(); } );
    $products = array_slice( $products, 0, 6 );
    ob_start(); ?>
    <main class="vm-data-page vm-low-cost-page">
        <section class="vm-sans-hero" aria-labelledby="vm-low-title"><div class="vm-sans-hero__content"><span class="vm-category-eyebrow">MAÎTRISEZ VOTRE BUDGET</span><h1 id="vm-low-title">Les forfaits mobiles à petit prix</h1><p>Comparez les offres les moins chères actuellement référencées sur Vectone et trouvez un forfait adapté à votre budget sans négliger vos usages réels.</p><div class="vm-sans-hero__actions"><a class="vm-sans-button vm-sans-button--primary" href="#vm-low-offers">Comparer les prix <span aria-hidden="true">→</span></a><a class="vm-sans-button vm-sans-button--secondary" href="#vectone-guide-low">Comment choisir ?</a></div></div><div class="vm-sans-hero__visual"><img class="vm-sans-hero__image" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/vectone-v3/images/forfaits-low-cost-hero.png' ); ?>" width="1448" height="1086" loading="eager" alt="" aria-hidden="true"></div></section>
        <section class="vm-sans-benefits" aria-labelledby="vm-low-benefits-title"><div class="vm-sans-section-heading"><span class="vm-category-eyebrow">EN TOUTE SIMPLICITÉ</span><h2 id="vm-low-benefits-title">Pourquoi regarder les forfaits à petit prix ?</h2></div><div class="vm-sans-benefits__grid"><article><span class="vm-sans-number">01</span><h3>PRIX</h3><p>Commencez par comparer le coût mensuel réellement affiché afin d’identifier les offres compatibles avec votre budget.</p></article><article><span class="vm-sans-number">02</span><h3>ESSENTIEL</h3><p>Un forfait moins cher peut être pertinent si vous utilisez principalement les fonctions essentielles : appels, messagerie, navigation et réseaux sociaux.</p></article><article><span class="vm-sans-number">03</span><h3>ÉQUILIBRE</h3><p>Le prix ne suffit pas. Vérifiez également la data, le réseau et les services inclus avant de choisir.</p></article></div></section>
        <section id="vm-low-offers" class="vm-sans-loop-intro"><span class="vm-category-eyebrow">LES OFFRES</span><h2>Comparez les forfaits les plus accessibles</h2><p>Les prix affichés proviennent directement des offres actuellement référencées sur Vectone.</p><div class="vm-category-offers"><?php foreach ( $products as $p ) echo vectone_cat_card( $p ); ?></div></section>
        <section id="vectone-guide-low" class="vm-sans-guide" aria-labelledby="vm-low-guide-title"><span class="vm-category-eyebrow">GUIDE DE CHOIX</span><h2 id="vm-low-guide-title">Comment choisir un forfait à petit prix ?</h2><p class="vm-sans-guide__intro">Le forfait le moins cher n’est pas toujours celui qui vous conviendra le mieux. Commencez par identifier vos usages essentiels puis comparez les offres correspondantes.</p><div class="vm-sans-guide__profiles"><article><h3>PETIT BUDGET</h3><p>Fixez d’abord le montant mensuel que vous souhaitez consacrer à votre forfait et comparez les offres situées dans cette gamme.</p></article><article><h3>USAGE SIMPLE</h3><p>Si vous utilisez surtout les appels, la messagerie, les réseaux sociaux et le Wi-Fi, une offre simple peut suffire.</p></article><article><h3>BON ÉQUILIBRE</h3><p>Comparez toujours le prix avec le volume de data, le réseau et les services inclus afin d’éviter de choisir uniquement sur le tarif.</p></article></div><aside class="vm-sans-advice"><h3>Avant de choisir le moins cher</h3><p>Un prix bas est intéressant seulement si le forfait couvre réellement vos besoins. Vérifiez la data, le réseau, les services inclus et les conditions de l’opérateur avant de souscrire.</p></aside></section>
        <section class="vm-sans-faq" aria-labelledby="vm-low-faq-title"><span class="vm-category-eyebrow">À SAVOIR</span><h2 id="vm-low-faq-title">Questions fréquentes</h2><details><summary>Qu’est-ce qu’un forfait low-cost ?</summary><p>Il s’agit généralement d’une offre mobile proposée à un tarif réduit ou avec une structure de services plus simple. Les conditions varient selon l’opérateur.</p></details><details><summary>Le forfait le moins cher est-il forcément le meilleur ?</summary><p>Non. Le meilleur choix dépend de votre budget mais aussi de votre consommation de data, du réseau et des services dont vous avez besoin.</p></details><details><summary>Comment comparer les prix correctement ?</summary><p>Comparez le tarif mensuel réellement affiché ainsi que les services inclus. Vérifiez également si certaines conditions peuvent faire évoluer le prix.</p></details><details><summary>Puis-je trouver beaucoup de data à petit prix ?</summary><p>Certaines offres peuvent proposer des volumes de data importants à des prix compétitifs. Il faut comparer les offres actuellement disponibles.</p></details><details><summary>Pourquoi les prix peuvent-ils changer ?</summary><p>Les opérateurs peuvent modifier leurs offres et leurs tarifs. Consultez toujours les détails de l’offre avant de souscrire.</p></details></section>
        <section class="vm-sans-final-cta" aria-labelledby="vm-low-final-title"><div><h2 id="vm-low-final-title">Prêt à réduire votre budget mobile ?</h2><p>Comparez les offres disponibles et trouvez un forfait qui correspond à vos usages sans payer pour des services dont vous n’avez pas besoin.</p></div><div><a class="vm-sans-button vm-sans-button--primary" href="#vm-low-offers">Comparer les prix <span aria-hidden="true">→</span></a><a class="vm-sans-contact" href="mailto:contact@vectonemobile.fr">Besoin d’aide ?</a></div></section>
    </main>
    <?php
    $content = function_exists( 'vectone_site_content' ) ? vectone_site_content() : array();
    $row = $content['categories']['low'] ?? array();
    $value = function( $key, $fallback ) use ( $row ) { return ! empty( $row[ $key ] ) ? $row[ $key ] : $fallback; };
    $html = ob_get_clean();
    $replacements = array(
        'MAÃŽTRISEZ VOTRE BUDGET' => $value( 'eyebrow', 'MAÎTRISEZ VOTRE BUDGET' ),
        'Les forfaits mobiles Ã  petit prix' => $value( 'title', 'Les forfaits mobiles à petit prix' ),
        'Comparez les offres les moins chÃ¨res actuellement rÃ©fÃ©rencÃ©es sur Vectone et trouvez un forfait adaptÃ© Ã  votre budget sans nÃ©gliger vos usages rÃ©els.' => $value( 'intro', 'Comparez les offres les moins chères actuellement référencées sur Vectone et trouvez un forfait adapté à votre budget sans négliger vos usages réels.' ),
        'Comparez les forfaits les plus accessibles' => $value( 'offers_title', 'Comparez les forfaits les plus accessibles' ),
        'Les prix affichÃ©s proviennent directement des offres actuellement rÃ©fÃ©rencÃ©es sur Vectone.' => $value( 'offers_sub', 'Les prix affichés proviennent directement des offres actuellement référencées sur Vectone.' ),
        'Questions frÃ©quentes' => $value( 'faq_title', 'Questions fréquentes' ),
        'PrÃªt Ã  rÃ©duire votre budget mobile ?' => $value( 'final_title', 'Prêt à réduire votre budget mobile ?' ),
        'Comparez les offres disponibles et trouvez un forfait qui correspond Ã  vos usages sans payer pour des services dont vous nâ€™avez pas besoin.' => $value( 'final_text', 'Comparez les offres disponibles et trouvez un forfait qui correspond à vos usages sans payer pour des services dont vous n’avez pas besoin.' ),
        'Comparer les prix' => $value( 'cta_text', 'Comparer les prix' ),
    );
    foreach ( $replacements as $from => $to ) $html = str_replace( $from, esc_html( $to ), $html );
    $items = array_filter( (array) ( $row['benefits'] ?? array() ), function( $item ) { return ! empty( $item['title'] ) && ! empty( $item['active'] ); } );
    usort( $items, function( $a, $b ) { return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 ); } );
    if ( $items ) $html = preg_replace_callback( '/(<div class="vm-sans-benefits__grid">)(.*?)(<\/div>)/si', function( $block ) use ( $items ) { $i = 0; $inner = preg_replace_callback( '/(<article[^>]*>.*?<h3>).*?(<\/h3>).*?(<p>).*?(<\/p>).*?(<\/article>)/si', function( $m ) use ( &$i, $items ) { if ( ! isset( $items[ $i ] ) ) return $m[0]; $item = $items[ $i++ ]; return $m[1] . esc_html( $item['title'] ) . $m[2] . $m[3] . esc_html( $item['description'] ?? '' ) . $m[4] . $m[5]; }, $block[2] ); return $block[1] . $inner . $block[3]; }, $html, 1 );
    if ( ! empty( $row['guide'] ) ) $html = preg_replace_callback( '/(<div class="vm-sans-guide__profiles">)(.*?)(<\/div>)/si', function( $block ) use ( $row ) { $items = array_values( array_filter( (array) $row['guide'], function( $item ) { return ! empty( $item['title'] ) && ! empty( $item['active'] ); } ) ); usort( $items, function( $a, $b ) { return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 ); } ); $i = 0; $inner = preg_replace_callback( '/(<article[^>]*>.*?<h3>).*?(<\/h3>).*?(<p>).*?(<\/p>).*?(<\/article>)/si', function( $m ) use ( &$i, $items ) { if ( ! isset( $items[ $i ] ) ) return $m[0]; $item = $items[ $i++ ]; return $m[1] . esc_html( $item['title'] ) . $m[2] . $m[3] . esc_html( $item['description'] ?? ( $item['text'] ?? '' ) ) . $m[4] . $m[5]; }, $block[2] ); return $block[1] . $inner . $block[3]; }, $html, 1 );
    $faq_items = array_filter( (array) ( $row['faq'] ?? $content['faq'] ?? array() ), function( $item ) { return ! empty( $item['question'] ) && ! empty( $item['active'] ); } );
    if ( $faq_items ) {
        usort( $faq_items, function( $a, $b ) { return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 ); } );
        $faq = '<section class="vm-sans-faq" aria-labelledby="vm-low-faq-title"><span class="vm-category-eyebrow">Ã€ SAVOIR</span><h2 id="vm-low-faq-title">' . esc_html( $value( 'faq_title', 'Questions frÃ©quentes' ) ) . '</h2>';
        foreach ( array_slice( $faq_items, 0, 10 ) as $item ) $faq .= '<details><summary>' . esc_html( $item['question'] ) . '</summary><p>' . esc_html( $item['answer'] ?? '' ) . '</p></details>';
        $faq .= '</section>';
        $html = preg_replace( '/<section class="vm-sans-faq".*?<\/section>/si', $faq, $html, 1 );
    }
    $final_url = $value( 'final_url', '#vm-low-offers' );
    $html = preg_replace( '/(<section class="vm-sans-final-cta".*?<a class="vm-sans-button vm-sans-button--primary" href=")[^"]*(".*?>)/si', '$1' . esc_url( $final_url ) . '$2', $html, 1 );
    echo $html;
}
add_shortcode( 'vectone_budget_offers', 'vectone_low_cost_page_v2' );

function vectone_international_page_v2() {
    ob_start(); ?>
    <main class="vm-data-page vm-international-page">
        <section class="vm-sans-hero" aria-labelledby="vm-int-title"><div class="vm-sans-hero__content"><span class="vm-category-eyebrow">RESTEZ CONNECTÉ</span><h1 id="vm-int-title">Les forfaits pour vos usages internationaux</h1><p>Comparez les offres qui mentionnent des appels, services ou usages internationaux et vérifiez les conditions proposées par chaque opérateur avant de souscrire.</p><div class="vm-sans-hero__actions"><a class="vm-sans-button vm-sans-button--primary" href="#vm-int-offers">Voir les offres <span aria-hidden="true">→</span></a><a class="vm-sans-button vm-sans-button--secondary" href="#vectone-guide-int">Comment choisir ?</a></div></div><div class="vm-sans-hero__visual"><img class="vm-sans-hero__image" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/vectone-v3/images/forfaits-internationaux-hero.png' ); ?>" width="1448" height="1086" loading="eager" alt="" aria-hidden="true"></div></section>
        <section class="vm-sans-benefits" aria-labelledby="vm-int-benefits-title"><div class="vm-sans-section-heading"><span class="vm-category-eyebrow">EN TOUTE SIMPLICITÉ</span><h2 id="vm-int-benefits-title">Pourquoi vérifier les conditions internationales ?</h2></div><div class="vm-sans-benefits__grid"><article><span class="vm-sans-number">01</span><h3>APPELS</h3><p>Certaines offres peuvent inclure des usages ou appels vers des destinations spécifiques. Vérifiez toujours les destinations et conditions prévues.</p></article><article><span class="vm-sans-number">02</span><h3>VOYAGE</h3><p>Les règles d’utilisation d’un forfait peuvent varier selon le pays, la zone géographique et l’opérateur.</p></article><article><span class="vm-sans-number">03</span><h3>CONDITIONS</h3><p>Avant de souscrire, consultez attentivement les limites, destinations incluses et éventuels coûts associés.</p></article></div></section>
        <section id="vm-int-offers" class="vm-sans-loop-intro"><span class="vm-category-eyebrow">LES OFFRES</span><h2>Comparez les forfaits adaptés aux usages internationaux</h2><div class="vm-int-empty-state"><span class="vm-int-empty-state__signal" aria-hidden="true"></span><h3>Aucune offre internationale suffisamment documentée pour le moment</h3><p>Vectone n’affiche ici que les offres disposant d’informations internationales suffisamment claires. De nouvelles offres pourront être ajoutées lorsque ces données seront disponibles.</p><div class="vm-int-empty-state__advice"><strong>Vous pouvez consulter les autres forfaits actuellement référencés.</strong><a class="vm-sans-button vm-sans-button--primary" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>">Voir tous les forfaits <span aria-hidden="true">→</span></a></div></div></section>
        <section id="vectone-guide-int" class="vm-sans-guide" aria-labelledby="vm-int-guide-title"><span class="vm-category-eyebrow">GUIDE DE CHOIX</span><h2 id="vm-int-guide-title">Comment choisir un forfait pour l’international ?</h2><p class="vm-sans-guide__intro">Le bon choix dépend surtout des pays concernés, de la fréquence de vos déplacements et du type d’usage prévu : appels, data ou communication depuis la France.</p><div class="vm-sans-guide__profiles"><article><h3>VOYAGES OCCASIONNELS</h3><p>Si vous voyagez ponctuellement, vérifiez surtout les conditions applicables dans les pays que vous visitez.</p></article><article><h3>APPELS VERS L’ÉTRANGER</h3><p>Si vous appelez régulièrement des proches ou contacts à l’international, consultez les destinations réellement incluses dans l’offre.</p></article><article><h3>USAGE RÉGULIER</h3><p>Pour des déplacements fréquents, comparez attentivement les conditions de roaming, les zones couvertes et les éventuelles limites d’usage.</p></article></div><aside class="vm-sans-advice"><h3>Avant de partir ou d’appeler à l’étranger</h3><p>Les conditions internationales peuvent varier fortement d’une offre à l’autre. Vérifiez toujours les pays concernés, les limites d’utilisation et les éventuels frais avant de souscrire ou d’utiliser votre forfait hors de France.</p></aside></section>
        <section class="vm-sans-faq" aria-labelledby="vm-int-faq-title"><span class="vm-category-eyebrow">À SAVOIR</span><h2 id="vm-int-faq-title">Questions fréquentes</h2><details><summary>Qu’est-ce qu’un forfait international ?</summary><p>Ce terme peut désigner une offre comprenant certains appels vers l’étranger, des usages depuis d’autres pays ou des conditions particulières de roaming. Les services exacts dépendent de chaque opérateur.</p></details><details><summary>Puis-je utiliser mon forfait à l’étranger ?</summary><p>Cela dépend de l’offre, du pays et des conditions prévues par l’opérateur. Vérifiez toujours ces informations avant votre déplacement.</p></details><details><summary>Les appels internationaux sont-ils toujours inclus ?</summary><p>Non. Les destinations, volumes et conditions varient selon les offres. Consultez les détails du forfait avant de souscrire.</p></details><details><summary>Quelle différence entre roaming et appels internationaux ?</summary><p>Le roaming concerne généralement l’utilisation de votre forfait lorsque vous vous trouvez à l’étranger. Les appels internationaux concernent les communications vers un autre pays.</p></details><details><summary>Pourquoi certaines offres ne sont-elles pas affichées ici ?</summary><p>Vectone ne présente dans cette sélection que les offres pour lesquelles une information internationale suffisamment claire est disponible.</p></details></section>
        <section class="vm-sans-final-cta" aria-labelledby="vm-int-final-title"><div><h2 id="vm-int-final-title">Besoin d’un forfait adapté à vos déplacements ?</h2><p>Comparez les offres disponibles et vérifiez les conditions correspondant aux pays et aux usages qui vous concernent.</p></div><div><a class="vm-sans-button vm-sans-button--primary" href="#vm-int-offers">Voir les offres <span aria-hidden="true">→</span></a><a class="vm-sans-contact" href="mailto:contact@vectonemobile.fr">Besoin d’aide ?</a></div></section>
    </main>
    <?php
    $content = function_exists( 'vectone_site_content' ) ? vectone_site_content() : array();
    $row = $content['categories']['international'] ?? array();
    $value = function( $key, $fallback ) use ( $row ) { return ! empty( $row[ $key ] ) ? $row[ $key ] : $fallback; };
    $html = ob_get_clean();
    $replacements = array(
        'RESTEZ CONNECTÃ‰' => $value( 'eyebrow', 'RESTEZ CONNECTÉ' ),
        'Les forfaits pour vos usages internationaux' => $value( 'title', 'Les forfaits pour vos usages internationaux' ),
        'Comparez les offres qui mentionnent des appels, services ou usages internationaux et vÃ©rifiez les conditions proposÃ©es par chaque opÃ©rateur avant de souscrire.' => $value( 'intro', 'Comparez les offres qui mentionnent des appels, services ou usages internationaux et vérifiez les conditions proposées par chaque opérateur avant de souscrire.' ),
        'Comparez les forfaits adaptÃ©s aux usages internationaux' => $value( 'offers_title', 'Comparez les forfaits adaptés aux usages internationaux' ),
        'Questions frÃ©quentes' => $value( 'faq_title', 'Questions fréquentes' ),
        'Besoin dâ€™un forfait adaptÃ© Ã  vos dÃ©placements ?' => $value( 'final_title', 'Besoin d’un forfait adapté à vos déplacements ?' ),
        'Comparez les offres disponibles et vÃ©rifiez les conditions correspondant aux pays et aux usages qui vous concernent.' => $value( 'final_text', 'Comparez les offres disponibles et vérifiez les conditions correspondant aux pays et aux usages qui vous concernent.' ),
        'Voir les offres' => $value( 'cta_text', 'Voir les offres' ),
    );
    foreach ( $replacements as $from => $to ) $html = str_replace( $from, esc_html( $to ), $html );
    $items = array_values( array_filter( (array) ( $row['benefits'] ?? array() ), function( $item ) { return ! empty( $item['title'] ) && ! empty( $item['active'] ); } ) );
    usort( $items, function( $a, $b ) { return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 ); } );
    if ( $items ) $html = preg_replace_callback( '/(<div class="vm-sans-benefits__grid">)(.*?)(<\/div>)/si', function( $block ) use ( $items ) { $i = 0; $inner = preg_replace_callback( '/(<article[^>]*>.*?<h3>).*?(<\/h3>).*?(<p>).*?(<\/p>).*?(<\/article>)/si', function( $m ) use ( &$i, $items ) { if ( ! isset( $items[ $i ] ) ) return $m[0]; $item = $items[ $i++ ]; return $m[1] . esc_html( $item['title'] ) . $m[2] . $m[3] . esc_html( $item['description'] ?? '' ) . $m[4] . $m[5]; }, $block[2] ); return $block[1] . $inner . $block[3]; }, $html, 1 );
    if ( ! empty( $row['guide'] ) ) $html = preg_replace_callback( '/(<div class="vm-sans-guide__profiles">)(.*?)(<\/div>)/si', function( $block ) use ( $row ) { $items = array_values( array_filter( (array) $row['guide'], function( $item ) { return ! empty( $item['title'] ) && ! empty( $item['active'] ); } ) ); usort( $items, function( $a, $b ) { return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 ); } ); $i = 0; $inner = preg_replace_callback( '/(<article[^>]*>.*?<h3>).*?(<\/h3>).*?(<p>).*?(<\/p>).*?(<\/article>)/si', function( $m ) use ( &$i, $items ) { if ( ! isset( $items[ $i ] ) ) return $m[0]; $item = $items[ $i++ ]; return $m[1] . esc_html( $item['title'] ) . $m[2] . $m[3] . esc_html( $item['description'] ?? ( $item['text'] ?? '' ) ) . $m[4] . $m[5]; }, $block[2] ); return $block[1] . $inner . $block[3]; }, $html, 1 );
    $faq_items = array_filter( (array) ( $content['categories']['international']['faq'] ?? $content['faq'] ?? array() ), function( $item ) { return ! empty( $item['question'] ) && ! empty( $item['active'] ); } );
    if ( $faq_items ) {
        usort( $faq_items, function( $a, $b ) { return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 ); } );
        $faq = '<section class="vm-sans-faq" aria-labelledby="vm-int-faq-title"><span class="vm-category-eyebrow">À SAVOIR</span><h2 id="vm-int-faq-title">' . esc_html( $value( 'faq_title', 'Questions fréquentes' ) ) . '</h2>';
        foreach ( array_slice( $faq_items, 0, 10 ) as $item ) $faq .= '<details><summary>' . esc_html( $item['question'] ) . '</summary><p>' . esc_html( $item['answer'] ?? '' ) . '</p></details>';
        $faq .= '</section>';
        $html = preg_replace( '/<section class="vm-sans-faq".*?<\/section>/si', $faq, $html, 1 );
    }
    $final_url = $value( 'final_url', '#vm-int-offers' );
    $html = preg_replace( '/(<section class="vm-sans-final-cta".*?<a class="vm-sans-button vm-sans-button--primary" href=")[^"]*(".*?>)/si', '$1' . esc_url( $final_url ) . '$2', $html, 1 );
    echo $html;
}
add_shortcode( 'vectone_international_offers', 'vectone_international_page_v2' );
