<?php



/*************************************************

## Woocommerce 

*************************************************/



function partdo_product_image(){

	if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) {

		$att=get_post_thumbnail_id();

		$image_src = wp_get_attachment_image_src( $att, 'full' );

		$image_src = $image_src[0];



		$size = get_theme_mod( 'partdo_product_image_size', array( 'width' => '', 'height' => '') );



		if($size['width'] && $size['height']){

			$image = partdo_resize( $image_src, $size['width'], $size['height'], true, true, true );  

		} else {

			$image = $image_src;

		}

		

		return esc_url($image);

	} else {

		return wc_placeholder_img_src('');

	}

}



if( ! function_exists('partdo_product_second_image') ){

	function partdo_product_second_image(){

		global $product;

		

		$product_image_ids = $product->get_gallery_image_ids();

		

		if($product_image_ids && get_theme_mod('partdo_product_box_gallery') == 1){

			

			echo '<img src="'.partdo_product_image().'"';

			echo ' data-hover-slides="';

			

			$total_count = count($product_image_ids);

			$count = 1;

			foreach( $product_image_ids as $product_image_id ){

				if($count == $total_count){

					$size = get_theme_mod( 'partdo_product_image_size', array( 'width' => '', 'height' => '') );

	

					if($size['width'] && $size['height']){

						$image = partdo_resize( wp_get_attachment_url( $product_image_id ), $size['width'], $size['height'], true, true, true );  

					} else {

						$image = wp_get_attachment_url( $product_image_id ); //comma removed for the latest item

					}

			

					echo partdo_sanitize_data($image);

	

				} else {

					$size = get_theme_mod( 'partdo_product_image_size', array( 'width' => '', 'height' => '') );

	

					if($size['width'] && $size['height']){

						$image = ''.partdo_resize( wp_get_attachment_url( $product_image_id ), $size['width'], $size['height'], true, true, true ).',';

					} else {

						$image = ''.wp_get_attachment_url( $product_image_id ).','; //comma added for each item

					}

			

					echo partdo_sanitize_data($image);

				}

				$count++;

			}

			

			echo '"';

			echo ' data-options=\'{"touch": "end", "preloadImages": true }\' alt="'.the_title_attribute( 'echo=0' ).'">';

	

		} else {

			echo '<img src="'.partdo_product_image().'" alt="'.the_title_attribute( 'echo=0' ).'">';

		}

	

	}

}

if( ! function_exists('partdo_sale_percentage') ){

	function partdo_sale_percentage(){

		global $product;

	

		$output = '';

		

		if(get_theme_mod('partdo_product_badge_tab', 0) == 1){

			

			$product = wc_get_product(get_the_ID());

			$badgetext = $product->get_meta('_klb_product_badge_text');

			$badgetype = $product->get_meta('_klb_product_badge_type');

			$badgebg = $product->get_meta('_klb_product_badge_bg_color');

			$badgecolor = $product->get_meta('_klb_product_badge_text_color');

			$percentagecheck = $product->get_meta('_klb_product_percentage_check');

			$percentagetype = $product->get_meta('_klb_product_percentage_type');

			$percentagebg = $product->get_meta('_klb_product_percentage_bg_color');

			$percentagecolor = $product->get_meta('_klb_product_percentage_text_color');

	

			$badgecss = '';

			if($badgebg || $badgecolor){

				$badgecss .= 'style="';

				if($badgebg){

					$badgecss .= 'background-color: '.esc_attr($badgebg).';';

				}

				if($badgecolor){

					$badgecss .= 'color: '.esc_attr($badgecolor).';';

				}

				$badgecss .= '"';

			}

			

			$percentagecss = '';

			if($percentagebg || $percentagecolor){

				$percentagecss .= 'style="';

				if($percentagebg){

					$percentagecss .= 'background-color: '.esc_attr($percentagebg).';';

				}

				if($percentagecolor){

					$percentagecss .= 'color: '.esc_attr($percentagecolor).';';

				}

				$percentagecss .= '"';

			}

			

			if ( $product->is_on_sale() || $badgetext ){

				$output .= '<div class="product-badges">';

				if ( !$percentagecheck && $product->is_on_sale() && $product->is_type( 'variable' ) ) {

					$percentage = ceil(100 - ($product->get_variation_sale_price() / $product->get_variation_regular_price( 'min' )) * 100);

					$output .= '<span class="badge '.esc_attr($percentagetype).' sale" '.$percentagecss.'>'.$percentage.'%</span>';

				} elseif( !$percentagecheck && $product->is_on_sale() && $product->get_regular_price()  && !$product->is_type( 'grouped' )) {

					$percentage = ceil(100 - ($product->get_sale_price() / $product->get_regular_price()) * 100);

					$output .= '<span class="badge '.esc_attr($percentagetype).' sale" '.$percentagecss.'>'.$percentage.'%</span>';

				}

				

				if($badgetext){

					$output .= '<span class="badge '.esc_attr($badgetype).'" '.$badgecss.'>'.esc_html($badgetext).'</span>';

				}

				

				$output .= '</div>';

			}

		}

	

		return $output;

	

	}

}



function partdo_vendor_name(){

	if (function_exists('get_mvx_vendor_settings')) {

		global $post;

		$vendor = get_mvx_product_vendors($post->ID);

		if (isset($vendor->page_title)) {

			$store_name = $vendor->page_title;

			

			return '<div class="product-seller"><span>'.esc_html__('Seller:', 'partdo').'</span><a href="'.esc_url($vendor->permalink).'"> '.esc_html($store_name).'</a></div>';

		}

	}elseif(class_exists('WeDevs_Dokan')){

		// Get the author ID (the vendor ID)

		$vendor_id = get_post_field( 'post_author', get_the_id() );



		$store_info  = dokan_get_store_info( $vendor_id ); // Get the store data

		$store_name  = $store_info['store_name'];          // Get the store name

		$store_url   = dokan_get_store_url( $vendor_id );  // Get the store URL



		if (isset($store_name)) {

			return '<div class="product-seller"><span>'.esc_html__('Seller:', 'partdo').'</span><a href="'.esc_url($store_url).'"> '.esc_html($store_name).'</a></div>';

		}

	}

}



if ( class_exists( 'woocommerce' ) ) {

add_theme_support( 'woocommerce' );

add_image_size('partdo-woo-product', 450, 450, true);



// Remove woocommerce defauly styles

add_filter( 'woocommerce_enqueue_styles', '__return_false' );



if( ! function_exists('partdo_override_page_title') ){

	// hide default shop title

	add_filter('woocommerce_show_page_title', 'partdo_override_page_title');

	function partdo_override_page_title() {

		return false;

	}

}





remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 ); /*remove result count above products*/

remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 ); //remove rating

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 ); //remove rating

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);

remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);

remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title',10);



add_action( 'woocommerce_before_shop_loop_item', 'partdo_shop_box', 10);

remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );

remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 ); /*remove breadcrumb*/







remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products',20);

remove_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products',10);

add_action( 'woocommerce_after_single_product_summary', 'partdo_related_products', 20);

function partdo_related_products(){

	$related_column = get_theme_mod('partdo_shop_related_post_column') ? get_theme_mod('partdo_shop_related_post_column') : '4';

    woocommerce_related_products( array('posts_per_page' => $related_column, 'columns' => $related_column));

}



remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');

add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display', 20);

add_filter( 'woocommerce_cross_sells_columns', 'partdo_change_cross_sells_columns' );

function partdo_change_cross_sells_columns( $columns ) {

	return 4;

}



/*************************************************

## Single Gallery Columns

*************************************************/



add_filter ( 'woocommerce_product_thumbnails_columns', 'partdo_product_thumbnails_columns', 10, 1 );

function partdo_product_thumbnails_columns( $columns ) {

    return get_theme_mod('partdo_shop_single_gallery_columns', 7);

}



/*************************************************

## Wishlist Shortcode

*************************************************/

function partdo_wishlist_shortcode(){

	$output = '';

	

	$wishlist = get_theme_mod( 'partdo_wishlist_button', '0' );

	

	if($wishlist == '1' && function_exists('run_tinv_wishlist')){

	$output .= do_shortcode('[ti_wishlists_addtowishlist]');

	}



	return $output;

}



/*************************************************

## Compare Shortcode

*************************************************/

function partdo_compare_shortcode(){

	$output = '';

	

	$compare = get_theme_mod( 'partdo_compare_button', '0' );

	

	if($compare == '1' && function_exists('woosc_init')){

	$output .= do_shortcode('[woosc type="link"]');

	}



	return $output;

}



/*************************************************

## Re-order WooCommerce Single Product Summary

*************************************************/

if(class_exists('Partdo_Elementor_Addons')){

	$reorder_single = get_theme_mod( 'partdo_shop_single_reorder', 

		array( 

			'woocommerce_template_single_title', 

			'woocommerce_template_single_rating', 

			'woocommerce_template_single_price', 

			'woocommerce_template_single_excerpt', 

			'woocommerce_template_single_add_to_cart', 

			'partdo_wishlist_shortcode_output', 

			'partdo_single_product_assistant', 

			'partdo_single_product_iconboxes', 

			'woocommerce_template_single_meta', 

			'partdo_social_share', 

			

		) 

	);

	

	if($reorder_single){

		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );

		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );

		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );

		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

		remove_action( 'woocommerce_single_product_summary', 'partdo_wishlist_shortcode_output', 32);

		remove_action( 'woocommerce_single_product_summary', 'partdo_single_product_assistant', 35);

		remove_action( 'woocommerce_single_product_summary', 'partdo_single_product_iconboxes', 38);

		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

		remove_action( 'woocommerce_single_product_summary', 'partdo_social_share', 70);

		

		$count = 10;

		

		foreach ( $reorder_single as $single_part ) {

			

			add_action( 'woocommerce_single_product_summary', $single_part, $count );

			

			$count+=10;

		}

	}

}



function partdo_product_countdown(){

	global $product;

	global $post;

	global $woocommerce;

	$id = get_the_ID();

	$output = '';

	

	if( $product->is_type('variable') ) {

		$variation_ids = $product->get_visible_children();



		if($variation_ids[0]){

			$variation = wc_get_product( $variation_ids[0] );

	

			$sale_price_dates_to = ( $date = get_post_meta( $variation_ids[0], '_sale_price_dates_to', true ) ) ? date_i18n( 'Y/m/d', $date ) : '';

		} else {

			$sale_price_dates_to = '';

		}

	} else {

		$sale_price_dates_to = ( $date = get_post_meta( $id, '_sale_price_dates_to', true ) ) ? date_i18n( 'Y/m/d', $date ) : '';

	}

	

	if($sale_price_dates_to){

		$output .= '<div class="product-countdown">';

		$output .= '<div class="countdown" data-date="'.esc_attr($sale_price_dates_to).'" data-text="'.esc_attr__('Expired','partdo').'">';

		$output .= '<div class="count-item">';

		$output .= '<div class="days">00</div>';

		$output .= '<div class="count-label">'.esc_html('d','partdo').'</div>';

		$output .= '</div><!-- count-item -->';

		$output .= '<span>:</span>';

		$output .= '<div class="count-item">';

		$output .= '<div class="hours">00</div>';

		$output .= '<div class="count-label">'.esc_html('h','partdo').'</div>';

		$output .= '</div><!-- count-item -->';

		$output .= '<span>:</span>';

		$output .= '<div class="count-item">';

		$output .= '<div class="minutes">00</div>';

		$output .= '<div class="count-label">'.esc_html('m','partdo').'</div>';

		$output .= '</div><!-- count-item -->';

		$output .= '<span>:</span>';

		$output .= '<div class="count-item">';

		$output .= '<div class="second">00</div>';

		$output .= '<div class="count-label">'.esc_html('s','partdo').'</div>';

		$output .= '</div><!-- count-item -->';

		$output .= '</div><!-- countdown -->';

		$output .= '</div><!-- product-countdown -->';

	}

	

	return $output;

}



/*----------------------------

  Product Type 1

 ----------------------------*/

