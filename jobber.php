<?php
/**
 * Plugin Name:       Jobber
 * Plugin URI:        https://github.com/10up/jobber-wp
 * Description:       Add the ability to embed Jobber forms in your WordPress site.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Author:            10up
 * Author URI:        https://10up.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       jobber-wp
 * Domain Path:       /languages
 *
 * @package Jobber
 */

// Useful global constants.
define( 'JOBBER_PLUGIN_VERSION', '0.1.0' );
define( 'JOBBER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'JOBBER_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'JOBBER_PLUGIN_INC', JOBBER_PLUGIN_PATH . 'includes/' );
define( 'JOBBER_PLUGIN_DIST_URL', JOBBER_PLUGIN_URL . 'dist/' );
define( 'JOBBER_PLUGIN_DIST_PATH', JOBBER_PLUGIN_PATH . 'dist/' );
define( 'JOBBER_PLUGIN_BASENAME', plugin_basename( __DIR__ . '/jobber.php' ) );

// Require Composer autoloader if it exists.
if ( file_exists( JOBBER_PLUGIN_PATH . 'vendor/autoload.php' ) ) {
	require_once JOBBER_PLUGIN_PATH . 'vendor/autoload.php';
}

// Include files.
require_once JOBBER_PLUGIN_INC . '/utility.php';
require_once JOBBER_PLUGIN_INC . '/core.php';

// Activation.
register_activation_hook( __FILE__, '\Jobber\Core\activate' );

// Bootstrap.
Jobber\Core\setup();
