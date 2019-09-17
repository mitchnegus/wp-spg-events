<?php

/**
 * SPG Events
 *
 * The plugin bootstrap file.
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @package           SPG_Events
 * @link
 * @since             1.0.0
 * @author						Mitch Negus
 * @copyright					2019 Mitch Negus
 * @license						GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:       SPG Events
 * Plugin URI:        
 * Description:       A plugin to showcase the projects (past and present) of the Science Policy Group at Berkeley.
 * Version:           1.0.0
 * Author:            Mitch Negus
 * Author URI:        https://www.mitchnegus.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       spg-events
 * Domain Path:       /languages
 */
namespace SPG_Events;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Current plugin version.
 * Start(ed) at version 1.0.0 and use SemVer - https://semver.org
 */
define( 'SPG_EVENTS_VERSION', '1.0.0' );
 
// These files need to be included as dependencies when on the front end.
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

// Define some constants for use in the plugin
if ( ! defined( 'WSE_PATH' ) ) {
	define( 'WSE_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'WSE_URL' ) ) {
	define( 'WSE_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * The code that runs during plugin activation.
 *
 * This action is documented in includes/class-events-activator.php
 */
function activate_projects() {
	require_once WSE_PATH . 'includes/class-events-activator.php';
	Events_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-events-deactivator.php
 */
function deactivate_projects() {
	require_once WSE_PATH . 'includes/class-events-deactivator.php';
	Projects_Deactivator::deactivate();
}

\register_activation_hook( __FILE__, '\SPG_Events\activate_projects' );
\register_deactivation_hook( __FILE__, '\SPG_Events\deactivate_projects' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require WSE_PATH . 'includes/class-events.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_events() {

	$plugin = new Events();
	$plugin->run();

}
run_events();
