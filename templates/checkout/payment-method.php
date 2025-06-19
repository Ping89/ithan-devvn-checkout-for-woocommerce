<?php
/**
 * Output a single payment method
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?> payment__list-item">
	<div class="payment-radio-wrapper">
		<input 
			id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" 
			class="input-radio payment-method-radio" 
			name="payment_method" 
			value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />

			<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>" class="payment-label">
				<span></span>
				<?php echo esc_html( $gateway->get_title() ); ?>
				<?php echo wp_kses_post( $gateway->get_icon() ); ?>
			</label>

	</div>
	
	<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
		<div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php if ( ! $gateway->chosen ) : /* phpcs:ignore Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace */ ?>style="display:none;"<?php endif; /* phpcs:ignore Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace */ ?>>
			<?php $gateway->payment_fields(); ?>
		</div>
	<?php endif; ?>
</li>
