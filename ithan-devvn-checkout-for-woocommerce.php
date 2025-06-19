<?php
/**
 * Plugin Name: ithan devvn checkout for woocommerce
 * Requires Plugins: woocommerce, woo-checkout-field-editor-pro
 * Plugin URI: https://github.com/Ping89/ithan-devvn-checkout-for-woocommerce
 * Description: Custom Checkout Form for Vietnam addresses (of DEVVN-ithan plugin).
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * Tags: checkout, fields, woocommerce, custom, payment
 * Version: 3.2
 * Author: iThanDev Team
 * Author URI: https://github.com/Ping89
 * Text Domain: ithan-devvn-checkout-for-woocommerce
 * Domain Path: /languages
 * License: GPLv2 or later
 */

 if ( !defined( 'ABSPATH' ) ) exit;

// Exit if accessed directly

use laptrinhvienso0\ithandevvncheckoutforwoocommerce\Ithadech_Vietnam_Shipping;

function ithandech_load_titles() {
    // load_plugin_textdomain( 'ithan-devvn-checkout-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    // Bây giờ mới gọi __() an toàn
    global $ITHANDECH_GENDER_TITLES;
    $ITHANDECH_GENDER_TITLES = [
        'nam_nu'                => __( 'Nam-Nữ', 'ithan-devvn-checkout-for-woocommerce' ),
        'ong_ba'                => __( 'Ông-Bà', 'ithan-devvn-checkout-for-woocommerce' ),
        'bac-trai_bac-gai'      => __( 'Bác Trai-Bác Gái (Bá)', 'ithan-devvn-checkout-for-woocommerce' ),
        'chu_thim'              => __( 'Chú-Thím', 'ithan-devvn-checkout-for-woocommerce' ),
        'duong_co'              => __( 'Dượng-Cô', 'ithan-devvn-checkout-for-woocommerce' ),
        'cau_mo'                => __( 'Cậu-Mợ', 'ithan-devvn-checkout-for-woocommerce' ),
        'duong_di'              => __( 'Dượng-Dì', 'ithan-devvn-checkout-for-woocommerce' ),
        'anh_chi'               => __( 'Anh-Chị', 'ithan-devvn-checkout-for-woocommerce' ),
        'em-trai_em-gai'        => __( 'Em Trai-Em Gái', 'ithan-devvn-checkout-for-woocommerce' ),
        'chau-trai_chau-gai'    => __( 'Cháu Trai-Cháu Gái', 'ithan-devvn-checkout-for-woocommerce' ),
        'su-thay_su-co'         => __( 'Sư Thầy-Sư Cô', 'ithan-devvn-checkout-for-woocommerce' ),
        'cha-dao_ni-co'         => __( 'Cha Đạo-Ni Cô', 'ithan-devvn-checkout-for-woocommerce' ),
        'tia_ma'                => __( 'Tía-Má', 'ithan-devvn-checkout-for-woocommerce' ),
        'bo_me'                 => __( 'Bố-Mẹ', 'ithan-devvn-checkout-for-woocommerce' ),
        'cha_me'                => __( 'Cha-Mẹ', 'ithan-devvn-checkout-for-woocommerce' ),
    ];
}
add_action( 'init', 'ithandech_load_titles' );

define('ITHANDECH_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ITHANDECH_MALE_TITLES', ['Nam', 'Ông', 'Bác Trai', 'Tía', 'Cha', 'Bố', 'Thầy', 'Chú', 'Cậu', 'Dượng', 'Dượng', 'Anh', 'Em trai', 'Cháu trai', 'Con Trai', 'Sư Thầy', 'Tăng', 'Thượng Tọa', 'Cha Đạo', 'Chàng', 'Phu', 'Ta', 'Trẫm', 'Qủa Nhân', 'Nam Đế', 'Nam Vương']);
define('ITHANDECH_FEMALE_TITLES', ['Nữ', 'Bà', 'Bác Gái', 'Má', 'Mẹ', 'Mẹ', 'U', 'Thím', 'Mợ', 'Cô', 'Dì', 'Chị', 'Em gái', 'Cháu gái', 'Con Gái', 'Sư Cô', 'Ni', 'Bổn Tọa', 'Nữ Tu', 'Thiếp', 'Phụ', 'Tiện Nữ', 'Thảo Dân', 'Tiện Dân', 'Nữ Vương', 'Nữ Đế']);

// Kiểm tra nếu WooCommerce đã được kích hoạt
function ithandech_is_woocommerce_active(): bool
{
    $active_plugins = (array) get_option('active_plugins', array());
    if (is_multisite()) {
        $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
    }
    return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins) || class_exists('WooCommerce');
}

// Kiểm tra nếu Woo Checkout Field Editor Pro đã được kích hoạt
function ithandech_is_woo_checkout_field_editor_pro_active(): bool
{
    $active_plugins = (array) get_option('active_plugins', array());
    if (is_multisite()) {
        $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
    }
    return in_array('woo-checkout-field-editor-pro/checkout-form-designer.php', $active_plugins)
        || array_key_exists('woo-checkout-field-editor-pro/checkout-form-designer.php', $active_plugins)
        || class_exists('Woo_Checkout_Field_Editor_Pro');
}

// Kiểm tra cả hai plugin đã được kích hoạt hay chưa
add_action('admin_init', 'ithandech_check_required_plugins_active');

function ithandech_check_required_plugins_active(): void
{
    if ( !ithandech_is_woocommerce_active() ) {
        add_action('admin_notices', 'ithandech_woocommerce_required_notice');
    }

    if ( !ithandech_is_woo_checkout_field_editor_pro_active() ) {
        // Nếu Woo Checkout Field Editor Pro chưa được kích hoạt, hiển thị thông báo yêu cầu kích hoạt plugin này
        add_action( 'admin_notices', 'ithandech_checkout_field_editor_required_notice' );
    }
}

// Thông báo yêu cầu kích hoạt WooCommerce
function ithandech_woocommerce_required_notice(): void
{
    echo '<div class="error"><p><strong>' . esc_html(__('Bạn cần cài đặt và kích hoạt plugin "WooCommerce" trước khi sử dụng plugin ithan-devvn-checkout-for-woocommerce-plugin.', 'ithan-devvn-checkout-for-woocommerce')) . '</strong></p></div>';
}

// Thông báo yêu cầu kích hoạt Woo Checkout Field Editor Pro
function ithandech_checkout_field_editor_required_notice(): void
{
    echo '<div class="error"><p><strong>' . esc_html(__('Bạn cần cài đặt và kích hoạt plugin "Woo Checkout Field Editor Pro" trước khi sử dụng plugin ithan-devvn-checkout-for-woocommerce-plugin.', 'ithan-devvn-checkout-for-woocommerce')) . '</strong></p></div>';
}

// Bước 1: Nạp file
include_once ITHANDECH_PLUGIN_DIR . 'includes/ithandech_wc_checkout_helper.php';
include_once ITHANDECH_PLUGIN_DIR . 'includes/ithandech_tinh_thanhpho.php';
include_once ITHANDECH_PLUGIN_DIR . 'includes/ithandech_quan_huyen.php';
include_once ITHANDECH_PLUGIN_DIR . 'includes/ithandech_xa_phuong_thitran.php';

include_once ITHANDECH_PLUGIN_DIR . 'includes/ithadech_vietnam_shipping.php';

include_once ITHANDECH_PLUGIN_DIR . 'includes/ithandech_vietqr_options.php';

/**
 * Hàm trả về đối tượng ITHADECH_Vietnam_Shipping
 */
function ithandech_devvn_vietnam_shipping(): ?Ithadech_Vietnam_Shipping
{
    return Ithadech_Vietnam_Shipping::instance();
}

// Đăng ký callback cho AJAX
add_action('wp_ajax_ithandech_load_districts', 'ithandech_load_districts_callback');
add_action('wp_ajax_nopriv_ithandech_load_districts', 'ithandech_load_districts_callback');

function ithandech_load_districts_callback(): void
{
    // Kiểm tra nonce để bảo vệ khỏi CSRF
    if ( !isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'ithandech_load_address_nonce') ) {
        wp_send_json_error(array('message' => 'Nonce validation failed.'));
        exit;
    }

    // Lấy province_code từ AJAX và loại bỏ các dấu gạch chéo
    $province_code = isset($_POST['province_code']) ? sanitize_text_field(wp_unslash($_POST['province_code'])) : '';

    // Gọi hàm trong class plugin ithandech_devvn_vietnam_shipping()
    $districts = ithandech_devvn_vietnam_shipping()->ithandech_get_list_district_select(
        $province_code
    );

    // Trả về JSON
    wp_send_json($districts);
}


add_action('wp_ajax_ithandech_load_wards', 'ithandech_load_wards_callback');
add_action('wp_ajax_nopriv_ithandech_load_wards', 'ithandech_load_wards_callback');
function ithandech_load_wards_callback(): void
{
    if ( !isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'ithandech_load_address_nonce') ) {
        wp_send_json_error(array('message' => 'Nonce validation failed.'));
        exit;
    }

    $district_code = isset($_POST['district_code']) ? sanitize_text_field(wp_unslash($_POST['district_code'])) : '';

    $wards = ithandech_devvn_vietnam_shipping()->ithandech_get_list_village_select($district_code);

    wp_send_json($wards);
}

// 1) Khai báo states cho VN
add_filter('woocommerce_states', 'ithandech_vietnam_states');
function ithandech_vietnam_states( $states ) {
    global $ithandech_tinh_thanhpho;
    $states['VN'] = $ithandech_tinh_thanhpho; // mảng province
    return $states;
}


// 2) Tùy biến trường checkout

// Chuyển địa chỉ thanh toán về VN dù cho người dùng đang có địa chỉ nước ngoài
add_filter( 'woocommerce_checkout_get_value', 'ithandech_force_checkout_field_value_to_vn', 10, 2 );
function ithandech_force_checkout_field_value_to_vn( $value, $input ) {
    if ( $input === 'billing_country' || $input === 'shipping_country' ) {
        return 'VN';
    }
    return $value;
}

add_filter( 'woocommerce_checkout_fields', 'ithandech_add_checkout_gender_radio' );
function ithandech_add_checkout_gender_radio( $fields ) {

    // Thêm trường radio billing_gender
    $fields['billing']['billing_gender'] = array(
        'type'     => 'radio',
        'label'    => __( 'Xưng Hô', 'ithan-devvn-checkout-for-woocommerce' ),
        'required' => true,
        'options'  => array(
            'male'   => __( 'Nam', 'ithan-devvn-checkout-for-woocommerce' ),
            'female' => __( 'Nữ', 'ithan-devvn-checkout-for-woocommerce' ),
        ),
        'priority' => 5, // Cho nó nằm vị trí tùy ý
    );

    $fields['shipping']['shipping_gender'] = array(
        'type'     => 'radio',
        'label'    => __( 'Xưng Hô', 'ithan-devvn-checkout-for-woocommerce' ),
        'required' => true,
        'options'  => array(
            'male'   => __( 'Nam', 'ithan-devvn-checkout-for-woocommerce' ),
            'female' => __( 'Nữ', 'ithan-devvn-checkout-for-woocommerce' ),
        ),
        'priority' => 5, // Cho nó nằm vị trí tùy ý
    );

    return $fields;
}

