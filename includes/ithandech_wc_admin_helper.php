<?php 

// Exit if accessed directly

use PHP_CodeSniffer\Files\DummyFile;

if ( !defined( 'ABSPATH' ) ) exit;

/*--------------------------------------------------------------*
 * 1.  Định nghĩa hằng số (dễ đọc – tránh gõ sai)
 *--------------------------------------------------------------*/
const ITHANDECH_WOO_OPT_NAME   = 'ithandech_woo_theme_options';   // tên option ở DB
const ITHANDECH_WOO_OPT_GROUP  = 'ithandech_woo_theme_group';     // tên group Settings API
const ITHANDECH_WOO_PAGE_SLUG  = 'ithandech_woo_theme_page';      // slug page
const ITHANDECH_WOO_SECTION_ID = 'ithandech_woo_theme_section';   // id section

/**
 * Thêm field radio (một lựa chọn duy nhất) vào Settings API.
 *
 * @param string $field_key            Khóa trong option (vd. 'color_scheme').
 * @param array  $choices              Mảng value => label hoặc ['light','dark'].
 * @param string $field_label          Nhãn hiển thị bên trái (mặc định dùng i18n).
 * @param array  $in_options           Mảng option đã get_option().
 */
function ithandech_add_admin_radio_field(
	string $field_key,
	array  $choices,
    string $field_label          = 'Lựa chọn',
	array  $in_options           = []
) {

	add_settings_field(
		$field_key,
		$field_label,
		// ------- CALLBACK -------
		function () use ( $field_key, $choices, $in_options ) {

			$current = $in_options[ $field_key ] ?? '';   // giá trị đã lưu (string)

			echo '<fieldset>';

			foreach ( $choices as $value => $label ) {

				// Nếu mảng chỉ chứa value → key số
				if ( is_int( $value ) ) {
					$value = $label;
				}

				printf(
					'<label style="display:block;margin:4px 0;">
						<input type="radio" name="%1$s[%2$s]" value="%3$s" %4$s />
						%5$s
					</label>',
					esc_attr( ITHANDECH_WOO_OPT_NAME ),
					esc_attr( $field_key ),
					esc_attr( $value ),
					checked( $current, $value, false ),
					esc_html( $label )
				);
			}

			echo '</fieldset>';
		},
		ITHANDECH_WOO_PAGE_SLUG,
		ITHANDECH_WOO_SECTION_ID
	);
}

/**
 * Checkbox group – có thêm $row_class để ẩn/hiện bằng JS.
 */
function ithandech_add_admin_checkbox_field(
	string $field_key,
	array  $choices,
	string $label,
	array  $current_options = [],
	string $option_name     = ITHANDECH_WOO_OPT_NAME,
	string $section_id      = ITHANDECH_WOO_SECTION_ID,
	string $page_slug       = ITHANDECH_WOO_PAGE_SLUG,
	string $row_class       = ''          // <‑ duy nhất mới
) {
	add_settings_field(
		$field_key,                 // 1. id
		$label,                     // 2. title
		/* 3. callback */ function () use ( $field_key, $choices, $current_options, $option_name ) {

			$selected = $current_options[ $field_key ] ?? [];

			echo '<fieldset>';
			foreach ( $choices as $val => $text ) {
				if ( is_int( $val ) ) { $val = $text; }
				printf(
					'<label style="display:block">
						<input type="checkbox" name="%1$s[%2$s][]" value="%3$s" %4$s/>
						%5$s
					</label>',
					esc_attr( $option_name ),
					esc_attr( $field_key ),
					esc_attr( $val ),
					checked( in_array( $val, (array) $selected, true ), true, false ),
					esc_html( $text )
				);
			}
			echo '</fieldset>';
		},
		$page_slug,                 // 4. page
		$section_id,                // 5. section
		[                           // 6. args  ← class nằm ở đây
			'class' => $row_class,
		]
	);
}


/**
 * Thêm một ô input đơn (text, color, number…) vào Settings API.
 *
 * @param string $field_key        Khóa bên trong option (vd. 'primary_color').
 * @param string $label            Nhãn hiển thị bên trái.
 * @param mixed  $default          Giá trị mặc định.
 * @param array  $current_options  Kết quả get_option(); để [] sẽ tự nạp.
 * @param string $option_name      Tên option lưu DB.
 * @param string $section_id       ID section đã add_settings_section().
 * @param string $page_slug        Slug page đã add_submenu_page().
 * @param string $type             Loại input ('text','color','number'…).
 * @param string $class            Lớp CSS thêm cho <input>.
 */
