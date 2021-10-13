<?php

/**
 * Plugin Name: Class Plugin (required) The name of your plugin, which will be displayed in the Plugins list in the WordPress Admin.
 * Plugin URI: The home page of the plugin, which should be a unique URL, preferably on your own website. This must be unique to your plugin. You cannot use a WordPress.org URL here.
 * Description: A short description of the plugin, as displayed in the Plugins section in the WordPress Admin. Keep this description to fewer than 140 characters.
 * Version: 0.0.1
 * Requires at least: 5.8
 * Requires PHP: 7.4.1
 * Author: Carl David Brubaker
 * Author URI: The author’s website or profile on another website, such as WordPress.org.
 * License: GPLv2 (or later)
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: PLUGIN   The gettext text domain of the plugin. More information can be found in the Text Domain section of the How to Internationalize your Plugin page.
 * Domain Path: /languages    The domain path lets WordPress know where to find the translations. More information can be found in the Domain Path section of the How to Internationalize your Plugin page.
 * Network: Whether the plugin can only be activated network-wide. Can only be set to true, and should be left out when not needed.
 */

namespace NAMESPACE;

defined('ABSPATH') or exit;

$prefix = 'PLUGIN_PREFIX';

if (!defined($prefix . '_PATH')) {
	define($prefix . '_PATH', plugin_dir_path(__FILE__));
}

if (!defined($prefix . '_VERSION')) {
	define($prefix . '_VERSION', '0.0.1');
}

if (!defined($prefix . '_TEXT_DOMAIN')) {
	define($prefix . '_TEXT_DOMAIN', 'Text Domain');
}

/**
 * File Imports
 */

class PLUGIN
{	
	protected string $domain = PLUGIN_TEXT_DOMAIN;
	protected string $version = PLUGIN_VERSION;
	protected string $prefix;

	public function __construct(string $prefix)
	{
		$this->prefix = $prefix;

		add_action('init', [$this, 'activateOrUpdate']);
	}
	
	public function activateOrUpdate()
	{
		// Check to see if wp_option(PLUGIN_VERSION) is the same as $this->version
		if (!$this->pluginVersionOptionIsTheLatest()) {
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
		$option_value = get_option($this->prefix . '_VERSION');
	
		if ($option_value === $this->version) {
			return true;
		} elseif ($option_value === false) {
			add_option($this->prefix . '_VERSION', $this->version);
		} else {
			update_option($this->prefix . '_VERSION', $this->version);
		}
		
		return false;
	}
}
new PLUGIN($prefix);