add_filter( 'woocommerce_form_field', 'ithandech_customize_gender_radio', 10, 4 );
function ithandech_customize_gender_radio( $field, $key, $args, $value ) {
    global $ITHANDECH_GENDER_TITLES;

    // Kiểm tra nếu là trường "gender"
    if ( 'billing_gender' === $key || 'shipping_gender' === $key) {

        $prefix_field = 'billing';
        if ('shipping_gender' === $key){
            $prefix_field = 'shipping';
        }

        $ithan_opt = ithandech_for_wc_get_theme_options();
        $selected_keys_gender = $ithan_opt['gender_custom_options'];
        if ( $ithan_opt['gender_options'] !== 'default' && $selected_keys_gender !== [] ){
            $male_titles = [];
            $female_titles = [];

            foreach ( $selected_keys_gender as $key ) {
                if ( isset($ITHANDECH_GENDER_TITLES[$key]) ) {
                    // Lấy chuỗi "Nam-Nữ", "Ông-Bà",...
                    $value = $ITHANDECH_GENDER_TITLES[$key];

                    // Tách chuỗi bằng dấu "-"
                    $parts = explode('-', $value);

                    // Bảo vệ: nếu không đủ 2 phần thì bỏ qua
                    if ( count($parts) === 2 ) {
                        $male_titles[] = trim($parts[0]);
                        $female_titles[] = trim($parts[1]);
                    }
                }
            }

        } else {
            // Mảng xưng hô cho giới tính nam và nữ
            $male_titles = ITHANDECH_MALE_TITLES;
            $female_titles = ITHANDECH_FEMALE_TITLES;
        }

        // HTML cho các radio buttons
        $gender_html = '<div class="gender-radio-container">';

        $gender_html .= '
        <div class="ithan__btn-wrapper"><button type="button" id="change-title-of-' . $prefix_field . '-gender-button-prevous" class="ithanpopup__gender-change-title" aria-label="Change title of gender">
            <i><svg class="icon"><use href="#previous-to"></use></svg></i>
        </button></div>';
        
        // Tạo radio button cho Nam và Nữ, hiển thị xưng hô ban đầu (Ông và Bà)
        $gender_html .= '<div class="gender-radio-wrapper" id="' . $prefix_field . '-male-gender-radio">';
        $gender_html .= '<input type="radio" class="gender__radio" name="' . $prefix_field . '_gender" value="ông" id="' . $prefix_field . '_gender_male" checked />';
        $gender_html .= '<label for="' . $prefix_field . '_gender_male" class="gender-label"><span></span>' . __( 'Ông', 'ithan-devvn-checkout-for-woocommerce' ) . '</label>';
        $gender_html .= '</div>';
        
        $gender_html .= '<div class="gender-radio-wrapper" id="' . $prefix_field . '-female-gender-radio">';
        $gender_html .= '<input type="radio" class="gender__radio" name="' . $prefix_field . '_gender" value="bà" id="' . $prefix_field . '_gender_female" />';
        $gender_html .= '<label for="' . $prefix_field . '_gender_female" class="gender-label"><span></span>' . __( 'Bà', 'ithan-devvn-checkout-for-woocommerce' ) . '</label>';
        $gender_html .= '</div>';

        // Tạo các input ẩn chứa giá trị xưng hô cho nam và nữ
        $gender_html .= '<input type="hidden" id="' . $prefix_field . '_hidden_male_titles" value="' . implode(',', $male_titles) . '" />';
        $gender_html .= '<input type="hidden" id="' . $prefix_field . '_hidden_female_titles" value="' . implode(',', $female_titles) . '" />';
        $gender_html .= '<input type="hidden" id="' . $prefix_field . '_current_male_index" value="0" />';
        $gender_html .= '<input type="hidden" id="' . $prefix_field . '_current_female_index" value="0" />';

        $gender_html .= '
        <div class="ithan__btn-wrapper"><button type="button" id="change-title-of-' . $prefix_field . '-gender-button-next" class="ithanpopup__gender-change-title" aria-label="Change title of gender">
            <i><svg class="icon"><use href="#next-to"></use></svg></i>
        </button></div>';
        
        $gender_html .= '</div>';

        // Thêm vào field
        $field = $gender_html;
    }

    return $field;
}

add_filter('woocommerce_checkout_fields', 'ithandech_custom_order_address_fields');
function ithandech_custom_order_address_fields( $fields ) : array {
    $default_province_code = '';
    $default_district_code = '';

    global $ithandech_tinh_thanhpho;
    // Lấy mảng quận/huyện dựa trên $province_code
    $districts = ithandech_devvn_vietnam_shipping()->ithandech_get_list_district_select( $default_province_code );

    // Lấy mảng xã/phường dựa trên $district_code
    $wards = ithandech_devvn_vietnam_shipping()->ithandech_get_list_village_select( $default_district_code );

    // Tùy chỉnh trường billing_first_name thành hidden
    if ( isset( $fields['billing']['billing_first_name'] ) ) {
        // Nếu muốn vẫn yêu cầu (và gán một giá trị mặc định) thì giữ required = true
        // Nếu không cần thiết phải bắt buộc nhập, bạn có thể chuyển required = false
        $fields['billing']['billing_first_name']['type']    = 'hidden';
        $fields['billing']['billing_first_name']['default'] = 'Ông'; // hoặc giá trị rỗng nếu cần
        // Bạn có thể bỏ label nếu muốn (vì trường hidden sẽ không hiển thị)
        $fields['billing']['billing_first_name']['label']   = '';
    }

    if ( isset( $fields['shipping']['shipping_first_name'] ) ) {
        $fields['shipping']['shipping_first_name']['type']    = 'hidden';
        $fields['shipping']['shipping_first_name']['default'] = 'Ông'; // hoặc giá trị rỗng nếu cần
        $fields['shipping']['shipping_first_name']['label']   = '';
    }

    if ( isset( $fields['billing']['billing_last_name'] ) ) {
        $fields['billing']['billing_last_name']['default'] = '';
        $fields['billing']['billing_last_name']['label']   = 'Họ và tên';

        $fields['billing']['billing_last_name']['required'] = true;
        // Thêm class hoặc id
        $fields['billing']['billing_last_name']['class'][]  = 'validate-last-name';
        $fields['billing']['billing_last_name']['id']       = 'billing_last_name';

        // Thêm placeholder
        $fields['billing']['billing_last_name']['placeholder'] = 'Nhập họ và tên';
    }

    if ( isset( $fields['shipping']['shipping_last_name'] ) ) {
        $fields['shipping']['shipping_last_name']['default'] = '';
        $fields['shipping']['shipping_last_name']['label']   = 'Họ và tên';

        $fields['shipping']['shipping_last_name']['required'] = true;
        // Thêm class hoặc id
        $fields['shipping']['shipping_last_name']['class'][]  = 'validate-last-name';
        $fields['shipping']['shipping_last_name']['id']       = 'shipping_last_name';

        // Thêm placeholder
        $fields['shipping']['shipping_last_name']['placeholder'] = 'Nhập họ và tên';
    }

    if ( isset( $fields['billing']['billing_country'] ) ) {
        $fields['billing']['billing_country']['default'] = 'VN'; // hoặc giá trị rỗng nếu cần
        $fields['billing']['billing_country']['type']    = 'hidden';
    }

    if ( isset( $fields['shipping']['shipping_country'] ) ) {
        $fields['shipping']['shipping_country']['default'] = 'VN'; // hoặc giá trị rỗng nếu cần
        $fields['shipping']['shipping_country']['type']    = 'hidden';
    }

    // start: trường dùng xác định phương thức vận chuyển
    if ( isset( $fields['billing']['billing_state'] ) ) {
        $fields['billing']['billing_state']['required'] = true;
        $fields['billing']['billing_state']['priority']  = 43;

        // Thêm class hoặc id
        $fields['billing']['billing_state']['type']    = 'hidden';
        $fields['billing']['billing_state']['id']       = 'billing_state';
    }

    if ( isset( $fields['shipping']['shipping_state'] ) ) {
        $fields['shipping']['shipping_state']['required'] = true;
        $fields['shipping']['shipping_state']['priority']  = 43;

        // Thêm class hoặc id
        $fields['shipping']['shipping_state']['type']    = 'hidden';
        $fields['shipping']['shipping_state']['id']      = 'shipping_state';
    }
    // end: trường dùng xác định phương thức vận chuyển

    if ( isset( $fields['billing']['billing_province_code'] ) ) {
        $fields['billing']['billing_province_code']['required'] = true;
        // Loại field = state -> WooCommerce sẽ dùng mảng ở trên
        $fields['billing']['billing_province_code']['options']  = $ithandech_tinh_thanhpho;
        $fields['billing']['billing_province_code']['priority']  = 42;

        // Thêm class hoặc id
        $fields['billing']['billing_province_code']['class'][]  = 'address__billing_state_select';
        $fields['billing']['billing_province_code']['id']       = 'address-billing_state';

    }
    // Nếu chưa cấu hình bằng Checkout Field Editor Plugin
    if ( ! isset( $fields['billing']['billing_province_code'] ) ) {
        $fields['billing']['billing_province_code'] = array(
            'type'        => 'select',
            'label'       => __( 'Tỉnh/Thành', 'ithan-devvn-checkout-for-woocommerce' ),
            'placeholder' => __( 'Tỉnh/Thành Phố', 'ithan-devvn-checkout-for-woocommerce' ),
            'default'     => '',
            'required'    => true,
            'class'       => array( 'form-row-wide', 'address__billing_state_select' ),
            'clear'       => true,
            'priority'    => 42,
            'id'          => 'address-billing_state',
            'options'     => $ithandech_tinh_thanhpho,
        );
    }

    if ( isset( $fields['shipping']['shipping_province_code'] ) ) {
        $fields['shipping']['shipping_province_code']['required'] = true;
        // Loại field = state -> WooCommerce sẽ dùng mảng ở trên
        $fields['shipping']['shipping_province_code']['options']  = $ithandech_tinh_thanhpho;
        $fields['shipping']['shipping_province_code']['priority']  = 42;

        // Thêm class hoặc id
        $fields['shipping']['shipping_province_code']['class'][]  = 'address__shipping_state_select';
        $fields['shipping']['shipping_province_code']['id']       = 'address-shipping_state';
    }

    // Nếu chưa cấu hình bằng Checkout Field Editor Plugin
    if ( ! isset( $fields['shipping']['shipping_province_code'] ) ) {
        $fields['shipping']['shipping_province_code'] = array(
            'type'        => 'select',
            'label'       => __( 'Tỉnh/Thành', 'ithan-devvn-checkout-for-woocommerce' ),
            'placeholder' => __( 'Tỉnh/Thành Phố', 'ithan-devvn-checkout-for-woocommerce' ),
            'default'     => '',
            'required'    => true,
            'class'       => array( 'form-row-wide', 'address__shipping_state_select' ),
            'clear'       => true,
            'priority'    => 42,
            'id'          => 'address-shipping_state',
            'options'     => $ithandech_tinh_thanhpho,
        );
    }

    if ( isset( $fields['billing']['billing_district_code'] ) ) {
        $fields['billing']['billing_district_code']['type']      = 'select';
        $fields['billing']['billing_district_code']['options']   = $districts;
        $fields['billing']['billing_district_code']['priority']   = 43;

        $fields['billing']['billing_district_code']['class'][]  = 'address__billing_city_select';
        $fields['billing']['billing_district_code']['id']       = 'address-billing_city';
    }

    // Nếu chưa cấu hình bằng Checkout Field Editor Plugin
    if ( ! isset( $fields['billing']['billing_district_code'] ) ) {
        $fields['billing']['billing_district_code'] = array(
            'type'        => 'select',
            'label'       => __( 'Quận/Huyện/t.x', 'ithan-devvn-checkout-for-woocommerce' ),
            'placeholder' => __( 'Quận/Huyện/t.x', 'ithan-devvn-checkout-for-woocommerce' ),
            'default'     => '',
            'required'    => true,
            'class'       => array( 'form-row-wide', 'address__billing_city_select' ),
            'clear'       => true,
            'priority'    => 43,
            'id'          => 'address-billing_city',
            'options'     => $districts,
        );
    }

    if ( isset( $fields['shipping']['shipping_district_code'] ) ) {
        $fields['shipping']['shipping_district_code']['type']      = 'select';
        $fields['shipping']['shipping_district_code']['options']   = $districts;
        $fields['shipping']['shipping_district_code']['priority']   = 43;

        $fields['shipping']['shipping_district_code']['class'][]  = 'address__shipping_city_select';
        $fields['shipping']['shipping_district_code']['id']       = 'address-shipping_city';
    }

    // Nếu chưa cấu hình bằng Checkout Field Editor Plugin
    if ( ! isset( $fields['shipping']['shipping_district_code'] ) ) {
        $fields['shipping']['shipping_district_code'] = array(
            'type'        => 'select',
            'label'       => __( 'Quận/Huyện/t.x', 'ithan-devvn-checkout-for-woocommerce' ),
            'placeholder' => __( 'Quận/Huyện/t.x', 'ithan-devvn-checkout-for-woocommerce' ),
            'default'     => '',
            'required'    => true,
            'class'       => array( 'form-row-wide', 'address__shipping_city_select' ),
            'clear'       => true,
            'priority'    => 43,
            'id'          => 'address-shipping_city',
            'options'     => $districts,
        );
    }

    if ( isset( $fields['billing']['billing_ward_code'] ) ) {
        $fields['billing']['billing_ward_code']['type']      = 'select';
        $fields['billing']['billing_ward_code']['options']   = $wards;
        $fields['billing']['billing_ward_code']['priority']   = 44;

        $fields['billing']['billing_ward_code']['class'][]  = 'address__billing_ward_select';
        $fields['billing']['billing_ward_code']['id']       = 'address-billing_ward';
    }

    // Nếu chưa cấu hình bằng Checkout Field Editor Plugin
    if ( ! isset( $fields['billing']['billing_ward_code'] ) ) {
        $fields['billing']['billing_ward_code'] = array(
            'type'        => 'select',
            'label'       => __( 'Xã/Phường', 'ithan-devvn-checkout-for-woocommerce' ),
            'placeholder' => __( 'Xã/Phường', 'ithan-devvn-checkout-for-woocommerce' ),
            'default'     => '',
            'required'    => true,
            'class'       => array( 'form-row-wide', 'address__billing_ward_select' ),
            'clear'       => true,
            'priority'    => 44,
            'id'          => 'address-billing_ward',
            'options'     => $wards,
        );
    }

    if ( isset( $fields['shipping']['shipping_ward_code'] ) ) {
        $fields['shipping']['shipping_ward_code']['type']      = 'select';
        $fields['shipping']['shipping_ward_code']['options']   = $wards;
        $fields['shipping']['shipping_ward_code']['priority']   = 44;

        $fields['shipping']['shipping_ward_code']['class'][]  = 'address__shipping_ward_select';
        $fields['shipping']['shipping_ward_code']['id']       = 'address-shipping_ward';
    }

    // Nếu chưa cấu hình bằng Checkout Field Editor Plugin
    if ( ! isset( $fields['shipping']['shipping_ward_code'] ) ) {
        $fields['shipping']['shipping_ward_code'] = array(
            'type'        => 'select',
            'label'       => __( 'Xã/Phường', 'ithan-devvn-checkout-for-woocommerce' ),
            'placeholder' => __( 'Xã/Phường', 'ithan-devvn-checkout-for-woocommerce' ),
            'default'     => '',
            'required'    => true,
            'class'       => array( 'form-row-wide', 'address__shipping_ward_select' ),
            'clear'       => true,
            'priority'    => 44,
            'id'          => 'address-shipping_ward',
            'options'     => $wards,
        );
    }

    // Bỏ field ra khỏi form
    unset($fields['billing']['billing_city']);
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_address_1']);

    unset($fields['shipping']['shipping_city']);
    unset($fields['shipping']['shipping_company']);
    unset($fields['shipping']['shipping_postcode']);
    unset($fields['shipping']['shipping_address_1']);

    return $fields;
}

