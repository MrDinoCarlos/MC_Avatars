<?php
/**
 * Plugin Name: Avatars for MC by MrDino
 * Plugin URI:  https://www.mrdino.es/mc-avatars
 * Description: Replace WordPress avatars with Minecraft-style avatars using Minecraft usernames and future custom skin features.
 * Version:     0.0.3
 * Author:      MrDino
 * Author URI:  https://www.mrdino.es
 * Donate link: https://buymeacoffee.com/mrdino
 * Text Domain: avatars-for-mc-by-mrdino
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Basic plugin constants.
define( 'MC_AVATARS_VERSION', time() );
define( 'MC_AVATARS_PLUGIN_FILE', __FILE__ );
define( 'MC_AVATARS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MC_AVATARS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MC_AVATARS_TEXT_DOMAIN', 'avatars-for-mc-by-mrdino' );

// Core class.
require_once MC_AVATARS_PLUGIN_DIR . 'includes/class-mc-avatars.php';

/**
 * Bootstrap the plugin.
 */
function mc_avatars_run() {
	MC_Avatars::get_instance();
}
mc_avatars_run();

/**
 * Enqueue assets on login / register screen (wp-login.php).
 */
function mc_avatars_enqueue_login_assets() {

	wp_enqueue_style(
		'mc-avatars-admin',
		MC_AVATARS_PLUGIN_URL . 'assets/css/admin.css',
		[],
		time()
	);

	wp_enqueue_script(
		'mc-avatars-admin',
		MC_AVATARS_PLUGIN_URL . 'assets/js/admin.js',
		[ 'jquery' ],
		time(),
		true
	);

	// Datos para AJAX (incluido nonce).
	wp_localize_script(
		'mc-avatars-admin',
		'mcAvatarsData',
		[
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'mc_avatars_lookup' ),
		]
	);
}
add_action( 'login_enqueue_scripts', 'mc_avatars_enqueue_login_assets' );

/**
 * AJAX handler para previsualizar avatar en el login.
 */
function mc_avatars_lookup_user() {

	// Verificación de nonce (seguridad).
	if (
		! isset( $_GET['_mc_avatars_nonce'] ) ||
		! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_mc_avatars_nonce'] ) ), 'mc_avatars_lookup' )
	) {
		wp_send_json_error(
			[
				'avatar'  => null,
				'message' => __( 'Invalid request.', 'avatars-for-mc-by-mrdino' ),
			],
			403
		);
	}

	// Obtenemos y saneamos el parámetro 'user' SIN usar $_GET directamente.
	$user_raw = filter_input( INPUT_GET, 'user', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

	if ( null === $user_raw || '' === $user_raw ) {
		wp_send_json(
			[
				'avatar' => null,
			]
		);
	}

	$user_val = sanitize_text_field( $user_raw );

	// Buscar por email o username.
	if ( strpos( $user_val, '@' ) !== false ) {
		$user = get_user_by( 'email', $user_val );
	} else {
		$user = get_user_by( 'login', $user_val );
	}

	// Si NO existe usuario → intentar obtener skin de Minecraft.
	if ( ! $user ) {

		$mc       = $user_val;
		$mc_lower = strtolower( $mc );

		// ¿Es un nombre de Minecraft válido?
		if ( preg_match( '/^[a-zA-Z0-9_]{3,16}$/', $mc ) ) {
			wp_send_json(
				[
					'avatar' => "https://mc-heads.net/avatar/{$mc_lower}/128",
				]
			);
		}

		// Si no, fallback Steve.
		wp_send_json(
			[
				'avatar' => 'https://mc-heads.net/avatar/MHF_Steve/128',
			]
		);
	}

	// Reutilizamos la propia lógica de avatar (filtro get_avatar_url).
	$avatar_url = get_avatar_url(
		$user->ID,
		[
			'size' => 128,
		]
	);

	wp_send_json(
		[
			'avatar' => $avatar_url,
		]
	);
}

add_action( 'wp_ajax_nopriv_mc_avatars_lookup', 'mc_avatars_lookup_user' );
add_action( 'wp_ajax_mc_avatars_lookup', 'mc_avatars_lookup_user' );