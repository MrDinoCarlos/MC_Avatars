<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce integration for MC Avatars.
 */
class MC_Avatars_WooCommerce {

	/**
	 * Constructor.
	 *
	 * No necesitamos el helper directamente porque usamos get_avatar_url(),
	 * que ya está filtrado por el plugin.
	 */
	public function __construct() {

		// Si WooCommerce no está activo, no hacemos nada.
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		// Avatar en el panel "My account".
		add_action( 'woocommerce_before_account_navigation', [ $this, 'render_account_avatar' ] );

		// Misma preview de login (username/email) en el formulario de "My account".
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_wc_login_assets' ] );
	}

	/**
	 * Muestra la cabeza de Minecraft en el panel "My account"
	 * encima del menú (Dashboard, Orders, etc.).
	 */
	public function render_account_avatar() {

		if ( ! is_user_logged_in() ) {
			return;
		}

		$user_id = get_current_user_id();

		$avatar_url = get_avatar_url(
			$user_id,
			[
				'size' => 96, // Nuestro filtro ya la convierte en cabeza Minecraft.
			]
		);

		if ( ! $avatar_url ) {
			return;
		}

		echo '<div class="mc-avatars-wc-account-avatar" style="margin:0 0 1.5em 0;">';
		echo '<img src="' . esc_url( $avatar_url ) . '" alt="' . esc_attr__( 'Account avatar', 'avatars-for-mc-by-mrdino' ) . '" width="96" height="96" style="display:block;border-radius:4px;" />';
		echo '</div>';
	}

	/**
	 * Encola JS/CSS de preview de avatar en el formulario de login
	 * de WooCommerce (página "My account" cuando NO estás logueado).
	 */
	public function enqueue_wc_login_assets() {

		// Solo front.
		if ( is_admin() ) {
			return;
		}

		// WooCommerce activo pero por seguridad comprobamos la función.
		if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
			return;
		}

		wp_enqueue_style(
			'mc-avatars-login',
			MC_AVATARS_PLUGIN_URL . 'assets/css/admin.css',
			[],
			MC_AVATARS_VERSION
		);

		wp_enqueue_script(
			'mc-avatars-login',
			MC_AVATARS_PLUGIN_URL . 'assets/js/admin.js',
			[ 'jquery' ],
			MC_AVATARS_VERSION,
			true
		);

		wp_localize_script(
			'mc-avatars-login',
			'mcAvatarsData',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'mc_avatars_lookup' ),
			]
		);
	}
}
