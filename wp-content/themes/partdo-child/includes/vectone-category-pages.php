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
    <article class="vm-category-offer"><div class="vm-category-offer__media"><?php echo wp_kses_post( $p->get_image( 'woocommerce_thumbnail' ) ); ?></div><div class="vm-category-offer__body"><?php if ( vectone_cat_operator( $p ) ) : ?><span class="vm-category-offer__operator"><?php echo esc_html( vectone_cat_operator( $p ) ); ?></span><?php endif; ?><h2><?php echo esc_html( $p->get_name() ); ?></h2><div class="vm-category-offer__attributes"><?php foreach ( $attrs as $a ) : ?><span><?php echo esc_html( $a ); ?></span><?php endforeach; ?></div></div><div class="vm-category-offer__action"><div class="vm-category-offer__price"><?php echo wp_kses_post( $p->get_price_html() ); ?></div><a class="vm-category-offer__cta" href="<?php echo esc_url( $url ); ?>"<?php echo $p->is_type( 'external' ) ? ' target="_blank" rel="sponsored noopener noreferrer"' : ''; ?>>Voir l’offre <span aria-hidden="true">→</span></a></div></article>
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