function partdo_product_type1(){

	global $product;

	global $post;

	global $woocommerce;

	

	

	$output = '';

	

	$id = get_the_ID();

	$allproduct = wc_get_product( get_the_ID() );



	$cart_url = wc_get_cart_url();

	$price = $allproduct->get_price_html();

	$weight = $product->get_weight();

	$stock_status = $product->get_stock_status();

	$stock_text = $product->get_availability();

	$short_desc = $product->get_short_description();

	$rating = wc_get_rating_html($product->get_average_rating());

	$ratingcount = $product->get_review_count();

	$wishlist = get_theme_mod( 'partdo_wishlist_button', '0' );

	$compare = get_theme_mod( 'partdo_compare_button', '0' );

	$quickview = get_theme_mod( 'partdo_quick_view_button', '0' );



	$managestock = $product->managing_stock();

	$stock_quantity = $product->get_stock_quantity();

	$stock_format = esc_html__('Only %s left in stock','partdo');

	$stock_poor = '';

	if($managestock && $stock_quantity < 10) {

		$stock_poor .= '<div class="product-message color-danger">'.sprintf($stock_format, $stock_quantity).'</div>';

	}

	

	$total_sales = $product->get_total_sales();

	$total_stock = $total_sales + $stock_quantity;

	

	if($managestock && $stock_quantity > 0) {

	$progress_percentage = floor($total_sales / (($total_sales + $stock_quantity) / 100)); // yuvarlama

	}

	

	$gallery = get_theme_mod('partdo_product_box_gallery') == 1 ? 'product-thumbnail' : '';



	$postview  = isset( $_POST['shop_view'] ) ? $_POST['shop_view'] : '';



	if(partdo_shop_view() == 'list_view' || $postview == 'list_view') {

		$output .= '<div class="product">'; 

		$output .= '<div class="product-wrapper"> ';

		$output .= '<div class="product-content">';				

		$output .= '<div class="thumbnail-wrapper entry-media">';

		$output .= partdo_sale_percentage();

		$output .= '<a class="'.esc_attr($gallery).'" href="'.get_permalink().'">';

			ob_start();

			$output .= partdo_product_second_image();

			$output .= ob_get_clean();

		$output .= '</a>';

		$output .= '<div class="product-buttons"> ';

							

		$output .= partdo_wishlist_shortcode();

		

		$output .= partdo_compare_shortcode();

							

		if($quickview == '1'){

		$output .= '<a class="detail-bnt quickview" data-product_id="'.$product->get_id().'"><i class="klbth-icon-eye-empty"></i></a>';

		}	

		$output .= '</div>';

		$output .= '</div>';

		$output .= '<div class="content-wrapper">';		

		$output .= '<h3 class="product-title"> <a href="'.get_permalink().'">'.get_the_title().'</a></h3>';

		

		

		if(partdo_vendor_name()){

			$output .= '<div class="content-switcher">';

			$output .= '<div class="switcher-wrapper">';			  

			$output .= partdo_vendor_name();

			if($ratingcount){	

				$output .= '<div class="product-rating">';

				  $output .= $rating;

				  $output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';

				$output .= '</div>';

			}				  

			$output .= '</div>';

			$output .= '</div>';

		} else {

			if($ratingcount){	

				$output .= '<div class="product-rating">';

				$output .= $rating;

				$output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';

				$output .= '</div>';

			}

		}

			

		$output .= '<span class="price kk">'; 

		$output .= $price;

		$output .= '</span>';

		

		if($stock_status == 'instock' && $stock_text['availability']){

			$output .= '<div class="product-stock in-stock"> <i class="klbth-icon-ecommerce-package-ready"></i><span>'.$stock_text['availability'].'</span></div>';

		} elseif($stock_text['availability']) {

			$output .= '<div class="product-stock outof-stock"> <i class="klbth-icon-ecommerce-package-ready"></i><span>'.$stock_text['availability'].'</span></div>';

		}

	

		$output .= '</div>';

		$output .= '</div>';

		

		$output .= '<div class="product-footer ds">';

		$output .= '<div class="product-footer-inner"> ';

		$output .= '<div class="product-footer-details">'; 

		$output .= '<p>'.partdo_limit_words(get_the_excerpt(), '20').'</p>';

		$output .= '</div>';

			ob_start();

			woocommerce_template_loop_add_to_cart();

			$output .= ob_get_clean();

		$output .= '</div>';

		$output .= '</div>';																												

		

		$output .= '</div>';

		$output .= '<div class="product-content-fade"></div>';

		$output .= '</div>';



	} else {

		$output .= '<div class="product product-type-1">';

		$output .= ' <div class="product-wrapper">'; 

			

		$output .= '<div class="product-content">';		

		$output .= '<div class="thumbnail-wrapper entry-media"> ';

		$output .= partdo_sale_percentage();

		$output .= '<a class="'.esc_attr($gallery).'" href="'.get_permalink().'">';

			ob_start();

			$output .= partdo_product_second_image();

			$output .= ob_get_clean();

		$output .= '</a>';

		$output .= '<div class="product-buttons"> ';

			

		$output .= partdo_wishlist_shortcode();

					

		$output .= partdo_compare_shortcode();

					

		if($quickview == '1'){

			$output .= '<a class="detail-bnt quickview" data-product_id="'.$product->get_id().'"><i class="klbth-icon-eye-empty"></i></a>';

		}	

					

		$output .= ' </div>';

		$output .= ' </div>';

		

		$output .= '<div class="content-wrapper">';

		$output .= '<h3 class="product-title"> <a href="'.get_permalink().'">'.get_the_title().'</a></h3>';

		if(partdo_vendor_name()){

			$output .= '<div class="content-switcher">';

			$output .= '<div class="switcher-wrapper">';			  

			$output .= partdo_vendor_name();

			if($ratingcount){	

				$output .= '<div class="product-rating">';

				  $output .= $rating;

				  $output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';

				$output .= '</div>';

			}				  

			$output .= '</div>';

			$output .= '</div>';

		} else {

			if($ratingcount){	

				$output .= '<div class="product-rating">';

				$output .= $rating;

				$output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';

				$output .= '</div>';

			}

		}

		$output .= '<span class="price ouy">'; 

		$output .= $price;

		$output .= '</span>';

		

		if($stock_status == 'instock' && $stock_text['availability']){

			$output .= '<div class="product-stock in-stock"> <i class="klbth-icon-ecommerce-package-ready"></i><span>'.$stock_text['availability'].'</span></div>';

		} elseif($stock_text['availability']) {

			$output .= '<div class="product-stock outof-stock"> <i class="klbth-icon-ecommerce-package-ready"></i><span>'.$stock_text['availability'].'</span></div>';

		}

		

		$output .= '</div>';

		$output .= '</div>';

		



		$output .= '<div class="product-footer dsf">';	

		$output .= '<div class="product-footer-inner"> ';

		if($short_desc){

		$output .= '<div class="product-footer-details"> ';

		$output .= $short_desc;

		$output .= '</div>';

		}

		ob_start();

		woocommerce_template_loop_add_to_cart();

		$output .= ob_get_clean();

		$output .= '</div>';

		$output .= '</div>';

	

		

		$output .= '</div>';	

		$output .= '<div class="product-content-fade"></div>';

		$output .= '</div>';



	}

	

	return $output;

}





/*----------------------------

  Product Type 2

 ----------------------------*/

