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

	// Hook to allow async or defer on asset loading.
	add_filter( 'script_loader_tag', __NAMESPACE__ . '\\script_loader_tag', 10, 2 );

	add_filter( 'plugin_action_links_' . JOBBER_PLUGIN_BASENAME, __NAMESPACE__ . '\\filter_plugin_action_links' );

	do_action( 'jobber_plugin_loaded' );
}

/**
 * Registers the default textdomain.
 *
 * @return void
 */
function i18n() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'jobber-wp' );
	load_textdomain( 'jobber-wp', WP_LANG_DIR . '/jobber-wp/jobber-wp-' . $locale . '.mo' );
	load_plugin_textdomain( 'jobber-wp', false, plugin_basename( JOBBER_PLUGIN_PATH ) . '/languages/' );
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
					wp_kses_post( __( 'Please ensure the <a href="https://github.com/10up/wp-framework"><code>10up/wp-framework</code></a> composer package is installed.', 'jobber-wp' ) )
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
				esc_html__( 'Settings', 'jobber-wp' )
			),
		],
		$links
	);
}
