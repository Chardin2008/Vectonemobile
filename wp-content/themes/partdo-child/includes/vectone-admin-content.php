<?php
defined( 'ABSPATH' ) || exit;

function vectone_site_content_defaults() {
    $home_defaults = array( 'hero_image_id' => 0, 'contact_eyebrow' => '', 'contact_title' => '', 'contact_text' => '', 'contact_direct_label' => '', 'contact_form_title' => '', 'contact_form_note' => '' );
    return array(
        'home' => $home_defaults,
        'topbar' => array( 'slogan' => 'COMPAREZ. CHOISISSEZ. CONNECTEZ.', 'help' => 'Besoin d’aide ?', 'email' => 'contact@vectonemobile.fr' ),
        'second_visual' => array( 'eyebrow' => 'PLUS DE CHOIX, PLUS DE LIBERTÉ', 'title' => 'Comparez plusieurs réseaux. Choisissez celui qui vous ressemble.', 'description' => 'Explorez plusieurs opérateurs et trouvez l’offre adaptée à vos usages depuis un seul endroit.', 'image' => '', 'cta_text' => '', 'cta_url' => '', 'show_cta' => 0 ),
        'guide' => array( 'eyebrow' => 'Le guide Vectone', 'title' => '5 questions à se poser avant de choisir son forfait.', 'description' => 'Data, engagement, réseau, international et portabilité : les points essentiels à comprendre avant de comparer.', 'profiles' => array() ),
        'faq' => array(
            array( 'question' => 'Combien de data me faut-il ?', 'answer' => 'Le bon volume dépend surtout de vos usages : navigation, réseaux sociaux, musique, vidéo et partage de connexion.', 'order' => 1, 'active' => 1 ),
            array( 'question' => 'Avec ou sans engagement ?', 'answer' => 'Un forfait sans engagement offre davantage de souplesse pour changer d’offre. Un forfait avec engagement implique une durée définie à vérifier avant de souscrire.', 'order' => 2, 'active' => 1 ),
            array( 'question' => 'Comment comparer les réseaux ?', 'answer' => 'La qualité dépend notamment de la couverture, de votre zone géographique et du réseau utilisé par l’opérateur.', 'order' => 3, 'active' => 1 ),
            array( 'question' => 'Quel forfait pour l’international ?', 'answer' => 'Vérifiez les destinations incluses, les appels, les SMS, la data et les conditions de roaming avant de choisir.', 'order' => 4, 'active' => 1 ),
            array( 'question' => 'Puis-je garder mon numéro ?', 'answer' => 'En France, la portabilité permet généralement de conserver son numéro lors d’un changement d’opérateur.', 'order' => 5, 'active' => 1 ),
        ),
        'footer' => array( 'line1' => 'COMPAREZ.', 'line2' => 'CHOISISSEZ.', 'line3' => 'CONNECTEZ.', 'cta_text' => 'Comparer les offres', 'cta_url' => '', 'secondary' => 'Des forfaits mobiles lisibles, des réseaux comparés, une décision plus simple.', 'email' => 'contact@vectonemobile.fr' ),
        'awin' => array( 'campaigns' => array(
            array( 'name' => 'France Telephone', 'text' => 'Découvrez les offres mobile disponibles chez notre partenaire.', 'url' => 'https://www.awin1.com/cread.php?awinmid=68728&awinaffid=1776512&clickref=vectone_home_phone_1&ued=https%3A%2F%2Fwww.france-telephone.com%2F', 'position' => 'home-phone-1', 'variant' => 'primary' ),
            array( 'name' => 'CDiscount Mobile', 'text' => 'Découvrez les forfaits et offres mobile disponibles chez CDiscount.', 'url' => 'https://www.awin1.com/cread.php?awinmid=19624&awinaffid=1776512&clickref=vectone_home_phone_2&ued=https%3A%2F%2Fwww.cdiscount.com%2Fcdiscount-mobile%2Fv-164-0.html', 'position' => 'home-phone-2', 'variant' => 'secondary' ),
            array( 'name' => 'Syma Mobile', 'text' => 'Découvrez les offres mobile proposées par Syma.', 'url' => 'https://tidd.ly/4ysPpRD', 'position' => 'home-phone-3', 'variant' => 'secondary' ),
        ) ),
        'categories' => array_fill_keys( array( 'sans', 'avec', 'data', 'low', 'international' ), array( 'eyebrow' => '', 'title' => '', 'intro' => '', 'offers_title' => '', 'offers_sub' => '', 'reviews_title' => '', 'reviews_text' => '', 'faq_title' => '', 'news_title' => '', 'news_text' => '', 'final_title' => '', 'final_text' => '', 'cta_text' => '', 'final_url' => '', 'faq' => array(), 'benefits' => array(), 'guide' => array() ) ),
    );
}

function vectone_site_content() {
    $saved = get_option( 'vectone_site_content', array() );
    return wp_parse_args( is_array( $saved ) ? $saved : array(), vectone_site_content_defaults() );
}

function vectone_site_content_value( $group, $key, $fallback = '' ) {
    $content = vectone_site_content();
    return isset( $content[ $group ][ $key ] ) && '' !== $content[ $group ][ $key ] ? $content[ $group ][ $key ] : $fallback;
}

function vectone_site_content_admin_menu() {
    add_menu_page( 'Contenus du site', 'Vectone Mobile', 'manage_options', 'vectone-site-content', 'vectone_site_content_admin_page_v2', 'dashicons-edit-page', 58 );
    add_submenu_page( 'vectone-site-content', 'Contenus du site', 'Contenus du site', 'manage_options', 'vectone-site-content', 'vectone_site_content_admin_page_v2' );
}
add_action( 'admin_menu', 'vectone_site_content_admin_menu' );

function vectone_site_content_admin_init() {
    register_setting( 'vectone_site_content_group', 'vectone_site_content', array( 'sanitize_callback' => 'vectone_site_content_sanitize' ) );
}
add_action( 'admin_init', 'vectone_site_content_admin_init' );

function vectone_site_content_sanitize( $input ) {
    $defaults = vectone_site_content_defaults(); $out = $defaults;
    foreach ( array( 'sans', 'avec', 'data', 'low', 'international' ) as $type ) {
        foreach ( array( 'empty_title', 'empty_text', 'empty_cta_text', 'empty_url', 'benefits_title', 'guide_eyebrow', 'guide_title', 'guide_intro', 'advice_title', 'advice_text', 'help_text', 'help_url' ) as $key ) {
            $out['categories'][ $type ][ $key ] = '';
        }
    }
    foreach ( array( 'topbar', 'second_visual', 'guide', 'footer' ) as $group ) {
        foreach ( (array) $defaults[ $group ] as $key => $fallback ) {
            $value = isset( $input[ $group ][ $key ] ) ? $input[ $group ][ $key ] : '';
            if ( 'cta_url' === $key || 'image' === $key ) $out[ $group ][ $key ] = esc_url_raw( $value );
            elseif ( 'show_cta' === $key ) $out[ $group ][ $key ] = empty( $value ) ? 0 : 1;
            else $out[ $group ][ $key ] = is_string( $value ) ? ( false !== strpos( $key, 'description' ) || 'secondary' === $key ? sanitize_textarea_field( $value ) : sanitize_text_field( $value ) ) : $fallback;
        }
    }
    foreach ( (array) ( $defaults['awin']['campaigns'] ?? array() ) as $index => $fallback ) {
        $row = is_array( $input['awin']['campaigns'][ $index ] ?? null ) ? $input['awin']['campaigns'][ $index ] : array();
        $out['awin']['campaigns'][ $index ] = array(
            'name' => sanitize_text_field( $row['name'] ?? $fallback['name'] ),
            'text' => sanitize_textarea_field( $row['text'] ?? $fallback['text'] ),
            'url' => esc_url_raw( $row['url'] ?? $fallback['url'] ),
            'position' => sanitize_key( $row['position'] ?? $fallback['position'] ),
            'variant' => in_array( $row['variant'] ?? '', array( 'primary', 'secondary' ), true ) ? $row['variant'] : $fallback['variant'],
        );
    }
    $out['home']['hero_image_id'] = absint( $input['home']['hero_image_id'] ?? 0 );
    foreach ( array( 'contact_eyebrow', 'contact_title', 'contact_direct_label', 'contact_form_title' ) as $key ) $out['home'][ $key ] = sanitize_text_field( $input['home'][ $key ] ?? '' );
    foreach ( array( 'contact_text', 'contact_form_note' ) as $key ) $out['home'][ $key ] = sanitize_textarea_field( $input['home'][ $key ] ?? '' );
    $out['guide']['profiles'] = array();
    foreach ( (array) ( $input['guide']['profiles'] ?? array() ) as $row ) {
        if ( empty( $row['title'] ) && empty( $row['description'] ) ) continue;
        $out['guide']['profiles'][] = array( 'title' => sanitize_text_field( $row['title'] ?? '' ), 'description' => sanitize_textarea_field( $row['description'] ?? '' ), 'details' => sanitize_textarea_field( $row['details'] ?? '' ), 'cta_text' => sanitize_text_field( $row['cta_text'] ?? '' ), 'cta_url' => esc_url_raw( $row['cta_url'] ?? '' ) );
    }
    $out['faq'] = array();
    foreach ( (array) ( $input['faq'] ?? array() ) as $row ) {
        if ( empty( $row['question'] ) && empty( $row['answer'] ) ) continue;
        $out['faq'][] = array( 'question' => sanitize_text_field( $row['question'] ?? '' ), 'answer' => sanitize_textarea_field( $row['answer'] ?? '' ), 'order' => absint( $row['order'] ?? 0 ), 'active' => empty( $row['active'] ) ? 0 : 1 );
    }
    foreach ( (array) $defaults['categories'] as $type => $fields ) {
        foreach ( $fields as $key => $fallback ) {
            if ( in_array( $key, array( 'faq', 'benefits', 'guide' ), true ) ) continue;
            $value = $input['categories'][ $type ][ $key ] ?? '';
            $out['categories'][ $type ][ $key ] = 'final_url' === $key ? esc_url_raw( $value ) : sanitize_textarea_field( $value );
        }
        foreach ( array( 'empty_title', 'empty_text', 'empty_cta_text', 'empty_url', 'benefits_title', 'guide_eyebrow', 'guide_title', 'guide_intro', 'advice_title', 'advice_text', 'help_text', 'help_url' ) as $key ) {
            $value = $input['categories'][ $type ][ $key ] ?? '';
            $out['categories'][ $type ][ $key ] = in_array( $key, array( 'empty_url', 'help_url' ), true ) ? esc_url_raw( $value ) : sanitize_textarea_field( $value );
        }
    }
    $out['categories']['sans']['faq'] = array();
    foreach ( (array) ( $input['categories']['sans']['faq'] ?? array() ) as $row ) {
        if ( empty( $row['question'] ) && empty( $row['answer'] ) ) continue;
        $out['categories']['sans']['faq'][] = array( 'question' => sanitize_text_field( $row['question'] ?? '' ), 'answer' => sanitize_textarea_field( $row['answer'] ?? '' ), 'order' => absint( $row['order'] ?? 0 ), 'active' => empty( $row['active'] ) ? 0 : 1 );
    }
    $out['categories']['sans']['faq'] = array_values( $out['categories']['sans']['faq'] );
    foreach ( array( 'avec', 'data', 'low', 'international' ) as $type ) {
        $out['categories'][ $type ]['faq'] = array();
        foreach ( (array) ( $input['categories'][ $type ]['faq'] ?? array() ) as $row ) {
            if ( empty( $row['question'] ) && empty( $row['answer'] ) ) continue;
            $out['categories'][ $type ]['faq'][] = array( 'question' => sanitize_text_field( $row['question'] ?? '' ), 'answer' => sanitize_textarea_field( $row['answer'] ?? '' ), 'order' => absint( $row['order'] ?? 0 ), 'active' => empty( $row['active'] ) ? 0 : 1 );
        }
    }
    foreach ( array( 'avec', 'data', 'low', 'international' ) as $type ) {
        foreach ( array( 'benefits', 'guide' ) as $group ) {
            $out['categories'][ $type ][ $group ] = array();
            foreach ( (array) ( $input['categories'][ $type ][ $group ] ?? array() ) as $row ) {
            if ( empty( $row['title'] ) && empty( $row['description'] ) && empty( $row['text'] ) ) continue;
                $out['categories'][ $type ][ $group ][] = array( 'title' => sanitize_text_field( $row['title'] ?? '' ), 'description' => sanitize_textarea_field( $row['description'] ?? '' ), 'text' => sanitize_textarea_field( $row['text'] ?? '' ), 'order' => absint( $row['order'] ?? 0 ), 'active' => empty( $row['active'] ) ? 0 : 1 );
            }
        }
    }
    return $out;
}

