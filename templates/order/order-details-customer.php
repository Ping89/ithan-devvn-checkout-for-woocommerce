<?php
/**
 * Order Customer Details
 *
 */

defined( 'ABSPATH' ) || exit;

$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>

<section class="woocommerce-customer-details">
	<?php if ( $show_shipping ) : ?>

	<section class="addresses">
		<div class="ithan__card-p-4 ithan__shadow-md namecard">

	<?php endif; ?>

		<h2 class="woocommerce-column__title"><?php esc_html_e( 'Billing address', 'ithan-devvn-checkout-for-woocommerce' ); ?></h2>
		<address>
			<h2><?php echo esc_html( $order->get_billing_last_name() ); ?><span>（<?php echo esc_html( $order->get_billing_first_name() ); ?>）</span></h2>
			<h5><?php echo esc_html( $order->get_billing_city() ); ?></h5>
			<hr />
			<p><?php echo esc_html( $order->get_billing_address_1() ); ?><br /><?php echo esc_html( $order->get_billing_address_2() ); ?></p>

			<?php if ( $order->get_billing_phone() ) : ?>
				<p class="woocommerce-customer-details--phone phone">
					<span>
						<svg class="icon phone"><use href="#phone"></use></svg>
					</span>
					<?php echo esc_html( $order->get_billing_phone() ); ?>
				</p>
			<?php endif; ?>

			<?php if ( $order->get_billing_email() ) : ?>
				<p class="woocommerce-customer-details--email email">
					<span>
						<svg class="icon email"><use href="#envelope"></use></svg>
					</span>
					<?php echo esc_html( $order->get_billing_email() ); ?>
				</p>
			<?php endif; ?>

			<?php
				/**
				 * Action hook fired after an address in the order customer details.
				 *
				 * @since 8.7.0
				 * @param string $address_type Type of address (billing or shipping).
				 * @param WC_Order $order Order object.
				 */
				do_action( 'woocommerce_order_details_after_customer_address', 'billing', $order );
			?>

			<div class="circle circle1"></div>
			<div class="circle circle2"></div>
		</address>

	<?php if ( $show_shipping ) : ?>

		</div><!-- /.col-1 -->

		<div class="woocommerce-column woocommerce-column--2 woocommerce-column--shipping-address col-2 ithan__card-p-4 ithan__shadow-md namecard">
			<h2 class="woocommerce-column__title"><?php esc_html_e( 'Shipping address', 'ithan-devvn-checkout-for-woocommerce' ); ?></h2>
			<address>
				<h2><?php echo esc_html( $order->get_shipping_last_name() ); ?><span>（<?php echo esc_html( $order->get_shipping_first_name() ); ?>）</span></h2>
				<h5><?php echo esc_html( $order->get_shipping_city() ); ?></h5>
				<hr />
				<p><?php echo esc_html( $order->get_shipping_address_1() ); ?><br /><?php echo esc_html( $order->get_shipping_address_2() ); ?></p>

				<?php if ( $order->get_shipping_phone() ) : ?>
					<p class="woocommerce-customer-details--phone phone">
						<span>
							<svg class="icon phone"><use href="#phone"></use></svg>
						</span>
						<?php echo esc_html( $order->get_shipping_phone() ); ?>
					</p>
				<?php endif; ?>

				<?php
					/**
					 * Action hook fired after an address in the order customer details.
					 *
					 * @since 8.7.0
					 * @param string $address_type Type of address (billing or shipping).
					 * @param WC_Order $order Order object.
					 */
					do_action( 'woocommerce_order_details_after_customer_address', 'shipping', $order );
				?>

				<div class="circle circle1"></div>
				<div class="circle circle2"></div>
			</address>
		</div><!-- /.col-2 -->

	</section><!-- /.col2-set -->

	<?php endif; ?>

	<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>

</section>
