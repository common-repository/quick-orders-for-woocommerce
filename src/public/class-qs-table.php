<?php

namespace quickster;

use  Donquixote\Cellbrush\Table\Table ;
class QS_Table
{
    /**
     * QS_Table constructor.
     */
    public function __construct()
    {
        add_action( 'wp_enqueue_scripts', array( $this, 'add_table_assets' ) );
        add_shortcode( 'quickster', array( $this, 'add_table_shortcode' ) );
        /* support for version 0.7 */
        add_shortcode( 'quick_order', array( $this, 'add_table_shortcode' ) );
    }
    
    /**
     * return instance from class
     */
    public static function get_instance()
    {
        new QS_Table();
    }
    
    /**
     * add assets for tables
     */
    public function add_table_assets()
    {
        
        if ( QS_Helper::content_has_shortcode( 'quickster' ) ) {
            wp_enqueue_style( 'datatable-css', QUICKSTER_URL . '/assets/public/datatables/datatables.min.css' );
            wp_enqueue_style( 'quickster-public-css', QUICKSTER_URL . '/assets/public/quickster-public.css' );
            wp_enqueue_script(
                'datatable-js',
                QUICKSTER_URL . '/assets/public/datatables/datatables.min.js',
                array( 'jquery' ),
                false
            );
        }
    
    }
    
    /**
     * @param $atts
     *
     * @return string
     * @throws \Exception
     */
    public function add_table_shortcode( $atts )
    {
        $html = '<div class="quickster-table">';
        $table = Table::create();
        /* table head */
        $heads = get_option( 'quickster_table_heads' );
        $advanced = get_option( 'quickster_additional_settings' );
        
        if ( is_array( $heads ) and array_key_exists( 'toggle_add_selection', $heads ) ) {
            $add_selection = $heads['toggle_add_selection'];
            $table_heads = array();
            foreach ( $heads as $option => $value ) {
                if ( strpos( $option, 'toggle' ) !== false ) {
                    $name = str_replace( 'toggle_', '', $option );
                }
                if ( strpos( $option, 'toggle' ) !== false ) {
                    $status = $value;
                }
                if ( strpos( $option, 'position' ) !== false ) {
                    $position = $value;
                }
                if ( strpos( $option, 'label' ) !== false ) {
                    $label = $value;
                }
                if ( isset( $status ) and isset( $position ) and isset( $label ) ) {
                    
                    if ( $status != 'off' ) {
                        $values = array(
                            'slug'     => $name,
                            'status'   => $status,
                            'position' => intval( $position ),
                            'label'    => $label,
                        );
                        $table_heads[$name] = $values;
                    }
                
                }
            }
            /* sort table_heads by position */
            foreach ( $table_heads as $key => $row ) {
                $table_head[$key] = $row['position'];
            }
            array_multisort( $table_head, SORT_ASC, $table_heads );
            $table_head_slugs = array();
            $table_head_names = array();
            foreach ( $table_heads as $th ) {
                $table_head_slugs[] = $th['slug'];
            }
            foreach ( $table_heads as $th ) {
                $table_head_names[] = $th['label'];
            }
            $table->thead()->addRowName( 'quickster-head' );
            foreach ( $table_heads as $th ) {
                $table->thead()->th( 'quickster-head', $th['slug'], $th['label'] );
            }
            $table->addColNames( $table_head_slugs );
            /* table body */
            
            if ( isset( $atts ) && !empty($atts) ) {
                foreach ( $atts as $attribute => $value ) {
                    
                    if ( isset( $value ) && !empty($value) ) {
                        /* add rows */
                        $rows = array();
                        $a = 1;
                        while ( $a <= count( $this->get_products( $attribute, $value ) ) ) {
                            array_push( $rows, 'row' . $a );
                            $a++;
                        }
                        $table->addRowNames( $rows );
                        /* add data */
                        $b = 1;
                        foreach ( $this->get_products( $attribute, $value ) as $product ) {
                            foreach ( $table_head_slugs as $td ) {
                                $table->td( 'row' . $b, $td, $this->get_data( $td, $product->ID ) );
                            }
                            $b++;
                        }
                    }
                
                }
            } else {
                /* add rows */
                $rows = array();
                $a = 1;
                while ( $a <= count( $this->get_products( '', '' ) ) ) {
                    array_push( $rows, 'row' . $a );
                    $a++;
                }
                $table->addRowNames( $rows );
                /* add data */
                $b = 1;
                foreach ( $this->get_products( '', '' ) as $product ) {
                    foreach ( $table_head_slugs as $td ) {
                        $table->td( 'row' . $b, $td, $this->get_data( $td, $product->ID ) );
                    }
                    $b++;
                }
            }
            
            $html .= $table->render();
            if ( $add_selection == 'on' ) {
                $html .= '<a class="add_selection button">' . __( 'Add selection to cart', 'quick-orders-for-woocommerce' ) . '</a>';
            }
            $html .= '</div>';
        } else {
            $html = __( 'Please configure and save your the column options for Quickster before using the shortcode.', 'quick-orders-for-woocommerce' );
        }
        
        return $html;
    }
    
