<?php
/**
 * Pay for order form
 * 
 */

defined( 'ABSPATH' ) || exit;

$totals = $order->get_order_item_totals(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
?>
<form id="order_review" class="ithan__payment-recieved" method="post">

<div class="cart">

	<?php if ( count( $order->get_items() ) > 0 ) : ?>
		<ul class="cartWrap">
			<li class="items">
			
				<div class="infoWrap">
					<div class="prodTotal cartSection">
						<p><?php esc_html_e("Tổng", 'ithan-devvn-checkout-for-woocommerce'); ?></p>
					</div>
				</div>

			</li>
			
			<?php 
				$index = 1; // Khởi tạo biến đếm
				$odd_even = ['odd', 'even']; // Mảng chứa 2 giá trị

				foreach ( $order->get_items() as $item_id => $item ) : 
					$item_class = $odd_even[$index % 2]; // Lấy 'even' nếu chia hết cho 2, ngược lại 'odd'

					$class_names = implode( ' ', array(
						'items',
						esc_attr( $item_class ),
						esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ),
					) );
			?>
				<?php
				if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
					continue;
				}
				?>
				<li class="items <?php echo esc_attr( $class_names );?>">
					<div class="infoWrap"> 
						<div class="cartSection">
							<?php 
								$product = $item->get_product(); // Lấy đối tượng WC_Product
								if (!$product) {
									continue; // Bỏ qua nếu sản phẩm không tồn tại
								}
								
								// Lấy URL hình ảnh chính (Featured Image)
								$thumbnail_url = $product->get_image_id() ? wp_get_attachment_image_url($product->get_image_id(), 'thumbnail') : wc_placeholder_img_src();
							?>
							<img src="<?php echo esc_attr($thumbnail_url); ?>" alt="" class="itemImg" />
							<p class="itemNumber">
								<?php esc_html_e( 'ID:', 'ithan-devvn-checkout-for-woocommerce' ); ?> <?php echo esc_html( $product->get_id() ); ?>
							</p>

							<h3>
								<?php
									echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );
								?>
							</h3>
							
							<p> 
								<?php echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity qty">' . sprintf( '&times;&nbsp;%s', esc_html( $item->get_quantity() ) ) . '</strong>', $item ); ?><?php // @codingStandardsIgnoreLine ?>
							</p>

							<div class="variant__info out">
								<?php 
									do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

									wc_display_item_meta( $item );

									do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
								?>
							</div>
						</div>  
					
						<div class="prodTotal cartSection">
							<p>
								<?php echo $order->get_formatted_line_subtotal( $item ); ?><?php // @codingStandardsIgnoreLine ?>
							</p>
						</div>
					</div>
				</li>
			<?php $index++;
				endforeach; ?>

			<?php if ( $totals ) : ?>
				<?php foreach ( $totals as $total ) : ?>
					<li class="items">
						<div class="infoWrap">
							<div class="cartSection"><?php echo $total['label']; ?></div><?php // @codingStandardsIgnoreLine ?>
							<div class="product-total prodTotal cartSection"><?php echo $total['value']; ?></div><?php // @codingStandardsIgnoreLine ?>
						</div>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>

		</ul>
	<?php endif; ?>
  </div>

	<?php
	/**
	 * Triggered from within the checkout/form-pay.php template, immediately before the payment section.
	 *
	 * @since 8.2.0
	 */
	do_action( 'woocommerce_pay_order_before_payment' ); 
	?>

	<div id="payment">
		<?php if ( $order->needs_payment() ) : ?>
			<ul class="wc_payment_methods payment_methods methods">
				<?php
				if ( ! empty( $available_gateways ) ) {
					foreach ( $available_gateways as $gateway ) {
						wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
					}
				} else {
					echo '<li>';
					wc_print_notice( apply_filters( 'woocommerce_no_available_payment_methods_message', esc_html__( 'Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'ithan-devvn-checkout-for-woocommerce' ) ), 'notice' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
					echo '</li>';
				}
				?>
			</ul>
		<?php endif; ?>
		<div class="form-row">
			<input type="hidden" name="woocommerce_pay" value="1" />

			<?php wc_get_template( 'checkout/terms.php' ); ?>

			<?php do_action( 'woocommerce_pay_order_before_submit' ); ?>

			<?php echo apply_filters( 'woocommerce_pay_order_button_html', '<button type="submit" class="button alt' . esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ) . '" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // @codingStandardsIgnoreLine ?>

			<?php do_action( 'woocommerce_pay_order_after_submit' ); ?>

			<?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
		</div>
	</div>
</form>
