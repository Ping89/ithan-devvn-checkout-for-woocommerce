<?php
/**
 * Checkout coupon form
 *
 */

defined( 'ABSPATH' ) || exit;

if ( ! wc_coupons_enabled() ) { // @codingStandardsIgnoreLine.
	return;
}

?>

<div class="woocommerce-form-coupon-toggle">
	<div class="check-box-wrapper">
		<input id="have-coupon-code-checkbox" class="input-checkbox checkbox__have-coupon" type="checkbox" name="have-coupon-code" value="1">
		<label for="have-coupon-code-checkbox" class="checkbox__label">
		<span></span><?php esc_html_e( 'Nhấn vào nhập mã giảm giá', 'ithan-devvn-checkout-for-woocommerce' ); ?>
		</label>
	</div>
	<a href="#" class="showcoupon"></a>
</div>

<form class="checkout_coupon woocommerce-form-coupon" method="post" style="display:none">

	<div class="form-row form-row-first">
		<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'ithan-devvn-checkout-for-woocommerce' ); ?></label>
		<div class="input-container">
			<div class="input-wrapper coupon__input-wrapper">
				<span>
					<svg class="icon"><use href="#coupon"></use></svg>
				</span>
				<input type="text" name="coupon_code" class="input-text input" placeholder="<?php esc_attr_e( 'Coupon code', 'ithan-devvn-checkout-for-woocommerce' ); ?>" id="coupon_code" value="" />
			</div>
		</div>
	</div>

	<div class="form-row form-row-last">
		<button type="submit" class="button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'ithan-devvn-checkout-for-woocommerce' ); ?>"><?php esc_html_e( 'Apply coupon', 'ithan-devvn-checkout-for-woocommerce' ); ?></button>
	</div>

	<div class="clear"></div>
</form>
