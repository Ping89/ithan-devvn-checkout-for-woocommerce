<?php
/**
 * Thankyou page
 *
 *
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="woocommerce-order row ithandech__thank-you">
	<?php
	if ( $order ) :
		do_action( 'woocommerce_before_thankyou', $order->get_id() );
		?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>
			<div class="large-12 col order-failed">
				<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'ithan-devvn-checkout-for-woocommerce' ); ?></p>

				<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
					<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'ithan-devvn-checkout-for-woocommerce' ); ?></a>
					<?php if ( is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'ithan-devvn-checkout-for-woocommerce' ); ?></a>
					<?php endif; ?>
				</p>
			</div>

		<?php else : ?>

      <div class="ithandech__row payment__card">

        <?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>

      </div>

      <div class="ithandech__row ithan__grid-cols-3">
        
        <div class="ithan__card-p-4 thank__you-card premium">
          <div class="success-color woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received heading">
            <h4> <?php esc_html_e("ĐƠN HÀNG!", 'ithan-devvn-checkout-for-woocommerce'); ?></h4>
            <p>
              <?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'ithan-devvn-checkout-for-woocommerce' ), $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </p>
          </div>

          <ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">

            <li class="woocommerce-order-overview__order order">
              <?php esc_html_e( 'Order number:', 'ithan-devvn-checkout-for-woocommerce' ); ?>
              <strong><?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
            </li>

              <li class="woocommerce-order-overview__date date">
                <?php esc_html_e( 'Date:', 'ithan-devvn-checkout-for-woocommerce' ); ?>
                <strong><?php echo wc_format_datetime( $order->get_date_created() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
              </li>

              <?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
                <li class="woocommerce-order-overview__email email">
                  <?php esc_html_e( 'Email:', 'ithan-devvn-checkout-for-woocommerce' ); ?>
                  <strong><?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
                </li>
              <?php endif; ?>

            <li class="woocommerce-order-overview__total total">
              <?php esc_html_e( 'Total:', 'ithan-devvn-checkout-for-woocommerce' ); ?>
              <strong><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
            </li>

            <?php if ( $order->get_payment_method_title() ) : ?>
              <li class="woocommerce-order-overview__payment-method method">
                <?php esc_html_e( 'Payment method:', 'ithan-devvn-checkout-for-woocommerce' ); ?>
              <strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
              </li>
            <?php endif; ?>

          </ul>

          <div class="clear"></div>
        </div>

        <?php
          // We make sure the order belongs to the user. This will also be true if the user is a guest, and the order belongs to a guest (userID === 0).
          $show_customer_details = $order->get_user_id() === get_current_user_id();
          if ( $show_customer_details ) {
            ithandech_for_wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
          }
        ?>      
      </div>

      <div class="ithandech__row">
        <?php
          ithandech_for_wc_get_template( 'order/order-details.php', array( 'order_id' => $order->get_id() ) );
        ?>
      </div>

    <?php endif; ?>

	<?php else : ?>

		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'ithan-devvn-checkout-for-woocommerce' ), null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

	<?php endif; ?>

</div>