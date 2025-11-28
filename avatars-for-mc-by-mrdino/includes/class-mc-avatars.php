<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MC_Avatars {

	/**
	 * Singleton instance.
	 *
	 * @var MC_Avatars|null
	 */
	private static $instance = null;

	/** @var MC_Avatars_Admin */
	public $admin;

	/** @var MC_Avatars_Avatar */
	public $avatar;

	/** @var MC_Avatars_BuddyPress|null */
	public $buddypress = null;

	/** @var MC_Avatars_WooCommerce|null */
	private $woocommerce = null;

	/**
	 * Get singleton instance.
	 *
	 * @return MC_Avatars
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * MC_Avatars constructor.
	 */
	private function __construct() {
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Include required files.
	 */
	private function includes() {
		// Núcleo
		require_once MC_AVATARS_PLUGIN_DIR . 'includes/class-mc-avatars-admin.php';
		require_once MC_AVATARS_PLUGIN_DIR . 'includes/class-mc-avatars-avatar.php';

		// BuddyPress / BuddyBoss
		if (
			defined( 'BP_VERSION' ) ||
			function_exists( 'buddypress' ) ||
			class_exists( 'BuddyPress' )
		) {
			require_once MC_AVATARS_PLUGIN_DIR . 'includes/integrations/class-mc-avatars-buddypress.php';
		}

		// WooCommerce
		if ( class_exists( 'WooCommerce' ) ) {
			require_once MC_AVATARS_PLUGIN_DIR . 'includes/integrations/class-mc-avatars-woocommerce.php';
		}

		// Ultimate Member
		if ( class_exists( 'UM' ) || function_exists( 'um_get_requested_user' ) ) {
			require_once MC_AVATARS_PLUGIN_DIR . 'includes/integrations/class-mc-avatars-ultimate-member.php';
		}
	}

	/**
	 * Register hooks.
	 */
	private function init_hooks() {
		// Core
		$this->admin  = new MC_Avatars_Admin();
		$this->avatar = new MC_Avatars_Avatar();

		// BuddyPress / BuddyBoss
		if (
			( defined( 'BP_VERSION' ) || function_exists( 'buddypress' ) || class_exists( 'BuddyPress' ) )
			&& class_exists( 'MC_Avatars_BuddyPress' )
		) {
			$this->buddypress = new MC_Avatars_BuddyPress( $this->avatar );
		}

		// WooCommerce
		if ( class_exists( 'MC_Avatars_WooCommerce' ) ) {
			$this->woocommerce = new MC_Avatars_WooCommerce();
		}

		// Si luego quieres instanciar Ultimate Member desde aquí,
		// podemos añadirlo cuando veamos ese archivo.
	}
}
