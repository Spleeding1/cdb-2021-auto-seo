<?php

namespace {{NameSpace}}\admin;

defined( 'ABSPATH' ) or exit;

/**
 * Handles admin options page for {{Plugin Name}}.
 * 
 * replace with filecopy
 */

class {{PluginClass}}_Admin
{
	protected string $domain = {{PLUGIN_PREFIX}}_TEXT_DOMAIN;

	public function __construct()
	{
		add_action(
			'admin_menu',
			array( $this, 'add_action_admin_settings_page' )
		);
		add_action(
			'admin_init',
			array( $this, 'add_action_register_settings' )
		);
	}

	/**
	 * Add {{PLUGIN_NAME}} settings page to 'Settings' in admin menu.
	 */
	public function add_action_admin_settings_page()
	{
		add_options_page(
			'{{Plugin Name}} Settings',
			'{{Plugin Name}}',
			'manage_options',
			'{{plugin-slug}}-options',
			array( $this, 'options_page'),
		);
	}

	/**
	 * Registers settings.
	 */
	public function add_action_register_settings()
	{
		register_setting(
			'{{plugin_snake}}_options',
			'{{plugin_snake}}_options',
			array( $this, 'validate_options' ),
		);
		add_settings_section(
			'{{plugin_snake}}_section_uninstall',
			esc_html__( 'Uninstall Settings', $this->domain ),
			array( $this, 'settings_section_uninstall' ),
			'{{plugin-slug}}-options'
		);
		add_settings_field(
			'{{plugin_snake}}_uninstall_delete_all_data',
			esc_html__( 'Delete all plugin data on uninstall', $this->domain ),
			array( $this, 'uninstall_delete_all_data_field' ),
			'{{plugin-slug}}-options',
			'{{plugin_snake}}_section_uninstall',
		);
	}

	/**
	 * Description for 'Delete all data' setting.
	 */
	public function settings_section_uninstall()
	{
		echo '<p>'
			 . __( 'Check if you want to delete all plugin data when plugin is uninstalled.' )
			 . '</p>';
	}

	/**
	 * uninstall_delete_all_data form field.
	 */
	public function uninstall_delete_all_data_field()
	{
		$options = get_option( '{{plugin_snake}}_options' );
		$delete_all_data = isset( $options['uninstall_delete_all_data'] )
				? $options['uninstall_delete_all_data'] : false;
		?>
		<input id="{{plugin_snake}}_uninstall_delete_all_data"
			   name="{{plugin_snake}}_options[uninstall_delete_all_data]"
			   type="checkbox"
			   <?php checked( $delete_all_data, true, true ); ?>>
		<?php
	}

	/**
	 * Displays the {{Plugin Name}} admin page.
	 */
	public function options_page()
	{
		?>
		<h1><?php echo esc_html__( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( '{{plugin_snake}}_options' );
			do_settings_sections( '{{plugin-slug}}-options' );
			submit_button();
			?>
		</form>
		<?php
	}

	/**
	 * Sanitizes submitted options input.
	 * @return array $input - submitted form data.
	 */
	public function validate_options( $input )
	{
		$input['uninstall_delete_all_data'] = isset( $input['uninstall_delete_all_data'] ) ? true : false;
		return $input;
	}
}