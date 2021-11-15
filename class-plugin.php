<?php

Find and Replace:
{{Plugin Name}}
{{PLUGIN_PREFIX}}
{{NameSpace}}
{{PluginClass}}
{{plugin_snake}}
{{plugin-slug}}

/**
 * Plugin Name: {{Plugin Name}} (required) The name of your plugin, which will be displayed in the Plugins list in the WordPress Admin.
 * Plugin URI: The home page of the plugin, which should be a unique URL, preferably on your own website. This must be unique to your plugin. You cannot use a WordPress.org URL here.
 * Description: A short description of the plugin, as displayed in the Plugins section in the WordPress Admin. Keep this description to fewer than 140 characters.
 * Version: 0.0.1
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Carl David Brubaker
 * Author URI: https://carlbrubaker.com/
 * License: GPLv2 (or later)
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: {{PLUGIN_PREFIX}}   The gettext text domain of the plugin. More information can be found in the Text Domain section of the How to Internationalize your Plugin page.
 * Domain Path: /languages    The domain path lets WordPress know where to find the translations. More information can be found in the Domain Path section of the How to Internationalize your Plugin page.
 * Network: Whether the plugin can only be activated network-wide. Can only be set to true, and should be left out when not needed.
 */

/**
 * filecopy
 */

namespace {{NameSpace}};

defined( 'ABSPATH' ) or exit;

$prefix = '{{PLUGIN_PREFIX}}';

if ( ! defined( $prefix . '_PATH' ) ) {
	define( $prefix . '_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( $prefix . '_VERSION' ) ) {
	define( $prefix . '_VERSION', '0.0.1' );
}

if ( ! defined( $prefix . '_TEXT_DOMAIN' ) ) {
	define( $prefix . '_TEXT_DOMAIN', 'Text Domain' );
}

class {{PluginClass}}
{
	/**
	 * Plugin prefix defined above. Sets when class is contructed.
	 */
	protected string $prefix;

	/**
	 * Plugin text domain defined globally.
	 */
	protected string $domain = {{PLUGIN_PREFIX}}_TEXT_DOMAIN;

	/**
	 * Plugin version defined globally.
	 */
	protected string $version = {{PLUGIN_PREFIX}}_VERSION;

	/**
	 * Names of plugin transients stored in the transients table.
	 * 
	 * Used to delete_transient on uninstall.
	 * - List all possible options that can be set to ensure proper cleanup.
	 * 
	 * Can be used to set_transient if 'update' => true.
	 * 
	 * $this->prefix is added to key on creation and deletion.
	 * 
	 * example:
	 * 
	 * $prefix = 'my_plugin';
	 * $transients = array(
	 *     'my_transient' => array(
	 *         'value' => 'My Value',
	 *         'update' => true),
	 * );
	 */
	protected array  $transients = array();

	public function __construct( string $prefix )
	{
		$this->prefix = $prefix;

		add_action( 'init', array( $this, 'activateOrUpdate' ) );
	}
	
	
	/**
	 * Perform actions if $options['version'] does not match $this->version.
	 */
	public function activateOrUpdate()
	{
		if ( ! $this->pluginVersionOptionIsTheLatest() ) {
			// Do stuff if version has changed.
			$this->updatePluginTransients();
			
			flush_rewrite_rules();
		}
	}
	
	/**
	 * Checks stored version in the options table and updates as necessary.
	 * @return bool
	 * true if plugin version matches stored version.
	 * false if plugin version does not match or no option stored.
	 * 
	 * Use this method to set and update
	 * array {{plugin_snake}}_options.
	 */
	protected function pluginVersionOptionIsTheLatest()
	{
		$options = get_option( '{{plugin_snake}}_options' );
	
		if ( $options ) {
			if (
				! empty( $option['version'] ) &&
				$option['version'] === $this->version
			) {
				return true;
			}
		} else {
			add_option(
				'{{plugin_snake}}_options',
				array(
					'version' => $this->version,
					'uninstall_delete_all_data' => true,
				)
			);

			return false;
		}

		$options['version'] = $this->version;
		update_option( '{{plugin_snake}}_options', $options );
		
		return false;
	}

	/**
	 * Sets plugin transients on the options table, using
	 * $this->transients array.
	 */
	protected function updatePluginTransients()
	{
		if ( empty( $this->transients ) ) {
			return;
		}

		foreach ( $this->transient as $transient => $setting ) {
			$update = $setting['update'] ?? null;
			$value  = $setting['value']  ?? null;
			if ( empty( $update ) || empty( $value ) ) {
				continue;
			}

			$transient_value = get_transient( $this->prefix . $transient );

			if ( $transient_value === $value ) {
				continue;
			} else {
				set_transient( $this->prefix . $transient, $value );
			}
		}
	}

	/**
	 * Deletes all plugin information on uninstall.
	 */
	public function uninstallPlugin()
	{
		$this->deletePluginOptions( '{{plugin_snake}}_options' );
		$this->deletePluginTransients();
	}

	/**
	 * Deletes all plugin transients listed in $this->transients.
	 */
	protected function deletePluginTransients()
	{
		if ( empty( $this->transients ) ) {
			return;
		}

		foreach ( $this->transients as $transient => $setting ) {
			delete_transient( $this->prefix . $transient );
		}
	}
}

if ( class_exists( '{{NameSpace}}\{{PluginClass}}' ) ) {
	new {{PluginClass}}( $prefix );
}

if ( is_admin() ) {
	require_once {{PLUGIN_PREFIX}}_PATH . 'admin.php';

	if ( class_exists( '{{NameSpace}}\admin\{{PluginClass}}_Admin' ) ) {
		new admin\{{PluginClass}}_Admin();
	}
}