function vectone_site_content_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    $c = vectone_site_content(); $faq = array_pad( (array) $c['faq'], 6, array( 'question' => '', 'answer' => '', 'order' => 0, 'active' => 0 ) );
    wp_enqueue_media();
    ?>
    <div class="wrap"><h1>Contenus du site</h1><p>Modifiez uniquement les textes éditoriaux du child theme. Un champ vide conserve automatiquement le contenu actuel.</p>
    <form method="post" action="options.php"><?php settings_fields( 'vectone_site_content_group' ); ?>
    <h2>Data — bénéfices</h2><table class="widefat striped"><thead><tr><th>Titre</th><th>Description</th><th>Ordre</th><th>Actif</th></tr></thead><tbody><?php $data_benefits = array_pad( (array) ( $c['categories']['data']['benefits'] ?? array() ), 3, array( 'title' => '', 'description' => '', 'order' => 0, 'active' => 0 ) ); foreach ( $data_benefits as $i => $row ) : ?><tr><td><input class="regular-text" name="vectone_site_content[categories][data][benefits][<?php echo $i; ?>][title]" value="<?php echo esc_attr( $row['title'] ?? '' ); ?>"></td><td><textarea name="vectone_site_content[categories][data][benefits][<?php echo $i; ?>][description]" rows="2"><?php echo esc_textarea( $row['description'] ?? '' ); ?></textarea></td><td><input type="number" name="vectone_site_content[categories][data][benefits][<?php echo $i; ?>][order]" value="<?php echo esc_attr( $row['order'] ?? 0 ); ?>" min="0"></td><td><input type="checkbox" name="vectone_site_content[categories][data][benefits][<?php echo $i; ?>][active]" value="1" <?php checked( $row['active'] ?? 0, 1 ); ?>></td></tr><?php endforeach; ?></tbody></table>
    <h2>Data — guide détaillé</h2><table class="widefat striped"><thead><tr><th>Élément</th><th>Titre</th><th>Description</th><th>Texte</th><th>Ordre</th><th>Actif</th></tr></thead><tbody><?php $data_guide = array_pad( (array) ( $c['categories']['data']['guide'] ?? array() ), 4, array( 'title' => '', 'description' => '', 'text' => '', 'order' => 0, 'active' => 0 ) ); foreach ( $data_guide as $i => $row ) : ?><tr><th><?php echo $i + 1; ?></th><td><input class="regular-text" name="vectone_site_content[categories][data][guide][<?php echo $i; ?>][title]" value="<?php echo esc_attr( $row['title'] ?? '' ); ?>"></td><td><textarea name="vectone_site_content[categories][data][guide][<?php echo $i; ?>][description]" rows="2"><?php echo esc_textarea( $row['description'] ?? '' ); ?></textarea></td><td><textarea name="vectone_site_content[categories][data][guide][<?php echo $i; ?>][text]" rows="2"><?php echo esc_textarea( $row['text'] ?? '' ); ?></textarea></td><td><input type="number" name="vectone_site_content[categories][data][guide][<?php echo $i; ?>][order]" value="<?php echo esc_attr( $row['order'] ?? 0 ); ?>" min="0"></td><td><input type="checkbox" name="vectone_site_content[categories][data][guide][<?php echo $i; ?>][active]" value="1" <?php checked( $row['active'] ?? 0, 1 ); ?>></td></tr><?php endforeach; ?></tbody></table>
    <h2>Topbar</h2><table class="form-table"><tr><th>Slogan</th><td><input class="regular-text" name="vectone_site_content[topbar][slogan]" value="<?php echo esc_attr( $c['topbar']['slogan'] ); ?>"></td></tr><tr><th>Besoin d’aide</th><td><input class="regular-text" name="vectone_site_content[topbar][help]" value="<?php echo esc_attr( $c['topbar']['help'] ); ?>"></td></tr><tr><th>Email</th><td><input class="regular-text" name="vectone_site_content[topbar][email]" value="<?php echo esc_attr( $c['topbar']['email'] ); ?>"></td></tr></table>
    <h2>Homepage — Deuxième visuel</h2><table class="form-table"><?php vectone_site_content_text_row( 'second_visual', 'eyebrow', 'Eyebrow', $c ); vectone_site_content_text_row( 'second_visual', 'title', 'Titre', $c, true ); vectone_site_content_text_row( 'second_visual', 'description', 'Description', $c, true ); ?><tr><th>Image</th><td><input class="regular-text vm-media-url" name="vectone_site_content[second_visual][image]" value="<?php echo esc_attr( $c['second_visual']['image'] ); ?>"><button type="button" class="button vm-media-button">Choisir une image</button></td></tr><tr><th>CTA</th><td><input class="regular-text" name="vectone_site_content[second_visual][cta_text]" value="<?php echo esc_attr( $c['second_visual']['cta_text'] ); ?>"><input class="regular-text" name="vectone_site_content[second_visual][cta_url]" value="<?php echo esc_attr( $c['second_visual']['cta_url'] ); ?>"><label><input type="checkbox" name="vectone_site_content[second_visual][show_cta]" value="1" <?php checked( $c['second_visual']['show_cta'], 1 ); ?>> afficher</label></td></tr></table>
    <h2>Homepage — Guide</h2><table class="form-table"><?php vectone_site_content_text_row( 'guide', 'eyebrow', 'Eyebrow', $c ); vectone_site_content_text_row( 'guide', 'title', 'Titre', $c, true ); vectone_site_content_text_row( 'guide', 'description', 'Description', $c, true ); ?></table><table class="widefat striped"><thead><tr><th>Profil</th><th>Titre</th><th>Description</th><th>Détails</th><th>CTA</th><th>URL</th></tr></thead><tbody><?php $profiles = array_pad( (array) ( $c['guide']['profiles'] ?? array() ), 5, array( 'title' => '', 'description' => '', 'details' => '', 'cta_text' => '', 'cta_url' => '' ) ); foreach ( $profiles as $i => $row ) : ?><tr><th><?php echo esc_html( $i + 1 ); ?></th><?php foreach ( array( 'title','description','details','cta_text','cta_url' ) as $key ) : ?><td><input class="regular-text" name="vectone_site_content[guide][profiles][<?php echo $i; ?>][<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $row[ $key ] ?? '' ); ?>"></td><?php endforeach; ?></tr><?php endforeach; ?></tbody></table>
    <h2>Homepage — FAQ</h2><table class="widefat striped"><thead><tr><th>Question</th><th>Réponse</th><th>Ordre</th><th>Actif</th></tr></thead><tbody><?php foreach ( $faq as $i => $row ) : ?><tr><td><input class="regular-text" name="vectone_site_content[faq][<?php echo $i; ?>][question]" value="<?php echo esc_attr( $row['question'] ); ?>"></td><td><textarea name="vectone_site_content[faq][<?php echo $i; ?>][answer]" rows="2"><?php echo esc_textarea( $row['answer'] ); ?></textarea></td><td><input type="number" name="vectone_site_content[faq][<?php echo $i; ?>][order]" value="<?php echo esc_attr( $row['order'] ); ?>" min="0"></td><td><input type="checkbox" name="vectone_site_content[faq][<?php echo $i; ?>][active]" value="1" <?php checked( $row['active'], 1 ); ?>></td></tr><?php endforeach; ?></tbody></table>
    <h2>Footer</h2><table class="form-table"><?php foreach ( array( 'line1' => 'Ligne 1', 'line2' => 'Ligne 2', 'line3' => 'Ligne 3', 'cta_text' => 'Texte CTA', 'cta_url' => 'URL CTA', 'secondary' => 'Texte secondaire', 'email' => 'Email' ) as $key => $label ) vectone_site_content_text_row( 'footer', $key, $label, $c, 'secondary' === $key ); ?></table>
    <h2>Pages forfaits</h2><p>Les champs vides conservent les textes actuels.</p><table class="widefat striped"><thead><tr><th>Page</th><th>Eyebrow</th><th>H1</th><th>Introduction</th><th>Titre offres</th><th>Sous-titre offres</th><th>Titre Avis</th><th>Titre FAQ</th><th>Titre actualités</th><th>CTA final</th><th>Texte CTA</th><th>Texte secondaire</th><th>URL CTA final</th></tr></thead><tbody><?php foreach ( array( 'sans' => 'Sans engagement', 'avec' => 'Avec engagement', 'data' => 'Data', 'low' => 'Low-cost', 'international' => 'International' ) as $type => $label ) : $row = $c['categories'][ $type ]; ?><tr><th><?php echo esc_html( $label ); ?></th><?php foreach ( array( 'eyebrow','title','intro','offers_title','offers_sub','reviews_title','faq_title','news_title','final_title','cta_text','final_text','final_url' ) as $key ) : ?><td><input type="text" name="vectone_site_content[categories][<?php echo esc_attr( $type ); ?>][<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $row[ $key ] ?? '' ); ?>"></td><?php endforeach; ?></tr><?php endforeach; ?></tbody></table>
    <h2>Sans engagement — FAQ administrable</h2><p>Si aucune ligne active n’est enregistrée, la FAQ historique est conservée.</p><table class="widefat striped"><thead><tr><th>Question</th><th>Réponse</th><th>Ordre</th><th>Actif</th></tr></thead><tbody><?php $sans_faq = array_pad( (array) ( $c['categories']['sans']['faq'] ?? array() ), 6, array( 'question' => '', 'answer' => '', 'order' => 0, 'active' => 0 ) ); foreach ( $sans_faq as $i => $row ) : ?><tr><td><input class="regular-text" name="vectone_site_content[categories][sans][faq][<?php echo $i; ?>][question]" value="<?php echo esc_attr( $row['question'] ?? '' ); ?>"></td><td><textarea name="vectone_site_content[categories][sans][faq][<?php echo $i; ?>][answer]" rows="2"><?php echo esc_textarea( $row['answer'] ?? '' ); ?></textarea></td><td><input type="number" name="vectone_site_content[categories][sans][faq][<?php echo $i; ?>][order]" value="<?php echo esc_attr( $row['order'] ?? 0 ); ?>" min="0"></td><td><input type="checkbox" name="vectone_site_content[categories][sans][faq][<?php echo $i; ?>][active]" value="1" <?php checked( $row['active'] ?? 0, 1 ); ?>></td></tr><?php endforeach; ?></tbody></table>
    <?php submit_button( 'Enregistrer les contenus' ); ?></form></div><script>jQuery(function($){$('.vm-media-button').on('click',function(){var b=$(this),f=wp.media({title:'Choisir une image',button:{text:'Utiliser cette image'},multiple:false});f.on('select',function(){b.prev('.vm-media-url').val(f.state().get('selection').first().toJSON().url);});f.open();});});</script>
    <?php
}

