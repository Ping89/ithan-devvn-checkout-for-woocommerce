(function ($) {
	/* IthandechCheckout.optName được WordPress bơm qua wp_localize_script() */
	if (typeof IthandechCheckout === 'undefined') {
		return; // safety‑net
	}

	const optName 				= IthandechCheckout.optName;                      // ex: 'ithandech_theme_options'
	const gender_custom_row     = $('.ith-gender-custom-row');
	const gender_radios  		= $('input[name="' + optName + '[gender_options]"]');

	const theme_custom_rows 	= $('.iththeme__opt');
	const select_theme_ctrl 	= $('select[name="' + optName + '[preset]"]')

	function gender_toggle() {
		gender_custom_row.toggle(gender_radios.filter(':checked').val() === 'custom');
	}

	function theme_custom_option_toggle(){
		theme_custom_rows.toggle(select_theme_ctrl.val() === 'custom');
	}

	$(gender_toggle);          // chạy ngay khi DOM ready
	gender_radios.on('change', gender_toggle);

	$(theme_custom_option_toggle);
	select_theme_ctrl.on('change', theme_custom_option_toggle);


})(jQuery);