<?php

function quickster_fs()
{
    global  $quickster_fs ;
    
    if ( !isset( $quickster_fs ) ) {
        require_once dirname( __FILE__ ) . '/freemius/start.php';
        $quickster_fs = fs_dynamic_init( array(
            'id'             => '1901',
            'slug'           => 'quick-orders-for-woocommerce',
            'type'           => 'plugin',
            'public_key'     => 'pk_7a9211571eb810bc38864bbc7f2ef',
            'is_premium'     => false,
            'has_addons'     => false,
            'has_paid_plans' => true,
            'menu'           => array(
            'slug'    => 'quickster',
            'support' => false,
            'parent'  => array(
            'slug' => 'woocommerce',
        ),
        ),
            'is_live'        => true,
        ) );
    }
    
    return $quickster_fs;
}

quickster_fs();
do_action( 'quickster_fs_loaded' );
function quickster_cleanup()
{
    $options = array(
        'quickster_shortcode',
        'quickster_table_heads',
        'quickster_additional_settings',
        'quickster_documentation'
    );
    
    if ( is_multisite() ) {
        foreach ( $options as $option ) {
            delete_site_option( $option );
        }
    } else {
        foreach ( $options as $option ) {
            delete_option( $option );
        }
    }

}

quickster_fs()->add_action( 'after_uninstall', 'quickster_cleanup' );