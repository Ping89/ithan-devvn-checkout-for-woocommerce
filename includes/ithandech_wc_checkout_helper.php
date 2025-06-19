<?php 


function ithandech_convert_to_minified($filename) {
    // Lấy thông tin path (tên file, extension, v.v.)
    $info = pathinfo($filename);
    
    // Lấy phần tên file (không gồm phần mở rộng)
    $basename = $info['filename'];
    
    // Nếu có phần mở rộng, ta lấy để ghép lại
    $extension = $info['extension'] ?? '';
    
    // Trả về tên file dạng style.min.css hoặc script.min.js
    return $basename . '.min.' . $extension;
}

/**
 * Tìm và tải file CSS/JS từ child theme hoặc plugin, tùy vào chế độ phát triển hoặc build
 *
 * @param string $file_name Tên file CSS/JS (VD: 'style.css', 'script.js')
 * @param string $file_type Loại file cần tìm ('css' hoặc 'js')
 * @param string $mode Chế độ hiện tại ('src', 'build' hoặc '')
 * @param string $plugin_path Đường dẫn thư mục chứa file trong plugin
 * @return string|bool Đường dẫn đến file nếu tìm thấy, hoặc false nếu không tìm thấy
 */
function ithandech_wc_checkout_get_assets_file($file_name, $file_type = 'css', $mode = '', $plugin_path = ''): bool|string
{
    // Nếu không có plugin path, sử dụng thư mục mặc định của plugin
    if (!$plugin_path) {
        $plugin_path = plugin_dir_path(dirname(__FILE__)) . 'assets/'; // Thư mục assets trong plugin
    }

    // Tạo 2 đường dẫn file cho child theme: một cho build và một cho dev
    $build_file_path = get_stylesheet_directory() . '/ithan-devvn-checkout-for-woocommerce/assets/build/' . $file_type . '/' . ithandech_convert_to_minified($file_name);
    $dev_file_path = get_stylesheet_directory() . '/ithan-devvn-checkout-for-woocommerce/assets/src/' . $file_type . '/' . $file_name;

    // Kiểm tra mode, nếu rỗng thì ưu tiên build trước, dev sau
    if ($mode === '') {
        // Ưu tiên build trước
        if (file_exists($build_file_path)) {
            $file_url = get_stylesheet_directory_uri() . '/ithan-devvn-checkout-for-woocommerce/assets/build/' . $file_type . '/' . ithandech_convert_to_minified($file_name);
        } else {
            // Nếu không có trong build, kiểm tra dev
            if (file_exists($dev_file_path)) {
                $file_url = get_stylesheet_directory_uri() . '/ithan-devvn-checkout-for-woocommerce/assets/src/' . $file_type . '/' . $file_name;
            } else {
                // Nếu không tìm thấy trong child theme, tìm trong plugin
                $plugin_file_path = $plugin_path . 'build/' . $file_type . '/' . ithandech_convert_to_minified($file_name);
                if (file_exists($plugin_file_path)) {
                    $file_url = plugins_url('assets/build/' . $file_type . '/' . ithandech_convert_to_minified($file_name), dirname(__FILE__));
                } else {
                    $plugin_file_path = $plugin_path . 'src/' . $file_type . '/' . $file_name;
                    if (file_exists($plugin_file_path)) {
                        $file_url = plugins_url('assets/src/' . $file_type . '/' . $file_name, dirname(__FILE__));
                    } else {
                        return false; // Nếu không tìm thấy ở đâu cả, trả về false
                    }
                }
            }
        }
    } else {
        // Nếu có mode ('src' hoặc 'build'), tìm theo mode
        if ($mode === 'build') {
            $mode_file_path = get_stylesheet_directory() . '/ithan-devvn-checkout-for-woocommerce/assets/' . $mode . '/' . $file_type . '/' . ithandech_convert_to_minified($file_name);
        }
        else{
            $mode_file_path = get_stylesheet_directory() . '/ithan-devvn-checkout-for-woocommerce/assets/' . $mode . '/' . $file_type . '/' . $file_name;
        }
        if (file_exists($mode_file_path)) {
            if ($mode === 'build'){
                $file_url = get_stylesheet_directory_uri() . '/ithan-devvn-checkout-for-woocommerce/assets/' . $mode . '/' . $file_type . '/' . ithandech_convert_to_minified($file_name);
            }
            else{
                $file_url = get_stylesheet_directory_uri() . '/ithan-devvn-checkout-for-woocommerce/assets/' . $mode . '/' . $file_type . '/' . $file_name;
            }
        } else {
            // Nếu không tìm thấy trong child theme, tìm trong plugin
            if ($mode === 'build'){
                $plugin_mode_file_path = $plugin_path . $mode . '/' . $file_type . '/' . ithandech_convert_to_minified($file_name);
            }
            else{
                $plugin_mode_file_path = $plugin_path . $mode . '/' . $file_type . '/' . $file_name;
            }
            if (file_exists($plugin_mode_file_path)) {
                if ($mode === 'build'){
                    $file_url = plugins_url('assets/' . $mode . '/' . $file_type . '/' . ithandech_convert_to_minified($file_name), dirname(__FILE__));
                }
                else{
                    $file_url = plugins_url('assets/' . $mode . '/' . $file_type . '/' . $file_name, dirname(__FILE__));
                }
            } else {
                return false; // Nếu không tìm thấy ở đâu cả, trả về false
            }
        }
    }

    // Nếu file là CSS
    if ($file_type === 'css') {
        return $file_url;
    }

    // Nếu file là JS
    if ($file_type === 'js') {
        return $file_url;
    }

    return false;
}


