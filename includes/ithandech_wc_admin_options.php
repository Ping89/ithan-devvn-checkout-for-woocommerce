<?php 

// Exit if accessed directly

use PHP_CodeSniffer\Files\DummyFile;

if ( !defined( 'ABSPATH' ) ) exit;

/*--------------------------------------------------------------*
 * 2.  Thêm submenu dưới WooCommerce
 *--------------------------------------------------------------*/
### Mẫu code sau khi tinh chỉnh

/* Thêm submenu sau WooCommerce (priority 98) */
static $ithandech_wc_hook_suffix = '';

add_action( 'admin_menu', function () use ( &$ithandech_wc_hook_suffix ) {

	$ithandech_wc_hook_suffix = add_submenu_page(
		'woocommerce',
		__( 'VN Address Customizer', 'ithan-devvn-checkout-for-woocommerce' ),
		__( 'VN Address Customizer', 'ithan-devvn-checkout-for-woocommerce' ),
		'manage_woocommerce',
		ITHANDECH_WOO_PAGE_SLUG,
		'ithandech_wc_render_settings_page'
	);

}, 98 );

/* 2. Enqueue asset đúng trang */
add_action( 'admin_enqueue_scripts', function ( $hook ) use ( &$ithandech_wc_hook_suffix ) {

	if ( $hook !== $ithandech_wc_hook_suffix ) {
		return;
	}

	// --- JS ---
	$js_path_src = ithandech_wc_checkout_get_assets_file("ithandech_admin_setting_page.js", "js", "", "");
	$js_ver = filemtime( $js_path_src );
	wp_enqueue_script(
		'ithandech-woo-admin-setting-js',
		$js_path_src,
		[ 'jquery' ],
		$js_ver,
		true
	);

    /* Truyền dữ liệu PHP → JS (được WP tự động esc_js) */
	wp_localize_script(
		'ithandech-woo-admin-setting-js', // handle
		'IthandechCheckout',                     // object name JS
		[
			'optName'   => ITHANDECH_WOO_OPT_NAME, // 'ithandech_theme_options'
		]
	);

	// --- CSS ---
	$css_path = plugin_dir_path( __FILE__ ) . 'assets/css/ithandech-devvn-admin-woocommerce-setting-page.css';
	wp_enqueue_style(
		'ithandech-admin-setting-css',
		$css_path,
		[],
		filemtime( $css_path )
	);
} );


/*--------------------------------------------------------------*
 * 3.  Đăng ký option + section + field
 *--------------------------------------------------------------*/
