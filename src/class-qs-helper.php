<?php

namespace quickster;

class QS_Helper {

	/**
	 * @return array
	 */
	public static function get_available_products() {
		$args     = array(
			'posts_per_page' => - 1,
			'post_type'      => 'product',
			'post_status'    => 'publish',
		);
		$products = array();
		$posts    = get_posts( $args );

		foreach ( $posts as $product ) {
			array_push( $products, array( $product->post_title, $product->ID ) );
		}

		return $products;
	}

	/**
	 * @return array
	 */
	public static function get_available_product_categories() {
		$cats                 = get_terms( 'product_cat', array(
			'hide_empty' => false,
		) );
		$available_categories = array();

		foreach ( $cats as $cat ) {
			array_push( $available_categories, array( $cat->name, $cat->term_id ) );
		}

		return $available_categories;
	}

	/**
	 * @return array
	 */
	public static function get_available_product_tags() {
		$cats           = get_terms( 'product_tag', array(
			'hide_empty' => false,
		) );
		$available_tags = array();

		foreach ( $cats as $cat ) {
			array_push( $available_tags, array( $cat->name, $cat->term_id ) );
		}

		return $available_tags;
	}


	/**
	 * @param string $shortcode
	 *
	 * @return bool
	 */
	public static function content_has_shortcode( $shortcode = '' ) {

		global $post;
		$post_obj = get_post( $post->ID );
		$found    = false;

		if ( ! $shortcode ) {
			return $found;
		}
		if ( stripos( $post_obj->post_content, '[' . $shortcode ) !== false ) {
			$found = true;
		}

		return $found;

	}


}