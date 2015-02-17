<?php
/*
Plugin Name: Woocommerce Flipper
Plugin URI: https://wordpress.org/plugins/woocommerce-flipper/
Version:0.1
Description: Shows a back side of  product on mouse hover.Suits for display your product both front and back side view.
Author: srinivasan
Author URI: https://github.com/rohithseenu
Text Domain:  Woocommerce-Flipper
Domain Path: /languages/

	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	/**
	 * Localisation (with WPML support)
	 **/
	add_action( 'init', 'plugin_init' );
	function plugin_init() {
		load_plugin_textdomain( 'woocommerce-flipper', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}


	/**
	 * New Image class
	 **/
	if ( ! class_exists( 'WC_flip' ) ) {

		class WC_flip {

			public function __construct() {
				add_action( 'wp_enqueue_scripts', array( $this, 'image_flip' ) );														// Enqueue the styles
				add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'woocommerce_template_loop_second_product_thumbnail' ), 11 );
				add_filter( 'post_class', array( $this, 'product_has_gallery' ) );
			}


	        /*-----------------------------------------------------------------------------------*/
			/* Class Functions */
			/*-----------------------------------------------------------------------------------*/

			// Setup styles
			function image_flip() {
				if ( apply_filters( 'woocommerce_product_image_flipper_styles', true ) ) {
					wp_enqueue_style( 'pif-styles', plugins_url( '/assets/css/style.css', __FILE__ ) );
				}
				wp_enqueue_script( 'flip-script', plugins_url( '/assets/js/script.js', __FILE__ ), array( 'jquery' ) );
			}

			// Add flip-image-gallery class to products that have a gallery
			function product_has_gallery( $classes ) {
				global $product;

				$post_type = get_post_type( get_the_ID() );

				if ( ! is_admin() ) {

					if ( $post_type == 'product' ) {

						$attachment_ids = $product->get_gallery_attachment_ids();

						if ( $attachment_ids ) {
							$classes[] = 'flip-image-gallery';
						}
					}

				}

				return $classes;
			}


			/*-----------------------------------------------------------------------------------*/
			/* Frontend Functions */
			/*-----------------------------------------------------------------------------------*/

			// Display the second thumbnails
			function woocommerce_template_loop_second_product_thumbnail() {
				global $product, $woocommerce;

				$attachment_ids = $product->get_gallery_attachment_ids();

				if ( $attachment_ids ) {
					$secondary_image_id = $attachment_ids['1'];
					echo wp_get_attachment_image( $secondary_image_id, 'shop_catalog', '', $attr = array( 'class' => 'secondary-image attachment-shop-catalog' ) );
				}
			}

		}


		$WC_flip = new WC_flip();
	}
}