function partdo_product_type2(){

	global $product;

	global $post;

	global $woocommerce;

	

	$output = '';

	

	$id = get_the_ID();

	$allproduct = wc_get_product( get_the_ID() );



	$cart_url = wc_get_cart_url();

	$price = $allproduct->get_price_html();

	$weight = $product->get_weight();

	$stock_status = $product->get_stock_status();

	$stock_text = $product->get_availability();

	$short_desc = $product->get_short_description();

	$rating = wc_get_rating_html($product->get_average_rating());

	$ratingcount = $product->get_review_count();

	$wishlist = get_theme_mod( 'partdo_wishlist_button', '0' );

	$compare = get_theme_mod( 'partdo_compare_button', '0' );

	$quickview = get_theme_mod( 'partdo_quick_view_button', '0' );



	$managestock = $product->managing_stock();

	$stock_quantity = $product->get_stock_quantity();

	$stock_format = esc_html__('Only %s left in stock','partdo');

	$stock_poor = '';

	if($managestock && $stock_quantity < 10) {

		$stock_poor .= '<div class="product-message color-danger">'.sprintf($stock_format, $stock_quantity).'</div>';

	}

	

	$total_sales = $product->get_total_sales();

	$total_stock = $total_sales + $stock_quantity;

	

	if($managestock && $stock_quantity > 0) {

	$progress_percentage = floor($total_sales / (($total_sales + $stock_quantity) / 100)); // yuvarlama

	}

	

	$gallery = get_theme_mod('partdo_product_box_gallery') == 1 ? 'product-thumbnail' : '';

	

	$postview  = isset( $_POST['shop_view'] ) ? $_POST['shop_view'] : '';

	

	if(partdo_shop_view() == 'list_view') {

		$output .= '<div class="product-wrapper"> ';

		$output .= '<div class="product-content">';				

		$output .= '<div class="thumbnail-wrapper entry-media">';

		$output .= partdo_sale_percentage();

		$output .= '<a class="'.esc_attr($gallery).'" href="'.get_permalink().'">';

			ob_start();

			$output .= partdo_product_second_image();

			$output .= ob_get_clean();

		$output .= '</a>';

		$output .= '<div class="product-buttons"> ';

							

		$output .= partdo_wishlist_shortcode();

		

		$output .= partdo_compare_shortcode();

							

		if($quickview == '1'){

		$output .= '<a class="detail-bnt quickview" data-product_id="'.$product->get_id().'"><i class="klbth-icon-eye-empty"></i></a>';

		}	

		$output .= '</div>';

		$output .= '</div>';

		$output .= '<div class="content-wrapper">';		

		$output .= '<h3 class="product-title"> <a href="'.get_permalink().'">'.get_the_title().'</a></h3>';

		

		if(partdo_vendor_name()){

			$output .= '<div class="content-switcher">';

			$output .= '<div class="switcher-wrapper">';			  

			$output .= partdo_vendor_name();

			if($ratingcount){	

				$output .= '<div class="product-rating">';

				  $output .= $rating;

				  $output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';

				$output .= '</div>';

			}				  

			$output .= '</div>';

			$output .= '</div>';

		} else {

			if($ratingcount){	

				$output .= '<div class="product-rating">';

				$output .= $rating;

				$output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';

				$output .= '</div>';

			}

		}

		

		$output .= '<div class="product-cart-form">';

		$output .= '<span class="price llo">'; 

		$output .= $price;

		$output .= '</span>';

			ob_start();

			woocommerce_template_loop_add_to_cart();

			$output .= ob_get_clean();;

		$output .= '</div>';

		if($stock_status == 'instock' && $stock_text['availability']){

			$output .= '<div class="product-stock in-stock"> <i class="klbth-icon-ecommerce-package-ready"></i><span>'.$stock_text['availability'].'</span></div>';

		} elseif($stock_text['availability']) {

			$output .= '<div class="product-stock outof-stock"> <i class="klbth-icon-ecommerce-package-ready"></i><span>'.$stock_text['availability'].'</span></div>';

		}

	

		$output .= '</div>';

		$output .= '</div>';																												

		

		$output .= '</div>';



	} else {

		$output .= ' <div class="product-wrapper product-type-2">'; 	

		$output .= '<div class="product-content">';		

		$output .= '<div class="thumbnail-wrapper entry-media"> ';

		$output .= partdo_sale_percentage();

		$output .= '<a class="'.esc_attr($gallery).'" href="'.get_permalink().'">';

			ob_start();

			$output .= partdo_product_second_image();

			$output .= ob_get_clean();

		$output .= '</a>';

		$output .= '<div class="product-buttons"> ';

			

		$output .= partdo_wishlist_shortcode();

					

		$output .= partdo_compare_shortcode();

					

		if($quickview == '1'){

			$output .= '<a class="detail-bnt quickview" data-product_id="'.$product->get_id().'"><i class="klbth-icon-eye-empty"></i></a>';

		}	

					

		$output .= ' </div>';

		$output .= ' </div>';

		

		$output .= '<div class="content-wrapper">';

		$output .= '<h3 class="product-title"> <a href="'.get_permalink().'">'.get_the_title().'</a></h3>';

		

		if(partdo_vendor_name()){

			$output .= '<div class="content-switcher">';

			$output .= '<div class="switcher-wrapper">';			  

			$output .= partdo_vendor_name();

			if($ratingcount){	

				$output .= '<div class="product-rating">';

				  $output .= $rating;

				  $output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';

				$output .= '</div>';

			}				  

			$output .= '</div>';

			$output .= '</div>';

		} else {

			if($ratingcount){	

				$output .= '<div class="product-rating">';

				$output .= $rating;

				$output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';

				$output .= '</div>';

			}

		}

		

		$output .= '<div class="product-cart-form">';

		$output .= '<span class="price ljh">'; 

		$output .= $price;

		$output .= '</span>';

			ob_start();

			woocommerce_template_loop_add_to_cart();

			$output .= ob_get_clean();;

		$output .= '</div>';

		if($stock_status == 'instock' && $stock_text['availability']){

			$output .= '<div class="product-stock in-stock"> <i class="klbth-icon-ecommerce-package-ready"></i><span>'.$stock_text['availability'].'</span></div>';

		} elseif($stock_text['availability']) {

			$output .= '<div class="product-stock outof-stock"> <i class="klbth-icon-ecommerce-package-ready"></i><span>'.$stock_text['availability'].'</span></div>';

		}

		

		$output .= '</div>';

		$output .= '</div>';

		$output .= '</div>';	



	}



	

	return $output;

}



/*----------------------------------

  Product Type 3 with progress bar

 -----------------------------------*/