/* Section-based editor. It uses the same option and field names as the original editor. */
function vectone_site_content_admin_page_v2() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    $c = vectone_site_content();
    $types = array( 'sans' => 'Sans engagement', 'avec' => 'Avec engagement', 'data' => 'Data', 'low' => 'Low-cost', 'international' => 'International' );
    wp_enqueue_media();
    ?>
    <div class="wrap vm-content-admin">
        <h1>Contenus du site</h1>
        <p class="vm-admin-intro">Modifiez chaque section séparément. Un champ vide conserve automatiquement le contenu historique du site.</p>
        <div class="vm-admin-layout">
            <nav class="vm-admin-sidebar" aria-label="Sections de contenu">
                <p class="vm-admin-sidebar-title">Éditer une section</p>
                <a href="#vm-admin-home">Accueil</a><a href="#vm-admin-topbar">Topbar</a><a href="#vm-admin-footer">Footer</a>
                <p class="vm-admin-sidebar-title">Pages forfaits</p>
                <?php foreach ( $types as $type => $label ) : ?><a href="#vm-admin-<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $label ); ?></a><?php endforeach; ?>
                <p class="vm-admin-sidebar-title">Repères</p><a href="#vm-admin-sources">Sources d’édition</a>
            </nav>
            <form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" class="vm-admin-form">
                <?php settings_fields( 'vectone_site_content_group' ); ?>
                <section id="vm-admin-home" class="vm-admin-section">
                    <div class="vm-admin-section-heading"><span class="vm-admin-kicker">Accueil</span><h2>Contenus de la homepage</h2><p>Les éléments éditoriaux déjà présents sur l’accueil.</p></div>
                    <details open><summary>Hero — image principale</summary><div class="vm-admin-details-body">
                        <?php $hero_id = absint( $c['home']['hero_image_id'] ?? 0 ); $hero_url = $hero_id ? wp_get_attachment_image_url( $hero_id, 'thumbnail' ) : ''; ?>
                        <div class="vm-admin-media-field"><input type="hidden" class="vm-home-hero-id" name="vectone_site_content[home][hero_image_id]" value="<?php echo esc_attr( $hero_id ); ?>"><img class="vm-home-hero-preview" src="<?php echo esc_url( $hero_url ?: '' ); ?>" alt="" <?php echo $hero_url ? '' : 'style="display:none"'; ?>><div><button type="button" class="button vm-home-hero-select">Choisir une image</button> <button type="button" class="button vm-home-hero-remove">Retirer</button><p class="description">Si aucune image n’est choisie, l’image actuelle du thème reste utilisée.</p></div></div>
                    </div></details>
                    <details open><summary>Contact de la homepage</summary><div class="vm-admin-fields vm-admin-details-body">
                        <?php vectone_admin_field_v2( 'home', 'contact_eyebrow', 'Surtitre', $c['home']['contact_eyebrow'] ?? '' ); vectone_admin_field_v2( 'home', 'contact_title', 'Titre', $c['home']['contact_title'] ?? '' ); vectone_admin_field_v2( 'home', 'contact_text', 'Introduction', $c['home']['contact_text'] ?? '', 'textarea' ); vectone_admin_field_v2( 'home', 'contact_direct_label', 'Libellé contact direct', $c['home']['contact_direct_label'] ?? '' ); vectone_admin_field_v2( 'home', 'contact_form_title', 'Titre du formulaire', $c['home']['contact_form_title'] ?? '' ); vectone_admin_field_v2( 'home', 'contact_form_note', 'Note du formulaire', $c['home']['contact_form_note'] ?? '', 'textarea' ); ?>
                    </div></details>
                    <details><summary>Deuxième visuel</summary><div class="vm-admin-fields vm-admin-details-body">
                        <?php vectone_admin_field_v2( 'second_visual', 'eyebrow', 'Surtitre', $c['second_visual']['eyebrow'] ?? '' ); vectone_admin_field_v2( 'second_visual', 'title', 'Titre', $c['second_visual']['title'] ?? '', 'textarea' ); vectone_admin_field_v2( 'second_visual', 'description', 'Description', $c['second_visual']['description'] ?? '', 'textarea' ); vectone_admin_field_v2( 'second_visual', 'image', 'URL de l’image', $c['second_visual']['image'] ?? '', 'url' ); ?>
                        <button type="button" class="button vm-media-button">Choisir dans la médiathèque</button><div class="vm-admin-inline-fields"><?php vectone_admin_field_v2( 'second_visual', 'cta_text', 'Texte CTA', $c['second_visual']['cta_text'] ?? '' ); vectone_admin_field_v2( 'second_visual', 'cta_url', 'URL CTA', $c['second_visual']['cta_url'] ?? '', 'url' ); ?><label class="vm-admin-check"><input type="checkbox" name="vectone_site_content[second_visual][show_cta]" value="1" <?php checked( $c['second_visual']['show_cta'] ?? 0, 1 ); ?>> Afficher le CTA</label></div>
                    </div></details>
                    <details><summary>Guide de la homepage</summary><div class="vm-admin-fields vm-admin-details-body">
                        <?php vectone_admin_field_v2( 'guide', 'eyebrow', 'Surtitre', $c['guide']['eyebrow'] ?? '' ); vectone_admin_field_v2( 'guide', 'title', 'Titre', $c['guide']['title'] ?? '', 'textarea' ); vectone_admin_field_v2( 'guide', 'description', 'Introduction', $c['guide']['description'] ?? '', 'textarea' ); ?>
                        <div class="vm-admin-full"><h3>Profils du guide</h3><?php $profiles = array_pad( (array) ( $c['guide']['profiles'] ?? array() ), 5, array( 'title' => '', 'description' => '', 'details' => '', 'cta_text' => '', 'cta_url' => '' ) ); foreach ( $profiles as $i => $row ) : ?><div class="vm-admin-repeater"><strong>Profil <?php echo esc_html( $i + 1 ); ?></strong><div class="vm-admin-fields"><?php foreach ( array( 'title' => 'Titre', 'description' => 'Description', 'details' => 'Détails', 'cta_text' => 'Texte CTA', 'cta_url' => 'URL CTA' ) as $key => $field_label ) vectone_admin_field_v2( 'guide[profiles][' . $i . ']', $key, $field_label, $row[ $key ] ?? '', in_array( $key, array( 'description', 'details' ), true ) ? 'textarea' : ( 'cta_url' === $key ? 'url' : 'text' ) ); ?></div></div><?php endforeach; ?></div>
                    </div></details>
                    <details><summary>FAQ de la homepage</summary><div class="vm-admin-details-body"><p class="description">Si aucune FAQ active n’est saisie, le contenu historique reste utilisé.</p><?php vectone_admin_faq_repeater_v2( 'faq', $c['faq'] ?? array(), 6 ); ?></div></details>
                </section>
                <section id="vm-admin-topbar" class="vm-admin-section"><div class="vm-admin-section-heading"><span class="vm-admin-kicker">Navigation globale</span><h2>Topbar</h2><p>Les textes de la barre supérieure.</p></div><div class="vm-admin-fields"><?php vectone_admin_field_v2( 'topbar', 'slogan', 'Slogan', $c['topbar']['slogan'] ?? '' ); vectone_admin_field_v2( 'topbar', 'help', 'Texte aide', $c['topbar']['help'] ?? '' ); vectone_admin_field_v2( 'topbar', 'email', 'Email', $c['topbar']['email'] ?? '', 'email' ); ?></div></section>
                <section id="vm-admin-footer" class="vm-admin-section"><div class="vm-admin-section-heading"><span class="vm-admin-kicker">Signature</span><h2>Footer</h2><p>La structure publique du footer reste inchangée.</p></div><div class="vm-admin-fields"><?php foreach ( array( 'line1' => 'Ligne 1', 'line2' => 'Ligne 2', 'line3' => 'Ligne 3', 'cta_text' => 'Texte CTA', 'cta_url' => 'URL CTA', 'secondary' => 'Texte secondaire', 'email' => 'Email' ) as $key => $label ) vectone_admin_field_v2( 'footer', $key, $label, $c['footer'][ $key ] ?? '', 'secondary' === $key ? 'textarea' : ( 'cta_url' === $key ? 'url' : ( 'email' === $key ? 'email' : 'text' ) ) ); ?></div></section>
                <?php foreach ( $types as $type => $label ) : $row = $c['categories'][ $type ] ?? array(); ?>
                    <section id="vm-admin-<?php echo esc_attr( $type ); ?>" class="vm-admin-section"><div class="vm-admin-section-heading"><span class="vm-admin-kicker">Page forfaits</span><h2><?php echo esc_html( $label ); ?></h2><p>Les champs vides conservent les textes historiques de cette page.</p></div>
                        <details open><summary>Hero et introduction</summary><div class="vm-admin-fields vm-admin-details-body"><?php foreach ( array( 'eyebrow' => 'Surtitre', 'title' => 'Titre principal', 'intro' => 'Introduction' ) as $key => $field_label ) vectone_admin_field_v2( 'categories[' . $type . ']', $key, $field_label, $row[ $key ] ?? '', in_array( $key, array( 'title', 'intro' ), true ) ? 'textarea' : 'text' ); ?></div></details>
                        <details open><summary>Offres</summary><div class="vm-admin-fields vm-admin-details-body"><?php vectone_admin_field_v2( 'categories[' . $type . ']', 'offers_title', 'Titre de la section', $row['offers_title'] ?? '' ); vectone_admin_field_v2( 'categories[' . $type . ']', 'offers_sub', 'Sous-titre', $row['offers_sub'] ?? '', 'textarea' ); ?></div></details>
                        <details><summary>Avis, FAQ et actualités</summary><div class="vm-admin-fields vm-admin-details-body"><?php vectone_admin_field_v2( 'categories[' . $type . ']', 'reviews_title', 'Titre Avis', $row['reviews_title'] ?? '' ); vectone_admin_field_v2( 'categories[' . $type . ']', 'reviews_text', 'Sous-titre Avis', $row['reviews_text'] ?? '', 'textarea' ); vectone_admin_field_v2( 'categories[' . $type . ']', 'faq_title', 'Titre FAQ', $row['faq_title'] ?? '' ); vectone_admin_field_v2( 'categories[' . $type . ']', 'news_title', 'Titre Actualités', $row['news_title'] ?? '' ); vectone_admin_field_v2( 'categories[' . $type . ']', 'news_text', 'Sous-titre Actualités', $row['news_text'] ?? '', 'textarea' ); ?></div></details>
                        <details><summary>CTA final</summary><div class="vm-admin-fields vm-admin-details-body"><?php vectone_admin_field_v2( 'categories[' . $type . ']', 'final_title', 'Titre CTA', $row['final_title'] ?? '', 'textarea' ); vectone_admin_field_v2( 'categories[' . $type . ']', 'final_text', 'Texte d’accompagnement', $row['final_text'] ?? '', 'textarea' ); vectone_admin_field_v2( 'categories[' . $type . ']', 'cta_text', 'Texte du bouton', $row['cta_text'] ?? '' ); vectone_admin_field_v2( 'categories[' . $type . ']', 'final_url', 'URL du bouton', $row['final_url'] ?? '', 'url' ); ?></div></details>
                        <details><summary>Textes secondaires</summary><div class="vm-admin-fields vm-admin-details-body"><?php foreach ( array( 'benefits_title' => 'Titre des bénéfices', 'guide_eyebrow' => 'Surtitre guide', 'guide_title' => 'Titre guide', 'guide_intro' => 'Introduction guide', 'advice_title' => 'Titre conseil', 'advice_text' => 'Texte conseil', 'help_text' => 'Texte lien aide', 'help_url' => 'URL lien aide' ) as $key => $field_label ) vectone_admin_field_v2( 'categories[' . $type . ']', $key, $field_label, $row[ $key ] ?? '', in_array( $key, array( 'guide_intro', 'advice_text' ), true ) ? 'textarea' : ( 'help_url' === $key ? 'url' : 'text' ) ); ?></div></details>
                        <?php if ( 'avec' === $type ) : ?><details><summary>État vide</summary><div class="vm-admin-fields vm-admin-details-body"><?php vectone_admin_field_v2( 'categories[' . $type . ']', 'empty_title', 'Titre', $row['empty_title'] ?? '' ); vectone_admin_field_v2( 'categories[' . $type . ']', 'empty_text', 'Description', $row['empty_text'] ?? '', 'textarea' ); vectone_admin_field_v2( 'categories[' . $type . ']', 'empty_cta_text', 'Texte CTA', $row['empty_cta_text'] ?? '' ); vectone_admin_field_v2( 'categories[' . $type . ']', 'empty_url', 'URL CTA', $row['empty_url'] ?? '', 'url' ); ?></div></details><?php endif; ?>
                        <?php if ( in_array( $type, array( 'avec', 'data', 'low', 'international' ), true ) ) : ?><details><summary>Bénéfices</summary><div class="vm-admin-details-body"><?php vectone_admin_repeater_v2( 'categories[' . $type . '][benefits]', $row['benefits'] ?? array(), 3, false ); ?></div></details><details><summary>Guide détaillé</summary><div class="vm-admin-details-body"><?php vectone_admin_repeater_v2( 'categories[' . $type . '][guide]', $row['guide'] ?? array(), 4, true ); ?></div></details><?php endif; ?>
                        <details><summary>FAQ de cette page</summary><div class="vm-admin-details-body"><p class="description">Les articles et les avis Elementor restent gérés dans leurs outils d’origine.</p><?php vectone_admin_faq_repeater_v2( 'categories[' . $type . '][faq]', $row['faq'] ?? array(), 6 ); ?></div></details>
                    </section>
                <?php endforeach; ?>
                <section id="vm-admin-sources" class="vm-admin-section"><div class="vm-admin-section-heading"><span class="vm-admin-kicker">Repères</span><h2>Sources d’édition</h2><p>Chaque contenu garde son outil d’administration naturel.</p></div><div class="vm-admin-source-grid"><div><strong>Produits, prix et offres</strong><span>WooCommerce → Produits</span></div><div><strong>Articles et actualités</strong><span>WordPress → Articles</span></div><div><strong>Avis et widgets Elementor</strong><span>Page ou modèle → Modifier avec Elementor</span></div><div><strong>Menus</strong><span>Apparence → Menus</span></div><div><strong>Formulaire</strong><span>Contact → Formulaires de contact</span></div><div><strong>Images</strong><span>Médiathèque WordPress</span></div></div></section>
                <div class="vm-admin-submit"><?php submit_button( 'Enregistrer les contenus', 'primary', 'submit', false ); ?><span>Les modifications restent dans l’option WordPress existante.</span></div>
            </form>
        </div>
    </div>
    <style>
        .vm-content-admin{max-width:1500px}.vm-admin-intro{font-size:14px;color:#566b66;margin:8px 0 22px}.vm-admin-layout{display:grid;grid-template-columns:210px minmax(0,1fr);gap:24px;align-items:start}.vm-admin-sidebar{position:sticky;top:42px;background:#123d35;border-radius:12px;padding:16px 12px;box-shadow:0 8px 24px rgba(18,61,53,.12)}.vm-admin-sidebar a{display:block;color:#eaf5f1;text-decoration:none;padding:9px 10px;border-radius:7px;font-weight:600}.vm-admin-sidebar a:hover,.vm-admin-sidebar a:focus{background:#1d594d;color:#d9ff65}.vm-admin-sidebar-title{text-transform:uppercase;color:#a7c6bd;font-size:10px;font-weight:700;letter-spacing:.12em;margin:12px 10px 5px}.vm-admin-form{min-width:0}.vm-admin-section{scroll-margin-top:30px;background:#fff;border:1px solid #d9e4e0;border-radius:14px;padding:24px;margin:0 0 22px;box-shadow:0 3px 16px rgba(18,61,53,.05)}.vm-admin-section-heading{border-bottom:1px solid #e7efed;margin-bottom:18px;padding-bottom:14px}.vm-admin-section-heading h2{font-size:24px;color:#123d35;margin:3px 0 5px}.vm-admin-section-heading p{margin:0;color:#687c77}.vm-admin-kicker{color:#f2384f;text-transform:uppercase;font-size:11px;font-weight:700;letter-spacing:.12em}.vm-admin-section details{border:1px solid #dfe9e6;border-radius:10px;margin:12px 0;background:#fbfdfc}.vm-admin-section summary{cursor:pointer;list-style:none;padding:15px 17px;font-size:16px;font-weight:700;color:#123d35}.vm-admin-section summary::-webkit-details-marker{display:none}.vm-admin-section summary:before{content:'+';display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;margin-right:9px;background:#eaf5f1;color:#123d35}.vm-admin-section details[open] summary:before{content:'−';background:#d9ff65}.vm-admin-details-body{padding:0 17px 17px}.vm-admin-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 20px}.vm-admin-field{min-width:0}.vm-admin-field.is-wide,.vm-admin-full{grid-column:1/-1}.vm-admin-field label{display:block;color:#264a42;font-size:12px;font-weight:700;margin-bottom:5px}.vm-admin-field input[type=text],.vm-admin-field input[type=url],.vm-admin-field input[type=email],.vm-admin-field textarea{width:100%;box-sizing:border-box;border:1px solid #cbdad5;border-radius:6px;padding:8px 10px;background:#fff}.vm-admin-field textarea{min-height:74px;resize:vertical}.vm-admin-field .description{margin-top:5px}.vm-admin-inline-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 20px;margin-top:14px}.vm-admin-check{align-self:end;padding:10px 0;color:#264a42;font-weight:600}.vm-admin-media-field{display:flex;gap:18px;align-items:center}.vm-home-hero-preview{width:150px;height:85px;object-fit:cover;border-radius:7px;border:1px solid #d9e4e0}.vm-admin-repeater{border:1px solid #dce8e4;border-radius:9px;padding:15px;margin:0 0 12px;background:#fff}.vm-admin-repeater>strong{display:block;color:#123d35;margin-bottom:12px}.vm-admin-row-meta{display:flex;gap:16px;align-items:center;margin-top:12px}.vm-admin-row-meta input[type=number]{width:90px}.vm-admin-row-meta label{font-size:12px;font-weight:700;color:#264a42}.vm-admin-source-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.vm-admin-source-grid div{border:1px solid #dce8e4;border-radius:9px;padding:14px}.vm-admin-source-grid strong,.vm-admin-source-grid span{display:block}.vm-admin-source-grid strong{color:#123d35}.vm-admin-source-grid span{font-size:13px;color:#687c77;margin-top:4px}.vm-admin-submit{position:sticky;bottom:18px;display:flex;align-items:center;gap:14px;background:#123d35;padding:12px 16px;border-radius:10px;box-shadow:0 8px 22px rgba(18,61,53,.2);z-index:5}.vm-admin-submit .button-primary{background:#f2384f;border-color:#f2384f;font-size:14px}.vm-admin-submit span{color:#d7e8e3;font-size:12px}@media(max-width:900px){.vm-admin-layout{grid-template-columns:1fr}.vm-admin-sidebar{position:static;display:flex;gap:5px;overflow:auto;white-space:nowrap}.vm-admin-sidebar-title{display:none}.vm-admin-sidebar a{flex:0 0 auto}.vm-admin-fields,.vm-admin-inline-fields,.vm-admin-source-grid{grid-template-columns:1fr}}@media(max-width:600px){.vm-admin-section{padding:16px}.vm-admin-media-field{align-items:flex-start;flex-direction:column}.vm-admin-submit{align-items:flex-start;flex-direction:column}.vm-admin-submit span{display:block}}
    </style>
    <script>
    document.addEventListener('DOMContentLoaded',function(){var mediaButton=document.querySelector('.vm-media-button');if(mediaButton&&window.wp&&wp.media){mediaButton.addEventListener('click',function(){var input=document.querySelector('input[name="vectone_site_content[second_visual][image]"]'),frame=wp.media({title:'Choisir une image',button:{text:'Utiliser cette image'},multiple:false});frame.on('select',function(){var item=frame.state().get('selection').first().toJSON();if(input)input.value=item.url;});frame.open();});}var select=document.querySelector('.vm-home-hero-select'),remove=document.querySelector('.vm-home-hero-remove'),id=document.querySelector('.vm-home-hero-id'),preview=document.querySelector('.vm-home-hero-preview');if(select&&window.wp&&wp.media){select.addEventListener('click',function(){var frame=wp.media({title:'Choisir l’image du Hero',button:{text:'Utiliser cette image'},multiple:false});frame.on('select',function(){var item=frame.state().get('selection').first().toJSON();id.value=item.id;preview.src=item.sizes&&item.sizes.thumbnail?item.sizes.thumbnail.url:item.url;preview.style.display='block';});frame.open();});}if(remove){remove.addEventListener('click',function(){id.value='0';preview.src='';preview.style.display='none';});}});
    </script>
    <?php
}