add_action( 'admin_init', function () {

    global $ITHANDECH_GENDER_TITLES;

	// 3.1 Option & hàm sanitize
    // phpcs:ignore PluginCheck.CodeAnalysis.SettingSanitization.register_settingDynamic
	register_setting(
		ITHANDECH_WOO_OPT_GROUP,
		ITHANDECH_WOO_OPT_NAME,
		[
			'type'              => 'array',
			'sanitize_callback' => 'ithandech_wc_setting_page_sanitize',
			'default'           => [
				'preset'       => 'default',
				'color_scheme' => 'light',
				'features'     => [],
			],
		]
	);                                                          // :contentReference[oaicite:1]{index=1}

	// 3.2 Section
	add_settings_section(
		ITHANDECH_WOO_SECTION_ID,
		__( 'Cài đặt giao diện cho trang thanh toán WC', 'ithan-devvn-checkout-for-woocommerce' ),
		'__return_false',
		ITHANDECH_WOO_PAGE_SLUG
	);

	// $options = get_option( ITHANDECH_WOO_OPT_NAME, [] );

    $options = wp_parse_args(
        get_option( ITHANDECH_WOO_OPT_NAME, [] ),
        [ 
            'gender_options'        => 'default',
            'gender_custom_options' => [],
            'preset'                => 'default',
        ]
    );

    ithandech_add_admin_radio_field(
        'gender_options',
        [
            'default' => __( 'Mặc định', 'ithan-devvn-checkout-for-woocommerce' ),
            'custom'  => __( 'Tùy chỉnh', 'ithan-devvn-checkout-for-woocommerce' )
        ],
        __( 'Xưng hô', 'ithan-devvn-checkout-for-woocommerce' ),
        $options
    );

	ithandech_add_admin_checkbox_field(
        'gender_custom_options',
        $ITHANDECH_GENDER_TITLES,
        __( 'Áp dụng xưng hô', 'ithan-devvn-checkout-for-woocommerce' ), // label
        $options,                       // current options
        ITHANDECH_WOO_OPT_NAME,               // option name (giữ nguyên)
        ITHANDECH_WOO_SECTION_ID,             // section
        ITHANDECH_WOO_PAGE_SLUG,              // page
        'ith-gender-custom-row'         // <‑ class cho <tr>
    );

    ithandech_add_admin_selection_field(
        'preset',
        [
            'custom' => __( 'Tùy chỉnh', 'ithan-devvn-checkout-for-woocommerce' ),
            'default' => __( 'Mặc định', 'ithan-devvn-checkout-for-woocommerce' ),
            'pink'    => __( 'Hồng', 'ithan-devvn-checkout-for-woocommerce' ),
            'blue'   => __( 'Xanh dương', 'ithan-devvn-checkout-for-woocommerce' ),
            'yellow'   => __( 'Vàng', 'ithan-devvn-checkout-for-woocommerce' ),
            'white'   => __( 'Trắng', 'ithan-devvn-checkout-for-woocommerce' ),
            'brown'   => __( 'Nâu', 'ithan-devvn-checkout-for-woocommerce' ),
            'cream'   => __( 'Kem', 'ithan-devvn-checkout-for-woocommerce' ),
            'black'   => __( 'Đen', 'ithan-devvn-checkout-for-woocommerce' ),
        ],
        __( 'Gói giao diện sẵn có/ tùy chỉnh', 'ithan-devvn-checkout-for-woocommerce' ),
        $options                // truyền options hiện tại
    );

    ithandech_add_custom_colors($options);

} );

function ithandech_add_custom_colors($options){
    $args = [
        'class' => 'iththeme__opt'
    ];

    ithandech_add_admin_input_field(
        'background_color',
		__( 'Màu nền', 'ithan-devvn-checkout-for-woocommerce' ),
        '#1e1e1e',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'surface_color',
		__( 'Màu bề mặt', 'ithan-devvn-checkout-for-woocommerce' ),
        '#2d3c2f',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'primary_text_color',
		__( 'Màu chữ chính', 'ithan-devvn-checkout-for-woocommerce' ),
        '#d4d4d4',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'secondary_text_color',
		__( 'Màu chữ phụ', 'ithan-devvn-checkout-for-woocommerce' ),
        '#555',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'muted_text_color',
		__( 'Màu chữ cỡ nhỏ', 'ithan-devvn-checkout-for-woocommerce' ),
        '#6b7280',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'product_info_color',
		__( 'Màu thông tin sản phẩm', 'ithan-devvn-checkout-for-woocommerce' ),
        '#5da5e0',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'accent_color',
		__( 'Màu nhấn mạnh (hover khi chọn selectbox)', 'ithan-devvn-checkout-for-woocommerce' ),
        '#007acc',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'selected_color',
		__( 'Màu đã chọn (trên selectbox)', 'ithan-devvn-checkout-for-woocommerce' ),
        '#a4805e',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'error_color',
		__( 'Màu thông báo lỗi (màu chỉ thị, màu nền))', 'ithan-devvn-checkout-for-woocommerce' ),
        '#e15858',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'price_color',
		__( 'Màu hiển thị giá cả', 'ithan-devvn-checkout-for-woocommerce' ),
        '#e06c75',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'bg_tooltip_color',
		__( 'Màu nền hiển thị nhãn', 'ithan-devvn-checkout-for-woocommerce' ),
        '#fff',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'text_tooltip_color',
		__( 'Màu chữ hiển thị nhãn', 'ithan-devvn-checkout-for-woocommerce' ),
        '#b3b6bb',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'raidio_checkbox_border_color',
		__( 'Màu đường viền các nút chọn lựa (tròn)', 'ithan-devvn-checkout-for-woocommerce' ),
        '#333333',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'raidio_checkbox_border_active_color',
		__( 'Màu đường viền các nút chọn lựa (tròn) khi chọn', 'ithan-devvn-checkout-for-woocommerce' ),
        '#38d35a',
        $options,
        args:$args
    );
    
    ithandech_add_admin_input_field(
        'order_total_border_line_color',
		__( 'Màu dòng kẻ ngang/ đường viền', 'ithan-devvn-checkout-for-woocommerce' ),
        '#e5e7eb',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'checkout_button_bg_color',
		__( 'Màu nút đặt hàng', 'ithan-devvn-checkout-for-woocommerce' ),
        '#d26e4b',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'checkout_button_hover_bg_color',
		__( 'Màu nút đặt hàng khi hover', 'ithan-devvn-checkout-for-woocommerce' ),
        '#d26e4b',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'order_ticket_devider_color',
		__( 'Màu dòng kẻ ngang mã QR thanh toán', 'ithan-devvn-checkout-for-woocommerce' ),
        '#ddd',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'order_ticket_container_color',
		__( 'Màu nền mã QR thanh toán', 'ithan-devvn-checkout-for-woocommerce' ),
        '#2d3c2f',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'order_ticket_address_circle_color',
		__( 'Màu nền khuyết tròn địa chỉ thanh toán', 'ithan-devvn-checkout-for-woocommerce' ),
        '#7AA880',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'order_boder_hover_card_color',
		__( 'Màu nền đường viền khi hover thanh toán', 'ithan-devvn-checkout-for-woocommerce' ),
        '#c026d3',
        $options,
        args:$args
    );

    ithandech_add_admin_input_field(
        'order_box_shadow_color',
		__( 'Màu bóng đổ thẻ trong thanh toán', 'ithan-devvn-checkout-for-woocommerce' ),
        '#171717',
        $options,
        args:$args
    );
}