// function ithandech_for_wc_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
//     // Nếu có các tham số truyền vào, extract để chuyển mảng thành các biến
//     if ( ! empty( $args ) && is_array( $args ) ) {
//         extract( $args );
//     }
    
//     // Nếu không có tham số $template_path, mặc định tìm trong thư mục 'pluginname/templates' của theme
//     if ( empty( $template_path ) ) {
//         $template_path = 'ithan-devvn-checkout-for-woocommerce/templates';
//     }
    
//     // Nếu không có tham số $default_path, sử dụng thư mục 'templates' bên trong plugin làm fallback
//     if ( empty( $default_path ) ) {
//         $default_path = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/';
//     }
    
//     // Bước 1: Tìm template trong theme tại đường dẫn: pluginname/templates/template_name
//     $located = locate_template( trailingslashit( $template_path ) . $template_name );
    
//     // Bước 2: Nếu không tìm thấy ở theme, dùng template mặc định trong plugin
//     if ( ! $located ) {
//         $located = trailingslashit( $default_path ) . $template_name;
//     }
    
//     // Nếu file template tồn tại, include file đó; nếu không, thông báo lỗi.
//     if ( file_exists( $located ) ) {
//         include( $located );
//     } else {
//         echo "Template " . esc_html( $template_name ) . " không tồn tại.";
//     }
// }

function ithandech_for_wc_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
    // 1) Ép kiểu & lọc args
    $args = is_array( $args ) ? $args : array();
    $template_vars = array();
    foreach ( $args as $key => $value ) {
        // chỉ cho phép các biến dạng [a-zA-Z_][a-zA-Z0-9_]*
        if ( preg_match( '/^[a-zA-Z_][a-zA-Z0-9_]*$/', $key ) ) {
            $template_vars[ $key ] = $value;
        }
    }

    // 2) Xác định đường dẫn template
    if ( empty( $template_path ) ) {
        $template_path = 'ithan-devvn-checkout-for-woocommerce/templates';
    }
    if ( empty( $default_path ) ) {
        $default_path = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/';
    }
    $located = locate_template( trailingslashit( $template_path ) . $template_name );
    if ( ! $located ) {
        $located = trailingslashit( $default_path ) . $template_name;
    }

    // 3) Include trong closure, gán biến thủ công
    if ( file_exists( $located ) ) {
        ( function( $__file, $__vars ) {
            foreach ( $__vars as $__k => $__v ) {
                ${$__k} = $__v;
            }
            include $__file;
        } )( $located, $template_vars );
    } else {
        echo 'Template ' . esc_html( $template_name ) . ' không tồn tại.';
    }
}


