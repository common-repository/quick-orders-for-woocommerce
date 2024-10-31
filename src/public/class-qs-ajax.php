<?php

namespace quickster;

class QS_Ajax {

	/**
	 * QS_Table constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_add_to_cart', array( $this, 'add_to_cart' ) );
		add_action( 'wp_ajax_nopriv_add_to_cart', array( $this, 'add_to_cart' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );

	}

	/**
	 * return instance from class
	 */
	public static function get_instance() {
		new QS_Ajax();
	}

	/**
	 * enqueue ajax assets and localize script
	 */
	public function load_assets() {

		if ( QS_Helper::content_has_shortcode( 'quickster' ) ) {

			wp_enqueue_script( 'jquery-number-js', QUICKSTER_URL . '/assets/public/jquery.number.js', array( 'jquery' ), false );


			wp_enqueue_script( 'quickster-public-js', QUICKSTER_URL . '/assets/public/quickster-public.js', array(
				'jquery',
				'datatable-js',
				'jquery-number-js'
			), false );

			$additional_settings = get_option( 'quickster_additional_settings' );

			if ( is_array( $additional_settings ) AND array_key_exists( 'toggle_cart_message', $additional_settings ) AND $additional_settings['toggle_cart_message'] == 'on' ) {

				$cart_message = array(
					'wysiwyg_cart_message'          => $additional_settings['wysiwyg_cart_message'],
					'cart_message_background_color' => $additional_settings['cart_message_background_color'],
					'cart_message_font_color'       => $additional_settings['cart_message_font_color'],
					'go_to_cart'                    => __( 'Go to Cart', 'quick-orders-for-woocommerce' ),
					'cart_url'                      => wc_get_cart_url(),
				);

			} else {
				$cart_message = false;
			}

			if ( is_array( $additional_settings ) AND array_key_exists( 'infinity_scroll_container_height', $additional_settings ) ) {
				$container_height = $additional_settings['infinity_scroll_container_height'];
			} else {
				$container_height = 0;
			}

			if ( is_array( $additional_settings ) AND array_key_exists( 'toggle_infinity_scroll', $additional_settings ) ) {
				$container_height = $additional_settings['toggle_infinity_scroll'];
			} else {
				$infinity_status = 0;
			}

			wp_localize_script( 'quickster-public-js', 'quickster', array(
				'ajax_url'                         => admin_url( 'admin-ajax.php' ),
				'ajax_nonce'                       => wp_create_nonce( 'quick_order' ),
				'home_url'                         => get_bloginfo( 'url' ),
				'cart_message'                     => $cart_message,
				'infinity_scroll_container_height' => $container_height,
				'search_placeholder'               => __( 'Search..', 'quick-orders-for-woocommerce' ),
				'activate_infinity'                => $infinity_status,
				'currency'                         => get_woocommerce_currency_symbol(),
				'currency_position'                => get_option( 'woocommerce_currency_pos' ),
				'number_decimals'                  => get_option( 'woocommerce_price_num_decimals' ),
				'decimal_separator'                => get_option( 'woocommerce_price_decimal_sep' ),
				'thousand_separator'               => get_option( 'woocommerce_price_thousand_sep' ),
				'bulk_price_text'                  => get_option( 'bm_global_price_label' )
			) );
		}

	}


	/**
	 * ajax add selection to cart
	 */
	public function add_to_cart() {

		check_ajax_referer( 'quick_order', 'security' );

		global $woocommerce;

		$quantities = $_POST['quantities'];
		$products   = explode( ',', sanitize_text_field( $_POST['products'] ) );


		foreach ( $quantities as $product ) {

			$product_id = sanitize_text_field( $product['id'] );
			$quantity   = sanitize_text_field( $product['quantity'] );

			if ( in_array( $product_id, $products ) ) {
				$woocommerce->cart->add_to_cart( $product_id, $quantity );
			}
		}
	}


}