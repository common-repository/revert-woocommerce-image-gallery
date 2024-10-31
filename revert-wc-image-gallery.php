<?php
/*
Plugin Name: Revert WooCommerce Image Gallery
Plugin URI:  http://wooassist.com
Description: This plugin reverts the product image gallery from v2.7 to v2.6+ gallery (without the zoom function). This should prevent you from zooming in on LowRes product images you have on products and should buy you time to upload HiRes images while enjoying the new v2.7 features.
Version:     1.0.1
Author:      Wooassist
Author URI:  http://wooassist.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}


if ( ! class_exists( 'Revert_WC_Image_Gallery' ) ) :

class Revert_WC_Image_Gallery {

	/**
	 * @var The single instance of the class		 
	 */
	private static $_instance = null;

	/**
	 * Main Revert_WC_Image_Gallery Instance
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *	 
	 * @static
	 * @see Revert_WC_Image_Gallery()
	 * @return Revert_WC_Image_Gallery main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Revert_WC_Image_Gallery Constructor.
	 */
	public function __construct() {	
	
		//load prettyPhoto files
		add_action('wp_enqueue_scripts', array( $this, 'woa_rwcig_prettyphoto' ) );
		//remove new product gallery
		add_action( 'init', array( $this, 'woa_remove_new_gallery' ) );		
		//add old product gallery
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'woa_old_image_gallery' ), 20 );
		//add old thumbnails
		add_action( 'woocommerce_product_thumbnails', array( $this, 'woa_old_thumbnails' ), 20 );
		
	}
	
	/**
	 * Load old image gallery template
	 */
	public function woa_old_image_gallery() {
		//include old gallery template
		include( '/includes/woa-old-gallery.php' );
	}
	
	/**
	 * Load old thumbnails template
	 */
	public function woa_old_thumbnails() {
		//include old thumbnails template
		include( '/includes/woa-old-thumbnails.php' );
	}
	
	/**
	 * Remove new image gallery (featured image and thumbnails)
	 */
	public function woa_remove_new_gallery() {
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
	remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
	}
	
	/**
	 * woa_rwcig add stylesheet for old image gallery and thumbnails
	 */
	public function woa_rwcig_prettyphoto() {
	
	if (is_product()) {
		wp_enqueue_script( 'woa_rwcig_prettyPhoto', plugins_url( '/js/jquery.prettyPhoto.min.js', __FILE__ ), array(), 2, TRUE );
		wp_enqueue_script( 'woa_rwcig_prettyPhoto-init', plugins_url( '/js/jquery.prettyPhoto.init.min.js', __FILE__ ), array(), 2, TRUE );
		wp_enqueue_style( 'woa_rwcig_prettyPhoto_css', plugins_url( '/css/prettyPhoto.css', __FILE__ ) );
		wp_enqueue_style( 'woa_rwcig_style', plugins_url( '/css/style.css', __FILE__ ) );
	}
	}
	
}

endif; // ! class_exists()

/**
 * Returns the main instance of Revert_WC_Image_Gallery.
 */
function woa_rwcig_run() {
	return Revert_WC_Image_Gallery::instance();
}

/**
 * WC Detection
 *
 * @since  1.5.4
 * @return boolean
 */
if ( ! function_exists( 'woa_rwcig_is_woocommerce_active' ) ) {
	function woa_rwcig_is_woocommerce_active() {
		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}
		
		return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins ) ;
	}
}

/*
 * Initialize
 */
if ( woa_rwcig_is_woocommerce_active() ) {
	
	woa_rwcig_run();
	
}