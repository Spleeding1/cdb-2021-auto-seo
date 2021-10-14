<?php

/**
 * Plugin Name: Silent SEO.
 * Plugin URI: The home page of the plugin, which should be a unique URL, preferably on your own website. This must be unique to your plugin. You cannot use a WordPress.org URL here.
 * Description: Automatically adds SEO tags to <head>. Does not display any field inputs in WordPress Editor. name="description" can be edited through post excerpts and taxonomy descriptions.
 * Version: 0.0.1
 * Requires at least: 5.8.1
 * Requires PHP: 7.4
 * Author: Carl David Brubaker
 * Author URI: The author’s website or profile on another website, such as WordPress.org.
 * License: GPLv2 (or later)
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: CDB_2021_SILENT_SEO
 * Domain Path: /languages    The domain path lets WordPress know where to find the translations. More information can be found in the Domain Path section of the How to Internationalize your Plugin page.
 * Network: Whether the plugin can only be activated network-wide. Can only be set to true, and should be left out when not needed.
 */

namespace cdb_2021_Silent_SEO;

defined( 'ABSPATH' ) or exit;

$prefix = 'CDB_2021_SILENT_SEO';

if ( ! defined( $prefix . '_PATH' ) ) {
	define( $prefix . '_PATH', plugin_dir_path(__FILE__) );
}

if ( ! defined($prefix . '_VERSION') ) {
	define( $prefix . '_VERSION', '0.0.1' );
}

if ( ! defined( $prefix . '_TEXT_DOMAIN') ) {
	define( $prefix . '_TEXT_DOMAIN', 'Text Domain' );
}

/**
 * File Imports
 */
if ( is_admin() ) {
	require_once 
}

class CDB_2021_SILENT_SEO
{	
	protected string $domain = CDB_2021_SILENT_SEO_TEXT_DOMAIN;
	protected string $version = CDB_2021_SILENT_SEO_VERSION;
	protected string $prefix;

	public function __construct( string $prefix )
	{
		$this->prefix = $prefix;

		add_action( 'init', array( $this, 'activateOrUpdate' ) );
		add_action( 'wp_head', array( $this, 'action_add_seo_meta_tags' ) );
		add_action( 'admin_menu', array( $this, 'add_action_admin_settings' ) );
		add_action(
			'admin_init',
			array( $this, 'add_action_register_settings' )
		);
	}
	
	/**
	 * Perform actions if wp_option(CDB_2021_SILENT_SEO_VERSION) does not match $this->version.
	 */
	public function activateOrUpdate()
	{
		if ( ! $this->pluginVersionOptionIsTheLatest() ) {
			// Do stuff if version has changed.
			
			flush_rewrite_rules();
		}
	}
	
	/**
	 * Checks stored version in the options table and updates as necessary.
	 * @return bool
	 * true if plugin version matches stored version.
	 * false if plugin version does not match or no option stored.
	 */
	public function pluginVersionOptionIsTheLatest()
	{
		$option_value = get_option( $this->prefix . '_VERSION' );
		
		if ( $option_value === $this->version ) {
			return true;
		}
		
		if ( $option_value === false ) {
			add_option( $this->prefix . '_VERSION', $this->version );
		} else {
			update_option( $this->prefix . '_VERSION', $this->version );
		}
		
		return false;
	}
	
	/**
	 * Adds <meta name="description"> to head if current page. 
	 * 
	 * is_singular - will display entered excerpt or pull from page content.
	 *  is_category, is_tag, is_author, is_post_type_archive, is_tax - will only display if description is set.
	 */
	public function action_add_seo_meta_tags()
	{
		if ( ! get_post_status() === 'public' ) {
			// return;
		}
		
		global $wp;
		$description = null;
		
		if ( is_singular() || is_front_page() ) {
			$description = explode( '&hellip;', get_the_excerpt() )[0] 
						   . '&hellip;';
		} else if ( is_category() || is_tag() || is_author()
		|| is_post_type_archive() || is_tax()) {
			$description = get_the_archive_description();
		}
		
		// Print meta description in head if $desc is not null.
		if ( $description ) {
			$description = strip_tags( $description );
			?>
			<meta name="description"
				  content="<?php echo esc_attr_e( $description ); ?>">
			<meta property="og:description"
				  content="<?php echo esc_attr_e( $description ); ?>">
			<?php
		}
		
		if ( $title = get_the_title() ) {
			?>
			<meta property="og:description"
				  content="<?php echo esc_attr_e( $description ); ?>">
			<?php
		}
		?>
		<meta property="og:type" content="website">
		<meta property="og:url"
			  content="<?php echo esc_attr( home_url( $wp->request ) ); ?>">
		<meta property="og:site_name"
			  content="<?php echo esc_attr_e( get_bloginfo( 'name' ) ); ?>">
		<meta property="og:site_name"
			  content="<?php echo esc_attr_e( get_bloginfo( 'language' ) ); ?>">
		<?php
	}

	public function add_action_admin_settings()
	{
		add_options_page(
			'Silent SEO Settings',
			'Silent SEO',
			'manage_options',
			'cdb-2021-silent-seo-settings',
			array( $this, 'silent_seo_options_page'),
		);
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

if ( class_exists( 'cdb_2021_Silent_SEO\CDB_2021_SILENT_SEO' ) ) {
	$silent_seo = new CDB_2021_SILENT_SEO( $prefix );
}
