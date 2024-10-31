jQuery(document).ready(function ($) {

    /* switch product_id with variation_id when select changes */

    $(".product-variations-dropdown select").change(function () {

        let variation_id = $(this).find(':selected').val();
        let variation_price = $(this).find(':selected').data('price');
        let variation_stock = $(this).find(':selected').data('stock');

        let data = $(this).find(':selected').data('variation').split('|');
        let attributes = [];


        $(data).each(function () {
            let split = this.toString().split(':');

            attributes.push(split);

        });

        let product_id = $(this).next(".variation-add").data('product');

        let query = quickster.home_url + '/?add-to-cart=' + product_id + '&variation_id=' + variation_id;


        $(attributes).each(function () {
            let data = this;
            let attribute = data[0];
            let value = data[1];

            query += '&' + attribute + '=' + value;

        });


        $(this).next(".variation-add").prop("href", query);

        /* modify price for variation */

        let select_product_id = $(this).data('product');

        let current_price = $('span.price[data-product="' + select_product_id + '"]');
        let current_stock = $('span.stock[data-product="' + select_product_id + '"]');

        if (select_product_id === current_price.data('product')) {

            /* update price */

            let current_price_markup = $('span.price[data-product="' + select_product_id + '"] .woocommerce-Price-amount.amount');
            let result_price = '';


            switch (quickster['currency_position']) {

                case 'left':
                    result_price = '<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">' + quickster['currency'] + '</span>' + jQuery.number(variation_price, quickster['number_decimals'], quickster['decimal_separator'], quickster['thousand_separator']).toString() + '</span>';

                    break;
                case 'right':
                    result_price = '<span class="woocommerce-Price-amount amount">' + jQuery.number(variation_price, quickster['number_decimals'], quickster['decimal_separator'], quickster['thousand_separator']).toString() + '</span>' + '<span class="woocommerce-Price-currencySymbol">' + quickster['currency'] + '</span>';

                    break;
                case 'left_space':
                    result_price = '<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">' + quickster['currency'] + '</span>' + ' ' + jQuery.number(variation_price, quickster['number_decimals'], quickster['decimal_separator'], quickster['thousand_separator']).toString() + '</span>';

                    break;
                case 'right_space':
                    result_price = '<span class="woocommerce-Price-amount amount">' + jQuery.number(variation_price, quickster['number_decimals'], quickster['decimal_separator'], quickster['thousand_separator']).toString() + ' ' + '</span>' + '<span class="woocommerce-Price-currencySymbol">' + quickster['currency'] + '</span>';

                    break;
            }


            $(current_price_markup).html(result_price);


        }

        if (select_product_id === current_stock.data('product')) {

            $(current_stock).html('<span class="stock" data-product="' + select_product_id + '">' + variation_stock + '</span>');
        }


        /* modify add input field */

        let add = $('input.add[data-product="' + product_id + '"]');
        let qty = $('input.qty[data-product="' + product_id + '"]');


        $(add).attr('data-product', variation_id);
        $(qty).attr('data-product', variation_id);


        $(add).attr('data-attributes', JSON.stringify(attributes));


    });

    /* refresh WooCommerce cart fragments */

    function refresh_fragments() {
        $(document.body).trigger('wc_fragment_refresh');
    }

    /* add quantity to 1 if checked and quantity input value is empty */

    $('.add').on('change', function () {

        let select_product_id = $(this).data('product');
        let qty = $('.qty[data-product="' + select_product_id + '"]');

        if (qty.val() === '') {
            qty.val(1);
        }

        if (!$(this).is(':checked')) {
            qty.val('');
        }

    });

    /* dynamic calculate total for action bar */

    $('.add').on('change', function () {


        let cart_total = 0;

        $('[name=add]:checked').each(function () {

                let product_id = $(this).data('product');
                let qty = $('.qty[data-product="' + product_id + '"]').val();
                let current_price = $(this).data('price');

                cart_total = cart_total + (qty * current_price);

            }
        );

        let result_price = '';

        switch (quickster['currency_position']) {

            case 'left':
                result_price = '<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">' + quickster['currency'] + '</span>' + jQuery.number(cart_total, quickster['number_decimals'], quickster['decimal_separator'], quickster['thousand_separator']).toString() + '</span>';

                break;
            case 'right':
                result_price = '<span class="woocommerce-Price-amount amount">' + jQuery.number(cart_total, quickster['number_decimals'], quickster['decimal_separator'], quickster['thousand_separator']).toString() + '</span>' + '<span class="woocommerce-Price-currencySymbol">' + quickster['currency'] + '</span>';

                break;
            case 'left_space':
                result_price = '<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">' + quickster['currency'] + '</span>' + ' ' + jQuery.number(cart_total, quickster['number_decimals'], quickster['decimal_separator'], quickster['thousand_separator']).toString() + '</span>';

                break;
            case 'right_space':
                result_price = '<span class="woocommerce-Price-amount amount">' + jQuery.number(cart_total, quickster['number_decimals'], quickster['decimal_separator'], quickster['thousand_separator']).toString() + ' ' + '</span>' + '<span class="woocommerce-Price-currencySymbol">' + quickster['currency'] + '</span>';

                break;
        }


        $('.current-cart-total').html(result_price);

        if (result_price !== '' && $('.footer-action-bar').css('display') !== 'block') {
            $('.footer-action-bar').toggle('slow');
        }

    });


    /* ajax request for add to cart */

    $('.add_selection').click(function () {

        let products = [];
        let quantities = [];
        let attributes = [];

        $('[name=add]:checked').each(function () {

                products.push($(this).data('product'));
                attributes[$(this).data('product')] = $(this).data('attributes');
            }
        );

        $('input[name="quantity"]').each(function () {

            quantities.push({
                id: $(this).data('product'),
                quantity: $(this).val()
            });
        });


        let data = {
            action: 'add_to_cart',
            products: products.join(", "),
            quantities: quantities,
            attributes: attributes,
            security: quickster.ajax_nonce
        };

        $.ajax({
            url: quickster.ajax_url,
            type: 'post',
            data: data,
            success: function () {

                refresh_fragments();
                setInterval(refresh_fragments, 60000);

                /* custom cart message */

                let cmsg = quickster.cart_message;


                if (cmsg !== '') {

                    let background_color = cmsg['cart_message_background_color'];
                    let font_color = cmsg['cart_message_font_color'];
                    let cart_message = cmsg['wysiwyg_cart_message'];
                    let cart_url = cmsg['cart_url'];
                    let cart_url_text = cmsg['go_to_cart'];

                    let message = '<div class="quickster-cart-message" style="background-color:' + background_color + ';color:' + font_color + '"><div class="left"><p>' + cart_message + '</p></div><div class="right"><a class="button" href="' + cart_url + '">' + cart_url_text + '</a></div></div>';

                    $(".quickster-table").prepend(message);
                }

            }
        })
    });

    /* initialise datatable and set options */

    let quickster_table = $('.quickster-table table');

    if (quickster['activate_infinity'] === 'on') {
        $(quickster_table).DataTable({
            language: {
                search: "",
                sLengthMenu: "_MENU_"
            },
            scrollY: quickster['infinity_scroll_container_height'],
            scroller: true,
            responsive: true,
        });
    } else {
        $(quickster_table).DataTable({
            language: {
                search: "",
                sLengthMenu: "_MENU_"
            },
            responsive: true,
        });
    }

    $(quickster_table).addClass('hover stripe');
    $('.dataTables_filter label').addClass('quickster-search');
    $(".quickster-search input").attr("placeholder", quickster.search_placeholder);


});