function ithandech_add_admin_input_field(
	string $field_key,
	string $label,
	$default                = '',
	array  $current_options = [],
	string $option_name     = ITHANDECH_WOO_OPT_NAME,
	string $section_id      = ITHANDECH_WOO_SECTION_ID,
	string $page_slug       = ITHANDECH_WOO_PAGE_SLUG,
	string $type            = 'text',
	string $class           = 'regular-text',
	array $args		        = []         // <‑ class, html attributes in tr
) {

	add_settings_field(
		$field_key,
		$label,
		/* ---------- CALLBACK ---------- */
		function () use ( $field_key, $default, $current_options, $option_name, $type, $class ) {

			// Nếu người gọi chưa truyền $current_options, tự nạp
			$options = $current_options ?: get_option( $option_name, [] );
			$value   = esc_attr( $options[ $field_key ] ?? $default );

			printf(
				'<input type="%1$s" name="%2$s[%3$s]" value="%4$s" class="%5$s" />',
				esc_attr( $type ),
				esc_attr( $option_name ),
				esc_attr( $field_key ),
				esc_attr( $value ),    // ← escape here
				esc_attr( $class )
			);
		},
		$page_slug,
		$section_id,
		args:$args
	);
}


/**
 * Thêm field <select> vào Settings API.
 *
 * @param string $field_key        Khóa bên trong option (vd. 'preset').
 * @param array  $choices          Mảng value => label OR ['default','dark'].
 * @param string $label            Nhãn hiển thị bên trái.
 * @param array  $current_options  Mảng kết quả get_option(); để [] sẽ tự nạp.
 * @param string $option_name      Tên option lưu DB.
 * @param string $section_id       ID section Settings API.
 * @param string $page_slug        Slug page hiển thị (submenu).
 */
function ithandech_add_admin_selection_field(
	string $field_key,
	array  $choices,
	string $label,
	array  $current_options = [],
	string $option_name     = ITHANDECH_WOO_OPT_NAME,
	string $section_id      = ITHANDECH_WOO_SECTION_ID,
	string $page_slug       = ITHANDECH_WOO_PAGE_SLUG,
	array $args		        = []         // <‑ class, html attributes
) {

	add_settings_field(
		$field_key,
		$label,
		/* ---------- CALLBACK ---------- */
		function () use ( $field_key, $choices, $current_options, $option_name ) {

			$options = $current_options ?: get_option( $option_name, [] );
			$current = $options[ $field_key ] ?? array_key_first( $choices );

			echo '<select name="' . esc_attr( $option_name ) . '[' . esc_attr( $field_key ) . ']">';

			foreach ( $choices as $value => $label ) {

				// Nếu mảng value‑only → key số
				if ( is_int( $value ) ) {
					$value = $label;
				}

				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $value ),
					selected( $current, $value, false ),
					esc_html( $label )
				);
			}

			echo '</select>';
		},
		$page_slug,
		$section_id,
		$args
	);
}


/*--------------------------------------------------------------*
 * 5.  Hàm sanitize (xác thực & lưu)
 *--------------------------------------------------------------*/

function ithandech_wc_setting_page_sanitize_color( 
	array  $input,
	array  $output,
	string $setting_name,
	string $color_setting_name,
	string $valid_code,
	string $error_type,
	string $error_message
){
	if ( isset( $input[$color_setting_name] ) ) {

		// WP hàm có sẵn: trả về '#rrggbb' hoặc null nếu sai
		$hex = sanitize_hex_color( $input[$color_setting_name] );

		if ( $hex ) {
			$output[$color_setting_name] = $hex;
		} else {
			add_settings_error(
				$setting_name,
				$valid_code,
				$error_message,
				$error_type
			);
		}
	}

	return $output;
}

/**
 * Lọc và xác thực dữ liệu trang “VN Address Customizer”. 
 *
 * @param array $input Dữ liệu thô gửi từ form.
 * @return array       Dữ liệu sạch để lưu trong wp_options.
 */
