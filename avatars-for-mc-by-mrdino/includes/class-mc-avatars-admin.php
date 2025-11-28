<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MC_Avatars_Admin {

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_settings_page' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	public function enqueue_assets( $hook ) {
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
	}

	/**
	 * Add submenu under "Settings".
	 */
	public function register_settings_page() {
		add_options_page(
			__( 'MC Avatars Settings', 'avatars-for-mc-by-mrdino' ),
			__( 'MC Avatars by MrDino', 'avatars-for-mc-by-mrdino' ),
			'manage_options',
			'mc-avatars-settings',
			[ $this, 'render_settings_page' ]
		);
	}

	/**
	 * Register settings & fields.
	 */
	public function register_settings() {
		register_setting(
			'mc_avatars_settings',
			'mc_avatars_skin_source',
			[
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'mc-heads',
			]
		);

		add_settings_section(
			'mc_avatars_main_section',
			__( 'General Settings', 'avatars-for-mc-by-mrdino' ),
			'__return_false',
			'mc-avatars-settings'
		);

		add_settings_field(
			'mc_avatars_skin_source',
			__( 'Skin Source', 'avatars-for-mc-by-mrdino' ),
			[ $this, 'field_skin_source' ],
			'mc-avatars-settings',
			'mc_avatars_main_section'
		);
	}

	/**
	 * Render "Skin Source" select field.
	 */
	public function field_skin_source() {
		$value = get_option( 'mc_avatars_skin_source', 'mc-heads' );
		?>
		<select name="mc_avatars_skin_source" id="mc_avatars_skin_source">
			<option value="mc-heads" <?php selected( $value, 'mc-heads' ); ?>>
				<?php esc_html_e( 'MC Heads (mc-heads.net)', 'avatars-for-mc-by-mrdino' ); ?>
			</option>
			<option value="crafatar" <?php selected( $value, 'crafatar' ); ?>>
				<?php esc_html_e( 'Crafatar (if available)', 'avatars-for-mc-by-mrdino' ); ?>
			</option>
		</select>
		<p class="description">
			<?php esc_html_e( 'Choose the main service used to load Minecraft heads.', 'avatars-for-mc-by-mrdino' ); ?>
		</p>
		<?php
	}

	/**
	 * Settings page markup.
	 */
	public function render_settings_page() {
		?>
		<div class="wrap mc-avatars-wrap">
			<h1><?php esc_html_e( 'MC Avatars Settings', 'avatars-for-mc-by-mrdino' ); ?></h1>

			<p class="mc-avatars-subtitle">
				<?php esc_html_e( 'Configure how Minecraft avatars are loaded and displayed.', 'avatars-for-mc-by-mrdino' ); ?>
			</p>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'mc_avatars_settings' );
				do_settings_sections( 'mc-avatars-settings' );
				submit_button();
				?>
			</form>

			<hr/>

			<p class="mc-avatars-footer">
				<?php
				esc_html_e(
					'MC Avatars by MrDino — If you like this plugin, you can support it here:',
					'avatars-for-mc-by-mrdino'
				);
				?>
				&nbsp;
				<a href="https://buymeacoffee.com/mrdino" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'BuyMeACoffee', 'avatars-for-mc-by-mrdino' ); ?>
				</a>
			</p>

		</div>
		<?php
	}
}
