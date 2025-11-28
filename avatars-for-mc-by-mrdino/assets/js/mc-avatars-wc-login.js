(function ($) {
	'use strict';

	function setupWooLoginPreview() {
		// Campo "Username or email address" del formulario de WooCommerce.
		var $field = $('form.woocommerce-form-login input[name="username"]');

		if (!$field.length) {
			return;
		}

		var $img = $('#mc-avatars-wc-login-preview');

		if (!$img.length) {
			return;
		}

		function updatePreview() {
			var value = $.trim($field.val());

			if (!value) {
				$img.attr('src', MC_Avatars_WC_Login.fallback);
				return;
			}

			$.post(
				MC_Avatars_WC_Login.ajaxurl,
				{
					action: 'mc_avatars_login_preview', // mismo endpoint que ya usas en el login de WP
					nonce: MC_Avatars_WC_Login.nonce,
					login: value // puede ser username o email, igual que en WP
				},
				function (response) {
					if (
						response &&
						response.success &&
						response.data &&
						response.data.avatar
					) {
						$img.attr('src', response.data.avatar);
					} else {
						$img.attr('src', MC_Avatars_WC_Login.fallback);
					}
				}
			);
		}

		// Misma UX que en el login de WP: se actualiza mientras escribes.
		$field.on('input blur', updatePreview);

		// Primera llamada por si el navegador autocompleta.
		updatePreview();
	}

	$(document).ready(setupWooLoginPreview);
})(jQuery);
