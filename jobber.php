<?php
/**
 * Plugin Name:       Jobber
 * Plugin URI:        https://github.com/10up/jobber-wp
 * Description:       Add the ability to embed Jobber forms in your WordPress site.
 * Version:           1.0.0
 * Requires at least: 6.6
 * Requires PHP:      7.4
 * Author:            10up
 * Author URI:        https://10up.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       jobber
 *
 * @package Jobber
 */

// Useful global constants.
define( 'JOBBER_PLUGIN_VERSION', '1.0.0' );
define( 'JOBBER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'JOBBER_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'JOBBER_PLUGIN_INC', JOBBER_PLUGIN_PATH . 'includes/' );
define( 'JOBBER_PLUGIN_DIST_URL', JOBBER_PLUGIN_URL . 'dist/' );
define( 'JOBBER_PLUGIN_DIST_PATH', JOBBER_PLUGIN_PATH . 'dist/' );
define( 'JOBBER_PLUGIN_BASENAME', plugin_basename( __DIR__ . '/jobber.php' ) );

// Show a notice if the autoload file is missing.
if ( ! is_readable( JOBBER_PLUGIN_PATH . 'vendor/autoload.php' ) ) {
	add_action(
		'admin_notices',
		function () {
			$message = __( 'Autoload file is missing for the <strong>Jobber</strong> plugin. If you are a user, please contact support. If you are a developer, run <code>composer install</code> within the plugin directory to fix.', 'jobber' );
			printf(
				'<div class="notice notice-warning"><p>%1$s</p></div>',
				wp_kses_post( $message )
			);
		}
	);

	// Exit early to avoid fatal errors.
	return;
}

// Require composer autoloader.
require_once JOBBER_PLUGIN_PATH . 'vendor/autoload.php';

// Include files.
require_once JOBBER_PLUGIN_INC . '/utility.php';
require_once JOBBER_PLUGIN_INC . '/core.php';

// Activation.
register_activation_hook( __FILE__, '\Jobber\Core\activate' );

// Bootstrap.
Jobber\Core\setup();
