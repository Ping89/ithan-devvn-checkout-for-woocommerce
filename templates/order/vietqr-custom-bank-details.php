<?php
/**
 * Template hiển thị chi tiết tài khoản VietQR
 *
 * Các biến cần truyền vào template:
 * - $order_id: ID đơn hàng.
 * - $account_fields: Mảng chi tiết tài khoản, dạng:
 *   array(
 *      'bank_name'      => array( 'label' => 'Bank', 'value' => 'Tên ngân hàng' ),
 *      'bank_id'        => array( 'label' => 'Bank Id', 'value' => 'ID ngân hàng' ),
 *      'account_number' => array( 'label' => 'Account number', 'value' => 'Số tài khoản' ),
 *      'account_name'   => array( 'label' => 'Account name', 'value' => 'Tên chủ tài khoản' ),
 *      'amount'         => array( 'label' => 'Amount', 'value' => 100000 ),
 *      'memo'           => array( 'label' => 'Memo', 'value' => 'Prefix'. $order_id ),
 *   );
 * - $qrcode_url: URL hình ảnh QR code.
 * - $qrcode_page: URL trang thanh toán (nếu có).
 * - $prefix: Giá trị tiền tố dùng trong memo.
 * - $show_download: true/false để hiển thị nút download QR (thường dựa trên việc khách hàng đang dùng mobile hay không).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="wc-vietqr-bank-details-wrapper ticket-container" style="font-family: sans-serif; padding: 20px;">

    <!-- Ticket Header -->
    <div class="ticket-header">
        <div class="ticket-logo"><?php esc_html_e( 'Mã QR', 'ithan-devvn-checkout-for-woocommerce' ); ?></div>
        <div class="ticket-code">
            <?php echo esc_html( $account_fields['memo']['label'] ); ?>
            <strong><?php echo wp_kses_post( wptexturize( $account_fields['memo']['value'] ) ); ?></strong>
        </div>
    </div>

    <!-- Phần hiển thị QR code -->
    <section class="woocommerce-vietqr-qr-scan ticket-qr" >        
        <div id="qrcode">
            <img src="<?php echo esc_url( $qrcode_url ); ?>" alt="<?php esc_attr_e( 'VietQR QR Image', 'ithan-devvn-checkout-for-woocommerce' ); ?>" width="400" />
        </div>

        <?php if ( $show_download ) : ?>
            <div class="download-button qr-code-download">
                <a href="<?php echo esc_url( $qrcode_page ); ?>" target="_blank">
                    <span class="qr-code-download__label"><?php esc_html_e( 'Tải QR Code', 'ithan-devvn-checkout-for-woocommerce' ); ?></span>
                    <span>
						<svg class="icon download"><use href="#download-btn"></use></svg>
					</span>
                </a>
            </div>
        <?php endif; ?>

        <div class="download-link-mobile">
            <a id="downloadQR" download="vietqr_<?php echo esc_attr( $account_fields['account_number']['value'] ); ?>.jpg" href="<?php echo esc_url( $qrcode_url ); ?>" style="display: none;">
                <button id="btnDownloadQR" style="padding: 10px 20px; border: none; background-color: #0274be; color: #fff; cursor: pointer;">
                    <?php esc_html_e( 'LƯU ẢNH QR', 'ithan-devvn-checkout-for-woocommerce' ); ?>
                </button>
            </a>
        </div>
    </section>

    <!-- Phần hiển thị thông tin tài khoản ngân hàng -->
    <section class="woocommerce-vietqr-bank-details">

        <!-- Ticket Information -->
        <div class="ticket-info">
            <h2 class="wc-vietqr-bank-details-heading" ><?php esc_html_e( 'Our bank details', 'ithan-devvn-checkout-for-woocommerce' ); ?></h2>
            <h4>
            <?php
                echo wp_kses_post( sprintf(
                    // translators: %s is the payment content including order prefix and ID (e.g., "PAY12345")
                    __( 'Please transfer the correct content <b>%s</b> for we can confirm the payment', 'ithan-devvn-checkout-for-woocommerce' ),
                    esc_html( $prefix . $order_id )
                ) );
            ?>

            </h4>

        </div>

        <!-- Divider with circles -->
        <div class="ticket-divider-container">
            <div class="ticket-divider-left-circle"></div>
            <div class="ticket-divider"></div>
            <div class="ticket-divider-right-circle"></div>
        </div>
        
        <div class="bank-details-container" >
        <?php foreach ( $account_fields as $field_key => $field ) : ?>
            <?php if ( ! empty( $field['value'] ) ) : ?>
                <div class="bank-detail-item">
                    <span class="bank-detail-label"><?php echo esc_html( $field['label'] ); ?>:</span>
                    <span class="bank-detail-value">
                        <?php if ( 'amount' === $field_key ) : ?>
                            <?php echo wp_kses_post( wc_price( $field['value'] ) ); ?>
                        <?php else : ?>
                            <?php echo wp_kses_post( wptexturize( $field['value'] ) ); ?>
                        <?php endif; ?>
                    </span>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        </div>
    </section>
</div>

<style>
    /* Responsive layout: trường hợp màn hình nhỏ */
    @media (max-width: 600px) {
        .bank-detail-item {
            width: 100%;
        }
    }
</style>