function ithandech_wc_setting_page_sanitize( $input ) {

	global $ITHANDECH_GENDER_TITLES;
	$output = [];

	/* ===== 1) Preset (select) ===== */
	if ( isset( $input['preset'] ) ) {
		$allowed = [ 'custom', 'default', 'pink', 'blue', 'yellow', 'white', 'brown', 'cream', 'black' ];
		$val     = sanitize_text_field( $input['preset'] );
		if ( in_array( $val, $allowed, true ) ) {
			$output['preset'] = $val;
		}
	}

	/* ===== 2) Background color (input[type=color]) ===== */
	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'background_color',
		'bg-color-invalid',
		'error',
		__( 'Mã màu nền không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);

	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'surface_color',
		'surface-color-invalid',
		'error',
		__( 'Mã màu bề mặt không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);

	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'primary_text_color',
		'primary-text-color-invalid',
		'error',
		__( 'Mã màu chữ chính không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);
	
	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'secondary_text_color',
		'secondary-text-color-invalid',
		'error',
		__( 'Mã màu chữ phụ không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);

	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'muted_text_color',
		'muted-text-color-invalid',
		'error',
		__( 'Mã màu chữ cỡ nhỏ không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);

	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'product_info_color',
		'product-info-color-invalid',
		'error',
		__( 'Mã màu thông tin sản phẩm không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);

	// $output = ithandech_wc_setting_page_sanitize_color(
	// 	$input,
	// 	$output,
	// 	ITHANDECH_WOO_OPT_NAME,
	// 	'product_info_hover_color',
	// 	'product-info-hover-color-invalid',
	// 	'error',
	// 	__( 'Mã thông tin sản phẩm khi hover không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	// );

	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'accent_color',
		'accent-color-invalid',
		'error',
		__( 'Mã màu nhấn mạnh (khi hover trên selectbox) không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);

	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'selected_color',
		'selected-color-invalid',
		'error',
		__( 'Mã màu đã chọn (trên selectbox) không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);

	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'error_color',
		'error-color-invalid',
		'error',
		__( 'Mã màu thông báo lỗi không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);

	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'price_color',
		'price-color-invalid',
		'error',
		__( 'Mã màu hiển thị giá cả không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);

	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'bg_tooltip_color',
		'bg-tooltip-color-invalid',
		'error',
		__( 'Mã màu nền nhãn hiển thị không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);

	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'raidio_checkbox_border_color',
		'raidio-checkbox-border-invalid',
		'error',
		__( 'Mã màu viền các nút lựa chọn (tròn) không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);

	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'raidio_checkbox_border_active_color',
		'raidio-checkbox-border-active-invalid',
		'error',
		__( 'Mã màu viền các nút lựa chọn (tròn) khi chọn không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);

	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'order_total_border_line_color',
		'order-total-border-line-color-invalid',
		'error',
		__( 'Mã màu dòng kẻ ngang/ đường viền không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);

	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'checkout_button_bg_color',
		'checkout-button-bg-color-invalid',
		'error',
		__( 'Mã màu nút đặt hàng không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);

	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'checkout_button_hover_bg_color',
		'checkout-button-hover-bg-color-invalid',
		'error',
		__( 'Mã màu nút đặt hàng khi hover không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);

	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'order_ticket_devider_color',
		'order-ticket-devider-color-invalid',
		'error',
		__( 'Mã màu dòng kẻ ngang mã QR thanh toán không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);

	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'order_ticket_container_color',
		'order-ticket-container-color-invalid',
		'error',
		__( 'Mã màu nền mã QR thanh toán không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);

	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'order_ticket_address_circle_color',
		'order-ticket-address-circle-color-invalid',
		'error',
		__( 'Mã màu nền khuyết tròn địa chỉ thanh toán không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);
	
	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'order_boder_hover_card_color',
		'order-boder-hover-card-color-invalid',
		'error',
		__( 'Mã nền đường viền khi hover thanh toán không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);
	
	$output = ithandech_wc_setting_page_sanitize_color(
		$input,
		$output,
		ITHANDECH_WOO_OPT_NAME,
		'order_box_shadow_color',
		'order-box-shadow-color-invalid',
		'error',
		__( 'Mã màu bóng đổ thẻ trong thanh toán không hợp lệ. Ví dụ hợp lệ: #ff6633', 'ithan-devvn-checkout-for-woocommerce' )
	);

	/* ===== 4) Gender (select) ===== */
	if ( isset( $input['gender_options'] ) ) {
		$allowed = [ 'default', 'custom' ];
		$val     = sanitize_text_field( $input['gender_options'] );
		if ( in_array( $val, $allowed, true ) ) {
			$output['gender_options'] = $val;
		}
	}

	/* ===== 5) Gender Custom (checkbox array) ===== */
	if ( isset( $input['gender_custom_options'] ) && is_array( $input['gender_custom_options'] ) ) {

		$gender_title_keys = array_keys($ITHANDECH_GENDER_TITLES);

		$clean = [];

		foreach ( $input['gender_custom_options'] as $choice ) {

			$choice = sanitize_text_field( $choice ); // lọc XSS
			if ( in_array( $choice, $gender_title_keys, true ) ) {
				$clean[] = $choice;
			}
		}

		$output['gender_custom_options'] = $clean; // mảng giá trị HỢP LỆ
	}

	/* ===== 6) Thông báo thành công / lỗi chung ===== */
	if ( ! empty( $output ) ) {
		add_settings_error(
			ITHANDECH_WOO_OPT_NAME,
			'saved',
			__( 'Đã lưu cài đặt.', 'ithan-devvn-checkout-for-woocommerce' ),
			'updated'
		);
	} elseif ( empty( $output ) ) {
		add_settings_error(
			ITHANDECH_WOO_OPT_NAME,
			'no-valid-settings',
			__( 'Không có giá trị hợp lệ để lưu.', 'ithan-devvn-checkout-for-woocommerce' ),
			'error'
		);
	}

	return $output;
}