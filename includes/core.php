<?php
/**
 * Core plugin functionality.
 *
 * @package Jobber
 */

namespace Jobber\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Jobber\Jobber;
use Jobber\Auth;
use Jobber\Admin\Settings;
use Jobber\ModuleInitialization;

/**
 * Default setup routine
 */
function setup() {
	/**
	 * Filters the plugin initialization hook priority.
	 *
	 * @since 1.0.0
	 * @hook jobber_plugin_init_priority
	 *
	 * @param int $priority Hook priority. Default 8.
	 *
	 * @return int Filtered hook priority.
	 */
	add_action( 'init', __NAMESPACE__ . '\\init', (int) apply_filters( 'jobber_plugin_init_priority', 8 ) );

	add_action( 'admin_notices', __NAMESPACE__ . '\\maybe_render_notices', 0 );

	add_filter( 'script_loader_tag', __NAMESPACE__ . '\\script_loader_tag', 10, 2 );
	add_filter( 'plugin_action_links_' . JOBBER_PLUGIN_BASENAME, __NAMESPACE__ . '\\filter_plugin_action_links' );

	/**
	 * Allows running custom functionality after the plugin is loaded.
	 *
	 * @since 1.0.0
	 * @hook jobber_plugin_loaded
	 */
	do_action( 'jobber_plugin_loaded' );
}

/**
 * Initializes the plugin and fires an action other plugins can hook into.
 */
function init() {
	/**
	 * Allows running custom functionality before the plugin is initialized.
	 *
	 * @since 1.0.0
	 * @hook jobber_plugin_before_init
	 */
	do_action( 'jobber_plugin_before_init' );

	ModuleInitialization::instance()->init_classes();

	/**
	 * Allows running custom functionality after the plugin is initialized.
	 *
	 * @since 1.0.0
	 * @hook jobber_plugin_init
	 */
	do_action( 'jobber_plugin_init' );
}

/**
 * Activate the plugin
 */
function activate() {
	// First load the init scripts.
	init();

	// Set a transient to show the activation notice.
	set_transient( 'jobber_activation_notice', 'jobber', HOUR_IN_SECONDS );
}

/**
 * Deactivate the plugin.
 */
function deactivate() {
	// Delete any transients.
	delete_transient( 'jobber_activation_notice' );
	foreach ( [ 'booking', 'request' ] as $form_type ) {
		$cache_key = 'jobber_query_' . md5( wp_json_encode( [ 'query' => $form_type ] ) );
		delete_transient( $cache_key );
	}

	// Send disconnect request to the middleware if we are authenticated.
	if ( Auth::is_authorized() ) {
		$disconnect = ( new Jobber() )->disconnect();
		if ( ! $disconnect || is_wp_error( $disconnect ) ) {
			// Update the authenticated setting if the disconnect fails.
			Settings::update_settings( [ 'authenticated' => false ] );
		}
	}
}

/**
 * Decide if an admin notice needs to render.
 */
function maybe_render_notices() {
	// Only show these notices to admins.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	render_activation_notice();
}

/**
 * Render an activation notice, if needed.
 */
function render_activation_notice() {
	if ( ! get_transient( 'jobber_activation_notice' ) ) {
		return;
	}

	// Prevent showing the default WordPress "Plugin Activated" notice.
	unset( $_GET['activate'] ); // phpcs:ignore WordPress.Security.NonceVerification
	?>

	<div data-notice="plugin-activation" class="notice notice-success is-dismissible">
		<div id="jobber-activation-notice" style="padding: 15px 5px;">
			<div class="jobber-logo">
				<img src="<?php echo esc_url( JOBBER_PLUGIN_URL . 'dist/images/jobber-logo.png' ); ?>" alt="<?php esc_attr_e( 'Jobber', 'jobber' ); ?>" style="max-width: 220px" />
			</div>
			<div class="jobber-activation-message" style="margin: 10px 0;">
				<p><?php esc_html_e( 'Thanks for installing the Jobber plugin.', 'jobber' ); ?></p>
				<p><?php esc_html_e( 'Connect your site to Jobber to get started.', 'jobber' ); ?></p>
			</div>
			<a class="button button-primary is-primary" href="<?php echo esc_url( admin_url( 'options-general.php?page=jobber_settings' ) ); ?>">
				<?php esc_html_e( 'Connect now', 'jobber' ); ?>
			</a>
		</div>
	</div>

	<?php
}

/**
 * Add async/defer attributes to enqueued scripts that have the specified script_execution flag.
 *
 * @link https://core.trac.wordpress.org/ticket/12009
 * @param string $tag    The script tag.
 * @param string $handle The script handle.
 * @return string|null
 */
function script_loader_tag( $tag, $handle ) {
	$script_execution = wp_scripts()->get_data( $handle, 'script_execution' );

	if ( ! $script_execution ) {
		return $tag;
	}

	if ( 'async' !== $script_execution && 'defer' !== $script_execution ) {
		return $tag;
	}

	// Abort adding async/defer for scripts that have this script as a dependency. _doing_it_wrong()?
	foreach ( wp_scripts()->registered as $script ) {
		if ( in_array( $handle, $script->deps, true ) ) {
			return $tag;
		}
	}

	// Add the attribute if it hasn't already been added.
	if ( ! preg_match( ":\s$script_execution(=|>|\s):", $tag ) ) {
		$tag = preg_replace( ':(?=></script>):', " $script_execution", $tag, 1 );
	}

	return $tag;
}

/**
 * Add a settings link to the plugin action row.
 *
 * @param array $links The plugin action links.
 * @return array
 */
function filter_plugin_action_links( $links ) {
	if ( ! is_array( $links ) ) {
		return $links;
	}

	return array_merge(
		[
			'settings' => sprintf(
				'<a href="%s"> %s </a>',
				esc_url( admin_url( 'options-general.php?page=jobber_settings' ) ),
				esc_html__( 'Settings', 'jobber' )
			),
		],
		$links
	);
}
