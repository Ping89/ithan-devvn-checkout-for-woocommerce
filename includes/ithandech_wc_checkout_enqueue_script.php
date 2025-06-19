<?php

function ithandech_checkout_quick_buy_enqueue_js(): void
{
    // Đảm bảo jQuery đã có
    wp_enqueue_script( 'jquery' );
    
    // Enqueue script custom
    wp_enqueue_script(
        'ithandech-billing-address-checkout-js',
        ithandech_wc_checkout_get_assets_file("ithandech_billing_address_selection.js", "js", "", ""),
        array('jquery'), 
        '1.0', 
        true
    );

    // Truyền biến ajaxurl cho JS (nếu cần)
    wp_localize_script(
        'ithandech-billing-address-checkout-js',
        'ithandech_billing_address_ajax_obj',
        array(
            'nonce' => wp_create_nonce('ithandech_load_address_nonce'), // Tạo nonce cho AJAX
            'ajaxurl' => admin_url('admin-ajax.php')
        )
    );
}

function ithandech_wc_checkout_enqueue_script_for_popup(): void 
{
    // Đảm bảo jQuery đã có
    wp_enqueue_script( 'jquery' );
    
    // Enqueue script custom
    wp_enqueue_script(
        'ithandech-billing-address-checkout-js',
        ithandech_wc_checkout_get_assets_file("ithandech_billing_address_selection_for_popup.js", "js", "", ""),
        array('jquery'), 
        '1.0', 
        true
    );

    // Truyền biến ajaxurl cho JS (nếu cần)
    wp_localize_script(
        'ithandech-billing-address-checkout-js',
        'ithandech_billing_address_ajax_obj',
        array(
            'nonce' => wp_create_nonce('ithandech_load_address_nonce'), // Tạo nonce cho AJAX
            'ajaxurl' => admin_url('admin-ajax.php')
        )
    );
}

function ithandech_wc_checkout_enqueue_js(): void
{
    // Đảm bảo jQuery đã có
    wp_enqueue_script( 'jquery' );
    
    // Enqueue script custom
    wp_enqueue_script(
        'ithandech-billing-address-checkout-js',
        ithandech_wc_checkout_get_assets_file("ithandech_billing_address_selection_for_wc_checkout_page.js", "js", "", ""),
        array('jquery'), 
        '1.0', 
        true
    );
    

    // Truyền biến ajaxurl cho JS (nếu cần)
    wp_localize_script(
        'ithandech-billing-address-checkout-js',
        'ithandech_billing_address_ajax_obj',
        array(
            'theme'     => 'theme-dark-plus',
            'nonce'     => wp_create_nonce('ithandech_load_address_nonce'), // Tạo nonce cho AJAX
            'ajaxurl'   => admin_url('admin-ajax.php')
        )
    );
}

add_action( 'wp_enqueue_scripts', 'ithandech_checkout_quick_buy_enqueue_js_action' );
function ithandech_checkout_quick_buy_enqueue_js_action(): void
{
    if ( is_checkout() ) {
        // ithan_checkout_quick_buy_enqueue_js(); // cho ithan quick buy
        ithandech_wc_checkout_enqueue_js();
    }
}

add_action('wp_enqueue_scripts', 'ithandech_woocommerce_enqueue_css');
function ithandech_woocommerce_enqueue_css(): void
{
    if (is_checkout()){
        // Enqueue file CSS
        $handle = 'ithandech-woocommerce-enqueue-css'; 

        wp_enqueue_style(
            $handle,
            ithandech_wc_checkout_get_assets_file("ithandech-devvn-woocommerce-checkout.css", "css", "", ""),
            array(), // Các dependency nếu có
            '1.0'
        );

        wp_enqueue_style(
            'ithandech-woocommerce-notices-enqueue-css',
            ithandech_wc_checkout_get_assets_file("ithandech-notices.css", "css", "", ""),
            array(), // Các dependency nếu có
            '1.0'
        );

        $opt = ithandech_for_wc_get_theme_options();

        if ( $opt['preset'] !== 'default' ){

            $css = ithandech_wc_checkout_get_css_theme($opt);   
            
            wp_add_inline_style( $handle, $css );
            wp_add_inline_style( 'woocommerce-general', $css );
        }
    }
}

// Admin

add_action('admin_enqueue_scripts', 'ithandech_woocommerce_enqueue_admin_edit_order_js');
function ithandech_woocommerce_enqueue_admin_edit_order_js($hook) {
    $screen = get_current_screen();
    // Kiểm tra xem có phải đang ở màn hình "wc-orders" và có action là "edit" hay không
    // && isset($_GET['action']) && 'edit' === $_GET['action']
    if ( isset( $screen->id ) && 'woocommerce_page_wc-orders' === $screen->id ) {
        wp_enqueue_script(
            'ithandech-admin-js', // handle cho script
            ithandech_wc_checkout_get_assets_file("ithandech_admin_edit_order.js", "js", "", ""),
            array('jquery'), // phụ thuộc vào jQuery
            '1.0', // phiên bản
            true // load ở footer
        );

        // Truyền biến ajaxurl cho JS (nếu cần)
        wp_localize_script(
            'ithandech-admin-js',
            'ithandech_billing_address_ajax_obj',
            array(
                'nonce' => wp_create_nonce('ithandech_load_address_nonce'), // Tạo nonce cho AJAX
                'ajaxurl' => admin_url('admin-ajax.php')
            )
        );


        wp_enqueue_style(
            'ithandech-admin-woocommerce-enqueue-css',
            ithandech_wc_checkout_get_assets_file("ithandech-devvn-admin-woocommerce-order.css", "css", "", ""),
            array(), // Các dependency nếu có
            '1.0'
        );
    }
}