function ithandech_wc_checkout_get_css_theme(array $opt){

    /*--------------------------------------------------------------
	# 1. Nạp (và cache) danh sách theme từ JSON
	--------------------------------------------------------------*/
	static $themes = null;
	if ( $themes === null ) {
		$json_file = plugin_dir_path( dirname(__FILE__) ) . 'assets/themes.json'; // điều chỉnh đường dẫn
		$json      = is_readable( $json_file ) ? file_get_contents( $json_file ) : '';
		$themes    = json_decode( $json, true ) ?: [];
	}

	/*--------------------------------------------------------------
	# 2. Xác định theme đang dùng
	--------------------------------------------------------------*/
	$scheme = $opt['preset'] ?? 'blue';       // mặc định ‘blue’
	$vars   = $themes[ $scheme ] ?? [];

	/*--------------------------------------------------------------
	# 3. Nếu người dùng chọn “custom” → dùng màu thủ công
	--------------------------------------------------------------*/

    if ( $scheme === 'custom' ) {
		$vars = [
			'--color-bg'                       => $opt['background_color']             ?? '#1e1e1e',
			'--color-surface'                  => $opt['surface_color']                ?? '#2d3c2f',
			'--color-text'                     => $opt['primary_text_color']           ?? '#d4d4d4',
			'--color-text2'                    => $opt['secondary_text_color']         ?? '#555',
			'--color-info'                     => $opt['product_info_color']           ?? '#5da5e0',
            '--color-accent'                   => $opt['accent_color']                 ?? '#007acc',
			'--color-selected'                 => $opt['selected_color']               ?? '#a4805e',
			'--color-error'                    => $opt['error_color']                  ?? '#e15858',
			'--color-price'                    => $opt['price_color']                  ?? '#e06c75',
			'--color-bg-tooltip'               => $opt['bg_tooltip_color']             ?? '#fff',
			'--color-text-tooltip'             => $opt['text_tooltip_color']           ?? '#007acc',
			'--color-raidio-checkbox-border'   => $opt['raidio_checkbox_border_color']      ?? '#333',
			'--color-raidio-checkbox-active-border'
			                                   => $opt['raidio_checkbox_border_active_color'] ?? '#38d35a',
			'--color-bg-checkout-button'       => $opt['checkout_button_bg_color']          ?? '#333',
			'--color-bg-hover-checkout-button' => $opt['checkout_button_hover_bg_color']    ?? '#38d35a',
			'--muted'                          => $opt['muted_text_color']                  ?? '#6b7280',

			'--color-divider'                 => $opt['order_ticket_devider_color']         ?? '#ddd',
            '--color-ticket-container'        => $opt['order_ticket_container_color']       ?? '#2d3c2f',
            '--color-address-circle'          => $opt['order_ticket_address_circle_color']  ?? '#7AA880',
            '--color-boder-hover-card'        => $opt['order_boder_hover_card_color']       ?? '#c026d3',
            '--color-box-shadow'              => $opt['order_box_shadow_color']             ?? '#171717',
            '--border-color'                  => $opt['order_total_border_line_color']      ?? '#e5e7eb'
		];
	}

    /*--------------------------------------------------------------
	# 4. Ghép thành chuỗi CSS
	--------------------------------------------------------------*/
	$css_lines = [];
	foreach ( $vars as $name => $value ) {
		$css_lines[] = sprintf( "%s: %s;", $name, esc_html( trim( $value ) ) );
	}

	return sprintf(
		":root {\n\t%s\n}",
		implode( "\n\t", $css_lines )
	);
}