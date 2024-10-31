<?php

namespace quickster;

class QS_Admin
{
    /**
     * return instance from class
     */
    public static function get_instance()
    {
        new QS_Admin();
    }
    
    public function __construct()
    {
        add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts' ) );
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        $settings = new QS_Settings();
        /* Tab: shortcode */
        $settings->add_section( array(
            'id'    => 'quickster_shortcode',
            'title' => __( 'Shortcode', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_shortcode', array(
            'id'   => 'quickster_shortcode_introduction',
            'type' => 'documentation',
            'desc' => __( 'Use this little tool to <b>generate your shortcode</b>. Simply fill out the fields, hit save changes and copy your shortcode. You can use it as often as you want, it <b>does not</b> influence your current shortcodes. ', 'quick-orders-for-woocommerce' ),
            'name' => __( 'Shortcode Generator', 'quick-orders-for-woocommerce' ),
        ) );
        // Field: Select.
        $settings->add_field( 'quickster_shortcode', array(
            'id'      => 'select_data_options',
            'type'    => 'select',
            'name'    => __( 'Select your parameter', 'quick-orders-for-woocommerce' ),
            'options' => array(
            'product_cats' => 'Categories',
            'product_tags' => 'Tags',
            'products'     => 'SKU',
        ),
        ) );
        $settings->add_field( 'quickster_shortcode', array(
            'id'   => 'include_product_categories_selection',
            'type' => 'text',
            'name' => __( 'Select product categories', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_shortcode', array(
            'id'   => 'include_product_tags_selection',
            'type' => 'text',
            'name' => __( 'Select product tags', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_shortcode', array(
            'id'   => 'include_product_sku_selection',
            'type' => 'text',
            'name' => __( 'Select products', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_shortcode', array(
            'id'   => 'quickster_shortcode_generator',
            'type' => 'shortcode',
            'name' => '<h3 style="color:#bc2500;">' . __( 'Copy shortcode', 'quick-orders-for-woocommerce' ) . '</h3>',
        ) );
        /* Tab: columns */
        $settings->add_section( array(
            'id'    => 'quickster_table_heads',
            'title' => __( 'Columns', 'quick-orders-for-woocommerce' ),
        ) );
        /* thumbnail */
        $settings->add_field( 'quickster_table_heads', array(
            'id'   => 'title_thumbnail',
            'type' => 'title',
            'name' => '<h3>' . __( 'Thumbnail', 'quick-orders-for-woocommerce' ) . '</h3>',
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'toggle_thumbnail',
            'type'    => 'toggle',
            'default' => 'on',
            'name'    => __( 'Activate', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'label_thumbnail',
            'type'    => 'text',
            'default' => 'Thumbnail',
            'name'    => __( 'Label', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'position_thumbnail',
            'default' => 1,
            'type'    => 'number',
            'name'    => __( 'Position', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'select_thumbnail_link',
            'type'    => 'select',
            'name'    => __( 'Activate product link', 'quick-orders-for-woocommerce' ),
            'options' => array(
            'no'  => 'no',
            'yes' => 'yes',
        ),
        ) );
        /* title */
        $settings->add_field( 'quickster_table_heads', array(
            'id'   => 'title_title',
            'type' => 'title',
            'name' => '<h3>' . __( 'Title', 'quick-orders-for-woocommerce' ) . '</h3>',
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'toggle_title',
            'type'    => 'toggle',
            'default' => 'on',
            'name'    => __( 'Activate', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'label_title',
            'type'    => 'text',
            'default' => 'Title',
            'name'    => __( 'Label', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'position_title',
            'default' => 2,
            'type'    => 'number',
            'name'    => __( 'Position', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'select_title_link',
            'type'    => 'select',
            'name'    => __( 'Activate product link', 'quick-orders-for-woocommerce' ),
            'options' => array(
            'no'  => 'no',
            'yes' => 'yes',
        ),
        ) );
        /* price */
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'title_price',
            'default' => 'on',
            'type'    => 'title',
            'name'    => '<h3>' . __( 'Price', 'quick-orders-for-woocommerce' ) . '</h3>',
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'   => 'toggle_price',
            'type' => 'toggle',
            'name' => __( 'Activate Price', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'label_price',
            'type'    => 'text',
            'default' => 'on',
            'default' => 'Price',
            'name'    => __( 'Label', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'position_price',
            'default' => 3,
            'type'    => 'number',
            'name'    => __( 'Position', 'quick-orders-for-woocommerce' ),
        ) );
        /* stock */
        $settings->add_field( 'quickster_table_heads', array(
            'id'   => 'title_stock',
            'type' => 'title',
            'name' => '<h3>' . __( 'Stock', 'quick-orders-for-woocommerce' ) . '</h3>',
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'   => 'toggle_stock',
            'type' => 'toggle',
            'name' => __( 'Activate Stock', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'label_stock',
            'type'    => 'text',
            'default' => 'Stock',
            'name'    => __( 'Label', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'position_stock',
            'default' => 4,
            'type'    => 'number',
            'name'    => __( 'Position', 'quick-orders-for-woocommerce' ),
        ) );
        /* cart_link */
        $settings->add_field( 'quickster_table_heads', array(
            'id'   => 'title_cart_link',
            'type' => 'title',
            'name' => '<h3>' . __( 'Add to Cart', 'quick-orders-for-woocommerce' ) . '</h3>',
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'toggle_cart_link',
            'type'    => 'toggle',
            'default' => 'on',
            'name'    => __( 'Activate Single Add to Cart', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'label_cart_link',
            'type'    => 'text',
            'default' => 'Add to Cart',
            'name'    => __( 'Label', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'position_cart_link',
            'default' => 9,
            'type'    => 'number',
            'name'    => __( 'Position', 'quick-orders-for-woocommerce' ),
        ) );
        /* quantity */
        $settings->add_field( 'quickster_table_heads', array(
            'id'   => 'title_quantity',
            'type' => 'title',
            'name' => '<h3>' . __( 'Quantity', 'quick-orders-for-woocommerce' ) . '</h3>',
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'toggle_quantity',
            'type'    => 'toggle',
            'default' => 'on',
            'name'    => __( 'Activate Quantity', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'label_quantity',
            'type'    => 'text',
            'default' => 'Quantity',
            'name'    => __( 'Label', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'position_quantity',
            'default' => 9,
            'type'    => 'number',
            'name'    => __( 'Position', 'quick-orders-for-woocommerce' ),
        ) );
        /* add selection to cart */
        $settings->add_field( 'quickster_table_heads', array(
            'id'   => 'title_add_selection',
            'type' => 'title',
            'name' => '<h3>' . __( 'Add selection to cart', 'quick-orders-for-woocommerce' ) . '</h3>',
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'toggle_add_selection',
            'type'    => 'toggle',
            'default' => 'on',
            'name'    => __( 'Activate Add selection to cart', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'label_add_selection',
            'type'    => 'text',
            'default' => 'Add selection to cart',
            'name'    => __( 'Label', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_table_heads', array(
            'id'      => 'position_add_selection',
            'default' => 12,
            'type'    => 'number',
            'name'    => __( 'Position', 'quick-orders-for-woocommerce' ),
        ) );
        /* Tab: addditional options */
        $settings->add_section( array(
            'id'    => 'quickster_additional_settings',
            'title' => __( 'Settings', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_additional_settings', array(
            'id'   => 'toggle_product_without_stock',
            'type' => 'toggle',
            'name' => __( 'Exclude Products with no stock', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_additional_settings', array(
            'id'   => 'toggle_product_without_price',
            'type' => 'toggle',
            'name' => __( 'Exclude Products with no price', 'quick-orders-for-woocommerce' ),
        ) );
        /* Tab: documentation */
        $settings->add_section( array(
            'id'    => 'quickster_documentation',
            'title' => __( 'Documentation', 'quick-orders-for-woocommerce' ),
        ) );
        $settings->add_field( 'quickster_documentation', array(
            'id'   => 'documentation_shortcode',
            'type' => 'documentation',
            'name' => '<b>' . __( 'Shortcode', 'quick-orders-for-woocommerce' ) . '</b>',
            'desc' => __( 'To generate a product table on your page use the shortcode', 'quick-orders-for-woocommerce' ) . '<code>[quickster]</code>.',
        ) );
        $settings->add_field( 'quickster_documentation', array(
            'id'   => 'documentation_parameter',
            'type' => 'documentation',
            'name' => '<b>' . __( 'Parameter', 'quick-orders-for-woocommerce' ) . '</b>',
            'desc' => __( 'You can add the following parameters to your shortcode: <b>product_cat, product_tag, sku</b>. You can add this parameters to your shortcode like so:', 'quick-orders-for-woocommerce' ) . '<br><code>[quickster product_cat="hoodies"]</code>.',
        ) );
        $settings->add_field( 'quickster_documentation', array(
            'id'   => 'documentation_multi_parameter',
            'type' => 'documentation',
            'name' => '<b>' . __( 'Multiple parameter values', 'quick-orders-for-woocommerce' ) . '</b>',
            'desc' => __( 'You can add multiple parameter values like so:', 'quick-orders-for-woocommerce' ) . '<br><code>[quickster product_cat="hoodies,shirts,albums"]</code>.',
        ) );
    }
    
    public function add_admin_scripts()
    {
        wp_enqueue_style( 'quickster-admin', QUICKSTER_URL . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'quickster-admin.css' );
        wp_enqueue_style( 'select-woo-css', QUICKSTER_URL . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'selectWoo.min.css' );
        wp_enqueue_script(
            'select-woo-js',
            QUICKSTER_URL . '/assets/admin/selectWoo.full.min.js',
            array( 'jquery' ),
            false
        );
        wp_enqueue_script(
            'quickster-admin-js',
            QUICKSTER_URL . '/assets/admin/quickster-admin.js',
            array( 'jquery' ),
            false
        );
        wp_localize_script( 'quickster-admin-js', 'quickster_admin', array(
            'products'     => QS_Helper::get_available_products(),
            'product_cats' => QS_Helper::get_available_product_categories(),
            'product_tags' => QS_Helper::get_available_product_tags(),
            'logo'         => QUICKSTER_URL . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . '/admin/' . DIRECTORY_SEPARATOR . 'quickster-logo.png',
        ) );
    }

}