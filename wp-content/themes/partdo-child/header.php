
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( "charset" ); ?>">
	<meta name="linkavista" content="link-757-4556">

	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="verification-code" content="ge9cta4tve6">
	<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7245168248687752"
     crossorigin="anonymous"></script>

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

	<div class="page-content" id="page">

		<?php if (get_theme_mod( 'partdo_preloader' )) { ?>
			<div class="site-loading">
				<div class="preloading">
					<svg class="circular" viewBox="25 25 50 50">
						<circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
					</svg>
				</div>
			</div>
		<?php } ?>

		<?php partdo_do_action( 'partdo_before_main_header'); ?>

		<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) { ?>
			<?php
			/**
			* Hook: partdo_main_header
			*
			* @hooked partdo_canvas_menu - 10
			* @hooked partdo_main_header_function - 20
			*/
		do_action( 'partdo_main_header' );
		
			?>
		<?php } ?>

		<?php partdo_do_action( 'partdo_after_main_header'); ?>
		
		<div class="site-primary" id="main">
			<div class="site-content" id="content">

		<!-- Ajout du script jQuery -->
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			// Code existant
			var url_get = window.location.href;
			var url_origin = window.location.origin + '/';
			if(url_get !== url_origin){
				$('.dropdown-menu.collapse.show').removeClass('show');
			}

			// Modification du lien mailto
			$('a[href="mailto:contact@vectonemobile.fr"]').attr('href', 'https://vectonemobile.fr/contact/');
		});
		</script>