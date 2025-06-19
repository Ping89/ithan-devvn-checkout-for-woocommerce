<?php 

// Bắt đầu output buffering trước khi plugin VietQR xuất nội dung
add_action('woocommerce_thankyou_vietqr', 'ithandech_start_vietqr_buffer', 0);
function ithandech_start_vietqr_buffer($order_id) {
    ob_start();
}

// Ở hook sau cùng, dọn dẹp buffer và xuất nội dung tùy chỉnh
add_action('woocommerce_thankyou_vietqr', 'ithandech_custom_vietqr_bank_details', 100);
function ithandech_custom_vietqr_bank_details($order_id) {
    // Dọn dẹp output cũ (không in ra)
    ob_end_clean();

    // In ra nội dung custom của bạn
    echo '<div class="vietqr-custom">';
    echo wp_kses_post( ithandech_custom_vietqr_thankyou_template( $order_id ) );
    echo '</div>';
}


function ithandech_custom_vietqr_thankyou_template( $order_id ) {
    // Lấy đơn hàng
    $order = wc_get_order( $order_id );

    // Lấy giá trị prefix từ setting của bạn hoặc từ instance của gateway
    $gateway = WC()->payment_gateways()->payment_gateways()['vietqr'];
    $prefix = $gateway->prefix; // hoặc bạn gán cố định, ví dụ: 'YourPrefix'
    
    // Lấy thông tin account_details từ gateway (đây là một mảng, thường chứa phần tử đầu tiên)
    $account_details = $gateway->account_details;
    
    // Nếu đã có thông tin, tạo mảng account_fields dựa trên thông tin từ account_details
    if ( ! empty( $account_details ) && is_array( $account_details ) ) {
        // Giả sử bạn sử dụng thông tin của phần tử đầu tiên
        $data = $account_details[0];
        
        // Tạo mảng account_fields theo cấu trúc mà plugin cần
        $account_fields = array(
            'bank_name'      => array(
                'label' => __( 'Bank', 'ithan-devvn-checkout-for-woocommerce' ),
                'value' => isset( $data['bank_name'] ) ? $data['bank_name'] : '',
            ),
            'bank_id'        => array(
                'label' => __( 'Bank Id', 'ithan-devvn-checkout-for-woocommerce' ),
                'value' => isset( $data['bank_id'] ) ? $data['bank_id'] : '',
            ),
            'account_number' => array(
                'label' => __( 'Account number', 'ithan-devvn-checkout-for-woocommerce' ),
                'value' => isset( $data['account_number'] ) ? $data['account_number'] : '',
            ),
            'account_name'   => array(
                'label' => __( 'Account name', 'ithan-devvn-checkout-for-woocommerce' ),
                'value' => isset( $data['account_name'] ) ? $data['account_name'] : '',
            ),
            'amount'         => array(
                'label' => __( 'Amount', 'ithan-devvn-checkout-for-woocommerce' ),
                'value' => $order->get_total(),
            ),
            'memo'           => array(
                'label' => __( 'Memo', 'ithan-devvn-checkout-for-woocommerce' ),
                'value' => $prefix . $order_id,
            ),
        );
    } else {
        // Nếu không có thông tin, bạn có thể đặt giá trị rỗng
        $account_fields = array(
            'bank_name'      => array(
                'label' => __( 'Bank', 'ithan-devvn-checkout-for-woocommerce' ),
                'value' => '',
            ),
            'bank_id'        => array(
                'label' => __( 'Bank Id', 'ithan-devvn-checkout-for-woocommerce' ),
                'value' => '',
            ),
            'account_number' => array(
                'label' => __( 'Account number', 'ithan-devvn-checkout-for-woocommerce' ),
                'value' => '',
            ),
            'account_name'   => array(
                'label' => __( 'Account name', 'ithan-devvn-checkout-for-woocommerce' ),
                'value' => '',
            ),
            'amount'         => array(
                'label' => __( 'Amount', 'ithan-devvn-checkout-for-woocommerce' ),
                'value' => $order->get_total(),
            ),
            'memo'           => array(
                'label' => __( 'Memo', 'ithan-devvn-checkout-for-woocommerce' ),
                'value' => $prefix . $order_id,
            ),
        );
    }
    
    // Lấy URL QR code bằng cách gọi hàm của gateway (đã xử lý bên trong)
    // $data  = $gateway->get_qrcode_vietqr_img_url( $account_fields );

    $data  = ithandech_get_qrcode_vietqr_img_url( $account_fields );
    $qrcode_url = $data['img_url'];
    $qrcode_page = $data['pay_url'];
    $show_download = wp_is_mobile();
    
    // Gọi template mới của bạn (ví dụ file: order/vietqr-custom-bank-details.php)
    ithandech_for_wc_get_template( 'order/vietqr-custom-bank-details.php', array(
        'order_id'       => $order_id,
        'account_fields' => $account_fields,
        'qrcode_url'     => $qrcode_url,
        'qrcode_page'    => $qrcode_page,
        'prefix'         => $prefix,
        'show_download'  => $show_download,
    ) );
}

function ithandech_get_qrcode_vietqr_img_url($account_fields, $template_id="YPb4Nyp"){

		$accountNo = $account_fields['account_number']['value'];
		$accountName = $account_fields['account_name']['value'];
		$acqId = $account_fields['bank_id']['value'];
		$addInfo = $account_fields['memo']['value'];
		$amount = $account_fields['amount']['value'];
		$template = $template_id;

		// $img_url = "https://img.vietqr.io/{$acqId}/{$accountNo}/{$amount}/{$addInfo}/{$format}.jpg";
		$img_url = "https://img.vietqr.io/image/{$acqId}-{$accountNo}-{$template}.jpg?amount={$amount}&addInfo={$addInfo}&accountName={$accountName}";
		$pay_url = "https://api.vietqr.io/{$acqId}/{$accountNo}/{$amount}/{$addInfo}";

		return array(
			"img_url" => $img_url,
			"pay_url" => $pay_url,
		);
}