function ithandech_for_wc_get_theme_options() : array {

	$defaults = [
		'preset'                            => 'default',
		'color_scheme'                      => 'light',
		'features'                          => [],

        // theme options
		'background_color'                  => '#1e1e1e',
		'surface_color'                     => '#2d3c2f',
        'primary_text_color'                => '#d4d4d4',
        'secondary_text_color'              => '#555',

        'message_info_color'                => '#5da5e0',
        'message_info_hover_color'          => '#92bb77',

        'accent_color'                      => '#007acc',
        'selected_color'                    => '#a4805e',
        'error_color'                       => '#e15858',
        'price_color'                       => '#e06c75',

        'bg_tooltip_color'                  => '#fff;',
        'text_tooltip_color'                => '#b3b6bb',

        // only for wc checkout page
        'muted_color'                       => '#6b7280',
        'border_color'                      => '#e5e7eb',

        // for order review
        'order_ticket_devider_color'        => '#ddd',
        'order_ticket_container_color'      => '#2d3c2f',
        'order_ticket_address_circle_color' => '#7AA880',
        'order_boder_hover_card_color'      => '#c026d3',
        'order_box_shadow_color'            => '#171717',

        // for buy-now-popup
        'quantity_color'                    => '#d4d4d4',
        'quantity_bnt_bg_color'             => '#ee4d2d',
        'quantity_bnt_bg_hover_color'       => '#f27b1d',

        'order_review_line_color'           => '#2d3c2f',
        'order_review_text_color'           => '#555',
        'order_review_sumit_color'          => '#d4d4d4',
        'order_review_sumit_bg_color'       => '#ee4d2d',

        'gender_options'                    => 'default',
        'gender_custom_options'             => []
	];

	$saved = get_option( ITHANDECH_WOO_OPT_NAME, [] );

	// wp_parse_args → giữ giá trị đã lưu, đổ mặc định cho phần thiếu
	return wp_parse_args( $saved, $defaults );
}

/*--------------------------------------------------------------*
 * 6.  Render form
 *--------------------------------------------------------------*/
function ithandech_wc_render_settings_page() { ?>
	<div class="wrap woocommerce">
		<h1><?php esc_html_e( 'VN Address Customizer', 'ithan-devvn-checkout-for-woocommerce' ); ?></h1>

		<?php settings_errors(); // hiển thị error|updated :contentReference[oaicite:2]{index=2} ?>

		<form method="post" action="options.php">
			<?php
				settings_fields( ITHANDECH_WOO_OPT_GROUP );
				do_settings_sections( ITHANDECH_WOO_PAGE_SLUG );
				submit_button();
			?>
		</form>
	</div>
<?php }