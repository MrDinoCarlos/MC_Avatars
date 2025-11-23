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
		require_once MC_AVATARS_PLUGIN_DIR . 'includes/class-mc-avatars-admin.php';
		require_once MC_AVATARS_PLUGIN_DIR . 'includes/class-mc-avatars-avatar.php';
	}

	/**
	 * Register hooks.
	 */
	private function init_hooks() {
		// Textdomain: desde WP 4.6 las traducciones se cargan automáticamente
		// desde wp-content/languages/plugins usando el slug del plugin.
		$this->admin  = new MC_Avatars_Admin();
		$this->avatar = new MC_Avatars_Avatar();
	}
}
