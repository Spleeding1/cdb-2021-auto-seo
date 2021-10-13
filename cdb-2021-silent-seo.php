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

namespace cdb_2021_Silent_Seo;

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

		// add_options_page( 'Silent SEO Settings', 'Silent SEO', )

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
			echo '<meta name="description" content="'
			. strip_tags( $description ) . '" >' . "\n";
			echo '<meta property="og:description" content="'
			. strip_tags( $description ) . '" >' . "\n";
		}
		
		if ( $title = get_the_title() ) {
			echo '<meta property="og:description" content="'
			. strip_tags( $description ) . '" >' . "\n";
		}
		
		echo '<meta property="og:type" content="website" >' . "\n";
		
		echo '<meta property="og:url" content="'
		. home_url( $wp->request ) . '" >' . "\n";
		
		echo '<meta property="og:site_name" content="'
		. get_bloginfo( 'name' ) . '" >' . "\n";
		echo '<meta property="og:site_name" content="'
		. get_bloginfo( 'language' ) . '" >' . "\n";
		
	}
}

if ( class_exists( 'cdb_2021_Silent_Seo\CDB_2021_SILENT_SEO' ) ) {
	$silent_seo = new CDB_2021_SILENT_SEO( $prefix );
}