function partdo_product_type3(){

	global $product;

	global $post;

	global $woocommerce;

	

	$output = '';

	

	$id = get_the_ID();

	$allproduct = wc_get_product( get_the_ID() );



	$cart_url = wc_get_cart_url();

	$price = $allproduct->get_price_html();

	$weight = $product->get_weight();

	$stock_status = $product->get_stock_status();

	$stock_text = $product->get_availability();

	$short_desc = $product->get_short_description();

	$rating = wc_get_rating_html($product->get_average_rating());

	$ratingcount = $product->get_review_count();

	$wishlist = get_theme_mod( 'partdo_wishlist_button', '0' );

	$compare = get_theme_mod( 'partdo_compare_button', '0' );

	$quickview = get_theme_mod( 'partdo_quick_view_button', '0' );



	$managestock = $product->managing_stock();

	$stock_quantity = $product->get_stock_quantity();

	$stock_format = esc_html__('Only %s left in stock','partdo');

	$stock_poor = '';

	if($managestock && $stock_quantity < 10) {

		$stock_poor .= '<div class="product-message color-danger">'.sprintf($stock_format, $stock_quantity).'</div>';

	}

	

	$total_sales = $product->get_total_sales();

	$total_stock = $total_sales + $stock_quantity;

	

	if($managestock && $stock_quantity > 0) {

	$progress_percentage = floor($total_sales / (($total_sales + $stock_quantity) / 100)); // yuvarlama

	}



	$gallery = get_theme_mod('partdo_product_box_gallery') == 1 ? 'product-thumbnail' : '';

	

	$postview  = isset( $_POST['shop_view'] ) ? $_POST['shop_view'] : '';



	if(partdo_shop_view() == 'list_view' || $postview == 'list_view') {

		$output .= '<div class="product">'; 

		$output .= '<div class="product-wrapper"> ';

		$output .= '<div class="product-content">';				

		$output .= '<div class="thumbnail-wrapper entry-media">';

		$output .= partdo_sale_percentage();

		$output .= '<a target="_blank" class="'.esc_attr($gallery).'" href="'.get_permalink().'">';

			ob_start();

			$output .= partdo_product_second_image();

			$output .= ob_get_clean();

		$output .= '</a>';

		$output .= '<div class="product-buttons"> ';

							

		$output .= partdo_wishlist_shortcode();

		

		$output .= partdo_compare_shortcode();

							

		if($quickview == '1'){

		$output .= '<a target="_blank" class="detail-bnt quickview" data-product_id="'.$product->get_id().'"><i class="klbth-icon-eye-empty"></i></a>';

		}	

		$output .= '</div>';

		$output .= '</div>';

		$output .= '<div class="content-wrapper">';		

		$output .= '<h3 class="product-title ss dsd"> <a href="'.get_permalink().'">'.get_the_title().'</a></h3>';

		

$attr=$product->get_attributes();


$attributes_to_display = array('pa_appels-et-sms', 'pa_duree-dengagement');

if ( !empty($attr) ) {
	
		 $output .= '<div class="product-attributes">';
		
		
		  foreach ( $attributes_to_display as $attribute_slug ) {
			  
			   if ( isset( $attr[ $attribute_slug ] ) ) {
				   
				   $attribute = $attr[ $attribute_slug ];
				    $name = wc_attribute_label( $attribute->get_name() );
					 $value = implode( ', ', wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) ) );
					  $output .= '<div class="attribute">';
					$output .= '<strong>' . esc_html( $name ) . ':</strong> ' . esc_html( $value );
					$output .= '</div>';
			   }
			  
		  }
	$output .= '</div>';
}



		

		if(partdo_vendor_name()){

			$output .= '<div class="content-switcher">';

			$output .= '<div class="switcher-wrapper">';			  

			$output .= partdo_vendor_name();

			if($ratingcount){	

				$output .= '<div class="product-rating">';

				  $output .= $rating;

				  $output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';

				$output .= '</div>';

			}				  

			$output .= '</div>';

			$output .= '</div>';

		} else {

			if($ratingcount){	

				$output .= '<div class="product-rating">';

				$output .= $rating;

				$output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';

				$output .= '</div>';

			}

		}

		

		

		

		if($managestock && $stock_quantity > 0) {

			$output .= '<div class="product-progress-wrapper">';

			$output .= '<div class="product-progress"> <span class="progress" style="width:'.esc_attr($progress_percentage).'%"> </span><span class="not-progress"></span></div>';

			$output .= '<div class="product-progress-count"> ';

			$output .= '<div class="available">'.esc_html__('Available:','partdo').'<strong>'.esc_html($product->get_stock_quantity()).'</strong>';

			$output .= '</div>';

			$output .= '<div class="sold">'.esc_html__('Sold:','partdo').' <strong>'.esc_html($product->get_total_sales()).'</strong>';

			$output .= '</div>';

			$output .= '</div>';

			$output .= '</div>';

		}

		

		$output .= '</div>';

		$output .= '</div>';
		
		
		

		$output .= '<div class="product-footer junoh">';
		$output .= '<span class="price junoh">'; 

		$output .= $price;

		$output .= '</span>';

		$output .= '<div class="product-footer-inner"> ';

		$output .= '<div class="product-footer-details">'; 

		$output .= '<p>'.partdo_limit_words(get_the_excerpt(), '20').'</p>';

		$output .= '</div>';

			ob_start();

			woocommerce_template_loop_add_to_cart();

			$output .= ob_get_clean();

		$output .= '</div>';

		$output .= '</div>';																												

		

		$output .= '</div>';

		$output .= '<div class="product-content-fade"></div>';

		$output .= '</div>';



	} else {

		$output .= '<div class="product product-type-3">';

		$output .= ' <div class="product-wrapper">'; 

			

		$output .= '<div class="product-content">';		

		$output .= '<div class="thumbnail-wrapper entry-media"> ';

		$output .= partdo_sale_percentage();

		$output .= '<a class="'.esc_attr($gallery).'" href="'.get_permalink().'">';

			ob_start();

			$output .= partdo_product_second_image();

			$output .= ob_get_clean();

		$output .= '</a>';

		$output .= '<div class="product-buttons"> ';

			

		$output .= partdo_wishlist_shortcode();

					

		$output .= partdo_compare_shortcode();

					

		if($quickview == '1'){

			$output .= '<a class="detail-bnt quickview" data-product_id="'.$product->get_id().'"><i class="klbth-icon-eye-empty"></i></a>';

		}	

					

		$output .= ' </div>';

		$output .= ' </div>';

		

		$output .= '<div class="content-wrapper">';

		$output .= '<h3 class="product-title"> <a href="'.get_permalink().'">'.get_the_title().'</a></h3>';

		

		if(partdo_vendor_name()){

			$output .= '<div class="content-switcher">';

			$output .= '<div class="switcher-wrapper">';			  

			$output .= partdo_vendor_name();

			if($ratingcount){	

				$output .= '<div class="product-rating">';

				  $output .= $rating;

				  $output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';

				$output .= '</div>';

			}				  

			$output .= '</div>';

			$output .= '</div>';

		} else {

			if($ratingcount){	

				$output .= '<div class="product-rating">';

				$output .= $rating;

				$output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';

				$output .= '</div>';

			}

		}

		

		$output .= '<span class="price dd">'; 

		$output .= $price;

		$output .= '</span>';

		

		if($managestock && $stock_quantity > 0) {

			$output .= '<div class="product-progress-wrapper">';

			$output .= '<div class="product-progress"> <span class="progress" style="width:'.esc_attr($progress_percentage).'%"> </span><span class="not-progress"></span></div>';

			$output .= '<div class="product-progress-count"> ';

			$output .= '<div class="available">'.esc_html__('Available:','partdo').'<strong>'.esc_html($product->get_stock_quantity()).'</strong>';

			$output .= '</div>';

			$output .= '<div class="sold">'.esc_html__('Sold:','partdo').' <strong>'.esc_html($product->get_total_sales()).'</strong>';

			$output .= '</div>';

			$output .= '</div>';

			$output .= '</div>';

		}

		

		$output .= '</div>';

		$output .= '</div>';

		

		if($short_desc){

			$output .= '<div class="product-footer ki">';	

			$output .= '<div class="product-footer-inner"> ';

			$output .= '<div class="product-footer-details"> ';

			$output .= $short_desc;

			$output .= '</div>';

			ob_start();

			woocommerce_template_loop_add_to_cart();

			$output .= ob_get_clean();

			$output .= '</div>';

			$output .= '</div>';

		}

		

		$output .= '</div>';	

		$output .= '<div class="product-content-fade"></div>';

		$output .= '</div>';



	}

	

	return $output;

}