function vectone_admin_field_v2( $group, $key, $label, $value = '', $type = 'text' ) {
    $name = 'vectone_site_content[' . $group . '][' . $key . ']';
    $wide = 'textarea' === $type ? ' is-wide' : '';
    echo '<div class="vm-admin-field' . esc_attr( $wide ) . '"><label>' . esc_html( $label ) . '</label>';
    if ( 'textarea' === $type ) echo '<textarea name="' . esc_attr( $name ) . '" rows="3">' . esc_textarea( $value ) . '</textarea>';
    else echo '<input type="' . esc_attr( $type ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '">';
    echo '</div>';
}

function vectone_admin_faq_repeater_v2( $path, $rows, $count = 6 ) {
    $rows = array_pad( (array) $rows, $count, array( 'question' => '', 'answer' => '', 'order' => 0, 'active' => 0 ) );
    foreach ( $rows as $i => $row ) : ?>
        <div class="vm-admin-repeater"><strong>Question <?php echo esc_html( $i + 1 ); ?></strong><div class="vm-admin-fields">
            <?php vectone_admin_field_v2( $path . '[' . $i . ']', 'question', 'Question', $row['question'] ?? '', 'text' ); vectone_admin_field_v2( $path . '[' . $i . ']', 'answer', 'Réponse', $row['answer'] ?? '', 'textarea' ); ?>
        </div><div class="vm-admin-row-meta"><label>Ordre <input type="number" min="0" name="vectone_site_content[<?php echo esc_attr( $path ); ?>][<?php echo esc_attr( $i ); ?>][order]" value="<?php echo esc_attr( $row['order'] ?? 0 ); ?>"></label><label><input type="checkbox" name="vectone_site_content[<?php echo esc_attr( $path ); ?>][<?php echo esc_attr( $i ); ?>][active]" value="1" <?php checked( $row['active'] ?? 0, 1 ); ?>> Actif</label></div></div>
    <?php endforeach;
}

function vectone_admin_repeater_v2( $path, $rows, $count = 3, $with_text = false ) {
    $rows = array_pad( (array) $rows, $count, array( 'title' => '', 'description' => '', 'text' => '', 'order' => 0, 'active' => 0 ) );
    foreach ( $rows as $i => $row ) : ?>
        <div class="vm-admin-repeater"><strong><?php echo $with_text ? 'Élément ' : 'Bénéfice '; echo esc_html( $i + 1 ); ?></strong><div class="vm-admin-fields">
            <?php vectone_admin_field_v2( $path . '[' . $i . ']', 'title', 'Titre', $row['title'] ?? '' ); vectone_admin_field_v2( $path . '[' . $i . ']', 'description', 'Description', $row['description'] ?? '', 'textarea' ); if ( $with_text ) vectone_admin_field_v2( $path . '[' . $i . ']', 'text', 'Texte détaillé', $row['text'] ?? '', 'textarea' ); ?>
        </div><div class="vm-admin-row-meta"><label>Ordre <input type="number" min="0" name="vectone_site_content[<?php echo esc_attr( $path ); ?>][<?php echo esc_attr( $i ); ?>][order]" value="<?php echo esc_attr( $row['order'] ?? 0 ); ?>"></label><label><input type="checkbox" name="vectone_site_content[<?php echo esc_attr( $path ); ?>][<?php echo esc_attr( $i ); ?>][active]" value="1" <?php checked( $row['active'] ?? 0, 1 ); ?>> Actif</label></div></div>
    <?php endforeach;
}

function vectone_site_content_text_row( $group, $key, $label, $c, $textarea = false ) {
    $value = $c[ $group ][ $key ] ?? ''; echo '<tr><th>' . esc_html( $label ) . '</th><td>' . ( $textarea ? '<textarea class="large-text" rows="3"' : '<input class="regular-text"' ) . ' name="vectone_site_content[' . esc_attr( $group ) . '][' . esc_attr( $key ) . ']">' . ( $textarea ? esc_textarea( $value ) . '</textarea>' : ' value="' . esc_attr( $value ) . '">' ) . '</td></tr>';
}

/* Apply editorial category values to the existing shortcode/PHP output without moving its layout. */
function vectone_site_content_category_filter( $content ) {
    if ( is_admin() || ! is_string( $content ) ) return $content;
    $c = vectone_site_content(); $cats = $c['categories'] ?? array();
    $map = array(
        'sans' => array( 'Les forfaits sans engagement', 'Comparez les offres mobiles sans durée minimale d’engagement et trouvez le forfait qui correspond réellement à vos usages et à votre budget.' ),
        'avec' => array( 'Les forfaits avec engagement', 'Comparez les offres mobiles avec durée d’engagement et trouvez une formule adaptée à vos usages, à votre budget et aux conditions proposées par chaque opérateur.' ),
        'data' => array( 'Forfaits avec un maximum de data', 'Comparez les forfaits offrant le plus de data parmi les offres disponibles.' ),
        'low' => array( 'Forfaits mobiles à petit prix', 'Comparez les offres les plus accessibles actuellement disponibles.' ),
        'international' => array( 'Forfaits pour rester connecté à l’international', 'Comparez les offres proposant des usages adaptés aux déplacements et communications internationales.' ),
    );
    foreach ( $map as $type => $fallback ) {
        $row = $cats[ $type ] ?? array();
        if ( ! empty( $row['title'] ) ) $content = str_replace( $fallback[0], $row['title'], $content );
        if ( ! empty( $row['intro'] ) ) $content = str_replace( $fallback[1], $row['intro'], $content );
        if ( ! empty( $row['faq_title'] ) ) $content = str_replace( 'Questions fréquentes', $row['faq_title'], $content );
    }
    $type = false;
    if ( function_exists( 'is_product_category' ) && is_product_category( 201 ) ) $type = 'sans';
    elseif ( function_exists( 'is_product_category' ) && is_product_category( 202 ) ) $type = 'avec';
    elseif ( function_exists( 'is_page' ) && is_page( 6672 ) ) $type = 'data';
    elseif ( function_exists( 'is_page' ) && is_page( 6673 ) ) $type = 'low';
    elseif ( function_exists( 'is_page' ) && is_page( 6674 ) ) $type = 'international';
    if ( $type ) $content = vectone_site_content_apply_category_secondary( $content, $type, $cats[ $type ] ?? array() );
    if ( function_exists( 'is_product_category' ) && is_product_category( 201 ) ) {
        $row = $cats['sans'] ?? array();
        if ( ! empty( $row['reviews_title'] ) ) $content = str_replace( 'Ce que disent nos utilisateurs.', $row['reviews_title'], $content );
        if ( ! empty( $row['reviews_text'] ) ) $content = str_replace( 'Ce que disent nos utilisateurs.', $row['reviews_text'], $content );
        if ( ! empty( $row['news_title'] ) ) $content = str_replace( 'Ce quâ€™il faut savoir avant de choisir.', $row['news_title'], $content );
        if ( ! empty( $row['news_text'] ) ) $content = str_replace( 'DÃ©cryptages et conseils pour mieux comprendre les offres mobiles.', $row['news_text'], $content );
    }
    return $content;
}

function vectone_site_content_apply_category_secondary( $html, $type, $row ) {
    foreach ( array( 'benefits_title' => '/(<div class="vm-sans-section-heading">.*?<h2[^>]*>).*?(<\/h2>)/si', 'guide_eyebrow' => '/(<section[^>]+class="vm-sans-guide"[^>]*>\s*<span[^>]*>).*?(<\/span>)/si', 'guide_title' => '/(<section[^>]+class="vm-sans-guide"[^>]*>.*?<h2[^>]*>).*?(<\/h2>)/si', 'guide_intro' => '/(<section[^>]+class="vm-sans-guide"[^>]*>.*?<p class="vm-sans-guide__intro">).*?(<\/p>)/si', 'advice_title' => '/(<aside class="vm-sans-advice">\s*<h3>).*?(<\/h3>)/si', 'advice_text' => '/(<aside class="vm-sans-advice">.*?<p>).*?(<\/p>)/si' ) as $key => $pattern ) {
        if ( '' !== trim( (string) ( $row[ $key ] ?? '' ) ) ) $html = preg_replace( $pattern, '$1' . esc_html( $row[ $key ] ) . '$2', $html, 1 );
    }
    if ( '' !== trim( (string) ( $row['help_text'] ?? '' ) ) ) $html = preg_replace( '/(<a class="vm-sans-contact"[^>]*>).*?(<\/a>)/si', '$1' . esc_html( $row['help_text'] ) . '$2', $html, 1 );
    if ( '' !== trim( (string) ( $row['help_url'] ?? '' ) ) ) $html = preg_replace( '/(<a class="vm-sans-contact" href=")[^"]*("[^>]*>)/si', '$1' . esc_url( $row['help_url'] ) . '$2', $html, 1 );
    return $html;
}
add_filter( 'the_content', 'vectone_site_content_category_filter', 99 );

function vectone_site_content_faq_filter( $content ) {
    if ( is_admin() || ! is_string( $content ) ) return $content;
    $items = array_filter( (array) ( vectone_site_content()['faq'] ?? array() ), function( $row ) { return ! empty( $row['question'] ) && ! empty( $row['active'] ); } );
    if ( ! $items ) return $content;
    usort( $items, function( $a, $b ) { return (int) ( $a['order'] ?? 0 ) <=> (int) ( $b['order'] ?? 0 ); } );
    $html = '<section class="vm-sans-faq" aria-labelledby="vm-admin-faq-title"><span class="vm-category-eyebrow">À SAVOIR</span><h2 id="vm-admin-faq-title">Questions fréquentes</h2>';
    foreach ( array_slice( $items, 0, 10 ) as $item ) $html .= '<details><summary>' . esc_html( $item['question'] ) . '</summary><p>' . esc_html( $item['answer'] ?? '' ) . '</p></details>';
    $html .= '</section>';
    return preg_replace( '/<section class="vm-sans-faq".*?<\/section>/si', $html, $content, 1 );
}
add_filter( 'the_content', 'vectone_site_content_faq_filter', 100 );

/* Low-cost editorial fields are injected into the existing content form only. */
function vectone_low_admin_fields() {
    if ( ! is_admin() || ( $_GET['page'] ?? '' ) !== 'vectone-site-content' ) return;
    $c = vectone_site_content();
    $benefits = array_pad( (array) ( $c['categories']['low']['benefits'] ?? array() ), 3, array( 'title' => '', 'description' => '', 'order' => 0, 'active' => 0 ) );
    $guide = array_pad( (array) ( $c['categories']['low']['guide'] ?? array() ), 4, array( 'title' => '', 'description' => '', 'text' => '', 'order' => 0, 'active' => 0 ) );
    echo '<script>document.addEventListener("DOMContentLoaded",function(){var f=document.querySelector("form[action=\"options.php\"]");if(!f)return;var h=document.createElement("div");h.innerHTML=' . wp_json_encode( '<h2>Low-cost — bénéfices et guide</h2><p>Ces champs remplacent les textes historiques lorsqu’ils sont renseignés.</p>' ) . ';f.insertBefore(h,f.querySelector("input[type=submit]").closest("p")||f.lastElementChild);});</script>';
    $json_b = wp_json_encode( $benefits ); $json_g = wp_json_encode( $guide );
    echo '<script>document.addEventListener("DOMContentLoaded",function(){var f=document.querySelector("form[action=\\"options.php\\"]");if(!f)return;var b=' . $json_b . ',g=' . $json_g . ',h=document.createElement("div");function row(path,i,x,guide){var p="vectone_site_content[categories][low]["+path+"]["+i+"]";return "<tr><td><input class=\"regular-text\" name=\""+p+"[title]\" value=\""+(x.title||"")+"\"></td><td><textarea name=\""+p+"[description]\">"+(x.description||x.text||"")+"</textarea></td><td><input type=\"number\" name=\""+p+"[order]\" value=\""+(x.order||0)+"\"></td><td><input type=\"checkbox\" name=\""+p+"[active]\" value=\"1\" "+(x.active?"checked":"")+"><\/td><\/tr>";}h.innerHTML="<h2>Low-cost — bénéfices</h2><table class=\"widefat striped\"><tr><th>Titre<\/th><th>Description<\/th><th>Ordre<\/th><th>Actif<\/th><\/tr>"+b.map(function(x,i){return row("benefits",i,x,false)}).join("")+"<\/table><h2>Low-cost — guide</h2><table class=\"widefat striped\"><tr><th>Titre<\/th><th>Description<\/th><th>Ordre<\/th><th>Actif<\/th><\/tr>"+g.map(function(x,i){return row("guide",i,x,true)}).join("")+"<\/table>";f.insertBefore(h,f.querySelector("input[type=submit]").closest("p")||f.lastElementChild);});</script>';
}
add_action( 'admin_footer', 'vectone_low_admin_fields' );

function vectone_international_admin_fields() {
    if ( ! is_admin() || ( $_GET['page'] ?? '' ) !== 'vectone-site-content' ) return;
    $c = vectone_site_content();
    $benefits = array_pad( (array) ( $c['categories']['international']['benefits'] ?? array() ), 3, array( 'title' => '', 'description' => '', 'order' => 0, 'active' => 0 ) );
    $guide = array_pad( (array) ( $c['categories']['international']['guide'] ?? array() ), 4, array( 'title' => '', 'description' => '', 'text' => '', 'order' => 0, 'active' => 0 ) );
    echo '<script>document.addEventListener("DOMContentLoaded",function(){var f=document.querySelector("form[action=\"options.php\"]");if(!f)return;var b=' . wp_json_encode( $benefits ) . ',g=' . wp_json_encode( $guide ) . ',h=document.createElement("div");function rows(path,a){return a.map(function(x,i){var p="vectone_site_content[categories][international]["+path+"]["+i+"]";return "<tr><td><input class=\"regular-text\" name=\""+p+"[title]\" value=\""+(x.title||"")+"\"></td><td><textarea name=\""+p+"[description]\">"+(x.description||x.text||"")+"</textarea></td><td><input type=\"number\" name=\""+p+"[order]\" value=\""+(x.order||0)+"\"></td><td><input type=\"checkbox\" name=\""+p+"[active]\" value=\"1\" "+(x.active?"checked":"")+"><\/td></tr>"}).join("")}h.innerHTML="<h2>International — bénéfices</h2><table class=\"widefat striped\"><tr><th>Titre<\/th><th>Description<\/th><th>Ordre<\/th><th>Actif<\/th><\/tr>"+rows("benefits",b)+"<\/table><h2>International — guide</h2><table class=\"widefat striped\"><tr><th>Titre<\/th><th>Description<\/th><th>Ordre<\/th><th>Actif<\/th><\/tr>"+rows("guide",g)+"<\/table>";f.insertBefore(h,f.querySelector("input[type=submit]").closest("p")||f.lastElementChild);});</script>';
}
add_action( 'admin_footer', 'vectone_international_admin_fields' );

function vectone_category_faq_admin_fields() {
    if ( ! is_admin() || ( $_GET['page'] ?? '' ) !== 'vectone-site-content' ) return;
    $content = vectone_site_content();
    $groups = array( 'avec' => 'Avec engagement', 'data' => 'Data', 'low' => 'Low-cost', 'international' => 'International' );
    $payload = array();
    foreach ( $groups as $type => $label ) $payload[ $type ] = array_pad( (array) ( $content['categories'][ $type ]['faq'] ?? array() ), 5, array( 'question' => '', 'answer' => '', 'order' => 0, 'active' => 0 ) );
    echo '<script>document.addEventListener("DOMContentLoaded",function(){var f=document.querySelector("form[action=\\"options.php\\"]");if(!f)return;var data=' . wp_json_encode( $payload ) . ',root=document.createElement("div");Object.keys(data).forEach(function(type){var title={avec:"Avec engagement",data:"Data",low:"Low-cost",international:"International"}[type],rows=data[type].map(function(x,i){var p="vectone_site_content[categories]["+type+"][faq]["+i+"]";return "<tr><td><input class=\\"regular-text\\" name=\\""+p+"[question]\\" value=\\""+(x.question||"")+"\\"></td><td><textarea name=\\""+p+"[answer]\\" rows=\\"2\\">"+(x.answer||"")+"</textarea></td><td><input type=\\"number\\" name=\\""+p+"[order]\\" value=\\""+(x.order||0)+"\\"></td><td><input type=\\"checkbox\\" name=\\""+p+"[active]\\" value=\\"1\\" "+(x.active?"checked":"")+"></td></tr>";}).join("");root.insertAdjacentHTML("beforeend","<h2>"+title+" — FAQ</h2><table class=\\"widefat striped\\"><thead><tr><th>Question</th><th>Réponse</th><th>Ordre</th><th>Actif</th></tr></thead><tbody>"+rows+"</tbody></table>");});f.insertBefore(root,f.querySelector("input[type=submit]").closest("p")||f.lastElementChild);});</script>';
}
add_action( 'admin_footer', 'vectone_category_faq_admin_fields', 30 );

function vectone_home_hero_admin_field() {
    if ( ! is_admin() || ( $_GET['page'] ?? '' ) !== 'vectone-site-content' ) return;
    $c = vectone_site_content();
    $id = absint( $c['home']['hero_image_id'] ?? 0 );
    $url = $id ? wp_get_attachment_image_url( $id, 'thumbnail' ) : '';
    $html = '<h2>Accueil — Hero</h2><table class="form-table"><tr><th>Image principale du Hero</th><td><input type="hidden" class="vm-home-hero-id" name="vectone_site_content[home][hero_image_id]" value="' . esc_attr( $id ) . '"><img class="vm-home-hero-preview" src="' . esc_url( $url ?: '' ) . '" style="max-width:120px;height:auto;display:' . ( $url ? 'block' : 'none' ) . ';margin-bottom:8px"><button type="button" class="button vm-home-hero-select">Sélectionner une image</button> <button type="button" class="button vm-home-hero-remove">Retirer</button><p class="description">Image administrable via la Médiathèque. Si elle est vide, l’image Hero actuelle du thème reste utilisée.</p></td></tr></table>';
    echo '<script>document.addEventListener("DOMContentLoaded",function(){var f=document.querySelector("form[action=\\"options.php\\"]");if(!f)return;var h=document.createElement("div");h.innerHTML=' . wp_json_encode( $html ) . ';f.insertBefore(h,f.querySelector("input[type=submit]").closest("p")||f.lastElementChild);var select=h.querySelector(".vm-home-hero-select"),remove=h.querySelector(".vm-home-hero-remove"),id=h.querySelector(".vm-home-hero-id"),preview=h.querySelector(".vm-home-hero-preview");if(select&&window.wp&&wp.media){select.onclick=function(){var frame=wp.media({title:"Choisir l’image du Hero",button:{text:"Utiliser cette image"},multiple:false});frame.on("select",function(){var a=frame.state().get("selection").first().toJSON();id.value=a.id;preview.src=a.sizes&&a.sizes.thumbnail?a.sizes.thumbnail.url:a.url;preview.style.display="block";});frame.open();};}if(remove)remove.onclick=function(){id.value="0";preview.src="";preview.style.display="none";};});</script>';
}
add_action( 'admin_footer', 'vectone_home_hero_admin_field', 20 );

function vectone_empty_state_admin_field() {
    if ( ! is_admin() || ( $_GET['page'] ?? '' ) !== 'vectone-site-content' ) return;
    $c = vectone_site_content(); $r = $c['categories']['avec'] ?? array();
    $html = '<h2>Avec engagement — état vide</h2><table class="form-table"><tr><th>Titre</th><td><input class="regular-text" name="vectone_site_content[categories][avec][empty_title]" value="' . esc_attr( $r['empty_title'] ?? '' ) . '"></td></tr><tr><th>Description</th><td><textarea class="large-text" rows="3" name="vectone_site_content[categories][avec][empty_text]">' . esc_textarea( $r['empty_text'] ?? '' ) . '</textarea></td></tr><tr><th>Texte CTA</th><td><input class="regular-text" name="vectone_site_content[categories][avec][empty_cta_text]" value="' . esc_attr( $r['empty_cta_text'] ?? '' ) . '"></td></tr><tr><th>URL CTA</th><td><input type="url" class="regular-text" name="vectone_site_content[categories][avec][empty_url]" value="' . esc_attr( $r['empty_url'] ?? '' ) . '"></td></tr></table>';
    echo '<script>document.addEventListener("DOMContentLoaded",function(){var f=document.querySelector("form[action=\\"options.php\\"]");if(!f)return;var h=document.createElement("div");h.innerHTML=' . wp_json_encode( $html ) . ';f.insertBefore(h,f.querySelector("input[type=submit]").closest("p")||f.lastElementChild);});</script>';
}
add_action( 'admin_footer', 'vectone_empty_state_admin_field', 25 );

function vectone_home_contact_admin_field() {
    if ( ! is_admin() || ( $_GET['page'] ?? '' ) !== 'vectone-site-content' ) return;
    $c = vectone_site_content(); $r = $c['home'] ?? array();
    $html = '<h2>Accueil — Contact homepage</h2><table class="form-table"><tr><th>Surtitre</th><td><input class="regular-text" name="vectone_site_content[home][contact_eyebrow]" value="' . esc_attr( $r['contact_eyebrow'] ?? '' ) . '"></td></tr><tr><th>Titre</th><td><input class="regular-text" name="vectone_site_content[home][contact_title]" value="' . esc_attr( $r['contact_title'] ?? '' ) . '"></td></tr><tr><th>Introduction</th><td><textarea class="large-text" rows="3" name="vectone_site_content[home][contact_text]">' . esc_textarea( $r['contact_text'] ?? '' ) . '</textarea></td></tr><tr><th>Libellé contact direct</th><td><input class="regular-text" name="vectone_site_content[home][contact_direct_label]" value="' . esc_attr( $r['contact_direct_label'] ?? '' ) . '"></td></tr><tr><th>Titre formulaire</th><td><input class="regular-text" name="vectone_site_content[home][contact_form_title]" value="' . esc_attr( $r['contact_form_title'] ?? '' ) . '"></td></tr><tr><th>Note formulaire</th><td><textarea class="large-text" rows="2" name="vectone_site_content[home][contact_form_note]">' . esc_textarea( $r['contact_form_note'] ?? '' ) . '</textarea></td></tr></table>';
    echo '<script>document.addEventListener("DOMContentLoaded",function(){var f=document.querySelector("form[action=\\"options.php\\"]");if(!f)return;var h=document.createElement("div");h.innerHTML=' . wp_json_encode( $html ) . ';f.insertBefore(h,f.querySelector("input[type=submit]").closest("p")||f.lastElementChild);});</script>';
}
add_action( 'admin_footer', 'vectone_home_contact_admin_field', 25 );

function vectone_category_secondary_admin_fields() {
    if ( ! is_admin() || ( $_GET['page'] ?? '' ) !== 'vectone-site-content' ) return;
    $c = vectone_site_content(); $types = array( 'sans' => 'Sans engagement', 'avec' => 'Avec engagement', 'data' => 'Data', 'low' => 'Low-cost', 'international' => 'International' ); $payload = array();
    foreach ( $types as $type => $label ) $payload[ $type ] = array( 'label' => $label, 'values' => $c['categories'][ $type ] ?? array() );
    echo '<script>document.addEventListener("DOMContentLoaded",function(){var f=document.querySelector("form[action=\\"options.php\\"]");if(!f)return;var data=' . wp_json_encode( $payload ) . ',root=document.createElement("div");Object.keys(data).forEach(function(type){var x=data[type],p="vectone_site_content[categories]["+type+"]",v=x.values||{},html="<h2>"+x.label+" — textes secondaires</h2><table class=\\"form-table\\"><tr><th>Titre des bénéfices</th><td><input class=\\"regular-text\\" name=\\""+p+"[benefits_title]\\" value=\\""+(v.benefits_title||"")+"\\"></td></tr><tr><th>Eyebrow guide</th><td><input class=\\"regular-text\\" name=\\""+p+"[guide_eyebrow]\\" value=\\""+(v.guide_eyebrow||"")+"\\"></td></tr><tr><th>Titre guide</th><td><input class=\\"regular-text\\" name=\\""+p+"[guide_title]\\" value=\\""+(v.guide_title||"")+"\\"></td></tr><tr><th>Introduction guide</th><td><textarea class=\\"large-text\\" name=\\""+p+"[guide_intro]\\">"+(v.guide_intro||"")+"</textarea></td></tr><tr><th>Titre conseil</th><td><input class=\\"regular-text\\" name=\\""+p+"[advice_title]\\" value=\\""+(v.advice_title||"")+"\\"></td></tr><tr><th>Texte conseil</th><td><textarea class=\\"large-text\\" name=\\""+p+"[advice_text]\\">"+(v.advice_text||"")+"</textarea></td></tr><tr><th>Lien secondaire</th><td><input class=\\"regular-text\\" name=\\""+p+"[help_text]\\" value=\\""+(v.help_text||"")+"\\"><input type=\\"url\\" class=\\"regular-text\\" name=\\""+p+"[help_url]\\" value=\\""+(v.help_url||"")+"\\"></td></tr></table>";root.insertAdjacentHTML("beforeend",html)});f.insertBefore(root,f.querySelector("input[type=submit]").closest("p")||f.lastElementChild);});</script>';
}
add_action( 'admin_footer', 'vectone_category_secondary_admin_fields', 25 );

function vectone_admin_guide_panel() {
    if ( ! is_admin() || ( $_GET['page'] ?? '' ) !== 'vectone-site-content' ) return;
    $rows = array(
        array( 'Accueil — contenus custom', 'Vectone Mobile → Contenus du site', 'Topbar, Hero image, deuxième visuel, guide, FAQ' ),
        array( 'Produits / offres / prix', 'WooCommerce → Produits', 'Produits, prix, images, catégories' ),
        array( 'Articles / actualités', 'Articles', 'Titres, images, extraits, dates, liens' ),
        array( 'Navigation', 'Apparence → Menus', 'Menus desktop, mobile et dropdowns' ),
        array( 'Pages Elementor', 'Pages → Modifier avec Elementor', 'Widgets, avis et actualités Elementor' ),
        array( 'Formulaire', 'Contact → Formulaires de contact', 'Champs et configuration CF7' ),
        array( 'Pages légales', 'Pages', 'Mentions légales et confidentialité' ),
    );
    $links = array( 'Produits' => admin_url( 'edit.php?post_type=product' ), 'Articles' => admin_url( 'edit.php' ), 'Menus' => admin_url( 'nav-menus.php' ), 'Pages' => admin_url( 'edit.php?post_type=page' ), 'Médiathèque' => admin_url( 'upload.php' ) );
    $html = '<h2>Guide d’administration</h2><p>Chaque contenu possède une seule source d’édition.</p><table class="widefat striped"><thead><tr><th>Section</th><th>Où modifier</th><th>Contenu</th></tr></thead><tbody>';
    foreach ( $rows as $row ) $html .= '<tr><td>' . esc_html( $row[0] ) . '</td><td>' . esc_html( $row[1] ) . '</td><td>' . esc_html( $row[2] ) . '</td></tr>';
    $html .= '</tbody></table><p class="vm-admin-guide-links">';
    foreach ( $links as $label => $url ) $html .= '<a class="button" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a> ';
    $html .= '</p>';
    echo '<script>document.addEventListener("DOMContentLoaded",function(){var f=document.querySelector("form[action=\\"options.php\\"]");if(!f)return;var h=document.createElement("div");h.innerHTML=' . wp_json_encode( $html ) . ';f.insertBefore(h,f.querySelector("input[type=submit]").closest("p")||f.lastElementChild);});</script>';
}
add_action( 'admin_footer', 'vectone_admin_guide_panel', 35 );

function vectone_avec_admin_fields() {
    if ( ! is_admin() || ( $_GET['page'] ?? '' ) !== 'vectone-site-content' ) return;
    $c = vectone_site_content();
    $benefits = array_pad( (array) ( $c['categories']['avec']['benefits'] ?? array() ), 3, array( 'title' => '', 'description' => '', 'order' => 0, 'active' => 0 ) );
    $guide = array_pad( (array) ( $c['categories']['avec']['guide'] ?? array() ), 4, array( 'title' => '', 'description' => '', 'text' => '', 'order' => 0, 'active' => 0 ) );
    echo '<script>document.addEventListener("DOMContentLoaded",function(){var f=document.querySelector("form[action=\\"options.php\\"]");if(!f)return;var b=' . wp_json_encode( $benefits ) . ',g=' . wp_json_encode( $guide ) . ',h=document.createElement("div");function rows(path,a){return a.map(function(x,i){var p="vectone_site_content[categories][avec]["+path+"]["+i+"]";return "<tr><td><input class=\\"regular-text\\" name=\\""+p+"[title]\\" value=\\""+(x.title||"")+"\\"></td><td><textarea name=\\""+p+"[description]\\">"+(x.description||x.text||"")+"</textarea></td><td><input type=\\"number\\" name=\\""+p+"[order]\\" value=\\""+(x.order||0)+"\\"></td><td><input type=\\"checkbox\\" name=\\""+p+"[active]\\" value=\\"1\\" "+(x.active?"checked":"")+"><\\/td><\\/tr>"}).join("")}h.innerHTML="<h2>Avec engagement — bénéfices</h2><table class=\\"widefat striped\\"><tr><th>Titre</th><th>Description</th><th>Ordre</th><th>Actif</th></tr>"+rows("benefits",b)+"<\\/table><h2>Avec engagement — guide</h2><table class=\\"widefat striped\\"><tr><th>Titre</th><th>Description</th><th>Ordre</th><th>Actif</th></tr>"+rows("guide",g)+"<\\/table>";f.insertBefore(h,f.querySelector("input[type=submit]").closest("p")||f.lastElementChild);});</script>';
}
add_action( 'admin_footer', 'vectone_avec_admin_fields', 30 );

/* Keep the existing option/form, but make the editor navigable section by section. */
function vectone_site_content_admin_ui() {
    if ( ! is_admin() || ( $_GET['page'] ?? '' ) !== 'vectone-site-content' ) return;
    ?>
    <style>
        body.toplevel_page_vectone-site-content .wrap > form { max-width: 1480px; }
        body.toplevel_page_vectone-site-content .wrap > form > h2 { display:none; }
        .vm-admin-nav { margin:18px 0; position:sticky; top:32px; z-index:10; }
        .vm-admin-nav button { cursor:pointer; }
        .vm-admin-nav button.is-active { background:#fff; border-bottom-color:#fff; color:#123d35; }
        .vm-admin-panel { background:#fff; border:1px solid #d8e2df; border-radius:8px; padding:18px; margin:0 0 18px; box-shadow:0 2px 8px rgba(18,61,53,.06); }
        .vm-admin-panel > h2 { display:block !important; margin:0 0 12px; color:#123d35; font-size:20px; }
        .vm-admin-panel table { margin:0; }
        .vm-admin-panel.is-hidden { display:none; }
        .vm-admin-save { position:sticky; bottom:16px; z-index:11; padding:10px 0; }
        .vm-admin-save .button-primary { background:#f2384f; border-color:#f2384f; }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var form=document.querySelector('form[action="options.php"]');
        if(!form) return;
        var headings=[].slice.call(form.querySelectorAll(':scope > h2'));
        if(!headings.length) return;
        var nav=document.createElement('div'); nav.className='vm-admin-nav nav-tab-wrapper';
        var panels=[];
        headings.forEach(function(h,i){
            var panel=document.createElement('section'); panel.className='vm-admin-panel'; panel.dataset.panel=i;
            form.insertBefore(panel,h);
            var node=h;
            while(node && node!==form){var next=node.nextSibling; panel.appendChild(node); node=next; if(next && next.nodeType===1 && next.matches('h2')) break;}
            panels.push(panel);
            var b=document.createElement('button'); b.type='button'; b.className='nav-tab'; b.textContent=h.textContent; b.dataset.panel=i;
            b.addEventListener('click',function(){panels.forEach(function(p){p.classList.toggle('is-hidden',p.dataset.panel!==b.dataset.panel)});[].slice.call(nav.children).forEach(function(x){x.classList.toggle('is-active',x===b)}); window.scrollTo({top:nav.offsetTop-20,behavior:'smooth'});}); nav.appendChild(b);
        });
        form.insertBefore(nav,form.firstChild);
        panels.forEach(function(p,i){if(i>0)p.classList.add('is-hidden');});
        if(nav.firstChild)nav.firstChild.classList.add('is-active');
        var submit=form.querySelector('input[type="submit"]');
        if(submit){var save=submit.closest('p')||submit.parentElement; if(save)save.classList.add('vm-admin-save');}
    });
    </script>
    <?php
}
add_action( 'admin_footer', 'vectone_site_content_admin_ui', 40 );

function vectone_awin_admin_menu() {
    add_submenu_page( 'vectone-site-content', 'Partenaires Awin', 'Partenaires Awin', 'manage_options', 'vectone-awin', 'vectone_awin_admin_page' );
}
add_action( 'admin_menu', 'vectone_awin_admin_menu' );

function vectone_awin_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    $campaigns = function_exists( 'vectone_awin_campaigns' ) ? vectone_awin_campaigns() : array();
    if ( isset( $_POST['vectone_awin_save'] ) && check_admin_referer( 'vectone_awin_save' ) ) {
        $input = isset( $_POST['campaigns'] ) && is_array( $_POST['campaigns'] ) ? $_POST['campaigns'] : array();
        $clean = array();
        foreach ( $campaigns as $i => $fallback ) {
            $row = is_array( $input[ $i ] ?? null ) ? $input[ $i ] : array();
            $clean[ $i ] = array(
                'name' => sanitize_text_field( $row['name'] ?? $fallback['name'] ),
                'text' => sanitize_textarea_field( $row['text'] ?? $fallback['text'] ),
                'url' => esc_url_raw( $row['url'] ?? $fallback['url'] ),
                'position' => sanitize_key( $row['position'] ?? $fallback['position'] ),
                'variant' => in_array( $row['variant'] ?? '', array( 'primary', 'secondary' ), true ) ? $row['variant'] : $fallback['variant'],
            );
        }
        update_option( 'vectone_awin_campaigns', $clean, false );
        $campaigns = $clean;
        echo '<div class="notice notice-success is-dismissible"><p>Campagnes Awin enregistrées.</p></div>';
    }
    ?>
    <div class="wrap"><h1>Partenaires Awin</h1><p>Les valeurs actuelles sont conservées par défaut. Les URLs existantes ne sont pas modifiées tant que vous n’enregistrez pas une nouvelle valeur.</p><form method="post"><?php wp_nonce_field( 'vectone_awin_save' ); ?><table class="widefat striped"><thead><tr><th>Partenaire</th><th>Texte</th><th>URL affiliée</th><th>Position</th><th>Variante</th></tr></thead><tbody>
    <?php foreach ( $campaigns as $i => $campaign ) : ?><tr><td><input class="regular-text" name="campaigns[<?php echo $i; ?>][name]" value="<?php echo esc_attr( $campaign['name'] ?? '' ); ?>"></td><td><textarea name="campaigns[<?php echo $i; ?>][text]" rows="2"><?php echo esc_textarea( $campaign['text'] ?? '' ); ?></textarea></td><td><input class="large-text" type="url" name="campaigns[<?php echo $i; ?>][url]" value="<?php echo esc_attr( $campaign['url'] ?? '' ); ?>"></td><td><input class="regular-text" name="campaigns[<?php echo $i; ?>][position]" value="<?php echo esc_attr( $campaign['position'] ?? '' ); ?>"></td><td><select name="campaigns[<?php echo $i; ?>][variant]"><option value="primary" <?php selected( $campaign['variant'] ?? '', 'primary' ); ?>>Principale</option><option value="secondary" <?php selected( $campaign['variant'] ?? '', 'secondary' ); ?>>Secondaire</option></select></td></tr><?php endforeach; ?></tbody></table><p><button class="button button-primary" type="submit" name="vectone_awin_save" value="1">Enregistrer les campagnes</button></p></form></div>
    <?php
}
