<?php
/**
 * Order details
 *
 */

 // phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if ( ! $order ) {
	
	return;
}

$order_items        = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$downloads          = $order->get_downloadable_items();
$actions            = array_filter(
	wc_get_account_orders_actions( $order ),
	function ( $action ) {
		return 'View' !== $action['name'];
	}
);

// We make sure the order belongs to the user. This will also be true if the user is a guest, and the order belongs to a guest (userID === 0).
$show_customer_details = $order->get_user_id() === get_current_user_id();

if ( isset($order) && is_a($order, 'WC_Order') ) {
    $show_downloads = $order->has_downloadable_item() && $order->is_download_permitted();
} else {
    // Nếu không có $order, mặc định false để tránh warning
    $show_downloads = false;
}

if ( $show_downloads ) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}
?>

<section class="woocommerce-order-details">
	<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

	<div class="ithan__col-lg">
	  <h2 class="woocommerce-order-details__title"><?php esc_html_e( 'Order details', 'ithan-devvn-checkout-for-woocommerce' ); ?></h2>

	  <div class="ithan__order-items-details">
	  <?php
		do_action( 'woocommerce_order_details_before_order_table_items', $order );

		foreach ( $order_items as $item_id => $item ) {
			$product = $item->get_product();

			ithandech_for_wc_get_template( 
				'order/order-details-item.php', 
				array(
					'order'              => $order,
					'item_id'            => $item_id,
					'item'               => $item,
					'show_purchase_note' => $show_purchase_note,
					'purchase_note'      => $product ? $product->get_purchase_note() : '',
					'product'            => $product,
				)
			);
		}

		do_action( 'woocommerce_order_details_after_order_table_items', $order );
		?>
	  </div>
		
		<?php
			foreach ( $order->get_order_item_totals() as $key => $total ) {
				?>
					<div class="ithan__flex-py-2-in-sum">
						<p><?php echo esc_html( $total['label'] ); ?></p>
						<p><?php echo wp_kses_post( $total['value'] ); ?></p>
					</div>
		<?php
			}
		?>
		<?php if ( $order->get_customer_note() ) : ?>
			<div class="flex justify-between py-2">
				<p><?php esc_html_e( 'Note:', 'ithan-devvn-checkout-for-woocommerce' ); ?></p>
				<p><?php echo wp_kses( nl2br( wptexturize( $order->get_customer_note() ) ), array() ); ?></p>
			</div>
		<?php endif; ?>
    </div>
	<?php
		if ( ! empty( $actions ) ) :
			?>
		<div class="ithan__col-lg">
			<h2 class="order-actions--heading"><?php esc_html_e( 'Actions', 'ithan-devvn-checkout-for-woocommerce' ); ?></h2>
			<?php
				$wp_button_class = wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '';
				foreach ( $actions as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					?>
					<div class="ithan__flex-py-2-in-sum">
						<?php
						if ( empty( $action['aria-label'] ) ) {
							// Generate the aria-label based on the action name.
							/* translators: %1$s Action name, %2$s Order number. */
							$action_aria_label = sprintf( __( '%1$s order number %2$s', 'ithan-devvn-checkout-for-woocommerce' ), $action['name'], $order->get_order_number() );
						} else {
							$action_aria_label = $action['aria-label'];
						}
							echo '<a href="' . esc_url( $action['url'] ) . '" class="woocommerce-button' . esc_attr( $wp_button_class ) . ' button ' . sanitize_html_class( $key ) . ' order-actions-button " aria-label="' . esc_attr( $action_aria_label ) . '">' . esc_html( $action['name'] ) . '</a>';
							unset( $action_aria_label );
						?>
					</div>
					<?php
				}
			?>

		</div>

	<?php endif ?>	

	<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
	
</section>