    /**
     * @param $attribute
     * @param $value
     *
     * @return array
     */
    public function get_products( $attribute, $value )
    {
        if ( strpos( $value, ',' ) !== false ) {
            $value = explode( ',', $value );
        }
        $additional_options = get_option( 'quickster_additional_settings' );
        $args['post_type'] = 'product';
        $args['post_status'] = 'publish';
        $args['posts_per_page'] = -1;
        /* exclude products with no stock */
        if ( is_array( $additional_options ) and array_key_exists( 'toggle_product_without_stock', $additional_options ) and $additional_options['toggle_product_without_stock'] == 'on' ) {
            $args['meta_query'] = array( array(
                'key'     => '_stock_status',
                'value'   => 'instock',
                'compare' => '=',
            ) );
        }
        /* exclude products with no price */
        if ( is_array( $additional_options ) and array_key_exists( 'toggle_product_without_price', $additional_options ) and $additional_options['toggle_product_without_price'] == 'on' ) {
            $args['meta_query'] = array( array(
                'key'     => '_regular_price',
                'value'   => '',
                'compare' => '!=',
            ) );
        }
        switch ( $attribute ) {
            case 'product_cat':
                $args['tax_query'] = array( array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => $value,
                    'operator' => 'IN',
                ) );
                break;
            case 'product_tag':
                $args['tax_query'] = array( array(
                    'taxonomy' => 'product_tag',
                    'field'    => 'slug',
                    'terms'    => $value,
                    'operator' => 'IN',
                ) );
                break;
            case 'sku':
                $args['meta_query'] = array( array(
                    'key'     => '_sku',
                    'value'   => $value,
                    'compare' => 'IN',
                ) );
                break;
        }
        $products = get_posts( $args );
        return $products;
    }
    
    /**
     * @param $type
     * @param $id
     *
     * @return int|null|string
     */
    public function get_data( $type, $id )
    {
        $product = wc_get_product( $id );
        $value = '';
        switch ( $type ) {
            case 'title':
                $column_options = get_option( 'quickster_table_heads' );
                $value = $product->get_name();
                if ( array_key_exists( 'select_title_link', $column_options ) and $column_options['select_title_link'] == 'yes' ) {
                    $value = '<a href="' . get_the_permalink( $product->get_id() ) . '">' . $product->get_name() . '</a>';
                }
                break;
            case 'price':
                $column_options = get_option( 'quickster_table_heads' );
                
                if ( array_key_exists( 'select_tax_display', $column_options ) and $column_options['select_tax_display'] == 'yes' ) {
                    /* woocommerce tax options */
                    $tax_display = get_option( 'woocommerce_tax_display_shop' );
                    /* get taxes for current product */
                    $tax = new \WC_Tax();
                    $taxes = $tax->get_rates( $product->get_tax_class() );
                    $rates = array_shift( $taxes );
                    $item_rate = round( array_shift( $rates ) );
                    /* check if incl or excl */
                    
                    if ( $tax_display == 'excl' ) {
                        $tax_line = sprintf( __( 'Plus %s', 'woocommerce-german-market' ), $item_rate );
                    } else {
                        $tax_line = sprintf( __( 'Includes %s', 'woocommerce-german-market' ), $item_rate );
                    }
                    
                    $value = '<span class="price" data-product="' . $product->get_id() . '">' . wc_price( $product->get_price() ) . '<br><small>' . $tax_line . '% ' . __( 'VAT', 'woocommerce-german-market' ) . '</small></span>';
                } else {
                    $value = '<span class="price" data-product="' . $product->get_id() . '">' . wc_price( $product->get_price() ) . '</span>';
                }
                
                break;
            case 'quantity':
                if ( !$product->is_type( 'external' ) and !$product->is_type( 'grouped' ) ) {
                    $value = '<input type="number" data-product="' . $product->get_id() . '" class="input-text qty text" step="1" min="0" max="9999" name="quantity" value="" title="Menge" size="4" pattern="[0-9]*" inputmode="numeric">';
                }
                break;
            case 'add_selection':
                if ( !$product->is_type( 'external' ) and !$product->is_type( 'grouped' ) ) {
                    $value = '<input name="add" class="add" data-product="' . $product->get_id() . '" data-price="' . get_post_meta( $product->get_id(), '_regular_price', true ) . '" type="checkbox">';
                }
                break;
            case 'cart_link':
                
                if ( $product->is_type( 'variable' ) ) {
                    $available_variations = $product->get_available_variations();
                    
                    if ( count( $available_variations ) > 0 ) {
                        $output = '<div class="product-variations-dropdown"><select id="available-variations" class="" name="available_variations" data-product="' . $product->get_id() . '">';
                        $output .= '<option value="">' . __( 'Choose a variation' ) . '</option>';
                        foreach ( $available_variations as $variation ) {
                            $option_value = array();
                            $option_attribute = array();
                            foreach ( $variation['attributes'] as $attribute => $term_slug ) {
                                $taxonomy = str_replace( 'attribute_', '', $attribute );
                                $attribute_name = get_taxonomy( $taxonomy )->labels->singular_name;
                                // Attribute name
                                $term_name = get_term_by( 'slug', $term_slug, $taxonomy )->name;
                                // Attribute value term name
                                $option_value[] = $attribute_name . ': ' . $term_name;
                                $option_attribute[] = $taxonomy . ':' . strtolower( $term_name );
                            }
                            $option_value = implode( ' | ', $option_value );
                            $comma_separated = implode( "|", $option_attribute );
                            $variation_obj = new \WC_Product_variation( $variation['variation_id'] );
                            $output .= '<option value="' . $variation['variation_id'] . '" data-variation="' . $comma_separated . '" data-price="' . get_post_meta( $variation['variation_id'], '_regular_price', true ) . '" data-stock="' . $variation_obj->get_stock_quantity() . '">' . $option_value . '</option>';
                        }
                        $output .= '</select>';
                        $output .= '<a class="button variation-add" data-product="' . $product->get_id() . '" href="#">' . __( 'Add to cart', 'quick-orders-for-woocommerce' ) . '</a>';
                        $output .= '</div>';
                        $value = $output;
                    }
                
                } else {
                    
                    if ( $product->is_type( 'external' ) ) {
                        $url = get_post_meta( $product->get_id(), '_product_url', true );
                        $button_text = get_post_meta( $product->get_id(), '_button_text', true );
                        $value = '<a class="button" href="' . $url . '">' . $button_text . '</a>';
                    } else {
                        
                        if ( $product->is_type( 'grouped' ) ) {
                            $value = '<a class="button" href="' . get_the_permalink( $product->get_id() ) . '">' . __( 'Go to bundle', 'quick-orders-for-woocommerce' ) . '</a>';
                        } else {
                            $url = get_bloginfo( 'url' ) . DIRECTORY_SEPARATOR . '?add-to-cart=' . $product->get_id();
                            $value = '<a class="button" href="' . $url . '">' . __( 'Add to cart', 'quick-orders-for-woocommerce' ) . '</a>';
                        }
                    
                    }
                
                }
                
                break;
            case 'stock':
                $value = '<span class="stock" data-product="' . $product->get_id() . '">' . $product->get_stock_quantity() . '</span>';
                break;
            case 'sku':
                $value = $product->get_sku();
                break;
            case 'product_tag':
                $product_tags = wp_get_post_terms( $product->get_id(), 'product_tag', array(
                    "fields" => "all",
                ) );
                if ( isset( $product_tags ) and !empty($product_tags) ) {
                    $value = $product_tags[0]->name;
                }
                break;
            case 'product_category':
                $product_cats = wp_get_post_terms( $product->get_id(), 'product_cat', array(
                    "fields" => "all",
                ) );
                if ( isset( $product_cats ) and !empty($product_cats) ) {
                    $value = $product_cats[0]->name;
                }
                break;
            case 'total_sales':
                $value = $product->get_total_sales();
                break;
            case 'price_per_unit':
                /* regular */
                $regular_unit = get_post_meta( $product->get_id(), '_unit_regular_price_per_unit', true );
                $regular_price_per_unit = get_post_meta( $product->get_id(), '_regular_price_per_unit', true );
                /* sale */
                $sale_unit = get_post_meta( $product->get_id(), '_unit_sale_price_per_unit', true );
                $sale_price_per_unit = get_post_meta( $product->get_id(), '_sale_price_per_unit', true );
                
                if ( !empty($sale_unit) and !empty($sale_price_per_unit) ) {
                    $value = wc_price( $sale_price_per_unit ) . ' ' . __( 'per', 'quick-orders-for-woocommerce' ) . ' ' . $sale_unit;
                } else {
                    if ( !empty($regular_unit) and !empty($regular_price_per_unit) ) {
                        $value = wc_price( $regular_price_per_unit ) . ' ' . __( 'per', 'quick-orders-for-woocommerce' ) . ' ' . $regular_unit;
                    }
                }
                
                break;
            case 'average_rating':
                $value = wc_get_rating_html( $product->get_average_rating() );
                break;
            case 'description':
                $value = $product->get_description();
                break;
            case 'thumbnail':
                $column_options = get_option( 'quickster_table_heads' );
                $value = '<img style="max-width:100px" src="' . get_the_post_thumbnail_url( $product->get_id() ) . '" />';
                if ( array_key_exists( 'select_thumbnail_link', $column_options ) and $column_options['select_thumbnail_link'] == 'yes' ) {
                    $value = '<a href="' . get_the_permalink( $product->get_id() ) . '"><img style="max-width:100px" src="' . get_the_post_thumbnail_url( $product->get_id() ) . '" /></a>';
                }
                break;
        }
        return $value;
    }

}