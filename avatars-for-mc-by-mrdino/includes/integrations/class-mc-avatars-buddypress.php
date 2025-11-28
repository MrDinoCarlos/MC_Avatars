<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BuddyPress / BuddyBoss integration for MC Avatars.
 */
class MC_Avatars_BuddyPress {

	/**
	 * @var MC_Avatars_Avatar
	 */
	private $avatar_helper;

	/**
	 * Constructor.
	 *
	 * @param MC_Avatars_Avatar $avatar_helper Avatar helper instance.
	 */
	public function __construct( MC_Avatars_Avatar $avatar_helper ) {
		$this->avatar_helper = $avatar_helper;

		// Avatares globales de BuddyPress (HTML).
		add_filter( 'bp_core_fetch_avatar', [ $this, 'filter_bp_avatar' ], 99, 2 );

		// Avatares globales cuando SOLO se pide la URL (html => false).
		// Esto es lo que usa el panel "Invite Members".
		add_filter( 'bp_core_fetch_avatar_url', [ $this, 'filter_bp_avatar' ], 99, 2 );

		// Avatares del directorio de miembros (/members/).
		add_filter( 'bp_get_member_avatar', [ $this, 'filter_member_avatar' ], 10, 2 );

		// Formulario "What's new, X?".
		add_action( 'bp_enqueue_scripts', [ $this, 'override_activity_form_avatar_js' ], 20 );
	}

	/**
	 * Reemplaza el avatar de BuddyPress/BuddyBoss por el de MC Avatars.
	 *
	 * @param string $avatar  Puede ser URL o <img>.
	 * @param array  $params  Parámetros de BuddyPress (item_id, width, height, etc.).
	 *
	 * @return string
	 */
	public function filter_bp_avatar( $avatar, $params ) {

		// Valores por defecto para evitar notices.
		$params = wp_parse_args(
			$params,
			array(
				'item_id' => 0,
				'object'  => '',
				'email'   => '',
				'width'   => 0,
				'height'  => 0,
				'user_id' => 0,
			)
		);

		$object = $params['object'];

		// NO tocar avatares que NO sean de usuario.
		if ( in_array( $object, array( 'group', 'groups', 'blog', 'site', 'bp_group' ), true ) ) {
			return $avatar;
		}

		// Resolver el user_id correcto.
		$user_id = 0;

		if ( ! empty( $params['user_id'] ) ) {
			$user_id = (int) $params['user_id'];

		} elseif ( ! empty( $params['item_id'] )
			&& ( empty( $object ) || in_array( $object, array( 'user', 'member', 'members' ), true ) )
		) {
			$user_id = (int) $params['item_id'];

		} elseif ( ! empty( $params['email'] ) ) {
			$user = get_user_by( 'email', $params['email'] );
			if ( $user ) {
				$user_id = (int) $user->ID;
			}
		}

		if ( ! $user_id ) {
			return $avatar;
		}

		// Determinar tamaño.
		$size = 0;

		if ( ! empty( $params['width'] ) ) {
			$size = (int) $params['width'];
		} elseif ( ! empty( $params['height'] ) ) {
			$size = (int) $params['height'];
		} elseif ( is_string( $avatar ) && preg_match( '/width=["\']?(\d+)/i', $avatar, $m ) ) {
			$size = (int) $m[1];
		} else {
			$size = 50;
		}

		if ( $size < 32 ) {
			$size = 32;
		}

		// Conseguir la URL del avatar MC para ese usuario.
		$url = $this->avatar_helper->filter_avatar_url(
			'',
			$user_id,
			array(
				'size' => $size,
			)
		);

		if ( ! $url ) {
			return $avatar;
		}

		// Si BuddyPress ya nos da un <img>, cambiamos solo el src.
		if ( is_string( $avatar ) && false !== strpos( $avatar, '<img' ) ) {
			$updated = preg_replace(
				'/src=("|\')[^"\']*("|\')/i',
				'src="' . esc_url( $url ) . '"',
				$avatar,
				1
			);

			return ( null !== $updated ) ? $updated : $avatar;
		}

		// Si no es HTML, asumimos que es URL y la sustituimos.
		return esc_url( $url );
	}

	/**
	 * Avatares del directorio de miembros (shortcode /members/ y similares).
	 *
	 * @param string $avatar HTML <img> original.
	 * @param array  $args   Argumentos de bp_get_member_avatar().
	 *
	 * @return string
	 */
	public function filter_member_avatar( $avatar, $args ) {
		global $members_template;

		$user_id = 0;

		if ( ! empty( $members_template->member->id ) ) {
			$user_id = (int) $members_template->member->id;
		} elseif ( ! empty( $args['item_id'] ) ) {
			$user_id = (int) $args['item_id'];
		}

		if ( ! $user_id ) {
			return $avatar;
		}

		// Intentar obtener el tamaño.
		$size = 0;

		if ( ! empty( $args['width'] ) ) {
			$size = (int) $args['width'];
		} elseif ( preg_match( '/width=["\']?(\d+)/i', $avatar, $m ) ) {
			$size = (int) $m[1];
		} else {
			$size = 50;
		}

		if ( $size < 32 ) {
			$size = 32;
		}

		$url = $this->avatar_helper->filter_avatar_url(
			'',
			$user_id,
			[
				'size' => $size,
			]
		);

		if ( ! $url ) {
			return $avatar;
		}

		$updated = preg_replace(
			'/src=("|\')[^"\']*("|\')/i',
			'src="' . esc_url( $url ) . '"',
			$avatar,
			1
		);

		return ( null !== $updated ) ? $updated : $avatar;
	}

	/**
	 * Sobrescribe el avatar del formulario "What's new" en BuddyPress Nouveau.
	 */
	public function override_activity_form_avatar_js() {

		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( ! wp_script_is( 'bp-nouveau', 'enqueued' ) ) {
			return;
		}

		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return;
		}

		$size = 50;

		$url = $this->avatar_helper->filter_avatar_url(
			'',
			$user_id,
			[
				'size' => $size,
			]
		);

		if ( ! $url ) {
			return;
		}

		$current_user = wp_get_current_user();
		$alt          = sprintf(
			/* translators: %s: user display name. */
			__( 'Profile photo of %s', 'avatars-for-mc-by-mrdino' ),
			$current_user->display_name
		);

		$inline_js = 'if ( window.BP_Nouveau && BP_Nouveau.activity && BP_Nouveau.activity.params ) {' .
		             'BP_Nouveau.activity.params.avatar_url = ' . wp_json_encode( $url ) . ';' .
		             'BP_Nouveau.activity.params.avatar_alt = ' . wp_json_encode( $alt ) . ';' .
		             '}';

		wp_add_inline_script( 'bp-nouveau', $inline_js, 'after' );
	}
}
