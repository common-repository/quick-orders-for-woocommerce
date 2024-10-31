<?php
/*
Plugin Name: Quickster
Text Domain: quick-orders-for-woocommerce
Domain Path: /languages
Description: Create flexible and modern Product Tables and Bulk Order Forms for WooCommerce.
Author: patrickposner
Version: 1.0.2
*/

if ( ! function_exists( 'quickster_fs' ) ) {

	define( 'QUICKSTER_ABSPATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
	define( 'QUICKSTER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
	define( "QUICKSTER_PATH", untrailingslashit( plugin_dir_path( __FILE__ ) ) );
	define( "QUICKSTER_URL", untrailingslashit( plugin_dir_url( __FILE__ ) ) );

	/* load setup */
	require_once( QUICKSTER_ABSPATH . 'inc' . DIRECTORY_SEPARATOR . 'setup.php' );

	/* localize */
	$textdomain_dir = plugin_basename( dirname( __FILE__ ) ) . '/languages';
	load_plugin_textdomain( 'quick-orders-for-woocommerce', false, $textdomain_dir );



	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require __DIR__ . '/vendor/autoload.php';
	}

	/* intialize classes */

	quickster\QS_Activation::init();
	quickster\QS_Admin::get_instance();
	quickster\QS_Table::get_instance();
	quickster\QS_Ajax::get_instance();
}