// 2. Lưu tự động các custom billing_* vào order meta
add_action( 'woocommerce_checkout_update_order_meta', 'ithandech_save_custom_address_fields_to_order' );
function ithandech_save_custom_address_fields_to_order( $order_id ) {
    // 0. Kiểm tra nonce của WooCommerce Checkout
    if ( empty( $_POST['woocommerce-process-checkout-nonce'] )
      || ! wp_verify_nonce(
            sanitize_text_field( wp_unslash( $_POST['woocommerce-process-checkout-nonce'] ) ),
            'woocommerce-process_checkout'
        )
    ) {
        return;
    }

    // 1. Danh sách field bạn muốn lưu
    $fields = array(
        'billing_province_code',
        'billing_district_code',
        'billing_ward_code',
        'shipping_province_code',
        'shipping_district_code',
        'shipping_ward_code',
    );

    // 2. Lặp và lưu vào order meta (_billing_xxx / _shipping_xxx)
    foreach ( $fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            $value    = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
            $meta_key = '_' . $field;
            update_post_meta( $order_id, $meta_key, $value );
        }
    }
}

add_action( 'woocommerce_checkout_update_user_meta', 'ithandech_save_custom_address_fields_to_user' );
function ithandech_save_custom_address_fields_to_user( $customer_id ) {
    // 0. Kiểm tra nonce của WooCommerce Checkout
    if ( empty( $_POST['woocommerce-process-checkout-nonce'] )
      || ! wp_verify_nonce(
            sanitize_text_field( wp_unslash( $_POST['woocommerce-process-checkout-nonce'] ) ),
            'woocommerce-process_checkout'
        )
    ) {
        return;
    }

    // 1. Danh sách field bạn muốn lưu
    $fields = array(
        'billing_province_code',
        'billing_district_code',
        'billing_ward_code',
        'shipping_province_code',
        'shipping_district_code',
        'shipping_ward_code',
        // … nếu cần thêm cứ bổ sung vào đây
    );

    // 2. Lặp và lưu vào user meta
    foreach ( $fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            $value = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
            update_user_meta( $customer_id, $field, $value );
        }
    }
}

