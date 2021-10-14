<?php

namespace cdb_2021_Simply_Auto_SEO\admin;

defined( 'ABSPATH' ) or exit;

/**
 * Handles admin options page for Simply Auto SEO.
 */

class CDB_2021_Simply_Auto_SEO_Admin
{
	protected string $domain = CDB_2021_SIMPLY_AUTO_SEO_TEXT_DOMAIN;

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
	 * Add Simply Auto SEO settings page to 'Settings' in admin menu.
	 */
	public function add_action_admin_settings_page()
	{
		add_options_page(
			'Simply Auto SEO Settings',
			'Simply Auto SEO',
			'manage_options',
			'cdb-2021-simply-auto-seo-settings',
			array( $this, 'simply_auto_seo_options_page'),
		);
	}

	/**
	 * Registers settings.
	 */
	public function add_action_register_settings()
	{
		register_setting(
			'cdb_2021_simply_auto_seo_options',
			'cdb_2021_simply_auto_seo_options',
			array( $this, 'simply_auto_seo_validate_options' ),
		);
		add_settings_section(
			'cdb_2021_simply_auto_seo_section_description',
			esc_html__( 'Description Settings', $this->domain ),
			array( $this, 'settings_section_description' ),
			'cdb_2021_simply_auto_seo'
		);
		add_settings_field(
			'cdb_2021_simply_auto_seo_trim_description',
			esc_html__( 'Trim Description at', $this->domain ),
			array( $this, 'trim_description_field' ),
			'cdb_2021_simply_auto_seo',
			'cdb_2021_simply_auto_seo_section_description',
		);
	}

	/**
	 * Description for 'Trim Description at' setting.
	 */
	public function settings_section_description()
	{
		echo '<p>'
			 . __( 'Enter a word or HTML ASCII code to trim the description. Useful if description is consistently displaying non-relevant information in the description, i.e. "Read More". The entered information will be excluded from the description. Trimming takes place before translations.' )
			 . '</p>';
	}

	/**
	 * trim_description form field.
	 */
	public function trim_description_field()
	{
		$options = get_option( 'cdb_2021_simply_auto_seo_options' );
		$trim = isset( $options['trim_description'] )
				? $options['trim_description'] : '';
		?>
		<input id="cdb_2021_simply_auto_seo_trim_description"
			   name="cdb_2021_simply_auto_seo_options[trim_description]"
			   type="text"
			   value="<?php echo esc_attr( $trim ); ?>">
		<?php
	}

	/**
	 * Displays the Simply Auto SEO admin page.
	 */
	public function simply_auto_seo_options_page()
	{
		?>
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'cdb_2021_simply_auto_seo_options' );
			do_settings_sections( 'cdb_2021_simply_auto_seo' );
			submit_button();
			?>
		</form>
		<?php
	}

	/**
	 * Sanitizes submitted trim_description input.
	 * @return array $input - submitted form data.
	 */
	public function simply_auto_seo_validate_options( $input )
	{
		if ( isset( $input['trim_description'] ) ) {
			$input['trim_description'] = sanitize_text_field( 
				$input['trim_description']
			);
		}
		return $input;
	}
}