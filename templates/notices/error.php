<?php
/**
 * Show error messages
 *
 * This template can be overridden by copying it to yourtheme/ithan-devvn-checkout-customizer/notices/error.php.
 *
 */

 if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

 if ( ! $notices ) {
 	return;
 }

?>

<div class="toast__panel-wrapper">
    <div class="toast__panel-container-hold-toasts display__none" data-has-run-message="false">
        <?php foreach ( $notices as $notice ) : ?>
            <div class="notification danger" 
                <?php echo esc_html(wc_get_notice_data_attr( $notice )); ?>
                data-duration="5000"
            >   
                <i class="t-icon">
                    <svg class="icon"><use href="#icon--error"></use></svg>
                </i>
                
                <span class="t-message">
                    <?php 
                        $allowed_html = [
                            'strong' => [], // We allow <strong> with no extra attributes
                            'em'     => [], // (Optional) Let’s allow <em> too, etc.
                        ];
                        // First, wc_kses_notice() might already sanitize certain parts,
                        // but to be explicit, we call wp_kses() to preserve <strong>.
                        echo wp_kses( wc_kses_notice( $notice['notice'] ), $allowed_html );
                    ?>
                </span>
                <i class="icon-close t-close">
                    <svg class="icon"><use href="#icon--close"></use></svg>
                </i>
                <div class="t-progress-bar"></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>