add_action('wp_head', 'ithandech_load_svg_icons_conditionally');
function ithandech_load_svg_icons_conditionally(): void
{
    echo '<svg style="display: none;">
            <symbol id="user" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </symbol>
            <symbol id="phone" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 4.5a.75.75 0 01.75-.75h2.457c.72 0 1.362.486 1.543 1.184l.53 2.118a.75.75 0 01-.216.67l-1.305 1.305a11.95 11.95 0 006.198 6.198l1.305-1.305a.75.75 0 01.67-.216l2.118.53c.698.181 1.184.823 1.184 1.543v2.457a.75.75 0 01-.75.75h-2.25C9.163 21 3 14.837 3 7.5V4.5z" />
            </symbol>
            <symbol id="envelope" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125A2.25 2.25 0 014.5 4.875h15a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25v-9z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M22.5 7.125l-9.008 5.337a.75.75 0 01-.684 0L3 7.125" />
            </symbol>
            <symbol id="zipcode" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none">
                <!-- Hộp chữ nhật với các góc bo tròn -->
                <rect x="3" y="4" width="18" height="16" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round"></rect>
                <!-- Các đường ngang biểu thị nội dung (ví dụ: mã ZIP) -->
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 9h10M7 13h4"></path>
            </symbol>
            <symbol id="location" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" 
                    d="M12 2.25c-3.724 0-6.75 2.993-6.75 6.682 0 4.188 3.338 7.7 6.034 10.577a1.75 1.75 0 0 0 2.432 0c2.696-2.877 6.034-6.389 6.034-10.577 0-3.689-3.026-6.682-6.75-6.682z"/>
                <path stroke-linecap="round" stroke-linejoin="round" 
                    d="M12 11.25a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5z"/>
            </symbol>
            <!-- Icon Exclamation -->
            <symbol id="exclamation" viewBox="0 0 16 16" fill="currentColor">
                <path d="M8 0a8 8 0 1 0 8 8A8 8 0 0 0 8 0zm0 1a7 7 0 1 1 0 14A7 7 0 0 1 8 1zm-.5 4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5V5a.5.5 0 0 1 .5-.5h1zm0 7a.5.5 0 0 1 .5.5h1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5h1z"/>
            </symbol>
            <!-- Icon Close (X) -->
            <symbol id="icon--close" viewBox="0 0 16 16" fill="currentColor">
                <path d="M11.742 4.258a1 1 0 1 0-1.484-1.328L8 6.716 5.742 3.93a1 1 0 1 0-1.484 1.328L6.716 8l-3.458 4.742a1 1 0 0 0 1.484 1.328L8 9.284l2.258 2.886a1 1 0 1 0 1.484-1.328L9.284 8l3.458-4.742z"/>
            </symbol>

            <symbol id="icon--error" fill="currentColor" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
            </symbol>

            <symbol id="gender" viewBox="0 0 32 32" fill="currentColor">
                <path d="M24.4992 2.04903C24.5122 1.64897 24.366 1.2447 24.0607 0.93934C23.7553 0.633976 23.351 0.487795 22.951 0.500796C22.934 0.500267 22.9171 0.5 22.9 0.5H19C18.1716 0.5 17.5 1.17157 17.5 2C17.5 2.82843 18.1716 3.5 19 3.5H19.3787L17.0479 5.83074C16.1547 5.3029 15.1127 5 14 5C13.2038 5 12.4439 5.15506 11.7488 5.43662C12.4714 6.18313 13.0498 7.06996 13.4394 8.05228C13.621 8.01796 13.8084 8 14 8C15.6568 8 17 9.34315 17 11C17 12.6569 15.6568 14 14 14C13.8084 14 13.621 13.982 13.4394 13.9477C13.0498 14.93 12.4714 15.8169 11.7488 16.5634C12.4439 16.8449 13.2038 17 14 17C17.3137 17 20 14.3137 20 11C20 9.8873 19.6971 8.84534 19.1692 7.95207L21.5 5.62132V6C21.5 6.82843 22.1716 7.5 23 7.5C23.8284 7.5 24.5 6.82843 24.5 6V2.1C24.5 2.08295 24.4997 2.06595 24.4992 2.04903Z" fill="#758CA3"/>
                <path clip-rule="evenodd" d="M12 11C12 13.7958 10.0878 16.1449 7.5 16.811V18.5H8C8.82843 18.5 9.5 19.1716 9.5 20C9.5 20.8284 8.82843 21.5 8 21.5H7.5V22.5C7.5 23.3284 6.82843 24 6 24C5.17157 24 4.5 23.3284 4.5 22.5V21.5H4C3.17157 21.5 2.5 20.8284 2.5 20C2.5 19.1716 3.17157 18.5 4 18.5H4.5V16.811C1.91216 16.1449 0 13.7958 0 11C0 7.68631 2.68629 5.00002 6 5.00002C9.31371 5.00002 12 7.68631 12 11ZM9 11C9 12.6569 7.65685 14 6 14C4.34315 14 3 12.6569 3 11C3 9.34316 4.34315 8.00002 6 8.00002C7.65685 8.00002 9 9.34316 9 11Z" fill="#758CA3" fill-rule="evenodd"/>
            </symbol>

            <symbol id="icon-male" viewBox="0 0 32 32" fill="currentColor">
                <path clip-rule="evenodd" fill-rule="evenodd" d="M21.0607 3.93934C21.366 4.2447 21.5122 4.64897 21.4992 5.04903C21.4997 5.06595 21.5 5.08295 21.5 5.1V9C21.5 9.82843 20.8284 10.5 20 10.5C19.1716 10.5 18.5 9.82843 18.5 9V8.62132L16.1693 10.952C16.6971 11.8453 17 12.8873 17 14C17 17.3137 14.3137 20 11 20C7.68629 20 5 17.3137 5 14C5 10.6863 7.68629 8 11 8C12.1127 8 13.1547 8.30289 14.048 8.83073L16.3787 6.5H16C15.1716 6.5 14.5 5.82843 14.5 5C14.5 4.17157 15.1716 3.5 16 3.5H19.9C19.9171 3.5 19.934 3.50027 19.951 3.5008C20.351 3.48779 20.7553 3.63398 21.0607 3.93934ZM14 14C14 15.6569 12.6569 17 11 17C9.34315 17 8 15.6569 8 14C8 12.3431 9.34315 11 11 11C12.6569 11 14 12.3431 14 14Z" fill="currentColor"/>
            </symbol>

            <symbol id="icon-female" viewBox="0 0 32 32" fill="currentColor">
                <path clip-rule="evenodd" fill-rule="evenodd" d="M18 9C18 11.7958 16.0878 14.1449 13.5 14.811V16.5H14C14.8284 16.5 15.5 17.1716 15.5 18C15.5 18.8284 14.8284 19.5 14 19.5H13.5V20.5C13.5 21.3284 12.8284 22 12 22C11.1716 22 10.5 21.3284 10.5 20.5V19.5H10C9.17157 19.5 8.5 18.8284 8.5 18C8.5 17.1716 9.17157 16.5 10 16.5H10.5V14.811C7.91216 14.1449 6 11.7958 6 9C6 5.68629 8.68629 3 12 3C15.3137 3 18 5.68629 18 9ZM12 12C13.6569 12 15 10.6569 15 9C15 7.34315 13.6569 6 12 6C10.3431 6 9 7.34315 9 9C9 10.6569 10.3431 12 12 12Z" fill="currentColor"/>
            </symbol>


            <symbol id="market-cart" viewBox="0 0 495.401 495.401" stroke-width="1.5" stroke="currentColor" fill="currentColor">
                <g>
                    <path d="M185.049,381.529c-22.852,0-41.379,18.517-41.379,41.36c0,22.861,18.527,41.379,41.379,41.379
                    c22.843,0,41.37-18.518,41.37-41.379C226.419,400.048,207.892,381.529,185.049,381.529z"/>
                    <path d="M365.622,381.529c-22.861,0-41.379,18.517-41.379,41.36c0,22.861,18.518,41.379,41.379,41.379
                    c22.844,0,41.38-18.518,41.38-41.379C407.002,400.048,388.466,381.529,365.622,381.529z"/>
                    <path d="M469.558,154.735l-229.192-0.019c-11.46,0-20.75,9.29-20.75,20.75s9.29,20.75,20.75,20.75
                    l202.778-0.01l-12.864,43.533l-206.164,0.044c-10.631,0-19.25,8.619-19.25,19.25c0,10.632,8.619,19.25,19.25,19.25l194.768,0.076
                    l-12.093,40.715H174.455L159.04,196.188L144.321,76.471c-1.198-9.473-8.066-17.251-17.319-19.611l-98-25
                    C16.56,28.684,3.901,36.199,0.727,48.641s4.339,25.102,16.781,28.275l82.667,21.089l32.192,241.591c0,0,1.095,28.183,26.69,28.183
                    h256.81c21.518,0,25.678-22.438,25.678-22.438l50.896-151.159C492.441,194.162,507.532,154.735,469.558,154.735z"/>
                </g>
            </symbol>

            <symbol id="next-to" viewBox="0 0 257.392 257.392" enable-background="new 0 0 257.392 257.392" stroke-width="1.5" stroke="currentColor" fill="currentColor">
                <linearGradient id="next-to-linear" x1="0%" y1="50%" x2="100%" y2="0%">
                    <stop offset="0%"   stop-color="#00f"/>
                    <stop offset="100%" stop-color="#f00"/>
                </linearGradient>
                    
                <circle id="next-to-first" fill="none" stroke="currentColor" stroke-width="10" stroke-miterlimit="10" cx="128.914" cy="128.914" r="119.901"/>
                <circle id="next-to-second" fill="none" stroke="url(#next-to-linear)" stroke-width="10" stroke-miterlimit="10" cx="128.914" cy="128.914" r="119.901"/>
                <path fill="currentColor" d="M152.053,80.839c-3.013-2.844-7.761-2.706-10.602,0.307c-2.843,3.013-2.706,7.759,0.307,10.602
                    l31.688,29.904H61.609c-4.142,0-7.5,3.357-7.5,7.5s3.358,7.5,7.5,7.5h112.776l-32.627,30.789c-3.013,2.843-3.15,7.59-0.307,10.603
                    c1.476,1.563,3.463,2.353,5.457,2.353c1.846,0,3.697-0.678,5.146-2.045l51.666-48.756L152.053,80.839z"/>
            </symbol>

            <symbol id="previous-to" viewBox="0 0 257.392 257.392" enable-background="new 0 0 257.392 257.392" stroke-width="1.5" stroke="currentColor" fill="currentColor">
                <linearGradient id="previous-to-linear" x1="100%" y1="50%" x2="0%" y2="0%">
                    <stop offset="0%"   stop-color="#f00"/>
                    <stop offset="100%" stop-color="#00f"/>
                </linearGradient>
                
                <circle id="previous-to-first" fill="none" stroke="currentColor" stroke-width="10" stroke-miterlimit="10" cx="128.914" cy="128.914" r="119.901"/>
                <circle id="previous-to-second" fill="none" stroke="url(#previous-to-linear)" stroke-width="10" stroke-miterlimit="10" cx="128.914" cy="128.914" r="119.901"/>
                <path fill="currentColor" d="M105.339,80.839c3.013-2.844,7.761-2.706,10.602,0.307c2.843,3.013,2.706,7.759-0.307,10.602
                    l-31.688,29.904H195.791c4.142,0,7.5,3.357,7.5,7.5s-3.358,7.5-7.5,7.5H80.015l32.627,30.789c3.013,2.843,3.15,7.59,0.307,10.603
                    c-1.476,1.563-3.463,2.353-5.457,2.353c-1.846,0-3.697-0.678-5.146-2.045l-51.666-48.756L105.339,80.839z"/>
            </symbol>

            <symbol id="coupon" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="currentColor">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M3.75 6.75L4.5 6H20.25L21 6.75V10.7812H20.25C19.5769 10.7812 19.0312 11.3269 19.0312 12C19.0312 12.6731 19.5769 13.2188 20.25 13.2188H21V17.25L20.25 18L4.5 18L3.75 17.25V13.2188H4.5C5.1731 13.2188 5.71875 12.6731 5.71875 12C5.71875 11.3269 5.1731 10.7812 4.5 10.7812H3.75V6.75ZM5.25 7.5V9.38602C6.38677 9.71157 7.21875 10.7586 7.21875 12C7.21875 13.2414 6.38677 14.2884 5.25 14.614V16.5L9 16.5L9 7.5H5.25ZM10.5 7.5V16.5L19.5 16.5V14.614C18.3632 14.2884 17.5312 13.2414 17.5312 12C17.5312 10.7586 18.3632 9.71157 19.5 9.38602V7.5H10.5Z" fill="#080341"/>
            </symbol>

            <symbol id="download-btn" width="15" height="15" viewBox="0 0 15 15" fill="currentColor">
				<path d="M7.50005 1.04999C7.74858 1.04999 7.95005 1.25146 7.95005 1.49999V8.41359L10.1819 6.18179C10.3576 6.00605 10.6425 6.00605 10.8182 6.18179C10.994 6.35753 10.994 6.64245 10.8182 6.81819L7.81825 9.81819C7.64251 9.99392 7.35759 9.99392 7.18185 9.81819L4.18185 6.81819C4.00611 6.64245 4.00611 6.35753 4.18185 6.18179C4.35759 6.00605 4.64251 6.00605 4.81825 6.18179L7.05005 8.41359V1.49999C7.05005 1.25146 7.25152 1.04999 7.50005 1.04999ZM2.5 10C2.77614 10 3 10.2239 3 10.5V12C3 12.5539 3.44565 13 3.99635 13H11.0012C11.5529 13 12 12.5528 12 12V10.5C12 10.2239 12.2239 10 12.5 10C12.7761 10 13 10.2239 13 10.5V12C13 13.1041 12.1062 14 11.0012 14H3.99635C2.89019 14 2 13.103 2 12V10.5C2 10.2239 2.22386 10 2.5 10Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path>
			</symbol>


        </svg>';
}

