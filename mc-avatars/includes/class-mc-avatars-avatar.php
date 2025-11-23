<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MC_Avatars_Avatar {

	public function __construct() {
		// Campos extra en el perfil.
		add_action( 'show_user_profile', [ $this, 'user_profile_fields' ] );
		add_action( 'edit_user_profile', [ $this, 'user_profile_fields' ] );
		add_action( 'personal_options_update', [ $this, 'save_user_profile_fields' ] );
		add_action( 'edit_user_profile_update', [ $this, 'save_user_profile_fields' ] );

		// Al registrar un usuario, usamos su login como mc_username por defecto.
		add_action( 'user_register', [ $this, 'set_default_mc_username_from_login' ], 10, 1 );

		// Reemplazar avatar.
		add_filter( 'get_avatar_url', [ $this, 'filter_avatar_url' ], 10, 3 );
		add_filter( 'get_avatar', [ $this, 'filter_avatar_html' ], 10, 6 );
	}

	/**
	 * Presets de cabezas predefinidas (MHF_ heads).
	 *
	 * @return array
	 */
	public static function get_presets() {
		return [
			'mhf_steve'    => [
				'label'    => 'Steve (Default Player)',
				'username' => 'MHF_Steve',
			],
			'mhf_alex'     => [
				'label'    => 'Alex (Default Player)',
				'username' => 'MHF_Alex',
			],
			'mhf_creeper'  => [
				'label'    => 'Creeper',
				'username' => 'MHF_Creeper',
			],
			'mhf_zombie'   => [
				'label'    => 'Zombie',
				'username' => 'MHF_Zombie',
			],
			'mhf_skeleton' => [
				'label'    => 'Skeleton',
				'username' => 'MHF_Skeleton',
			],
			'mhf_enderman' => [
				'label'    => 'Enderman',
				'username' => 'MHF_Enderman',
			],
		];
	}

	/**
	 * Campos del perfil: username + selector de cabezas.
	 */
	public function user_profile_fields( $user ) {
		$mc_username = get_user_meta( $user->ID, 'mc_username', true );
		$preset_key  = get_user_meta( $user->ID, 'mc_avatar_preset', true );
		?>
		<h3><?php esc_html_e( 'Minecraft Avatar', 'mc-avatars' ); ?></h3>

		<?php wp_nonce_field( 'mc_avatars_profile_update', 'mc_avatars_profile_nonce' ); ?>

		<table class="form-table" role="presentation">
			<tr>
				<th><label for="mc_username"><?php esc_html_e( 'Minecraft Username', 'mc-avatars' ); ?></label></th>
				<td>
					<input type="text"
						   name="mc_username"
						   id="mc_username"
						   value="<?php echo esc_attr( $mc_username ); ?>"
						   class="regular-text" />
					<p class="description">
						<?php esc_html_e( 'Enter your Minecraft username to use your Minecraft head as avatar.', 'mc-avatars' ); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th><label><?php esc_html_e( 'Predefined Head', 'mc-avatars' ); ?></label></th>
				<td>
					<input type="hidden"
						   name="mc_avatar_preset"
						   id="mc_avatar_preset"
						   value="<?php echo esc_attr( $preset_key ); ?>" />

					<div id="mc-avatar-preset-grid" class="mc-avatar-preset-grid">
						<!-- El grid se rellena desde admin.js, usando este hidden -->
					</div>

					<p class="description">
						<?php esc_html_e( 'Click a head to use it as your avatar. Click again to deselect and fall back to your Minecraft username (or Steve).', 'mc-avatars' ); ?>
					</p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Guardar los campos extra.
	 */
	public function save_user_profile_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		// Verificación de nonce.
		if (
			! isset( $_POST['mc_avatars_profile_nonce'] ) ||
			! wp_verify_nonce(
				sanitize_key( wp_unslash( $_POST['mc_avatars_profile_nonce'] ) ),
				'mc_avatars_profile_update'
			)
		) {
			return;
		}

		if ( isset( $_POST['mc_username'] ) ) {
			update_user_meta(
				$user_id,
				'mc_username',
				sanitize_text_field( wp_unslash( $_POST['mc_username'] ) )
			);
		}

		if ( isset( $_POST['mc_avatar_preset'] ) ) {
			$preset = sanitize_text_field( wp_unslash( $_POST['mc_avatar_preset'] ) );
			update_user_meta( $user_id, 'mc_avatar_preset', $preset );
		}
	}

	/**
	 * Cuando un usuario se registra, usar su login como mc_username por defecto
	 * y guardar el preset si viene del formulario.
	 */
	public function set_default_mc_username_from_login( $user_id ) {
		$user = get_user_by( 'id', $user_id );
		if ( ! $user ) {
			return;
		}

		$existing = get_user_meta( $user_id, 'mc_username', true );
		if ( empty( $existing ) ) {
			update_user_meta( $user_id, 'mc_username', $user->user_login );
		}

		// Solo procesar preset si viene con el nonce correcto.
		if (
			isset( $_POST['mc_avatars_profile_nonce'] ) &&
			wp_verify_nonce(
				sanitize_key( wp_unslash( $_POST['mc_avatars_profile_nonce'] ) ),
				'mc_avatars_profile_update'
			) &&
			isset( $_POST['mc_avatar_preset'] )
		) {
			$preset = sanitize_text_field( wp_unslash( $_POST['mc_avatar_preset'] ) );
			update_user_meta( $user_id, 'mc_avatar_preset', $preset );
		}
	}

	/**
	 * Reemplazar la URL del avatar por la cabeza de Minecraft.
	 */
	public function filter_avatar_url( $url, $id_or_email, $args ) {
		$user = $this->resolve_user( $id_or_email );

		if ( ! $user ) {
			return $url;
		}

		$presets    = self::get_presets();
		$preset_key = get_user_meta( $user->ID, 'mc_avatar_preset', true );

		if ( ! empty( $preset_key ) && isset( $presets[ $preset_key ] ) ) {
			$lookup_username = strtolower( $presets[ $preset_key ]['username'] );
		} else {
			$mc_username = get_user_meta( $user->ID, 'mc_username', true );
			if ( ! empty( $mc_username ) ) {
				$lookup_username = strtolower( $mc_username );
			} else {
				// Fallback: Steve oficial.
				$lookup_username = 'MHF_Steve';
			}
		}

		$source = get_option( 'mc_avatars_skin_source', 'mc-heads' );

		$size = isset( $args['size'] ) ? (int) $args['size'] : 128;
		if ( $size < 32 ) {
			$size = 32;
		}

		switch ( $source ) {
			case 'mc-heads':
				return 'https://mc-heads.net/avatar/' . rawurlencode( $lookup_username ) . '/' . $size;

			case 'crafatar':
			default:
				return 'https://crafatar.com/avatars/' . rawurlencode( $lookup_username ) .
					'?size=' . $size . '&overlay';
		}
	}

	/**
	 * Reemplazar también el HTML del avatar.
	 */
	public function filter_avatar_html( $avatar, $id_or_email, $size, $default, $alt, $args ) {
		$url = $this->filter_avatar_url( '', $id_or_email, array_merge( $args, [ 'size' => $size ] ) );

		if ( ! $url ) {
			return $avatar;
		}

		$class = isset( $args['class'] ) ? $args['class'] : 'avatar';

		return sprintf(
			'<img alt="%s" src="%s" class="%s" height="%d" width="%d" />',
			esc_attr( $alt ),
			esc_url( $url ),
			esc_attr( $class ),
			(int) $size,
			(int) $size
		);
	}

	/**
	 * Resolver un WP_User a partir de $id_or_email.
	 */
	private function resolve_user( $id_or_email ) {
		if ( is_numeric( $id_or_email ) ) {
			return get_user_by( 'id', $id_or_email );
		}

		if ( is_object( $id_or_email ) && ! empty( $id_or_email->user_id ) ) {
			return get_user_by( 'id', $id_or_email->user_id );
		}

		if ( is_string( $id_or_email ) ) {
			return get_user_by( 'email', $id_or_email );
		}

		return null;
	}
}
