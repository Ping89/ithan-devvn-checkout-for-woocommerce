<?php
/**
 * "Order received" message.
 *
 */

defined( 'ABSPATH' ) || exit;
?>

<p class="success-color woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">
	<?php
	/**
	 * Filter the message shown after a checkout is complete.
	 *
	 * @since 2.2.0
	 *
	 * @param string         $message The message.
	 * @param WC_Order|false $order   The order created during checkout, or false if order data is not available.
	 */
	$message = apply_filters(
		'woocommerce_thankyou_order_received_text',
		esc_html( __( 'Thank you. Your order has been received.', 'ithan-devvn-checkout-for-woocommerce' ) ),
		$order
	);

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<strong>' . $message . '</strong>';
	?>
</p>