/*----------------------------

  Product Type Header

 ----------------------------*/

function partdo_product_type_header(){

	global $product;

	global $post;

	global $woocommerce;

	

	$output = '';

	

	$id = get_the_ID();

	$allproduct = wc_get_product( get_the_ID() );



	$cart_url = wc_get_cart_url();

	$price = $allproduct->get_price_html();

	$weight = $product->get_weight();

	$stock_status = $product->get_stock_status();

	$stock_text = $product->get_availability();

	$short_desc = $product->get_short_description();

	$rating = wc_get_rating_html($product->get_average_rating());

	$ratingcount = $product->get_review_count();

	$wishlist = get_theme_mod( 'partdo_wishlist_button', '0' );

	$compare = get_theme_mod( 'partdo_compare_button', '0' );

	$quickview = get_theme_mod( 'partdo_quick_view_button', '0' );



	$managestock = $product->managing_stock();

	$stock_quantity = $product->get_stock_quantity();

	$stock_format = esc_html__('Only %s left in stock','partdo');

	$stock_poor = '';

	if($managestock && $stock_quantity < 10) {

		$stock_poor .= '<div class="product-message color-danger">'.sprintf($stock_format, $stock_quantity).'</div>';

	}



	$postview  = isset( $_POST['shop_view'] ) ? $_POST['shop_view'] : '';



	$output .= '<div class="product-wrapper">';

	$output .= '<div class="product-content">';

	$output .= '<div class="thumbnail-wrapper entry-media">';

	$output .= '<a href="'.get_permalink().'"><img src="'.partdo_product_image().'" alt="'.the_title_attribute( 'echo=0' ).'"></a>';

	$output .= '</div><!-- thumbnail-wrapper -->';

	$output .= '<div class="content-wrapper">';

	$output .= '<h3 class="product-title"> <a href="'.get_permalink().'">'.get_the_title().'</a></h3>';

	if($ratingcount){	

		$output .= '<div class="product-rating">';

		$output .= $rating;

		$output .= '<div class="rating-count"> <span class="count-text">'.sprintf(_n('%d review', '%d reviews', $ratingcount, 'partdo'), $ratingcount).'</span></div>';

		$output .= '</div>';

	}



	$output .= '<span class="price ff">';

	$output .= $price;

	$output .= '</span><!-- price -->';

	$output .= '</div><!-- content-wrapper -->';

	$output .= '</div><!-- product-content -->';

	$output .= '</div>';

	

	return $output;

}



/*----------------------------

  Add my owns Product Box

 ----------------------------*/

function partdo_shop_box () {

	if(get_theme_mod('partdo_product_box_type') == 'type3'){

		echo partdo_product_type3();

	} elseif (get_theme_mod('partdo_product_box_type') == 'type2'){

		echo partdo_product_type2();

	} else {

		echo partdo_product_type1();

	}

}



/*************************************************

## Woo Cart Ajax

*************************************************/ 



add_filter('woocommerce_add_to_cart_fragments', 'partdo_header_add_to_cart_fragment');

function partdo_header_add_to_cart_fragment( $fragments ) {

	global $woocommerce;

	ob_start();

	?>

	

	<span class="cart-count count"><?php echo sprintf(_n('%d', '%d', $woocommerce->cart->cart_contents_count, 'partdo'), $woocommerce->cart->cart_contents_count);?></span>

	



	<?php

	$fragments['span.cart-count'] = ob_get_clean();



	return $fragments;

}



add_filter( 'woocommerce_add_to_cart_fragments', function($fragments) {



    ob_start();

    ?>



    <div class="fl-mini-cart-content">

        <?php woocommerce_mini_cart(); ?>

    </div>



    <?php $fragments['div.fl-mini-cart-content'] = ob_get_clean();



    return $fragments;



} );



add_filter('woocommerce_add_to_cart_fragments', 'partdo_header_add_to_cart_fragment_subtotal');

function partdo_header_add_to_cart_fragment_subtotal( $fragments ) {

	global $woocommerce;

	ob_start();

	?>

	

    <p class="cart-price price"><?php echo WC()->cart->get_cart_subtotal(); ?></p>



    <?php $fragments['.cart-price'] = ob_get_clean();



	return $fragments;

}



add_filter('woocommerce_add_to_cart_fragments', 'partdo_header_add_to_cart_fragment_cart_count_text');

function partdo_header_add_to_cart_fragment_cart_count_text( $fragments ) {

	global $woocommerce;

	ob_start();

	?>

	

    <span class="cart-count-text count-text"><?php echo sprintf(_n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'partdo'), $woocommerce->cart->cart_contents_count);?></span>



    <?php $fragments['.cart-count-text'] = ob_get_clean();



	return $fragments;

}







/*************************************************

## Partdo Woo Search Form

*************************************************/ 



add_filter( 'get_product_search_form' , 'partdo_custom_product_searchform' );



function partdo_custom_product_searchform( $form ) {



	$form = '<form class="search-form" action="' . esc_url( home_url( '/'  ) ) . '">

                <input class="form-control search-input" type="search" value="' . get_search_query() . '" name="s" placeholder="'.esc_attr__('Search by Product Type, Part Number, or Brand...','partdo').'" autocomplete="off"/>

                <button class="btn" type="submit"><i class="klbth-icon-search"></i></button>

             </form>';

				  



	return $form;

}



function partdo_header_product_search() {

	$terms = get_terms( array(

		'taxonomy' => 'product_cat',

		'hide_empty' => true,

		'parent'    => 0,

	) );



	$form = '';



	if(class_exists( 'DGWT_WC_Ajax_Search' )){

		$form .= do_shortcode('[wcas-search-form]');

	} else {

		$form .= '<form class="search-form" action="' . esc_url( home_url( '/'  ) ) . '">';

		$form .= '<input class="form-control search-input" type="search" value="' . get_search_query() . '" name="s" placeholder="'.esc_attr__('Search for products...','partdo').'" autocomplete="off"/>';

		$form .= '<button class="btn" type="submit"><i class="klbth-icon-search"></i></button>';

		$form .= '<input type="hidden" name="post_type" value="product" />';

		$form .= '</form><!-- search-form -->';

	}



	return $form;

}



/*************************************************

## Partdo Gallery Thumbnail Size

*************************************************/ 

add_filter( 'woocommerce_get_image_size_gallery_thumbnail', function( $size ) {

    return array(

        'width' => 90,

        'height' => 54,

        'crop' => 0,

    );

} );





/*************************************************

## Quick View Scripts

*************************************************/ 



