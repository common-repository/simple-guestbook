<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://profiles.wordpress.org/dichternebel/
 * @since             1.0.0
 * @package           Simple_Guestbook
 *
 * @wordpress-plugin
 * Plugin Name:       Simple Guestbook
 * Plugin URI:        https://wordpress.org/plugins/simple-guestbook/
 * Description:       A guestbook based on page comments
 * Version:           1.0.0
 * Author:            dichternebel
 * Author URI:        https://profiles.wordpress.org/dichternebel/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       simple-guestbook
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SIMPLE_GUESTBOOK_VERSION', '1.0.0' );

/**
 * Global plugin definition.
 */
define( 'SIMPLE_GUESTBOOK_PLUGIN_NAME', 'simple-guestbook');
define( 'SIMPLE_GUESTBOOK_OPTION_NAME', 'simple_guestbook_options');
define( 'SIMPLE_GUESTBOOK_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-simple-guestbook-activator.php
 */
function simple_guestbook_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-guestbook-activator.php';
	Simple_Guestbook_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-simple-guestbook-deactivator.php
 */
function simple_guestbook_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-guestbook-deactivator.php';
	Simple_Guestbook_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'simple_guestbook_activate' );
register_deactivation_hook( __FILE__, 'simple_guestbook_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-simple-guestbook.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function simple_guestbook_run() {

	$plugin = new Simple_Guestbook(
		SIMPLE_GUESTBOOK_VERSION,
	 	SIMPLE_GUESTBOOK_PLUGIN_NAME,
		SIMPLE_GUESTBOOK_OPTION_NAME,
		SIMPLE_GUESTBOOK_PLUGIN_BASENAME
	);
	$plugin->run();

}
simple_guestbook_run();