add_filter( 'woocommerce_form_field', 'ithandech_customize_fields_with_icon', 10, 4 );
function ithandech_customize_fields_with_icon( $field, $key, $args, $value ) {

    // Danh sách các trường cần tùy biến (và biểu tượng SVG tương ứng)
    $icon_map = [
        'billing_last_name' => 'user',
        'billing_phone'     => 'phone',
        'billing_email'     => 'envelope',
        'billing_postcode'  => 'zipcode',
        'billing_address_2' => 'location',

        'shipping_last_name' => 'user',
        'shipping_phone'     => 'phone',
        'shipping_postcode'  => 'zipcode',
        'shipping_address_2' => 'location',
    ];

    // Kiểm tra xem $key có nằm trong danh sách cần tùy biến không
    if ( ! array_key_exists( $key, $icon_map ) ) {
        return $field; // Không tùy biến, trả về HTML gốc
    }

    // Kiểm tra type (text, tel, email...). Nếu khác, có thể return gốc hoặc tùy biến tiếp
    if ( ! in_array( $args['type'], [ 'text', 'tel', 'email' ], true ) ) {
        return $field;
    }

    // Lấy icon ID từ mảng
    $icon_id = $icon_map[ $key ];

    // Xử lý label (có dấu sao nếu required)
    $label_html = '';
    if ( ! empty( $args['label'] ) ) {
        $label_text = $args['label'];
        if ( ! empty( $args['required'] ) ) {
            $label_text .= ' <span class="required">*</span>';
        }
        $label_html = sprintf(
            '<label for="%s" class="checkout-label tooltip__jump display__none">%s</label>',
            esc_attr( $args['id'] ),
            wp_kses_post( $label_text )
        );
    }

    $input_html = '<input type="' . esc_attr($args['type']) . '" 
    class="input ' . (isset($args['input_class']) ? esc_attr(join(' ', (array) $args['input_class'])) : '') . '" 
    name="' . esc_attr($key) . '" 
    id="' . esc_attr($args['id']) . '" 
    placeholder="' . esc_attr($args['placeholder']) . '" 
    value="' . esc_attr($value) . '"';

    if (!empty($args['custom_attributes']) && is_array($args['custom_attributes'])) {
        foreach ($args['custom_attributes'] as $attr => $val) {
            $input_html .= ' ' . esc_attr($attr) . '="' . esc_attr($val) . '"';
        }
    }

    $input_html .= ' />';

    // Mô tả (nếu có)
    $description_html = '';
    if ( ! empty( $args['description'] ) ) {
        $description_html = '<span class="description">' . wp_kses_post( $args['description'] ) . '</span>';
    }

    // Tạo class wrapper theo tên field (để CSS riêng từng loại nếu muốn)
    // Ví dụ: billing_last_name -> "last_name-wrapper"
    $wrapper_class = str_replace( 'billing_', '', $key ) . '-wrapper';

    /**
     * 1) Kiểm tra trường có required không?
     * 2) Nếu có lỗi từ WooCommerce (trong $args['errors']), thì hiển thị lỗi đó.
     * 3) Nếu không có lỗi nhưng vẫn là trường required, bạn có thể hiển thị
     *    một tooltip mặc định, hoặc để trống tuỳ ý.
     */
    $error_html = ithandech_get_error_tooltip_html($args);

    // Xây dựng HTML cuối
    return sprintf(
        '<div class="wc__form-field has__tooltips">
            %s
            <div class="input-container">
                <div class="input-wrapper %s">
                    <span>
                        <svg class="icon"><use href="#%s"></use></svg>
                    </span>
                    %s
                </div>
                %s <!-- Chèn tooltip lỗi (nếu có) vào đây -->
            </div>
            %s
        </div>',
        $label_html,
        esc_attr( $wrapper_class ),
        esc_attr( $icon_id ),
        $input_html,
        $error_html,
        $description_html
    );
}


/**
 * @param $args
 * @return string
 */
function ithandech_get_error_tooltip_html($args): string
{
    $error_html = '';
    if (!empty($args['required'])) {
        // Nếu WooCommerce phát hiện lỗi, nó sẽ lưu vào $args['errors'] (mảng)
        if (!empty($args['errors']) && is_array($args['errors'])) {
            // Nếu có nhiều lỗi, gộp chúng lại (hoặc bạn có thể hiển thị từng lỗi)
            $error_message = implode(', ', $args['errors']);
            $error_html = '<span class="error">' . esc_html($error_message) . '</span>';
        } else {
            // Mặc định (chưa có lỗi gì hoặc chưa submit form), vẫn có thể hiển thị tooltip
            // hoặc bạn để trống nếu chỉ muốn hiển thị lỗi khi thật sự có lỗi.
            $error_html = '<span class="error">Bắt buộc</span>';
        }
    }
    return $error_html;
}

add_filter( 'woocommerce_form_field', 'ithandech_customize_location_fields', 10, 4 );
function ithandech_customize_location_fields( $field, $key, $args, $value ) {
    // Danh sách các trường select muốn tùy biến
    $location_fields = array( 
        'billing_province_code', 'billing_district_code', 'billing_ward_code',
        'shipping_province_code', 'shipping_district_code', 'shipping_ward_code',
    );

    // Nếu $key không nằm trong mảng, hoặc type != select, trả về HTML gốc
    if ( ! in_array( $key, $location_fields, true ) || 'select' !== $args['type'] ) {
        return $field;
    }

    // === 1) Tạo label (nếu có) ===
    $label_html = '';
    if ( ! empty( $args['label'] ) ) {
        // Nếu field required, thêm dấu *
        $label_text = $args['label'];
        if ( ! empty( $args['required'] ) ) {
            $label_text .= ' <span class="required">*</span>';
        }
        $label_html = sprintf(
            '<label for="%s" class="checkout-label location__tooltip-jump">%s</label>',
            esc_attr( $args['id'] ),
            wp_kses_post( $label_text )
        );
    }

    // === 2) Tạo HTML cho <select> ===
    $options_html = '';

    if (!empty($args['options']) && is_array($args['options'])) {
        foreach ($args['options'] as $option_value => $option_label) {
            // Xác định thuộc tính "selected" nếu giá trị khớp
            $selected = selected($value, $option_value, false);

            // Nối chuỗi thủ công để tránh lỗi
            $options_html .= '<option value="' . esc_attr($option_value) . '" ' . $selected . '>'
                . esc_html($option_label) . '</option>';
        }
    }

    // Kiểm tra nếu trường required để thêm aria-required="true"
    $required_attr = ! empty( $args['required'] ) ? 'aria-required=true' : '';

    $select_html = '<select name="' . esc_attr($key) . '" 
    id="' . esc_attr($args['id']) . '" 
    class="' . esc_attr(trim(implode(' ', (array) $args['input_class']))) . '"';

    if (!empty($required_attr)) {
        $select_html .= ' ' . esc_attr($required_attr);
    }

    $select_html .= '>' . $options_html . '</select>';

    // === 3) Mô tả (nếu có) ===
    $description_html = '';
    if ( ! empty( $args['description'] ) ) {
        $description_html = '<span class="description">' . wp_kses_post( $args['description'] ) . '</span>';
    }

    // === 4) Xử lý tooltip lỗi cho trường required ===
    $error_html = ithandech_get_error_tooltip_html($args);

    // === 5) Gói <select> trong .input-wrapper, kèm icon location và tooltip lỗi ===
    return sprintf(
        '<div class="wc__form-field has__tooltips">
            %s
            <div class="input-container">
                <div class="input-wrapper location-wrapper">
                    <span>
                        <svg class="icon"><use href="#location"></use></svg>
                    </span>
                    %s
                </div>
                %s
            </div>
            %s
        </div>
        ',
        $label_html,
        $select_html,
        $error_html,
        $description_html
    );
}

add_action('woocommerce_checkout_process', 'ithandech_buy_now_custom_validate_checkout_fields');
function ithandech_buy_now_custom_validate_checkout_fields(): void
{
    // Kiểm tra nonce của WooCommerce
    // Lấy và xử lý nonce: loại bỏ escape ký tự và sanitize
    $nonce = isset( $_POST['woocommerce-process-checkout-nonce'] )
        ? sanitize_text_field( wp_unslash( $_POST['woocommerce-process-checkout-nonce'] ) )
        : '';

    if ( ! wp_verify_nonce( $nonce, 'woocommerce-process_checkout' ) ) {
        wc_add_notice( __( 'Yêu cầu không hợp lệ. Vui lòng thử lại.', 'ithan-devvn-checkout-for-woocommerce' ), 'error' );
        return;
    }

    if ( empty( $_POST['billing_province_code'] ) ) {
        wc_add_notice( __( 'Cung cấp địa chỉ tỉnh/thành phố.', 'ithan-devvn-checkout-for-woocommerce' ), 'error' );
    }
    if ( empty( $_POST['billing_district_code'] ) ) {
        wc_add_notice( __( 'Cung cấp địa chỉ quận/huyện/thị xã...', 'ithan-devvn-checkout-for-woocommerce' ), 'error' );
    }
}