function partdo_quick_view_scripts() {

  	wp_enqueue_script( 'partdo-quick-ajax', get_template_directory_uri() . '/assets/js/custom/quick_ajax.js', array('jquery'), '1.0.0', true );

	wp_enqueue_script( 'partdo-tab-ajax', get_template_directory_uri() . '/assets/js/custom/tab-ajax.js', array('jquery'), '1.0.0', true );

	wp_localize_script( 'partdo-quick-ajax', 'MyAjax', array(

		'ajaxurl' => esc_url(admin_url( 'admin-ajax.php' )),

	));

  	wp_enqueue_script( 'partdo-variationform', get_template_directory_uri() . '/assets/js/custom/variationform.js', array('jquery'), '1.0.0', true );

	wp_enqueue_script( 'wc-add-to-cart-variation' );

}

add_action( 'wp_enqueue_scripts', 'partdo_quick_view_scripts' );



/*************************************************

## Quick View CallBack

*************************************************/

add_action( 'wp_ajax_nopriv_quick_view', 'partdo_quick_view_callback' );

add_action( 'wp_ajax_quick_view', 'partdo_quick_view_callback' );

function partdo_quick_view_callback() {



	$id = intval( $_POST['id'] );

	$loop = new WP_Query( array(

		'post_type' => 'product',

		'p' => $id,

	  )

	);

	

	while ( $loop->have_posts() ) : $loop->the_post(); 

	$product = new WC_Product(get_the_ID());

	

	$rating = wc_get_rating_html($product->get_average_rating());

	$price = $product->get_price_html();

	$rating_count = $product->get_rating_count();

	$review_count = $product->get_review_count();

	$average      = $product->get_average_rating();

	$product_image_ids = $product->get_gallery_attachment_ids();



	$output = '';

	

		$output .= '<div class="quickview-product single-product-wrapper product white-popup">';

		$output .= '<div class="quick-product-wrapper">';

		$output .= '<button title="'.esc_attr__('Close (Esc)', 'partdo').'" type="button" class="mfp-close">×</button>';



		$output .= '<article class="product single-product">';

		if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) {

			$att=get_post_thumbnail_id();

			$image_src = wp_get_attachment_image_src( $att, 'full' );

			$image_src = $image_src[0];

			

			$output .= '<div class="cell product-gallery thumbnails-bottom">';

			$output .= '<div class="product-images-wrapper">';

			$output .= '<div class="klbth-slider-wrapper">';

			$output .= '<div class="klbth-slider-loader">';

			$output .= '<div class="klbth-loader size-md color-primary border-sm"></div>';

			$output .= '</div>';

			$output .= '<div class="klbth-slider" id="product-images" data-items="1" data-mobileitems="1" data-tabletitems="1" data-arrows="false" data-dots="false" data-asfornav="#product-thumbnails">';



			$output .= '<div class="slider-item">'; 

			$output .= '<div class="entry-media"> <a href="#"><img src="'.esc_url($image_src).'"/></a></div>';

			$output .= '</div>';



			foreach( $product_image_ids as $product_image_id ){

				$image_url = wp_get_attachment_url( $product_image_id );

				$output .= '<div class="slider-item">'; 

				$output .= '<div class="entry-media"> <a href="#"><img src="'.esc_url($image_url).'"/></a></div>';

				$output .= '</div>';

			}



			$output .= '</div>';

			$output .= '</div>';

			$output .= '</div>';

			if($product_image_ids){

				$output .= '<div class="product-thumbnails-wrapper">';

				$output .= '<div class="klbth-slider-wrapper">';

				$output .= '<div class="klbth-slider-loader">';

				$output .= '<div class="klbth-loader size-md color-primary border-sm"></div>';

				$output .= '</div>';

				$output .= '<div class="klbth-slider" id="product-thumbnails" data-items="6" data-mobileitems="3" data-tabletitems="4" data-arrows="false" data-dots="false" data-focus="true" data-asfornav="#product-images">';



				$output .= '<div class="slider-item"> ';

				$output .= '<div class="entry-media"> <img src="'.esc_url($image_src).'"/></div>';

				$output .= '</div>';



				foreach( $product_image_ids as $product_image_id ){	

					$image_url = wp_get_attachment_url( $product_image_id );

					$output .= '<div class="slider-item"> ';

					$output .= '<div class="entry-media"> <img src="'.esc_url($image_url).'"/></div>';

					$output .= '</div>';

				}

				$output .= '</div>';

				$output .= '</div>';

				$output .= '</div>';

			}	

			$output .= '</div>';

		}

		

		$output .= '<div class="cell product-detail">';

			ob_start();

			do_action( 'woocommerce_single_product_summary' );

			$output .= ob_get_clean();

		$output .= '</div>';

		  

		$output .= '</article><!-- single-product -->';



		$output .= '</div><!-- quick-product-wrapper -->';



		$output .= '</div><!-- quickview-product -->';



		endwhile; 

		wp_reset_postdata();



	 	$output_escaped = $output;

	 	echo $output_escaped;

		

		wp_die();



}





/*************************************************

## Partdo Filter by Attribute

*************************************************/ 

function partdo_woocommerce_layered_nav_term_html( $term_html, $term, $link, $count ) { 



	$attribute_label_name = wc_attribute_label($term->taxonomy);;

	$attribute_id = wc_attribute_taxonomy_id_by_name($attribute_label_name);

	$attr  = wc_get_attribute($attribute_id);

	$array = json_decode(json_encode($attr), true);



	if($array['type'] == 'color'){

		$color = get_term_meta( $term->term_id, 'product_attribute_color', true );

		$term_html = '<div class="type-color"><span class="color-box" style="background-color:'.esc_attr($color).';"></span>'.$term_html.'</div>';

	}

	

	if($array['type'] == 'button'){

		$term_html = '<div class="type-button"><span class="button-box"></span>'.$term_html.'</div>';

	}



    return $term_html; 

}; 

         

add_filter( 'woocommerce_layered_nav_term_html', 'partdo_woocommerce_layered_nav_term_html', 10, 4 ); 





/*************************************************

## Shop Width Body Classes

*************************************************/



function partdo_body_classes( $classes ) {



	if( is_shop() && (get_theme_mod('partdo_shop_width') == 'wide' || partdo_get_option() == 'wide')) {

		$classes[] = 'shop-wide';

	}elseif( is_product() && (get_theme_mod('partdo_single_full_width') == 1 || partdo_get_option() == 'wide')) { 

		$classes[] = 'shop-wide';

	} else {

		$classes[] = '';

	}

	

	return $classes;

}

add_filter( 'body_class', 'partdo_body_classes' );



/*************************************************

## Stock Availability Translation

*************************************************/ 

