<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              elementinvader.com 
 * @since             1.0.0
 * @package           Elementinvader
 *
 * @wordpress-plugin
 * Plugin Name:       Element Invader - Elementor Template Kits Library
 * Plugin URI:        https://elementinvader.com
 * Description:       ElementInvader offers premium library of one click ready and free Elementor templates from https://elementinvader.com/ service.
 * Version:           1.2.3
 * Author:            ElementInvader
 * Author URI:        https://elementinvader.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       elementinvader
 * Domain Path:       /languages
 * 
 * Elementor tested up to: 3.15.2
 * Elementor Pro tested up to: 3.16.2
 * 
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
define( 'ELEMENTINVADER_VERSION', '1.2.3' );
define( 'ELEMENTINVADER_NAME', 'elementinvader' );
define( 'ELEMENTINVADER_PATH', plugin_dir_path( __FILE__ ) );
define( 'ELEMENTINVADER_URL', plugin_dir_url( __FILE__ ) );
define( 'ELEMENTINVADER_WEBSITE', 'https://elementinvader.com/' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-elementinvader-activator.php
 */
function activate_elementinvader() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-elementinvader-activator.php';
	Elementinvader_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-elementinvader-deactivator.php
 */
function deactivate_elementinvader() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-elementinvader-deactivator.php';
	Elementinvader_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_elementinvader' );
register_deactivation_hook( __FILE__, 'deactivate_elementinvader' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-elementinvader.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_elementinvader() {

	$plugin = new Elementinvader();
	$plugin->run();

}
run_elementinvader();