add_action('woocommerce_checkout_create_order', 'ithandech_add_checkout_meta_to_order', 10, 2);
function ithandech_add_checkout_meta_to_order($order, $data): void
{
    // Nếu $data rỗng (null hoặc empty) thì thực hiện kiểm tra nonce
    if ( empty($data) ) {
        $nonce = isset($_POST['woocommerce-process-checkout-nonce'])
            ? sanitize_text_field( wp_unslash( $_POST['woocommerce-process-checkout-nonce'] ) )
            : '';
        if ( ! wp_verify_nonce( $nonce, 'woocommerce-process_checkout' ) ) {
            wc_add_notice( __( 'Yêu cầu không hợp lệ. Vui lòng thử lại.', 'ithan-devvn-checkout-for-woocommerce' ), 'error' );
            return;
        }
    }

    // ------------ XỬ LÝ BILLING ------------
    $billing_address_1 = '';

    // 1) Tỉnh/Thành (billing_province_code)
    $province_code = ! empty($data['billing_province_code'])
        ? sanitize_text_field($data['billing_province_code'])
        : ( ! empty($_POST['billing_province_code'])
            ? sanitize_text_field(wp_unslash($_POST['billing_province_code']))
            : ''
        );
    if ( $province_code ) {
        $province_name = ithandech_devvn_vietnam_shipping()->ithandech_get_province_name_by_code($province_code);
        // Lưu vào meta city (hoặc _billing_state, tuỳ ý)
        // $order->update_meta_data('_billing_city', $province_name);
        $order->set_billing_city( $province_name );
        $billing_address_1 .= $province_name;

        $order->update_meta_data('_billing_province_code', $province_code);
    }

    // 2) Quận/Huyện (billing_district_code)
    $district_code = ! empty($data['billing_district_code'])
        ? sanitize_text_field($data['billing_district_code'])
        : ( ! empty($_POST['billing_district_code'])
            ? sanitize_text_field(wp_unslash($_POST['billing_district_code']))
            : ''
        );
    if ( $district_code ) {
        $district_name = ithandech_devvn_vietnam_shipping()->ithandech_get_district_name_by_code($district_code);
        $billing_address_1 .= ' - ' . $district_name;

        $order->update_meta_data('_billing_district_code', $district_code);
    }

    // 3) Xã/Phường (billing_ward_code)
    $ward_code = ! empty($data['billing_ward_code'])
        ? sanitize_text_field($data['billing_ward_code'])
        : ( ! empty($_POST['billing_ward_code'])
            ? sanitize_text_field(wp_unslash($_POST['billing_ward_code']))
            : ''
        );
    if ( $ward_code ) {
        $ward_name = ithandech_devvn_vietnam_shipping()->ithandech_get_ward_name_by_code($ward_code);
        $billing_address_1 .= ' - ' . $ward_name;

        $order->update_meta_data('_billing_ward_code', $ward_code);
    }

    // Cập nhật meta _billing_address_1
    // $order->update_meta_data('_billing_address_1', $billing_address_1);
    $order->set_billing_address_1( $billing_address_1 );

    // 4) Địa chỉ cụ thể (billing_address_2)
    $billing_address_2 = ! empty($data['billing_address_2'])
        ? sanitize_text_field($data['billing_address_2'])
        : ( ! empty($_POST['billing_address_2'])
            ? sanitize_text_field(wp_unslash($_POST['billing_address_2']))
            : ''
        );
    // $order->update_meta_data('_billing_address_2', $billing_address_2);
    $order->set_billing_address_2( $billing_address_2 );

    // 5) SĐT (billing_phone)
    $billing_phone = ! empty($data['billing_phone'])
        ? sanitize_text_field($data['billing_phone'])
        : ( ! empty($_POST['billing_phone'])
            ? sanitize_text_field(wp_unslash($_POST['billing_phone']))
            : ''
        );
    // $order->update_meta_data('_billing_phone', $billing_phone);
    $order->set_billing_phone( $billing_phone );

    // ------------ XỬ LÝ SHIPPING ------------
    $shipping_address_1 = '';

    // 1) Tỉnh/Thành phố (shipping_province_code)
    // - Ưu tiên $data (đơn ảo), tiếp đó _POST (checkout form),
    // - Nếu vẫn trống, copy từ billing_province_code ở trên (biến $province_code).
    $shipping_province_code = ! empty($data['shipping_province_code'])
        ? sanitize_text_field($data['shipping_province_code'])
        : ( ! empty($_POST['shipping_province_code'])
            ? sanitize_text_field(wp_unslash($_POST['shipping_province_code']))
            : ( ! empty($province_code) ? $province_code : '' )
        );
    if ( $shipping_province_code ) {
        $shipping_province_name = ithandech_devvn_vietnam_shipping()->ithandech_get_province_name_by_code($shipping_province_code);
        // $order->update_meta_data('_shipping_city', $shipping_province_name);
         $order->set_shipping_city( $shipping_province_name );
        $shipping_address_1 .= $shipping_province_name;
    }

    // 2) Quận/Huyện (shipping_district_code)
    $shipping_district_code = ! empty($data['shipping_district_code'])
        ? sanitize_text_field($data['shipping_district_code'])
        : ( ! empty($_POST['shipping_district_code'])
            ? sanitize_text_field(wp_unslash($_POST['shipping_district_code']))
            : ( ! empty($district_code) ? $district_code : '' )
        );
    if ( $shipping_district_code ) {
        $shipping_district_name = ithandech_devvn_vietnam_shipping()->ithandech_get_district_name_by_code($shipping_district_code);
        $shipping_address_1 .= ' - ' . $shipping_district_name;
    }

    // 3) Xã/Phường (shipping_ward_code)
    $shipping_ward_code = ! empty($data['shipping_ward_code'])
        ? sanitize_text_field($data['shipping_ward_code'])
        : ( ! empty($_POST['shipping_ward_code'])
            ? sanitize_text_field(wp_unslash($_POST['shipping_ward_code']))
            : ( ! empty($ward_code) ? $ward_code : '' )
        );
    if ( $shipping_ward_code ) {
        $shipping_ward_name = ithandech_devvn_vietnam_shipping()->ithandech_get_ward_name_by_code($shipping_ward_code);
        $shipping_address_1 .= ' - ' . $shipping_ward_name;
    }

    // Cập nhật meta _shipping_address_1
    // $order->update_meta_data('_shipping_address_1', $shipping_address_1);
    $order->set_shipping_address_1( $shipping_address_1 );

    // 4) Địa chỉ cụ thể (shipping_address_2)
    $shipping_address_2 = ! empty($data['shipping_address_2'])
        ? sanitize_text_field($data['shipping_address_2'])
        : ( ! empty($_POST['shipping_address_2'])
            ? sanitize_text_field(wp_unslash($_POST['shipping_address_2']))
            : ( ! empty($billing_address_2) ? $billing_address_2 : '' )
        );
    // $order->update_meta_data('_shipping_address_2', $shipping_address_2);
    $order->set_shipping_address_2( $shipping_address_2 );

    // 5) SĐT giao hàng (shipping_phone)
    $shipping_phone = ! empty($data['shipping_phone'])
        ? sanitize_text_field($data['shipping_phone'])
        : ( ! empty($_POST['shipping_phone'])
            ? sanitize_text_field(wp_unslash($_POST['shipping_phone']))
            : ( ! empty($billing_phone) ? $billing_phone : '' )
        );
    // $order->update_meta_data('_shipping_phone', $shipping_phone);
    $order->set_shipping_phone( $shipping_phone );

    // 6) shipping_first_name
    $shipping_first_name = ! empty($data['shipping_first_name'])
        ? sanitize_text_field($data['shipping_first_name'])
        : ( ! empty($_POST['shipping_first_name'])
            ? sanitize_text_field(wp_unslash($_POST['shipping_first_name']))
            : ( ! empty($data['billing_first_name'])
                ? sanitize_text_field($data['billing_first_name'])
                : ( ! empty($_POST['billing_first_name'])
                    ? sanitize_text_field(wp_unslash($_POST['billing_first_name']))
                    : ''
                )
            )
        );
    
    // $order->update_meta_data('_shipping_first_name', $shipping_first_name);
    $order->set_shipping_first_name( $shipping_first_name );

    // 7) shipping_last_name
    $shipping_last_name = ! empty($data['shipping_last_name'])
        ? sanitize_text_field($data['shipping_last_name'])
        : ( ! empty($_POST['shipping_last_name'])
            ? sanitize_text_field(wp_unslash($_POST['shipping_last_name']))
            : ( ! empty($data['billing_last_name'])
                ? sanitize_text_field($data['billing_last_name'])
                : ( ! empty($_POST['billing_last_name'])
                    ? sanitize_text_field(wp_unslash($_POST['billing_last_name']))
                    : ''
                )
            )
        );
    // $order->update_meta_data('_shipping_last_name', $shipping_last_name);
    $order->set_shipping_last_name( $shipping_last_name );

    // 8) shipping_company
    $shipping_company = ! empty($data['shipping_company'])
        ? sanitize_text_field($data['shipping_company'])
        : ( ! empty($_POST['shipping_company'])
            ? sanitize_text_field(wp_unslash($_POST['shipping_company']))
            : ( ! empty($data['billing_company'])
                ? sanitize_text_field($data['billing_company'])
                : ( ! empty($_POST['billing_company'])
                    ? sanitize_text_field(wp_unslash($_POST['billing_company']))
                    : ''
                )
            )
        );
    // $order->update_meta_data('_shipping_company', $shipping_company);
    $order->set_shipping_company( $shipping_company );

    // 9) shipping_postcode
    $shipping_postcode = ! empty($data['shipping_postcode'])
        ? sanitize_text_field($data['shipping_postcode'])
        : ( ! empty($_POST['shipping_postcode'])
            ? sanitize_text_field(wp_unslash($_POST['shipping_postcode']))
            : ( ! empty($data['billing_postcode'])
                ? sanitize_text_field($data['billing_postcode'])
                : ( ! empty($_POST['billing_postcode'])
                    ? sanitize_text_field(wp_unslash($_POST['billing_postcode']))
                    : ''
                )
            )
        );
    // $order->update_meta_data('_shipping_postcode', $shipping_postcode);
    $order->set_shipping_postcode( $shipping_postcode );
}

add_action( 'woocommerce_admin_order_totals_after_shipping', 'ithandech_add_payment_method_row_below_shipping' );
function ithandech_add_payment_method_row_below_shipping( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return;
    }

    // Lấy tiêu đề phương thức thanh toán
    $payment_method_title = $order->get_payment_method_title();
    // Nếu chưa có hoặc đơn hàng không có payment method, bạn có thể kiểm tra thêm
    if ( ! $payment_method_title ) {
        $payment_method_title = __( 'Không xác định', 'ithan-devvn-checkout-for-woocommerce' );
    }
    echo '
    <tr>
        <td class="total" colspan="2">' . esc_html( $payment_method_title ) . '</td>
        <td></td>
    </tr>';
}

add_filter( 'woocommerce_checkout_posted_data', 'ithandech_customize_checkout_posted_data_with_province_code' );
function ithandech_customize_checkout_posted_data_with_province_code( $data ) {
    // Bắt buộc set quốc gia là 'VN'
    $data['billing_country']  = 'VN';
    $data['shipping_country'] = 'VN';

    // Bắt buộc gán billing_state từ billing_province_code, nếu không có thì dùng giá trị mặc định (ví dụ 'HANOI')
    if ( isset( $data['billing_province_code'] ) && ! empty( $data['billing_province_code'] ) ) {
        $data['billing_state'] = $data['billing_province_code'];
    }

    // Bắt buộc gán shipping_state từ shipping_province_code nếu có,
    // nếu không có, thử dùng billing_province_code, nếu vẫn không có thì dùng default 'HANOI'
    if ( isset( $data['shipping_province_code'] ) && ! empty( $data['shipping_province_code'] ) ) {
        $data['shipping_state'] = $data['shipping_province_code'];
    } elseif ( isset( $data['billing_province_code'] ) && ! empty( $data['billing_province_code'] ) ) {
        $data['shipping_state'] = $data['billing_province_code'];
    }

    // error_log( print_r( $data, true ) );

    return $data;
}

// Nạp file Admin trước để sử dụng ở fontend
include_once ITHANDECH_PLUGIN_DIR . 'includes/ithandech_wc_admin_helper.php';
include_once ITHANDECH_PLUGIN_DIR . 'includes/ithandech_wc_admin_options.php';

include_once ITHANDECH_PLUGIN_DIR . 'includes/ithandech_wc_checkout_enqueue_script.php';
include_once ITHANDECH_PLUGIN_DIR . 'includes/ithandech_wc_checkout_notices.php';

