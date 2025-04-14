<?php
/**
 * Core plugin functionality.
 *
 * @package Jobber
 */

namespace Jobber\Core;

use TenupFramework\ModuleInitialization;
use Jobber\Utility;


/**
 * Default setup routine
 *
 * @return void
 */
function setup() {
	add_action( 'init', __NAMESPACE__ . '\\i18n' );
	add_action( 'init', __NAMESPACE__ . '\\init', (int) apply_filters( 'jobber_plugin_init_priority', 8 ) );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\admin_scripts' );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\admin_styles' );
	add_action( 'admin_notices', __NAMESPACE__ . '\\maybe_render_notices', 0 );

	// Hook to allow async or defer on asset loading.
	add_filter( 'script_loader_tag', __NAMESPACE__ . '\\script_loader_tag', 10, 2 );

	do_action( 'jobber_plugin_loaded' );
}

/**
 * Registers the default textdomain.
 *
 * @return void
 */
function i18n() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'jobber-plugin' );
	load_textdomain( 'jobber-plugin', WP_LANG_DIR . '/jobber-plugin/jobber-plugin-' . $locale . '.mo' );
	load_plugin_textdomain( 'jobber-plugin', false, plugin_basename( JOBBER_PLUGIN_PATH ) . '/languages/' );
}

/**
 * Initializes the plugin and fires an action other plugins can hook into.
 *
 * @return void
 */
function init() {
	do_action( 'jobber_plugin_before_init' );

	if ( ! class_exists( '\TenupFramework\ModuleInitialization' ) ) {
		add_action(
			'admin_notices',
			function () {
				$class = 'notice notice-error';

				printf(
					'<div class="%1$s"><p>%2$s</p></div>',
					esc_attr( $class ),
					wp_kses_post( __( 'Please ensure the <a href="https://github.com/10up/wp-framework"><code>10up/wp-framework</code></a> composer package is installed.', 'jobber-plugin' ) )
				);
			}
		);
		return;
	}

	ModuleInitialization::instance()->init_classes( JOBBER_PLUGIN_INC );
	do_action( 'jobber_plugin_init' );
}

/**
 * Activate the plugin
 *
 * @return void
 */
function activate() {
	// First load the init scripts in case any rewrite functionality is being loaded
	init();
	flush_rewrite_rules();

	// Set a transient to show the activation notice.
	set_transient( 'jobber_activation_notice', 'jobber', HOUR_IN_SECONDS );
}

/**
 * Deactivate the plugin
 *
 * Uninstall routines should be in uninstall.php
 *
 * @return void
 */
function deactivate() {
}


/**
 * The list of known contexts for enqueuing scripts/styles.
 *
 * @return array<string>
 */
function get_enqueue_contexts() {
	return [ 'admin', 'frontend', 'shared' ];
}

/**
 * Generate an URL to a script, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @param string $script Script file name (no .js extension)
 * @param string $context Context for the script ('admin', 'frontend', or 'shared')
 *
 * @throws \RuntimeException If an invalid $context is specified.
 *
 * @return string URL
 */
function script_url( $script, $context ) {

	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		throw new \RuntimeException( 'Invalid $context specified in Jobber script loader.' );
	}

	return JOBBER_PLUGIN_URL . "dist/js/{$script}.js";
}

/**
 * Generate an URL to a stylesheet, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @param string $stylesheet Stylesheet file name (no .css extension)
 * @param string $context Context for the script ('admin', 'frontend', or 'shared')
 *
 * @throws \RuntimeException If an invalid $context is specified.
 *
 * @return string URL
 */
function style_url( $stylesheet, $context ) {

	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		throw new \RuntimeException( 'Invalid $context specified in Jobber stylesheet loader.' );
	}

	return JOBBER_PLUGIN_URL . "dist/css/{$stylesheet}.css";
}

/**
 * Enqueue scripts for admin.
 *
 * @return void
 */
function admin_scripts() {
	wp_enqueue_script(
		'jobber_plugin_admin',
		script_url( 'admin', 'admin' ),
		Utility\get_asset_info( 'admin', 'dependencies' ),
		Utility\get_asset_info( 'admin', 'version' ),
		true
	);
}


/**
 * Enqueue styles for admin.
 *
 * @return void
 */
function admin_styles() {
	wp_enqueue_style(
		'jobber_plugin_admin',
		style_url( 'admin', 'admin' ),
		[],
		Utility\get_asset_info( 'admin', 'version' ),
	);
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
				<img src="<?php echo esc_url( JOBBER_PLUGIN_URL . 'assets/images/jobber-logo.png' ); ?>" alt="<?php esc_attr_e( 'Jobber', 'jobber-wp' ); ?>" style="max-width: 200px" />
			</div>
			<div class="jobber-activation-message" style="margin: 10px 0;">
				<p><?php esc_html_e( 'Thanks for downloading the Jobber Forms plugin.', 'jobber-wp' ); ?></p>
				<p><?php esc_html_e( 'Connect your site to Jobber to get started.', 'jobber-wp' ); ?></p>
			</div>
			<a class="components-button is-primary" href="<?php echo esc_url( admin_url( 'options-general.php?page=jobber_settings' ) ); ?>">
				<?php esc_html_e( 'Connect now', 'jobber-wp' ); ?>
			</a>
		</div>
	</div>

	<?php
	delete_transient( 'jobber_activation_notice' );
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
