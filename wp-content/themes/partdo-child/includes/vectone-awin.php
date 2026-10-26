<?php

/**
 * Partner campaigns displayed after the WooCommerce offer loop.
 * Campaign URLs are intentionally kept as explicit source values.
 */
function vectone_awin_campaigns() {
    $defaults = array(
        array(
            'name'     => 'France Telephone',
            'text'     => 'Découvrez les offres mobile disponibles chez notre partenaire.',
            'url'      => 'https://www.awin1.com/cread.php?awinmid=68728&awinaffid=1776512&clickref=vectone_home_phone_1&ued=https%3A%2F%2Fwww.france-telephone.com%2F',
            'position' => 'home-phone-1',
            'variant'  => 'primary',
        ),
        array(
            'name'     => 'CDiscount Mobile',
            'text'     => 'Découvrez les forfaits et offres mobile disponibles chez CDiscount.',
            'url'      => 'https://www.awin1.com/cread.php?awinmid=19624&awinaffid=1776512&clickref=vectone_home_phone_2&ued=https%3A%2F%2Fwww.cdiscount.com%2Fcdiscount-mobile%2Fv-164-0.html',
            'position' => 'home-phone-2',
            'variant'  => 'secondary',
        ),
        array(
            'name'     => 'Syma Mobile',
            'text'     => 'Découvrez les offres mobile proposées par Syma.',
            'url'      => 'https://tidd.ly/4ysPpRD',
            'position' => 'home-phone-3',
            'variant'  => 'secondary',
        ),
    );
    $configured = get_option( 'vectone_awin_campaigns', array() );
    foreach ( $defaults as $index => $default ) {
        if ( ! is_array( $configured[ $index ] ?? null ) ) continue;
        foreach ( array( 'name', 'text', 'url', 'position', 'variant' ) as $key ) {
            if ( isset( $configured[ $index ][ $key ] ) && '' !== $configured[ $index ][ $key ] ) $defaults[ $index ][ $key ] = $configured[ $index ][ $key ];
        }
    }
    return $defaults;
}

function vectone_awin_render_campaign( $campaign ) {
    $images = array(
        'France Telephone' => 'awin-france-telephone.webp',
        'CDiscount Mobile' => 'awin-cdiscount-mobile.webp',
        'Syma Mobile'      => 'awin-syma-mobile.webp',
    );
    $image_url = isset( $images[ $campaign['name'] ] ) ? get_stylesheet_directory_uri() . '/assets/vectone-v3/images/' . $images[ $campaign['name'] ] : '';
    $cta = $campaign['variant'] === 'primary' ? "Découvrir l'offre" : ( $campaign['name'] === 'Syma Mobile' ? 'Découvrir' : 'Voir les offres' );
    ?>
    <article class="vm-awin-card vm-awin-card--<?php echo esc_attr( $campaign['variant'] ); ?>">
        <div class="vm-awin-card__content">
            <span class="vm-awin-card__partner">Partenaire</span>
            <h3><?php echo esc_html( $campaign['name'] ); ?></h3>
            <p><?php echo esc_html( $campaign['text'] ); ?></p>
            <a class="vm-awin-card__cta" href="<?php echo esc_url( $campaign['url'] ); ?>" target="_blank" rel="sponsored noopener noreferrer" data-affiliate-network="awin" data-affiliate-position="<?php echo esc_attr( $campaign['position'] ); ?>">
                <?php echo esc_html( $cta ); ?> <span aria-hidden="true">→</span>
            </a>
        </div>
        <span class="vm-awin-card__visual" aria-hidden="true" style="background-image: url('<?php echo esc_url( $image_url ); ?>');"></span>
    </article>
    <?php
}

function vectone_awin_render_section() {
    if ( ! is_front_page() || ! function_exists( 'is_shop' ) ) {
        return;
    }
    static $rendered = false;
    if ( $rendered ) {
        return;
    }
    $rendered = true;
    $campaigns = vectone_awin_campaigns();
    ?>
    <section class="vm-awin-section" aria-labelledby="vm-awin-title">
        <div class="vm-awin-section__head">
            <div>
                <p class="vm-awin-eyebrow">Sélection partenaires</p>
                <h2 id="vm-awin-title">Téléphones &amp; bons plans mobile.</h2>
                <p class="vm-awin-section__intro">Découvrez une sélection d’offres proposées par nos partenaires.</p>
            </div>
        </div>
        <div class="vm-awin-grid">
            <?php foreach ( $campaigns as $campaign ) : ?>
                <?php vectone_awin_render_campaign( $campaign ); ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
}
add_action( 'woocommerce_after_shop_loop', 'vectone_awin_render_section', 30 );