// Admin

function ithandech_custom_admin_address_fields($fields, $current_province, $curent_district, $current_ward_code, $current_first_name) {
    global $ithandech_tinh_thanhpho;
    // Lấy danh sách quận/huyện dựa trên $default_province_code
    $districts = ithandech_devvn_vietnam_shipping()->ithandech_get_list_district_select( $current_province );

    // Lấy danh sách xã/phường dựa trên $default_district_code
    $wards = ithandech_devvn_vietnam_shipping()->ithandech_get_list_village_select( $curent_district );

    // Ẩn một số trường không cần hiển thị
    if ( isset( $fields['state'] ) ) {
        // unset( $fields['state'] );

        // $fields['state'] = [];
        // $fields['state']['type'] = 'hidden';
        // $fields['state']['wrapper_class'] = 'form-field-wide ithan__display-none';
        
        // $prevousVal = $fields['state']['value'];

        if (isset($fields['state']['value'])) {
            $previousVal = $fields['state']['value'];
        } else {
            $previousVal = ''; // giá trị mặc định
        }

        $fields['state'] = array(
            'label'         => '',
            'id'            => '_billing_state',
            'name'          => '_billing_state',
            'show'          => false,
            'value'         => $previousVal,
            'wrapper_class' => 'form-field-wide',
            // 'class'         => 'regular-text',
            'type'          => 'hidden',
            'wrapper_class' => 'form-field-wide ithan__display-none'
        );
    }

    if ( isset( $fields['city'] ) ) {
        unset( $fields['city'] );
    }
    if ( isset( $fields['company'] ) ) {
        unset( $fields['company'] );
    }
    if ( isset( $fields['address_1'] ) ) {
        // unset( $fields['address_1'] );

        // $fields['address_1']['label'] = '';
        $fields['address_1']['type'] = 'hidden';
        $fields['address_1']['wrapper_class'] = 'form-field-wide ithan__display-none';
    }
    if ( isset( $fields['postcode'] ) ) {
        unset( $fields['postcode'] );
    }

    $maleTitles = ITHANDECH_MALE_TITLES;
    $femaleTitles = ITHANDECH_FEMALE_TITLES;

    $merged = array();
    $length = max(count($maleTitles), count($femaleTitles));

    for ($i = 0; $i < $length; $i++) {
        if (isset($maleTitles[$i])) {
            $merged[$maleTitles[$i]] = $maleTitles[$i];
        }
        if (isset($femaleTitles[$i])) {
            $merged[$femaleTitles[$i]] = $femaleTitles[$i];
        }
    }
    
    // Đổi label cho first_name và last_name
    if ( isset( $fields['first_name'] ) ) {
        $fields['first_name']['type'] = 'select';
        $fields['first_name']['label'] = 'Xưng Hô';
        $fields['first_name']['wrapper_class'] = 'form-field-wide';

        $fields['first_name']['options'] = $merged;
        $fields['first_name']['default'] = $current_first_name;
    }
    
    if ( isset( $fields['last_name'] ) ) {
        $fields['last_name']['label'] = 'Họ tên';
        $fields['last_name']['required'] = true; // Bắt buộc nhập Họ tên
        $fields['last_name']['class'] = 'ithandech_required';
        $fields['last_name']['wrapper_class'] = 'form-field-wide';
        $fields['last_name']['description'] = __( 'Họ tên là bắt buộc.', 'ithan-devvn-checkout-for-woocommerce' );
    }

    // Cho trường country: hiển thị dưới dạng select với 1 option (VN) và chỉ đọc
    if ( isset( $fields['country'] ) ) {
        $fields['country']['type'] = 'select';
        $fields['country']['options'] = array( 'VN' => 'Việt Nam' );
        $fields['country']['custom_attributes'] = array( 'disabled' => 'disabled' );
        $fields['country']['label'] = 'Quốc gia (chỉ đọc)';
        $fields['country']['wrapper_class'] = 'form-field-wide';
    }

    if ( isset( $fields['address_2'] ) ) {
        $fields['address_2']['label'] = 'Địa Chỉ';
        $fields['address_2']['class'] = 'ithandech_required';
        $fields['address_2']['wrapper_class'] = 'form-field-wide';
        $fields['address_2']['required'] = true; // Bắt buộc nhập địa chỉ (dòng 2)
        $fields['address_2']['description'] = __( 'Địa chỉ là bắt buộc.', 'ithan-devvn-checkout-for-woocommerce' );
    }
    
    // Bắt buộc nhập số điện thoại (phone)
    if ( isset( $fields['phone'] ) ) {
        $fields['phone']['required'] = true;
        $fields['phone']['class'] = 'ithandech_required';
        $fields['phone']['description'] = __( 'SĐT bắt buộc.', 'ithan-devvn-checkout-for-woocommerce' );
        $fields['phone']['wrapper_class'] = 'form-field-wide';
    }

    if ( isset( $fields['email'] ) ) {
        $fields['email']['class'] = 'ithandech_required';
        $fields['email']['wrapper_class'] = 'form-field-wide';
        $fields['email']['description'] = __( 'Địa chỉ email không hợp lệ.', 'ithan-devvn-checkout-for-woocommerce' );
    }

    // Xử lý trường province_code, district_code, ward_code    
    
    if ( ! isset( $fields['province_code'] ) ) {
        $fields['province_code'] = array(
            'label'         => __( 'Chọn tỉnh/thành', 'ithan-devvn-checkout-for-woocommerce' ),
            'type'          => 'select',
            'show'          => false,
            'options'       => $ithandech_tinh_thanhpho, // danh sách tỉnh thành
            'description'   => __( 'Tỉnh/Thành là bắt buộc.', 'ithan-devvn-checkout-for-woocommerce' ),
            'wrapper_class' => 'form-field-wide',
            'class'         => 'ithandech_required',
            'required'      => true,
            'default'       => $current_province, // Giá trị mặc định nếu chưa có dữ liệu
            'value'         => $current_province, // Gán giá trị hiện tại từ order meta
        );
    } else {
        $fields['province_code']['type'] = 'select';
        $fields['province_code']['show'] = false;
        $fields['province_code']['options'] = $ithandech_tinh_thanhpho;
        $fields['province_code']['required'] = true;
        $fields['province_code']['class'] = 'ithandech_required';
        $fields['province_code']['description'] = __( 'Tỉnh/Thành là bắt buộc.', 'ithan-devvn-checkout-for-woocommerce' );
        $fields['province_code']['wrapper_class'] = 'form-field-wide';
        // Nếu chưa được thiết lập, gán giá trị default là 'HANOI'
        if ( empty( $fields['province_code']['default'] ) ) {
            $fields['province_code']['default'] = $current_province;
        }
         // Gán thuộc tính value để hiển thị giá trị hiện tại
        $fields['province_code']['value'] = $current_province;
    }

    if ( ! isset( $fields['district_code'] ) ) {
        $fields['district_code'] = array(
            'label'         => __( 'Chọn quận/huyện', 'ithan-devvn-checkout-for-woocommerce' ),
            'type'          => 'select',
            'show'          => false,
            'options'       => $districts,
            'wrapper_class' => 'form-field-wide',
            'required'      => true,  // Bắt buộc nhập
            'class'         => 'ithandech_required',
            'wrapper_class' => 'form-field-wide',
            'description'   => __( 'Quận/Huyện là bắt buộc.', 'ithan-devvn-checkout-for-woocommerce' ),
            'default'       => $curent_district, // Giá trị mặc định nếu chưa có dữ liệu
            'value'         => $curent_district, // Gán giá trị hiện tại từ order meta
        );
    } else {
        $fields['district_code']['type'] = 'select';
        $fields['district_code']['show'] = false;
        $fields['district_code']['options'] =  $districts;
        $fields['district_code']['required'] = true;
        $fields['district_code']['class'] = 'ithandech_required';
        $fields['district_code']['description'] = __( 'Quận/Huyện là bắt buộc.', 'ithan-devvn-checkout-for-woocommerce' );
        $fields['district_code']['wrapper_class'] = 'form-field-wide';
        if ( empty( $fields['district_code']['default'] ) ) {
            $fields['district_code']['default'] = $curent_district;
        }
        $fields['district_code']['value'] = $curent_district;
    }
    if ( ! isset( $fields['ward_code'] ) ) {
        $fields['ward_code'] = array(
            'label'         => __( 'Chọn xã/phường', 'ithan-devvn-checkout-for-woocommerce' ),
            'type'          => 'select',
            'show'          => false,
            'options'       => $wards,
            'wrapper_class' => 'form-field-wide',
            'required'      => true,  // Bắt buộc nhập
            'class'         => 'ithandech_required',
            'wrapper_class' => 'form-field-wide',
            'description'   => __( 'Xã/Phường là bắt buộc.', 'ithan-devvn-checkout-for-woocommerce' ),
            'default'       => $current_ward_code, // Giá trị mặc định nếu chưa có dữ liệu
            'value'         => $current_ward_code, // Gán giá trị hiện tại từ order meta
        );
    } else {
        $fields['ward_code']['type'] = 'select';
        $fields['ward_code']['show'] = false;
        $fields['ward_code']['options'] =  $wards;
        $fields['ward_code']['required'] = true;
        $fields['ward_code']['class'] = 'ithandech_required';
        $fields['ward_code']['description'] = __( 'Xã/Phường là bắt buộc.', 'ithan-devvn-checkout-for-woocommerce' );
        $fields['ward_code']['wrapper_class'] = 'form-field-wide';
        if ( empty( $fields['ward_code']['default'] ) ) {
            $fields['ward_code']['default'] = $current_ward_code;
        }
        $fields['ward_code']['value'] = $current_ward_code;
    }
    
    // Sắp xếp lại thứ tự hiển thị theo yêu cầu:
    $desired_order = array( 
        'salutation',      // Xưng Hô
        'first_name',      // Xưng Hô
        'last_name',       // Họ tên
        'phone',           // Số điện thoại
        'email',           // Email
        'country',         // Khu vực/quốc gia
        'province_code',   // Tỉnh/Thành
        'district_code',   // Quận/huyện
        'ward_code',       // Xã/phường
        'address_2'        // Địa chỉ dòng 2
    );
    
    $ordered_fields = array();
    foreach ( $desired_order as $key ) {
        if ( isset( $fields[ $key ] ) ) {
            $ordered_fields[ $key ] = $fields[ $key ];
            unset( $fields[ $key ] );
        }
    }
    
    // Gộp các trường còn lại (nếu có) vào cuối mảng
    $fields = array_merge( $ordered_fields, $fields );

    return $fields;
}

