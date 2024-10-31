jQuery(document).ready(function ($) {

    /* add dynamic class to selects of shortcode tab */
    let counter = 1;

    $('#quickster_shortcode input[type=text]').each(function () {
        $(this).addClass('select_' + counter);
        counter++;
    });

    /* autocomplete for products */

    let product_data = [];

    $(quickster_admin['products']).each(function () {
        let product = {
            id: this[1],
            text: this[0] + ' (ID: ' + this[1] + ')',
        }
        product_data.push(product);
    });

    $('.select_3').selectWoo({
        data: product_data,
        multiple: true,
    });

    /* autocomplete for product categories */

    let cat_data = [];

    $(quickster_admin['product_cats']).each(function () {

        let cat = {
            id: this[1],
            text: this[0] + ' (ID: ' + this[1] + ')',
        }

        cat_data.push(cat);

    });

    $('.select_1').selectWoo({
        data: cat_data,
        multiple: true,
    });

    /* autocomplete for product tags */

    let tag_data = [];

    $(quickster_admin['product_tags']).each(function () {

        let tag = {
            id: this[1],
            text: this[0] + ' (ID: ' + this[1] + ')',
        }

        tag_data.push(tag);

    });

    $('.select_2').selectWoo({
        data: tag_data,
        multiple: true,
    });


    /* freemius checkout */

    let logo = '<div class="quickster-logo checkout-logo" style="background-image:url(' + quickster_admin.logo + ');background-size: contain;background-repeat: no-repeat;"></div>\n';
    $('#fs_pricing').prepend(logo);


});