if(get_theme_mod('partdo_stock_quantity',0) != 1){

add_filter( 'woocommerce_get_availability', 'partdo_custom_get_availability', 1, 2);

function partdo_custom_get_availability( $availability, $_product ) {

    

    // Change In Stock Text

    if ( $_product->is_in_stock() ) {

        $availability['availability'] = esc_html__('In Stock', 'partdo');

    }

    // Change Out of Stock Text

    if ( ! $_product->is_in_stock() ) {

        $availability['availability'] = esc_html__('Out of stock', 'partdo');

    }

    return $availability;

}

}



/*************************************************

## Archive Description After Content

*************************************************/

if(get_theme_mod('partdo_category_description_after_content',0) == 1){

	remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10);

	remove_action('woocommerce_archive_description', 'woocommerce_product_archive_description', 10);

	add_action('partdo_after_main_shop', 'woocommerce_taxonomy_archive_description', 5);

	add_action('partdo_after_main_shop', 'woocommerce_product_archive_description', 5);

}



/*************************************************

## Catalog Mode - Disable Add to cart Button

*************************************************/

if(get_theme_mod('partdo_catalog_mode', 0) == 1 || partdo_get_option() == 'catalogmode'){ 

	add_filter( 'woocommerce_loop_add_to_cart_link', 'partdo_remove_add_to_cart_buttons', 1 );

	function partdo_remove_add_to_cart_buttons() {

		return false;

	}

	remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 40);

}



/*************************************************

## Size Chart Modal

*************************************************/ 

function partdo_size_chart_modal(){

	$sizechart = get_post_meta( get_the_ID(), 'klb_product_size_chart', true );

	

	if($sizechart && is_product()){

		

		wp_enqueue_script( 'partdo-sizemodal');

		

		$chart  = '  <div class="size-box-holder">';

		$chart .= '<div class="size-box-inner">';

		$chart .= '<div class="size-box-close"><i class="klbth-icon-cancel"></i></div>';



		$chart .= $sizechart;

		

		$chart .= '</div><!-- size-box-inner -->';

		$chart .= '<div class="size-box-mask"></div>';

		$chart .= '</div><!-- size-box-holder -->';

		  

		echo partdo_sanitize_data($chart);

	}

}

add_action('wp_footer','partdo_size_chart_modal');



/*************************************************

## Related Products with Tags

*************************************************/

if(get_theme_mod('partdo_related_by_tags',0) == 1){

	add_filter( 'woocommerce_product_related_posts_relate_by_category', '__return_false' );

}



/*************************************************

## Woo Smart Compare Disable

*************************************************/ 

add_filter( 'woosc_button_position_archive', '__return_false' );

add_filter( 'woosc_button_position_single', '__return_false' );





/*************************************************

## Partdo WooCommerce Product Attributes

*************************************************/ 

function partdo_woocommerce_product_attributes(){

	global $product;

	$attributes = array_filter( $product->get_attributes(), 'wc_attributes_array_filter_visible' );

	?>

		<?php if($attributes){ ?>

			<div class="product-extra-details hide-below-992"> 

				<?php wc_display_product_attributes( $product ); ?>

			</div>

		<?php } ?>

		

	<?php

}

add_action('woocommerce_before_single_product_summary','partdo_woocommerce_product_attributes',30);





} // is woocommerce activated



/*************************************************

## Product Low Stock

*************************************************/

function partdo_product_low_stock() {

	

	global $product;

	

	$managestock = $product->managing_stock();

	$stock_quantity = $product->get_stock_quantity();

	$stock_format = esc_html__('This item is low in stock.','partdo');

	$stock_format2 = esc_html__('Item(s) left: %s','partdo');

	$stock_poor = '';

	

	if($managestock && $stock_quantity < 10) {

		$stock_poor .= '<div class="product-low-stock">';

			$stock_poor .= '<div class="icon">';

			$stock_poor .= '<svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 47.5 47.5" viewBox="0 0 47.5 47.5"><defs><clipPath id="a" clipPathUnits="userSpaceOnUse"><path d="M 0,38 38,38 38,0 0,0 0,38 Z"/></clipPath><clipPath id="b" clipPathUnits="userSpaceOnUse"><path d="m 18.583,27.833 c -2.957,-0.231 -5.666,2.542 -4.666,7.042 l 0,0 c -3.239,-2.386 -3.332,-6.403 -2.333,-9 l 0,0 C 12.625,23.167 11.542,20.917 9,20.667 l 0,0 C 6.161,20.387 4.584,23.709 6.038,29 l 0,0 C 3.52,26.035 2,22.195 2,18 l 0,0 C 2,8.611 9.611,1 19,1 l 0,0 c 9.389,0 17,7.611 17,17 l 0,0 c 0,2.063 -0.367,4.039 -1.04,5.868 l 0,0 C 34.5,18.48 31.627,15.711 28.625,17 l 0,0 c -2.812,1.208 -0.917,5.917 -0.777,8.164 l 0,0 c 0.236,3.809 -0.012,8.169 -6.931,11.794 l 0,0 c 2.875,-5.499 0.333,-8.917 -2.334,-9.125"/></clipPath></defs><g transform="matrix(1.25 0 0 -1.25 0 47.5)"><g clip-path="url(#a)"><path fill="#f4900c" d="M 0,0 C 0,2.063 -0.367,4.039 -1.04,5.868 -1.5,0.479 -4.373,-2.289 -7.375,-1 c -2.813,1.208 -0.917,5.917 -0.777,8.164 0.236,3.809 -0.012,8.169 -6.931,11.794 2.875,-5.5 0.333,-8.916 -2.334,-9.125 -2.958,-0.23 -5.666,2.542 -4.666,7.042 -3.238,-2.386 -3.333,-6.402 -2.334,-9 C -23.375,5.167 -24.458,2.917 -27,2.667 -29.839,2.387 -31.417,5.708 -29.962,11 -32.48,8.035 -34,4.195 -34,0 c 0,-9.389 7.611,-17 17,-17 9.389,0 17,7.611 17,17" transform="translate(36 18)"/></g><g clip-path="url(#b)"><path fill="#ffcc4d" d="m 0,0 c 0,2.187 -0.584,4.236 -1.605,6.001 0.147,-3.084 -2.562,-4.293 -4.02,-3.709 -2.105,0.843 -1.541,2.291 -2.083,5.291 -0.542,3 -2.625,5.084 -5.709,6 2.25,-6.333 -1.247,-8.667 -3.08,-9.084 C -18.369,4.073 -20.25,4.5 -20.465,8.506 -22.648,6.332 -24,3.324 -24,0 c 0,-6.627 5.373,-12 12,-12 6.627,0 12,5.373 12,12" transform="translate(31 7)"/></g></g></svg></div>';

			$stock_poor .= '<div class="info"><span>'.sprintf($stock_format).'</span>';

			$stock_poor .= '<p>'.sprintf($stock_format2, $stock_quantity).'</p>';

			$stock_poor .= '</div>';

		$stock_poor .= '</div>';

	}

	

	echo $stock_poor;

}

add_action( 'woocommerce_after_add_to_cart_button',  'partdo_product_low_stock' );



?>