function ithandech_safe_get_order_id() {
    global $theorder;

    // 1) Nếu đã có WC_Order object, dùng luôn
    if ( ! empty( $theorder ) && $theorder instanceof WC_Order ) {
        return $theorder->get_id();
    }

    // 2) Chưa có WC_Order, phải lấy từ URL
    //    – đảm bảo có id và nonce
    if ( empty( $_GET['id'] ) || empty( $_GET['_wpnonce'] ) ) {
        return 0;
    }

    // 3) Uns­lash và sanitize nonce
    $nonce = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );

    // 4) Verify nonce với action name đúng
    if ( ! wp_verify_nonce( $nonce, 'woocommerce-order-edit' ) ) {
        return 0;
    }

    // 5) Lấy và sanitize ID
    $id = absint( wp_unslash( $_GET['id'] ) );

    // 6) Kiểm tra quyền của user với order này
    if ( ! current_user_can( 'edit_shop_order', $id ) ) {
        return 0;
    }

    return $id;
}

add_filter( 'woocommerce_admin_shipping_fields', 'ithandech_customize_admin_shipping_fields' );
function ithandech_customize_admin_shipping_fields( $fields ) {
    // Lấy order ID từ biến toàn cục $the_order (nếu có)
    $order_id = ithandech_safe_get_order_id();

    $current_province   = '';
    $curent_district    = '';
    $current_ward_code  = '';
    $current_first_name = '';

    if ( $order_id ) {
        $order = wc_get_order( $order_id );
        // Lấy giá trị hiện tại của trường shipping_province_code từ wp_wc_orders_meta
        $current_province   = $order->get_meta( '_shipping_province_code', true );
        $curent_district    = $order->get_meta( '_shipping_district_code', true );
        $current_ward_code  = $order->get_meta( '_shipping_ward_code', true );
        $current_first_name = $order->get_shipping_first_name();

        if ( empty( $current_province ) ){
            // Lấy giá trị hiện tại của trường billing_province_code từ wp_wc_orders_meta
            $current_province = $order->get_meta( '_billing_province_code', true );
            $curent_district = $order->get_meta( '_billing_district_code', true );
            $current_ward_code = $order->get_meta( '_billing_ward_code', true );
            $current_first_name = $order->get_billing_first_name();
        }
        
    }
    // Nếu chưa có giá trị, dùng giá trị mặc định "HANOI"
    if ( empty( $current_province ) ) {
        $current_province = 'HANOI';
    }
    if ( empty( $curent_district ) ) {
        $curent_district = '001';
    }
    if ( empty( $current_ward_code ) ) {
        $current_ward_code = '00001';
    }

    $fields = ithandech_custom_admin_address_fields($fields, $current_province, $curent_district, $current_ward_code, $current_first_name);

    return $fields;
}

add_filter( 'woocommerce_admin_billing_fields', 'ithandech_customize_admin_billing_fields');
function ithandech_customize_admin_billing_fields( $fields ) {
    $order_id = ithandech_safe_get_order_id();

    $current_province   = '';
    $curent_district    = '';
    $current_ward_code  = '';
    $current_first_name = '';
    if ( $order_id ) {
        $order = wc_get_order( $order_id );
        // Lấy giá trị hiện tại của trường billing_province_code từ wp_wc_orders_meta
        $current_province   = $order->get_meta( '_billing_province_code', true );
        $curent_district    = $order->get_meta( '_billing_district_code', true );
        $current_ward_code  = $order->get_meta( '_billing_ward_code', true );

        $current_first_name = $order->get_billing_first_name();
    }
    // Nếu chưa có giá trị, dùng giá trị mặc định "HANOI"
    if ( empty( $current_province ) ) {
        $current_province = 'HANOI';
    }
    if ( empty( $curent_district ) ) {
        $curent_district = '001';
    }
    if ( empty( $current_ward_code ) ) {
        $current_ward_code = '00001';
    }

    $fields = ithandech_custom_admin_address_fields($fields, $current_province, $curent_district, $current_ward_code, $current_first_name);
    
    return $fields;
}

// được gọi ngay trong quá trình lưu (save) một đơn hàng (post type shop_order) trên trang Order Edit trong khu vực admin WooCommerce
add_action( 'woocommerce_process_shop_order_meta', 'ithandech_update_billing_address_with_address_codes', 99, 1 );
function ithandech_update_billing_address_with_address_codes( $order_id ) {
    $order = wc_get_order( $order_id );

    // ------------ XỬ LÝ BILLING ------------
    $billing_address_1 = '';

    $province_code = $order->get_meta( '_billing_province_code', true );
    if ( ! empty( $province_code ) ) {
        $order->update_meta_data( '_billing_state', $province_code );
    }
    
    // 1) Tỉnh/Thành (billing_province_code)
    if ( $province_code ) {
        $province_name = ithandech_devvn_vietnam_shipping()->ithandech_get_province_name_by_code($province_code);
        $order->update_meta_data('_billing_city', $province_name);
        $billing_address_1 .= $province_name;
    }

    // 2) Quận/Huyện (billing_district_code)
    $district_code = $order->get_meta( '_billing_district_code', true );
    if ( $district_code ) {
        $district_name = ithandech_devvn_vietnam_shipping()->ithandech_get_district_name_by_code($district_code);
        $billing_address_1 .= ' - ' . $district_name;
    }

    // 3) Xã/Phường (billing_ward_code)
    $ward_code = $order->get_meta( '_billing_ward_code', true );
    if ( $ward_code ) {
        $ward_name = ithandech_devvn_vietnam_shipping()->ithandech_get_ward_name_by_code($ward_code);
        $billing_address_1 .= ' - ' . $ward_name;
    }

    // Cập nhật meta _billing_address_1
    $order->update_meta_data('_billing_address_1', $billing_address_1);


    // ------------ XỬ LÝ SHIPPING ------------
    $shipping_address_1 = '';
    
    $shipping_province_code = $order->get_meta( '_shipping_province_code', true ); 
    if ( empty($shipping_province_code) ){ // nếu không tìm thấy hoặc trống (chuỗi rỗng)
        $shipping_address_1 .= $province_name;
        $order->update_meta_data( '_shipping_city', $province_name );
        $order->update_meta_data( '_shipping_state', $province_code );
    } else {
        $shipping_province_name = ithandech_devvn_vietnam_shipping()->ithandech_get_province_name_by_code($shipping_province_code);
        $order->update_meta_data( '_shipping_city', $shipping_province_name );
        $order->update_meta_data( '_shipping_state', $shipping_province_code );
        $shipping_address_1 .= $shipping_province_name;
    }

    // 2) Quận/Huyện (shipping_district_code)
    $shipping_district_code = $order->get_meta( '_shipping_district_code', true ); 
    if ( empty($shipping_district_code) ){ // nếu không tìm thấy hoặc trống (chuỗi rỗng)
        $shipping_address_1 .=  ' - ' .  $district_name;
    } else {
        $shipping_district_name = ithandech_devvn_vietnam_shipping()->ithandech_get_district_name_by_code($shipping_district_code);
        $shipping_address_1 .= ' - ' . $shipping_district_name;
    }

    // 3) Xã/Phường (shipping_ward_code)
    $shipping_ward_code = $order->get_meta( '_shipping_ward_code', true ); 
    if ( empty($shipping_ward_code) ){ // nếu không tìm thấy hoặc trống (chuỗi rỗng)
        $shipping_address_1 .=  ' - ' .  $ward_name;
    } else {
        $shipping_ward_name = ithandech_devvn_vietnam_shipping()->ithandech_get_ward_name_by_code($shipping_ward_code);
        $shipping_address_1 .= ' - ' . $shipping_ward_name;
    }

    // Cập nhật meta _shipping_address_1
    $order->update_meta_data( '_shipping_address_1', $shipping_address_1 );

    $order->save();
}

add_action( 'admin_notices', 'ithandech_custom_admin_notice_for_orders' );
function ithandech_custom_admin_notice_for_orders() {
    // Kiểm tra xem có đang ở trang edit đơn hàng không
    // Lấy nonce từ URL (GET), unslash và sanitize
    $nonce = isset( $_GET['_wpnonce'] )
    ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) )
    : '';

    if ( 
        isset( $_GET['page'], $_GET['action'], $_GET['_wpnonce'] ) &&
        'wc-orders' === $_GET['page'] &&
        'edit' === $_GET['action'] &&
        $nonce &&
        wp_verify_nonce($nonce, 'ithandech_order_notice')
    ) {
        
        // Ví dụ, kiểm tra có param ithan_error không
        // Lấy và sanitize biến ithan_error từ URL
        $ithan_error = '';
        if ( isset( $_GET['ithan_error'] ) ) {
            $ithan_error = rawurldecode(
                sanitize_text_field(
                    wp_unslash( $_GET['ithan_error'] )
                )
            );
        }

        if ( $ithan_error ) {
            echo '<div class="notice notice-error is-dismissible"><p>'
                 . esc_html( urldecode($ithan_error) )
                 . '</p></div>';
        }

        // Lấy và sanitize biến ithan_success từ URL
        $ithan_success = '';

        if ( isset( $_GET['ithan_success'] ) ) {
            $ithan_success = rawurldecode( sanitize_text_field( wp_unslash( $_GET['ithan_success'] ) ) );
        }

        if ( $ithan_success ) {
            echo '<div class="notice notice-success is-dismissible"><p>' 
                . esc_html(urldecode($ithan_success))  // Đã dùng biến đã sanitize
                . '</p></div>';
        }
    }
}

add_action( 'woocommerce_admin_order_data_after_shipping_address', 'ithandech_editable_order_meta_shipping' );

function ithandech_editable_order_meta_shipping( $order ){

	$shippingdate = $order->get_meta( 'shippingdate' );

	?>
		<div class="address">
            <p<?php echo empty( $shippingdate ) ? ' class="' . esc_attr( 'none_set' ) . '"' : ''; ?>>
                <strong><?php echo esc_html__( 'Ngày giao hàng:', 'ithan-devvn-checkout-for-woocommerce' ); ?></strong>
                <?php echo ! empty( $shippingdate ) ? esc_html( $shippingdate ) : esc_html__( 'Anytime.', 'ithan-devvn-checkout-for-woocommerce' ); ?>
			</p>
		</div>
		<div class="edit_address">
			<?php
				woocommerce_wp_text_input( array(
					'id' => 'shippingdate',
					'label' => __( 'Ngày giao hàng', 'ithan-devvn-checkout-for-woocommerce' ),
					'wrapper_class' => 'form-field-wide',
					'class' => 'date-picker',
					'style' => 'width:100%',
					'value' => $shippingdate,
                    'description'   => __( 'Ngày mà khách hàng mong muốn nhận được hàng.', 'ithan-devvn-checkout-for-woocommerce' ),
				) );
			?>
		</div>
	<?php
}

add_filter( 'woocommerce_admin_order_data_after_billing_address', 'ithandech_inject_custom_html_for_alert_message' );
function ithandech_inject_custom_html_for_alert_message( $order ) {
    echo '<div id="ithandech--order-alert" class="ava-modal"> </div>';
}
