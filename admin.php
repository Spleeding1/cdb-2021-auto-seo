<?php

namespace cdb_2021_Silent_SEO\admin;

defined( 'ABSPATH' ) or exit;

/**
 * Handles admin options page for Silent SEO.
 */

class CDB_2021_Silent_SEO_Admin
{
	protected string $domain = CDB_2021_SILENT_SEO_TEXT_DOMAIN;

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

	public function add_action_admin_settings_page()
	{
		add_options_page(
			'Silent SEO Settings',
			'Silent SEO',
			'manage_options',
			'cdb-2021-silent-seo-settings',
			array( $this, 'silent_seo_options_page'),
		);
	}

	public function add_action_register_settings()
	{
		register_setting(
			'cdb_2021_silent_seo_options',
			'cdb_2021_silent_seo_options',
			array( $this, 'silent_seo_validate_options' ),
		);
		add_settings_section(
			'cdb_2021_silent_seo_section_description',
			esc_html__( 'Description Settings', $this->domain ),
			array( $this, 'settings_section_description' ),
			'cdb_2021_silent_seo'
		);
		add_settings_field(
			'cdb_2021_silent_seo_trim_description',
			esc_html__( 'Trim Description at', $this->domain ),
			array( $this, 'trim_description_field' ),
			'cdb_2021_silent_seo',
			'cdb_2021_silent_seo_section_description',
		);
	}

	public function settings_section_description()
	{
		echo '<p>'
			 . __( 'Enter a word or HTML ASCII code to trim the description. Useful if description is consistently displaying non-relevant information in the description, i.e. "Read More". Trimming takes place before translations.' )
			 . '</p>';
	}

	public function trim_description_field()
	{
		$options = get_option( 'cdb_2021_silent_seo_options' );
		$trim = isset( $options['trim_description'] )
				? $options['trim_description'] : '';
		?>
		<input id="cdb_2021_silent_seo_trim_description"
			   name="cdb_2021_silent_seo_options[trim_description]"
			   type="text"
			   value="<?php echo esc_attr( $trim ); ?>">
		<?php
	}

	public function silent_seo_options_page()
	{
		?>
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'cdb_2021_silent_seo_options' );
			do_settings_sections( 'cdb_2021_silent_seo' );
			submit_button();
			?>
		</form>
		<?php
	}

	public function silent_seo_validate_options( $input )
	{
		if ( isset( $input['trim_description'] ) ) {
			$input['trim_description'] = sanitize_text_field( 
				$input['trim_description']
			);
		}
		return $input;
	}
}