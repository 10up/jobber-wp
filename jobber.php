<?php
/**
 * Plugin Name:       Jobber
 * Description:       Add the ability to embed Jobber forms in your WordPress site.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Author:            10up
 * Author URI:        https://10up.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       jobber-plugin
 * Domain Path:       /languages
 * Update URI:        https://github.com/10up/jobber-wp
 *
 * @package           Jobber
 */

// Useful global constants.
define( 'JOBBER_PLUGIN_VERSION', '0.1.0' );
define( 'JOBBER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'JOBBER_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'JOBBER_PLUGIN_INC', JOBBER_PLUGIN_PATH . 'includes/' );
define( 'JOBBER_PLUGIN_DIST_URL', JOBBER_PLUGIN_URL . 'dist/' );
define( 'JOBBER_PLUGIN_DIST_PATH', JOBBER_PLUGIN_PATH . 'dist/' );

$is_local_env = in_array( wp_get_environment_type(), [ 'local', 'development' ], true );
$is_local_url = strpos( home_url(), '.test' ) || strpos( home_url(), '.local' );
$is_local     = $is_local_env || $is_local_url;

if ( $is_local && file_exists( __DIR__ . '/dist/fast-refresh.php' ) ) {
	require_once __DIR__ . '/dist/fast-refresh.php';

	if ( function_exists( 'TenUpToolkit\set_dist_url_path' ) ) {
		TenUpToolkit\set_dist_url_path( basename( __DIR__ ), JOBBER_PLUGIN_DIST_URL, JOBBER_PLUGIN_DIST_PATH );
	}
}

// Require Composer autoloader if it exists.
if ( file_exists( JOBBER_PLUGIN_PATH . 'vendor/autoload.php' ) ) {
	require_once JOBBER_PLUGIN_PATH . 'vendor/autoload.php';
}

// Include files.
require_once JOBBER_PLUGIN_INC . '/utility.php';
require_once JOBBER_PLUGIN_INC . '/core.php';

// Activation/Deactivation.
register_activation_hook( __FILE__, '\Jobber\Core\activate' );
register_deactivation_hook( __FILE__, '\Jobber\Core\deactivate' );

// Bootstrap.
Jobber\Core\setup();
