<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ultimate Member integration for MC Avatars.
 *
 * Objetivo:
 * - Reemplazar las fotos de perfil de Ultimate Member por nuestro avatar de Minecraft.
 * - Sin tocar nada del resto del plugin.
 */
class MC_Avatars_Ultimate_Member {

	/**
	 * @var MC_Avatars_Avatar
	 */
	private $avatar_helper;

	/**
	 * Constructor.
	 *
	 * @param MC_Avatars_Avatar $avatar_helper Instancia del helper de avatares.
	 */
	public function __construct( MC_Avatars_Avatar $avatar_helper ) {
		$this->avatar_helper = $avatar_helper;

		// Si UM no está activo, no hacemos nada.
		if ( ! function_exists( 'UM' ) ) {
			return;
		}

		/**
		 * 1) Filtro genérico de URL de avatar de UM.
		 *
		 *   apply_filters( 'um_user_avatar_url_filter', $url, $user, $size );
		 *
		 * Nosotros soportamos varias formas de segundo parámetro
		 * (ID, objeto, array) para estar seguros.
		 */
		add_filter( 'um_user_avatar_url_filter', [ $this, 'filter_um_avatar_url' ], 20, 3 );

		/**
		 * 2) HTML de la foto de perfil en distintos contextos.
		 *
		 * Estos filtros existen en UM y devuelven normalmente un <img>.
		 * Aunque el nombre exacto pueda variar según versiones/extensiones,
		 * enganchar aquí es seguro (si no se usan, simplemente no se llamarán).
		 */
		add_filter( 'um_profile_photo',                 [ $this, 'filter_um_profile_photo' ], 20, 2 );
		add_filter( 'um_account_user_photo',            [ $this, 'filter_um_profile_photo' ], 20, 2 );
		add_filter( 'um_member_directory_user_photo',   [ $this, 'filter_um_profile_photo' ], 20, 2 );
	}

	/**
	 * Saca un user_id desde lo que nos pase Ultimate Member.
	 *
	 * @param mixed $user Puede ser ID, objeto WP_User, array con 'ID' o 'user_id', etc.
	 *
	 * @return int
	 */
	private function resolve_user_id( $user ) {

		if ( is_numeric( $user ) ) {
			return (int) $user;
		}

		if ( $user instanceof WP_User ) {
			return (int) $user->ID;
		}

		if ( is_array( $user ) ) {
			if ( isset( $user['ID'] ) ) {
				return (int) $user['ID'];
			}
			if ( isset( $user['user_id'] ) ) {
				return (int) $user['user_id'];
			}
		}

		if ( is_object( $user ) ) {
			if ( ! empty( $user->ID ) ) {
				return (int) $user->ID;
			}
			if ( ! empty( $user->user_id ) ) {
				return (int) $user->user_id;
			}
		}

		return 0;
	}

	/**
	 * Devuelve solo una URL de avatar Minecraft para un user_id y tamaño.
	 *
	 * @param int $user_id
	 * @param int $size
	 *
	 * @return string|null
	 */
	private function get_mc_avatar_url( $user_id, $size = 96 ) {

		if ( ! $user_id ) {
			return null;
		}

		$url = $this->avatar_helper->filter_avatar_url(
			'',
			$user_id,
			[
				'size' => (int) $size,
			]
		);

		return $url ?: null;
	}

	/**
	 * Filtro de URL que usa Ultimate Member internamente.
	 *
	 * @param string $url   URL de avatar calculada por UM.
	 * @param mixed  $user  Puede ser ID, objeto usuario, array...
	 * @param int    $size  Tamaño solicitado.
	 *
	 * @return string
	 */
	public function filter_um_avatar_url( $url, $user, $size = 96 ) {

		$user_id = $this->resolve_user_id( $user );

		if ( ! $user_id ) {
			return $url;
		}

		$mc_url = $this->get_mc_avatar_url( $user_id, $size );

		return $mc_url ?: $url;
	}

	/**
	 * Filtro para el HTML de la foto de perfil que pinta UM.
	 *
	 * En muchos sitios UM construye su propio <img>; aquí lo sustituimos
	 * por un get_avatar() que ya pasa por nuestros filtros globales.
	 *
	 * @param string $html    HTML original (<img> de UM).
	 * @param mixed  $user_id Puede ser ID u otra cosa según el hook.
	 *
	 * @return string
	 */
	public function filter_um_profile_photo( $html, $user_id ) {

		$user_id = $this->resolve_user_id( $user_id );

		if ( ! $user_id ) {
			return $html;
		}

		// Usamos get_avatar() para respetar toda la lógica central de MC Avatars.
		$avatar_html = get_avatar(
			$user_id,
			96,
			'',
			'',
			[
				'class' => 'um-avatar mc-avatars-um-avatar',
			]
		);

		return $avatar_html ?: $html;
	}
}
